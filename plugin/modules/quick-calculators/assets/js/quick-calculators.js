document.addEventListener('DOMContentLoaded', function () {

	// ---- Auto-fill today's date on any "as of" style field ----
	document.querySelectorAll('.qcalc-autotoday').forEach(function (input) {
		var today = new Date();
		var yyyy = today.getFullYear();
		var mm = String(today.getMonth() + 1).padStart(2, '0');
		var dd = String(today.getDate()).padStart(2, '0');
		input.value = yyyy + '-' + mm + '-' + dd;
	});

	// ---- Age Calculator ----
	document.querySelectorAll('.qcalc-age-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var dobVal = wrap.querySelector('.qcalc-dob').value;
			var asofVal = wrap.querySelector('.qcalc-asof').value;
			var result = wrap.querySelector('.qcalc-age-result');

			if (!dobVal) {
				showError(result, 'Please select your date of birth.');
				return;
			}

			var dob = new Date(dobVal);
			var asof = asofVal ? new Date(asofVal) : new Date();

			if (dob > asof) {
				showError(result, 'Date of birth cannot be after the "as of" date.');
				return;
			}

			var years = asof.getFullYear() - dob.getFullYear();
			var months = asof.getMonth() - dob.getMonth();
			var days = asof.getDate() - dob.getDate();

			if (days < 0) {
				months -= 1;
				var prevMonth = new Date(asof.getFullYear(), asof.getMonth(), 0);
				days += prevMonth.getDate();
			}
			if (months < 0) {
				years -= 1;
				months += 12;
			}

			var totalDays = Math.floor((asof - dob) / (1000 * 60 * 60 * 24));

			result.innerHTML =
				'<strong>' + years + ' years, ' + months + ' months, ' + days + ' days</strong><br>' +
				'Total: ' + totalDays.toLocaleString() + ' days old';
			result.style.display = 'block';
		});
	});

	// ---- BMI Calculator ----

	// Toggle metric/imperial height fields + weight unit label
	document.querySelectorAll('.qcalc-bmi-units').forEach(function (select) {
		select.addEventListener('change', function () {
			var wrap = select.closest('.qcalc-wrap');
			var metricBlock = wrap.querySelector('.qcalc-bmi-height-metric');
			var imperialBlock = wrap.querySelector('.qcalc-bmi-height-imperial');
			var weightLabel = wrap.querySelector('.qcalc-bmi-weight-unit-label');

			if (select.value === 'metric') {
				metricBlock.style.display = '';
				imperialBlock.style.display = 'none';
				weightLabel.textContent = '(kg)';
			} else {
				metricBlock.style.display = 'none';
				imperialBlock.style.display = '';
				weightLabel.textContent = '(lb)';
			}
		});
	});

	document.querySelectorAll('.qcalc-bmi-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var units = wrap.querySelector('.qcalc-bmi-units').value;
			var gender = wrap.querySelector('.qcalc-bmi-gender').value;
			var weight = parseFloat(wrap.querySelector('.qcalc-bmi-weight').value);
			var result = wrap.querySelector('.qcalc-bmi-result');

			var bmi;

			if (units === 'metric') {
				var heightCm = parseFloat(wrap.querySelector('.qcalc-bmi-height-cm').value);
				if (!weight || !heightCm) {
					showError(result, 'Please enter both weight and height.');
					return;
				}
				var heightM = heightCm / 100;
				bmi = weight / (heightM * heightM);
			} else {
				var feet = parseFloat(wrap.querySelector('.qcalc-bmi-height-ft').value) || 0;
				var inches = parseFloat(wrap.querySelector('.qcalc-bmi-height-in').value) || 0;
				var totalInches = (feet * 12) + inches;

				if (!weight || !totalInches) {
					showError(result, 'Please enter weight and height (feet & inches).');
					return;
				}
				bmi = (weight / (totalInches * totalInches)) * 703;
			}

			var category;
			if (bmi < 18.5) category = 'Underweight';
			else if (bmi < 25) category = 'Normal weight';
			else if (bmi < 30) category = 'Overweight';
			else category = 'Obese';

			var genderLabel = gender === 'female' ? 'Female' : 'Male';

			result.innerHTML =
				'<strong>BMI: ' + bmi.toFixed(1) + '</strong><br>' +
				'Gender: ' + genderLabel + '<br>' +
				'Category: ' + category +
				'<br><small>This is a general indicator, not a medical diagnosis. BMI ranges are the same standard scale for both genders.</small>';
			result.style.display = 'block';
		});
	});

	// ---- Percentage Calculator ----
	document.querySelectorAll('.qcalc-pct-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var x = parseFloat(wrap.querySelector('.qcalc-pct-x').value);
			var y = parseFloat(wrap.querySelector('.qcalc-pct-y').value);
			var result = wrap.querySelector('.qcalc-pct-result');

			if (isNaN(x) || isNaN(y)) {
				showError(result, 'Please enter both numbers.');
				return;
			}

			var answer = (x / 100) * y;
			result.innerHTML = '<strong>' + x + '% of ' + y + ' = ' + roundSmart(answer) + '</strong>';
			result.style.display = 'block';
		});
	});

	// ---- GPA Calculator ----
	document.querySelectorAll('.qcalc-gpa-add').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var rows = wrap.querySelector('.qcalc-gpa-rows');
			var firstRow = rows.querySelector('.qcalc-gpa-row');
			var newRow = firstRow.cloneNode(true);
			newRow.querySelector('.qcalc-gpa-course').value = '';
			newRow.querySelector('.qcalc-gpa-credits').value = '3';
			rows.appendChild(newRow);
		});
	});

	document.querySelectorAll('.qcalc-gpa-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var rows = wrap.querySelectorAll('.qcalc-gpa-row');
			var result = wrap.querySelector('.qcalc-gpa-result');

			var totalPoints = 0;
			var totalCredits = 0;

			rows.forEach(function (row) {
				var grade = parseFloat(row.querySelector('.qcalc-gpa-grade').value);
				var credits = parseFloat(row.querySelector('.qcalc-gpa-credits').value) || 0;
				totalPoints += grade * credits;
				totalCredits += credits;
			});

			if (totalCredits === 0) {
				showError(result, 'Please enter at least one course with credit hours.');
				return;
			}

			var gpa = totalPoints / totalCredits;
			result.innerHTML =
				'<strong>Your GPA: ' + gpa.toFixed(2) + '</strong><br>' +
				'Total credit hours: ' + totalCredits;
			result.style.display = 'block';
		});
	});

	// ==================== FINANCE TOOLS ====================

	// Simple Interest
	document.querySelectorAll('.qcalc-si-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var p = parseFloat(wrap.querySelector('.qcalc-si-principal').value);
			var r = parseFloat(wrap.querySelector('.qcalc-si-rate').value);
			var t = parseFloat(wrap.querySelector('.qcalc-si-time').value);
			var result = wrap.querySelector('.qcalc-si-result');

			if (isNaN(p) || isNaN(r) || isNaN(t)) {
				showError(result, 'Please fill in all fields.');
				return;
			}

			var interest = (p * r * t) / 100;
			var total = p + interest;
			result.innerHTML =
				'<strong>Interest: ' + roundSmart(interest).toLocaleString() + '</strong><br>' +
				'Total Amount: ' + roundSmart(total).toLocaleString();
			result.style.display = 'block';
		});
	});

	// Compound Interest
	document.querySelectorAll('.qcalc-ci-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var p = parseFloat(wrap.querySelector('.qcalc-ci-principal').value);
			var r = parseFloat(wrap.querySelector('.qcalc-ci-rate').value);
			var t = parseFloat(wrap.querySelector('.qcalc-ci-time').value);
			var n = parseFloat(wrap.querySelector('.qcalc-ci-freq').value);
			var result = wrap.querySelector('.qcalc-ci-result');

			if (isNaN(p) || isNaN(r) || isNaN(t)) {
				showError(result, 'Please fill in all fields.');
				return;
			}

			var total = p * Math.pow(1 + (r / 100) / n, n * t);
			var interest = total - p;
			result.innerHTML =
				'<strong>Total Amount: ' + roundSmart(total).toLocaleString() + '</strong><br>' +
				'Interest Earned: ' + roundSmart(interest).toLocaleString();
			result.style.display = 'block';
		});
	});

	// Loan / EMI
	document.querySelectorAll('.qcalc-loan-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var amount = parseFloat(wrap.querySelector('.qcalc-loan-amount').value);
			var rate = parseFloat(wrap.querySelector('.qcalc-loan-rate').value);
			var months = parseFloat(wrap.querySelector('.qcalc-loan-months').value);
			var result = wrap.querySelector('.qcalc-loan-result');

			if (!amount || !months) {
				showError(result, 'Please fill in all fields.');
				return;
			}

			var monthlyRate = (rate / 100) / 12;
			var emi;
			if (monthlyRate === 0) {
				emi = amount / months;
			} else {
				emi = (amount * monthlyRate * Math.pow(1 + monthlyRate, months)) / (Math.pow(1 + monthlyRate, months) - 1);
			}
			var totalPayment = emi * months;
			var totalInterest = totalPayment - amount;

			result.innerHTML =
				'<strong>Monthly EMI: ' + roundSmart(emi).toLocaleString() + '</strong><br>' +
				'Total Payment: ' + roundSmart(totalPayment).toLocaleString() + '<br>' +
				'Total Interest: ' + roundSmart(totalInterest).toLocaleString();
			result.style.display = 'block';
		});
	});

	// Discount
	document.querySelectorAll('.qcalc-disc-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var price = parseFloat(wrap.querySelector('.qcalc-disc-price').value);
			var percent = parseFloat(wrap.querySelector('.qcalc-disc-percent').value);
			var result = wrap.querySelector('.qcalc-disc-result');

			if (isNaN(price) || isNaN(percent)) {
				showError(result, 'Please fill in all fields.');
				return;
			}

			var savedAmount = (price * percent) / 100;
			var finalPrice = price - savedAmount;

			result.innerHTML =
				'<strong>Final Price: ' + roundSmart(finalPrice).toLocaleString() + '</strong><br>' +
				'You Save: ' + roundSmart(savedAmount).toLocaleString();
			result.style.display = 'block';
		});
	});

	// Tip
	document.querySelectorAll('.qcalc-tip-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var bill = parseFloat(wrap.querySelector('.qcalc-tip-bill').value);
			var percent = parseFloat(wrap.querySelector('.qcalc-tip-percent').value);
			var people = parseFloat(wrap.querySelector('.qcalc-tip-people').value) || 1;
			var result = wrap.querySelector('.qcalc-tip-result');

			if (isNaN(bill) || isNaN(percent)) {
				showError(result, 'Please fill in all fields.');
				return;
			}

			var tipAmount = (bill * percent) / 100;
			var total = bill + tipAmount;

			result.innerHTML =
				'<strong>Tip Amount: ' + roundSmart(tipAmount).toLocaleString() + '</strong><br>' +
				'Total Bill: ' + roundSmart(total).toLocaleString() + '<br>' +
				'Per Person: ' + roundSmart(total / people).toLocaleString();
			result.style.display = 'block';
		});
	});

	// ==================== CONVERTERS ====================

	var LENGTH_TO_M = { mm: 0.001, cm: 0.01, m: 1, km: 1000, in: 0.0254, ft: 0.3048, yd: 0.9144, mi: 1609.344 };

	document.querySelectorAll('.qcalc-len-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var value = parseFloat(wrap.querySelector('.qcalc-len-value').value);
			var from = wrap.querySelector('.qcalc-len-from').value;
			var to = wrap.querySelector('.qcalc-len-to').value;
			var result = wrap.querySelector('.qcalc-len-result');

			if (isNaN(value)) {
				showError(result, 'Please enter a value.');
				return;
			}

			var meters = value * LENGTH_TO_M[from];
			var converted = meters / LENGTH_TO_M[to];

			result.innerHTML = '<strong>' + value + ' ' + from + ' = ' + roundSmart(converted).toLocaleString() + ' ' + to + '</strong>';
			result.style.display = 'block';
		});
	});

	var WEIGHT_TO_G = { mg: 0.001, g: 1, kg: 1000, oz: 28.3495, lb: 453.592, ton: 1000000 };

	document.querySelectorAll('.qcalc-wt-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var value = parseFloat(wrap.querySelector('.qcalc-wt-value').value);
			var from = wrap.querySelector('.qcalc-wt-from').value;
			var to = wrap.querySelector('.qcalc-wt-to').value;
			var result = wrap.querySelector('.qcalc-wt-result');

			if (isNaN(value)) {
				showError(result, 'Please enter a value.');
				return;
			}

			var grams = value * WEIGHT_TO_G[from];
			var converted = grams / WEIGHT_TO_G[to];

			result.innerHTML = '<strong>' + value + ' ' + from + ' = ' + roundSmart(converted).toLocaleString() + ' ' + to + '</strong>';
			result.style.display = 'block';
		});
	});

	document.querySelectorAll('.qcalc-temp-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var value = parseFloat(wrap.querySelector('.qcalc-temp-value').value);
			var from = wrap.querySelector('.qcalc-temp-from').value;
			var to = wrap.querySelector('.qcalc-temp-to').value;
			var result = wrap.querySelector('.qcalc-temp-result');

			if (isNaN(value)) {
				showError(result, 'Please enter a value.');
				return;
			}

			// convert to Celsius first
			var celsius;
			if (from === 'c') celsius = value;
			else if (from === 'f') celsius = (value - 32) * 5 / 9;
			else celsius = value - 273.15;

			var converted;
			if (to === 'c') converted = celsius;
			else if (to === 'f') converted = (celsius * 9 / 5) + 32;
			else converted = celsius + 273.15;

			result.innerHTML = '<strong>' + value + '° ' + from.toUpperCase() + ' = ' + roundSmart(converted) + '° ' + to.toUpperCase() + '</strong>';
			result.style.display = 'block';
		});
	});

	// Area — toggle fields based on shape
	document.querySelectorAll('.qcalc-area-shape').forEach(function (select) {
		select.addEventListener('change', function () {
			var wrap = select.closest('.qcalc-wrap');
			var fieldsDiv = wrap.querySelector('.qcalc-area-fields');
			var shape = select.value;
			fieldsDiv.setAttribute('data-shape', shape);

			if (shape === 'circle') {
				fieldsDiv.innerHTML = '<label>Radius <input type="number" class="qcalc-area-a" step="any" /></label>';
			} else if (shape === 'square') {
				fieldsDiv.innerHTML = '<label>Side <input type="number" class="qcalc-area-a" step="any" /></label>';
			} else if (shape === 'triangle') {
				fieldsDiv.innerHTML = '<label>Base <input type="number" class="qcalc-area-a" step="any" /></label><label>Height <input type="number" class="qcalc-area-b" step="any" /></label>';
			} else {
				fieldsDiv.innerHTML = '<label>Length <input type="number" class="qcalc-area-a" step="any" /></label><label>Width <input type="number" class="qcalc-area-b" step="any" /></label>';
			}
		});
	});

	document.querySelectorAll('.qcalc-area-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var shape = wrap.querySelector('.qcalc-area-shape').value;
			var a = parseFloat(wrap.querySelector('.qcalc-area-a').value);
			var bInput = wrap.querySelector('.qcalc-area-b');
			var b = bInput ? parseFloat(bInput.value) : null;
			var result = wrap.querySelector('.qcalc-area-result');

			if (isNaN(a) || (shape !== 'circle' && shape !== 'square' && isNaN(b)) || (shape === 'rectangle' && isNaN(b))) {
				if (isNaN(a)) {
					showError(result, 'Please enter the required values.');
					return;
				}
			}

			var area;
			var unit = 'sq units';
			if (shape === 'circle') {
				area = Math.PI * a * a;
			} else if (shape === 'square') {
				area = a * a;
			} else if (shape === 'triangle') {
				area = 0.5 * a * b;
			} else {
				area = a * b;
			}

			result.innerHTML = '<strong>Area: ' + roundSmart(area).toLocaleString() + ' ' + unit + '</strong>';
			result.style.display = 'block';
		});
	});

	// Number base converter
	document.querySelectorAll('.qcalc-base-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var value = wrap.querySelector('.qcalc-base-value').value.trim();
			var fromBase = parseInt(wrap.querySelector('.qcalc-base-from').value, 10);
			var result = wrap.querySelector('.qcalc-base-result');

			var decimal = parseInt(value, fromBase);
			if (isNaN(decimal)) {
				showError(result, 'Please enter a valid number for the selected base.');
				return;
			}

			result.innerHTML =
				'<strong>Binary:</strong> ' + decimal.toString(2) + '<br>' +
				'<strong>Octal:</strong> ' + decimal.toString(8) + '<br>' +
				'<strong>Decimal:</strong> ' + decimal.toString(10) + '<br>' +
				'<strong>Hexadecimal:</strong> ' + decimal.toString(16).toUpperCase();
			result.style.display = 'block';
		});
	});

	// Roman numeral converter
	document.querySelectorAll('.qcalc-roman-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var numInput = wrap.querySelector('.qcalc-roman-num');
			var textInput = wrap.querySelector('.qcalc-roman-text');
			var result = wrap.querySelector('.qcalc-roman-result');

			if (numInput.value) {
				var num = parseInt(numInput.value, 10);
				if (num < 1 || num > 3999) {
					showError(result, 'Please enter a number between 1 and 3999.');
					return;
				}
				result.innerHTML = '<strong>' + num + ' = ' + toRoman(num) + '</strong>';
				result.style.display = 'block';
			} else if (textInput.value) {
				var roman = textInput.value.trim().toUpperCase();
				var val = fromRoman(roman);
				if (val === null) {
					showError(result, 'Please enter a valid Roman numeral.');
					return;
				}
				result.innerHTML = '<strong>' + roman + ' = ' + val + '</strong>';
				result.style.display = 'block';
			} else {
				showError(result, 'Please enter a number or a Roman numeral.');
			}
		});
	});

	function toRoman(num) {
		var map = [[1000, 'M'], [900, 'CM'], [500, 'D'], [400, 'CD'], [100, 'C'], [90, 'XC'], [50, 'L'], [40, 'XL'], [10, 'X'], [9, 'IX'], [5, 'V'], [4, 'IV'], [1, 'I']];
		var out = '';
		map.forEach(function (pair) {
			while (num >= pair[0]) {
				out += pair[1];
				num -= pair[0];
			}
		});
		return out;
	}

	function fromRoman(str) {
		var map = { I: 1, V: 5, X: 10, L: 50, C: 100, D: 500, M: 1000 };
		var total = 0;
		for (var i = 0; i < str.length; i++) {
			var current = map[str[i]];
			var next = map[str[i + 1]];
			if (!current) return null;
			if (next && current < next) {
				total -= current;
			} else {
				total += current;
			}
		}
		return total > 0 ? total : null;
	}

	// Number to words
	document.querySelectorAll('.qcalc-nw-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var num = parseInt(wrap.querySelector('.qcalc-nw-value').value, 10);
			var result = wrap.querySelector('.qcalc-nw-result');

			if (isNaN(num)) {
				showError(result, 'Please enter a number.');
				return;
			}

			result.innerHTML = '<strong>' + numberToWords(num) + '</strong>';
			result.style.display = 'block';
		});
	});

	function numberToWords(num) {
		if (num === 0) return 'Zero';
		var negative = num < 0;
		num = Math.abs(num);

		var ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
		var tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

		function chunk(n) {
			var str = '';
			if (n >= 100) {
				str += ones[Math.floor(n / 100)] + ' Hundred ';
				n %= 100;
			}
			if (n >= 20) {
				str += tens[Math.floor(n / 10)] + ' ';
				n %= 10;
			}
			if (n > 0) {
				str += ones[n] + ' ';
			}
			return str.trim();
		}

		var units = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];
		var groups = [];
		while (num > 0) {
			groups.push(num % 1000);
			num = Math.floor(num / 1000);
		}

		var words = [];
		for (var i = groups.length - 1; i >= 0; i--) {
			if (groups[i] > 0) {
				words.push(chunk(groups[i]) + (units[i] ? ' ' + units[i] : ''));
			}
		}

		return (negative ? 'Negative ' : '') + words.join(' ').trim();
	}

	// ==================== TEXT TOOLS ====================

	document.querySelectorAll('.qcalc-wc-input').forEach(function (textarea) {
		textarea.addEventListener('input', function () {
			var wrap = textarea.closest('.qcalc-wrap');
			var result = wrap.querySelector('.qcalc-wc-result');
			var text = textarea.value;

			var words = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
			var chars = text.length;
			var sentences = text.trim() === '' ? 0 : (text.match(/[.!?]+/g) || []).length;
			var paragraphs = text.trim() === '' ? 0 : text.split(/\n+/).filter(function (p) { return p.trim() !== ''; }).length;

			result.innerHTML = 'Words: ' + words + ' | Characters: ' + chars + ' | Sentences: ' + sentences + ' | Paragraphs: ' + paragraphs;
		});
	});

	document.querySelectorAll('.qcalc-cc-input').forEach(function (textarea) {
		textarea.addEventListener('input', function () {
			var wrap = textarea.closest('.qcalc-wrap');
			var result = wrap.querySelector('.qcalc-cc-result');
			var text = textarea.value;

			var withSpaces = text.length;
			var withoutSpaces = text.replace(/\s/g, '').length;

			result.innerHTML = 'With spaces: ' + withSpaces + ' | Without spaces: ' + withoutSpaces;
		});
	});

	document.querySelectorAll('.qcalc-case-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var input = wrap.querySelector('.qcalc-case-input');
			var result = wrap.querySelector('.qcalc-case-result');
			var text = input.value;
			var mode = btn.getAttribute('data-case');

			var output;
			if (mode === 'upper') {
				output = text.toUpperCase();
			} else if (mode === 'lower') {
				output = text.toLowerCase();
			} else if (mode === 'title') {
				output = text.toLowerCase().replace(/\b\w/g, function (c) { return c.toUpperCase(); });
			} else {
				output = text.toLowerCase().replace(/(^\s*\w|[.!?]\s*\w)/g, function (c) { return c.toUpperCase(); });
			}

			result.innerHTML = output;
			result.style.display = 'block';
		});
	});

	document.querySelectorAll('.qcalc-pw-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var length = parseInt(wrap.querySelector('.qcalc-pw-length').value, 10) || 16;
			var useUpper = wrap.querySelector('.qcalc-pw-upper').checked;
			var useNumbers = wrap.querySelector('.qcalc-pw-numbers').checked;
			var useSymbols = wrap.querySelector('.qcalc-pw-symbols').checked;
			var result = wrap.querySelector('.qcalc-pw-result');

			var chars = 'abcdefghijklmnopqrstuvwxyz';
			if (useUpper) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			if (useNumbers) chars += '0123456789';
			if (useSymbols) chars += '!@#$%^&*()_+-=[]{}';

			var password = '';
			var randomValues = new Uint32Array(length);
			window.crypto.getRandomValues(randomValues);
			for (var i = 0; i < length; i++) {
				password += chars[randomValues[i] % chars.length];
			}

			result.innerHTML = password;
			result.style.display = 'block';
		});
	});

	// ==================== HEALTH TOOLS ====================

	document.querySelectorAll('.qcalc-bmr-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var gender = wrap.querySelector('.qcalc-bmr-gender').value;
			var age = parseFloat(wrap.querySelector('.qcalc-bmr-age').value);
			var weight = parseFloat(wrap.querySelector('.qcalc-bmr-weight').value);
			var height = parseFloat(wrap.querySelector('.qcalc-bmr-height').value);
			var activity = parseFloat(wrap.querySelector('.qcalc-bmr-activity').value);
			var result = wrap.querySelector('.qcalc-bmr-result');

			if (!age || !weight || !height) {
				showError(result, 'Please fill in all fields.');
				return;
			}

			var bmr;
			if (gender === 'male') {
				bmr = (10 * weight) + (6.25 * height) - (5 * age) + 5;
			} else {
				bmr = (10 * weight) + (6.25 * height) - (5 * age) - 161;
			}

			var maintenance = bmr * activity;

			result.innerHTML =
				'<strong>BMR: ' + Math.round(bmr) + ' calories/day</strong><br>' +
				'Estimated daily calories (with activity): ' + Math.round(maintenance);
			result.style.display = 'block';
		});
	});

	document.querySelectorAll('.qcalc-iw-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var gender = wrap.querySelector('.qcalc-iw-gender').value;
			var heightCm = parseFloat(wrap.querySelector('.qcalc-iw-height').value);
			var result = wrap.querySelector('.qcalc-iw-result');

			if (!heightCm) {
				showError(result, 'Please enter your height.');
				return;
			}

			var heightIn = heightCm / 2.54;
			var heightOver5ft = Math.max(0, heightIn - 60);

			var idealKg;
			if (gender === 'male') {
				idealKg = 50 + 2.3 * heightOver5ft;
			} else {
				idealKg = 45.5 + 2.3 * heightOver5ft;
			}

			result.innerHTML =
				'<strong>Estimated Ideal Weight: ' + roundSmart(idealKg) + ' kg</strong><br>' +
				'<small>Based on the Devine formula. Actual healthy weight varies by build and muscle mass.</small>';
			result.style.display = 'block';
		});
	});

	document.querySelectorAll('.qcalc-preg-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var lmpVal = wrap.querySelector('.qcalc-preg-lmp').value;
			var result = wrap.querySelector('.qcalc-preg-result');

			if (!lmpVal) {
				showError(result, 'Please select the first day of your last period.');
				return;
			}

			var lmp = new Date(lmpVal);
			var dueDate = new Date(lmp);
			dueDate.setDate(dueDate.getDate() + 280);

			var options = { year: 'numeric', month: 'long', day: 'numeric' };
			result.innerHTML = '<strong>Estimated Due Date: ' + dueDate.toLocaleDateString('en-US', options) + '</strong>';
			result.style.display = 'block';
		});
	});

	// ==================== MATH TOOLS ====================

	document.querySelectorAll('.qcalc-avg-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var input = wrap.querySelector('.qcalc-avg-input').value;
			var result = wrap.querySelector('.qcalc-avg-result');

			var nums = input.split(',').map(function (n) { return parseFloat(n.trim()); }).filter(function (n) { return !isNaN(n); });

			if (nums.length === 0) {
				showError(result, 'Please enter valid numbers separated by commas.');
				return;
			}

			var sum = nums.reduce(function (a, b) { return a + b; }, 0);
			var avg = sum / nums.length;

			result.innerHTML =
				'<strong>Average: ' + roundSmart(avg) + '</strong><br>' +
				'Count: ' + nums.length + ' | Sum: ' + roundSmart(sum);
			result.style.display = 'block';
		});
	});

	document.querySelectorAll('.qcalc-pc-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var initial = parseFloat(wrap.querySelector('.qcalc-pc-initial').value);
			var newVal = parseFloat(wrap.querySelector('.qcalc-pc-new').value);
			var result = wrap.querySelector('.qcalc-pc-result');

			if (isNaN(initial) || isNaN(newVal) || initial === 0) {
				showError(result, 'Please enter valid, non-zero values.');
				return;
			}

			var change = ((newVal - initial) / Math.abs(initial)) * 100;
			var direction = change >= 0 ? 'Increase' : 'Decrease';

			result.innerHTML = '<strong>' + direction + ': ' + roundSmart(Math.abs(change)) + '%</strong>';
			result.style.display = 'block';
		});
	});

	document.querySelectorAll('.qcalc-mp-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var obtained = parseFloat(wrap.querySelector('.qcalc-mp-obtained').value);
			var total = parseFloat(wrap.querySelector('.qcalc-mp-total').value);
			var result = wrap.querySelector('.qcalc-mp-result');

			if (isNaN(obtained) || !total) {
				showError(result, 'Please fill in both fields.');
				return;
			}

			var percent = (obtained / total) * 100;
			var grade;
			if (percent >= 90) grade = 'A+';
			else if (percent >= 80) grade = 'A';
			else if (percent >= 70) grade = 'B';
			else if (percent >= 60) grade = 'C';
			else if (percent >= 50) grade = 'D';
			else grade = 'F';

			result.innerHTML = '<strong>Percentage: ' + roundSmart(percent) + '%</strong><br>Approximate Grade: ' + grade;
			result.style.display = 'block';
		});
	});

	document.querySelectorAll('.qcalc-sdt-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var dEl = wrap.querySelector('.qcalc-sdt-distance');
			var sEl = wrap.querySelector('.qcalc-sdt-speed');
			var tEl = wrap.querySelector('.qcalc-sdt-time');
			var result = wrap.querySelector('.qcalc-sdt-result');

			var d = dEl.value ? parseFloat(dEl.value) : null;
			var s = sEl.value ? parseFloat(sEl.value) : null;
			var t = tEl.value ? parseFloat(tEl.value) : null;

			var filled = [d, s, t].filter(function (v) { return v !== null; }).length;
			if (filled !== 2) {
				showError(result, 'Please fill in exactly two fields, leave the third blank.');
				return;
			}

			if (d === null) {
				d = s * t;
				result.innerHTML = '<strong>Distance: ' + roundSmart(d) + ' km</strong>';
			} else if (s === null) {
				s = d / t;
				result.innerHTML = '<strong>Speed: ' + roundSmart(s) + ' km/h</strong>';
			} else {
				t = d / s;
				result.innerHTML = '<strong>Time: ' + roundSmart(t) + ' hours</strong>';
			}
			result.style.display = 'block';
		});
	});

	document.querySelectorAll('.qcalc-ratio-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var a = parseFloat(wrap.querySelector('.qcalc-ratio-a').value);
			var b = parseFloat(wrap.querySelector('.qcalc-ratio-b').value);
			var result = wrap.querySelector('.qcalc-ratio-result');

			if (!a || !b) {
				showError(result, 'Please enter both values.');
				return;
			}

			var divisor = gcd(Math.round(a), Math.round(b));
			result.innerHTML = '<strong>Simplified Ratio: ' + (a / divisor) + ' : ' + (b / divisor) + '</strong>';
			result.style.display = 'block';
		});
	});

	function gcd(a, b) {
		a = Math.abs(a);
		b = Math.abs(b);
		while (b) {
			var temp = b;
			b = a % b;
			a = temp;
		}
		return a || 1;
	}

	// ==================== DATE/TIME TOOLS ====================

	document.querySelectorAll('.qcalc-dd-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var wrap = btn.closest('.qcalc-wrap');
			var startVal = wrap.querySelector('.qcalc-dd-start').value;
			var endVal = wrap.querySelector('.qcalc-dd-end').value;
			var result = wrap.querySelector('.qcalc-dd-result');

			if (!startVal || !endVal) {
				showError(result, 'Please select both dates.');
				return;
			}

			var start = new Date(startVal);
			var end = new Date(endVal);
			var diffMs = Math.abs(end - start);
			var days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
			var weeks = Math.floor(days / 7);
			var months = Math.floor(days / 30.44);

			result.innerHTML =
				'<strong>' + days.toLocaleString() + ' days</strong><br>' +
				'≈ ' + weeks + ' weeks | ≈ ' + months + ' months';
			result.style.display = 'block';
		});
	});

	function showError(resultEl, message) {
		resultEl.innerHTML = message;
		resultEl.style.display = 'block';
	}

	function roundSmart(num) {
		return Math.round(num * 100) / 100;
	}
});
