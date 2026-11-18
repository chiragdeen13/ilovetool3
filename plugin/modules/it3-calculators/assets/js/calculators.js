/* ============ iLoveTool3 Calculators JS v2 ============ */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); }
        else { document.addEventListener('DOMContentLoaded', fn); }
    }

    function fmt(n, dec) {
        return Number(n).toLocaleString('en-US', {
            minimumFractionDigits: dec || 0,
            maximumFractionDigits: (dec === undefined ? 0 : dec)
        });
    }

    function showResult(calc, html) {
        var box = calc.querySelector('.it3-result');
        var err = calc.querySelector('.it3-error');
        if (err) { err.style.display = 'none'; }
        box.innerHTML = html;
        box.style.display = 'block';
    }

    function showError(calc, msg) {
        var box = calc.querySelector('.it3-error');
        var res = calc.querySelector('.it3-result');
        if (res) { res.style.display = 'none'; }
        box.textContent = msg;
        box.style.display = 'block';
    }

    function val(id) { return document.getElementById(id).value.trim(); }
    function num(id) { return parseFloat(val(id)); }

    /* ================= CALCULATOR LOGICS ================= */
    var calcs = {

        /* ---------- 1. LOAN / EMI ---------- */
        loan: function (calc) {
            var amount = num('it3-loan-amount');
            var rate = num('it3-loan-rate');
            var years = num('it3-loan-years');

            if (!amount || amount <= 0) { showError(calc, 'Please enter a valid loan amount.'); return; }
            if (isNaN(rate) || rate < 0) { showError(calc, 'Please enter a valid interest rate.'); return; }
            if (!years || years <= 0) { showError(calc, 'Please enter a valid tenure in years.'); return; }

            var months = years * 12;
            var r = rate / 100 / 12;
            var emi;

            if (r === 0) { emi = amount / months; }
            else {
                emi = amount * r * Math.pow(1 + r, months) / (Math.pow(1 + r, months) - 1);
            }

            var total = emi * months;
            var interest = total - amount;

            showResult(calc,
                '<div class="it3-res-row"><span>Monthly EMI</span><strong class="it3-highlight">' + fmt(emi, 2) + '</strong></div>' +
                '<div class="it3-res-row"><span>Total Interest</span><strong class="it3-green">' + fmt(interest, 2) + '</strong></div>' +
                '<div class="it3-res-row"><span>Total Payment (' + months + ' months)</span><strong>' + fmt(total, 2) + '</strong></div>'
            );
        },

        /* ---------- 2. CURRENCY CONVERTER (LIVE) ---------- */
        currency: function (calc) {
            var amount = num('it3-cur-amount');
            var from = val('it3-cur-from');
            var to = val('it3-cur-to');

            if (!amount && amount !== 0) { showError(calc, 'Please enter an amount.'); return; }

            showResult(calc, '<p style="text-align:center;color:#64748b;">&#8987; Fetching live rates...</p>');

            fetch('https://open.er-api.com/v6/latest/' + from)
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.result === 'success' && data.rates && data.rates[to] !== undefined) {
                        var rate = data.rates[to];
                        var converted = amount * rate;
                        showResult(calc,
                            '<div class="it3-res-big">' + fmt(amount, 2) + ' ' + from + ' = <br><span class="it3-highlight">' + fmt(converted, 2) + ' ' + to + '</span></div>' +
                            '<div class="it3-res-row"><span>Exchange Rate</span><strong>1 ' + from + ' = ' + fmt(rate, 4) + ' ' + to + '</strong></div>' +
                            '<div class="it3-res-row"><span>Last Updated</span><strong>' + (data.time_last_update_utc || 'Live') + '</strong></div>'
                        );
                    } else {
                        showError(calc, 'Rate not available for ' + from + ' to ' + to + '. Please try again.');
                    }
                })
                .catch(function () {
                    /* fallback API */
                    fetch('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/' + from.toLowerCase() + '.json')
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            var ratesObj = data[from.toLowerCase()];
                            if (ratesObj && ratesObj[to.toLowerCase()] !== undefined) {
                                var rate2 = ratesObj[to.toLowerCase()];
                                showResult(calc,
                                    '<div class="it3-res-big">' + fmt(amount, 2) + ' ' + from + ' = <br><span class="it3-highlight">' + fmt(amount * rate2, 2) + ' ' + to + '</span></div>' +
                                    '<div class="it3-res-row"><span>Exchange Rate</span><strong>1 ' + from + ' = ' + fmt(rate2, 4) + ' ' + to + '</strong></div>'
                                );
                            } else {
                                showError(calc, 'Could not fetch exchange rate. Check your internet connection.');
                            }
                        })
                        .catch(function () {
                            showError(calc, 'Could not fetch live rates. Please check internet and try again.');
                        });
                });
        },

        /* ---------- 3. SIP CALCULATOR ---------- */
        sip: function (calc) {
            var monthly = num('it3-sip-monthly');
            var rate = num('it3-sip-rate');
            var years = num('it3-sip-years');

            if (!monthly || monthly <= 0) { showError(calc, 'Please enter a valid monthly investment.'); return; }
            if (isNaN(rate) || rate < 0) { showError(calc, 'Please enter a valid expected return rate.'); return; }
            if (!years || years <= 0) { showError(calc, 'Please enter a valid duration in years.'); return; }

            var i = rate / 100 / 12;
            var n = years * 12;
            var invested = monthly * n;
            var futureValue;

            if (i === 0) { futureValue = invested; }
            else {
                futureValue = monthly * ((Math.pow(1 + i, n) - 1) / i) * (1 + i);
            }

            var gains = futureValue - invested;

            showResult(calc,
                '<div class="it3-res-row"><span>Invested Amount</span><strong>' + fmt(invested, 0) + '</strong></div>' +
                '<div class="it3-res-row"><span>Estimated Returns</span><strong class="it3-green">' + fmt(gains, 0) + '</strong></div>' +
                '<div class="it3-res-row"><span>Total Value After ' + years + ' Years</span><strong class="it3-highlight">' + fmt(futureValue, 0) + '</strong></div>'
            );
        },

        /* ---------- 4. TAX CALCULATOR ---------- */
        tax: function (calc) {
            var country = val('it3-tax-country');
            var income = num('it3-tax-income');

            if (isNaN(income) || income < 0) { showError(calc, 'Please enter a valid annual income.'); return; }

            var slabs = TAX_SLABS[country];
            if (!slabs) { showError(calc, 'Invalid selection.'); return; }

            var cur = slabs.currency;
            var tax = computeProgressiveTax(income, slabs.slabs);
            var netIncome = income - tax;

            /* India rebate u/s 87A */
            var rebateNote = '';
            if (country === 'in' && income <= 1200000 && tax > 0) {
                tax = 0;
                netIncome = income;
                rebateNote = '<div class="it3-res-row"><span>Section 87A Rebate Applied</span><strong class="it3-green">Tax Free!</strong></div>';
            }
			var effRate = income > 0 ? (tax / income * 100) : 0;

            showResult(calc,
                '<div class="it3-res-row"><span>Annual Income</span><strong>' + cur + fmt(income, 0) + '</strong></div>' +
                '<div class="it3-res-row"><span>Estimated Tax</span><strong class="it3-highlight" style="color:#ef4444;">' + cur + fmt(tax, 0) + '</strong></div>' +
                '<div class="it3-res-row"><span>Net Income After Tax</span><strong class="it3-green">' + cur + fmt(netIncome, 0) + '</strong></div>' +
                '<div class="it3-res-row"><span>Effective Tax Rate</span><strong>' + effRate.toFixed(2) + '%</strong></div>' +
                rebateNote +
                '<p style="margin-top:10px;font-size:12px;color:#94a3b8;">* Estimate only. Actual tax may differ. Consult a tax advisor.</p>'
            );
        },

        /* ---------- 5. CALORIE ---------- */
        calorie: function (calc) {
            var age = num('it3-cal-age');
            var gender = val('it3-cal-gender');
            var height = num('it3-cal-height');
            var weight = num('it3-cal-weight');
            var activity = parseFloat(val('it3-cal-activity'));

            if (!age || age < 10 || age > 100) { showError(calc, 'Please enter a valid age (10-100).'); return; }
            if (!height || height < 50) { showError(calc, 'Please enter a valid height in cm.'); return; }
            if (!weight || weight < 20) { showError(calc, 'Please enter a valid weight in kg.'); return; }

            var bmr = (gender === 'male')
                ? 10 * weight + 6.25 * height - 5 * age + 5
                : 10 * weight + 6.25 * height - 5 * age - 161;

            var maintain = bmr * activity;

            showResult(calc,
                '<div class="it3-res-row"><span>BMR (Base Metabolism)</span><strong>' + Math.round(bmr) + ' cal/day</strong></div>' +
                '<div class="it3-res-row"><span>Maintain Weight</span><strong class="it3-highlight">' + Math.round(maintain) + ' cal/day</strong></div>' +
                '<div class="it3-res-row"><span>Mild Weight Loss (-0.5kg/wk)</span><strong class="it3-green">' + Math.round(maintain - 500) + ' cal/day</strong></div>' +
                '<div class="it3-res-row"><span>Weight Gain (+0.5kg/wk)</span><strong class="it3-blue">' + Math.round(maintain + 500) + ' cal/day</strong></div>'
            );
        },

        /* ---------- 6. WATER INTAKE ---------- */
        water: function (calc) {
            var weight = num('it3-wat-weight');
            var exercise = parseFloat(val('it3-wat-exercise')) || 0;
            var climate = val('it3-wat-climate');

            if (!weight || weight < 20) { showError(calc, 'Please enter a valid weight in kg.'); return; }

            var ml = weight * 35;
            ml += Math.ceil(exercise / 30) * 350;
            if (climate === 'hot') { ml *= 1.15; }

            showResult(calc,
                '<div class="it3-res-row"><span>Daily Water Needed</span><strong class="it3-highlight">' + (ml / 1000).toFixed(2) + ' liters</strong></div>' +
                '<div class="it3-res-row"><span>In Glasses (250ml each)</span><strong>' + Math.ceil(ml / 250) + ' glasses</strong></div>'
            );
        },

        /* ---------- 7. IDEAL WEIGHT ---------- */
        weight: function (calc) {
            var gender = val('it3-iw-gender');
            var heightCm = num('it3-iw-height');

            if (!heightCm || heightCm < 100 || heightCm > 250) { showError(calc, 'Please enter a valid height (100-250 cm).'); return; }

            var inchesOver5ft = Math.max(0, (heightCm / 2.54) - 60);
            var devine = (gender === 'male') ? 50 + 2.3 * inchesOver5ft : 45.5 + 2.3 * inchesOver5ft;
            var robinson = (gender === 'male') ? 52 + 1.9 * inchesOver5ft : 49 + 1.7 * inchesOver5ft;

            var hM = heightCm / 100;

            showResult(calc,
                '<div class="it3-res-row"><span>Ideal Weight (Devine)</span><strong class="it3-highlight">' + devine.toFixed(1) + ' kg</strong></div>' +
                '<div class="it3-res-row"><span>Ideal Weight (Robinson)</span><strong>' + robinson.toFixed(1) + ' kg</strong></div>' +
                '<div class="it3-res-row"><span>Healthy Range (BMI 18.5-24.9)</span><strong class="it3-green">' + (18.5 * hM * hM).toFixed(1) + ' - ' + (24.9 * hM * hM).toFixed(1) + ' kg</strong></div>'
            );
        },

        /* ---------- 8. BODY FAT (NAVY METHOD) ---------- */
        bodyfat: function (calc) {
            var gender = val('it3-bf-gender');
            var age = num('it3-bf-age');
            var height = num('it3-bf-height');
            var neck = num('it3-bf-neck');
            var waist = num('it3-bf-waist');
            var hip = num('it3-bf-hip');

            if (!age || age < 10) { showError(calc, 'Please enter a valid age.'); return; }
            if (!height || !neck || !waist) { showError(calc, 'Please fill height, neck and waist measurements.'); return; }
            if (gender === 'female' && !hip) { showError(calc, 'Hip measurement is required for women.'); return; }
            if (gender === 'male' && waist <= neck) { showError(calc, 'Waist must be larger than neck for men.'); return; }

            var bf;
            if (gender === 'male') {
                bf = 495 / (1.0324 - 0.19077 * Math.log10(waist - neck) + 0.15456 * Math.log10(height)) - 450;
            } else {
                bf = 495 / (1.29579 - 0.35004 * Math.log10(waist + hip - neck) + 0.22100 * Math.log10(height)) - 450;
            }

            bf = Math.max(0, bf);

            /* Category */
            var category;
            if (gender === 'male') {
                category = bf < 6 ? 'Essential Fat' : bf < 14 ? 'Athlete' : bf < 18 ? 'Fitness' : bf < 25 ? 'Average' : 'Obese';
            } else {
                category = bf < 14 ? 'Essential Fat' : bf < 21 ? 'Athlete' : bf < 25 ? 'Fitness' : bf < 32 ? 'Average' : 'Obese';
            }

            /* Lean mass */
            var leanPct = 100 - bf;

            showResult(calc,
                '<div class="it3-res-big">Body Fat: <span class="it3-highlight">' + bf.toFixed(1) + '%</span></div>' +
                '<div class="it3-res-row"><span>Category</span><strong class="' + (category === 'Obese' ? '' : 'it3-green') + '">' + category + '</strong></div>' +
                '<div class="it3-res-row"><span>Lean Body Mass</span><strong>' + leanPct.toFixed(1) + '%</strong></div>' +
                '<div class="it3-res-row"><span>Age</span><strong>' + age + ' years</strong></div>' +
                '<p style="margin-top:10px;font-size:12px;color:#94a3b8;">* U.S. Navy Method using circumference measurements. Accuracy &plusmn;3%.</p>'
            );
        },

        /* ---------- 9. CGPA TO PERCENTAGE ---------- */
        cgpa: function (calc) {
            var cgpa = num('it3-cg-value');
            var scale = parseFloat(val('it3-cg-scale'));

            if (isNaN(cgpa) || cgpa <= 0) { showError(calc, 'Please enter a valid CGPA.'); return; }
            if (cgpa > scale) { showError(calc, 'CGPA cannot be greater than scale (' + scale + ').'); return; }

            var percentage = (cgpa / scale) * 100;
            var cbseNote = '';

            /* CBSE official factor for 10-point scale */
            if (scale === 10) {
                var cbse = cgpa * 9.5;
                if (cbse > 100) { cbse = 100; }
                cbseNote = '<div class="it3-res-row"><span>CBSE Formula (CGPA × 9.5)</span><strong class="it3-green">' + cbse.toFixed(2) + '%</strong></div>';
            }

            showResult(calc,
                '<div class="it3-res-big">Percentage: <span class="it3-highlight">' + percentage.toFixed(2) + '%</span></div>' +
                '<div class="it3-res-row"><span>CGPA</span><strong>' + cgpa + ' / ' + scale + '</strong></div>' +
                cbseNote
            );
        },

        /* ---------- 10. GRADE CALCULATOR ---------- */
        grade: function (calc) {
            var current = num('it3-gr-current');
            var weight = num('it3-gr-weight');
            var desired = num('it3-gr-desired');

            if (isNaN(current) || current < 0) { showError(calc, 'Please enter a valid current grade.'); return; }
            if (!weight || weight <= 0 || weight > 100) { showError(calc, 'Final exam weight must be between 1 and 100.'); return; }
            if (isNaN(desired) || desired < 0) { showError(calc, 'Please enter a valid desired grade.'); return; }

            var w = weight / 100;
            var needed = (desired - current * (1 - w)) / w;

            if (needed > 100) {
                showResult(calc,
                    '<div class="it3-res-big" style="color:#ef4444;">Not Possible &#128557;</div>' +
                    '<p style="text-align:center;color:#64748b;">You would need <strong>' + needed.toFixed(1) + '%</strong> on the final — above 100%.<br>Maximum achievable: <strong>' + (current * (1 - w) + 100 * w).toFixed(1) + '%</strong></p>'
                );
            } else if (needed <= 0) {
                showResult(calc,
                    '<div class="it3-res-big" style="color:#059669;">&#127881; Already Achieved!</div>' +
                    '<p style="text-align:center;color:#64748b;">Even scoring 0% on the final keeps you at or above your goal.<br>You need only <strong>' + Math.max(0, needed).toFixed(1) + '%</strong> on the final exam.</p>'
                );
            } else {
                showResult(calc,
                    '<div class="it3-res-big">You Need: <span class="it3-highlight">' + needed.toFixed(1) + '%</span></div>' +
                    '<p style="text-align:center;color:#64748b;">On your final exam (worth ' + weight + '% of total grade)</p>'
                );
            }
        },

        /* ---------- 11. DATE DIFFERENCE ---------- */
        date: function (calc) {
            var startStr = val('it3-date-start');
            var endStr = val('it3-date-end');

            if (!startStr || !endStr) { showError(calc, 'Please select both dates.'); return; }

            var start = new Date(startStr);
            var end = new Date(endStr);
            if (end < start) { var tmp = start; start = end; end = tmp; }

            var days = Math.round((end - start) / 86400000);
            var weeks = (days / 7).toFixed(1);

            var y1 = start.getFullYear(), m1 = start.getMonth(), d1 = start.getDate();
            var y2 = end.getFullYear(), m2 = end.getMonth(), d2 = end.getDate();
            var yy = y2 - y1, mm = m2 - m1, dd = d2 - d1;

            if (dd < 0) {
                mm--;
                dd += new Date(y2, m2, 0).getDate();
            }
            if (mm < 0) { yy--; mm += 12; }

            showResult(calc,
                '<div class="it3-res-row"><span>Total Days</span><strong class="it3-highlight">' + fmt(days) + ' days</strong></div>' +
                '<div class="it3-res-row"><span>Total Weeks</span><strong>' + weeks + ' weeks</strong></div>' +
                '<div class="it3-res-row"><span>Total Hours</span><strong>' + fmt(days * 24) + ' hours</strong></div>' +
                '<div class="it3-res-row"><span>Years / Months / Days</span><strong class="it3-green">' + yy + 'y ' + mm + 'm ' + dd + 'd</strong></div>'
            );
        },

        /* ---------- 12. TIME ZONE CONVERTER ---------- */
        timezone: function (calc) {
            var dateStr = val('it3-tz-date');
            var timeStr = val('it3-tz-time');
            var fromTz = val('it3-tz-from');
            var toTz = val('it3-tz-to');

            if (!dateStr || !timeStr) { showError(calc, 'Please select both date and time.'); return; }

            try {
                var naive = new Date(dateStr + 'T' + timeStr + ':00');

                /* offset of FROM timezone applied at that instant */
                var asUTC = new Date(naive.toLocaleString('en-US', { timeZone: 'UTC' }));
                var inFromTz = new Date(naive.toLocaleString('en-US', { timeZone: fromTz }));
                var offsetMs = asUTC.getTime() - inFromTz.getTime();

                var actualInstant = new Date(naive.getTime() + offsetMs - naive.getTimezoneOffset() * 60000);

                var fmtOpts = {
                    timeZone: toTz,
                    weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
                    hour: '2-digit', minute: '2-digit', hour12: true
                };

                var fromDisplay = actualInstant.toLocaleString('en-US', Object.assign({}, fmtOpts, { timeZone: fromTz }));
                var toDisplay = actualInstant.toLocaleString('en-US', fmtOpts);

                showResult(calc,
                    '<div class="it3-res-row"><span>In ' + fromTz.split('/').pop().replace(/_/g, ' ') + '</span><strong>' + fromDisplay + '</strong></div>' +
                    '<div class="it3-res-row"><span>In ' + toTz.split('/').pop().replace(/_/g, ' ') + '</span><strong class="it3-highlight">' + toDisplay + '</strong></div>'
                );
            } catch (err) {
                showError(calc, 'Conversion failed. Please check inputs.');
            }
        },

        'timezone-now': function (calc) {
            var now = new Date();
            document.getElementById('it3-tz-date').value =
                now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
            document.getElementById('it3-tz-time').value =
                String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        },

        /* ---------- 13. DISCOUNT ---------- */
        discount: function (calc) {
            var price = num('it3-disc-price');
            var pct = num('it3-disc-percent');

            if (isNaN(price) || price <= 0) { showError(calc, 'Please enter a valid original price.'); return; }
            if (isNaN(pct) || pct < 0 || pct > 100) { showError(calc, 'Discount must be between 0 and 100.'); return; }

            var save = price * pct / 100;

            showResult(calc,
                '<div class="it3-res-row"><span>You Save</span><strong class="it3-green">' + fmt(save, 2) + '</strong></div>' +
                '<div class="it3-res-row"><span>Final Price</span><strong class="it3-highlight">' + fmt(price - save, 2) + '</strong></div>'
            );
        },

        /* ---------- 14. TIP ---------- */
        tip: function (calc) {
            var bill = num('it3-tip-bill');
            var pct = parseFloat(val('it3-tip-percent'));
            var people = parseInt(val('it3-tip-people'), 10) || 1;

            if (!bill || bill <= 0) { showError(calc, 'Please enter a valid bill amount.'); return; }

            var tip = bill * pct / 100;
            var total = bill + tip;

            showResult(calc,
                '<div class="it3-res-row"><span>Tip Amount (' + pct + '%)</span><strong>' + fmt(tip, 2) + '</strong></div>' +
                '<div class="it3-res-row"><span>Total Bill</span><strong>' + fmt(total, 2) + '</strong></div>' +
                '<div class="it3-res-row"><span>Per Person (' + people + ')</span><strong class="it3-highlight">' + fmt(total / people, 2) + '</strong></div>'
            );
        },

        /* ---------- 15. UNIT CONVERTER ---------- */
        units: function (calc) {
            var value = num('it3-unit-value');
            if (isNaN(value)) { showError(calc, 'Please enter a value to convert.'); return; }

            var cat = val('it3-unit-cat');
            var from = val('it3-unit-from');
            var to = val('it3-unit-to');
            var result;

            if (cat === 'temperature') {
                result = convertTemp(value, from, to);
            } else if (cat === 'weight') {
                result = value * (WEIGHT_UNITS[from] / WEIGHT_UNITS[to]);
            } else {
                result = value * (LENGTH_UNITS[from] / LENGTH_UNITS[to]);
            }

            var decimals = result !== 0 && Math.abs(result) < 0.01 ? 6 : (Math.abs(result) > 10000 ? 2 : 4);
            result = parseFloat(result.toFixed(decimals));

            showResult(calc, '<div class="it3-res-big">' + value + ' ' + from + ' = <br><span class="it3-highlight">' + result.toLocaleString() + ' ' + to + '</span></div>');
        },

        /* ---------- 16. RANDOM NUMBERS ---------- */
        random: function (calc) {
            var min = parseInt(val('it3-rand-min'), 10);
            var max = parseInt(val('it3-rand-max'), 10);
            var count = parseInt(val('it3-rand-count'), 10) || 1;
            var unique = document.getElementById('it3-rand-unique').checked;
            var sort = document.getElementById('it3-rand-sort').checked;

            if (isNaN(min) || isNaN(max)) { showError(calc, 'Please enter valid minimum and maximum values.'); return; }
            if (min > max) { var t = min; min = max; max = t; }
            if (count < 1 || count > 100) { showError(calc, 'Count must be between 1 and 100.'); return; }
            if (unique && (max - min + 1) < count) {
                showError(calc, 'Range too small! Only ' + (max - min + 1) + ' unique numbers possible between ' + min + ' and ' + max + '.');
                return;
            }

            var results = [];
            if (unique) {
                var pool = [];
                for (var p = min; p <= max; p++) { pool.push(p); }
                for (var i = 0; i < count; i++) {
                    var idx = secureRandomInt(pool.length);
                    results.push(pool[idx]);
                    pool.splice(idx, 1);
                }
            } else {
                for (var j = 0; j < count; j++) {
                    results.push(min + secureRandomInt(max - min + 1));
                }
            }

            if (sort) { results.sort(function (a, b) { return a - b; }); }

            var chipsHtml = '';
            for (var k = 0; k < results.length; k++) {
                chipsHtml += '<span class="it3-num-chip">' + results[k] + '</span>';
            }

            showResult(calc,
                '<div class="it3-chips-wrap">' + chipsHtml + '</div>' +
                '<div class="it3-res-row"><span>Range</span><strong>' + min + ' to ' + max + '</strong></div>'
            );
        },

        /* ---------- 17. PASSWORD GENERATOR ---------- */
        password: function (calc) {
            var length = parseInt(val('it3-pw-length'), 10) || 16;
            var useUpper = document.getElementById('it3-pw-upper').checked;
            var useLower = document.getElementById('it3-pw-lower').checked;
            var useNumbers = document.getElementById('it3-pw-numbers').checked;
            var useSymbols = document.getElementById('it3-pw-symbols').checked;

            if (!useUpper && !useLower && !useNumbers && !useSymbols) {
                showError(calc, 'Select at least one character type.');
                return;
            }

            var upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            var lower = 'abcdefghijklmnopqrstuvwxyz';
            var numbers = '0123456789';
            var symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

            var pool = '';
            var guaranteed = '';
            if (useUpper) { pool += upper; guaranteed += randomChar(upper); }
            if (useLower) { pool += lower; guaranteed += randomChar(lower); }
            if (useNumbers) { pool += numbers; guaranteed += randomChar(numbers); }
            if (useSymbols) { pool += symbols; guaranteed += randomChar(symbols); }

            var chars = guaranteed.split('');
            while (chars.length < length) {
                chars.push(randomChar(pool));
            }

            for (var j = chars.length - 1; j > 0; j--) {
                var kk = secureRandomInt(j + 1);
                var tt = chars[j]; chars[j] = chars[kk]; chars[kk] = tt;
            }
            var password = chars.slice(0, length).join('');

            var variety = [useUpper, useLower, useNumbers, useSymbols].filter(Boolean).length;
            var score = 0;
            if (length >= 12) { score++; }
            if (length >= 20) { score++; }
            if (variety >= 3) { score++; }
            if (variety === 4) { score++; }

            var strengthText = ['Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'][score];
            var strengthColor = ['#ef4444', '#ef4444', '#f59e0b', '#10b981', '#059669'][score];

            showResult(calc,
                '<div class="it3-pw-box"><code id="it3-pw-output"></code>' +
                '<button type="button" class="it3-copy-btn" data-target="it3-pw-output">&#128203; Copy</button></div>' +
                '<div class="it3-strength"><span>Strength:</span> <strong style="color:' + strengthColor + '">' + strengthText + '</strong> <span style="color:#94a3b8;">(' + length + ' chars, entropy ~' + Math.round(length * Math.log2(pool.length)) + ' bits)</span></div>'
            );

            var out = calc.querySelector('.it3-result code');
            if (out) { out.textContent = password; }
        },

        /* ---------- 18. WORD COUNTER ---------- */
        words: function (calc) {
            var text = document.getElementById('it3-wc-text').value;
            var stats = document.getElementById('it3-wc-stats');
            var trimmed = text.trim();

            var words = trimmed ? trimmed.split(/\s+/).length : 0;
            var chars = text.length;
            var noSpaces = text.replace(/\s/g, '').length;
            var sentences = trimmed ? (trimmed.match(/[.!?]+(\s|$)/g) || []).length : 0;
            var paras = trimmed ? trimmed.split(/\n\s*\n|\n/).filter(function (p) { return p.trim(); }).length : 0;
            var readTime = words > 0 ? Math.max(1, Math.ceil(words / 200)) : 0;

            document.getElementById('it3-wc-words').textContent = words.toLocaleString();
            document.getElementById('it3-wc-chars').textContent = chars.toLocaleString();
            document.getElementById('it3-wc-nospace').textContent = noSpaces.toLocaleString();
            document.getElementById('it3-wc-sentences').textContent = sentences;
            document.getElementById('it3-wc-paras').textContent = paras;
            document.getElementById('it3-wc-read').textContent = readTime;

            stats.style.display = 'grid';

            if (words === 0) {
                var err = calc.querySelector('.it3-error');
                err.textContent = 'Type or paste some text first.';
                err.style.display = 'block';
            } else {
                errHide(calc);
            }
        },
		/* 19. MARLA CONVERTER */
		marla: function(calc){
			  var amount = num('it3-marla-amount');
			  var from = val('it3-marla-from');
			  var std = parseFloat(val('it3-marla-standard'));
			  if(!amount || amount <= 0){ showError(calc, 'Please enter an amount greater than 0.'); return; }
			  var SQFT = { marla: std, kanal: 20*std, sqft: 1, sqyd: 9, sqm: 10.7639104, acre: 43560, murabba: 1089000 };
			  var base = amount * SQFT[from];
			  function cv(u){ return base / SQFT[u]; }
			  function f4(x){ return Number(x.toFixed(4)).toLocaleString('en-US',{maximumFractionDigits:4}); }
			  var html = '<table><tr><th>Unit</th><th>Value</th></tr>'
			      + '<tr><td>Marla</td><td>' + f4(cv('marla')) + '</td></tr>'
			      + '<tr><td>Kanal</td><td>' + f4(cv('kanal')) + '</td></tr>'
			      + '<tr><td>Square Feet</td><td>' + f4(cv('sqft')) + '</td></tr>'
			      + '<tr><td>Square Yards (Gaj)</td><td>' + f4(cv('sqyd')) + '</td></tr>'
			      + '<tr><td>Square Meters</td><td>' + f4(cv('sqm')) + '</td></tr>'
			      + '<tr><td>Acre</td><td>' + f4(cv('acre')) + '</td></tr>'
			      + '<tr><td>Murabba</td><td>' + f4(cv('murabba')) + '</td></tr></table>'
			      + '<p>Standard used: 1 marla = ' + std + ' sq ft. Always confirm the standard in your land record before agreeing a price.</p>';
			  showResult(calc, html);
		},
/* 20. TOLA CONVERTER */
tola: function(calc){
  var amount = num('it3-tola-amount');
  var from = val('it3-tola-from');
  if(!amount || amount <= 0){ showError(calc, 'Please enter an amount greater than 0.'); return; }
  var GRAM = { tola: 11.6638, masha: 0.9719833, ratti: 0.1214979, gram: 1, kg: 1000, ozt: 31.1034768 };
  var base = amount * GRAM[from];
  function cv(u){ return base / GRAM[u]; }
  function f4(x){ return Number(x.toFixed(4)).toLocaleString('en-US',{maximumFractionDigits:4}); }
  var html = '<table><tr><th>Unit</th><th>Value</th></tr>'
    + '<tr><td>Tola</td><td>' + f4(cv('tola')) + '</td></tr>'
    + '<tr><td>Masha</td><td>' + f4(cv('masha')) + '</td></tr>'
    + '<tr><td>Ratti</td><td>' + f4(cv('ratti')) + '</td></tr>'
    + '<tr><td>Grams</td><td>' + f4(cv('gram')) + '</td></tr>'
    + '<tr><td>Kilograms</td><td>' + f4(cv('kg')) + '</td></tr>'
    + '<tr><td>Troy Ounces</td><td>' + f4(cv('ozt')) + '</td></tr></table>'
    + '<p>Weight only: 1 tola = 11.6638 grams. Karat changes the purity and price of gold, not its weight.</p>';
  showResult(calc, html);
},
		    /* 21. SOLAR CALCULATOR */ solar: function(calc) { var usage = num('it3-solar-usage'); var sun = num('it3-solar-sun'); var panelW = num('it3-solar-panel'); var price = num('it3-solar-price'); var cost = num('it3-solar-cost'); var cur = val('it3-solar-currency'); if (!usage || usage <= 0) { showError(calc, 'Please enter your average monthly electricity use (kWh).'); return; } var req = usage / 30 / (sun * 0.8); var panels = Math.ceil(req * 1000 / panelW); var sys = panels * panelW / 1000; var gen = sys * sun * 0.8 * 30; var sep = (cur === 'Rs' || cur === 'AED') ? ' ' : ''; var m = function(v) { return cur + sep + (Number.isInteger(v) ? fmt(v) : fmt(v, 2)); }; var html = '<div class="it3-result-row"><span>Recommended system size</span><strong>' + fmt(req, 2) + ' kW</strong></div><div class="it3-result-row"><span>Panels needed</span><strong>' + panels + '</strong></div><div class="it3-result-row"><span>System size</span><strong>' + fmt(sys, 2) + ' kW</strong></div><div class="it3-result-row"><span>Estimated monthly generation</span><strong>' + fmt(gen) + ' kWh</strong></div>'; if (price > 0) { var off = Math.min(gen, usage) * price; html += '<div class="it3-result-row"><span>Estimated monthly bill offset</span><strong>' + m(off) + '</strong></div>'; } if (cost > 0) { var total = sys * 1000 * cost; html += '<div class="it3-result-row"><span>Estimated total cost</span><strong>' + m(total) + '</strong></div>'; if (price > 0) { html += '<div class="it3-result-row"><span>Simple payback period</span><strong>' + (total / (Math.min(gen, usage) * price * 12)).toFixed(1) + ' years</strong></div>'; } } showResult(calc, html); },

        /* 22. GOLD PRICE CALCULATOR */ gold: function(calc) { var w = num('it3-gold-weight'); var u = parseFloat(val('it3-gold-unit')) || 1; var r = num('it3-gold-rate'); var ru = val('it3-gold-rate-unit'); var k = parseFloat(val('it3-gold-karat')) || 1; var mk = num('it3-gold-making') || 0; var cur = val('it3-gold-currency'); if (!w || w <= 0) { showError(calc, 'Please enter a weight greater than 0.'); return; } if (!r || r <= 0) { showError(calc, 'Please enter the current gold price.'); return; } var g = w * u; var p = ru === 'gram' ? r : (ru === 'tola' ? r / 11.6638 : r / 31.1034768); var pure = g * k; var mv = pure * p; var ma = mv * mk / 100; var tot = mv + ma; var sep = (cur === 'Rs' || cur === 'AED') ? ' ' : ''; var m = function(v) { return cur + sep + fmt(v, 2); }; var html = '<div class="it3-result-row"><span>Weight in grams</span><strong>' + fmt(g, 4) + ' grams</strong></div>' + '<div class="it3-result-row"><span>Pure gold content</span><strong>' + fmt(pure, 4) + ' grams</strong></div>' + '<div class="it3-result-row"><span>Metal value</span><strong>' + m(mv) + '</strong></div>' + '<div class="it3-result-row"><span>Making charges</span><strong>' + m(ma) + '</strong></div>' + '<div class="it3-result-row"><span>Total</span><strong>' + m(tot) + '</strong></div>'; showResult(calc, html); },
		'words-clear': function (calc) {
		
            document.getElementById('it3-wc-text').value = '';
            document.getElementById('it3-wc-stats').style.display = 'none';
            errHide(calc);
        }
    };

    function errHide(calc) {
        var e = calc.querySelector('.it3-error');
        if (e) { e.style.display = 'none'; }
    }

    /* ================= TAX SLABS ================= */
    var TAX_SLABS = {
        pk: {
            currency: 'Rs ',
            slabs: [
                { upto: 600000, rate: 0 },
                { upto: 1200000, base: 0, rate: 1, over: 600000 },
                { upto: 2200000, base: 6000, rate: 11, over: 1200000 },
                { upto: 3200000, base: 116000, rate: 20, over: 2200000 },
                { upto: 4100000, base: 316000, rate: 25, over: 3200000 },
                { upto: 5600000, base: 541000, rate: 29, over: 4100000 },
				{ upto: 7000000, base: 976000, rate: 32, over: 5600000 },
				{ upto: Infinity, base: 1424000, rate: 35, over: 7000000 }
            ]
        },
        in: {
            currency: '₹',
            slabs: [
                { upto: 400000, rate: 0 },
                { upto: 800000, base: 0, rate: 5, over: 400000 },
                { upto: 1200000, base: 20000, rate: 10, over: 800000 },
                { upto: 1600000, base: 60000, rate: 15, over: 1200000 },
                { upto: 2000000, base: 120000, rate: 20, over: 1600000 },
                { upto: 2400000, base: 200000, rate: 25, over: 2000000 },
				{ upto: Infinity, base: 300000, rate: 30, over: 2400000}
            ]
        },
        us: {
            currency: '$',
            slabs: [
                { upto: 12400, rate: 10},
                { upto: 50400, base: 1240, rate: 12, over: 12400},
                { upto: 105700, base: 5800, rate: 22, over: 50400},
                { upto: 201775, base: 17966, rate: 24, over: 105700},
                { upto: 256225, base: 41024, rate: 32, over: 201775},
                { upto: 640600, base: 58448, rate: 35, over: 256225},
                { upto: Infinity, base: 192979.25, rate: 37, over: 640600}
            ]
        }
    };

    function computeProgressiveTax(income, slabs) {
        if (income <= slabs[0].upto) { return income * slabs[0].rate / 100; }
        for (var i = 1; i < slabs.length; i++) {
            if (income <= slabs[i].upto) {
                return slabs[i].base + (income - slabs[i].over) * slabs[i].rate / 100;
            }
        }
        return 0;
    }

    /* ================= UNIT DATA ================= */
    var LENGTH_UNITS = {
        'mm': 0.001, 'cm': 0.01, 'm': 1, 'km': 1000,
        'in': 0.0254, 'ft': 0.3048, 'yd': 0.9144, 'mi': 1609.344
    };
    var WEIGHT_UNITS = {
        'mg': 0.000001, 'g': 0.001, 'kg': 1, 't': 1000,
        'oz': 0.028349523125, 'lb': 0.45359237, 'st': 6.35029318
    };
    var TEMP_UNITS = ['°C', '°F', 'K'];

    function convertTemp(v, from, to) {
        var c;
        if (from === '°C') { c = v; }
        else if (from === '°F') { c = (v - 32) * 5 / 9; }
        else { c = v - 273.15; }

        if (to === '°C') { return c; }
        if (to === '°F') { return c * 9 / 5 + 32; }
        return c + 273.15;
    }

    function populateUnits(cat) {
        var fromSel = document.getElementById('it3-unit-from');
        var toSel = document.getElementById('it3-unit-to');
        var options;

        if (cat === 'temperature') { options = TEMP_UNITS; }
        else if (cat === 'weight') { options = Object.keys(WEIGHT_UNITS); }
        else { options = Object.keys(LENGTH_UNITS); }

        var html = '';
        for (var i = 0; i < options.length; i++) {
            html += '<option value="' + options[i] + '">' + options[i] + '</option>';
        }
        fromSel.innerHTML = html;
        toSel.innerHTML = html;
        toSel.selectedIndex = Math.min(1, options.length - 1);
    }

    /* ================= TIMEZONES ================= */
    function populateTimezones() {
        var fromSel = document.getElementById('it3-tz-from');
        var toSel = document.getElementById('it3-tz-to');
        if (!fromSel || !toSel) { return; }

        var zones;
        try {
            zones = Intl.supportedValuesOf ? Intl.supportedValuesOf('timeZone') : null;
        } catch (e) { zones = null; }

        if (!zones || !zones.length) {
            zones = [
                'Asia/Karachi','Asia/Kolkata','Asia/Dubai','Asia/Riyadh','Asia/Dhaka',
                'Europe/London','Europe/Paris','Europe/Berlin','Europe/Istanbul','Europe/Moscow',
                'America/New_York','America/Chicago','America/Los_Angeles','America/Toronto','America/Sao_Paulo',
                'Africa/Cairo','Africa/Johannesburg','Africa/Lagos','Asia/Tokyo','Asia/Singapore',
                'Asia/Shanghai','Asia/Hong_Kong','Australia/Sydney','Pacific/Auckland','UTC'
            ];
        }

        /* guess user's zone first */
        var userZone = 'UTC';
        try { userZone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC'; } catch (e) {}

        var html = '';
        for (var i = 0; i < zones.length; i++) {
            var label = zones[i].split('/').pop().replace(/_/g, ' ') + ' (' + zones[i] + ')';
            html += '<option value="' + zones[i] + '">' + label + '</option>';
        }
        fromSel.innerHTML = html;
        toSel.innerHTML = html;

        if (zones.indexOf(userZone) !== -1) { fromSel.value = userZone; }
        var defaultTargets = ['Asia/Karachi', 'Asia/Kolkata', 'America/New_York', 'Europe/London', 'UTC'];
        for (var d = 0; d < defaultTargets.length; d++) {
            if (zones.indexOf(defaultTargets[d]) !== -1 && defaultTargets[d] !== fromSel.value) {
                toSel.value = defaultTargets[d];
                break;
            }
        }
    }

    /* ================= HELPERS ================= */
    function secureRandomInt(maxExclusive) {
        if (window.crypto && window.crypto.getRandomValues) {
            var arr = new Uint32Array(1);
            window.crypto.getRandomValues(arr);
            return arr[0] % maxExclusive;
        }
        return Math.floor(Math.random() * maxExclusive);
    }

    function randomChar(str) {
        return str.charAt(secureRandomInt(str.length));
    }

    /* ================= WIRING ================= */
    ready(function () {
        /* Buttons */
        document.querySelectorAll('.it3-btn[data-action]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = btn.getAttribute('data-action');
                var box = btn.closest('.it3-calc');
                if (calcs[action] && box) { calcs[action](box); }
            });
        });

        /* Unit converter */
        var unitCat = document.getElementById('it3-unit-cat');
        if (unitCat) {
            unitCat.addEventListener('change', function () { populateUnits(unitCat.value); });
            populateUnits(unitCat.value);
        }

        /* Timezone selects */
        populateTimezones();
        /* Default date/time for timezone tool */
        var tzDate = document.getElementById('it3-tz-date');
        var tzTime = document.getElementById('it3-tz-time');
        if (tzDate && tzTime) {
            var now = new Date();
            tzDate.value = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
            tzTime.value = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        }

        /* Password slider */
        var pwLen = document.getElementById('it3-pw-length');
        if (pwLen) {
            pwLen.addEventListener('input', function () {
                document.getElementById('it3-pw-len-val').textContent = pwLen.value;
            });
        }

        /* Live word counter (debounced) */
        var wcText = document.getElementById('it3-wc-text');
        if (wcText) {
            var timer = null;
            wcText.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    if (wcText.value.trim()) {
                        calcs.words(wcText.closest('.it3-calc'));
                    }
                }, 300);
            });
        }

        /* Copy buttons (delegated) */
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.it3-copy-btn');
            if (!btn) { return; }
            var target = document.getElementById(btn.getAttribute('data-target'));
            if (!target) { return; }
            copyText(target.textContent, btn);
        });

        /* Enter key triggers calculate */
        document.querySelectorAll('.it3-calc input').forEach(function (input) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && input.type !== 'date' && input.type !== 'time') {
                    e.preventDefault();
                    var btn = input.closest('.it3-calc').querySelector('.it3-btn[data-action]:not(.it3-btn-light)');
                    if (btn) { btn.click(); }
                }
            });
        });
    });

    function copyText(text, btn) {
        var done = function () {
            var old = btn.innerHTML;
            btn.innerHTML = '&#9989; Copied!';
            setTimeout(function () { btn.innerHTML = old; }, 1500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(function () { fallbackCopy(text); done(); });
        } else {
            fallbackCopy(text);
            done();
        }
    }

    function fallbackCopy(text) {
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

/* 23. QR CODE GENERATOR - client-side only: the text never leaves the visitor's browser. */
(function () {
  function initQr() {
    var box = document.querySelector('.it3-calc[data-calc="qr"]');
    if (!box) { return; }
    var textEl = document.getElementById('it3-qr-text');
    var sizeEl = document.getElementById('it3-qr-size');
    var fgEl = document.getElementById('it3-qr-fg');
    var bgEl = document.getElementById('it3-qr-bg');
    var errEl = document.getElementById('it3-qr-error');
    var previewEl = document.getElementById('it3-qr-preview');
    var canvasEl = document.getElementById('it3-qr-canvas');
    var genBtn = document.getElementById('it3-qr-generate');
    var dlBtn = document.getElementById('it3-qr-download');
    if (!textEl || !canvasEl || !genBtn || !dlBtn) { return; }
    function showQrError(msg) { if (errEl) { errEl.textContent = msg; errEl.style.display = 'block'; } }
    function hideQrError() { if (errEl) { errEl.textContent = ''; errEl.style.display = 'none'; } }
    dlBtn.addEventListener('click', function () {
      var a = document.createElement('a');
      a.download = 'ilovetool3-qr-code.png';
      a.href = canvasEl.toDataURL('image/png');
      document.body.appendChild(a);
      a.click();
      a.remove();
    });
    genBtn.addEventListener('click', function (e) {
      if (e) { e.preventDefault(); }
      var text = textEl.value.trim();
      hideQrError();
      if (!text) {
        if (previewEl) { previewEl.style.display = 'none'; }
        dlBtn.style.display = 'none';
        showQrError('Please enter some text or a URL first.');
        return;
      }
      if (typeof qrcode === 'undefined') {
        showQrError('The QR library failed to load. Please check your connection and try again.');
        return;
      }
      var size = sizeEl ? parseInt(sizeEl.value, 10) || 512 : 512;
      var fg = fgEl ? fgEl.value : '#000000';
      var bg = bgEl ? bgEl.value : '#ffffff';
      var qr;
      try {
        qr = qrcode(0, 'M');
        qr.addData(text);
        qr.make();
      } catch (err) {
        if (previewEl) { previewEl.style.display = 'none'; }
        dlBtn.style.display = 'none';
        showQrError('That text is too long for one QR code. Please use a shorter text.');
        return;
      }
      var count = qr.getModuleCount();
      var quiet = 4;
      var total = count + quiet * 2;
      var cell = size / total;
      canvasEl.width = size;
      canvasEl.height = size;
      var ctx = canvasEl.getContext('2d');
      ctx.fillStyle = bg;
      ctx.fillRect(0, 0, size, size);
      ctx.fillStyle = fg;
      for (var r = 0; r < count; r++) {
        for (var c = 0; c < count; c++) {
          if (qr.isDark(r, c)) { ctx.fillRect(Math.round((c + quiet) * cell), Math.round((r + quiet) * cell), Math.ceil(cell), Math.ceil(cell)); }
        }
      }
      if (previewEl) { previewEl.style.display = 'block'; }
      dlBtn.style.display = 'inline-block';
    });
  }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initQr); } else { initQr(); }


  /* ---------- 23. IMAGE COMPRESSOR ---------- */
  function initImageCompressor() {
    var calc = document.querySelector('.it3-calc[data-calc="image-compressor"]');
    if (!calc) { return; }
    var fileInput = calc.querySelector('#it3-img-file');
    var quality = calc.querySelector('#it3-img-quality');
    var qualityValue = calc.querySelector('#it3-img-quality-value');
    var format = calc.querySelector('#it3-img-format');
    var btn = calc.querySelector('#it3-img-compress');
    var dl = calc.querySelector('#it3-img-download');
    var preview = calc.querySelector('#it3-img-preview');
	    var errBox = calc.querySelector('.it3-error');
    var res = calc.querySelector('.it3-result');
    if (!fileInput || !btn) { return; }
    function fmtSize(bytes) {
      if (bytes < 1024) { return bytes + ' B'; }
      if (bytes < 1048576) { return (bytes / 1024).toFixed(1) + ' KB'; }
      return (bytes / 1048576).toFixed(2) + ' MB';
    }
    quality.addEventListener('input', function () { qualityValue.textContent = quality.value; });
    btn.addEventListener('click', function () {
      var file = fileInput.files && fileInput.files[0];
              if (!file) { errBox.textContent = 'Please choose an image first.';  errBox.style.display = 'block'; return; }
          if (!file.type || file.type.indexOf('image/') !== 0) { errBox.textContent = 'That file is not an image. Please choose a JPG, PNG or WebP image.';  errBox.style.display = 'block'; return; }
      var reader = new FileReader();
      reader.onload = function (e) {
        var img = new Image();
        img.onload = function () {
          var canvas = document.createElement('canvas');
          canvas.width = img.naturalWidth;
          canvas.height = img.naturalHeight;
          canvas.getContext('2d').drawImage(img, 0, 0);
          var mime = format.value;
          var q = parseInt(quality.value, 10) / 100;
          canvas.toBlob(function (blob) {
                                          if (!blob) { errBox.textContent = 'Sorry, this image could not be compressed in your browser.'; errBox.style.display = 'block'; return; }
            var savedPct = file.size > 0 ? Math.round((1 - blob.size / file.size) * 100) : 0;
            var savedText;
            if (mime === 'image/png') { savedText = 'PNG is lossless \u2014 quality slider does not apply'; }
            else if (savedPct >= 0) { savedText = savedPct + '%'; }
            else { savedText = savedPct + '% (larger than the original)'; }
            calc.querySelector('#it3-img-original-size').textContent = fmtSize(file.size);
            calc.querySelector('#it3-img-compressed-size').textContent = fmtSize(blob.size);
            calc.querySelector('#it3-img-saved').textContent = savedText;
            var err = calc.querySelector('.it3-error');
            if (err) { err.style.display = 'none'; }
            res.style.display = 'block';
            var url = URL.createObjectURL(blob);
            preview.src = url;
            preview.style.display = 'block';
            var ext = mime === 'image/png' ? 'png' : (mime === 'image/webp' ? 'webp' : 'jpg');
            dl.href = url;
            dl.download = 'compressed-image.' + ext;
            dl.style.display = 'inline-block';
          }, mime, q);
        };
              img.onerror = function () { errBox.textContent = 'That file is not an image. Please choose a JPG, PNG or WebP image.';  errBox.style.display = 'block'; };
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initImageCompressor); } else { initImageCompressor(); }
})();
/* ==== 25. USERNAME GENERATOR ==== */
function initUsernameGenerator() {
  var btn = document.getElementById('it3-username-generate');
  if (!btn || btn.getAttribute('data-it3-username-init')) return;
  btn.setAttribute('data-it3-username-init', '1');
  var baseInput = document.getElementById('it3-username-base');
  var styleSel = document.getElementById('it3-username-style');
  var lenSel = document.getElementById('it3-username-length');
  var numChk = document.getElementById('it3-username-numbers');
  var resultBox = document.getElementById('it3-username-result');
  var errorBox = document.getElementById('it3-username-error');
  var words = {
    fun: ['happy', 'sunny', 'cool', 'bubbly', 'jolly', 'peppy', 'zesty', 'cheery', 'witty', 'lucky', 'bouncy', 'silly'],
    professional: ['pro', 'official', 'real', 'prime', 'elite', 'expert', 'trusted', 'smart', 'bright', 'global', 'top', 'ace'],
    gaming: ['shadow', 'ghost', 'ninja', 'storm', 'blade', 'viper', 'dragon', 'wolf', 'hawk', 'rider', 'sniper', 'legend']
  };
  var extras = ['star', 'king', 'queen', 'boss', 'hero', 'ace', 'max', 'fox', 'bear', 'lion', 'nova', 'zen'];
  function clean(s) { return (s || '').toLowerCase().replace(/[^a-z0-9]/g, ''); }
  function pick(arr) { return arr[Math.floor(Math.random() * arr.length)]; }
  function numSuffix() { var n = ''; var c = 1 + Math.floor(Math.random() * 3); for (var i = 0; i < c; i++) { n += Math.floor(Math.random() * 10); } return n; }
  btn.addEventListener('click', function () {
    if (errorBox) { errorBox.style.display = 'none'; errorBox.textContent = ''; }
    var base = clean(baseInput ? baseInput.value : '');
    var style = styleSel ? styleSel.value : 'fun';
    var maxLen = lenSel ? parseInt(lenSel.value, 10) : 12;
    if (!maxLen || maxLen < 3) { maxLen = 12; }
    var withNums = !!(numChk && numChk.checked);
    var list = words[style] || words.fun;
    var seen = {};
    var out = [];
    var tries = 0;
    while (out.length < 10 && tries < 300) {
      tries++;
      var core = '';
      var pattern = Math.floor(Math.random() * 4);
      if (base) {
        if (pattern === 0) { core = base + pick(list); }
        else if (pattern === 1) { core = pick(list) + base; }
        else if (pattern === 2) { core = base + pick(extras); }
        else { core = pick(extras) + base; }
      } else {
        if (pattern === 0) { core = pick(list) + pick(extras); }
        else if (pattern === 1) { core = pick(extras) + pick(list); }
        else if (pattern === 2) { core = pick(list) + pick(list); }
        else { core = pick(extras) + pick(list); }
      }
      var suffix = (withNums && Math.random() < 0.7) ? numSuffix() : '';
      var room = maxLen - suffix.length;
      if (room < 1) { room = 1; }
      if (core.length > room) { core = core.slice(0, room); }
      var cand = core + suffix;
      if (cand.length < 3 || seen[cand]) { continue; }
      seen[cand] = true;
      out.push(cand);
    }
    if (!out.length) {
      if (resultBox) { resultBox.style.display = 'none'; }
      if (errorBox) { errorBox.textContent = 'Sorry, no usernames could be generated. Try a shorter base word or a longer max length.'; errorBox.style.display = 'block'; }
      return;
    }
    var html = '<p><strong>Your username ideas:</strong></p>';
    for (var j = 0; j < out.length; j++) {
      html += '<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #eee;"><span>' + out[j] + '</span><button type="button" class="it3-btn it3-copy-btn" data-copy="' + out[j] + '">Copy</button></div>';
    }
    if (resultBox) { resultBox.innerHTML = html; resultBox.style.display = 'block'; }
  });
  if (resultBox) {
    resultBox.addEventListener('click', function (e) {
      var t = e.target;
      if (!t || !t.getAttribute || t.getAttribute('data-copy') === null) { return; }
      var name = t.getAttribute('data-copy');
      function done() { t.textContent = 'Copied!'; setTimeout(function () { t.textContent = 'Copy'; }, 1500); }
      function fallbackCopy(text) { var ta = document.createElement('textarea'); ta.value = text; document.body.appendChild(ta); ta.select(); try { document.execCommand('copy'); } catch (err) {} document.body.removeChild(ta); }
      if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(name).then(done, function () { fallbackCopy(name); done(); }); } else { fallbackCopy(name); done(); }
    });
  }
}
if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initUsernameGenerator); } else { initUsernameGenerator(); }

/* ==== SALARY / HOURLY WAGE CALCULATOR ==== */
function initSalaryCalculator() {
 var btn = document.getElementById('it3-salary-calculate');
 if (!btn) { return; }
 btn.addEventListener('click', function () {
 var amountEl = document.getElementById('it3-salary-amount');
 var periodEl = document.getElementById('it3-salary-period');
 var hoursEl = document.getElementById('it3-salary-hours');
 var weeksEl = document.getElementById('it3-salary-weeks');
 var currencyEl = document.getElementById('it3-salary-currency');
 var errorBox = document.getElementById('it3-salary-error');
 var resultBox = document.getElementById('it3-salary-result');
 function showErr(msg) {
 if (errorBox) { errorBox.textContent = msg; errorBox.style.display = 'block'; }
 if (resultBox) { resultBox.style.display = 'none'; }
 }
 var amount = parseFloat(amountEl.value);
 if (isNaN(amount)) { showErr('Please enter your pay amount as a number.'); return; }
 var hours = parseFloat(hoursEl.value);
 var weeks = parseFloat(weeksEl.value);
 if (isNaN(hours) || hours <= 0) { showErr('Please enter your hours per week as a number greater than zero.'); return; }
 if (isNaN(weeks) || weeks <= 0) { showErr('Please enter your weeks per year as a number greater than zero.'); return; }
 var period = periodEl.value;
 var hourly = 0;
 if (period === 'hourly') { hourly = amount; }
 else if (period === 'daily') { hourly = amount * 5 / hours; }
 else if (period === 'weekly') { hourly = amount / hours; }
 else if (period === 'monthly') { hourly = amount * 12 / (hours * weeks); }
 else if (period === 'yearly') { hourly = amount / (hours * weeks); }
 var yearly = hourly * hours * weeks;
 var weekly = yearly / weeks;
 var monthly = yearly / 12;
 var daily = weekly / 5;
 var symbols = { USD: '$', PKR: 'Rs ', GBP: '\u00a3', EUR: '\u20ac', INR: '\u20b9', AED: 'AED ' };
 var sym = symbols[currencyEl.value] || '$';
 function money(v) { return sym + v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
 if (errorBox) { errorBox.style.display = 'none'; }
 var hEl = document.getElementById('it3-salary-hourly');
 var dEl = document.getElementById('it3-salary-daily');
 var wEl = document.getElementById('it3-salary-weekly');
 var mEl = document.getElementById('it3-salary-monthly');
 var yEl = document.getElementById('it3-salary-yearly');
 if (hEl) { hEl.textContent = money(hourly); }
 if (dEl) { dEl.textContent = money(daily); }
 if (wEl) { wEl.textContent = money(weekly); }
 if (mEl) { mEl.textContent = money(monthly); }
 if (yEl) { yEl.textContent = money(yearly); }
 if (resultBox) { resultBox.style.display = 'block'; }
 });
}
if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initSalaryCalculator); } else { initSalaryCalculator(); }



/* ==== 27. VAT / GST CALCULATOR ==== */
function initVatGstCalculator() {
    var btn = document.getElementById('it3-vat-calculate');
    if (!btn) { return; }
    var presetEl = document.getElementById('it3-vat-preset');
    var rateEl = document.getElementById('it3-vat-rate');
    if (presetEl && rateEl) {
        presetEl.addEventListener('change', function () {
            if (presetEl.value !== 'custom') { rateEl.value = presetEl.value; }
        });
    }
    btn.addEventListener('click', function () {
        var amountEl = document.getElementById('it3-vat-amount');
        var modeEl = document.getElementById('it3-vat-mode');
        var currencyEl = document.getElementById('it3-vat-currency');
        var errorBox = document.getElementById('it3-vat-error');
        var resultBox = document.getElementById('it3-vat-result');
        function showErr(msg) {
            if (errorBox) { errorBox.textContent = msg; errorBox.style.display = 'block'; }
            if (resultBox) { resultBox.style.display = 'none'; }
        }
        var amount = parseFloat(amountEl.value);
        if (isNaN(amount)) { showErr('Please enter an amount as a number.'); return; }
        var rate = parseFloat(rateEl.value);
        if (isNaN(rate) || rate < 0) { showErr('Please enter a tax rate as a number.'); return; }
        var net, tax, gross;
        if (modeEl.value === 'remove') {
            gross = amount;
            net = gross / (1 + rate / 100);
            tax = gross - net;
        } else {
            net = amount;
            gross = net * (1 + rate / 100);
            tax = gross - net;
        }
        var symbols = { USD: '$', PKR: 'Rs ', GBP: '\u00a3', EUR: '\u20ac', INR: '\u20b9', AED: 'AED ' };
        var sym = symbols[currencyEl.value] || '$';
        function money(v) { return sym + v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        if (errorBox) { errorBox.style.display = 'none'; }
        var netEl = document.getElementById('it3-vat-net');
        var taxEl = document.getElementById('it3-vat-tax');
        var grossEl = document.getElementById('it3-vat-gross');
        if (netEl) { netEl.textContent = money(net); }
        if (taxEl) { taxEl.textContent = money(tax); }
        if (grossEl) { grossEl.textContent = money(gross); }
        if (resultBox) { resultBox.style.display = 'block'; }
    });
}
if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initVatGstCalculator); } else { initVatGstCalculator(); }






















/* ==== 28. TIME CARD / WORK HOURS CALCULATOR ==== */function initTimeCardCalculator() {  var btn = document.getElementById('it3-timecard-calc');  if (!btn) { return; }  var clearBtn = document.getElementById('it3-timecard-clear');  var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];  var labels = { monday: 'Monday', tuesday: 'Tuesday', wednesday: 'Wednesday', thursday: 'Thursday', friday: 'Friday', saturday: 'Saturday', sunday: 'Sunday' };  function toMinutes(v) { var p = v.split(':'); return parseInt(p[0], 10) * 60 + parseInt(p[1], 10); }  btn.addEventListener('click', function() {    var rateEl = document.getElementById('it3-timecard-rate');    var thresholdEl = document.getElementById('it3-timecard-threshold');    var multEl = document.getElementById('it3-timecard-multiplier');    var currencyEl = document.getElementById('it3-timecard-currency');    var errorBox = document.getElementById('it3-timecard-error');    var resultBox = document.getElementById('it3-timecard-result');    function showErr(msg) {      if (errorBox) { errorBox.textContent = msg; errorBox.style.display = 'block'; }      if (resultBox) { resultBox.style.display = 'none'; }    }    var rate = parseFloat(rateEl.value);    if (isNaN(rate) || rate <= 0) { showErr('Please enter your hourly rate as a number greater than 0.'); return; }    var threshold = parseFloat(thresholdEl.value);    if (isNaN(threshold) || threshold < 0) { showErr('Please enter the overtime threshold in hours per week as a number of 0 or more.'); return; }    var mult = parseFloat(multEl.value);    if (isNaN(mult) || mult < 1) { showErr('Please enter the overtime multiplier as a number of 1 or more.'); return; }
 var total = 0; var dayHours = {}; for (var i = 0; i < days.length; i++) { var day = days[i]; var startEl = document.getElementById('it3-timecard-' + day + '-start'); var endEl = document.getElementById('it3-timecard-' + day + '-end'); var breakEl = document.getElementById('it3-timecard-' + day + '-break'); if (!startEl.value && !endEl.value) { dayHours[day] = 0; continue; } if (!startEl.value || !endEl.value) { showErr('Please enter both a start time and an end time for ' + labels[day] + ', or leave both blank.'); return; } var s = toMinutes(startEl.value); var e = toMinutes(endEl.value); if (e < s) { showErr('The end time for ' + labels[day] + ' is earlier than its start time. Please enter an end time after the start time.'); return; } var brk = parseFloat(breakEl.value); if (isNaN(brk) || brk < 0) { brk = 0; } var h = (e - s) / 60 - brk / 60; if (h < 0) { h = 0; } dayHours[day] = h; total += h; } var regular = Math.min(total, threshold); var overtime = Math.max(0, total - threshold); var regularPay = regular * rate; var overtimePay = overtime * rate * mult; var gross = regularPay + overtimePay; var symbols = { USD: '$', PKR: 'Rs ', GBP: '£', EUR: '€', INR: '₹', AED: 'AED ' }; var sym = symbols[currencyEl.value] || '$'; function money(v) { return sym + v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } function hrs(v) { return v.toFixed(2); } if (errorBox) { errorBox.style.display = 'none'; } for (var j = 0; j < days.length; j++) { var dEl = document.getElementById('it3-timecard-' + days[j] + '-hours'); if (dEl) { dEl.textContent = hrs(dayHours[days[j]]); } } var totalEl = document.getElementById('it3-timecard-total'); if (totalEl) { totalEl.textContent = hrs(total); } var regEl = document.getElementById('it3-timecard-regular'); if (regEl) { regEl.textContent = hrs(regular); } var otEl = document.getElementById('it3-timecard-overtime'); if (otEl) { otEl.textContent = hrs(overtime); } var regPayEl = document.getElementById('it3-timecard-regular-pay'); if (regPayEl) { regPayEl.textContent = money(regularPay); } var otPayEl = document.getElementById('it3-timecard-overtime-pay'); if (otPayEl) { otPayEl.textContent = money(overtimePay); } var grossEl = document.getElementById('it3-timecard-gross'); if (grossEl) { grossEl.textContent = money(gross); } if (resultBox) { resultBox.style.display = 'block'; } }); if (clearBtn) { clearBtn.addEventListener('click', function() { var inputs = document.querySelectorAll('[data-timecard-input]'); for (var k = 0; k < inputs.length; k++) { inputs[k].value = inputs[k].getAttribute('data-default') || ''; } var thEl = document.getElementById('it3-timecard-threshold'); if (thEl) { thEl.value = '40'; } var muEl = document.getElementById('it3-timecard-multiplier'); if (muEl) { muEl.value = '1.5'; } var err2 = document.getElementById('it3-timecard-error'); if (err2) { err2.style.display = 'none'; } var res2 = document.getElementById('it3-timecard-result'); if (res2) { res2.style.display = 'none'; } }); } } if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initTimeCardCalculator); } else { initTimeCardCalculator(); }

/* ==== 29. FREELANCE HOURLY RATE CALCULATOR ==== */
function initFreelanceRateCalculator() { var btn=document.getElementById('it3-freelance-calculate'); if(!btn) {return; } var presetEl=document.getElementById('it3-freelance-fee-preset'); var customWrap=document.getElementById('it3-freelance-custom-wrap'); var customEl=document.getElementById('it3-freelance-fee-custom'); if(presetEl){presetEl.addEventListener('change',function(){if(customWrap){customWrap.style.display=(presetEl.value==='custom')?'block':'none';}});} btn.addEventListener('click',function(){var targetEl=document.getElementById('it3-freelance-target');var expensesEl=document.getElementById('it3-freelance-expenses');var hoursEl=document.getElementById('it3-freelance-hours');var currencyEl=document.getElementById('it3-freelance-currency');var errorBox=document.getElementById('it3-freelance-error');var resultBox=document.getElementById('it3-freelance-result');function showErr(msg){if(errorBox){errorBox.textContent=msg;errorBox.style.display='block';}if(resultBox){resultBox.style.display='none';}}var target=parseFloat(targetEl.value);if(isNaN(target)){showErr('Please enter your target monthly take-home pay as a number.');return;}var expenses=parseFloat(expensesEl.value);if(isNaN(expenses)){expenses=0;}var hours=parseFloat(hoursEl.value);if(isNaN(hours)){showErr('Please enter your billable hours per month as a number.');return;}if(hours===0){showErr('Billable hours per month cannot be 0. Please enter a number greater than 0.');return;}var fee=0;if(presetEl&&presetEl.value==='custom'){fee=parseFloat(customEl.value);if(isNaN(fee)){showErr('Please enter your custom platform fee as a number.');return;}}else if(presetEl){fee=parseFloat(presetEl.value);}if(isNaN(fee)||fee<0||fee>=100){showErr('Please enter a platform fee between 0 and 99.');return;}var gross=(target+expenses)/(1-fee/100);var hourly=gross/hours;var day=hourly*8;var symbols={USD:'$',PKR:'Rs ',GBP:'\u00a3',EUR:'\u20ac',INR:'\u20b9',AED:'AED '};var sym=symbols[currencyEl.value]||'$';function money(v){return sym+v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}if(errorBox){errorBox.style.display='none';}var grossEl=document.getElementById('it3-freelance-gross');var hourlyEl=document.getElementById('it3-freelance-hourly');var dayEl=document.getElementById('it3-freelance-day');if(grossEl){grossEl.textContent=money(gross);}if(hourlyEl){hourlyEl.textContent=money(hourly);}if(dayEl){dayEl.textContent=money(day);}if(resultBox){resultBox.style.display='block';}})}
if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initFreelanceRateCalculator);}else{initFreelanceRateCalculator();}
/* ==== 30. STOCK AVERAGE CALCULATOR ==== */
function initIt3StockAverageCalculator() {
    var btn = document.getElementById('it3-stock-calculate');
    if (!btn) { return; }
    btn.addEventListener('click', function() {
        var errorBox = document.getElementById('it3-stock-error');
        var resultBox = document.getElementById('it3-stock-result');
        var currencyEl = document.getElementById('it3-stock-currency');
        function showErr(msg) {
            if (errorBox) { errorBox.textContent = msg; errorBox.style.display = 'block'; }
            if (resultBox) { resultBox.style.display = 'none'; }
        }
        function val(id) { var el = document.getElementById(id); return el ? parseFloat(el.value) : NaN; }
        var symbols = { USD: '$', PKR: 'Rs ', GBP: '\u00a3', EUR: '\u20ac', INR: '\u20b9', AED: 'AED ' };
        var sym = symbols[currencyEl ? currencyEl.value : 'USD'] || '$';
        function money(v) { return sym + v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        function numFmt(v) { return v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        var totalShares = 0;
        var totalInvested = 0;
        var filled = 0;
        for (var i = 1; i <= 5; i++) {
            var sh = val('it3-stock-shares-' + i);
            var pr = val('it3-stock-price-' + i);
            var hasSh = !isNaN(sh) && sh > 0;
            var hasPr = !isNaN(pr) && pr >= 0;
            if (hasSh && hasPr) { totalShares += sh; totalInvested += sh * pr; filled++; }
            else if (hasSh || hasPr) { showErr('Please enter both shares and a price per share for Buy ' + i + ', or leave that row blank.'); return; }
        }
        if (filled === 0 || totalShares <= 0) { showErr('Please enter at least one buy: add shares and a price per share for Buy 1 (or another buy row).'); return; }
        var fees = val('it3-stock-fees');
        if (isNaN(fees) || fees < 0) { fees = 0; }
        totalInvested += fees;
        var current = val('it3-stock-current');
        if (isNaN(current) || current <= 0) { showErr('Please enter the current price per share.'); return; }
        var avg = totalInvested / totalShares;
        var currentValue = totalShares * current;
        var gain = currentValue - totalInvested;
        var pct = (gain / totalInvested) * 100;
        var pctText = (pct >= 0 ? '+' : '') + pct.toFixed(2) + '%';
        if (errorBox) { errorBox.style.display = 'none'; }
        var setText = function(id, text) { var el = document.getElementById(id); if (el) { el.textContent = text; } };
        setText('it3-stock-total-shares', numFmt(totalShares));
        setText('it3-stock-total-invested', money(totalInvested));
        setText('it3-stock-average', money(avg));
        setText('it3-stock-current-value', money(currentValue));
        setText('it3-stock-gain', money(gain) + ' (' + pctText + ')');
        setText('it3-stock-breakeven', money(avg));
        var targetRow = document.getElementById('it3-stock-target-row');
        var targetNote = document.getElementById('it3-stock-target-note');
        var target = val('it3-stock-target');
        if (targetRow) { targetRow.style.display = 'none'; }
        if (targetNote) { targetNote.style.display = 'none'; targetNote.textContent = ''; }
        if (!isNaN(target) && target > 0) {
            var toBuy = (target * totalShares - totalInvested) / (current - target);
            if (isFinite(toBuy) && toBuy > 0) {
                setText('it3-stock-target-shares', numFmt(toBuy));
                if (targetRow) { targetRow.style.display = 'block'; }
            } else if (targetNote) {
                var note = 'You cannot reach a target average of ' + money(target) + ' by buying more shares at the current price of ' + money(current) + '.';
                if (current > target) { note += ' The current price is above your target, so buying more shares at that price cannot bring your average down to your target.'; }
                if (current < target) { note += ' The current price is below your target, so buying more shares at that price would lower your average, not raise it to your target.'; }
                targetNote.textContent = note;
                targetNote.style.display = 'block';
            }
        }
        if (resultBox) { resultBox.style.display = 'block'; }
    });
}
if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initIt3StockAverageCalculator); } else { initIt3StockAverageCalculator(); }

function initIt3PasswordStrengthChecker(){var input=document.getElementById('it3-password-input');if(!input)return;var toggle=document.getElementById('it3-password-toggle');var labelEl=document.getElementById('it3-password-label');var fill=document.getElementById('it3-password-meter-fill');var entropyEl=document.getElementById('it3-password-entropy');var crackEl=document.getElementById('it3-password-crack');var common=['123456','password','123456789','qwerty','111111','12345678','abc123','password1','12345','123123'];var labels={length:'At least 12 characters',lowerupper:'Lowercase and uppercase letters',number:'A number',symbol:'A symbol',repeat:'No repeated characters in a row',common:'Not a common password'};var checks={};Object.keys(labels).forEach(function(k){checks[k]=document.getElementById('it3-password-check-'+k);});function setCheck(k,ok){if(checks[k])checks[k].textContent=(ok?'✓ ':'✗ ')+labels[k];}function u(n,w){return Math.round(n)+' '+w+(Math.round(n)===1?'':'s');}function crackTime(e){if(e<=0)return'—';var s=Math.pow(2,e)/10000000000;if(s<1)return'instantly';if(s<60)return u(s,'second');var m=s/60;if(m<60)return u(m,'minute');var h=m/60;if(h<24)return u(h,'hour');var d=h/24;if(d<30)return u(d,'day');var mo=d/30;if(mo<12)return u(mo,'month');var y=d/365;if(y<1000)return u(y,'year');if(y<1000000)return(Math.round(y/100)*100).toLocaleString('en-US')+' years';return'centuries';}function update(){var p=input.value;if(!p){labelEl.textContent='—';fill.style.width='0%';entropyEl.textContent='0 bits';crackEl.textContent='—';Object.keys(labels).forEach(function(k){setCheck(k,false);});return;}var pool=0;if(/[a-z]/.test(p))pool+=26;if(/[A-Z]/.test(p))pool+=26;if(/[0-9]/.test(p))pool+=10;if(/[^a-zA-Z0-9]/.test(p))pool+=32;var entropy=p.length*Math.log2(pool);var isCommon=common.indexOf(p.toLowerCase())!==-1;var label,width,color;if(isCommon||entropy<28){label='Very Weak';width='20%';color='#e53935';}else if(entropy<36){label='Weak';width='40%';color='#fb8c00';}else if(entropy<50){label='Fair';width='60%';color='#fdd835';}else if(entropy<60){label='Good';width='80%';color='#7cb342';}else{label='Strong';width='100%';color='#43a047';}labelEl.textContent=label;fill.style.width=width;fill.style.background=color;entropyEl.textContent=entropy.toFixed(1)+' bits';crackEl.textContent=isCommon?'instantly':crackTime(entropy);setCheck('length',p.length>=12);setCheck('lowerupper',/[a-z]/.test(p)&&/[A-Z]/.test(p));setCheck('number',/[0-9]/.test(p));setCheck('symbol',/[^a-zA-Z0-9]/.test(p));setCheck('repeat',!/(.)\1/.test(p));setCheck('common',!isCommon);}input.addEventListener('input',update);if(toggle){toggle.addEventListener('click',function(){var show=input.type==='password';input.type=show?'text':'password';toggle.textContent=show?'Hide':'Show';});}update();}
if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initIt3PasswordStrengthChecker);}else{initIt3PasswordStrengthChecker();}

/* ================= IBAN VALIDATOR ================= */
function initIt3IbanValidator(){
var input=document.getElementById('it3-iban-input');
if(!input){return;}
var btn=document.getElementById('it3-iban-validate');
if(!btn){return;}
var lengths={PK:24,GB:22,DE:22,FR:27,ES:24,IT:27,NL:18,CH:21,AE:23,SA:24,TR:26,QA:29,KW:30,BH:22,EG:29,JO:30,IQ:23,TN:24,MA:24,DZ:24,MR:27,LB:28,PS:29,NO:15,SE:24,DK:18,FI:18,AT:20,BE:16,IE:22,PT:25,GR:27,LU:20,CY:28,MT:31,CZ:24,SK:24,HU:28,PL:28,RO:24,BG:22,HR:21,SI:19,LT:20,LV:21,EE:20,IS:26};
var names={PK:'Pakistan',GB:'United Kingdom',DE:'Germany',FR:'France',ES:'Spain',IT:'Italy',NL:'Netherlands',CH:'Switzerland',AE:'United Arab Emirates',SA:'Saudi Arabia',TR:'Turkey',QA:'Qatar',KW:'Kuwait',BH:'Bahrain',EG:'Egypt',JO:'Jordan',IQ:'Iraq',TN:'Tunisia',MA:'Morocco',DZ:'Algeria',MR:'Mauritania',LB:'Lebanon',PS:'Palestine',NO:'Norway',SE:'Sweden',DK:'Denmark',FI:'Finland',AT:'Austria',BE:'Belgium',IE:'Ireland',PT:'Portugal',GR:'Greece',LU:'Luxembourg',CY:'Cyprus',MT:'Malta',CZ:'Czech Republic',SK:'Slovakia',HU:'Hungary',PL:'Poland',RO:'Romania',BG:'Bulgaria',HR:'Croatia',SI:'Slovenia',LT:'Lithuania',LV:'Latvia',EE:'Estonia',IS:'Iceland'};
function mod97(s){var r=0;for(var i=0;i<s.length;i++){var c=s.charCodeAt(i);if(c>=48&&c<=57){r=(r*10+(c-48))%97;}else if(c>=65&&c<=90){var v=c-55;r=(r*10+Math.floor(v/10))%97;r=(r*10+(v%10))%97;}}return r;}
function setText(id,t){var el=document.getElementById(id);if(el){el.textContent=t;}}
function validate(){
var raw=input.value||'';
var iban=raw.replace(/\s+/g,'').toUpperCase();
var result=document.getElementById('it3-iban-result');
var err=document.getElementById('it3-iban-error');
var reason=document.getElementById('it3-iban-reason');
if(err){err.style.display='none';err.textContent='';}
if(iban.length<4){if(result){result.style.display='none';}if(err){err.textContent='Please enter an IBAN.';err.style.display='block';}return;}
var code=iban.slice(0,2);
var bban=iban.slice(4);
var expected=lengths[code];
var country=names[code]||'';
var remainder=mod97(bban+iban.slice(0,4));
setText('it3-iban-code',code);
setText('it3-iban-country',country||'Not recognised');
setText('it3-iban-expected',expected?String(expected):'Unknown - this country code is not recognised, so the length cannot be checked.');
setText('it3-iban-entered',String(iban.length));
setText('it3-iban-bban',bban||'-');
setText('it3-iban-mod97',String(remainder));
var verdict=document.getElementById('it3-iban-verdict');
var reasons=[];
if(!/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/.test(iban)){reasons.push('The IBAN must start with a 2-letter country code and 2 check digits, followed by letters and digits only.');}
if(!expected){reasons.push('The country code "'+code+'" is not recognised, so the tool cannot check its length or validate this IBAN.');}
else if(iban.length!==expected){reasons.push('The IBAN is '+iban.length+' characters long, but IBANs from '+(country||code)+' should be '+expected+' characters long.');}
if(remainder!==1){reasons.push('The MOD-97 check gives '+remainder+' instead of 1, so the check digits do not match the rest of the IBAN.');}
var valid=reasons.length===0;
if(verdict){verdict.textContent=valid?'Valid':'Invalid';verdict.style.color=valid?'#43a047':'#e53935';}
if(reason){reason.textContent=valid?'This IBAN is well-formed: the country, length, structure and MOD-97 check digit are all correct.':reasons.join(' ');}
if(result){result.style.display='block';}
}
btn.addEventListener('click',validate);
input.addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();validate();}});
}
if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initIt3IbanValidator);}else{initIt3IbanValidator();}

/* ================= INVOICE GENERATOR ================= */
function initIt3InvoiceGenerator(){
var root=document.querySelector('[data-calc="invoice-generator"]');
if(!root){return;}
var symbols={USD:'$',PKR:'Rs ',GBP:'\u00a3',EUR:'\u20ac',INR:'\u20b9',AED:'AED '};
function fmt(n,sym){return sym+Number(n).toFixed(2);}
function sym(){var c=document.getElementById('it3-invoice-currency');return symbols[c?c.value:'USD']||'$';}
function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function showError(msg){var e=document.getElementById('it3-invoice-error');if(e){e.textContent=msg;e.style.display=msg?'block':'none';}}
function calc(){
var s=sym();var subtotal=0;var filled=0;var bad=false;
for(var i=1;i<=5;i++){
var qEl=document.getElementById('it3-invoice-qty-'+i);
var pEl=document.getElementById('it3-invoice-price-'+i);
var lEl=document.getElementById('it3-invoice-line-'+i);
var q=(qEl&&qEl.value!=='')?parseFloat(qEl.value):NaN;
var p=(pEl&&pEl.value!=='')?parseFloat(pEl.value):NaN;
var line=0;
if((qEl&&qEl.value!=='')||(pEl&&pEl.value!=='')){
if(isNaN(q)||isNaN(p)){bad=true;}
else{line=q*p;subtotal+=line;filled++;}
}
if(lEl){lEl.textContent=((qEl&&qEl.value!=='')&&(pEl&&pEl.value!=='')&&!isNaN(q)&&!isNaN(p))?fmt(line,s):'';}
}
return {subtotal:subtotal,filled:filled,bad:bad};
}
function generate(){
var r=calc();
if(r.bad){showError('Please enter numbers only for quantity and unit price.');return;}
if(r.filled===0){showError('Please add at least one line item with a description, quantity and unit price.');return;}
showError('');
var s=sym();
var discEl=document.getElementById('it3-invoice-discount');
var taxEl=document.getElementById('it3-invoice-tax');
var discPct=(discEl&&discEl.value!=='')?parseFloat(discEl.value):0;
var taxPct=(taxEl&&taxEl.value!=='')?parseFloat(taxEl.value):0;
if(isNaN(discPct)||isNaN(taxPct)){showError('Please enter numbers only for discount and tax rate.');return;}
var discAmt=r.subtotal*discPct/100;
var taxable=r.subtotal-discAmt;
var taxAmt=taxable*taxPct/100;
var total=taxable+taxAmt;
var g=function(id){var el=document.getElementById(id);return el?el.value:'';};
var set=function(id,t){var el=document.getElementById(id);if(el){el.textContent=t;}};
set('it3-invoice-p-business',g('it3-invoice-business')||'-');
set('it3-invoice-p-business-info',g('it3-invoice-business-info'));
set('it3-invoice-p-client',g('it3-invoice-client')||'-');
set('it3-invoice-p-client-info',g('it3-invoice-client-info'));
set('it3-invoice-p-number',g('it3-invoice-number')||'INV-001');
set('it3-invoice-p-date',g('it3-invoice-date')||'-');
set('it3-invoice-p-due',g('it3-invoice-due')||'-');
var tbody=document.getElementById('it3-invoice-p-items');
if(tbody){tbody.innerHTML='';
for(var i=1;i<=5;i++){
var d=g('it3-invoice-desc-'+i);
var qEl=document.getElementById('it3-invoice-qty-'+i);
var pEl=document.getElementById('it3-invoice-price-'+i);
if(!d&&(!qEl||qEl.value==='')){continue;}
var q=parseFloat(qEl.value)||0;
var p=parseFloat(pEl.value)||0;
var tr=document.createElement('tr');
tr.innerHTML='<td>'+esc(d||'-')+'</td><td>'+q+'</td><td>'+fmt(p,s)+'</td><td>'+fmt(q*p,s)+'</td>';
tbody.appendChild(tr);
}
}
set('it3-invoice-p-subtotal',fmt(r.subtotal,s));
set('it3-invoice-p-discount',fmt(discAmt,s));
set('it3-invoice-p-taxable',fmt(taxable,s));
set('it3-invoice-p-tax',fmt(taxAmt,s));
set('it3-invoice-p-total',fmt(total,s));
var prev=document.getElementById('it3-invoice-preview');
if(prev){prev.style.display='block';}
}
for(var i=1;i<=5;i++){
['it3-invoice-qty-','it3-invoice-price-','it3-invoice-desc-'].forEach(function(prefix){var el=document.getElementById(prefix+i);if(el){el.addEventListener('input',calc);}});
}
var gen=document.getElementById('it3-invoice-generate');
if(gen){gen.addEventListener('click',generate);}
var pr=document.getElementById('it3-invoice-print');
if(pr){pr.addEventListener('click',function(){generate();var prev=document.getElementById('it3-invoice-preview');if(prev&&prev.style.display!=='none'){window.print();}});}
var dateEl=document.getElementById('it3-invoice-date');
if(dateEl&&!dateEl.value){dateEl.value=new Date().toISOString().slice(0,10);}
}
if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initIt3InvoiceGenerator);}else{initIt3InvoiceGenerator();}

/* ================= IMAGE RESIZER ================= */
function initIt3ImageResizer(){
  var root=document.querySelector('[data-calc="image-resizer"]');
  if(!root){return;}
  var fileInput=root.querySelector('#it3-rs-file');
  var preset=root.querySelector('#it3-rs-preset');
  var widthInput=root.querySelector('#it3-rs-width');
  var heightInput=root.querySelector('#it3-rs-height');
  var ratioBox=root.querySelector('#it3-rs-ratio');
  var formatSel=root.querySelector('#it3-rs-format');
  var quality=root.querySelector('#it3-rs-quality');
  var qualityValue=root.querySelector('#it3-rs-quality-value');
  var targetInput=root.querySelector('#it3-rs-target');
  var resizeBtn=root.querySelector('#it3-rs-resize');
  var errorBox=root.querySelector('#it3-rs-error');
  var originalBox=root.querySelector('#it3-rs-original');
  var outputBox=root.querySelector('#it3-rs-output');
  var resultBox=root.querySelector('#it3-rs-result');
  var preview=root.querySelector('#it3-rs-preview');
  var downloadLink=root.querySelector('#it3-rs-download');
  if(!fileInput||!resizeBtn){return;}
  var origW=0,origH=0,origSize=0;
  function fmtSize(bytes){
    if(bytes<1024){return bytes+' B';}
    if(bytes<1048576){return (bytes/1024).toFixed(1)+' KB';}
    return (bytes/1048576).toFixed(2)+' MB';
  }
  function showError(msg){errorBox.textContent=msg;errorBox.style.display='block';}
  function hideError(){errorBox.style.display='none';errorBox.textContent='';}
  function extFor(mime){return mime==='image/png'?'png':(mime==='image/webp'?'webp':'jpg');}
  quality.addEventListener('input',function(){qualityValue.textContent=quality.value;});
  fileInput.addEventListener('change',function(){
    hideError();
    var file=fileInput.files&&fileInput.files[0];
    if(!file){return;}
    if(!file.type||file.type.indexOf('image/')!==0){showError('That file is not an image. Please choose a JPG, PNG or WebP image.');fileInput.value='';return;}
    var reader=new FileReader();
    reader.onload=function(e){
      var img=new Image();
      img.onload=function(){
        origW=img.naturalWidth;origH=img.naturalHeight;origSize=file.size;
        originalBox.textContent='Original: '+origW+' x '+origH+' px - '+fmtSize(origSize)+'.';
        originalBox.style.display='block';
        if(!widthInput.value){widthInput.value=origW;}
        if(!heightInput.value){heightInput.value=origH;}
      };
      img.onerror=function(){showError('That file is not an image. Please choose a JPG, PNG or WebP image.');};
      img.src=e.target.result;
    };
    reader.readAsDataURL(file);
  });
  preset.addEventListener('change',function(){
    var v=preset.value;
    if(v==='custom'){return;}
    var parts=v.split('x');
    widthInput.value=parts[0];
    heightInput.value=parts[1];
    ratioBox.checked=false;
  });
  widthInput.addEventListener('input',function(){
    if(ratioBox.checked&&origW>0&&origH>0){
      var w=parseInt(widthInput.value,10);
      if(w>0){heightInput.value=Math.max(1,Math.round(w*origH/origW));}
    }
  });
  heightInput.addEventListener('input',function(){
    if(ratioBox.checked&&origW>0&&origH>0){
      var h=parseInt(heightInput.value,10);
      if(h>0){widthInput.value=Math.max(1,Math.round(h*origW/origH));}
    }
  });
  formatSel.addEventListener('change',function(){quality.disabled=(formatSel.value==='image/png');});
  resizeBtn.addEventListener('click',function(){
    hideError();
    var file=fileInput.files&&fileInput.files[0];
    if(!file){showError('Please choose an image first.');return;}
    if(!file.type||file.type.indexOf('image/')!==0){showError('That file is not an image. Please choose a JPG, PNG or WebP image.');return;}
    var w=parseInt(widthInput.value,10);
    var h=parseInt(heightInput.value,10);
    if(!w||!h||w<1||h<1){showError('Please enter a width and a height in pixels (both must be 1 or more).');return;}
    var reader=new FileReader();
    reader.onload=function(e){
      var img=new Image();
      img.onload=function(){
        var canvas=document.createElement('canvas');
        canvas.width=w;canvas.height=h;
        canvas.getContext('2d').drawImage(img,0,0,w,h);
        var mime=formatSel.value;
        var q=parseInt(quality.value,10)/100;
        var targetKb=parseInt(targetInput.value,10);
        function finish(blob,finalQ){
          if(!blob){showError('Sorry, this image could not be resized in your browser.');return;}
          var url=URL.createObjectURL(blob);
          preview.src=url;
          preview.style.display='block';
          downloadLink.href=url;
          downloadLink.download='ilovetool3-resized-image.'+extFor(mime);
          downloadLink.style.display='inline-block';
          var msg='Resized to '+w+' x '+h+' px - output file size: '+fmtSize(blob.size)+' (original '+origW+' x '+origH+' px, '+fmtSize(origSize)+').';
          if(finalQ!==null){msg+=' Final quality: '+Math.round(finalQ*100)+'%.';}
          if(mime==='image/png'&&targetKb>0){msg+=' PNG is lossless, so the target file size does not apply.';}
          resultBox.textContent=msg;
          outputBox.style.display='block';
        }
        if(mime==='image/png'||!targetKb||targetKb<=0){canvas.toBlob(function(blob){finish(blob,null);},mime,q);return;}
        var targetBytes=targetKb*1024;
        var step=function(curQ){
          canvas.toBlob(function(blob){
            if(!blob){showError('Sorry, this image could not be resized in your browser.');return;}
            if(blob.size<=targetBytes||curQ<=0.10){finish(blob,curQ);}
            else{step(Math.max(0.10,curQ-0.10));}
          },mime,curQ);
        };
        step(q);
      };
      img.onerror=function(){showError('That file is not an image. Please choose a JPG, PNG or WebP image.');};
      img.src=e.target.result;
    };
    reader.readAsDataURL(file);
  });
}
if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initIt3ImageResizer);}else{initIt3ImageResizer();}
function initIt3PaintTileCalculator(){
var modeEl=document.getElementById('it3-pt-mode'); if(!modeEl){return;}
var unitsEl=document.getElementById('it3-pt-units');
var areaEl=document.getElementById('it3-pt-area');
var areaUnitEl=document.getElementById('it3-pt-area-unit');
var coatsEl=document.getElementById('it3-pt-coats');
var coverageEl=document.getElementById('it3-pt-coverage');
var coverageLabel=document.getElementById('it3-pt-coverage-label');
var tinEl=document.getElementById('it3-pt-tin');
var paintPriceEl=document.getElementById('it3-pt-paint-price');
var paintCurrWrap=document.getElementById('it3-pt-paint-currency-wrap');
var paintCurrEl=document.getElementById('it3-pt-paint-currency');
var lengthEl=document.getElementById('it3-pt-length');
var widthEl=document.getElementById('it3-pt-width');
var floorAreaEl=document.getElementById('it3-pt-floor-area');
var tileSizeEl=document.getElementById('it3-pt-tile-size');
var customTilesEl=document.getElementById('it3-pt-custom-tile');
var customWEl=document.getElementById('it3-pt-custom-w');
var customHEl=document.getElementById('it3-pt-custom-h');
var customUnitEl=document.getElementById('it3-pt-custom-unit');
var customUnit2El=document.getElementById('it3-pt-custom-unit2');
var layoutEl=document.getElementById('it3-pt-layout');
var perBoxEl=document.getElementById('it3-pt-per-box');
var boxPriceEl=document.getElementById('it3-pt-box-price');
var tileCurrWrap=document.getElementById('it3-pt-tile-currency-wrap');
var tileCurrEl=document.getElementById('it3-pt-tile-currency');
var calcBtn=document.getElementById('it3-pt-calc');
var errorBox=document.getElementById('it3-pt-error');
var outputBox=document.getElementById('it3-pt-output');
var symbols={USD:'$',PKR:'Rs ',GBP:'\u00a3',EUR:'\u20ac',INR:'\u20b9',AED:'AED '};
function money(v,cur){var s=symbols[cur]||'$'; return s+v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}
function showErr(m){errorBox.textContent=m; errorBox.style.display='block'; outputBox.style.display='none';}
function hideErr(){errorBox.style.display='none';}
function setOptions(sel,opts,def){sel.innerHTML=''; for(var i=0;i<opts.length;i++){var o=document.createElement('option'); o.value=opts[i][0]; o.textContent=opts[i][1]; if(opts[i][0]===def){o.selected=true;} sel.appendChild(o);}}
function applyUnits(){
var us=unitsEl.value==='us';
if(us){
areaUnitEl.value='ft2';
coverageLabel.textContent='Coverage (ft\u00b2 per gallon)';
if(coverageEl.value==='10'){coverageEl.value='350';}
setOptions(tinEl,[['0.25','1 quart'],['1','1 gallon']],'1');
setOptions(tileSizeEl,[['12x12','12 \u00d7 12 in'],['12x24','12 \u00d7 24 in'],['24x24','24 \u00d7 24 in'],['18x18','18 \u00d7 18 in'],['custom','Custom']],'12x12');
customUnitEl.textContent='in'; customUnit2El.textContent='in';
}else{
areaUnitEl.value='m2';
coverageLabel.textContent='Coverage (m\u00b2 per litre)';
if(coverageEl.value==='350'){coverageEl.value='10';}
setOptions(tinEl,[['1','1 L'],['2.5','2.5 L'],['5','5 L'],['10','10 L']],'5');
setOptions(tileSizeEl,[['30x30','30 \u00d7 30 cm'],['60x60','60 \u00d7 60 cm'],['30x60','30 \u00d7 60 cm'],['45x45','45 \u00d7 45 cm'],['custom','Custom']],'60x60');
customUnitEl.textContent='cm'; customUnit2El.textContent='cm';
}
customTilesEl.style.display=(tileSizeEl.value==='custom')?'block':'none';
updateFloorArea();
}
function applyMode(){
var tile=modeEl.value==='tile';
document.getElementById('it3-pt-paint').style.display=tile?'none':'block';
document.getElementById('it3-pt-tile').style.display=tile?'block':'none';
hideErr(); outputBox.style.display='none';
}
function updateFloorArea(){
var l=parseFloat(lengthEl.value), w=parseFloat(widthEl.value);
if(!isNaN(l)&&!isNaN(w)&&l>0&&w>0){floorAreaEl.textContent=(l*w).toFixed(2)+' '+(unitsEl.value==='us'?'ft\u00b2':'m\u00b2');}else{floorAreaEl.textContent='-';}
}
function toggleCurrency(){
paintCurrWrap.style.display=(paintPriceEl.value&&parseFloat(paintPriceEl.value)>0)?'block':'none';
tileCurrWrap.style.display=(boxPriceEl.value&&parseFloat(boxPriceEl.value)>0)?'block':'none';
}
function tileArea(){
var v=tileSizeEl.value;
if(v==='custom'){var cw=parseFloat(customWEl.value), ch=parseFloat(customHEl.value); if(!cw||!ch||cw<=0||ch<=0){return 0;} return unitsEl.value==='us'?(cw*ch)/144:(cw*ch)/10000;}
var p=v.split('x'); var a=parseFloat(p[0]), b=parseFloat(p[1]);
return unitsEl.value==='us'?(a*b)/144:(a*b)/10000;
}
calcBtn.addEventListener('click',function(){
hideErr();
if(modeEl.value==='paint'){
var area=parseFloat(areaEl.value); var coverage=parseFloat(coverageEl.value); var tin=parseFloat(tinEl.value); var coats=parseInt(coatsEl.value,10);
if(!area||area<=0){showErr('Please enter the total area to paint. It must be more than zero.'); return;}
if(!coverage||coverage<=0){showErr('Please enter the coverage per unit. It must be more than zero.'); return;}
if(!tin||tin<=0){showErr('Please choose a tin size.'); return;}
var needed=area*coats/coverage;
var tins=Math.ceil(needed/tin);
var unit=unitsEl.value==='us'?'gallons':'litres';
document.getElementById('it3-pt-paint-needed').textContent=needed.toFixed(2)+' '+unit;
document.getElementById('it3-pt-tins').textContent=tins;
var price=parseFloat(paintPriceEl.value);
var costRow=document.getElementById('it3-pt-paint-cost-row');
if(price&&price>0){document.getElementById('it3-pt-paint-cost').textContent=money(price*tins,paintCurrEl.value); costRow.style.display='block';}else{costRow.style.display='none';}
document.getElementById('it3-pt-paint-results').style.display='block';
document.getElementById('it3-pt-tile-results').style.display='none';
outputBox.style.display='block';
}else{
var len=parseFloat(lengthEl.value), wid=parseFloat(widthEl.value);
if(!len||!wid||len<=0||wid<=0){showErr('Please enter the floor length and floor width. Both must be more than zero.'); return;}
var floor=len*wid;
var tArea=tileArea();
if(!tArea||tArea<=0){showErr('Please choose a tile size, or enter a custom tile width and height greater than zero.'); return;}
var perBox=parseInt(perBoxEl.value,10);
if(!perBox||perBox<=0){showErr('Please enter the number of tiles per box. It must be 1 or more.'); return;}
var waste=parseFloat(layoutEl.value)/100;
var wasteArea=floor*(1+waste);
var tiles=Math.ceil(wasteArea/tArea);
var boxes=Math.ceil(tiles/perBox);
var areaUnit=unitsEl.value==='us'?'ft\u00b2':'m\u00b2';
document.getElementById('it3-pt-tile-floor-area').textContent=floor.toFixed(2)+' '+areaUnit;
document.getElementById('it3-pt-waste-area').textContent=wasteArea.toFixed(2)+' '+areaUnit;
document.getElementById('it3-pt-tiles').textContent=tiles;
document.getElementById('it3-pt-boxes').textContent=boxes;
var bprice=parseFloat(boxPriceEl.value);
var bcostRow=document.getElementById('it3-pt-tile-cost-row');
if(bprice&&bprice>0){document.getElementById('it3-pt-tile-cost').textContent=money(bprice*boxes,tileCurrEl.value); bcostRow.style.display='block';}else{bcostRow.style.display='none';}
document.getElementById('it3-pt-tile-results').style.display='block';
document.getElementById('it3-pt-paint-results').style.display='none';
outputBox.style.display='block';
}
});
modeEl.addEventListener('change',applyMode);
unitsEl.addEventListener('change',applyUnits);
tileSizeEl.addEventListener('change',function(){customTilesEl.style.display=(tileSizeEl.value==='custom')?'block':'none';});
lengthEl.addEventListener('input',updateFloorArea);
widthEl.addEventListener('input',updateFloorArea);
paintPriceEl.addEventListener('input',toggleCurrency);
boxPriceEl.addEventListener('input',toggleCurrency);
applyUnits(); applyMode(); toggleCurrency();
}
if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initIt3PaintTileCalculator);}else{initIt3PaintTileCalculator();}

/* ==== Profit Margin & Markup Calculator ==== */
function initIt3ProfitMarginCalculator(){
var root=document.querySelector('[data-calc="profit-margin-calculator"]');
if(!root){return;}
var modeEl=document.getElementById('it3-pm-mode');
var currEl=document.getElementById('it3-pm-currency');
var costEl=document.getElementById('it3-pm-cost');
var priceEl=document.getElementById('it3-pm-price');
var marginEl=document.getElementById('it3-pm-margin');
var markupEl=document.getElementById('it3-pm-markup');
var priceWrap=document.getElementById('it3-pm-price-wrap');
var marginWrap=document.getElementById('it3-pm-margin-wrap');
var markupWrap=document.getElementById('it3-pm-markup-wrap');
var calcBtn=document.getElementById('it3-pm-calc');
var errEl=document.getElementById('it3-pm-error');
var outputBox=document.getElementById('it3-pm-output');
var priceResults=document.getElementById('it3-pm-price-results');
var targetResults=document.getElementById('it3-pm-target-results');
var symbols={USD:'$',PKR:'Rs ',GBP:'\u00a3',EUR:'\u20ac',INR:'\u20b9',AED:'AED '};
function money(n){var s=symbols[currEl.value]||'$';return (n<0?'-':'')+s+Math.abs(n).toFixed(2);}
function showErr(msg){errEl.textContent=msg;errEl.style.display='block';outputBox.style.display='none';}
function hideErr(){errEl.style.display='none';}
function applyMode(){var m=modeEl.value;priceWrap.style.display=(m==='price')?'block':'none';marginWrap.style.display=(m==='margin')?'block':'none';markupWrap.style.display=(m==='markup')?'block':'none';hideErr();outputBox.style.display='none';}
calcBtn.addEventListener('click',function(){
hideErr();
var cost=parseFloat(costEl.value);
if(!cost||cost<=0){showErr('Please enter a cost. It must be more than zero.');return;}
if(modeEl.value==='price'){
var price=parseFloat(priceEl.value);
if(!price||price<=0){showErr('Please enter a selling price. It must be more than zero.');return;}
var profit=price-cost;
var margin=profit/price*100;
var markup=profit/cost*100;
document.getElementById('it3-pm-profit').textContent=money(profit);
document.getElementById('it3-pm-margin-out').textContent=margin.toFixed(2)+'%';
document.getElementById('it3-pm-markup-out').textContent=markup.toFixed(2)+'%';
priceResults.style.display='block';
targetResults.style.display='none';
outputBox.style.display='block';
}else if(modeEl.value==='margin'){
var targetMargin=parseFloat(marginEl.value);
if(isNaN(targetMargin)){showErr('Please enter a target margin %.');return;}
if(targetMargin<0||targetMargin>=100){showErr('Target margin must be less than 100%. Margin can never reach 100% because margin is profit divided by the selling price, and at 100% the cost would have to be zero.');return;}
var sellPrice=cost/(1-targetMargin/100);
var profit2=sellPrice-cost;
document.getElementById('it3-pm-price-out').textContent=money(sellPrice);
document.getElementById('it3-pm-profit2').textContent=money(profit2);
priceResults.style.display='none';
targetResults.style.display='block';
outputBox.style.display='block';
}else{
var targetMarkup=parseFloat(markupEl.value);
if(isNaN(targetMarkup)){showErr('Please enter a target markup %.');return;}
if(targetMarkup<0){showErr('Target markup cannot be less than zero.');return;}
var sellPrice2=cost*(1+targetMarkup/100);
var profit3=sellPrice2-cost;
document.getElementById('it3-pm-price-out').textContent=money(sellPrice2);
document.getElementById('it3-pm-profit2').textContent=money(profit3);
priceResults.style.display='none';
targetResults.style.display='block';
outputBox.style.display='block';
}
});
modeEl.addEventListener('change',applyMode);
applyMode();
}
if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initIt3ProfitMarginCalculator);}else{initIt3ProfitMarginCalculator();}