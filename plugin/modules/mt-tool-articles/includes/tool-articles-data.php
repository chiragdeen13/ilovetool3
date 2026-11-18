<?php
/* MT Tool Articles — Article Database */
if (!defined('ABSPATH')) exit;

global $mta_articles, $mta_category_templates;

$mta_articles = array(

/* ================= IMEI ================= */
'imei-check' => array(
    'intro' => 'IMEI Check is a free online tool that helps you verify your mobile device identity instantly. Every phone has a unique 15-digit IMEI (International Mobile Equipment Identity) number that works like a fingerprint for your device. With this tool, you can check device information, verify authenticity before buying a used phone, and confirm your handset details.',
    'howto' => array('Dial *#06# on your phone to find your 15-digit IMEI number.', 'Enter the IMEI number in the input box above.', 'Select the type of check you want to perform.', 'Click "Check Now" and get instant results.'),
    'features' => array('Instant device verification', 'Supports all phone brands including iPhone, Samsung, Xiaomi', 'No registration required', 'Safe and secure checking'),
    'faqs' => array(
        array('q' => 'What is an IMEI number?', 'a' => 'IMEI stands for International Mobile Equipment Identity. It is a unique 15-digit code assigned to every mobile device. No two devices share the same IMEI number, making it perfect for identification.'),
        array('q' => 'How do I find my IMEI number?', 'a' => 'The easiest way is to dial *#06# on your phone keypad. The IMEI will appear instantly on your screen. You can also find it in Settings > About Phone, or printed on the original box and SIM tray.'),
        array('q' => 'Is it safe to check my IMEI online?', 'a' => 'Yes! Checking an IMEI is completely safe. The IMEI only identifies the device hardware and cannot be used to hack or track your phone. Our tool processes your request securely.'),
    ),
),

/* ================= NEW: AGE CALCULATOR ================= */
'age-calculator' => array(
    'intro' => 'Age Calculator computes your exact age in years, months, and days from your date of birth. Unlike rough estimates, this tool accounts for varying month lengths and leap years to give precise results. It also shows total days lived, your birth weekday, and a countdown to your next birthday.',
    'howto' => array('Select your date of birth using the calendar picker.', 'Click the Calculate Age button.', 'View your exact age broken into years, months, and days.', 'Check bonus facts like days lived and next birthday countdown.'),
    'features' => array('Precise Y/M/D breakdown accounting for leap years', 'Total days and weeks lived counter', 'Next birthday countdown included', 'Weekday-of-birth trivia fact'),
    'faqs' => array(
        array('q' => 'How is exact age calculated?', 'a' => 'The calculator counts complete calendar years first, then remaining months, then remaining days — borrowing days from the previous month when needed, exactly like humans count age.'),
        array('q' => 'Why does my age differ from other calculators?', 'a' => 'Some tools simply divide total days by 365.25, producing fractional inaccuracies. Our calendar-aware method matches official documents like passports and IDs.'),
        array('q' => 'Can I calculate someone else’s age?', 'a' => 'Absolutely! Enter any birth date — historical figures, family members, pets — and get their precise current age instantly.'),
    ),
),

/* ================= NEW: BMI CALCULATOR ================= */
'bmi-calculator' => array(
    'intro' => 'BMI (Body Mass Index) Calculator assesses whether your weight is healthy relative to your height. Used by doctors worldwide, BMI provides a quick screening metric with four categories: Underweight, Normal, Overweight, and Obese. Enter your weight in kilograms and height in centimeters for instant results with your healthy weight range.',
    'howto' => array('Enter your weight in kilograms.', 'Enter your height in centimeters.', 'Click Calculate BMI.', 'See your BMI score, category, and recommended weight range.'),
    'features' => array('WHO standard BMI categories', 'Color-coded health classification', 'Personalized healthy weight range', 'Instant calculation, zero waiting'),
    'faqs' => array(
        array('q' => 'How is BMI calculated?', 'a' => 'BMI equals weight in kilograms divided by height in meters squared (kg/m²). For example, 70kg at 1.70m gives 70 ÷ 2.89 = 24.2 — comfortably within normal range.'),
        array('q' => 'What are the BMI categories?', 'a' => 'Below 18.5 is Underweight, 18.5–24.9 is Normal, 25–29.9 is Overweight, and 30+ is Obese. These WHO thresholds apply to adults aged 20+.'),
        array('q' => 'Does BMI work for athletes?', 'a' => 'BMI has known limits — muscle weighs more than fat, so muscular athletes may register as "Overweight" despite low body fat. Pair BMI with body fat measurements for fuller picture.'),
    ),
),

/* ================= NEW: PERCENTAGE CALCULATOR ================= */
'percentage-calculator' => array(
    'intro' => 'Percentage Calculator solves all three classic percentage problems in one tool: finding X% of a value, determining what percent X is of Y, and calculating percentage change between two values. Students, shoppers, business owners, and analysts use percentages daily — this tool eliminates mental math mistakes forever.',
    'howto' => array('Select your calculation mode from the dropdown.', 'Enter values in the labeled fields (labels adjust per mode).', 'Click Calculate.', 'Read clearly formatted results with context.'),
    'features' => array('Three calculation modes in one tool', 'Dynamic labels guide correct input', 'Percentage increase/decrease detection', 'Handles decimals and large numbers'),
    'faqs' => array(
        array('q' => 'How do I calculate 15% of 200?', 'a' => 'Use "What is X% of Y" mode: enter 15 as X and 200 as Y. Result: 30. The math is (15 ÷ 100) × 200 = 30.'),
        array('q' => 'How do I find what percent 45 is of 180?', 'a' => 'Switch to "X is what percent of Y" mode: 45 is X, 180 is Y. Answer: 25%. Formula: (45 ÷ 180) × 100 = 25%.'),
        array('q' => 'How is percentage change calculated?', 'a' => 'Change = (New − Old) ÷ |Old| × 100. From 80 to 100 is +25% increase; from 100 to 80 is −20% decrease — note asymmetry because the base changes!'),
    ),
),

/* ================= NEW: GPA CALCULATOR ================= */
'gpa-calculator' => array(
    'intro' => 'GPA Calculator computes your Grade Point Average on the standard American 4.0 scale. Add unlimited courses with credit hours and letter grades — the weighted average updates instantly showing your cumulative GPA, total credits, quality points, and equivalent letter grade band. Essential for scholarship applications, Dean’s list tracking, and graduate school planning.',
    'howto' => array('Add each course with its name, credit hours, and letter grade.', 'Remove unwanted rows with the × button.', 'Click Calculate GPA when all courses are entered.', 'View GPA, total credits, and quality points breakdown.'),
    'features' => array('Full A to F scale including +/- grades', 'Credit-hour weighted calculation', 'Unlimited course rows', 'Quality points transparency'),
    'faqs' => array(
        array('q' => 'How is GPA calculated with credits?', 'a' => 'Multiply each course grade-point by its credit hours, sum these quality points, then divide by total credit hours. A 4.0 in a 3-credit course contributes 12 points; a 3.0 in 4 credits adds another 12 — combined: 24 ÷ 7 = 3.43 GPA.'),
        array('q' => 'Do +/- grades matter?', 'a' => 'Yes! A- (3.7) versus B+ (3.3) shifts GPAs significantly over many courses. Use your institution’s exact scale if it differs from our defaults.'),
        array('q' => 'What is a good college GPA?', 'a' => '3.0 is solid, 3.5+ opens most scholarships and honors programs, 3.7+ qualifies typically for Latin honors (cum laude level). Graduate programs often prefer 3.5+.'),
    ),
),

/* ================= NEW: IMEI VALIDATOR ================= */
'imei-validator' => array(
    'intro' => 'IMEI Validator verifies whether any 15-digit IMEI number is structurally valid using the Luhn checksum algorithm — the same mathematical test every genuine manufacturer passes before shipping devices. Instantly spot fake, mistyped, or fabricated IMEIs, plus discover the device’s reporting body region from its Type Allocation Code prefix.',
    'howto' => array('Dial *#06# on the phone or read IMEI from the box/SIM tray.', 'Enter the 15-digit IMEI in the field above.', 'Click Validate.', 'Review Luhn result, TAC code, and regional reporting body details.'),
    'features' => array('Luhn checksum verification', 'TAC (Type Allocation Code) extraction', 'Reporting body region detection (BABT/PTCRB/TAF)', 'Expected check-digit hint on failure'),
    'faqs' => array(
        array('q' => 'What is the Luhn algorithm?', 'a' => 'The Luhn algorithm is a checksum formula that validates identification numbers. For IMEIs, the 15th digit is calculated from the first 14 — if computed and actual digits mismatch, the IMEI is invalid or mistyped.'),
        array('q' => 'Does VALID mean the phone is not stolen?', 'a' => 'No! Luhn validation confirms structural correctness only. Stolen-device status requires blacklist databases — use our Blacklist Check service for that purpose.'),
        array('q' => 'Why did a real phone fail validation?', 'a' => 'Most likely a typo — swapping two digits breaks Luhn. Carefully re-enter all 15 digits. CDMA-era MEID converters occasionally produce non-Luhn sequences too.'),
    ),
),

/* ================= NEW: IMEI GENERATOR ================= */
'imei-generator' => array(
    'intro' => 'IMEI Number Generator creates valid, Luhn-compliant 15-digit IMEI numbers for software development and testing. Provide an optional 8-digit TAC prefix to simulate specific device models, or generate fully random valid identifiers. Perfect for QA teams testing mobile apps that parse IMEI inputs without touching real hardware identities.',
    'howto' => array('Optionally enter an 8-digit TAC prefix (e.g., 35459903).', 'Set how many IMEIs you need (1-20).', 'Click Generate.', 'Copy Luhn-valid IMEIs directly into your test suite.'),
    'features' => array('Guaranteed Luhn-valid output', 'Custom TAC prefixes supported', 'Batch generation up to 20', 'Cryptographic-quality randomness'),
    'faqs' => array(
        array('q' => 'Are generated IMEIs safe to use?', 'a' => 'For testing purposes absolutely — QA engineers need realistic-format data daily. However, deliberately cloning IMEIs onto physical devices is illegal in most countries. Never do that.'),
        array('q' => 'What is a TAC prefix?', 'a' => 'Type Allocation Code — the first 8 IMEI digits identifying the model and manufacturer. Using real prefixes makes test data look authentic; omitting one generates random valid structures.'),
        array('q' => 'Will these pass blacklist checks?', 'a' => 'Generated IMEIs are random within valid structure — they will not exist in manufacturer or operator databases, so blacklist services return "not found" rather than clean/stolen statuses.'),
    ),
),

/* ================= FINANCE ================= */
'loan-calculator' => array(
    'intro' => 'Loan EMI Calculator helps you calculate your monthly Equated Monthly Installment (EMI) for any loan — home loan, car loan, personal loan, or education loan. Simply enter the loan amount, interest rate, and tenure to see exactly how much you will pay every month, along with total interest and total payment over the loan period.',
    'howto' => array('Enter your total loan amount.', 'Enter the annual interest rate offered by your bank.', 'Enter the loan tenure in years.', 'Click Calculate EMI to see monthly installment, total interest, and total payment.'),
    'features' => array('Accurate EMI calculation using standard formula', 'Shows complete interest breakdown', 'Works for home, car, personal and education loans', 'Free with unlimited calculations'),
    'faqs' => array(
        array('q' => 'What is EMI?', 'a' => 'EMI stands for Equated Monthly Installment. It is the fixed amount you pay every month towards your loan, which includes both principal repayment and interest charges.'),
        array('q' => 'How is EMI calculated?', 'a' => 'EMI is calculated using the formula: EMI = P × r × (1+r)^n / ((1+r)^n − 1), where P is the principal amount, r is the monthly interest rate, and n is the total number of months.'),
        array('q' => 'Can I use this calculator for any loan?', 'a' => 'Yes! This calculator works for home loans, car loans, personal loans, education loans, and business loans. Just enter the correct amount, rate, and tenure.'),
    ),
),
'sip-calculator' => array(
    'intro' => 'SIP (Systematic Investment Plan) Calculator shows you the future value of your regular monthly investments. If you invest a fixed amount every month in mutual funds or other investment schemes, this tool estimates your total returns based on expected growth rate and investment duration.',
    'howto' => array('Enter your monthly investment amount.', 'Enter the expected annual return rate (typically 10-15% for equity funds).', 'Enter the investment duration in years.', 'Click Calculate to see invested amount, estimated returns, and total value.'),
    'features' => array('Future value estimation with compound growth', 'Shows wealth gained vs invested comparison', 'Perfect for mutual fund planning', 'Helps set realistic financial goals'),
    'faqs' => array(
        array('q' => 'What is SIP?', 'a' => 'SIP stands for Systematic Investment Plan. It lets you invest a fixed amount regularly (usually monthly) in mutual funds, helping you build wealth over time through rupee-cost averaging and compounding.'),
        array('q' => 'What return rate should I assume?', 'a' => 'Historically, equity mutual funds have delivered 10-15% annual returns over long periods. Debt funds typically return 6-8%. Use conservative estimates for planning.'),
        array('q' => 'Is SIP return guaranteed?', 'a' => 'No, SIP returns depend on market performance. This calculator provides estimates based on assumed constant returns. Actual returns may vary.'),
    ),
),
'tax-calculator' => array(
    'intro' => 'Income Tax Calculator gives you a quick estimate of your annual income tax based on current tax slabs. We support tax systems for Pakistan (Salaried 2024-25), India (New Regime), and USA (Federal Single Filer). Enter your annual income to see estimated tax, net income, and effective tax rate.',
    'howto' => array('Select your country or tax system from the dropdown.', 'Enter your total annual income.', 'Click Calculate Tax.', 'View estimated tax, take-home income, and effective tax rate.'),
    'features' => array('Multiple country support (Pakistan, India, USA)', 'Progressive slab-based accurate calculation', 'Shows effective tax rate percentage', 'Updated with latest published slabs'),
    'faqs' => array(
        array('q' => 'How does progressive tax work?', 'a' => 'In progressive taxation, different portions of your income are taxed at different rates. Higher income portions fall into higher tax brackets, but lower portions still enjoy lower rates.'),
        array('q' => 'Is this calculator accurate for official filing?', 'a' => 'This calculator provides close estimates based on published slabs. Actual tax liability may differ due to deductions, rebates, and exemptions. Always consult a tax advisor for filing.'),
        array('q' => 'Which tax year rates are used?', 'a' => 'Pakistan uses Salaried slabs 2024-25, India uses New Regime FY 2024-25, and USA uses Federal 2025 single filer brackets.'),
    ),
),
'discount-calculator' => array(
    'intro' => 'Discount Calculator instantly tells you the final price after discount and exactly how much money you save. Perfect for shopping sales, comparing offers, and making smart purchase decisions during seasonal discounts like Black Friday or Eid sales.',
    'howto' => array('Enter the original price of the item.', 'Enter the discount percentage being offered.', 'Click Calculate.', 'See your savings amount and the final price you will pay.'),
    'features' => array('Instant savings calculation', 'Works for any discount percentage', 'Great for sale shopping decisions', 'Simple two-input interface'),
    'faqs' => array(
        array('q' => 'How do I calculate 20% off?', 'a' => 'Multiply the price by 0.20 to get the discount amount, then subtract it from the original price. Or just enter the values above and let the calculator do it instantly!'),
        array('q' => 'Does this work for stacked discounts?', 'a' => 'For stacked discounts (like 20% + additional 10%), apply them one at a time. First calculate the price after 20% off, then apply 10% on the new amount.'),
        array('q' => 'Can I calculate discount on any currency?', 'a' => 'Yes! The calculator works with plain numbers, so it applies equally to Dollars, Rupees, Euros, or any currency.'),
    ),
),
'tip-calculator' => array(
    'intro' => 'Tip Calculator makes dining out stress-free. Enter your bill amount, choose a tip percentage, and split between any number of people to know exactly what each person owes. Never struggle with mental math at restaurants again!',
    'howto' => array('Enter your total bill amount.', 'Select tip percentage (10% to 25%).', 'Enter how many people are splitting the bill.', 'Click Calculate Tip to see tip amount, total, and per-person share.'),
    'features' => array('Standard tipping presets (10-25%)', 'Bill splitting between multiple people', 'Per-person amount calculation', 'Perfect for group dining'),
    'faqs' => array(
        array('q' => 'How much should I tip?', 'a' => 'In the US, 15-20% is standard for good service. In many other countries, tipping customs vary — some include service charge already. Adjust based on local culture and service quality.'),
        array('q' => 'Should I tip on tax amount?', 'a' => 'Traditionally, tips are calculated on the pre-tax bill amount. However, many people simply tip on the total for convenience.'),
        array('q' => 'How does bill splitting work?', 'a' => 'Enter the number of people, and we divide the total (bill + tip) equally, showing exactly what each person should pay.'),
    ),
),
'compound-interest-calculator' => array(
    'intro' => 'Compound Interest Calculator shows the magic of compounding — earning interest on your interest. Whether saving in a bank account, investing in certificates, or planning long-term wealth, this tool projects your money growth with yearly, quarterly, monthly, or daily compounding plus optional monthly deposits.',
    'howto' => array('Enter your initial principal amount.', 'Enter the annual interest rate.', 'Select time period and compounding frequency.', 'Optionally add monthly deposits and click Calculate.'),
    'features' => array('5 compounding frequencies supported', 'Optional monthly deposit contributions', 'Complete breakdown of invested vs earned', 'Long-term wealth projection'),
    'faqs' => array(
        array('q' => 'What is compound interest?', 'a' => 'Compound interest is interest calculated on both the initial principal AND accumulated interest from previous periods. Over time, this snowball effect significantly grows your money compared to simple interest.'),
        array('q' => 'Which compounding frequency is best?', 'a' => 'More frequent compounding means faster growth. Daily compounding earns slightly more than monthly, which beats yearly — assuming the same nominal rate.'),
        array('q' => 'What is the Rule of 72?', 'a' => 'A quick trick: divide 72 by your interest rate to estimate years needed to double your money. At 8% return, money doubles roughly every 9 years.'),
    ),
),

/* ================= HEALTH ================= */
'calorie-calculator' => array(
    'intro' => 'Daily Calorie Calculator tells you exactly how many calories your body needs per day based on your age, gender, height, weight, and activity level. Using the scientifically-proven Mifflin-St Jeor equation, it calculates your BMR (Basal Metabolic Rate) and daily calorie targets for maintaining, losing, or gaining weight.',
    'howto' => array('Enter your age and select gender.', 'Enter your height in centimeters and weight in kilograms.', 'Select your daily activity level honestly.', 'Click Calculate Calories to see your personalized targets.'),
    'features' => array('Scientifically proven Mifflin-St Jeor formula', 'Three goals: maintain, lose, or gain weight', 'Activity level adjustment from sedentary to athlete', 'BMR breakdown included'),
    'faqs' => array(
        array('q' => 'What is BMR?', 'a' => 'BMR (Basal Metabolic Rate) is the number of calories your body burns at complete rest just to stay alive — breathing, circulating blood, and cell production. It accounts for 60-70% of daily calorie burn.'),
        array('q' => 'How accurate is this calculator?', 'a' => 'The Mifflin-St Jeor equation is considered the most accurate predictive formula, with margin of error around 10%. Individual metabolism varies, so treat results as strong starting points.'),
        array('q' => 'Should I eat below my BMR to lose weight?', 'a' => 'Generally no. Eating below BMR for extended periods can slow metabolism. The calculator suggests a moderate 500-calorie deficit for sustainable weight loss of about 0.5kg per week.'),
    ),
),
'water-intake-calculator' => array(
    'intro' => 'Water Intake Calculator tells you exactly how much water you should drink daily based on your body weight, exercise routine, and climate. Proper hydration improves energy, skin health, digestion, and brain function — but most people drink less water than they need!',
    'howto' => array('Enter your body weight in kilograms.', 'Enter your daily exercise duration in minutes.', 'Select your climate conditions.', 'Click Calculate to see recommended liters and glasses per day.'),
    'features' => array('Personalized based on body weight', 'Exercise adjustment included', 'Climate compensation (hot weather)', 'Easy glass-count conversion'),
    'faqs' => array(
        array('q' => 'How much water should I drink daily?', 'a' => 'A common guideline is 35ml per kg of body weight. So a 70kg person needs about 2.45 liters daily, more with exercise or hot weather. This calculator computes your exact number.'),
        array('q' => 'Do tea and coffee count towards water intake?', 'a' => 'Yes, all fluids contribute to hydration. However, plain water is best since it has no calories, sugar, or caffeine. Aim for most of your intake from water.'),
        array('q' => 'Can I drink too much water?', 'a' => 'Yes, extremely rare but possible — called hyponatremia. Spread intake throughout the day rather than drinking large amounts at once, and increase gradually if increasing intake.'),
    ),
),
'ideal-weight-calculator' => array(
    'intro' => 'Ideal Weight Calculator estimates your healthy body weight range based on your height and gender using established medical formulas (Devine and Robinson). It also shows your healthy BMI range so you have a realistic target zone instead of a single rigid number.',
    'howto' => array('Select your gender.', 'Enter your height in centimeters.', 'Click Calculate.', 'See ideal weight by Devine formula, Robinson formula, and healthy BMI range.'),
    'features' => array('Two scientific formulas (Devine & Robinson)', 'Healthy BMI-based weight range included', 'Gender-specific calculations', 'Realistic target zones, not rigid numbers'),
    'faqs' => array(
        array('q' => 'Why do different formulas give different results?', 'a' => 'Devine and Robinson formulas were developed from different clinical data sets, giving slightly different results. Treat both as reference points within a healthy range rather than exact targets.'),
        array('q' => 'Is ideal weight the same for everyone of same height?', 'a' => 'No. Muscle mass, bone density, and body composition matter. An athletic person may weigh more than the "ideal" yet be perfectly healthy. BMI range offers flexibility.'),
        array('q' => 'Should I aim for the exact ideal weight?', 'a' => 'Focus on staying within the healthy range shown. Sustainable habits matter more than hitting one specific number on the scale.'),
    ),
),
'body-fat-calculator' => array(
    'intro' => 'Body Fat Calculator uses the U.S. Navy Method — the most practical way to estimate body fat percentage at home using just a measuring tape. Unlike BMI, body fat percentage distinguishes muscle from fat, giving a true picture of your fitness level and health risks.',
    'howto' => array('Select gender and enter your age.', 'Measure and enter your height in cm.', 'Measure neck circumference at its narrowest point.', 'Measure waist (and hips for women), then click Calculate.'),
    'features' => array('U.S. Navy circumference method', 'Fitness category classification', 'Lean body mass calculation', 'Only needs a measuring tape'),
    'faqs' => array(
        array('q' => 'How accurate is the Navy Method?', 'a' => 'It is typically within 3-4% of DEXA scan results when measurements are taken correctly. Measure at the same spots, keep tape snug not tight, and repeat for consistency.'),
        array('q' => 'What is a healthy body fat percentage?', 'a' => 'For men: 10-20% is fit, 18-24% average. For women: 18-28% is fit, 25-31% average. Athletes run lower. Essential fat below these ranges is unhealthy.'),
        array('q' => 'Why measure neck and waist?', 'a' => 'The Navy method estimates body volume from circumference ratios. Fat accumulates around the waist while the neck stays relatively lean, allowing fat estimation without expensive equipment.'),
    ),
),

/* ================= EDUCATION ================= */
'cgpa-to-percentage' => array(
    'intro' => 'CGPA to Percentage Converter transforms your Cumulative Grade Point Average into percentage format instantly. Essential for job applications, scholarship forms, and university admissions that require percentage instead of CGPA. Supports 10-point, 5-point, and 4-point scales.',
    'howto' => array('Enter your CGPA value.', 'Select your grading scale (out of 10, 5, or 4).', 'Click Convert.', 'Get instant percentage using standard conversion formulas.'),
    'features' => array('Supports 10, 5, and 4 point scales', 'CBSE official formula (×9.5) included', 'Instant conversion with validation', 'Required for jobs and higher studies'),
    'faqs' => array(
        array('q' => 'How do I convert CGPA to percentage?', 'a' => 'For the 10-point scale, multiply CGPA by 9.5 (CBSE standard). For example, 8.5 CGPA = 8.5 × 9.5 = 80.75%. Linear conversion (CGPA/scale × 100) is also shown.'),
        array('q' => 'What is a good CGPA?', 'a' => 'On a 10-point scale, 8+ CGPA (75%+) is considered very good. On a 4-point scale, 3.5+ is excellent. Requirements vary by institution and purpose.'),
        array('q' => 'Why do universities use CGPA instead of percentage?', 'a' => 'CGPA smooths out minor score variations across semesters and reduces unhealthy competition for decimal-level marks. It represents overall academic consistency better.'),
    ),
),
'grade-calculator' => array(
    'intro' => 'Final Grade Calculator answers the question every student asks: "What do I need on my final exam?" Enter your current grade, the final exam weight, and your target grade — instantly know exactly what score you need to achieve your goal.',
    'howto' => array('Enter your current grade percentage before the final.', 'Enter what percentage of total grade the final exam is worth.', 'Enter the overall grade you want to achieve.', 'Click Calculate to see required exam score.'),
    'features' => array('Exact required score calculation', 'Detects impossible targets honestly', 'Shows maximum achievable grade', 'Reduces exam stress with clear goals'),
    'faqs' => array(
        array('q' => 'How do I calculate what I need on my final?', 'a' => 'Use the formula: Required = (Desired − Current × (1 − Weight)) ÷ Weight. For example, with 78% current, 40% weighted final, wanting 85%: (85 − 78×0.6) ÷ 0.4 = 89.5%.'),
        array('q' => 'What if I need more than 100%?', 'a' => 'That means your target is mathematically impossible with remaining weight. The calculator will tell you the maximum achievable grade so you can adjust expectations realistically.'),
        array('q' => 'Can I use this for multiple exams?', 'a' => 'This handles one final exam. For multiple remaining assessments, combine their weights first or calculate sequentially after each exam.'),
    ),
),
'date-calculator' => array(
    'intro' => 'Date Difference Calculator finds the exact time between any two dates. Perfect for counting days until events, calculating age precisely, tracking project durations, visa validity periods, work anniversaries, or pregnancy weeks. Results show days, weeks, hours, and years/months/days breakdown.',
    'howto' => array('Select the start date.', 'Select the end date.', 'Click Calculate Difference.', 'View results in days, weeks, hours, and Y/M/D format.'),
    'features' => array('Multiple result formats (days, weeks, Y/M/D)', 'Hours calculation included', 'Handles date order automatically', 'Leap-year accurate'),
    'faqs' => array(
        array('q' => 'Are leap years counted correctly?', 'a' => 'Yes! The calculator uses actual calendar dates, so February 29th and leap years are handled accurately automatically.'),
        array('q' => 'Does the end date count as a full day?', 'a' => 'The calculation measures midnight-to-midnight difference. From Jan 1 to Jan 2 equals 1 day. Add 1 manually if you need inclusive counting for both endpoints.'),
        array('q' => 'Can I calculate past dates?', 'a' => 'Absolutely! Any two calendar dates work — historical events, past birthdays, or project start dates. Order is handled automatically.'),
    ),
),
'time-zone-converter' => array(
    'intro' => 'Time Zone Converter instantly converts time between any two world time zones. Essential for international meetings, flight scheduling, calling overseas family, webinar timing, and gaming with global friends. Uses your browser’s built-in timezone database — always accurate with daylight saving handled automatically.',
    'howto' => array('Select the date and time you want to convert.', 'Choose the source time zone (From).', 'Choose the destination time zone (To).', 'Click Convert Time to see corresponding local time.'),
    'features' => array('All worldwide time zones supported', 'Daylight Saving Time auto-handled', 'Your local timezone auto-detected', '"Use Current Time" quick button'),
    'faqs' => array(
        array('q' => 'Does it handle Daylight Saving Time?', 'a' => 'Yes! The converter uses the IANA timezone database built into modern browsers, which includes complete DST rules for every zone automatically.'),
        array('q' => 'What are UTC and GMT?', 'a' => 'UTC (Coordinated Universal Time) is the primary time standard; GMT is effectively equivalent for everyday use. All time zones are expressed as UTC+offset (like UTC+5 for Pakistan).'),
        array('q' => 'Why is meeting scheduling hard across zones?', 'a' => 'Time zones create overlap challenges — when it is morning in New York, it is evening in Karachi. Use this converter to find windows convenient for everyone.'),
    ),
),

/* ================= UTILITY ================= */
'unit-converter' => array(
    'intro' => 'Unit Converter handles all your everyday conversion needs in one place. Convert length (mm to miles), weight (grams to pounds), and temperature (Celsius to Fahrenheit) instantly with precise conversion factors. Perfect for cooking, travel, studies, engineering homework, and international shopping.',
    'howto' => array('Select category: Length, Weight, or Temperature.', 'Enter the value you want to convert.', 'Choose the From unit and To unit.', 'Click Convert for instant precise results.'),
    'features' => array('Length: mm, cm, m, km, inch, feet, yard, mile', 'Weight: mg, g, kg, ton, oz, pound, stone', 'Temperature: Celsius, Fahrenheit, Kelvin', 'High precision conversion factors'),
    'faqs' => array(
        array('q' => 'How do I convert Celsius to Fahrenheit?', 'a' => 'Multiply Celsius by 9/5 then add 32. Example: 37°C = 37×1.8+32 = 98.6°F. Temperature conversions use special formulas — the calculator handles them automatically.'),
        array('q' => 'How many feet in a meter?', 'a' => 'One meter equals approximately 3.28084 feet. Conversely, one foot equals 0.3048 meters exactly by international definition.'),
        array('q' => 'Are conversions exact?', 'a' => 'Conversions use internationally standardized factors and display up to 6 decimal places. Results are as precise as the units themselves allow.'),
    ),
),
'random-number-generator' => array(
    'intro' => 'Random Number Generator produces truly random numbers using cryptographic-grade randomness from your browser. Generate single numbers for decisions, lottery picks, raffle draws, game moves, or sampling — with options for unique-only results and sorted output.',
    'howto' => array('Set your minimum and maximum range.', 'Choose how many numbers to generate (1-100).', 'Enable no-duplicates or sorting options if needed.', 'Click Generate Numbers for cryptographically secure results.'),
    'features' => array('Cryptographic-grade randomness (crypto API)', 'Generate up to 100 numbers at once', 'Unique-only mode prevents repeats', 'Optional ascending sort'),
    'faqs' => array(
        array('q' => 'How random are these numbers?', 'a' => 'We use your browser’s crypto.getRandomValues() API — the same cryptographic randomness used for security keys. Far superior to basic Math.random().'),
        array('q' => 'Can I use this for giveaways?', 'a' => 'Absolutely! Enable no-duplicates, set your participant count as the range, generate winners. For transparency, screen-record the draw.'),
        array('q' => 'What happens if range is smaller than count?', 'a' => 'With unique-mode ON, the generator warns you when unique numbers exceed the possible range. Turn duplicates ON to allow repeated values.'),
    ),
),
'password-generator' => array(
    'intro' => 'Password Generator creates strong, uncrackable passwords using cryptographic randomness. Customize length and character types (uppercase, lowercase, numbers, symbols) with live strength metering. Protect your accounts from brute-force and dictionary attacks with genuinely random passwords.',
    'howto' => array('Drag the slider to set password length (6-64 characters).', 'Check which character types to include.', 'Click Generate Password.', 'Copy your password with one click and store it safely.'),
    'features' => array('Cryptographically secure generation', 'Length up to 64 characters', 'Full character customization', 'Entropy-based strength meter'),
    'faqs' => array(
        array('q' => 'What makes a password strong?', 'a' => 'Length matters most — each added character multiplies cracking difficulty. Mix character types and avoid dictionary words, names, or patterns. 16+ random characters is excellent.'),
        array('q' => 'Are these passwords stored anywhere?', 'a' => 'Never! Generation happens entirely in your browser using JavaScript. Nothing is transmitted or saved. Refresh produces a completely new password.'),
        array('q' => 'Should I use the same password everywhere?', 'a' => 'Definitely not. One breach would compromise everything. Use a unique generated password per site, managed through a password manager.'),
    ),
),
'word-counter' => array(
    'intro' => 'Word Counter analyzes your text instantly as you type — counting words, characters (with and without spaces), sentences, paragraphs, and estimated reading time. Essential for essays with limits, tweets, meta descriptions, blog posts, and content writing deadlines.',
    'howto' => array('Paste or type your text into the box.', 'Watch live statistics update automatically.', 'Click Count Words for detailed stats.', 'Clear button resets everything.'),
    'features' => array('Live counting while typing', 'Words, characters, sentences, paragraphs', 'Reading time estimation (200 wpm)', 'Characters with/without spaces separated'),
    'faqs' => array(
        array('q' => 'How is reading time calculated?', 'a' => 'Average adult reading speed is about 200-250 words per minute. We use 200 wpm for conservative estimates — useful for speeches and presentations.'),
        array('q' => 'Does it count spaces as characters?', 'a' => 'Both counts shown separately! Total characters include spaces (useful for Twitter/SMS limits); no-spaces count excludes whitespace.'),
        array('q' => 'Is my text uploaded anywhere?', 'a' => 'No! All counting happens locally in your browser. Your text never leaves your device — safe for confidential documents.'),
    ),
),

/* ================= TEXT TOOLS ================= */
'case-converter' => array(
    'intro' => 'Case Converter transforms text between nine different cases instantly — UPPERCASE, lowercase, Title Case, Sentence case, camelCase, PascalCase, snake_case, kebab-case, and aLtErNaTiNg case. Perfect for writers, programmers, students, and social media formatting.',
    'howto' => array('Paste or type your text in the input box.', 'Click any case button (UPPERCASE, Title Case, etc.).', 'See converted text appear instantly below.', 'Copy the result with one click.'),
    'features' => array('Nine case conversions available', 'Programmer formats: camelCase, snake_case, kebab-case', 'One-click copy output', 'Handles long documents easily'),
    'faqs' => array(
        array('q' => 'What is camelCase used for?', 'a' => 'camelCase names variables in JavaScript and Java (myVariableName). snake_case is common in Python, while kebab-case names CSS classes and URLs.'),
        array('q' => 'Will formatting be preserved?', 'a' => 'Letter case changes; words stay intact. Sentence case capitalizes after sentence endings, while Title Case capitalizes every word.'),
        array('q' => 'Can I convert long documents?', 'a' => 'Yes! JavaScript handles thousands of words instantly. Copy entire articles or chapter drafts without issues.'),
    ),
),
'number-to-words' => array(
    'intro' => 'Number to Words Converter spells out numbers in English words — essential for writing cheques, legal documents, invoices, and formal applications. Supports Pakistani/Indian Rupees with Lakh-Crore system AND international Million-Billion format with currency-specific outputs ending in "Only".',
    'howto' => array('Enter any number up to trillions.', 'Select currency: PKR, INR, USD, or Plain.', 'Click Convert to Words.', 'Copy the properly formatted cheque-ready text.'),
    'features' => array('Lakh-Crore system for PKR/INR', 'Million-Billion system for USD', 'Paisa/Cents handling for decimals', 'Cheque format with "Only" suffix'),
    'faqs' => array(
        array('q' => 'How do I write amounts on cheques?', 'a' => 'Banks require words after figures. Select PKR or INR, enter amount, copy the output like "Rupees Two Lakh Fifty Thousand Only" directly onto your cheque.'),
        array('q' => 'What is the difference between systems?', 'a' => 'South Asian system groups digits as thousands-lakh(100k)-crore(10M). International groups as thousand-million-billion. Same number, different naming!'),
        array('q' => 'What is the maximum supported number?', 'a' => 'Up to 999 Trillion (or corresponding Crore Kharab equivalents) — far beyond any practical cheque or document requirement.'),
    ),
),
'duplicate-line-remover' => array(
    'intro' => 'Duplicate Line Remover cleans lists by eliminating repeated lines instantly. Perfect for deduplicating email lists, keyword lists, log entries, CSV data, code imports, or any line-based text. Smart options handle case sensitivity, whitespace, empty lines, and sorting.',
    'howto' => array('Paste your list with duplicates (one item per line).', 'Toggle options: trim, ignore case, remove empties, sort.', 'Click Remove Duplicates.', 'Copy your clean, unique list.'),
    'features' => array('Case-sensitive and insensitive modes', 'Automatic whitespace trimming', 'Empty line cleanup', 'Statistics: removed count display'),
    'faqs' => array(
        array('q' => 'Does "Apple" match "apple"?', 'a' => 'With "Ignore letter case" checked, yes — they become duplicates. Uncheck it to treat different capitalizations as distinct entries.'),
        array('q' => 'Will my list order change?', 'a' => 'Duplicates keep first occurrence order unless you enable Sort A-Z. Original sequence preservation is default behavior.'),
        array('q' => 'Can it handle huge files?', 'a' => 'Thousands of lines process instantly. For extremely large files (100k+ lines), browser memory becomes the limit — split such files first.'),
    ),
),
'lorem-ipsum-generator' => array(
    'intro' => 'Lorem Ipsum Generator creates placeholder text for designs, mockups, templates, and layouts. Generate paragraphs, sentences, or words on demand — the industry-standard dummy text since the 1500s that lets clients focus on design rather than reading content.',
    'howto' => array('Choose output type: paragraphs, sentences, or words.', 'Set quantity (1-100).', 'Click Generate Text.', 'Copy placeholder text into your design.'),
    'features' => array('Paragraphs, sentences, or words mode', 'Classic Lorem Ipsum vocabulary', 'Starts with traditional opening', 'Instant generation, easy copying'),
    'faqs' => array(
        array('q' => 'What is Lorem Ipsum?', 'a' => 'Lorem Ipsum is scrambled Latin from Cicero’s writings circa 45 BC. It became printing-industry filler text and remains the web-design standard for placeholder copy.'),
        array('q' => 'Why not use real text in mockups?', 'a' => 'Readable content distracts reviewers who start editing words instead of evaluating layout. Lorem Ipsum keeps attention on design structure.'),
        array('q' => 'Do search engines penalize Lorem Ipsum pages?', 'a' => 'Placeholder text on LIVE websites looks unfinished to Google. Always replace dummy text with real content before launching!'),
    ),
),
'text-reverser' => array(
    'intro' => 'Text Reverser flips your text three ways: reverse all letters (dlroW olleH), reverse letters within each word keeping order (olleH dlroW), or reverse word order (World Hello). Fun for puzzles, social media posts, testing palindrome detection, and creative writing effects.',
    'howto' => array('Type or paste your text.', 'Pick reversal mode: Letters, Each Word, or Word Order.', 'See reversed output instantly.', 'Copy the fun result anywhere.'),
    'features' => array('Three reversal modes', 'Unicode-safe letter handling', 'Word-order flip preserves spelling', 'Great for games and puzzles'),
    'faqs' => array(
        array('q' => 'What are palindromes?', 'a' => 'Words spelled identically forwards and backwards — like "level", "madam", "racecar". Reverse them here to verify they remain unchanged!'),
        array('q' => 'Does emoji reverse correctly?', 'a' => 'Modern emoji use multi-byte sequences. Letter-reversal of emoji-containing text may produce combined characters — stick to letters for clean reversal.'),
        array('q' => 'Why reverse word order?', 'a' => 'Reversed sentences create interesting headlines, test parsing logic, or generate mirror-writing effects for artistic posts.'),
    ),
),
'remove-line-breaks' => array(
    'intro' => 'Remove Line Breaks joins broken text into continuous flow — fixing PDF copy-paste disasters, cleaning wrapped emails, preparing text for spreadsheets, and merging poem-style formatting. Replace breaks with spaces, commas, nothing, or custom separators.',
    'howto' => array('Paste text containing unwanted line breaks.', 'Choose replacement: space, comma, none, or custom.', 'Click Remove Line Breaks.', 'Copy the joined single-line result.'),
    'features' => array('Space/comma/nothing/custom replacement', 'Fixes PDF copy-paste fragmentation', 'Single-click processing', 'Handles paragraphs of any length'),
    'faqs' => array(
        array('q' => 'Why does PDF text break weirdly?', 'a' => 'PDFs store fixed-position text; copying inserts line breaks at visual wrap points. Removing breaks reconstructs flowing sentences.'),
        array('q' => 'When should I use comma replacement?', 'a' => 'Converting column lists (names, emails, tags) into comma-separated values ready for CSV import or tag fields.'),
        array('q' => 'Does paragraph structure survive?', 'a' => 'All breaks collapse uniformly. For keeping paragraph gaps, process paragraph-by-paragraph or use custom replacement like double-space.'),
    ),
),

/* ================= FUN ================= */
'love-calculator' => array(
    'intro' => 'Love Calculator matches two names and reveals your compatibility percentage with a fun message! Enter your name and partner’s name to discover your love score. Pure entertainment for parties, crushes, and giggles — same names always produce the same score!',
    'howto' => array('Type your name in the first box.', 'Type your partner’s name in the second box.', 'Click Calculate Love!', 'Share your compatibility score with friends!'),
    'features' => array('Deterministic scores (same names = same result)', 'Fun messages for every score range', 'Instant results with emojis', 'Great party entertainment'),
    'faqs' => array(
        array('q' => 'Is the love score real?', 'a' => 'It is pure entertainment! Scores come from a name-based algorithm — real relationships depend on trust, respect, communication, and shared values, not calculators!'),
        array('q' => 'Why do the same names always score identically?', 'a' => 'We use deterministic hashing, so "Ali + Sara" always yields the same percentage. Consistency makes results feel special and shareable.'),
        array('q' => 'Can I test friendship compatibility?', 'a' => 'Sure! Enter any two names — friends, celebrities, pets. The algorithm happily calculates playful scores for any pairing.'),
    ),
),
'birthday-countdown' => array(
    'intro' => 'Birthday Countdown tells you exactly how many days until your next birthday, plus fascinating birthday facts — your precise age in years/months/days, total days lived, the weekday you were born, and your zodiac sign. Celebrate knowing your special day approaches!',
    'howto' => array('Select your date of birth.', 'Click Calculate.', 'See countdown, precise age, zodiac sign, and birth weekday.', 'Plan your celebration accordingly!'),
    'features' => array('Live countdown to next birthday', 'Precise age in Y/M/D format', 'Zodiac sign detection', 'Total days lived counter'),
    'faqs' => array(
        array('q' => 'What is my zodiac sign?', 'a' => 'Zodiac depends on birth month/day: Aries (Mar21-Apr19), Taurus (Apr20-May20), Gemini (May21-Jun20), Cancer (Jun21-Jul22), Leo (Jul23-Aug22), Virgo (Aug23-Sep22), Libra (Sep23-Oct22), Scorpio (Oct23-Nov21), Sagittarius (Nov22-Dec21), Capricorn (Dec22-Jan19), Aquarius (Jan20-Feb18), Pisces (Feb19-Mar20).'),
        array('q' => 'What about February 29 birthdays?', 'a' => 'Leaplings celebrate March 1st in common years (JavaScript date rolling). Your countdown adjusts automatically every year.'),
        array('q' => 'Why does weekday-of-birth matter?', 'a' => 'It is a popular trivia fact — people love knowing they arrived on a Friday! The calculator derives it precisely from calendar math.'),
    ),
),

/* ================= ISLAMIC ================= */
'zakat-calculator' => array(
    'intro' => 'Zakat Calculator helps Muslims fulfill their third pillar accurately. Enter your assets — gold, silver, cash, investments, business stock, and receivables minus debts — then compare against today’s Nisab threshold (87.48g gold or 612.36g silver) to compute your 2.5% Zakat obligation.',
    'howto' => array('Enter today’s gold and silver rates per gram.', 'Fill in values of all your zakatable assets.', 'Enter debts/liabilities to deduct.', 'Select Nisab standard and click Calculate Zakat.'),
    'features' => array('Gold and Silver Nisab standards', 'Complete asset categories covered', 'Debt deduction support', 'Below-Nisab detection'),
    'faqs' => array(
        array('q' => 'What is Nisab?', 'a' => 'Nisab is the minimum wealth threshold making Zakat obligatory: 87.48 grams of gold OR 612.36 grams of silver held for one lunar year. Silver Nisab is commonly recommended as it benefits more recipients.'),
        array('q' => 'What is the Zakat rate?', 'a' => 'Zakat is 2.5% (one-fortieth) of qualifying wealth held above Nisab for a full hijri year. Some assets like agricultural produce use different rates (5-10%).'),
        array('q' => 'Do I pay Zakat on my home or car?', 'a' => 'Personal-use items — your residence, car, clothing, furniture — are exempt. Zakat applies to wealth-generating or growable assets: savings, gold/jewelry, investments, business inventory, and receivables.'),
    ),
),
'electricity-bill-calculator' => array(
    'intro' => 'Electricity Bill Calculator estimates your Pakistan electricity bill from consumed units. Supporting Protected and Unprotected domestic tariffs with editable slab rates, FPA, GST, electricity duty, TV fee, and meter rent — plan your budget and understand exactly where your bill comes from.',
    'howto' => array('Read units consumed from your meter or previous bill.', 'Select consumer type: Protected or Unprotected.', 'Expand advanced options to adjust rates if needed.', 'Click Calculate Bill for complete breakdown.'),
    'features' => array('Pakistan domestic slab structure', 'Protected/Unprotected consumer types', 'Editable FPA, GST, duty rates', 'Slab-wise cost breakdown display'),
    'faqs' => array(
        array('q' => 'What are protected consumers?', 'a' => 'Households consuming ≤200 units monthly for six consecutive months qualify for subsidized "protected" rates — cheaper per-unit costs. Exceeding 200 units shifts you to unprotected tariff.'),
        array('q' => 'What is FPA on my bill?', 'a' => 'Fuel Price Adjustment passes fuel-cost fluctuations from power generation to consumers monthly. It varies (sometimes negative!) and appears as a separate line item.'),
        array('q' => 'Why does my actual bill differ?', 'a' => 'Actual bills add QTA adjustments, taxes, surcharges, and utility-specific variations. This calculator estimates core charges — treat it as a planning approximation.'),
    ),
),

/* ================= DEVELOPER ================= */
'json-formatter' => array(
    'intro' => 'JSON Formatter beautifies, minifies, and validates JSON data instantly. Debug API responses, clean config files, and prepare readable documentation. Error messages pinpoint problems, making JSON troubleshooting painless for developers of every level.',
    'howto' => array('Paste your JSON into the input area.', 'Choose Beautify, Minify, or Validate.', 'Select indent preference (2 spaces, 4 spaces, tab).', 'Copy formatted output or fix reported errors.'),
    'features' => array('Beautify with configurable indentation', 'Minify for production payloads', 'Validation with descriptive errors', 'Client-side processing — data stays private'),
    'faqs' => array(
        array('q' => 'What is JSON?', 'a' => 'JSON (JavaScript Object Notation) is the lightweight, human-readable data interchange format powering modern APIs, configurations, and data storage across virtually all platforms.'),
        array('q' => 'Common JSON errors?', 'a' => 'Trailing commas, single quotes instead of double, unquoted keys, and unescaped newlines inside strings top the list. The validator reports exact error descriptions.'),
        array('q' => 'Is my API data safe here?', 'a' => 'Completely! Formatting executes purely client-side via JavaScript. Sensitive API responses never leave your browser — safe even for production data.'),
    ),
),
'base64-encoder-decoder' => array(
    'intro' => 'Base64 Encoder/Decoder converts text to Base64 encoding and back — Unicode-safe for all languages including Urdu and emoji. Essential for data URIs, basic auth headers, JWT inspection, email attachments understanding, and embedded image encoding.',
    'howto' => array('Paste text (encode) or Base64 string (decode).', 'Click Encode or Decode direction button.', 'View result in output box.', 'Copy with one click.'),
    'features' => array('Full Unicode/UTF-8 support', 'Bidirectional encode/decode', 'Handles multiline input', 'Error detection for invalid Base64'),
    'faqs' => array(
        array('q' => 'Is Base64 encryption?', 'a' => 'No! Base64 is encoding, not encryption — trivially reversible with zero security. Never "protect" secrets by merely Base64-encoding them.'),
        array('q' => 'Why do = signs appear?', 'a' => 'Base64 pads input to 4-character blocks using = signs. One pad for 2 leftover bytes, two pads for 1. Pads decode transparently.'),
        array('q' => 'Why did decoding fail?', 'a' => 'Invalid characters outside the Base64 alphabet (A-Z, a-z, 0-9, +, /, =) or corrupted padding cause failures. Check for stray spaces or truncation.'),
    ),
),
'url-encoder-decoder' => array(
    'intro' => 'URL Encoder/Decoder makes text URL-safe and decodes percent-encoded strings back to readable form. Encode query parameters, UTM campaigns, and special characters (&, ?, #, spaces) correctly, or decode those %20 mysteries in analytics links.',
    'howto' => array('Paste text or encoded URL.', 'Click Encode, Decode, or Encode Full URL.', 'Review converted output.', 'Copy the URL-safe string.'),
    'features' => array('Component vs full-URL encoding modes', 'Decode with + as space handling', 'Special character safety (&?#)', 'Essential for UTM link building'),
    'faqs' => array(
        array('q' => 'When do I need URL encoding?', 'a' => 'Whenever values contain reserved characters: spaces become %20, & becomes %26, # becomes %23. Query parameter VALUES must be encoded to avoid breaking URL structure.'),
        array('q' => 'Encode component vs full URL?', 'a' => 'Component mode encodes everything including :// — for parameter values. Full-URL mode preserves structural characters — for complete links needing partial safety.'),
        array('q' => 'Why do + signs appear when decoding?', 'a' => 'In form submissions (application/x-www-form-urlencoded), spaces traditionally encode as +. We translate + back to space during decoding for compatibility.'),
    ),
),
'hash-generator' => array(
    'intro' => 'Hash Generator computes MD5, SHA-1, SHA-256, SHA-384, and SHA-512 hashes simultaneously using Web Crypto API. Verify file integrity, store password digests, generate checksums, and learn hash properties — all computed locally in your browser.',
    'howto' => array('Enter any text to hash.', 'Check desired algorithms (MD5, SHA family).', 'Click Generate Hashes.', 'Copy individual hash values as needed.'),
    'features' => array('Five algorithms at once', 'Cryptographic Web Crypto engine', 'Lowercase hex output standard', 'Local computation — private forever'),
    'faqs' => array(
        array('q' => 'MD5 vs SHA-256 — which should I use?', 'a' => 'MD5 and SHA-1 are cryptographically broken for security purposes (collisions demonstrated). Use SHA-256+ for integrity checks; neither raw hash suits password storage — use bcrypt/argon2.'),
        array('q' => 'Can hashes be reversed?', 'a' => 'Cryptographically no — hashing is one-way. Rainbow tables crack weak/unsalted passwords though, which is why proper systems add salts and slow algorithms.'),
        array('q' => 'Same text always same hash?', 'a' => 'Yes! Hash functions are deterministic — identical input yields identical output regardless of time or machine. That property enables file verification.'),
    ),
),
'uuid-generator' => array(
    'intro' => 'UUID Generator creates RFC-compliant UUID v4 identifiers using cryptographic randomness. Generate single IDs or batches up to 100 with uppercase and brace-wrapping options. Standard for database keys, distributed systems, API correlation IDs, and file naming.',
    'howto' => array('Set quantity (1-100 UUIDs).', 'Toggle uppercase or braces options.', 'Click Generate UUIDs.', 'Copy the full batch instantly.'),
    'features' => array('RFC 4122 v4 compliant', 'Crypto-quality randomness', 'Batch generation up to 100', 'Format options: uppercase, {braces}'),
    'faqs' => array(
        array('q' => 'What is a UUID v4?', 'a' => 'Version 4 UUIDs are 128-bit identifiers with 122 random bits — format xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx. Collision odds are astronomically low: you could generate billions for centuries without repeating.'),
        array('q' => 'UUID v4 vs auto-increment IDs?', 'a' => 'UUIDs enable offline generation without coordination, merge cleanly across systems, and hide record counts. Auto-increments sort naturally but leak information and need central coordination.'),
        array('q' => 'Are generated UUIDs stored?', 'a' => 'Never! Everything generates client-side via window.crypto. Your UUIDs exist only in your browser until copied elsewhere.'),
    ),
),
'color-converter' => array(
    'intro' => 'Color Converter translates colors between HEX, RGB, and HSL formats with live preview. Pick visually from the color wheel or paste hex codes — instantly get all developer-friendly formats for CSS, design tools, and brand guideline compliance.',
    'howto' => array('Pick color from the wheel or paste HEX value.', 'See RGB and HSL update live.', 'Check the preview swatch.', 'Copy any format with its copy button.'),
    'features' => array('HEX ↔ RGB ↔ HSL conversion', 'Interactive color picker wheel', 'Live preview swatch', 'Individual format copy buttons'),
    'faqs' => array(
        array('q' => 'HEX vs RGB vs HSL — when to use which?', 'a' => 'HEX (#4F46E5) dominates CSS/design handoffs. RGB suits opacity manipulation. HSL shines for programmatic variations — lightness tweaks create hover shades elegantly.'),
        array('q' => 'Can I enter 3-digit hex codes?', 'a' => 'Yes! Shorthand #F53 expands to #FF5533 automatically. Both formats accepted in the hex input field.'),
        array('q' => 'What does HSL stand for?', 'a' => 'Hue (color angle 0-360°), Saturation (vividness %), Lightness (% black-white mix). Intuitive for humans compared to RGB’s additive channels.'),
    ),
),

/* ================= NEW: PDF CONVERSION SUITE =================
   Added because none of the 21 PDF-to-X / X-to-PDF tool pages had
   curated content before this — every one of them was falling
   through to the generic 'general' category template, despite
   PDF to Word being the #1 priority page in this SEO pass. ================= */
'pdf-to-word' => array(
    'intro' => 'PDF to Word turns the text in a PDF into an editable Word document. Best for contracts, resumes, or reports you need to edit rather than just read. Complex layouts (multi-column pages, unusual fonts) may need minor formatting cleanup after conversion — that\'s normal for any PDF-to-Word conversion, not specific to this tool.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your Word (.docx) file.'),
    'features' => array('Recovers editable, selectable text from a PDF', 'Keeps paragraph structure and basic formatting', 'Works with multi-page documents', 'No software installation needed'),
    'faqs' => array(
        array('q' => 'Is this PDF to Word tool free?', 'a' => 'Yes — converting PDF to Word (.docx) here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to Word keeps as much of the original layout as the Word (.docx) format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'word-to-pdf' => array(
    'intro' => 'Word to PDF converts a Word document into a fixed-layout PDF. The PDF looks identical on every device and printer, which is why it\'s the standard format for submitting finished documents — job applications, signed forms, official reports.',
    'howto' => array('Upload your Word (.doc/.docx) file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PDF file.'),
    'features' => array('Layout, fonts and spacing stay locked', 'Readable on any device without Word installed', 'Smaller file size than the original document in most cases', 'Ready to print or email as-is'),
    'faqs' => array(
        array('q' => 'Is this Word to PDF tool free?', 'a' => 'Yes — converting Word (.doc/.docx) to PDF here is free, with no sign-up or account required.'),
        array('q' => 'Will my Word (.doc/.docx) file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'Word to PDF keeps as much of the original layout as the PDF format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-jpg' => array(
    'intro' => 'PDF to JPG renders each page of a PDF as a separate JPG image. Useful when someone only needs to view or share a page — on WhatsApp, social media, or a website — rather than edit it. Multi-page PDFs produce one JPG per page.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your JPG image(s) file.'),
    'features' => array('One JPG image created per PDF page', 'Small file size, easy to share anywhere', 'No PDF reader needed to view the result', 'Good for previews and thumbnails'),
    'faqs' => array(
        array('q' => 'Is this PDF to JPG tool free?', 'a' => 'Yes — converting PDF to JPG image(s) here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to JPG keeps as much of the original layout as the JPG image(s) format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'jpg-to-pdf' => array(
    'intro' => 'JPG to PDF combines one or more JPG images into a single PDF document. Commonly used to turn phone-camera photos of paperwork — receipts, ID cards, handwritten notes — into one shareable PDF instead of several separate image files.',
    'howto' => array('Upload your JPG image(s) file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PDF file.'),
    'features' => array('Combines multiple images into one document', 'Keeps original image quality', 'Produces a single file that\'s easy to email', 'No page-size guesswork — sized automatically'),
    'faqs' => array(
        array('q' => 'Is this JPG to PDF tool free?', 'a' => 'Yes — converting JPG image(s) to PDF here is free, with no sign-up or account required.'),
        array('q' => 'Will my JPG image(s) file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'JPG to PDF keeps as much of the original layout as the PDF format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-excel' => array(
    'intro' => 'PDF to Excel extracts tabular data from a PDF into an editable spreadsheet. Works best on PDFs with a clear table structure (bank statements, invoices, price lists). PDFs with merged cells or scanned (non-text) tables may need a manual check afterward — that\'s a limitation of table extraction in general, not just this tool.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your Excel (.xlsx) file.'),
    'features' => array('Extracts rows and columns into real spreadsheet cells', 'Numbers stay editable, not just text', 'Handles multi-page tables', 'Ready for formulas and pivot tables afterward'),
    'faqs' => array(
        array('q' => 'Is this PDF to Excel tool free?', 'a' => 'Yes — converting PDF to Excel (.xlsx) here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to Excel keeps as much of the original layout as the Excel (.xlsx) format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'excel-to-pdf' => array(
    'intro' => 'Excel to PDF converts a spreadsheet into a fixed, print-ready PDF. Keeps column widths and page breaks the way you set them, so the PDF prints the way the spreadsheet actually looks — useful for invoices, reports, and anything shared outside your team.',
    'howto' => array('Upload your Excel (.xlsx/.xls) file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PDF file.'),
    'features' => array('Preserves column layout and page breaks', 'Looks the same on any device', 'No risk of a recipient accidentally editing your numbers', 'Ready to print or attach to an email'),
    'faqs' => array(
        array('q' => 'Is this Excel to PDF tool free?', 'a' => 'Yes — converting Excel (.xlsx/.xls) to PDF here is free, with no sign-up or account required.'),
        array('q' => 'Will my Excel (.xlsx/.xls) file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'Excel to PDF keeps as much of the original layout as the PDF format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-powerpoint' => array(
    'intro' => 'PDF to PowerPoint turns each page of a PDF into an editable slide. Handy when you\'ve been given a PDF handout or a scanned deck and need to edit or present it — each PDF page becomes its own slide you can rearrange and edit in PowerPoint.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PowerPoint (.pptx) file.'),
    'features' => array('One slide created per PDF page', 'Slides open directly in PowerPoint or Google Slides', 'Lets you edit content that was previously locked in a PDF', 'Keeps page order intact'),
    'faqs' => array(
        array('q' => 'Is this PDF to PowerPoint tool free?', 'a' => 'Yes — converting PDF to PowerPoint (.pptx) here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to PowerPoint keeps as much of the original layout as the PowerPoint (.pptx) format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'powerpoint-to-pdf' => array(
    'intro' => 'PowerPoint to PDF flattens a presentation into a PDF anyone can open. Useful for sending slides to people who don\'t have PowerPoint installed, or for locking a deck\'s design and slide numbering before it goes out.',
    'howto' => array('Upload your PowerPoint (.ppt/.pptx) file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PDF file.'),
    'features' => array('Every slide becomes one fixed PDF page', 'Fonts and layout can\'t shift on another device', 'Opens instantly with any PDF reader', 'Good for archiving a final version of a deck'),
    'faqs' => array(
        array('q' => 'Is this PowerPoint to PDF tool free?', 'a' => 'Yes — converting PowerPoint (.ppt/.pptx) to PDF here is free, with no sign-up or account required.'),
        array('q' => 'Will my PowerPoint (.ppt/.pptx) file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PowerPoint to PDF keeps as much of the original layout as the PDF format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-png' => array(
    'intro' => 'PDF to PNG renders each PDF page as a lossless PNG image. PNG keeps sharper detail than JPG at the cost of a larger file, which matters for pages with diagrams, screenshots or text-heavy graphics rather than photos.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PNG image(s) file.'),
    'features' => array('One PNG image created per PDF page', 'Lossless quality — no compression artifacts', 'Supports transparency where relevant', 'Good for graphics, diagrams and screenshots'),
    'faqs' => array(
        array('q' => 'Is this PDF to PNG tool free?', 'a' => 'Yes — converting PDF to PNG image(s) here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to PNG keeps as much of the original layout as the PNG image(s) format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'png-to-pdf' => array(
    'intro' => 'PNG to PDF combines one or more PNG images into a single PDF. A common use is bundling multiple screenshots or scanned pages — each already a PNG — into one PDF you can send as a single file.',
    'howto' => array('Upload your PNG image(s) file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PDF file.'),
    'features' => array('Combines multiple PNGs into one document', 'Keeps original image sharpness', 'Produces a single, easy-to-share file', 'Automatic page sizing per image'),
    'faqs' => array(
        array('q' => 'Is this PNG to PDF tool free?', 'a' => 'Yes — converting PNG image(s) to PDF here is free, with no sign-up or account required.'),
        array('q' => 'Will my PNG image(s) file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PNG to PDF keeps as much of the original layout as the PDF format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-text' => array(
    'intro' => 'PDF to Text extracts the raw text content of a PDF, with no formatting or images. Strips everything down to plain text — useful when you need to copy-edit the wording, run a word count, or feed the content into another text tool, and don\'t care about layout.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your plain text (.txt) file.'),
    'features' => array('Extracts text only — no images or formatting', 'Output is a plain, universally-readable .txt file', 'Ideal for copy-editing or further text processing', 'Works on multi-page PDFs'),
    'faqs' => array(
        array('q' => 'Is this PDF to Text tool free?', 'a' => 'Yes — converting PDF to plain text (.txt) here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to Text keeps as much of the original layout as the plain text (.txt) format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'text-to-pdf' => array(
    'intro' => 'Text to PDF wraps plain text content into a simple, formatted PDF. Turns a bare .txt file — notes, a script output, a plain draft — into a PDF that\'s easier to read, print, or send than raw text.',
    'howto' => array('Upload your plain text (.txt) file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PDF file.'),
    'features' => array('Adds basic, readable formatting automatically', 'Produces a document that opens the same everywhere', 'No word processor required', 'Good for quick notes or logs you need to share'),
    'faqs' => array(
        array('q' => 'Is this Text to PDF tool free?', 'a' => 'Yes — converting plain text (.txt) to PDF here is free, with no sign-up or account required.'),
        array('q' => 'Will my plain text (.txt) file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'Text to PDF keeps as much of the original layout as the PDF format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-html' => array(
    'intro' => 'PDF to HTML converts PDF content into an HTML page. Useful for pulling a PDF\'s content into a web page or an HTML editor, where it\'s easier to restyle, embed, or update than inside a locked PDF.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your HTML file.'),
    'features' => array('Keeps basic document structure (headings, paragraphs)', 'Output can be opened in any browser or HTML editor', 'Easier to restyle than editing a PDF directly', 'Good starting point for republishing content online'),
    'faqs' => array(
        array('q' => 'Is this PDF to HTML tool free?', 'a' => 'Yes — converting PDF to HTML here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to HTML keeps as much of the original layout as the HTML format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'html-to-pdf' => array(
    'intro' => 'HTML to PDF renders an HTML page or document into a fixed PDF. Common for saving a receipt, invoice, or article as a permanent, unchangeable file — once it\'s a PDF, it looks the same regardless of any future change to the original page.',
    'howto' => array('Upload your HTML file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PDF file.'),
    'features' => array('Freezes a web page\'s current appearance', 'Result can\'t be edited by whoever opens it', 'Opens on any device with a PDF reader', 'Good for receipts, invoices and saved records'),
    'faqs' => array(
        array('q' => 'Is this HTML to PDF tool free?', 'a' => 'Yes — converting HTML to PDF here is free, with no sign-up or account required.'),
        array('q' => 'Will my HTML file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'HTML to PDF keeps as much of the original layout as the PDF format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-csv' => array(
    'intro' => 'PDF to CSV extracts tabular data from a PDF as raw comma-separated values. CSV is the plainest possible data format — no styling, just rows and commas — which makes it the easiest way to pull a PDF\'s table into a database, script, or another program that expects raw data rather than a formatted spreadsheet.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your CSV file.'),
    'features' => array('Outputs plain, universally-importable data rows', 'Works with any spreadsheet or database software', 'No spreadsheet formatting to strip out later', 'Good for scripts and data pipelines'),
    'faqs' => array(
        array('q' => 'Is this PDF to CSV tool free?', 'a' => 'Yes — converting PDF to CSV here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to CSV keeps as much of the original layout as the CSV format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-xml' => array(
    'intro' => 'PDF to XML extracts a PDF\'s content and structure into XML markup. Aimed at developers who need to parse a PDF\'s content programmatically — XML\'s tags preserve more structure than plain text, which makes automated processing easier.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your XML file.'),
    'features' => array('Preserves document structure as machine-readable tags', 'Easier to parse in code than raw PDF text', 'Useful for automated document-processing pipelines', 'Works on multi-page PDFs'),
    'faqs' => array(
        array('q' => 'Is this PDF to XML tool free?', 'a' => 'Yes — converting PDF to XML here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to XML keeps as much of the original layout as the XML format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-svg' => array(
    'intro' => 'PDF to SVG converts each PDF page into a scalable vector image. Unlike a JPG or PNG, an SVG stays crisp at any zoom level or print size, which matters for pages with line art, diagrams or logos that a designer needs to resize.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your SVG file.'),
    'features' => array('Vector output stays sharp at any size or zoom', 'Ideal for logos, diagrams and line art', 'One SVG created per PDF page', 'Opens in any browser or vector design tool'),
    'faqs' => array(
        array('q' => 'Is this PDF to SVG tool free?', 'a' => 'Yes — converting PDF to SVG here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to SVG keeps as much of the original layout as the SVG format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-webp' => array(
    'intro' => 'PDF to WebP renders each PDF page as a WebP image. WebP is a newer image format that\'s typically smaller than an equivalent-quality JPG or PNG, which makes it a good fit for pages you\'re publishing on a website where load time matters.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your WebP image(s) file.'),
    'features' => array('One WebP image created per PDF page', 'Typically smaller file size than JPG at similar quality', 'Well supported by modern browsers', 'Good for web publishing'),
    'faqs' => array(
        array('q' => 'Is this PDF to WebP tool free?', 'a' => 'Yes — converting PDF to WebP image(s) here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to WebP keeps as much of the original layout as the WebP image(s) format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'webp-to-pdf' => array(
    'intro' => 'WebP to PDF combines one or more WebP images into a single PDF. Lets you bundle WebP images — commonly saved from websites or modern phone cameras — into one shareable PDF document.',
    'howto' => array('Upload your WebP image(s) file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PDF file.'),
    'features' => array('Combines multiple WebP images into one document', 'Keeps original image quality', 'Produces a single file that\'s easy to share', 'Automatic page sizing per image'),
    'faqs' => array(
        array('q' => 'Is this WebP to PDF tool free?', 'a' => 'Yes — converting WebP image(s) to PDF here is free, with no sign-up or account required.'),
        array('q' => 'Will my WebP image(s) file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'WebP to PDF keeps as much of the original layout as the PDF format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-pdf-a' => array(
    'intro' => 'PDF to PDF/A converts a regular PDF into PDF/A, the ISO-standardised format for long-term archiving. PDF/A embeds fonts and locks down features that can break over time, which is why courts, libraries and records-management systems specifically require it for documents that must remain readable decades from now.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your PDF/A file.'),
    'features' => array('Meets the ISO 19005 archival standard', 'Embeds fonts so the file can\'t lose its appearance later', 'Accepted by legal and records-retention systems', 'Same visual content as the original PDF'),
    'faqs' => array(
        array('q' => 'Is this PDF to PDF/A tool free?', 'a' => 'Yes — converting PDF to PDF/A here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to PDF/A keeps as much of the original layout as the PDF/A format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),

'pdf-to-epub' => array(
    'intro' => 'PDF to EPUB converts a PDF into the EPUB e-book format. EPUB text reflows to fit any screen size, which is why e-readers and reading apps (Apple Books, Kobo, most e-ink readers) prefer it over a fixed-layout PDF — especially for text-heavy documents like books or long reports.',
    'howto' => array('Upload your PDF file.', 'Click Convert.', 'Wait a few seconds while the file converts.', 'Download your EPUB file.'),
    'features' => array('Text reflows to fit phone, tablet or e-reader screens', 'Opens in Apple Books, Kobo and most e-reader apps', 'Better reading experience than a fixed PDF on small screens', 'Best suited to text-heavy PDFs rather than complex layouts'),
    'faqs' => array(
        array('q' => 'Is this PDF to EPUB tool free?', 'a' => 'Yes — converting PDF to EPUB here is free, with no sign-up or account required.'),
        array('q' => 'Will my PDF file be safe?', 'a' => 'Your file is used only to perform the conversion and isn\'t shared with anyone else. Avoid uploading documents with highly sensitive personal data to any online converter as a general precaution.'),
        array('q' => 'Does the conversion keep the original formatting?', 'a' => 'PDF to EPUB keeps as much of the original layout as the EPUB format allows. Very complex source layouts can shift slightly after conversion — that\'s a limitation of format conversion in general, not specific to one tool.')
    ),
),
'currency-converter' => array(
    'intro' => 'Currency Converter converts an amount from one world currency to another using current exchange rates. Pick your source and target currencies, enter an amount, and get an instant converted value — useful for travel budgeting, online shopping, invoicing, or simply checking what a price abroad works out to in your own currency.',
    'howto' => array('Select the currency you\'re converting from.', 'Select the currency you\'re converting to.', 'Enter the amount.', 'Read the converted value instantly.'),
    'features' => array('Covers major world currencies', 'Uses current exchange rate data', 'Instant conversion as you type', 'No account or sign-up needed'),
    'faqs' => array(
        array('q' => 'How current are the exchange rates?', 'a' => 'Rates are refreshed from an exchange-rate data source rather than fixed manually, but currency markets move constantly — for a large transaction, always confirm the exact rate with your bank or payment provider before committing.'),
        array('q' => 'Can I use this for a real money transfer?', 'a' => 'This tool is for quick reference and budgeting only. Banks and money-transfer services apply their own rate and fees, which will differ from the reference rate shown here.'),
        array('q' => 'Does it support historical rates?', 'a' => 'No — this converter shows a current rate only, not historical or forecast rates.'),
    ),
),

'fancy-text-generator' => array(
    'intro' => 'Fancy Text Generator converts ordinary typed text into dozens of stylised Unicode character sets — bold script, bubble letters, gothic, and more — that you can copy and paste anywhere text is accepted, from Instagram bios to Discord usernames.',
    'howto' => array('Type your text into the input box.', 'Browse the generated style variations.', 'Click any style to copy it.', 'Paste it wherever you need — bio, caption, username.'),
    'features' => array('Dozens of Unicode style variations', 'One-click copy for each style', 'Works with any text, no character limit enforced by the tool', 'No account or software needed'),
    'faqs' => array(
        array('q' => 'Why does fancy text look broken in some apps?', 'a' => 'These styles use special Unicode characters rather than an actual font. Most apps render them fine, but some older systems or fonts may not support every character, showing a box or missing glyph instead.'),
        array('q' => 'Can search engines or screen readers read fancy text normally?', 'a' => 'Not reliably — stylised Unicode characters aren\'t treated as normal letters by search engines or accessibility tools, so avoid using it for important headings or content that needs to be searchable or accessible.'),
        array('q' => 'Is this free to use?', 'a' => 'Yes, generating and copying fancy text here is completely free.'),
    ),
),

'stylish-text-generator' => array(
    'intro' => 'Stylish Text Generator produces a range of decorative text styles — outlined, shadowed, symbol-accented and more — for making social media bios, captions and usernames stand out. Type once, then copy whichever style fits.',
    'howto' => array('Type your text into the input box.', 'Scroll through the available styles.', 'Click a style to copy it.', 'Paste it into your bio, caption or profile.'),
    'features' => array('Multiple decorative style options', 'One-click copy per style', 'No account or software needed', 'Works instantly as you type'),
    'faqs' => array(
        array('q' => 'Will these styles work on every platform?', 'a' => 'Most social platforms display them correctly since they\'re built from standard Unicode characters, but rendering can vary slightly between apps and devices.'),
        array('q' => 'Is this different from the Fancy Text Generator?', 'a' => 'Both produce decorative Unicode text; this one focuses on a different set of stylised looks. Try both and use whichever style fits your bio or caption.'),
        array('q' => 'Is there a character limit?', 'a' => 'The tool itself doesn\'t cap input length, but the platform you paste into (Instagram, Discord, etc.) may enforce its own limit.'),
    ),
),

'qr-code-generator' => array(
    'intro' => 'QR Code Generator creates a scannable QR code from any link, plain text, or contact detail you enter. Download the image and use it on printed materials, business cards, posters, or packaging — anywhere someone can scan it with a phone camera to instantly open the link or read the text.',
    'howto' => array('Enter the link or text you want the QR code to open or show.', 'Click Generate.', 'Preview the QR code.', 'Download the image to use or print.'),
    'features' => array('Works with URLs, plain text and contact details', 'Instant preview before download', 'Downloadable image file', 'No account or software needed'),
    'faqs' => array(
        array('q' => 'Do QR codes generated here expire?', 'a' => 'No — the QR code encodes your link or text directly, so it works for as long as the destination link itself stays valid. There\'s no separate expiry on the code.'),
        array('q' => 'Can any phone scan it?', 'a' => 'Yes, any modern phone camera or QR scanner app can read it — no special app is required on most recent phones.'),
        array('q' => 'Can I change the QR code after downloading it?', 'a' => 'No — once downloaded, that image is fixed. If the underlying link or text changes, generate and download a new QR code.'),
    ),
),

);

/* ============================================================
   CATEGORY TEMPLATES — unknown tools ke liye fallback
   ============================================================ */
$mta_category_templates = array(

'finance' => array(
    'template' => 'finance',
    'intro' => '{TOOL} is a free financial calculator designed to simplify your money decisions. Financial planning requires accurate numbers, and this tool delivers instant, reliable calculations without spreadsheets or complicated formulas. Whether budgeting, investing, or borrowing, get clarity in seconds.',
    'howto' => array('Enter your financial figures in the input fields.', 'Verify values reflect your actual situation.', 'Click the calculate button.', 'Review results and plan accordingly.'),
    'features' => array('Accurate standard financial formulas', 'Instant results with breakdown', 'No sign-up or personal data needed', 'Unlimited free usage'),
    'faqs' => array(
        array('q' => 'Is this financial calculator free?', 'a' => 'Completely free with unlimited calculations — our goal is making financial planning accessible to everyone.'),
        array('q' => 'How accurate are the results?', 'a' => 'Calculations use industry-standard formulas. Results are estimates for planning; consult professionals for binding decisions.'),
        array('q' => 'Is my financial data stored?', 'a' => 'Never. All calculations happen locally in your browser — nothing transmits or saves anywhere.'),
    ),
),

'health' => array(
    'template' => 'health',
    'intro' => '{TOOL} is a free health calculator providing personalized insights based on your body metrics. Understanding your numbers empowers better health decisions — whether managing weight, hydration, or fitness goals. Get science-backed estimates instantly, privately, in your browser.',
    'howto' => array('Enter your physical measurements accurately.', 'Select relevant options (gender, activity, etc.).', 'Click the calculate button.', 'Apply insights toward your wellness journey.'),
    'features' => array('Science-based calculation formulas', 'Personalized to your body metrics', 'Private — data never leaves device', 'Suitable for regular progress tracking'),
    'faqs' => array(
        array('q' => 'How accurate is this health tool?', 'a' => 'It uses validated scientific formulas with typical population accuracy around 90%. Individual variation exists — treat results as guidance, not diagnosis.'),
        array('q' => 'Should I consult a doctor anyway?', 'a' => 'Yes! Online tools supplement but never replace professional medical advice, especially with existing conditions or medications.'),
        array('q' => 'Can children use this calculator?', 'a' => 'Most formulas target adults. Growing bodies need pediatric-specific assessments from healthcare providers.'),
    ),
),

'education' => array(
    'template' => 'education',
    'intro' => '{TOOL} is a free educational calculator built for students, teachers, and parents. Academic calculations demand precision — grades, percentages, and GPAs affect admissions and opportunities. This tool eliminates manual math errors and delivers instant trustworthy results.',
    'howto' => array('Enter your academic values carefully.', 'Double-check numbers against official records.', 'Click calculate for instant results.', 'Use results for planning your academic strategy.'),
    'features' => array('Standard academic formulas', 'Eliminates calculation errors', 'Instant results save study time', 'Perfect for goal-setting'),
    'faqs' => array(
        array('q' => 'Is this suitable for official transcripts?', 'a' => 'Use for personal planning and verification. Official documents should follow your institution’s specific calculation methods.'),
        array('q' => 'Does it work for all education systems?', 'a' => 'Formulas cover common systems. Institutions occasionally use variations — verify against your handbook.'),
        array('q' => 'Can teachers use this for grading?', 'a' => 'Absolutely! Teachers save hours computing class grades, though always cross-check critical calculations.'),
    ),
),

'text' => array(
    'template' => 'text',
    'intro' => '{TOOL} is a free text utility that processes your writing instantly in-browser. Writers, students, marketers, and developers save hours on repetitive text tasks. Paste your content, click once, done — no software installation ever needed.',
    'howto' => array('Paste or type your text into the input area.', 'Configure any available options.', 'Click the action button.', 'Copy your processed text instantly.'),
    'features' => array('Instant in-browser processing', 'Handles long documents smoothly', 'Privacy-first — text never uploads', 'One-click copy output'),
    'faqs' => array(
        array('q' => 'Is there a text length limit?', 'a' => 'Practically generous — thousands of words process instantly. Browser memory is the only theoretical limit.'),
        array('q' => 'Is my text sent to servers?', 'a' => 'Never. Processing happens entirely client-side; your content stays on your device.'),
        array('q' => 'Does formatting survive processing?', 'a' => 'Character transformations preserve structure. Test a sample first if preserving specific formatting matters.'),
    ),
),

'developer' => array(
    'template' => 'developer',
    'intro' => '{TOOL} is a free developer utility running entirely in your browser. Debug faster, format cleaner, and convert smarter without installing anything. Built by developers for developers — fast, private, and always accessible in a pinch.',
    'howto' => array('Paste your input into the tool.', 'Select desired options or algorithms.', 'Execute the operation.', 'Copy the output into your project.'),
    'features' => array('Zero-install browser-based utility', 'Client-side processing (data stays local)', 'Developer-standard output formats', 'Fast enough for production debugging'),
    'faqs' => array(
        array('q' => 'Is sensitive data safe?', 'a' => 'Yes — processing occurs client-side via JavaScript. Tokens, configs, and payloads never leave your browser.'),
        array('q' => 'Which standards does it follow?', 'a' => 'Outputs follow official specifications (RFC standards, W3C formats) ensuring compatibility with your stack.'),
        array('q' => 'Does it work offline?', 'a' => 'Once the page loads, core functionality runs without connectivity — handy for restricted environments.'),
    ),
),

'datetime' => array(
    'template' => 'datetime',
    'intro' => '{TOOL} is a free time calculator handling date math precisely. Time calculations trip up everyone — months have different lengths, leap years interfere, timezones confuse. This tool gets it right instantly using your browser’s accurate calendar engine.',
    'howto' => array('Input your dates/times using the pickers.', 'Adjust zones or options as available.', 'Click calculate.', 'Read precise results in multiple formats.'),
    'features' => array('Leap-year accurate calculations', 'Timezone-aware operations', 'Multiple output formats', 'Uses browser-native calendar precision'),
    'faqs' => array(
        array('q' => 'Does it handle leap years?', 'a' => 'Yes — underlying calendar logic accounts for leap years automatically across any date range.'),
        array('q' => 'Historical dates supported?', 'a' => 'Any Gregorian calendar date works, past or future, subject to picker limits.'),
        array('q' => 'Timezone accuracy?', 'a' => 'Browser IANA databases include complete DST histories — among the most reliable sources available.'),
    ),
),

'general' => array(
    'template' => 'general',
    'intro' => '{TOOL} is a free online tool that gets the job done instantly. Part of a growing collection of browser-based utilities designed for speed, simplicity, and privacy. No downloads, no accounts, no nonsense — just working tools that respect your time.',
    'howto' => array('Enter your input in the provided fields.', 'Adjust any options to your needs.', 'Click the main action button.', 'Copy or note your results.'),
    'features' => array('Free forever with unlimited use', 'Runs privately in your browser', 'Mobile, tablet, and desktop friendly', 'No registration required'),
    'faqs' => array(
        array('q' => 'Is {TOOL} really free?', 'a' => 'Yes, completely free with no hidden limits, trials, or premium tiers.'),
        array('q' => 'Do I need to create an account?', 'a' => 'No registration whatsoever — open the page and start using immediately.'),
        array('q' => 'Does it work on mobile phones?', 'a' => 'Fully responsive — works great on any modern smartphone, tablet, or computer.'),
    ),
),
);
