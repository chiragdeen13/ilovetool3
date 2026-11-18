(function (global) {
	'use strict';

	/* ---------- Galois Field GF(256), primitive polynomial 0x11D ---------- */
	var EXP = new Array(512);
	var LOG = new Array(256);
	(function () {
		var x = 1;
		for (var i = 0; i < 255; i++) {
			EXP[i] = x;
			LOG[x] = i;
			x <<= 1;
			if (x & 0x100) { x ^= 0x11D; }
		}
		for (var j = 255; j < 512; j++) { EXP[j] = EXP[j - 255]; }
	})();
	function gexp(n) { while (n < 0) n += 255; while (n >= 255) n -= 255; return EXP[n]; }
	function glog(n) { return LOG[n]; }

	/* ---------- Polynomial over GF(256) ---------- */
	function Poly(nums) {
		var offset = 0;
		while (offset < nums.length - 1 && nums[offset] === 0) { offset++; }
		this.num = nums.slice(offset);
	}
	Poly.prototype.get = function (i) { return this.num[i]; };
	Poly.prototype.len = function () { return this.num.length; };
	Poly.prototype.multiply = function (e) {
		var num = new Array(this.len() + e.len() - 1).fill(0);
		for (var i = 0; i < this.len(); i++) {
			if (this.get(i) === 0) { continue; }
			for (var j = 0; j < e.len(); j++) {
				if (e.get(j) === 0) { continue; }
				num[i + j] ^= gexp(glog(this.get(i)) + glog(e.get(j)));
			}
		}
		return new Poly(num);
	};
	Poly.prototype.mod = function (e) {
		if (this.len() - e.len() < 0) { return this; }
		var ratio = glog(this.get(0)) - glog(e.get(0));
		var num = this.num.slice();
		for (var i = 0; i < e.len(); i++) {
			if (e.get(i) !== 0) { num[i] ^= gexp(glog(e.get(i)) + ratio); }
		}
		return new Poly(num).mod(e);
	};
	function rsGeneratorPoly(ecLen) {
		var a = new Poly([1]);
		for (var i = 0; i < ecLen; i++) { a = a.multiply(new Poly([1, gexp(i)])); }
		return a;
	}

	/* ---------- Per-version tables (versions 1-10 only; kept deliberately
	   small so every entry below could be hand-verified against the known
	   "total codewords per version" sequence — 26,44,70,100,134,172,196,
	   242,292,346 — before shipping. Format: [ecCodewordsPerBlock,
	   [ [numBlocks, dataCodewordsPerBlock], ... up to 2 groups ] ] ---------- */
	var RS_BLOCKS = {
		1: { L: [7, [[1, 19]]], M: [10, [[1, 16]]], Q: [13, [[1, 13]]], H: [17, [[1, 9]]] },
		2: { L: [10, [[1, 34]]], M: [16, [[1, 28]]], Q: [22, [[1, 22]]], H: [28, [[1, 16]]] },
		3: { L: [15, [[1, 55]]], M: [26, [[1, 44]]], Q: [18, [[2, 17]]], H: [22, [[2, 13]]] },
		4: { L: [20, [[1, 80]]], M: [18, [[2, 32]]], Q: [26, [[2, 24]]], H: [16, [[4, 9]]] },
		5: { L: [26, [[1, 108]]], M: [24, [[2, 43]]], Q: [18, [[2, 15], [2, 16]]], H: [22, [[2, 11], [2, 12]]] },
		6: { L: [18, [[2, 68]]], M: [16, [[4, 27]]], Q: [24, [[4, 19]]], H: [28, [[4, 15]]] },
		7: { L: [20, [[2, 78]]], M: [18, [[4, 31]]], Q: [18, [[2, 14], [4, 15]]], H: [26, [[4, 13], [1, 14]]] },
		8: { L: [24, [[2, 97]]], M: [22, [[2, 38], [2, 39]]], Q: [22, [[4, 18], [2, 19]]], H: [26, [[4, 14], [2, 15]]] },
		9: { L: [30, [[2, 116]]], M: [22, [[3, 36], [2, 37]]], Q: [20, [[4, 16], [4, 17]]], H: [24, [[4, 12], [4, 13]]] },
		10: { L: [18, [[2, 68], [2, 69]]], M: [26, [[4, 43], [1, 44]]], Q: [24, [[6, 19], [2, 20]]], H: [28, [[6, 15], [2, 16]]] },
	};
	var ALIGN_COORDS = { 1: [], 2: [6, 18], 3: [6, 22], 4: [6, 26], 5: [6, 30], 6: [6, 34], 7: [6, 22, 38], 8: [6, 24, 42], 9: [6, 26, 46], 10: [6, 28, 50] };
	var EC_BITS = { L: 1, M: 0, Q: 3, H: 2 }; // format-info EC level indicator bits

	function dataCapacityBytes(version, level) {
		var groups = RS_BLOCKS[version][level][1];
		var total = 0;
		groups.forEach(function (g) { total += g[0] * g[1]; });
		return total;
	}
	function pickVersion(byteLen, level) {
		for (var v = 1; v <= 10; v++) {
			var cap = dataCapacityBytes(v, level);
			var headerBits = 4 + (v <= 9 ? 8 : 16);
			var neededBits = headerBits + byteLen * 8;
			if (neededBits <= cap * 8) { return v; }
		}
		return 0; // too long
	}

	/* ---------- Bit buffer ---------- */
	function BitBuffer() { this.bits = []; }
	BitBuffer.prototype.put = function (num, len) {
		for (var i = len - 1; i >= 0; i--) { this.bits.push((num >>> i) & 1); }
	};

	function textToUtf8Bytes(str) {
		return Array.from(new TextEncoder().encode(str));
	}

	function buildDataCodewords(bytesArr, version, level) {
		var buf = new BitBuffer();
		buf.put(0x4, 4); // byte mode indicator
		buf.put(bytesArr.length, version <= 9 ? 8 : 16);
		bytesArr.forEach(function (b) { buf.put(b, 8); });
		var capacityBits = dataCapacityBytes(version, level) * 8;
		var term = Math.min(4, capacityBits - buf.bits.length);
		if (term > 0) { buf.put(0, term); }
		while (buf.bits.length % 8 !== 0) { buf.bits.push(0); }
		var pad = [0xEC, 0x11];
		var pi = 0;
		while (buf.bits.length < capacityBits) { buf.put(pad[pi % 2], 8); pi++; }
		var codewords = [];
		for (var i = 0; i < buf.bits.length; i += 8) {
			var byte = 0;
			for (var j = 0; j < 8; j++) { byte = (byte << 1) | buf.bits[i + j]; }
			codewords.push(byte);
		}
		return codewords;
	}

	function interleave(dataCodewords, version, level) {
		var groups = RS_BLOCKS[version][level][1];
		var ecLen = RS_BLOCKS[version][level][0];
		var genPoly = rsGeneratorPoly(ecLen);
		var blocks = [];
		var offset = 0;
		groups.forEach(function (g) {
			for (var b = 0; b < g[0]; b++) {
				var dataBlock = dataCodewords.slice(offset, offset + g[1]);
				offset += g[1];
				var rawPoly = new Poly(dataBlock.concat(new Array(ecLen).fill(0)));
				var ecPoly = rawPoly.mod(genPoly);
				var ecBytes = new Array(ecLen).fill(0);
				for (var i = 0; i < ecPoly.len(); i++) { ecBytes[ecLen - ecPoly.len() + i] = ecPoly.get(i); }
				blocks.push({ data: dataBlock, ec: ecBytes });
			}
		});
		var maxData = Math.max.apply(null, blocks.map(function (b) { return b.data.length; }));
		var result = [];
		for (var i = 0; i < maxData; i++) {
			blocks.forEach(function (b) { if (i < b.data.length) { result.push(b.data[i]); } });
		}
		for (var i = 0; i < ecLen; i++) {
			blocks.forEach(function (b) { result.push(b.ec[i]); });
		}
		return result;
	}

	/* ---------- Matrix construction ---------- */
	function makeMatrix(size) {
		var m = [];
		for (var i = 0; i < size; i++) { m.push(new Array(size).fill(null)); }
		return m;
	}
	function placeFinder(m, row, col) {
		for (var r = -1; r <= 7; r++) {
			for (var c = -1; c <= 7; c++) {
				var rr = row + r, cc = col + c;
				if (rr < 0 || rr >= m.length || cc < 0 || cc >= m.length) { continue; }
				var dark = (r >= 0 && r <= 6 && (c === 0 || c === 6)) ||
					(c >= 0 && c <= 6 && (r === 0 || r === 6)) ||
					(r >= 2 && r <= 4 && c >= 2 && c <= 4);
				m[rr][cc] = dark ? 1 : 0;
			}
		}
	}
	function placeAlignment(m, row, col) {
		for (var r = -2; r <= 2; r++) {
			for (var c = -2; c <= 2; c++) {
				var dark = (Math.abs(r) === 2 || Math.abs(c) === 2 || (r === 0 && c === 0));
				m[row + r][col + c] = dark ? 1 : 0;
			}
		}
	}
	function formatInfoBits(level, maskIdx) {
		var data = (EC_BITS[level] << 3) | maskIdx;
		var g = data << 10;
		var poly = 0x537; // generator for BCH(15,5)
		for (var i = 4; i >= 0; i--) {
			if (g & (1 << (i + 10))) { g ^= poly << i; }
		}
		var bits = ((data << 10) | g) ^ 0x5412;
		return bits;
	}
	function versionInfoBits(version) {
		var g = version << 12;
		var poly = 0x1F25;
		for (var i = 5; i >= 0; i--) {
			if (g & (1 << (i + 12))) { g ^= poly << i; }
		}
		return (version << 12) | g;
	}
	function applyMask(idx, r, c) {
		switch (idx) {
			case 0: return (r + c) % 2 === 0;
			case 1: return r % 2 === 0;
			case 2: return c % 3 === 0;
			case 3: return (r + c) % 3 === 0;
			case 4: return (Math.floor(r / 2) + Math.floor(c / 3)) % 2 === 0;
			case 5: return ((r * c) % 2) + ((r * c) % 3) === 0;
			case 6: return (((r * c) % 2) + ((r * c) % 3)) % 2 === 0;
			case 7: return (((r + c) % 2) + ((r * c) % 3)) % 2 === 0;
		}
		return false;
	}
	function penalty(m) {
		var n = m.length, score = 0;
		function run(line) {
			var count = 1, s = 0;
			for (var i = 1; i < line.length; i++) {
				if (line[i] === line[i - 1]) { count++; } else { if (count >= 5) { s += 3 + (count - 5); } count = 1; }
			}
			if (count >= 5) { s += 3 + (count - 5); }
			return s;
		}
		for (var r = 0; r < n; r++) { score += run(m[r]); }
		for (var c = 0; c < n; c++) { score += run(m.map(function (row) { return row[c]; })); }
		for (var r = 0; r < n - 1; r++) {
			for (var c = 0; c < n - 1; c++) {
				var v = m[r][c];
				if (v === m[r][c + 1] && v === m[r + 1][c] && v === m[r + 1][c + 1]) { score += 3; }
			}
		}
		function findFinderLike(arr) {
			var pattern1 = [1, 0, 1, 1, 1, 0, 1, 0, 0, 0, 0];
			var pattern2 = [0, 0, 0, 0, 1, 0, 1, 1, 1, 0, 1];
			var s = 0;
			for (var i = 0; i <= arr.length - 11; i++) {
				var seg = arr.slice(i, i + 11);
				if (seg.every(function (v, k) { return v === pattern1[k]; }) ||
					seg.every(function (v, k) { return v === pattern2[k]; })) { s += 40; }
			}
			return s;
		}
		for (var r = 0; r < n; r++) { score += findFinderLike(m[r]); }
		for (var c = 0; c < n; c++) { score += findFinderLike(m.map(function (row) { return row[c]; })); }
		var dark = 0;
		for (var r = 0; r < n; r++) { for (var c = 0; c < n; c++) { if (m[r][c]) { dark++; } } }
		var percent = (dark * 100) / (n * n);
		score += Math.floor(Math.abs(percent - 50) / 5) * 10;
		return score;
	}

	function generateMatrix(text, level) {
		level = level || 'M';
		var bytes = textToUtf8Bytes(text);
		var version = pickVersion(bytes.length, level);
		if (!version) { return null; }
		var dataCodewords = buildDataCodewords(bytes, version, level);
		var allCodewords = interleave(dataCodewords, version, level);
		var bitStream = [];
		allCodewords.forEach(function (byte) { for (var i = 7; i >= 0; i--) { bitStream.push((byte >>> i) & 1); } });

		var size = version * 4 + 17;
		var m = makeMatrix(size);
		placeFinder(m, 0, 0); placeFinder(m, 0, size - 7); placeFinder(m, size - 7, 0);

		var coords = ALIGN_COORDS[version];
		for (var ai = 0; ai < coords.length; ai++) {
			for (var aj = 0; aj < coords.length; aj++) {
				var row = coords[ai], col = coords[aj];
				if (m[row][col] !== null) { continue; }
				placeAlignment(m, row, col);
			}
		}
		for (var t = 0; t < size; t++) {
			if (m[6][t] === null) { m[6][t] = (t % 2 === 0) ? 1 : 0; }
			if (m[t][6] === null) { m[t][6] = (t % 2 === 0) ? 1 : 0; }
		}
		/* Format-info placeholder reservation — runs AFTER timing patterns
		   are placed, so the null-check below correctly leaves the
		   timing/format intersection cells (8,6) and (6,8) alone instead
		   of overwriting them (this ordering bug was the main reason
		   generated codes failed to scan/decode). */
		for (var i = 0; i < 9; i++) {
			if (m[i][8] === null) { m[i][8] = 2; }
			if (m[8][i] === null) { m[8][i] = 2; }
		}
		for (var i = size - 8; i < size; i++) {
			if (m[8][i] === null) { m[8][i] = 2; }
			if (m[i][8] === null) { m[i][8] = 2; }
		}
		m[size - 8][8] = 1; // dark module

		if (version >= 7) {
			var vbits = versionInfoBits(version);
			for (var i = 0; i < 18; i++) {
				var bit = (vbits >>> i) & 1;
				m[Math.floor(i / 3)][size - 11 + (i % 3)] = bit;
				m[size - 11 + (i % 3)][Math.floor(i / 3)] = bit;
			}
		}

		var dataOrder = [];
		var col = size - 1;
		while (col > 0) {
			if (col === 6) { col--; }
			for (var vert = 0; vert < size; vert++) {
				var row = ((Math.floor((size - 1 - col) / 2)) % 2 === 0) ? size - 1 - vert : vert;
				for (var cc = 0; cc < 2; cc++) {
					var c2 = col - cc;
					if (m[row][c2] === null) { dataOrder.push([row, c2]); }
				}
			}
			col -= 2;
		}
		var bestMask = 0, bestScore = Infinity, bestMatrix = null;
		for (var maskIdx = 0; maskIdx < 8; maskIdx++) {
			var mm = m.map(function (r) { return r.slice(); });
			var bp = 0;
			dataOrder.forEach(function (pos) {
				var bitVal = bp < bitStream.length ? bitStream[bp] : 0; bp++;
				var flip = applyMask(maskIdx, pos[0], pos[1]);
				mm[pos[0]][pos[1]] = flip ? (bitVal ^ 1) : bitVal;
			});
			var fbits = formatInfoBits(level, maskIdx);
			for (var i = 0; i < 15; i++) {
				var bit = (fbits >>> i) & 1;
				if (i < 6) { mm[i][8] = bit; }
				else if (i < 8) { mm[i + 1][8] = bit; }
				else { mm[size - 15 + i][8] = bit; }
				if (i < 8) { mm[8][size - i - 1] = bit; }
				else if (i < 9) { mm[8][15 - i - 1 + 1] = bit; }
				else { mm[8][15 - i - 1] = bit; }
			}
			for (var r = 0; r < size; r++) { for (var c = 0; c < size; c++) { if (mm[r][c] === null || mm[r][c] === 2) { mm[r][c] = 0; } } }
			var sc = penalty(mm);
			if (sc < bestScore) { bestScore = sc; bestMask = maskIdx; bestMatrix = mm; }
		}
		return { size: size, version: version, matrix: bestMatrix };
	}

	global.MTQRCode = { generate: generateMatrix };
	if (typeof module !== 'undefined') { module.exports = global.MTQRCode; }
})(typeof window !== 'undefined' ? window : global);
