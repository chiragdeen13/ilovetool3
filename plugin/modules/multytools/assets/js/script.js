/* ============ MultyTools JS v1.0 ============ */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); }
        else { document.addEventListener('DOMContentLoaded', fn); }
    }

    function $(id) { return document.getElementById(id); }
    function val(id) { var el = $(id); return el ? el.value.trim() : ''; }
    function num(id) { return parseFloat(val(id)); }
    function fmt(n, dec) {
        return Number(n).toLocaleString('en-US', { maximumFractionDigits: dec === undefined ? 2 : dec });
    }

    function showResult(tool, html) {
        var box = tool.querySelector('.mt-result');
        var err = tool.querySelector('.mt-error');
        if (err) { err.style.display = 'none'; }
        if (!box) { return; }
        box.innerHTML = html;
        box.style.display = 'block';
    }
    function showError(tool, msg) {
        var box = tool.querySelector('.mt-error');
        var res = tool.querySelector('.mt-result');
        if (res) { res.style.display = 'none'; }
        box.textContent = msg;
        box.style.display = 'block';
    }

    /* ================= TEXT HELPERS ================= */

    var ONES = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    var TENS = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    function threeDigitsWords(n) {
        var parts = [];
        if (n >= 100) {
            parts.push(ONES[Math.floor(n / 100)], 'Hundred');
            n %= 100;
        }
        if (n >= 20) {
            parts.push(TENS[Math.floor(n / 10)]);
            n %= 10;
            if (n > 0) { parts.push(ONES[n]); }
        } else if (n > 0) {
            parts.push(ONES[n]);
        }
        return parts.join(' ');
    }

    function internationalWords(numIn) {
        numIn = Math.floor(numIn);
        if (numIn === 0) { return 'Zero'; }
        var units = [[1e12, 'Trillion'], [1e9, 'Billion'], [1e6, 'Million'], [1e3, 'Thousand']];
        var parts = [];
        for (var i = 0; i < units.length; i++) {
            if (numIn >= units[i][0]) {
                parts.push(threeDigitsWords(Math.floor(numIn / units[i][0])) + ' ' + units[i][1]);
                numIn %= units[i][0];
            }
        }
        if (numIn > 0) { parts.push(threeDigitsWords(numIn)); }
        return parts.join(' ');
    }

    function indianWords(numIn) {
        numIn = Math.floor(numIn);
        if (numIn === 0) { return 'Zero'; }
        var parts = [];
        if (numIn >= 1e7) {
            parts.push(internationalWords(Math.floor(numIn / 1e7)) + ' Crore');
            numIn %= 1e7;
        }
        if (numIn >= 1e5) {
            parts.push(threeDigitsWords(Math.floor(numIn / 1e5)) + ' Lakh');
            numIn %= 1e5;
        }
        if (numIn >= 1e3) {
            parts.push(threeDigitsWords(Math.floor(numIn / 1e3)) + ' Thousand');
            numIn %= 1e3;
        }
        if (numIn > 0) { parts.push(threeDigitsWords(numIn)); }
        return parts.join(' ');
    }

    /* ================= MD5 (public domain algorithm) ================= */
    var HEX_CHR = '0123456789abcdef'.split('');

    function md5cmn(q, a, b, x, s, t) {
        a = (((a + q) | 0) + ((x + t) | 0)) | 0;
        return (((a << s) | (a >>> (32 - s))) + b) | 0;
    }
    function md5ff(a, b, c, d, x, s, t) { return md5cmn((b & c) | (~b & d), a, b, x, s, t); }
    function md5gg(a, b, c, d, x, s, t) { return md5cmn((b & d) | (c & ~d), a, b, x, s, t); }
    function md5hh(a, b, c, d, x, s, t) { return md5cmn(b ^ c ^ d, a, b, x, s, t); }
    function md5ii(a, b, c, d, x, s, t) { return md5cmn(c ^ (b | ~d), a, b, x, s, t); }

    function md5cycle(x, k) {
        var a = x[0], b = x[1], c = x[2], d = x[3];

        a = md5ff(a, b, c, d, k[0], 7, -680876936);
        d = md5ff(d, a, b, c, k[1], 12, -389564586);
        c = md5ff(c, d, a, b, k[2], 17, 606105819);
        b = md5ff(b, c, d, a, k[3], 22, -1044525330);
        a = md5ff(a, b, c, d, k[4], 7, -176418897);
        d = md5ff(d, a, b, c, k[5], 12, 1200080426);
        c = md5ff(c, d, a, b, k[6], 17, -1473231341);
        b = md5ff(b, c, d, a, k[7], 22, -45705983);
        a = md5ff(a, b, c, d, k[8], 7, 1770035416);
        d = md5ff(d, a, b, c, k[9], 12, -1958414417);
        c = md5ff(c, d, a, b, k[10], 17, -42063);
        b = md5ff(b, c, d, a, k[11], 22, -1990404162);
        a = md5ff(a, b, c, d, k[12], 7, 1804603682);
        d = md5ff(d, a, b, c, k[13], 12, -40341101);
        c = md5ff(c, d, a, b, k[14], 17, -1502002290);
        b = md5ff(b, c, d, a, k[15], 22, 1236535329);

        a = md5gg(a, b, c, d, k[1], 5, -165796510);
        d = md5gg(d, a, b, c, k[6], 9, -1069501632);
        c = md5gg(c, d, a, b, k[11], 14, 643717713);
        b = md5gg(b, c, d, a, k[0], 20, -373897302);
        a = md5gg(a, b, c, d, k[5], 5, -701558691);
        d = md5gg(d, a, b, c, k[10], 9, 38016083);
        c = md5gg(c, d, a, b, k[15], 14, -660478335);
        b = md5gg(b, c, d, a, k[4], 20, -405537848);
        a = md5gg(a, b, c, d, k[9], 5, 568446438);
        d = md5gg(d, a, b, c, k[14], 9, -1019803690);
        c = md5gg(c, d, a, b, k[3], 14, -187363961);
        b = md5gg(b, c, d, a, k[8], 20, 1163531501);
        a = md5gg(a, b, c, d, k[13], 5, -1444681467);
        d = md5gg(d, a, b, c, k[2], 9, -51403784);
        c = md5gg(c, d, a, b, k[7], 14, 1735328473);
        b = md5gg(b, c, d, a, k[12], 20, -1926607734);

        a = md5hh(a, b, c, d, k[5], 4, -378558);
        d = md5hh(d, a, b, c, k[8], 11, -2022574463);
        c = md5hh(c, d, a, b, k[11], 16, 1839030562);
        b = md5hh(b, c, d, a, k[14], 23, -35309556);
        a = md5hh(a, b, c, d, k[1], 4, -1530992060);
        d = md5hh(d, a, b, c, k[4], 11, 1272893353);
        c = md5hh(c, d, a, b, k[7], 16, -155497632);
        b = md5hh(b, c, d, a, k[10], 23, -1094730640);
        a = md5hh(a, b, c, d, k[13], 4, 681279174);
        d = md5hh(d, a, b, c, k[0], 11, -358537222);
        c = md5hh(c, d, a, b, k[3], 16, -722521979);
        b = md5hh(b, c, d, a, k[6], 23, 76029189);
        a = md5hh(a, b, c, d, k[9], 4, -640364487);
        d = md5hh(d, a, b, c, k[12], 11, -421815835);
        c = md5hh(c, d, a, b, k[15], 16, 530742520);
        b = md5hh(b, c, d, a, k[2], 23, -995338651);

        a = md5ii(a, b, c, d, k[0], 6, -198630844);
        d = md5ii(d, a, b, c, k[7], 10, 1126891415);
        c = md5ii(c, d, a, b, k[14], 15, -1416354905);
        b = md5ii(b, c, d, a, k[5], 21, -57434055);
        a = md5ii(a, b, c, d, k[12], 6, 1700485571);
        d = md5ii(d, a, b, c, k[3], 10, -1894986606);
        c = md5ii(c, d, a, b, k[10], 15, -1051523);
        b = md5ii(b, c, d, a, k[1], 21, -2054922799);
        a = md5ii(a, b, c, d, k[8], 6, 1873313359);
        d = md5ii(d, a, b, c, k[15], 10, -30611744);
        c = md5ii(c, d, a, b, k[6], 15, -1560198380);
        b = md5ii(b, c, d, a, k[13], 21, 1309151649);
        a = md5ii(a, b, c, d, k[4], 6, -145523070);
        d = md5ii(d, a, b, c, k[11], 10, -1120210379);
        c = md5ii(c, d, a, b, k[2], 15, 718787259);
        b = md5ii(b, c, d, a, k[9], 21, -343485551);

        x[0] = (a + x[0]) | 0;
        x[1] = (b + x[1]) | 0;
        x[2] = (c + x[2]) | 0;
        x[3] = (d + x[3]) | 0;
    }

    function md5blk(s) {
        var blks = [], i;
        for (i = 0; i < 64; i += 4) {
            blks[i >> 2] = s.charCodeAt(i)
                + (s.charCodeAt(i + 1) << 8)
                + (s.charCodeAt(i + 2) << 16)
                + (s.charCodeAt(i + 3) << 24);
        }
        return blks;
    }

    function md51(s) {
        var n = s.length;
        var state = [1732584193, -271733879, -1732584194, 271733878];
        var i;
        for (i = 64; i <= n; i += 64) {
            md5cycle(state, md5blk(s.substring(i - 64, i)));
        }
        s = s.substring(i - 64);
        var tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for (i = 0; i < s.length; i++) {
            tail[i >> 2] |= s.charCodeAt(i) << ((i % 4) << 3);
        }
        tail[i >> 2] |= 0x80 << ((i % 4) << 3);
        if (i > 55) {
            md5cycle(state, tail);
            for (i = 0; i < 16; i++) { tail[i] = 0; }
        }
        tail[14] = n * 8;
        md5cycle(state, tail);
        return state;
    }

    function rhex(n) {
        var s = '', j;
        for (j = 0; j < 4; j++) {
            s += HEX_CHR[(n >> (j * 8 + 4)) & 0x0f] + HEX_CHR[(n >> (j * 8)) & 0x0f];
        }
        return s;
    }

    function computeMd5(inputStr) {
        var utf8 = new TextEncoder().encode(inputStr);
        var bin = '';
        for (var i = 0; i < utf8.length; i++) { bin += String.fromCharCode(utf8[i]); }
        var state = md51(bin);
        var out = '';
        for (var j = 0; j < 4; j++) { out += rhex(state[j]); }
        return out;
    }

    async function computeSha(algo, inputStr) {
        var data = new TextEncoder().encode(inputStr);
        var buf = await window.crypto.subtle.digest(algo, data);
        var arr = Array.from(new Uint8Array(buf));
        return arr.map(function (b) { return b.toString(16).padStart(2, '0'); }).join('');
    }

    /* ================= BASE64 ================= */
    function b64Encode(str) {
        var bytes = new TextEncoder().encode(str);
        var bin = '';
        bytes.forEach(function (b) { bin += String.fromCharCode(b); });
        return btoa(bin);
    }
    function b64Decode(str) {
        var bin = atob(str.replace(/\s/g, ''));
        var bytes = Uint8Array.from(bin, function (c) { return c.charCodeAt(0); });
        return new TextDecoder().decode(bytes);
    }

    /* ================= UUID ================= */
    function uuidV4() {
        if (window.crypto && window.crypto.randomUUID) { return window.crypto.randomUUID(); }
        return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, function (c) {
            var r = window.crypto.getRandomValues(new Uint8Array(1))[0] % 16;
            return (c ^ r).toString(16);
        });
    }

    /* ================= COLOR ================= */
    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        if (hex.length === 3) { hex = hex.split('').map(function (c) { return c + c; }).join(''); }
        var r = parseInt(hex.slice(0, 2), 16);
        var g = parseInt(hex.slice(2, 4), 16);
        var b = parseInt(hex.slice(4, 6), 16);
        return { r: r, g: g, b: b };
    }

    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h = 0, s = 0, l = (max + min) / 2;

        if (max !== min) {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = ((g - b) / d + (g < b ? 6 : 0)); break;
                case g: h = ((b - r) / d + 2); break;
                default: h = ((r - g) / d + 4);
            }
            h /= 6;
        }
        return {
            h: Math.round(h * 360),
            s: Math.round(s * 100),
            l: Math.round(l * 100)
        };
    }

    /* ================= ZODIAC ================= */
    function getZodiac(month, day) {
        var signs = [
            [120, 'Capricorn &#128052;'], [219, 'Aquarius &#128014;'], [320, 'Pisces &#128011;'],
            [420, 'Aries &#128016;'], [521, 'Taurus &#128002;'], [621, 'Gemini &#128092;'],
            [722, 'Cancer &#128025;'], [823, 'Leo &#128012;'], [923, 'Virgo &#129691;'],
            [1023, 'Libra &#9878;'], [1122, 'Scorpio &#129710;'], [1222, 'Sagittarius &#127993;'],
            [1232, 'Capricorn &#128052;']
        ];
        var md = month * 100 + day;
        for (var i = 0; i < signs.length; i++) {
            if (md <= signs[i][0]) { return signs[i][1]; }
        }
        return 'Capricorn &#128052;';
    }

    /* ================= TOOL ACTIONS ================= */
    var tools = {

        /* ---------- CASE CONVERTER ---------- */
        'case-upper': function () {
            $('mt-case-output').value = $('mt-case-input').value.toUpperCase();
        },
        'case-lower': function () {
            $('mt-case-output').value = $('mt-case-input').value.toLowerCase();
        },
        'case-title': function () {
            $('mt-case-output').value = $('mt-case-input').value.toLowerCase()
                .replace(/\b\w/g, function (c) { return c.toUpperCase(); });
        },
        'case-sentence': function () {
            var t = $('mt-case-input').value.toLowerCase();
            $('mt-case-output').value = t.replace(/(^\s*\w|[.!?]\s+\w)/g, function (c) { return c.toUpperCase(); });
        },
        'case-camel': function () {
            var words = $('mt-case-input').value.split(/[^A-Za-z0-9]+/).filter(Boolean);
            $('mt-case-output').value = words.map(function (w, i) {
                w = w.toLowerCase();
                return i === 0 ? w : w.charAt(0).toUpperCase() + w.slice(1);
            }).join('');
        },
        'case-pascal': function () {
            var words = $('mt-case-input').value.split(/[^A-Za-z0-9]+/).filter(Boolean);
            $('mt-case-output').value = words.map(function (w) {
                w = w.toLowerCase();
                return w.charAt(0).toUpperCase() + w.slice(1);
            }).join('');
        },
        'case-snake': function () {
            var words = $('mt-case-input').value.split(/[^A-Za-z0-9]+/).filter(Boolean);
            $('mt-case-output').value = words.map(function (w) { return w.toLowerCase(); }).join('_');
        },
        'case-kebab': function () {
            var words = $('mt-case-input').value.split(/[^A-Za-z0-9]+/).filter(Boolean);
            $('mt-case-output').value = words.map(function (w) { return w.toLowerCase(); }).join('-');
        },
        'case-alternate': function () {
            var t = $('mt-case-input').value;
            var out = '', idx = 0;
            for (var i = 0; i < t.length; i++) {
                var ch = t[i];
                if (/[a-zA-Z]/.test(ch)) {
                    out += idx % 2 === 0 ? ch.toLowerCase() : ch.toUpperCase();
                    idx++;
                } else {
                    out += ch;
                }
            }
            $('mt-case-output').value = out;
        },
        'case-clear': function () {
            $('mt-case-input').value = '';
            $('mt-case-output').value = '';
        },

        /* ---------- NUMBER TO WORDS ---------- */
        numwords: function (tool) {
            var number = num('mt-nw-number');
            var currency = val('mt-nw-currency');

            if (isNaN(number)) { showError(tool, 'Please enter a valid number.'); return; }
            if (number < 0) { showError(tool, 'Please enter a positive number.'); return; }
            if (number >= 1e15) { showError(tool, 'Number too large! Maximum supported is 999 Trillion.'); return; }

            var whole = Math.floor(number);
            var decimals = Math.round((number - whole) * 100);
            var result;

            if (currency === 'plain') {
                result = indianWords(whole) || 'Zero';
                if (val('mt-nw-currency') === 'plain') {
                    /* use international for plain */
                }
                result = internationalWords(whole);
            } else if (currency === 'usd') {
                result = 'US Dollars ' + internationalWords(whole);
                if (decimals > 0) { result += ' and ' + internationalWords(decimals) + ' Cents'; }
                result += ' Only';
            } else {
                var curName = currency === 'pkr' ? 'Rupees' : 'Rupees';
                var subName = 'Paisa';
                result = curName + ' ' + indianWords(whole);
                if (decimals > 0) { result += ' and ' + indianWords(decimals) + ' ' + subName; }
                result += ' Only';
            }

            showResult(tool,
                '<div class="mt-res-big">' + fmt(number, 2) + '</div>' +
                '<div class="mt-res-row"><span>In Words</span><strong style="font-size:15px;text-transform:none;">' + result + '</strong></div>'
            );
        },

        /* ---------- DUPLICATE LINES ---------- */
        duplines: function (tool) {
            var text = $('mt-dup-input').value;
            if (!text.trim()) { showError(tool, 'Please paste some text first.'); return; }

            var trim = $('mt-dup-trim').checked;
            var insensitive = $('mt-dup-insensitive').checked;
            var removeEmpty = $('mt-dup-removeempty').checked;
            var sortAZ = $('mt-dup-sort').checked;

            var lines = text.split(/\r?\n/);
            var originalCount = lines.filter(function (l) { return l.trim(); }).length;

            var seen = {};
            var unique = [];
            for (var i = 0; i < lines.length; i++) {
                var line = trim ? lines[i].trim() : lines[i];
                if (removeEmpty && line === '') { continue; }
                var key = insensitive ? line.toLowerCase() : line;
                if (!(key in seen)) {
                    seen[key] = true;
                    unique.push(line);
                }
            }

            if (sortAZ) { unique.sort(function (a, b) { return a.localeCompare(b); }); }

            var removed = originalCount - unique.length;

            $('mt-dup-output').value = unique.join('\n');
            showResult(tool,
                '<div class="mt-res-row"><span>Total Lines</span><strong>' + originalCount + '</strong></div>' +
                '<div class="mt-res-row"><span>Unique Lines</span><strong class="it3-green mt-green">' + unique.length + '</strong></div>' +
                '<div class="mt-res-row"><span>Duplicates Removed</span><strong style="color:#ef4444;">' + removed + '</strong></div>'
            );
        },

        /* ---------- LOREM IPSUM ---------- */
        lorem: function (tool) {
            var type = val('mt-li-type');
            var count = parseInt(val('mt-li-count'), 10) || 3;
            if (count < 1 || count > 100) { showError(tool, 'Count must be between 1 and 100.'); return; }

            var bank = ['lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'sed', 'do',
                'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore', 'magna', 'aliqua', 'enim',
                'ad', 'minim', 'veniam', 'quis', 'nostrud', 'exercitation', 'ullamco', 'laboris', 'nisi', 'aliquip',
                'ex', 'ea', 'commodo', 'consequat', 'duis', 'aute', 'irure', 'in', 'reprehenderit', 'voluptate',
                'velit', 'esse', 'cillum', 'eu', 'fugiat', 'nulla', 'pariatur', 'excepteur', 'sint', 'occaecat',
                'cupidatat', 'non', 'proident', 'sunt', 'culpa', 'qui', 'officia', 'deserunt', 'mollit', 'anim',
                'id', 'est', 'laborum'];

            function randWord() { return bank[Math.floor(Math.random() * bank.length)]; }

            function randSentence(minW, maxW) {
                var wc = minW + Math.floor(Math.random() * (maxW - minW + 1));
                var words = [];
                for (var i = 0; i < wc; i++) { words.push(randWord()); }
                var s = words.join(' ');
                return s.charAt(0).toUpperCase() + s.slice(1) + '.';
            }

            function randParagraph() {
                var sc = 4 + Math.floor(Math.random() * 3);
                var sentences = [];
                for (var i = 0; i < sc; i++) { sentences.push(randSentence(6, 14)); }
                return sentences.join(' ');
            }

            var output = '';

            if (type === 'words') {
                var words = [];
                for (var w = 0; w < count; w++) { words.push(randWord()); }
                output = 'Lorem ipsum dolor sit amet, ' + words.slice(5).join(' ');
            } else if (type === 'sentences') {
                var sents = ['Lorem ipsum dolor sit amet, consectetur adipiscing elit.'];
                for (var s = 1; s < count; s++) { sents.push(randSentence(8, 16)); }
                output = sents.join(' ');
            } else {
                var paras = [];
                for (var p = 0; p < count; p++) {
                    if (p === 0) {
                        paras.push('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ' + randParagraph());
                    } else {
                        paras.push(randParagraph());
                    }
                }
                output = paras.join('\n\n');
            }

            $('mt-li-output').value = output;
        },

        /* ---------- REMOVE LINE BREAKS ---------- */
        linebreaks: function (tool) {
            var text = $('mt-lb-input').value;
            if (!text.trim()) { showError(tool, 'Please paste some text first.'); return; }

            var mode = val('mt-lb-replace');
            var replaceWith;
            if (mode === 'space') { replaceWith = ' '; }
            else if (mode === 'none') { replaceWith = ''; }
            else if (mode === 'comma') { replaceWith = ', '; }
            else { replaceWith = $('mt-lb-custom').value || ' '; }

            $('mt-lb-output').value = text.replace(/\r?\n+/g, replaceWith);
        },

        /* ---------- COMPOUND INTEREST ---------- */
        compound: function (tool) {
            var principal = num('mt-ci-principal');
            var rate = num('mt-ci-rate');
            var years = num('mt-ci-years');
            var freq = parseFloat(val('mt-ci-freq'));
            var monthlyAdd = num('mt-ci-monthly') || 0;

            if (!principal || principal <= 0) { showError(tool, 'Please enter a valid principal amount.'); return; }
            if (isNaN(rate) || rate < 0) { showError(tool, 'Please enter a valid interest rate.'); return; }
            if (!years || years <= 0) { showError(tool, 'Please enter a valid time period.'); return; }

            var i = rate / 100 / freq;
            var periods = Math.round(freq * years);
            var fvPrincipal = principal * Math.pow(1 + i, periods);

            var totalDeposited = 0;
            var fvDeposits = 0;

            if (monthlyAdd > 0) {
                totalDeposited = monthlyAdd * 12 * years;
                var pmtPerPeriod = monthlyAdd * (12 / freq);
                if (i === 0) {
                    fvDeposits = pmtPerPeriod * periods;
                } else {
                    fvDeposits = pmtPerPeriod * ((Math.pow(1 + i, periods) - 1) / i);
                }
            }

            var finalValue = fvPrincipal + fvDeposits;
            var invested = principal + totalDeposited;
            var interestEarned = finalValue - invested;

            showResult(tool,
                '<div class="mt-res-row"><span>Total Invested</span><strong>' + fmt(invested, 0) + '</strong></div>' +
                '<div class="mt-res-row"><span>Interest Earned</span><strong class="mt-green">' + fmt(interestEarned, 0) + '</strong></div>' +
                '<div class="mt-res-row"><span>Future Value After ' + years + ' Years</span><strong class="mt-highlight" style="font-size:22px;">' + fmt(finalValue, 0) + '</strong></div>' +
                '<p style="margin-top:10px;font-size:12px;color:#94a3b8;">Compounded ' + (freq === 365 ? 'daily' : freq === 12 ? 'monthly' : freq === 4 ? 'quarterly' : freq === 2 ? 'half-yearly' : 'yearly') + '</p>'
            );
        },

        /* ---------- ZAKAT ---------- */
        zakat: function (tool) {
            var goldRate = num('mt-zk-goldrate');
            var silverRate = num('mt-zk-silverrate');

            if (!goldRate || goldRate <= 0) { showError(tool, 'Please enter current gold rate per gram.'); return; }
            if (!silverRate || silverRate <= 0) { showError(tool, 'Please enter current silver rate per gram.'); return; }

            var assets = {
                gold: num('mt-zk-gold') || 0,
                silver: num('mt-zk-silver') || 0,
                cash: num('mt-zk-cash') || 0,
                inv: num('mt-zk-investments') || 0,
                biz: num('mt-zk-business') || 0,
                lent: num('mt-zk-lent') || 0
            };
            var debts = num('mt-zk-debts') || 0;

            var totalAssets = assets.gold + assets.silver + assets.cash + assets.inv + assets.biz + assets.lent;
            var netWealth = totalAssets - debts;

            var standard = val('mt-zk-standard');
            var nisab = standard === 'gold'
                ? goldRate * 87.48
                : silverRate * 612.36;

            var aboveNisab = netWealth >= nisab;
            var zakatDue = aboveNisab ? netWealth * 0.025 : 0;

            showResult(tool,
                '<div class="mt-res-row"><span>Total Assets</span><strong>' + fmt(totalAssets, 0) + '</strong></div>' +
                '<div class="mt-res-row"><span>Net Wealth (after debts)</span><strong>' + fmt(netWealth, 0) + '</strong></div>' +
                '<div class="mt-res-row"><span>Nisab (' + standard + ')</span><strong>' + fmt(nisab, 0) + '</strong></div>' +
                '<div class="mt-res-row"><span>Status</span><strong class="' + (aboveNisab ? 'mt-green' : '') + '">' + (aboveNisab ? 'Above Nisab — Zakat Due' : 'Below Nisab — No Zakat') + '</strong></div>' +
                '<div class="mt-res-big" style="margin-top:12px;">Zakat Due (2.5%): <span class="mt-highlight">' + fmt(zakatDue, 0) + '</span></div>' +
                '<p style="margin-top:10px;font-size:12px;color:#94a3b8;">* Estimate for guidance. Consult a scholar for your specific situation.</p>'
            );
        },

        /* ---------- ELECTRICITY BILL ---------- */
        electric: function (tool) {
            var units = num('mt-eb-units');
            var type = val('mt-eb-type');

            if (!units || units <= 0) { showError(tool, 'Please enter valid units consumed.'); return; }
            if (type === 'protected' && units > 200) {
                showError(tool, 'Protected consumers can only use up to 200 units. Select Unprotected.');
                return;
            }

            var unprotectedSlabs = [[100, 25.30], [200, 32.26], [300, 37.92], [400, 43.40], [500, 47.83], [600, 49.80], [700, 52.13], [Infinity, 55.70]];
            var protectedSlabs = [[100, 19.14], [Infinity, 22.10]];
            var slabs = type === 'protected' ? protectedSlabs : unprotectedSlabs;

            var remaining = units;
            var prevLimit = 0;
            var energyCost = 0;
            var breakdown = [];

            for (var s = 0; s < slabs.length && remaining > 0; s++) {
                var limit = Math.min(slabs[s][0], Infinity);
                var slabUnits = Math.min(remaining, limit - prevLimit);
                if (slabUnits <= 0) { break; }
                var cost = slabUnits * slabs[s][1];
                energyCost += cost;
                breakdown.push('<div class="mt-res-row"><span>' + (prevLimit + 1) + '-' + (limit === Infinity ? '+' : limit) + ' units @ ' + slabs[s][1].toFixed(2) + '</span><strong>Rs ' + fmt(cost, 2) + '</strong></div>');
                remaining -= slabUnits;
                prevLimit = limit;
            }

            var fpaRate = num('mt-eb-fpa') || 0;
            var fixedCharges = num('mt-eb-fixed') || 0;
            var gstPct = num('mt-eb-gst') || 0;
            var dutyPct = num('mt-eb-duty') || 0;
            var tvFee = $('mt-eb-tvfee').checked ? 35 : 0;
            var meterRent = $('mt-eb-meter').checked ? 15 : 0;

            var fpa = units * fpaRate;
            var taxableAmount = energyCost + fpa;
            var gst = taxableAmount * gstPct / 100;
            var duty = energyCost * dutyPct / 100;

            var total = energyCost + fpa + fixedCharges + gst + duty + tvFee + meterRent;

            showResult(tool,
                '<h4 class="mt-subtitle">Energy Cost Breakdown</h4>' + breakdown.join('') +
                '<div class="mt-res-row"><span>Energy Cost Total</span><strong>Rs ' + fmt(energyCost, 2) + '</strong></div>' +
                (fpa > 0 ? '<div class="mt-res-row"><span>FPA (' + fpaRate + '/unit)</span><strong>Rs ' + fmt(fpa, 2) + '</strong></div>' : '') +
                '<div class="mt-res-row"><span>Fixed Charges</span><strong>Rs ' + fmt(fixedCharges, 2) + '</strong></div>' +
                '<div class="mt-res-row"><span>GST (' + gstPct + '%)</span><strong>Rs ' + fmt(gst, 2) + '</strong></div>' +
                (duty > 0 ? '<div class="mt-res-row"><span>Electricity Duty (' + dutyPct + '%)</span><strong>Rs ' + fmt(duty, 2) + '</strong></div>' : '') +
                (tvFee ? '<div class="mt-res-row"><span>TV Fee</span><strong>Rs 35.00</strong></div>' : '') +
                (meterRent ? '<div class="mt-res-row"><span>Meter Rent</span><strong>Rs 15.00</strong></div>' : '') +
                '<div class="mt-res-big" style="margin-top:12px;">Estimated Bill: <span class="mt-highlight">Rs ' + fmt(total, 2) + '</span></div>' +
                '<p style="margin-top:10px;font-size:12px;color:#94a3b8;">* Estimate only. Actual bill includes QTA, taxes &amp; adjustments from your utility.</p>'
            );
        },

        /* ---------- LOVE CALCULATOR ---------- */
        love: function (tool) {
            var name1 = val('mt-love-name1');
            var name2 = val('mt-love-name2');

            if (!name1 || !name2) { showError(tool, 'Dono naam likho pehle!'); return; }

            var combined = (name1.toLowerCase() + 'loves' + name2.toLowerCase());
            var h = 0;
            for (var i = 0; i < combined.length; i++) {
                h = ((h * 31) + combined.charCodeAt(i)) >>> 0;
            }
            var score = (h % 91) + 10; /* 10-100 */

            var msg, emoji;
            if (score >= 90) { msg = 'Perfect match! Made in heaven! &#128149;'; emoji = '&#128140;'; }
            else if (score >= 75) { msg = 'Strong connection! Ye rishta pakka hai! &#128152;'; emoji = '&#128140;'; }
            else if (score >= 50) { msg = 'Good potential! Thori mehnat karo! &#128525;'; emoji = '&#128147;'; }
            else if (score >= 30) { msg = 'Hmm... dosti hi theek hai! &#128528;'; emoji = '&#128148;'; }
            else { msg = 'Sirf dost reh lo, bhai! &#128517;'; emoji = '&#128148;'; }

            showResult(tool,
                '<div class="mt-love-score">' + emoji + '<br>' + name1 + ' &#10084; ' + name2 + '<br><span style="font-size:42px;color:' + (score >= 50 ? '#e11d48' : '#64748b') + '">' + score + '%</span></div>' +
                '<p style="text-align:center;font-size:15px;color:#475569;margin-top:8px;">' + msg + '</p>' +
                '<p style="text-align:center;font-size:12px;color:#94a3b8;margin-top:5px;">* Just for fun!</p>'
            );
        },

        /* ---------- BIRTHDAY COUNTDOWN ---------- */
        birthday: function (tool) {
            var dobStr = val('mt-bd-date');
            if (!dobStr) { showError(tool, 'Please select your date of birth.'); return; }

            var dob = new Date(dobStr + 'T00:00:00');
            var today = new Date();
            today.setHours(0, 0, 0, 0);

            if (dob > today) { showError(tool, 'Date of birth cannot be in the future!'); return; }

            /* Age Y/M/D */
            var yy = today.getFullYear() - dob.getFullYear();
            var mm = today.getMonth() - dob.getMonth();
            var dd = today.getDate() - dob.getDate();
            if (dd < 0) { mm--; dd += new Date(today.getFullYear(), today.getMonth(), 0).getDate(); }
            if (mm < 0) { yy--; mm += 12; }

            /* Next birthday */
            var thisYear = today.getFullYear();
            var nextBday = new Date(thisYear, dob.getMonth(), dob.getDate());
            if (nextBday.getTime() < today.getTime()) {
                nextBday = new Date(thisYear + 1, dob.getMonth(), dob.getDate());
            }
            var daysLeft = Math.ceil((nextBday - today) / 86400000);
            var turningAge = nextBday.getFullYear() - dob.getFullYear();

            var totalDays = Math.floor((today - dob) / 86400000);
            var bornDay = dob.toLocaleDateString('en-US', { weekday: 'long' });
            var zodiac = getZodiac(dob.getMonth() + 1, dob.getDate());

            var bdayMsg = daysLeft === 0
                ? '<div class="mt-res-big">&#127881; HAPPY BIRTHDAY! Aaj aapka din hai! &#127881;</div>'
                : '<div class="mt-res-big">Next Birthday: <span class="mt-highlight">' + daysLeft + ' days</span> to go!</div>';

            showResult(tool,
                bdayMsg +
                '<div class="mt-res-row"><span>You Will Turn</span><strong>' + turningAge + ' years old</strong></div>' +
                '<div class="mt-res-row"><span>Birthday On</span><strong>' + nextBday.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + '</strong></div>' +
                '<div class="mt-res-row"><span>Current Age</span><strong>' + yy + ' years, ' + mm + ' months, ' + dd + ' days</strong></div>' +
                '<div class="mt-res-row"><span>Total Days Lived</span><strong>' + fmt(totalDays, 0) + ' days</strong></div>' +
                '<div class="mt-res-row"><span>Born On</span><strong>' + bornDay + '</strong></div>' +
                '<div class="mt-res-row"><span>Zodiac Sign</span><strong>' + zodiac + '</strong></div>'
            );
        },

        /* ---------- JSON FORMATTER ---------- */
        'json-beautify': function (tool) { jsonProcess(tool, 'beautify'); },
        'json-minify': function (tool) { jsonProcess(tool, 'minify'); },
        'json-validate': function (tool) { jsonProcess(tool, 'validate'); },

        /* ---------- BASE64 ---------- */
        'base64-encode': function (tool) {
            var input = $('mt-b64-input').value;
            if (!input) { showError(tool, 'Please enter some text to encode.'); return; }
            try {
                $('mt-b64-output').value = b64Encode(input);
                hideError(tool);
            } catch (e) {
                showError(tool, 'Encoding failed: ' + e.message);
            }
        },
        'base64-decode': function (tool) {
            var input = $('mt-b64-input').value;
            if (!input) { showError(tool, 'Please paste base64 string to decode.'); return; }
            try {
                $('mt-b64-output').value = b64Decode(input);
                hideError(tool);
            } catch (e) {
                showError(tool, 'Invalid Base64 string!');
            }
        },

        /* ---------- URL ENCODER ---------- */
        'url-encode': function (tool) {
            var input = $('mt-url-input').value;
            if (!input) { showError(tool, 'Please enter text or URL.'); return; }
            $('mt-url-output').value = encodeURIComponent(input);
            hideError(tool);
        },
        'url-decode': function (tool) {
            var input = $('mt-url-input').value;
            if (!input) { showError(tool, 'Please paste encoded URL.'); return; }
            try {
                $('mt-url-output').value = decodeURIComponent(input.replace(/\+/g, ' '));
                hideError(tool);
            } catch (e) {
                showError(tool, 'Invalid encoded URL!');
            }
        },
        'url-encode-full': function (tool) {
            var input = $('mt-url-input').value;
            if (!input) { showError(tool, 'Please enter URL.'); return; }
            $('mt-url-output').value = encodeURI(input);
            hideError(tool);
        },

        /* ---------- HASH GENERATOR ---------- */
        hash: async function (tool) {
            var input = $('mt-hash-input').value;
            if (!input) { showError(tool, 'Please enter text to hash.'); return; }

            var checks = [
                { id: 'mt-hash-md5', label: 'MD5' },
                { id: 'mt-hash-sha1', label: 'SHA-1' },
                { id: 'mt-hash-sha256', label: 'SHA-256' },
                { id: 'mt-hash-sha384', label: 'SHA-384' },
                { id: 'mt-hash-sha512', label: 'SHA-512' }
            ];

            var selected = checks.filter(function (c) { return $(c.id).checked; });
            if (!selected.length) { showError(tool, 'Select at least one hash algorithm.'); return; }

            var resultsBox = $('mt-hash-results');
            resultsBox.style.display = 'block';
            resultsBox.innerHTML = '<p style="text-align:center;color:#64748b;padding:20px;">&#8987; Computing hashes...</p>';
            hideError(tool);

            var html = '';
            for (var i = 0; i < selected.length; i++) {
                var algo = selected[i];
                var hashVal = '';
                try {
                    if (algo.label === 'MD5') {
                        hashVal = computeMd5(input);
                    } else {
                        if (!window.crypto || !window.crypto.subtle) {
                            throw new Error('Web Crypto not available (HTTPS required)');
                        }
                        var jsAlgo = algo.label;
                        hashVal = await computeSha(jsAlgo.toUpperCase(), input);
                    }
                    html += '<div class="mt-hash-item"><label>' + algo.label + '</label>' +
                        '<div class="mt-inline-copy"><input type="text" readonly value="' + hashVal + '" />' +
                        '<button type="button" class="mt-copy-btn" data-copy-value="' + hashVal + '">&#128203;</button></div></div>';
                } catch (err) {
                    html += '<div class="mt-hash-item"><label>' + algo.label + '</label><input type="text" readonly value="Error: ' + err.message + '" /></div>';
                }
            }

            resultsBox.innerHTML = '<div class="mt-result" style="display:block;">' + html + '</div>';
        },

        /* ---------- UUID ---------- */
        uuid: function (tool) {
            var count = parseInt(val('mt-uuid-count'), 10) || 5;
            if (count < 1 || count > 100) { showError(tool, 'Count must be between 1 and 100.'); return; }

            var upper = $('mt-uuid-uppercase').checked;
            var braces = $('mt-uuid-braces').checked;

            var uuids = [];
            for (var i = 0; i < count; i++) {
                var u = uuidV4();
                if (upper) { u = u.toUpperCase(); }
                if (braces) { u = '{' + u + '}'; }
                uuids.push(u);
            }

            $('mt-uuid-output').value = uuids.join('\n');
            hideError(tool);
        }
    };

    function jsonProcess(tool, mode) {
        var input = $('mt-json-input').value;
        if (!input.trim()) { showError(tool, 'Please paste JSON first.'); return; }

        var statusBox = $('mt-json-status');

        try {
            var parsed = JSON.parse(input);
            hideError(tool);

            var indentSetting = val('mt-json-indent');
            var indent = indentSetting === 'tab' ? '\t' : parseInt(indentSetting, 10);

            if (mode === 'beautify') {
                $('mt-json-output').value = JSON.stringify(parsed, null, indent);
                statusBox.innerHTML = '<div class="mt-res-row"><span>Status</span><strong class="mt-green">&#9989; Valid JSON — Formatted!</strong></div>';
            } else if (mode === 'minify') {
                $('mt-json-output').value = JSON.stringify(parsed);
                statusBox.innerHTML = '<div class="mt-res-row"><span>Status</span><strong class="mt-green">&#9989; Valid JSON — Minified!</strong></div>';
            } else {
                $('mt-json-output').value = '';
                statusBox.innerHTML = '<div class="mt-res-row"><span>Status</span><strong class="mt-green">&#9989; Valid JSON!</strong></div>';
            }
            statusBox.style.display = 'block';
        } catch (e) {
            $('mt-json-output').value = '';
            statusBox.innerHTML = '<div class="mt-res-row"><span>Status</span><strong style="color:#ef4444;">&#10060; Invalid JSON</strong></div>' +
                '<div class="mt-res-row"><span>Error</span><strong style="font-size:13px;">' + e.message + '</strong></div>';
            statusBox.style.display = 'block';
        }
    }

    function hideError(tool) {
        var e = tool.querySelector('.mt-error');
        if (e) { e.style.display = 'none'; }
    }

    /* ================= FANCY / STYLISH TEXT GENERATORS ================= */

    /* Builds an offset-mapping function for a contiguous Unicode block
       that has NO gaps (safe to do with simple arithmetic). */
    function mapOffset(upperOffset, lowerOffset, digitOffset) {
        return function (str) {
            var out = '';
            for (var i = 0; i < str.length; i++) {
                var c = str.charCodeAt(i);
                if (c >= 65 && c <= 90 && upperOffset !== null) { out += String.fromCodePoint(c + upperOffset); }
                else if (c >= 97 && c <= 122 && lowerOffset !== null) { out += String.fromCodePoint(c + lowerOffset); }
                else if (c >= 48 && c <= 57 && digitOffset !== null) { out += String.fromCodePoint(c + digitOffset); }
                else { out += str[i]; }
            }
            return out;
        };
    }

    var fancyBold        = mapOffset(120211, 120205, 120764); // A-Z 1D5D4, a-z 1D5EE, 0-9 1D7EC
    var fancyItalic       = mapOffset(120263, 120257, null); // A-Z 1D608, a-z 1D622
    var fancyBoldItalic   = mapOffset(120315, 120309, null); // A-Z 1D63C, a-z 1D656
    var fancyBoldScript   = mapOffset(119951, 119945, null); // A-Z 1D4D0, a-z 1D4EA
    var fancyBoldFraktur  = mapOffset(120107, 120101, null); // A-Z 1D56C, a-z 1D586
    var fancyMonospace    = mapOffset(120367, 120361, 120774); // A-Z 1D670, a-z 1D68A, 0-9 1D7F6
    var fancyFullwidth    = function (str) {
        var out = '';
        for (var i = 0; i < str.length; i++) {
            var c = str.charCodeAt(i);
            if (c === 32) { out += '\u3000'; }
            else if (c >= 33 && c <= 126) { out += String.fromCodePoint(c + 65248); }
            else { out += str[i]; }
        }
        return out;
    };
    var fancyCircled = function (str) {
        var out = '';
        for (var i = 0; i < str.length; i++) {
            var c = str.charCodeAt(i);
            if (c >= 65 && c <= 90) { out += String.fromCodePoint(c + 9333); }
            else if (c >= 97 && c <= 122) { out += String.fromCodePoint(c + 9327); }
            else if (c === 48) { out += '\u24EA'; }
            else if (c >= 49 && c <= 57) { out += String.fromCodePoint(c + 9263); }
            else { out += str[i]; }
        }
        return out;
    };
    var DOUBLE_UP = { A: '𝔸', B: '𝔹', C: 'ℂ', D: '𝔻', E: '𝔼', F: '𝔽', G: '𝔾', H: 'ℍ', I: '𝕀', J: '𝕁', K: '𝕂', L: '𝕃', M: '𝕄', N: 'ℕ', O: '𝕆', P: 'ℙ', Q: 'ℚ', R: 'ℝ', S: '𝕊', T: '𝕋', U: '𝕌', V: '𝕍', W: '𝕎', X: '𝕏', Y: '𝕐', Z: 'ℤ' };
    var fancyDoubleStruck = function (str) {
        var out = '';
        for (var i = 0; i < str.length; i++) {
            var ch = str[i], c = str.charCodeAt(i);
            if (DOUBLE_UP[ch]) { out += DOUBLE_UP[ch]; }
            else if (c >= 97 && c <= 122) { out += String.fromCodePoint(c + 120049); }
            else if (c >= 48 && c <= 57) { out += String.fromCodePoint(c + 120744); }
            else { out += ch; }
        }
        return out;
    };
    var SMALLCAPS = { a: 'ᴀ', b: 'ʙ', c: 'ᴄ', d: 'ᴅ', e: 'ᴇ', f: 'ꜰ', g: 'ɢ', h: 'ʜ', i: 'ɪ', j: 'ᴊ', k: 'ᴋ', l: 'ʟ', m: 'ᴍ', n: 'ɴ', o: 'ᴏ', p: 'ᴘ', q: 'ǫ', r: 'ʀ', s: 's', t: 'ᴛ', u: 'ᴜ', v: 'ᴠ', w: 'ᴡ', x: 'x', y: 'ʏ', z: 'ᴢ' };
    var fancySmallCaps = function (str) {
        var out = '';
        for (var i = 0; i < str.length; i++) {
            var lower = str[i].toLowerCase();
            out += SMALLCAPS[lower] || str[i];
        }
        return out;
    };
    var UPSIDE = { a: 'ɐ', b: 'q', c: 'ɔ', d: 'p', e: 'ǝ', f: 'ɟ', g: 'ƃ', h: 'ɥ', i: 'ᴉ', j: 'ɾ', k: 'ʞ', l: 'l', m: 'ɯ', n: 'u', o: 'o', p: 'd', q: 'b', r: 'ɹ', s: 's', t: 'ʇ', u: 'n', v: 'ʌ', w: 'ʍ', x: 'x', y: 'ʎ', z: 'z', '1': 'Ɩ', '2': 'ᄅ', '3': 'Ɛ', '4': 'ㄣ', '6': '9', '7': 'ㄥ', '9': '6', '.': '˙', ',': "'", "'": ',', '?': '¿', '!': '¡', '(': ')', ')': '(', '[': ']', ']': '[', '{': '}', '}': '{' };
    var fancyUpsideDown = function (str) {
        var out = '';
        for (var i = 0; i < str.length; i++) {
            var lower = str[i].toLowerCase();
            out += (UPSIDE[lower] !== undefined) ? UPSIDE[lower] : str[i];
        }
        return out.split('').reverse().join('');
    };
    var fancyStrikethrough = function (str) {
        var out = '';
        for (var i = 0; i < str.length; i++) { out += str[i] + '\u0336'; }
        return out;
    };

    var FANCY_STYLES = [
        { label: 'Bold', fn: fancyBold },
        { label: 'Italic', fn: fancyItalic },
        { label: 'Bold Italic', fn: fancyBoldItalic },
        { label: 'Bold Script', fn: fancyBoldScript },
        { label: 'Double-Struck', fn: fancyDoubleStruck },
        { label: 'Bold Fraktur (Gothic)', fn: fancyBoldFraktur },
        { label: 'Monospace', fn: fancyMonospace },
        { label: 'Circled', fn: fancyCircled },
        { label: 'Fullwidth', fn: fancyFullwidth },
        { label: 'Small Caps', fn: fancySmallCaps },
        { label: 'Upside Down', fn: fancyUpsideDown },
        { label: 'Strikethrough', fn: fancyStrikethrough }
    ];

    var STYLISH_TEMPLATES = [
        { label: 'Brackets', fn: function (t) { return '『' + t + '』'; } },
        { label: 'Lenticular', fn: function (t) { return '【' + t + '】'; } },
        { label: 'Star Wings', fn: function (t) { return '★彡 ' + t + ' 彡★'; } },
        { label: 'Flowers', fn: function (t) { return '✿ ' + t + ' ✿'; } },
        { label: 'Dotted', fn: function (t) { return '•[ ' + t + ' ]•'; } },
        { label: 'Heart Arrow', fn: function (t) { return '➳ ' + t + ' ღ'; } },
        { label: 'Blocks', fn: function (t) { return '▁▂▃ ' + t + ' ▃▂▁'; } },
        { label: 'Waves', fn: function (t) { return '↭ ' + t + ' ↭'; } },
        { label: 'Soft Dots', fn: function (t) { return '°•. ' + t + ' .•°'; } },
        { label: 'Snowflake', fn: function (t) { return '❃ ' + t + ' ❃'; } },
        { label: 'Bold Script + Flowers', fn: function (t) { return '🌸 ' + fancyBoldScript(t) + ' 🌸'; } },
        { label: 'Fraktur + Brackets', fn: function (t) { return '『' + fancyBoldFraktur(t) + '』'; } }
    ];

    function renderFancyList(containerId, input, list) {
        var container = $(containerId);
        if (!container) { return; }
        if (!input) { container.innerHTML = '<div class="mt-fancy-empty">Type something above to see styled versions...</div>'; return; }

        var html = '';
        list.forEach(function (style) {
            var out = style.fn(input);
            var escaped = out.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            html += '<div class="mt-fancy-row">'
                + '<div class="mt-fancy-row-text"><span class="mt-fancy-row-label">' + style.label + '</span>' + escaped + '</div>'
                + '<button type="button" class="mt-copy-btn" data-copy-value="' + out.replace(/"/g, '&quot;') + '">&#128203; Copy</button>'
                + '</div>';
        });
        container.innerHTML = html;
    }

    /* ================= WIRING ================= */
    ready(function () {

        /* Generic action buttons */
        document.querySelectorAll('.mt-btn[data-action]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = btn.getAttribute('data-action');
                var toolBox = btn.closest('.mt-tool');
                if (tools[action] && toolBox) {
                    var r = tools[action](toolBox);
                    if (r && typeof r.catch === 'function') {
                        r.catch(function (err) { showError(toolBox, err.message || 'Error'); });
                    }
                }
            });
        });

        /* Case converter buttons */
        document.querySelectorAll('[data-case]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = 'case-' + btn.getAttribute('data-case');
                var toolBox = btn.closest('.mt-tool');
                if (tools[action]) { tools[action](toolBox); }
            });
        });

        /* Reverse buttons */
        document.querySelectorAll('[data-rev]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var mode = btn.getAttribute('data-rev');
                var input = $('mt-rev-input').value;
                if (!input) { return; }
                var output = '';
                if (mode === 'letters') {
                    output = Array.from(input).reverse().join('');
                } else if (mode === 'eachword') {
                    output = input.split(/(\s+)/).map(function (word) {
                        return Array.from(word).reverse().join('');
                    }).join('');
                } else {
                    output = input.split(/\s+/).reverse().join(' ');
                }
                $('mt-rev-output').value = output;
            });
        });

        /* Line breaks custom option toggle */
        var lbReplace = $('mt-lb-replace');
        if (lbReplace) {
            lbReplace.addEventListener('change', function () {
                $('mt-lb-custom-wrap').style.display = lbReplace.value === 'custom' ? 'block' : 'none';
            });
        }

        /* Base64 buttons */
        document.querySelectorAll('[data-b64]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = 'base64-' + btn.getAttribute('data-b64');
                var toolBox = btn.closest('.mt-tool');
                if (tools[action]) { tools[action](toolBox); }
            });
        });

        /* URL buttons */
        document.querySelectorAll('[data-url]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = 'url-' + btn.getAttribute('data-url');
                var toolBox = btn.closest('.mt-tool');
                if (tools[action]) { tools[action](toolBox); }
            });
        });

        /* JSON buttons */
        document.querySelectorAll('[data-json]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = 'json-' + btn.getAttribute('data-json');
                var toolBox = btn.closest('.mt-tool');
                if (tools[action]) { tools[action](toolBox); }
            });
        });

        /* Copy buttons (delegated) */
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.mt-copy-btn');
            if (!btn) { return; }

            var targetId = btn.getAttribute('data-copy-target');
            var fixedValue = btn.getAttribute('data-copy-value');
            var text = fixedValue || (targetId && $(targetId) ? $(targetId).value : '');

            if (!text) { return; }
            copyText(text, btn);
        });

        /* Color picker live update */
        var wheel = $('mt-color-wheel');
        if (wheel) {
            var updateColor = function (hexVal) {
                var rgb = hexToRgb(hexVal);
                var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
                $('mt-color-hex').value = hexVal.toUpperCase();
                $('mt-color-rgb').value = 'rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')';
                $('mt-color-hsl').value = 'hsl(' + hsl.h + ', ' + hsl.s + '%, ' + hsl.l + '%)';
                var preview = $('mt-color-preview');
                if (preview) { preview.style.background = hexVal; }
            };
            wheel.addEventListener('input', function () { updateColor(wheel.value); });
            updateColor(wheel.value);

            /* Manual hex entry */
            $('mt-color-hex').addEventListener('change', function () {
                var v = $('mt-color-hex').value.trim();
                if (/^#?[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/.test(v)) {
                    if (v.charAt(0) !== '#') { v = '#' + v; }
                    wheel.value = v;
                    updateColor(v);
                }
            });
        }

        /* Fancy Text Generator — live update as you type */
        var fancyInput = $('mt-fancy-input');
        if (fancyInput) {
            fancyInput.addEventListener('input', function () {
                renderFancyList('mt-fancy-output', fancyInput.value, FANCY_STYLES);
            });
            renderFancyList('mt-fancy-output', '', FANCY_STYLES);
        }

        /* Stylish Text Generator — live update as you type */
        var stylishInput = $('mt-stylish-input');
        if (stylishInput) {
            stylishInput.addEventListener('input', function () {
                renderFancyList('mt-stylish-output', stylishInput.value, STYLISH_TEMPLATES);
            });
            renderFancyList('mt-stylish-output', '', STYLISH_TEMPLATES);
        }

        /* QR Code Generator */
        var qrCanvas = $('mt-qr-canvas');
        if (qrCanvas && window.MTQRCode) {
            var qrTool = qrCanvas.closest('.mt-tool');
            var qrTypeSel = $('mt-qr-type');
            var qrErrorBox = $('mt-qr-error');
            var qrSizeVal = $('mt-qr-size-val');
            var lastQrResult = null;

            function qrEsc(s) { return String(s).replace(/([\\;,:"])/g, '\\$1'); }

            function qrBuildContent() {
                var type = qrTypeSel.value;
                if (type === 'url') {
                    var v = $('mt-qr-url').value.trim();
                    if (!v) { return ''; }
                    return /^https?:\/\//i.test(v) ? v : 'https://' + v;
                }
                if (type === 'text') { return $('mt-qr-text').value; }
                if (type === 'wifi') {
                    var ssid = $('mt-qr-wifi-ssid').value.trim();
                    if (!ssid) { return ''; }
                    var pass = $('mt-qr-wifi-pass').value.trim();
                    var sec = $('mt-qr-wifi-sec').value;
                    return 'WIFI:T:' + sec + ';S:' + qrEsc(ssid) + ';' + (sec !== 'nopass' ? 'P:' + qrEsc(pass) + ';' : '') + ';';
                }
                if (type === 'email') {
                    var to = $('mt-qr-email-to').value.trim();
                    if (!to) { return ''; }
                    var subj = encodeURIComponent($('mt-qr-email-subject').value);
                    var body = encodeURIComponent($('mt-qr-email-body').value);
                    return 'mailto:' + to + '?subject=' + subj + '&body=' + body;
                }
                if (type === 'phone') {
                    var p = $('mt-qr-phone').value.trim();
                    return p ? 'tel:' + p : '';
                }
                if (type === 'sms') {
                    var sp = $('mt-qr-sms-phone').value.trim();
                    if (!sp) { return ''; }
                    return 'SMSTO:' + sp + ':' + $('mt-qr-sms-body').value;
                }
                if (type === 'vcard') {
                    var name = $('mt-qr-vc-name').value.trim();
                    if (!name) { return ''; }
                    var lines = ['BEGIN:VCARD', 'VERSION:3.0', 'FN:' + name];
                    var phone = $('mt-qr-vc-phone').value.trim(); if (phone) { lines.push('TEL:' + phone); }
                    var email = $('mt-qr-vc-email').value.trim(); if (email) { lines.push('EMAIL:' + email); }
                    var org = $('mt-qr-vc-org').value.trim(); if (org) { lines.push('ORG:' + org); }
                    lines.push('END:VCARD');
                    return lines.join('\n');
                }
                return '';
            }

            function qrDraw(result) {
                var size = parseInt($('mt-qr-size').value, 10) || 300;
                var fg = $('mt-qr-fg').value;
                var bg = $('mt-qr-bg').value;
                var modules = result.size;
                var quiet = 4;
                var totalModules = modules + quiet * 2;
                var scale = Math.max(1, Math.floor(size / totalModules));
                var canvasSize = totalModules * scale;
                qrCanvas.width = canvasSize;
                qrCanvas.height = canvasSize;
                var ctx = qrCanvas.getContext('2d');
                ctx.fillStyle = bg;
                ctx.fillRect(0, 0, canvasSize, canvasSize);
                ctx.fillStyle = fg;
                for (var r = 0; r < modules; r++) {
                    for (var c = 0; c < modules; c++) {
                        if (result.matrix[r][c]) { ctx.fillRect((c + quiet) * scale, (r + quiet) * scale, scale, scale); }
                    }
                }
            }

            function qrRender() {
                if (qrSizeVal) { qrSizeVal.textContent = $('mt-qr-size').value + 'px'; }
                var content = qrBuildContent();
                qrErrorBox.style.display = 'none';
                if (!content) {
                    qrCanvas.width = 0; qrCanvas.height = 0;
                    lastQrResult = null;
                    return;
                }
                var level = $('mt-qr-ec').value;
                var result = window.MTQRCode.generate(content, level);
                if (!result) {
                    qrErrorBox.textContent = 'Text bohat lamba hai — thora chota karein ya kam error-correction level try karein.';
                    qrErrorBox.style.display = 'block';
                    lastQrResult = null;
                    qrCanvas.width = 0; qrCanvas.height = 0;
                    return;
                }
                lastQrResult = result;
                qrDraw(result);
            }

            function qrShowFieldsFor(type) {
                qrTool.querySelectorAll('.mt-qr-fields').forEach(function (g) { g.style.display = 'none'; });
                var grp = qrTool.querySelector('.mt-qr-fields[data-qr-fields="' + type + '"]');
                if (grp) { grp.style.display = ''; }
            }

            qrShowFieldsFor(qrTypeSel.value);
            qrTypeSel.addEventListener('change', function () { qrShowFieldsFor(qrTypeSel.value); qrRender(); });
            qrTool.querySelectorAll('input, select, textarea').forEach(function (el) {
                if (el === qrTypeSel) { return; }
                el.addEventListener('input', qrRender);
                el.addEventListener('change', qrRender);
            });

            var qrPngBtn = $('mt-qr-download-png');
            if (qrPngBtn) {
                qrPngBtn.addEventListener('click', function () {
                    if (!lastQrResult) { return; }
                    var link = document.createElement('a');
                    link.download = 'qrcode.png';
                    link.href = qrCanvas.toDataURL('image/png');
                    link.click();
                });
            }
            var qrSvgBtn = $('mt-qr-download-svg');
            if (qrSvgBtn) {
                qrSvgBtn.addEventListener('click', function () {
                    if (!lastQrResult) { return; }
                    var fg = $('mt-qr-fg').value;
                    var bg = $('mt-qr-bg').value;
                    var modules = lastQrResult.size;
                    var quiet = 4;
                    var total = modules + quiet * 2;
                    var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' + total + ' ' + total + '">';
                    svg += '<rect width="' + total + '" height="' + total + '" fill="' + bg + '"/>';
                    for (var r = 0; r < modules; r++) {
                        for (var c = 0; c < modules; c++) {
                            if (lastQrResult.matrix[r][c]) { svg += '<rect x="' + (c + quiet) + '" y="' + (r + quiet) + '" width="1" height="1" fill="' + fg + '"/>'; }
                        }
                    }
                    svg += '</svg>';
                    var blob = new Blob([svg], { type: 'image/svg+xml' });
                    var link = document.createElement('a');
                    link.download = 'qrcode.svg';
                    link.href = URL.createObjectURL(blob);
                    link.click();
                });
            }

            qrRender();
        }

        /* Enter key support */
        document.querySelectorAll('.mt-tool input[type=number], .mt-tool input[type=text]:not(.mt-inline-copy input)').forEach(function (input) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var btn = input.closest('.mt-tool').querySelector('.mt-btn[data-action]');
                    if (btn) { btn.click(); }
                }
            });
        });
    });

    function copyText(text, btn) {
        var done = function () {
            var old = btn.innerHTML;
            btn.innerHTML = '&#9989;';
            setTimeout(function () { btn.innerHTML = old; }, 1200);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(function () { fbCopy(text); done(); });
        } else {
            fbCopy(text);
            done();
        }
    }

    function fbCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    }
})();
