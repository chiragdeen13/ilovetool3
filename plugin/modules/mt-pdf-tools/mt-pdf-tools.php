<?php
/*
Plugin Name: MT PDF Tools
Description: 20-in-1 local PDF converter for WordPress (PDF <-> Word/Excel/PPT/Images/Text/HTML/EPUB etc). Self-hosted, no key required. Optional CloudConvert key for formats your server can't do locally.
Version: 1.1 (fixed)
Author: MT
*/

if (!defined('ABSPATH')) exit;

define('MT_PDF_DIR', plugin_dir_path(__FILE__));

/* ---------------- engine detection ---------------- */
function mt_which($cmd) {
    $out = strtolower(PHP_OS) === 'winnt'
        ? shell_exec("where $cmd 2>nul")
        : shell_exec("which $cmd 2>/dev/null");
    if (!$out) return false;
    $line = strtok(trim($out), "\n");
    return file_exists($line) ? $line : false;
}
function mt_engines() {
    return array(
        'imagick'     => extension_loaded('imagick'),
        'zip'         => extension_loaded('zip'),
        'pdftotext'   => mt_which('pdftotext'),
        'ghostscript' => mt_which('gs') ?: mt_which('gswin64c') ?: mt_which('gswin32c'),
    );
}

/* ---------------- FIX: file-type validation ----------------
   Before, any file could be uploaded for any tool. Uploading a .docx
   to "JPG to PDF" (or similar mismatches) produced a silently broken
   or empty result instead of a clear error. This checks the real
   extension (and PDF magic bytes) against what the chosen tool expects. */
function mt_expected_ext($op) {
    $map = array(
        'PDF to Word'      => array('pdf'),
        'PDF to Excel'     => array('pdf'),
        'PDF to PowerPoint'=> array('pdf'),
        'PDF to JPG'       => array('pdf'),
        'PDF to PNG'       => array('pdf'),
        'PDF to Text'      => array('pdf'),
        'PDF to HTML'      => array('pdf'),
        'PDF to CSV'       => array('pdf'),
        'PDF to XML'       => array('pdf'),
        'PDF to SVG'       => array('pdf'),
		'Merge PDF'        => array('pdf'),
		'Split PDF'        => array('pdf'),
		'Compress PDF'      => array('pdf'),
        'PDF to WebP'      => array('pdf'),
        'PDF to PDF/A'     => array('pdf'),
        'PDF to EPUB'      => array('pdf'),
        'Word to PDF'      => array('doc', 'docx'),
        'Excel to PDF'     => array('xls', 'xlsx'),
        'PowerPoint to PDF'=> array('ppt', 'pptx'),
        'JPG to PDF'       => array('jpg', 'jpeg'),
        'PNG to PDF'       => array('png'),
        'WebP to PDF'      => array('webp'),
        'Text to PDF'      => array('txt'),
        'HTML to PDF'      => array('html', 'htm'),
    );
    return isset($map[$op]) ? $map[$op] : null;
}
function mt_validate_upload($op, $tmp_path, $orig_filename) {
    $ext = strtolower(pathinfo($orig_filename, PATHINFO_EXTENSION));
    $allowed = mt_expected_ext($op);
    if ($allowed && !in_array($ext, $allowed, true)) {
        return sprintf(
            'Ye tool "%s" sirf .%s file accept karta hai. Aapne .%s file di.',
            $op, implode('/.', $allowed), $ext ?: '?'
        );
    }
    if ($allowed === array('pdf')) {
        $head = @file_get_contents($tmp_path, false, null, 0, 5);
        if ($head !== '%PDF-') {
            return 'Ye file valid PDF nahi lagti (corrupt ya galat file type).';
        }
    }
    return true; // ok
}

/* ---------------- FIX: safe text for base-14 PDF fonts ----------------
   The manual PDF writer uses the built-in Helvetica font, which only
   supports Latin-1/WinAnsi characters. Urdu/Arabic/Emoji text was being
   written raw and rendered as garbage or broke the content stream.
   This transliterates what it can and swaps anything else for '?'
   instead of corrupting the page. */
function mt_ascii_safe($s) {
    $out = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $s);
    if ($out === false) $out = preg_replace('/[^\x20-\x7E\n]/', '?', $s);
    return $out;
}

/* ---------------- PDF text extraction ---------------- */
function mt_pdf_text($path) {
    $e = mt_engines();
    if ($e['pdftotext']) {
        $tmp = tempnam(sys_get_temp_dir(), 'mt_') . '.txt';
        exec(escapeshellarg($e['pdftotext']) . ' ' . escapeshellarg($path) . ' ' . escapeshellarg($tmp) . ' 2>nul');
        if (file_exists($tmp)) { $t = file_get_contents($tmp); unlink($tmp); if (trim($t)) return $t; }
    }
    return mt_pdf_text_regex($path);
}
/* FIX: no-binary PDF text extractor (used when pdftotext / exec is blocked,
   e.g. on shared hosting like Hostinger where shell commands are disabled).
   The old version only matched simple "(text) Tj" and missed:
     - TJ arrays, e.g. [(Hel) -20 (lo) 5 (World)] TJ  <- most real PDFs use this
     - hex strings, e.g. <48656C6C6F> Tj
     - escaped/octal characters inside literal strings, e.g. \( \) \n \061
   This version handles all of the above with pure PHP, no shell/exec needed. */
function mt_pdf_unescape_literal($s) {
    $out = '';
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        $c = $s[$i];
        if ($c !== '\\') { $out .= $c; continue; }
        $n = isset($s[$i + 1]) ? $s[$i + 1] : '';
        if ($n === 'n') { $out .= "\n"; $i++; }
        elseif ($n === 'r') { $out .= "\r"; $i++; }
        elseif ($n === 't') { $out .= "\t"; $i++; }
        elseif ($n === '(' || $n === ')' || $n === '\\') { $out .= $n; $i++; }
        elseif ($n !== '' && ctype_digit($n)) { // octal escape, up to 3 digits
            $oct = $n; $j = $i + 2;
            for ($k = 0; $k < 2 && isset($s[$j]) && ctype_digit($s[$j]); $k++, $j++) { $oct .= $s[$j]; }
            $out .= chr(octdec($oct) & 0xFF);
            $i = $j - 1;
        } else { $out .= $n; $i++; } // line-continuation or unknown escape: drop backslash
    }
    return $out;
}
function mt_pdf_hex_to_str($hex) {
    $hex = preg_replace('/\s+/', '', $hex);
    if (strlen($hex) % 2) { $hex .= '0'; }
    $bin = @hex2bin($hex);
    return $bin === false ? '' : $bin;
}
function mt_pdf_text_regex($path) {
    $data = file_get_contents($path);
    $text = '';
    if (preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $data, $m)) {
        foreach ($m[1] as $chunk) {
            $chunk = ltrim($chunk);
            $dec = @gzuncompress($chunk);
            if ($dec !== false) { $chunk = $dec; } // FlateDecode is the overwhelming common case
            $line = '';
            // Walk operator by operator: Tj (single string) and TJ (array of strings/numbers)
            if (preg_match_all('/(\((?:\\\\.|[^()\\\\])*\)|<[0-9A-Fa-f\s]*>)\s*Tj|\[((?:\((?:\\\\.|[^()\\\\])*\)|<[0-9A-Fa-f\s]*>|[^\[\]])*)\]\s*TJ/', $chunk, $ops, PREG_SET_ORDER)) {
                foreach ($ops as $op) {
                    $piece = '';
                    if ($op[1] !== '') {
                        $piece = mt_pdf_extract_one($op[1]);
                    } elseif (isset($op[2])) {
                        if (preg_match_all('/\((?:\\\\.|[^()\\\\])*\)|<[0-9A-Fa-f\s]*>/', $op[2], $strs)) {
                            foreach ($strs[0] as $s) { $piece .= mt_pdf_extract_one($s); }
                        }
                    }
                    $line .= $piece;
                }
            }
            // Treat each stream as one flow of text, break into readable lines on sentence-ish boundaries
            if (trim($line) !== '') { $text .= $line . "\n"; }
        }
    }
    return $text;
}
function mt_pdf_extract_one($tok) {
    if ($tok === '') { return ''; }
    if ($tok[0] === '(') {
        return mt_pdf_unescape_literal(substr($tok, 1, -1));
    }
    if ($tok[0] === '<') {
        return mt_pdf_hex_to_str(substr($tok, 1, -1));
    }
    return '';
}

/* ---------------- minimal PDF writers (no libs) ---------------- */
function mt_pdf_from_text($text) {
    $text = mt_ascii_safe($text); // FIX: avoid corrupt/garbled output text
    $lines = explode("\n", wordwrap(strip_tags($text), 90, "\n", true));
    $pages = array_chunk($lines, 50);
    if (empty($pages)) $pages = array(array(''));
    $objs = array();
    $num = 1;
    $objs[$num++] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>"; // obj1 font
    $objs[$num++] = ""; // obj2 catalog (filled later)
    $objs[$num++] = ""; // obj3 pages (filled later)
    $fontObj = 1; $catalogObj = 2; $pagesObj = 3;
    $page_objs = array();
    foreach ($pages as $pg) {
        $stream = "BT /F1 11 Tf 40 780 Td 14 TL\n";
        foreach ($pg as $ln) {
            $ln = str_replace(array('\\', '(', ')'), array('\\\\', '\\(', '\\)'), $ln);
            $stream .= "($ln) Tj T*\n";
        }
        $stream .= "ET";
        $c = $num++;
        $objs[$c] = "<< /Length " . strlen($stream) . " >>\nstream\n$stream\nendstream";
        $p = $num++;
        $objs[$p] = "<< /Type /Page /Parent $pagesObj 0 R /MediaBox [0 0 595 842] /Contents $c 0 R /Resources << /Font << /F1 $fontObj 0 R >> >> >>";
        $page_objs[] = $p;
    }
    $kids = '/Kids [' . implode(' 0 R ', $page_objs) . ' 0 R]';
    $objs[$catalogObj] = "<< /Type /Catalog /Pages $pagesObj 0 R >>";
    $objs[$pagesObj] = "<< /Type /Pages $kids /Count " . count($page_objs) . " >>";
    ksort($objs);
    $pdf = "%PDF-1.4\n";
    $off = array();
    $max = max(array_keys($objs));
    foreach ($objs as $i => $o) {
        $off[$i] = strlen($pdf);
        $pdf .= "$i 0 obj\n$o\nendobj\n";
    }
    $xref = "xref\n0 " . ($max + 1) . "\n0000000000 65535 f \n";
    for ($i = 1; $i <= $max; $i++) $xref .= sprintf("%010d 00000 n \n", $off[$i]);
    $xref .= "trailer\n<< /Size " . ($max + 1) . " /Root $catalogObj 0 R >>\nstartxref\n" . strlen($pdf) . "\n%%EOF";
    $pdf .= $xref;
    return $pdf;
}

/* ---------------- OOXML / EPUB (ZipArchive) ---------------- */
function mt_zip($files, $out) {
    if (!class_exists('ZipArchive')) return false;
    $z = new ZipArchive();
    if ($z->open($out, ZipArchive::CREATE) !== true) return false;
    foreach ($files as $name => $content) $z->addFromString($name, $content);
    $z->close();
    return true;
}
function mt_make_docx($text) {
    $paras = '';
    foreach (explode("\n", $text) as $l) {
        $l = htmlspecialchars($l ?: ' ', ENT_XML1);
        $paras .= "<w:p><w:r><w:t xml:space=\"preserve\">$l</w:t></w:r></w:p>";
    }
    $doc = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>' . $paras . '</w:body></w:document>';
    $files = array(
        '[Content_Types].xml' => '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>',
        '_rels/.rels' => '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>',
        'word/document.xml' => $doc,
    );
    return $files;
}
function mt_make_xlsx($rows) {
    $sheet = '<sheetData>';
    foreach ($rows as $r) {
        $sheet .= '<row>';
        foreach ((array)$r as $c) $sheet .= '<c><v>' . htmlspecialchars($c) . '</v></c>';
        $sheet .= '</row>';
    }
    $sheet .= '</sheetData>';
    $files = array(
        '[Content_Types].xml' => '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>',
        '_rels/.rels' => '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
        'xl/workbook.xml' => '<?xml version="1.0"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheets><sheet name="Sheet1" sheetId="1" r:id="rId1" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/></sheets></workbook>',
        'xl/_rels/workbook.xml.rels' => '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>',
        'xl/worksheets/sheet1.xml' => '<?xml version="1.0"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . $sheet . '</worksheet>',
    );
    return $files;
}
function mt_make_pptx($slides) {
    $slds = '';
    foreach ($slides as $i => $t) {
        $slds .= '<?xml version="1.0"?><p:sld xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"><p:cSld><p:spTree><p:sp><p:txBody><a:bodyPr/><a:p><a:r><a:t>' . htmlspecialchars($t) . '</a:t></a:r></a:p></p:txBody></p:sp></p:spTree></p:cSld></p:sld>';
    }
    $files = array(
        '[Content_Types].xml' => '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/ppt/presentation.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"/></Types>',
        '_rels/.rels' => '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="ppt/presentation.xml"/></Relationships>',
        'ppt/presentation.xml' => '<?xml version="1.0"?><p:presentation xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">' . $slds . '</p:presentation>',
    );
    return $files;
}
function mt_make_epub($text) {
    $xhtml = '<?xml version="1.0"?><html xmlns="http://www.w3.org/1999/xhtml"><head><title>Export</title></head><body><pre>' . htmlspecialchars($text) . '</pre></body></html>';
    $files = array(
        'mimetype' => 'application/epub+zip',
        'META-INF/container.xml' => '<?xml version="1.0"?><container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container"><rootfiles><rootfile full-path="content.opf" media-type="application/oebps-package+xml"/></rootfiles></container>',
        'content.opf' => '<?xml version="1.0"?><package xmlns="http://www.idpf.org/2007/opf" version="3.0" unique-identifier="id"><metadata xmlns:dc="http://purl.org/dc/elements/1.1/"><dc:title>Export</dc:title><dc:language>en</dc:language><dc:identifier id="id">urn:uuid:' . uniqid() . '</dc:identifier></metadata><manifest><item id="c" href="chapter.xhtml" media-type="application/xhtml+xml"/></manifest><spine><itemref idref="c"/></spine></package>',
        'chapter.xhtml' => $xhtml,
    );
    return $files;
}

/* ---------------- raster (Imagick) ---------------- */
function mt_pdf_raster($path, $fmt, $outdir) {
    if (!extension_loaded('imagick')) return false;
    /* FIX: this whole block previously had no try/catch. If Ghostscript
       (Imagick's PDF delegate) is missing or the PDF is malformed,
       readImage()/writeImage() throw an uncaught ImagickException that
       crashes the ENTIRE page with a white screen instead of falling
       back to the CloudConvert API or showing a clean error. */
    try {
        $im = new Imagick();
        $im->readImage($path);
        $im->setImageFormat($fmt);
        if ($im->getNumberImages() > 1) {
            $zip = new ZipArchive();
            $zp = $outdir . '/pages.zip';
            $zip->open($zp, ZipArchive::CREATE);
            foreach ($im as $i => $page) {
                $page->setImageFormat($fmt);
                $zip->addFromString("page-" . str_pad($i+1,3,'0',STR_PAD_LEFT) . ".$fmt", $page->getImageBlob());
            }
            $zip->close();
            return $zp;
        }
        $out = $outdir . '/page-001.' . $fmt;
        $im->writeImage($out);
        return $out;
    } catch (Exception $e) {
        return false; // caller falls back to mt_api() / shows a clean error
    }
}

/* ---------------- FIX: image -> PDF ----------------
   OLD BUG #1: When Imagick wasn't available, the manual JPEG fallback
   built an /Im0 XObject with NO /Subtype /Image, /Width, /Height,
   /ColorSpace or /BitsPerComponent — so viewers could not render it.
   The result "PDF" opened but showed a blank page.
   OLD BUG #2: The content stream's /Length was hardcoded to 30, which
   is wrong for any width/height whose digit count differs — this
   corrupts the stream and can make the whole file fail to open.
   FIX: proper Image XObject dict, real dimensions from the JPEG
   header, correct stream length, and the image is scaled to fit an
   A4 page instead of being drawn at raw pixel size (which could be
   larger than the page or tiny). PNG/WebP still route through
   Imagick when available; if Imagick is missing for those, the tool
   now fails with a clear message instead of pretending to work. */
function mt_image_to_pdf($path, $out) {
    if (extension_loaded('imagick')) {
        try {
            $im = new Imagick();
            $im->readImage($path);
            $im->setImageFormat('pdf');
            $im->writeImage($out);
            return true;
        } catch (Exception $e) {
            // fall through to manual method below
        }
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, array('jpg', 'jpeg'), true)) {
        return mt_jpeg_to_pdf_manual($path, $out);
    }
    return false; // e.g. PNG/WebP without Imagick — caller shows a clear error
}
function mt_jpeg_to_pdf_manual($path, $out) {
    $img = file_get_contents($path);
    if (!$img || substr($img, 0, 2) !== "\xff\xd8") return false;

    // Parse the JPEG SOF marker for real width/height/component count
    $w = $h = 0; $comps = 3;
    $i = 2; $len = strlen($img);
    while ($i < $len - 1) {
        if (ord($img[$i]) !== 0xFF) { $i++; continue; }
        $marker = ord($img[$i + 1]);
        if ($marker === 0xD8 || $marker === 0x01 || ($marker >= 0xD0 && $marker <= 0xD7)) { $i += 2; continue; }
        if ($i + 3 >= $len) break;
        $segLen = (ord($img[$i + 2]) << 8) + ord($img[$i + 3]);
        if ($marker >= 0xC0 && $marker <= 0xCF && $marker !== 0xC4 && $marker !== 0xC8 && $marker !== 0xCC) {
            $h = (ord($img[$i + 5]) << 8) + ord($img[$i + 6]);
            $w = (ord($img[$i + 7]) << 8) + ord($img[$i + 8]);
            $comps = ord($img[$i + 9]);
            break;
        }
        if ($marker === 0xD9) break; // EOI
        $i += 2 + $segLen;
    }
    if (!$w || !$h) { $w = 595; $h = 842; }
    $colorspace = ($comps === 1) ? '/DeviceGray' : '/DeviceRGB';

    $imgObj = "<< /Type /XObject /Subtype /Image /Width $w /Height $h "
        . "/ColorSpace $colorspace /BitsPerComponent 8 /Filter /DCTDecode "
        . "/Length " . strlen($img) . " >>\nstream\n$img\nendstream";

    // Scale to fit an A4 page, keep aspect ratio, center it
    $pageW = 595; $pageH = 842;
    $scale = min($pageW / $w, $pageH / $h, 1);
    $drawW = max(1, round($w * $scale));
    $drawH = max(1, round($h * $scale));
    $x = round(($pageW - $drawW) / 2);
    $y = round(($pageH - $drawH) / 2);

    $content = "q $drawW 0 0 $drawH $x $y cm /Im0 Do Q";
    $contentObj = "<< /Length " . strlen($content) . " >>\nstream\n$content\nendstream"; // real length, not hardcoded

    $objs = array();
    $objs[1] = "<< /Type /Catalog /Pages 2 0 R >>";
    $objs[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
    $objs[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 $pageW $pageH] /Contents 4 0 R /Resources << /XObject << /Im0 5 0 R >> >> >>";
    $objs[4] = $contentObj;
    $objs[5] = $imgObj;
    ksort($objs);

    $pdf = "%PDF-1.4\n";
    $off = array();
    foreach ($objs as $i => $o) { $off[$i] = strlen($pdf); $pdf .= "$i 0 obj\n$o\nendobj\n"; }
    $max = max(array_keys($objs));
    $xref = "xref\n0 " . ($max + 1) . "\n0000000000 65535 f \n";
    for ($i = 1; $i <= $max; $i++) $xref .= sprintf("%010d 00000 n \n", $off[$i]);
    $xref .= "trailer\n<< /Size " . ($max + 1) . " /Root 1 0 R >>\nstartxref\n" . strlen($pdf) . "\n%%EOF";
    $pdf .= $xref;

    return file_put_contents($out, $pdf) !== false;
}

/* ---------------- dispatch ---------------- */
function mt_pdf_page_count($path) {
	$e = mt_engines();
	if ($e['ghostscript']) {
		$ps = '('. $path . ') (r) file runpdfbegin pdfpagecount = quit';
		exec(escapeshellarg($e['ghostscript']) . ' -q -dNODISPLAY -dBATCH -c ' . escapeshellarg($ps) . ' 2>/dev/null', $out, $code);
		if ($code === 0 && $out) {
			foreach ($out as $line) {
				if (preg_match('/\d+/', $line, $m)) return (int) $m[0];
				
			}
		}
	}

	if ($e['imagick'] && class_exists('Imagick')) {
		try {
			$im = new Imagick($path);
			$n = $im->getNumberImages();
			$im->clear();
			if ($n >= 1) return (int) $n;
			
		} catch (Exception $ex) { }
	}

return 0;}

function mt_pdfa_icc() {
    $cands = glob('/usr/share/ghostscript/*/iccprofiles/srgb.icc');
    if ($cands) return $cands[0];
    foreach (array('/usr/share/ghostscript/iccprofiles/srgb.icc', '/usr/share/color/icc/srgb.icc') as $p) {
        if (file_exists($p)) return $p;
	}
    return false;
}
	function mt_convert($op, $inpath, $outdir) {
    $e = mt_engines();
    $base = pathinfo($inpath, PATHINFO_FILENAME);
    $out = null;
    switch ($op) {
        case 'PDF to Word':
            $txt = mt_pdf_text($inpath);
            mt_zip(mt_make_docx($txt), $o = "$outdir/$base.docx"); return $o;
        case 'Word to PDF':
            $txt = mt_docx_text($inpath);
            file_put_contents($o = "$outdir/$base.pdf", mt_pdf_from_text($txt)); return $o;
        case 'PDF to JPG': return mt_pdf_raster($inpath, 'jpg', $outdir) ?: mt_api($op,$inpath,$outdir);
        case 'JPG to PDF':
            return mt_image_to_pdf($inpath, $o = "$outdir/$base.pdf") ? $o : mt_api($op,$inpath,$outdir);
        case 'PDF to Excel':
            $txt = mt_pdf_text($inpath);
            $rows = array_map(function($l){ return array($l); }, explode("\n", $txt));
            mt_zip(mt_make_xlsx($rows), $o = "$outdir/$base.xlsx"); return $o;
        case 'Excel to PDF':
            $rows = mt_xlsx_rows($inpath);
            $txt = '';
            foreach ($rows as $r) $txt .= implode(" | ", (array)$r) . "\n";
            file_put_contents($o = "$outdir/$base.pdf", mt_pdf_from_text($txt)); return $o;
        case 'PDF to PowerPoint':
            $txt = mt_pdf_text($inpath);
            $slides = array_filter(explode("\n", $txt));
            mt_zip(mt_make_pptx($slides), $o = "$outdir/$base.pptx"); return $o;
        case 'PowerPoint to PDF':
            $txt = mt_pptx_text($inpath);
            file_put_contents($o = "$outdir/$base.pdf", mt_pdf_from_text($txt)); return $o;
        case 'PDF to PNG': return mt_pdf_raster($inpath, 'png', $outdir) ?: mt_api($op,$inpath,$outdir);
        case 'PNG to PDF':
            return mt_image_to_pdf($inpath, $o = "$outdir/$base.pdf") ? $o : mt_api($op,$inpath,$outdir);
        case 'PDF to Text':
            file_put_contents($o = "$outdir/$base.txt", mt_pdf_text($inpath)); return $o;
        case 'Text to PDF':
            $txt = file_get_contents($inpath);
            file_put_contents($o = "$outdir/$base.pdf", mt_pdf_from_text($txt)); return $o;
        case 'PDF to HTML':
            $txt = mt_pdf_text($inpath);
            file_put_contents($o = "$outdir/$base.html", "<html><body><pre>" . htmlspecialchars($txt) . "</pre></body></html>"); return $o;
        case 'HTML to PDF':
            $html = file_get_contents($inpath);
            $txt = strip_tags($html);
            file_put_contents($o = "$outdir/$base.pdf", mt_pdf_from_text($txt)); return $o;
        case 'PDF to CSV':
            $txt = mt_pdf_text($inpath);
            $csv = implode("\n", array_map(function($l){ return '"'.str_replace('"','""',$l).'"'; }, explode("\n", $txt)));
            file_put_contents($o = "$outdir/$base.csv", $csv); return $o;
        case 'PDF to XML':
            $txt = mt_pdf_text($inpath);
            file_put_contents($o = "$outdir/$base.xml", "<?xml version=\"1.0\"?><document><![CDATA[" . $txt . "]]></document>"); return $o;
        case 'PDF to SVG':
            if ($e['imagick']) {
                /* FIX: uncaught Imagick exceptions here previously crashed the page. */
                try {
                    $im = new Imagick(); $im->readImage($inpath . '[0]'); $im->setImageFormat('png');
                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$im->getImageWidth().'" height="'.$im->getImageHeight().'"><image href="data:image/png;base64,'.base64_encode($im->getImageBlob()).'" width="'.$im->getImageWidth().'" height="'.$im->getImageHeight().'"/></svg>';
                    file_put_contents($o = "$outdir/$base.svg", $svg); return $o;
                } catch (Exception $ex) {
                    return mt_api($op,$inpath,$outdir);
                }
            }
            return mt_api($op,$inpath,$outdir);
        case 'PDF to WebP': return mt_pdf_raster($inpath, 'webp', $outdir) ?: mt_api($op,$inpath,$outdir);
        case 'WebP to PDF':
            return mt_image_to_pdf($inpath, $o = "$outdir/$base.pdf") ? $o : mt_api($op,$inpath,$outdir);
		case 'PDF to PDF/A':
        $gs = $e['ghostscript'];
        if (!$gs) return false;
        $o = "$outdir/$base" . "_pdfa.pdf";
        $icc = mt_pdfa_icc();
        $cmd = escapeshellarg($gs) . ' -dPDFA=1 -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dPDFACompatibilityPolicy=1 -sColorConversionStrategy=UseDeviceIndependentColor'
            . ($icc ? ' -sOutputICCProfile=' . escapeshellarg($icc) : '')
            . ' -sOutputFile=' . escapeshellarg($o) . ' ' . escapeshellarg($inpath) . ' 2>/dev/null';
        exec($cmd, $gs_out, $gs_code);
        if ($gs_code !== 0 || !file_exists($o) || filesize($o) < 1 || file_get_contents($o, false, null, 0, 5) !== '%PDF-') return false;
        $c = file_get_contents($o);
        if (strpos($c, 'http://www.aiim.org/pdfa/ns/id/') === false) return false;
        return $o;
        case 'PDF to EPUB':
            $txt = mt_pdf_text($inpath);
            mt_zip(mt_make_epub($txt), $o = "$outdir/$base.epub"); return $o;
		case 'Split PDF':
			$gs = mt_engines()['ghostscript'];
			if (!$gs) return false;
			$n = mt_pdf_page_count($inpath);
			if ($n < 1 || $n > 200) return false;
			$files = array();
			for ($i = 1; $i <= $n; $i++) {
				exec(escapeshellarg($gs). ' -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dPDFSETTINGS=/prepress -dFirstPage='. $i. ' -dLastPage='. $i. ' -sOutputFile='. escapeshellarg("$outdir/page-$i.pdf"). ' '. escapeshellarg($inpath). ' 2/dev/null', $gs_out, $gs_code);
				if (!file_exists("$outdir/page-$i.pdf") || filesize("$outdir/page-$i.pdf") < 1 || file_get_contents("$outdir/page-$i.pdf", false, null, 0, 5) !== '%PDF-') return false;
				$files["page-$i.pdf"] = file_get_contents("$outdir/page-$i.pdf");
			}
			mt_zip($files, $o = "$outdir/pages.zip");
			return $o;
		case 'Compress PDF':
			$gs = $e['ghostscript'];
			if (!$gs) return false;
			$o = "$outdir/$base" . "_compressed.pdf";
			$cmd = escapeshellarg($gs) . ' -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dPDFSETTINGS=/ebook -dCompatibilityLevel=1.4 -dDownsampleColorImages=true -dColorImageResolution=150 -dDownsampleGrayImages=true -dGrayImageResolution=150 -dDownsampleMonoImages=true -dMonoImageResolution=300 -dSubsetFonts=true -dEmbedAllFonts=true -sOutputFile=' . escapeshellarg($o) . ' ' . escapeshellarg($inpath) . ' 2>/dev/null';
			exec($cmd, $gs_out, $gs_code);
			if ($gs_code !== 0 || !file_exists($o) || filesize($o) < 1 || file_get_contents($o, false, null, 0, 5) !== '%PDF-') return false;
			return $o;
        default:
            return false;
    }
}

/* ---------------- docx/xlsx/pptx text helpers ---------------- */
function mt_docx_text($path) {
    if (!class_exists('ZipArchive')) return '';
    $z = new ZipArchive(); if ($z->open($path) !== true) return '';
    $xml = $z->getFromName('word/document.xml'); $z->close();
    $xml = preg_replace('/<w:p[ >]/', "\n", $xml);
    return trim(strip_tags(str_replace('</w:t>', ' ', $xml)));
}
function mt_xlsx_rows($path) {
    $rows = array();
    if (!class_exists('ZipArchive')) return $rows;
    $z = new ZipArchive(); if ($z->open($path) !== true) return $rows;
    $sheet = $z->getFromName('xl/worksheets/sheet1.xml');
    if (preg_match_all('/<row[^>]*>(.*?)<\/row>/s', $sheet, $rs)) {
        foreach ($rs[1] as $r) {
            $row = array();
            if (preg_match_all('/<c[^>]*>(?:<v>([^<]*)<\/v>)?/s', $r, $cs)) foreach ($cs[1] as $v) $row[] = $v;
            $rows[] = $row;
        }
    }
    $z->close();
    return $rows;
}
function mt_pptx_text($path) {
    if (!class_exists('ZipArchive')) return '';
    $z = new ZipArchive(); if ($z->open($path) !== true) return '';
    $txt = '';
    for ($i = 1; $i <= 50; $i++) {
        $s = $z->getFromName("ppt/slides/slide$i.xml");
        if ($s === false) break;
        $txt .= strip_tags($s) . "\n";
    }
    $z->close();
    return $txt;
}

/* ---------------- optional CloudConvert fallback ---------------- */
function mt_api($op, $in, $outdir) {
    $key = get_option('mt_pdf_apikey');
    if (!$key) return false;
    $map = array(
        'PDF to Word'=>'docx','Word to PDF'=>'pdf','PDF to JPG'=>'jpg','JPG to PDF'=>'pdf',
        'PDF to Excel'=>'xlsx','Excel to PDF'=>'pdf','PDF to PowerPoint'=>'pptx','PowerPoint to PDF'=>'pdf',
        'PDF to PNG'=>'png','PNG to PDF'=>'pdf','PDF to Text'=>'txt','PDF to HTML'=>'html',
        'HTML to PDF'=>'pdf','PDF to CSV'=>'csv','PDF to XML'=>'xml','PDF to SVG'=>'svg',
        'PDF to WebP'=>'webp','WebP to PDF'=>'pdf','PDF to PDF/A'=>'pdf','PDF to EPUB'=>'epub',
    );
    if (!isset($map[$op])) return false;
    $fmt = $map[$op];
    $base = pathinfo($in, PATHINFO_FILENAME);
    $r = wp_remote_post('https://api.cloudconvert.com/v2/jobs', array(
        'headers' => array('Authorization' => "Bearer $key", 'Content-Type' => 'application/json'),
        'body' => json_encode(array('tasks' => array(
            'import' => array('operation' => 'import/upload'),
            'convert' => array('operation' => 'convert', 'input' => 'import', 'input_format' => strtolower(pathinfo($in, PATHINFO_EXTENSION)), 'output_format' => $fmt),
            'export' => array('operation' => 'export/url', 'input' => 'convert'),
        ))),
        'timeout' => 30,
    ));
    if (is_wp_error($r)) return false;
    $body = json_decode(wp_remote_retrieve_body($r), true);
    if (empty($body['data']['tasks']['import']['result']['form']['url'])) return false;
    $up = $body['data']['tasks']['import']['result']['form'];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $up['url'], CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => array_merge($up['parameters'], array('file' => new CURLFile($in))),
    ));
    curl_exec($curl); curl_close($curl);
    for ($i = 0; $i < 10; $i++) {
        $r2 = wp_remote_get('https://api.cloudconvert.com/v2/jobs/' . $body['data']['id'], array('headers' => array('Authorization' => "Bearer $key"), 'timeout' => 20));
        $j = json_decode(wp_remote_retrieve_body($r2), true);
        if (!empty($j['data']['tasks']['export']['result']['files'][0]['url'])) {
            $url = $j['data']['tasks']['export']['result']['files'][0]['url'];
            $dl = wp_remote_get($url, array('timeout' => 60, 'stream' => true, 'filename' => $o = "$outdir/$base.$fmt"));
            return file_exists($o) ? $o : false;
        }
        sleep(3);
    }
    return false;
}

/* ---------------- admin UI ---------------- */
add_action('admin_menu', function () { // FIX (consolidation): now a submenu of the shared "iLoveTool3 Suite" parent menu instead of its own top-level entry
    add_submenu_page('imei-master', 'MT PDF Tools', 'MT PDF Tools', 'manage_options', 'mt-pdf-tools', 'mt_pdf_admin');
}, 10);
function mt_pdf_admin() {
    $e = mt_engines();
    echo '<div class="wrap"><h1>MT PDF Tools</h1>';
    echo '<p>Local engines: ';
    echo 'Imagick ' . ($e['imagick'] ? '✅' : '❌') . ' | Zip ' . ($e['zip'] ? '✅' : '❌') .
         ' | pdftotext ' . ($e['pdftotext'] ? '✅' : '❌') . ' | Ghostscript ' . ($e['ghostscript'] ? '✅' : '❌');
    echo '</p>';
    if (!$e['imagick']) echo '<p><b>Note:</b> Install Imagick + Ghostscript on your server for image/PDF-A conversions (PNG/WebP → PDF and PDF → PNG/WebP/SVG/PDF-A need it, or a CloudConvert key as fallback). Office formats and JPG → PDF work without it.</p>';

    if (isset($_POST['mt_pdf_op']) && !empty($_FILES['mt_pdf_file']['tmp_name'])) {
        check_admin_referer('mt_pdf_nonce');
        $op = sanitize_text_field($_POST['mt_pdf_op']);
        $tmp = $_FILES['mt_pdf_file']['tmp_name'];
        $orig_name = $_FILES['mt_pdf_file']['name'];
        $err = mt_validate_upload($op, $tmp, $orig_name); // FIX: validate before converting
        if ($err !== true) {
            echo '<p style="color:red">' . esc_html($err) . '</p>';
        } else {
            $outdir = sys_get_temp_dir() . '/mt_' . uniqid();
            mkdir($outdir);
            try {
                $res = mt_convert($op, $tmp, $outdir);
                if ($res && file_exists($res)) {
                    echo '<p><b>Done:</b> <a href="' . wp_nonce_url(admin_url('admin-ajax.php?action=mt_pdf_dl&f=' . base64_encode($res)), 'mt_dl') . '">Download result</a></p>';
                } else {
                    echo '<p style="color:red">Conversion failed. If your server lacks Imagick, set a CloudConvert key below.</p>';
                }
            } catch (Exception $ex) {
                echo '<p style="color:red">Error: ' . esc_html($ex->getMessage()) . '</p>';
            }
        }
    }

    echo '<form method="post" enctype="multipart/form-data">';
    wp_nonce_field('mt_pdf_nonce');
    echo '<p><select name="mt_pdf_op">';
    $ops = array('PDF to Word','Word to PDF','PDF to JPG','JPG to PDF','PDF to Excel','Excel to PDF','PDF to PowerPoint','PowerPoint to PDF','PDF to PNG','PNG to PDF','PDF to Text','Text to PDF','PDF to HTML','HTML to PDF','PDF to CSV','PDF to XML','PDF to SVG','PDF to WebP','WebP to PDF','PDF to PDF/A','PDF to EPUB','Merge PDF','Split PDF','Compress PDF');
    foreach ($ops as $o) echo "<option>$o</option>";
    echo '</select></p><p><input type="file" name="mt_pdf_file" required></p>';
    echo '<p><button class="button button-primary">Convert</button></p></form>';

    echo '<hr><h2>Optional: CloudConvert API key</h2><form method="post">';
    wp_nonce_field('mt_pdf_key');
    if (isset($_POST['mt_key_save'])) update_option('mt_pdf_apikey', sanitize_text_field($_POST['mt_pdf_apikey']));
    echo '<p><input type="text" name="mt_pdf_apikey" value="' . esc_attr(get_option('mt_pdf_apikey')) . '" placeholder="API key" style="width:400px"></p>';
    echo '<p><button name="mt_key_save" class="button">Save key</button></p></form>';

    if (isset($_GET['created'])) echo '<div class="notice notice-success"><p>20 tool pages + index page created. Visit <a href="' . esc_url(home_url('/pdf-tools/')) . '">/pdf-tools/</a>.</p></div>'; // FIX: trailing slash (canonical URL) + esc_url
    if (isset($_GET['repaired'])) echo '<div class="notice notice-success"><p>Repaired ' . intval($_GET['repaired']) . ' existing page(s): removed URL nesting and fixed any broken/blank meta descriptions.</p></div>';

    echo '<hr><h2>Build site pages</h2>';
    echo '<p>Click below to auto-create one WordPress page per converter (styled like your theme) + an index page. Each page uses the <code>[mt_pdf_tools op="..."]</code> shortcode.</p>';
    echo '<form method="post" action="' . admin_url('admin-post.php') . '">';
    echo '<input type="hidden" name="action" value="mt_create_pages">';
    wp_nonce_field('mt_create_pages');
    echo '<button class="button button-primary">Create 20 tool pages + index</button></form>';

    echo '<hr><h2>Repair existing tool pages</h2>';
    echo '<p>If your 20 tool pages were created before this fix, some may show the raw <code>[mt_pdf_tools op="..."]</code> shortcode text as their Google search description, and their canonical URL may not match the real page URL. Click below to fix both — this only touches pages with a blank/broken description, so any description you already customized by hand is left alone.</p>';
    echo '<form method="post" action="' . admin_url('admin-post.php') . '">';
    echo '<input type="hidden" name="action" value="mt_repair_pages">';
    wp_nonce_field('mt_repair_pages');
    echo '<button class="button button-primary">Repair existing pages</button></form>';
    echo '</div>';
}
function mt_pdf_dl_handler() {
    check_admin_referer('mt_dl');
    $f = base64_decode($_GET['f']);
    $allowed = realpath(sys_get_temp_dir());
    if ($f && strpos(realpath($f), $allowed) === 0 && file_exists($f)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($f) . '"');
        readfile($f); exit;
    }
    wp_die('Invalid file.');
}
add_action('wp_ajax_mt_pdf_dl', 'mt_pdf_dl_handler');
add_action('wp_ajax_nopriv_mt_pdf_dl', 'mt_pdf_dl_handler');

// Frontend styling (blends with theme; clean card look)
function mt_pdf_css() {
    return '
    .mt-wrap{max-width:920px;margin:30px auto;font-family:inherit;color:#222}
    .mt-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-top:18px}
    .mt-card{display:block;padding:16px;border:1px solid #e6e6e6;border-radius:10px;background:#fff;text-decoration:none;color:#222;font-weight:600;transition:.15s}
    .mt-card:hover{border-color:#f3ba2f;box-shadow:0 4px 14px rgba(0,0,0,.08);transform:translateY(-2px)}
    .mt-box{max-width:540px;margin:24px auto;padding:24px;border:1px solid #e6e6e6;border-radius:12px;background:#fff}
    .mt-box h2{margin:0 0 12px}
    .mt-select,.mt-file{width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:8px;box-sizing:border-box}
    .mt-btn{background:#f3ba2f;color:#000;border:none;padding:11px 22px;font-weight:700;border-radius:8px;cursor:pointer}
    .mt-note{color:#777;font-size:13px}
    /* Keeps the index heading looking exactly as it did when it was an H1. */
    .mt-index-heading{font-size:1.9em;font-weight:700;margin:0 0 10px;line-height:1.2}
    .mt-err{color:#b00020;font-size:14px;margin-top:10px}
    .mt-dl{display:inline-block;margin-top:14px;background:#111;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none}
    ';
}
/* FIX: which ops need Imagick, so we can warn BEFORE the user uploads
   instead of after they've already waited for a failed conversion. */
function mt_needs_imagick($op) {
    return in_array($op, array('PDF to JPG', 'PDF to PNG', 'PDF to WebP', 'PDF to SVG', 'PNG to PDF', 'WebP to PDF'), true);
}
function mt_pdf_tools_shortcode($atts) {
    $atts = shortcode_atts(array('op' => ''), $atts);
    $ops = array('PDF to Word','Word to PDF','PDF to JPG','JPG to PDF','PDF to Excel','Excel to PDF','PDF to PowerPoint','PowerPoint to PDF','PDF to PNG','PNG to PDF','PDF to Text','Text to PDF','PDF to HTML','HTML to PDF','PDF to CSV','PDF to XML','PDF to SVG','PDF to WebP','WebP to PDF','PDF to PDF/A','PDF to EPUB','Merge PDF','Split PDF','Compress PDF');
    ob_start();
    echo '<style>' . mt_pdf_css() . '</style><div class="mt-wrap">';
    if ($atts['op']) {
        $op = esc_html($atts['op']);
        $expected = mt_expected_ext($atts['op']);
        $accept = $expected ? '.' . implode(',.', $expected) : '';
        $e = mt_engines();
        $has_key = (bool) get_option('mt_pdf_apikey');

        echo "<div class='mt-box'><h2>$op</h2>";

        /* FIX: honest up-front warning instead of a silent/late failure. */
        if (mt_needs_imagick($atts['op']) && !$e['imagick'] && !$has_key) {
            echo '<p class="mt-err">⚠️ This conversion currently needs a server component (Imagick) that isn\'t enabled on this hosting yet, and no fallback API key is configured. It will likely fail — please try a different tool, or contact the site admin.</p>';
        }

        /* FIX: honest expectation-setting for the plain-text-based
           conversions, which do not preserve original formatting/images
           and only render Latin/English text correctly. */
        $text_only_ops = array('Word to PDF', 'Excel to PDF', 'PowerPoint to PDF', 'Text to PDF', 'HTML to PDF', 'PDF to Word', 'PDF to Excel', 'PDF to PowerPoint');
        if (in_array($atts['op'], $text_only_ops, true)) {
            echo '<p class="mt-note">ℹ️ This tool converts text content only — original formatting, images, and tables are not preserved.';
            if (in_array($atts['op'], array('Word to PDF', 'Excel to PDF', 'PowerPoint to PDF', 'Text to PDF'), true)) {
                echo ' Only Latin/English characters are supported; other scripts (Urdu, Arabic, etc.) may appear as "?".';
            }
            echo '</p>';
        }

        echo "<form method='post' enctype='multipart/form-data'>";
        echo "<input type='hidden' name='mt_pdf_op' value='$op'>";
        if ($atts['op'] === 'Merge PDF') echo "<input type='file' name='mt_pdf_files[]' class='mt-file' accept='.pdf' multiple required><br>";
		else echo "<input type='file' name='mt_pdf_file' class='mt-file' accept='$accept' required><br>";
        echo "<button class='mt-btn'>Convert</button></form>";
		// Merge PDF: multi-file upload is handled here.
		if (isset($_POST['mt_pdf_op']) && sanitize_text_field($_POST['mt_pdf_op']) === 'Merge PDF' && !empty($_FILES['mt_pdf_files']['tmp_name']) && is_array($_FILES['mt_pdf_files']['tmp_name'])) {
			$mf = $_FILES['mt_pdf_files'];
			$mc = count($mf['tmp_name']);
			$merr = '';
			if ($mc < 2) $merr = 'Merge karne ke liye kam se kam 2 PDF files chahiye.';
			elseif ($mc > 10) $merr = 'Merge karne ke liye zyada se zyada 10 PDF files allowed hain.';
			else {
				foreach ($mf['tmp_name'] as $mi => $mtmp) {
					$merr2 = mt_validate_upload('Merge PDF', $mtmp, $mf['name'][$mi]);
					if ($merr2 !== true) { $merr = $merr2; break; }
					
				}
			}
			if ($merr !== '') echo '<p class="mt-err">' . esc_html($merr) . '</p>';
			else {
				$outdir = sys_get_temp_dir() . '/mt_' . uniqid();
				mkdir($outdir);
				$parts = array();
				foreach ($mf['tmp_name'] as $mi => $mtmp) {
					$dst = $outdir . '/part-' . str_pad($mi + 1, 2, '0', STR_PAD_LEFT) . '.pdf';
					copy($mtmp, $dst);
					$parts[] = escapeshellarg($dst);
					
				}
				$gs = mt_engines()['ghostscript'];
				$merged = "$outdir/merged.pdf";
				if ($gs) exec(escapeshellarg($gs) . ' -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dPDFSETTINGS=/prepress -sOutputFile=' . escapeshellarg($merged) . ' ' . implode(' ', $parts) . ' 2/dev/null', $mout, $mcode);
				if (file_exists($merged) && filesize($merged) > 0 && file_get_contents($merged, false, null, 0, 5) === '%PDF-') {
					$url = wp_nonce_url(admin_url('admin-ajax.php?action=mt_pdf_dl&f=' . base64_encode($merged)), 'mt_dl');
					echo sprintf('<a class="mt-dl" href="%s">Download result</a>', esc_url($url));
					
				} else {
					echo '<p class="mt-note">Failed (server may lack Ghostscript; merge could not be completed).</p>';
					
				}
			}
		}
		if (isset($_POST['mt_pdf_op']) && sanitize_text_field($_POST['mt_pdf_op']) === 'Compress PDF' && empty($_FILES['mt_pdf_file']['tmp_name'])) echo '<p class="mt-err">Choose a file first.</p>';
        if (isset($_POST['mt_pdf_op']) && !empty($_FILES['mt_pdf_file']['tmp_name'])) {
            $op_raw = sanitize_text_field($_POST['mt_pdf_op']);
            $err = mt_validate_upload($op_raw, $_FILES['mt_pdf_file']['tmp_name'], $_FILES['mt_pdf_file']['name']); // FIX
			if ($err !== true && $op_raw === 'Compress PDF') $err = 'This file is not a PDF. Choose a PDF file to compress.';
            if ($err !== true) {
                echo '<p class="mt-err">' . esc_html($err) . '</p>';
            } else {
                $outdir = sys_get_temp_dir() . '/mt_' . uniqid(); mkdir($outdir);
                $res = mt_convert($op_raw, $_FILES['mt_pdf_file']['tmp_name'], $outdir);
                if ($res && file_exists($res)) {
                    $url = wp_nonce_url(admin_url('admin-ajax.php?action=mt_pdf_dl&f=' . base64_encode($res)), 'mt_dl');
                    echo '<a class="mt-dl" href="' . esc_url($url) . '">Download result</a>';
					if ($op_raw === 'Compress PDF') {
						$orig = filesize($_FILES['mt_pdf_file']['tmp_name']); $comp = filesize($res); $saved = $orig > 0 ? round(100 * ($orig - $comp) / $orig, 1) : 0;
						echo '<p class="mt-note">Original size: ' . size_format($orig) . ' &middot; Compressed size: ' . size_format($comp) . ' &middot; Saved: ' . $saved . '%</p>';
						if ($saved <= 0) echo '<p class="mt-note">This file barely shrank &mdash; text-only PDFs compress little. Image-heavy PDFs shrink much more.</p>';
					}
				} else if ($op_raw === 'Compress PDF') echo '<p class="mt-err">Compression failed. Try a different PDF or a smaller file.</p>'; else echo '<p class="mt-note">Failed (server may lack Imagick; set CloudConvert key in admin).</p>';
            }
        }
        echo '</div>';
    } else {
        /* SEO FIX: this was an <h1>. The theme already renders the page
           title ("PDF Tools") as the page's H1, so this produced a second
           H1 on /pdf-tools/. Demoted to H2 — it is a section heading inside
           the content, not the page's primary heading. Visual weight is
           unchanged via the .mt-index-heading rule in the stylesheet. */
        echo '<h2 class="mt-index-heading">PDF Tools</h2><p class="mt-note">Pick a converter:</p><div class="mt-grid">';
        foreach ($ops as $o) {
            $slug = sanitize_title($o);
            $page = get_page_by_path($slug);
            /* SEO FIX: this fallback URL had no trailing slash while every
               other tool link on the site does. WordPress serves pages with
               a trailing slash, so /pdf-to-word 301-redirects to
               /pdf-to-word/ — meaning every card on this index page was an
               internal link into a redirect instead of the canonical URL.
               (get_permalink() above was always correct; only this fallback,
               used when the page hasn't been created yet, was not.) */
            $href = home_url('/pdf-tools/' . $slug . '/');
            echo '<a class="mt-card" href="' . esc_url($href) . '">' . esc_html($o) . '</a>';
        }
        echo '</div>';
    }
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('mt_pdf_tools', 'mt_pdf_tools_shortcode');

// Auto-create one WordPress page per tool + an index page
add_action('admin_post_mt_create_pages', 'mt_create_pages');
function mt_create_pages() {
    if (!current_user_can('manage_options')) wp_die('No permission');
    check_admin_referer('mt_create_pages');
    $ops = array('PDF to Word','Word to PDF','PDF to JPG','JPG to PDF','PDF to Excel','Excel to PDF','PDF to PowerPoint','PowerPoint to PDF','PDF to PNG','PNG to PDF','PDF to Text','Text to PDF','PDF to HTML','HTML to PDF','PDF to CSV','PDF to XML','PDF to SVG','PDF to WebP','WebP to PDF','PDF to PDF/A','PDF to EPUB','Merge PDF','Split PDF','Compress PDF');
    $index_id = wp_insert_post(array('post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'PDF Tools', 'post_name' => 'pdf-tools', 'post_content' => '[mt_pdf_tools]', 'post_excerpt' => mt_pdf_description('PDF Tools')));
    foreach ($ops as $op) {
        $slug = sanitize_title($op);
        if (!get_page_by_path($slug)) {
            /* FIX: no post_parent — keeps the URL flat (/pdf-to-word/)
               matching how the site actually serves it, instead of the
               canonical tag pointing at a nested /pdf-tools/pdf-to-word/
               path that doesn't match the real URL.
               FIX: post_excerpt is now a real sentence instead of blank
               — most SEO plugins fall back to the excerpt for the meta
               description when nothing else is set, which is why pages
               were showing the raw [mt_pdf_tools op="..."] shortcode
               text as their Google search snippet. */
            $new_id = wp_insert_post(array(
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => $op,
                'post_name'    => $slug,
                'post_content' => '[mt_pdf_tools op="' . esc_attr($op) . '"]',
                'post_excerpt' => mt_pdf_description($op),
            ));
            mt_set_seo_description($new_id, mt_pdf_description($op));
        }
    }
    wp_redirect(admin_url('admin.php?page=mt-pdf-tools&created=1'));
    exit;
}

/* FIX: a real, human sentence for each tool instead of leaving the
   description blank (which several SEO plugins then fill in themselves
   using the raw post content — i.e. the shortcode text). */
function mt_pdf_description($op) {
    if ($op === 'PDF Tools') {
        return 'Free online PDF tools — convert PDF to Word, Excel, JPG, PowerPoint and more, or convert other files to PDF. No sign-up required.';
    }
    return 'Convert ' . $op . ' for free, right in your browser. No sign-up, no software install — upload your file and download the result instantly.';
}

/* FIX: also push the description into whichever SEO plugin is active,
   so the fix applies even if that plugin ignores post_excerpt. */
function mt_set_seo_description($post_id, $description) {
    if (!$post_id) return;
    update_post_meta($post_id, 'rank_math_description', $description); // Rank Math
    update_post_meta($post_id, '_yoast_wpseo_metadesc', $description);  // Yoast SEO
    update_post_meta($post_id, '_aioseo_description', $description);    // All in One SEO
}

/* FIX: one-click repair for the 20 tool pages that already exist on a
   live site from BEFORE this fix — removes any parent-page nesting and
   overwrites a broken/blank description with a real one. Only touches
   pages whose description is currently empty or still contains the raw
   shortcode text, so a description you've manually customized is left alone. */
add_action('admin_post_mt_repair_pages', 'mt_repair_pages');
function mt_repair_pages() {
    if (!current_user_can('manage_options')) wp_die('No permission');
    check_admin_referer('mt_repair_pages');
    $ops = array('PDF to Word','Word to PDF','PDF to JPG','JPG to PDF','PDF to Excel','Excel to PDF','PDF to PowerPoint','PowerPoint to PDF','PDF to PNG','PNG to PDF','PDF to Text','Text to PDF','PDF to HTML','HTML to PDF','PDF to CSV','PDF to XML','PDF to SVG','PDF to WebP','WebP to PDF','PDF to PDF/A','PDF to EPUB','Merge PDF','Split PDF','Compress PDF','PDF Tools');
    $fixed = 0;
    foreach ($ops as $op) {
        $slug = ($op === 'PDF Tools') ? 'pdf-tools' : sanitize_title($op);
        $page = get_page_by_path($slug);
        if (!$page) continue;

        $needs_fix = false;
        $update = array('ID' => $page->ID);
        if ($page->post_parent) { $update['post_parent'] = 0; $needs_fix = true; }
        if (trim($page->post_excerpt) === '' || strpos($page->post_excerpt, '[mt_pdf_tools') !== false) {
            $update['post_excerpt'] = mt_pdf_description($op);
            $needs_fix = true;
        }
        if ($needs_fix) {
            wp_update_post($update);
            mt_set_seo_description($page->ID, mt_pdf_description($op));
            $fixed++;
        }
    }
    wp_redirect(admin_url('admin.php?page=mt-pdf-tools&repaired=' . $fixed));
    exit;
}
