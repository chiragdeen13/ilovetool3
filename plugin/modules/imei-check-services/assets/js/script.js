/* ============ IMEI Check Services JS v3 ============ */
jQuery(document).ready(function ($) {
    'use strict';

    /* ========== HELPERS ========== */
    function showResult($tool, html) {
        $tool.find('.t3-error').hide();
        var $res = $tool.find('.t3-result');
        $res.html(html).show();
    }
    function showError($tool, msg) {
        $tool.find('.t3-result').hide();
        $tool.find('.t3-error').text(msg).show();
    }
    function v(id) { return $('#' + id).val().trim(); }
    function n(id) { return parseFloat($('#' + id).val()); }

    function fmt(n, dec) {
        return Number(n).toLocaleString('en-US', { maximumFractionDigits: dec === undefined ? 2 : dec });
    }

    /* ========== 1. IMEI CHECK (AJAX) ========== */
    $('#imeiCheckForm').on('submit', function (e) {
        e.preventDefault();
        var imei = $('#imei_number').val().trim();
        var type = $('input[name="service_type"]:checked').val() || 'apple_basic';

        if (!/^\d{15}$/.test(imei)) {
            imeiError('Please enter a valid 15-digit IMEI number.');
            return;
        }

        imeiHideAll();
        $('#imeiLoading').show();

        $.post(imeiAjax.url, {
            action: 'imei_check', nonce: imeiAjax.nonce,
            imei: imei, service_type: type
        }).done(function (res) {
            if (res.success) { imeiShowResult(res.data); }
            else { imeiError(res.data.message || 'Error occurred.'); }
        }).fail(function () { imeiError('Network error. Try again.'); });
    });

    $('#imeiNewCheck').on('click', function () {
        imeiHideAll();
        $('#imeiCheckForm').show();
        $('#imei_number').val('').focus();
    });

    function imeiHideAll() { $('#imeiLoading, #imeiError, #imeiResult').hide(); }
    function imeiError(msg) {
        imeiHideAll();
        $('#imeiErrorMsg').text(msg);
        $('#imeiError').show();
    }

    var svcLabels = { apple_basic: 'Apple Basic Info', apple_warranty: 'Apple Warranty', apple_carrier: 'Apple Carrier', icloud_check: 'iCloud Check', samsung: 'Samsung Check', blacklist: 'Blacklist Check' };
    var badWords = ['BLACKLISTED', 'Locked', 'ON', 'Lost'];

    function imeiShowResult(data) {
        var html = '';
        /* FIX: previously, when no real IMEI API was configured (or the
           live API failed), the backend sent back realistic-looking
           mock data (e.g. "iPhone 14 Pro 256GB Space Black") with zero
           visible indication it wasn't real. Users had no way to know
           they were looking at sample data. This adds a clear banner
           whenever the response isn't confirmed live data. */
        if (data.source === 'demo' || data.source === 'demo-fallback') {
            html += '<div class="imei-demo-banner" style="background:#fff3cd;border:1px solid #ffe58f;color:#7a5c00;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:13px;font-weight:600;">'
                + '⚠️ Demo Mode — this is sample data, not a real lookup for this IMEI.'
                + (data.data && data.data._notice ? ' (' + data.data._notice + ')' : ' Live lookup is not yet configured for this site.')
                + '</div>';
        }
        html += '<div class="imei-row"><div class="imei-row-label">Service</div><div class="imei-row-val">' + (svcLabels[data.service_type] || '') + '</div></div>';
        $.each(data.data, function (k, val2) {
            if (k === '_notice') { return; } // already shown in the banner above, don't repeat as a data row
            var cls = '';
            for (var i = 0; i < badWords.length; i++) {
                if (String(val2).indexOf(badWords[i]) !== -1) { cls = 'bad'; break; }
            }
            html += '<div class="imei-row"><div class="imei-row-label">' + k + '</div><div class="imei-row-val ' + cls + '">' + val2 + '</div></div>';
        });
        $('#resultContent').html(html);
        $('#imeiCheckForm').hide();
        $('#imeiLoading').hide();
        $('#imeiResult').show();
    }

    /* ========== LUHN ALGORITHM (IMEI) ========== */
    function luhnCheckDigit(base14) {
        var sum = 0;
        for (var i = 0; i < 14; i++) {
            var d = parseInt(base14.charAt(i), 10);
            if (i % 2 === 1) { d *= 2; if (d > 9) { d -= 9; } }
            sum += d;
        }
        return String((10 - (sum % 10)) % 10);
    }

    function luhnValidate(imei) {
        if (!/^\d{15}$/.test(imei)) { return false; }
        return luhnCheckDigit(imei.substr(0, 14)) === imei.charAt(14);
    }

    /* ========== TOOL ACTIONS ========== */
    var tools3 = {

        age: function ($tool) {
            var dobStr = v('ag-dob');
            if (!dobStr) { showError($tool, 'Please select your date of birth.'); return; }

            var dob = new Date(dobStr + 'T00:00:00');
            var today = new Date();
            today.setHours(0, 0, 0, 0);

            if (dob > today) { showError($tool, 'Date of birth cannot be in the future!'); return; }

            var yy = today.getFullYear() - dob.getFullYear();
            var mm = today.getMonth() - dob.getMonth();
            var dd = today.getDate() - dob.getDate();

            if (dd < 0) { mm--; dd += new Date(today.getFullYear(), today.getMonth(), 0).getDate(); }
            if (mm < 0) { yy--; mm += 12; }

            var nextBday = new Date(today.getFullYear(), dob.getMonth(), dob.getDate());
            if (nextBday < today) { nextBday = new Date(today.getFullYear() + 1, dob.getMonth(), dob.getDate()); }
            var daysLeft = Math.ceil((nextBday - today) / 86400000);
            var totalDays = Math.floor((today - dob) / 86400000);
            var totalWeeks = Math.floor(totalDays / 7);
            var bornDay = dob.toLocaleDateString('en-US', { weekday: 'long' });

            showResult($tool,
                '<div class="imei-res-big" style="text-align:center;font-size:26px;font-weight:700;color:#4f46e5;padding:8px;">' + yy + ' years, ' + mm + ' months, ' + dd + ' days</div>' +
                '<div class="imei-row"><div class="imei-row-label">Total Days Lived</div><div class="imei-row-val">' + fmt(totalDays, 0) + ' days (' + fmt(totalWeeks, 0) + ' weeks)</div></div>' +
                '<div class="imei-row"><div class="imei-row-label">Born On</div><div class="imei-row-val">' + bornDay + '</div></div>' +
                '<div class="imei-row"><div class="imei-row-label">Next Birthday</div><div class="imei-row-val">' + fmt(daysLeft, 0) + ' days to go! &#127874;</div></div>'
            );
        },

        bmi: function ($tool) {
            var weight = n('bmi-weight');
            var heightCm = n('bmi-height');

            if (!weight || weight < 10) { showError($tool, 'Please enter a valid weight in kg.'); return; }
            if (!heightCm || heightCm < 50) { showError($tool, 'Please enter a valid height in cm.'); return; }

            var hM = heightCm / 100;
            var bmi = weight / (hM * hM);
            var rounded = Math.round(bmi * 10) / 10;

            var category, color;
            if (bmi < 18.5) { category = 'Underweight'; color = '#f59e0b'; }
            else if (bmi < 25) { category = 'Normal Weight'; color = '#059669'; }
            else if (bmi < 30) { category = 'Overweight'; color = '#d97706'; }
            else { category = 'Obese'; color = '#dc2626'; }

            var healthyLow = (18.5 * hM * hM).toFixed(1);
            var healthyHigh = (24.9 * hM * hM).toFixed(1);

            showResult($tool,
                '<div style="text-align:center;padding:10px;"><span style="font-size:44px;font-weight:800;color:' + color + ';">' + rounded + '</span><br/><strong style="font-size:17px;color:' + color + ';">' + category + '</strong></div>' +
                '<div class="imei-row"><div class="imei-row-label">Healthy Weight Range</div><div class="imei-row-val">' + healthyLow + ' - ' + healthyHigh + ' kg</div></div>' +
                '<div class="imei-row"><div class="imei-row-label">Your Stats</div><div class="imei-row-val">' + weight + ' kg, ' + heightCm + ' cm</div></div>'
            );
        },

        percentage: function ($tool) {
            var mode = v('pc-mode');
            var x = n('pc-val1');
            var y = n('pc-val2');
            var result, label;

            if (isNaN(x)) { showError($tool, 'Please enter a valid first value.'); return; }

            if (mode === 'of') {
                if (isNaN(y)) { showError($tool, 'Please enter a valid second value.'); return; }
                result = x / 100 * y;
                label = x + '% of ' + y + ' = <strong style="color:#4f46e5;">' + fmt(result, 2) + '</strong>';

            } else if (mode === 'isWhat') {
                if (!y || y === 0) { showError($tool, 'Second value cannot be zero.'); return; }
                result = x / y * 100;
                label = x + ' is <strong style="color:#4f46e5;">' + fmt(result, 2) + '%</strong> of ' + y;

            } else {
                if (isNaN(y)) { showError($tool, 'Please enter a valid second value.'); return; }
                if (x === 0) { showError($tool, 'First value cannot be zero for change calculation.'); return; }
                result = (y - x) / Math.abs(x) * 100;
                var dir = result >= 0 ? '&#128200; Increase of ' : '&#128201; Decrease of ';
                label = 'From ' + x + ' to ' + y + ':<br/>' + dir + '<strong style="color:#4f46e5;">' + fmt(Math.abs(result), 2) + '%</strong>';
            }

            showResult($tool, '<div style="text-align:center;font-size:19px;padding:14px;color:#334155;">' + label + '</div>');
        },

        gpa: function ($tool) {
            var rows = $('#gpa-rows .gpa-row');
            if (rows.length === 0) { showError($tool, 'Add at least one course.'); return; }

            var totalPoints = 0, totalCredits = 0, validCourses = 0;

            rows.each(function () {
                var credits = parseFloat($(this).find('.gpa-credits').val());
                var grade = parseFloat($(this).find('.gpa-grade').val());
                if (!isNaN(credits) && credits > 0 && !isNaN(grade)) {
                    totalPoints += credits * grade;
                    totalCredits += credits;
                    validCourses++;
                }
            });

            if (validCourses === 0 || totalCredits === 0) {
                showError($tool, 'Add at least one course with valid credits and grade.');
                return;
            }

            var gpa = totalPoints / totalCredits;
            var letter, color;
            if (gpa >= 3.7) { letter = 'A'; color = '#059669'; }
            else if (gpa >= 3.0) { letter = 'B'; color = '#2563eb'; }
            else if (gpa >= 2.0) { letter = 'C'; color = '#d97706'; }
            else if (gpa >= 1.0) { letter = 'D'; color = '#ea580c'; }
            else { letter = 'F'; color = '#dc2626'; }

            showResult($tool,
                '<div style="text-align:center;padding:10px;"><span style="font-size:44px;font-weight:800;color:' + color + ';">' + gpa.toFixed(2) + '</span><br/><strong style="color:' + color + '">GPA &mdash; Grade ' + letter + ' range</strong></div>' +
                '<div class="imei-row"><div class="imei-row-label">Total Credits</div><div class="imei-row-val">' + fmt(totalCredits, 1) + '</div></div>' +
                '<div class="imei-row"><div class="imei-row-label">Quality Points</div><div class="imei-row-val">' + fmt(totalPoints, 1) + '</div></div>' +
                '<div class="imei-row"><div class="imei-row-label">Courses Counted</div><div class="imei-row-val">' + validCourses + '</div></div>'
            );
        },

        imeivalidate: function ($tool) {
            var raw = v('iv-imei').replace(/\D/g, '');
            if (raw.length !== 15) { showError($tool, 'IMEI must be exactly 15 digits. You entered ' + raw.length + '.'); return; }

            var valid = luhnValidate(raw);
            var reportingBody = '';
            var tac = raw.substr(0, 8);

            var firstTwo = parseInt(raw.substr(0, 2), 10);
            if (firstTwo === 35) { reportingBody = 'BABT (UK) — likely European/Asian device'; }
            else if (firstTwo === 1) { reportingBody = 'PTCRB (USA) — likely North American device'; }
            else if (firstTwo === 86) { reportingBody = 'TAF (China) — likely Chinese device'; }
            else { reportingBody = 'Unknown reporting body prefix'; }

            var digitSum = 0;
            for (var i = 0; i < 15; i++) { digitSum += parseInt(raw.charAt(i), 10); }

            if (valid) {
                showResult($tool,
                    '<div style="text-align:center;padding:12px;background:#ecfdf5;border-radius:10px;margin-bottom:10px;"><span style="font-size:38px;">&#9989;</span><br/><strong style="font-size:20px;color:#047857;">VALID IMEI!</strong></div>' +
                    '<div class="imei-row"><div class="imei-row-label">Luhn Check</div><div class="imei-row-val" style="color:#059669;font-weight:700;">PASSED</div></div>' +
                    '<div class="imei-row"><div class="imei-row-label">TAC (Type Allocation Code)</div><div class="imei-row-val">' + tac + '</div></div>' +
                    '<div class="imei-row"><div class="imei-row-label">Reporting Body</div><div class="imei-row-val">' + reportingBody + '</div></div>' +
                    '<div class="imei-row"><div class="imei-row-label">Check Digit</div><div class="imei-row-val">' + raw.charAt(14) + ' &#9989;</div></div>'
                );
            } else {
                showResult($tool,
                    '<div style="text-align:center;padding:12px;background:#fef2f2;border-radius:10px;margin-bottom:10px;"><span style="font-size:38px;">&#10060;</span><br/><strong style="font-size:20px;color:#dc2626;">INVALID IMEI</strong></div>' +
                    '<div class="imei-row"><div class="imei-row-label">Luhn Check</div><div class="imei-row-val" style="color:#dc2626;font-weight:700;">FAILED</div></div>' +
                    '<div class="imei-row"><div class="imei-row-label">Expected Check Digit</div><div class="imei-row-val">' + luhnCheckDigit(raw.substr(0, 14)) + ' (you entered: ' + raw.charAt(14) + ')</div></div>'
                );
            }
        },

        imeigen: function ($tool) {
            var count = parseInt(v('ig-count'), 10) || 5;
            if (count < 1 || count > 20) { showError($tool, 'Count must be between 1 and 20.'); return; }

            var tac = v('ig-tac').replace(/\D/g, '');
            if (tac && tac.length !== 8) { showError($tool, 'TAC prefix must be exactly 8 digits (or leave empty).'); return; }

            var results = [];
            for (var c = 0; c < count; c++) {
                var base = tac;
                if (!base) {
                    base = '35' + String(45 + Math.floor(Math.random() * 50));
                    while (base.length < 8) { base += Math.floor(Math.random() * 10); }
                } else {
                    base = base.substr(0, 8);
                }
                var serial = '';
                while ((base + serial).length < 14) { serial += Math.floor(Math.random() * 10); }
                var full14 = (base + serial).substr(0, 14);
                results.push(full14 + luhnCheckDigit(full14));
            }

            var chips = '';
            for (var i = 0; i < results.length; i++) {
                chips += '<div style="background:#eef2ff;border:1px solid #c7d2fe;border-radius:8px;padding:9px 14px;font-family:monospace;font-weight:600;color:#4338ca;margin:4px;display:inline-block;">' + results[i] + '</div>';
            }

            showResult($tool,
                '<p style="margin:0 0 8px;color:#64748b;font-size:13px;">&#9989; Sab IMEIs Luhn-valid hain (testing ke liye):</p>' +
                '<div>' + chips + '</div>' +
                '<p style="margin-top:10px;font-size:12px;color:#94a3b8;">* Sirf development/testing use ke liye. Real devices track karne ke liye use na karein.</p>'
            );
        }
    };

    /* Percentage mode labels */
    var pcLabels = {
        'of': ['X (%)', 'Y'],
        'isWhat': ['X (part)', 'Y (total)'],
        'change': ['From (old value)', 'To (new value)']
    };
    $(document).on('change', '#pc-mode', function () {
        var labels = pcLabels[$(this).val()] || pcLabels['of'];
        $('#pc-label1').text(labels[0]);
        $('#pc-label2').text(labels[1]);
    });

    /* GPA dynamic rows */
    var gradeOptions = '<option value="4">A (4.0)</option><option value="3.7">A- (3.7)</option><option value="3.3">B+ (3.3)</option><option value="3">B (3.0)</option><option value="2.7">B- (2.7)</option><option value="2.3">C+ (2.3)</option><option value="2">C (2.0)</option><option value="1.7">C- (1.7)</option><option value="1.3">D+ (1.3)</option><option value="1">D (1.0)</option><option value="0">F (0.0)</option>';

    function addGpaRow(name) {
        $('#gpa-rows').append(
            '<div class="gpa-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">' +
            '<input type="text" class="gpa-name" placeholder="Course name" style="flex:1.4;padding:10px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;" value="' + (name || '') + '" />' +
            '<input type="number" class="gpa-credits" placeholder="Credits" min="0.5" step="0.5" style="width:90px;padding:10px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;" />' +
            '<select class="gpa-grade" style="width:110px;padding:10px;border:2px solid #e2e8f0;border-radius:8px;font-size:14px;">' + gradeOptions + '</select>' +
            '<button type="button" class="gpa-remove" style="background:#fee2e2;color:#dc2626;border:none;border-radius:8px;width:36px;height:38px;cursor:pointer;font-weight:700;">&times;</button>' +
            '</div>'
        );
    }

    $(document).on('click', '#gpa-add', function () { addGpaRow(); });
    $(document).on('click', '.gpa-remove', function () { $(this).closest('.gpa-row').remove(); });

    /* Initialize GPA with 3 default rows */
    if ($('#gpa-rows').length) {
        addGpaRow('');
        addGpaRow('');
        addGpaRow('');
    }

    /* Wire all t3 buttons */
    $(document).on('click', '[data-t3]', function () {
        var action = $(this).attr('data-t3');
        var $tool = $(this).closest('.imei-wrap');
        if (tools3[action] && $tool.length) {
            tools3[action]($tool);
        }
    });

    /* Enter key support */
    $('.imei-wrap input').on('keydown', function (e) {
        if (e.key === 'Enter' && $(this).attr('type') !== 'date') {
            e.preventDefault();
            var btn = $(this).closest('.imei-wrap').find('[data-t3]').first();
            if ($(this).closest('#imei-check-form').length) {
                $(this).closest('form').trigger('submit');
            } else if (btn.length) {
                btn.trigger('click');
            }
        }
    });
});
