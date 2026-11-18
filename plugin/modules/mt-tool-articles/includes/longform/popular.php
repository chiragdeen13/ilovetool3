<?php
/* MT Long-form Part 2: Popular tools */
if (!defined('ABSPATH')) exit;

return array(

'imei-validator' => array(
    'sections' => array(
        array('h' => 'Understanding the Luhn Algorithm',
              'p' => array(
                'The Luhn algorithm, created by IBM scientist Hans Peter Luhn in 1954, validates identification numbers through simple modular arithmetic. Credit cards, national IDs, and IMEI numbers all use it because it catches nearly every single-digit typo and most adjacent transpositions instantly.',
                'For IMEIs, positions one through fourteen each contribute to a weighted sum — odd-indexed digits count directly while even-indexed digits double, subtracting nine when doubling exceeds nine. The final check digit makes the grand total divisible by ten.',
                'Our validator shows the expected check digit whenever validation fails, telling you exactly what the last digit should have been. This transforms frustrating "invalid" messages into actionable corrections — usually revealing exactly which digit got mistyped.',
              )),
        array('h' => 'Reading Your TAC Code Results',
              'p' => array(
                'The first eight digits form your Type Allocation Code. Prefixes beginning 35 indicate BABT (British Approvals Board for Telecommunications) allocation covering most European and Asian markets. Codes starting 01 fall under PTCRB (North America), while 86 prefixes belong to TAF (China).',
                'Matching TAC codes against known model databases confirms whether that prefix corresponds to your claimed device model. A "Samsung Galaxy" whose TAC registers as an Apple model reveals misrepresentation instantly.',
              )),
    ),
),

'age-calculator' => array(
    'sections' => array(
        array('h' => 'How Exact Age Calculation Works',
              'p' => array(
                'Human age counting seems simple until months with different lengths interfere. Born January 31st? February never offers a matching day. Our calculator uses calendar-aware logic: complete years count first, then remaining months, then remaining days — borrowing from the previous month’s actual length when days go negative.',
                'This mirrors how official documents compute age for passports, school enrollment, and legal majority determinations. Simple division by 365.25 produces fractional ages that drift from reality by weeks over decades.',
                'Leap years add complexity our engine handles automatically. People born February 29th technically celebrate birthdays every four years; legally, most jurisdictions treat March 1st as their common-year birthday. Our next-birthday countdown follows this convention.',
              )),
        array('h' => 'Practical Uses Beyond Curiosity',
              'p' => array(
                'Parents track developmental milestones against pediatric schedules measured in precise months. Visa applications demand exact age at submission dates. Insurance premiums, retirement planning, sports category eligibility, and pension qualification all hinge on precise chronological age.',
                'Fitness enthusiasts calculate biological benchmarks; genealogists verify historical records; HR departments confirm employment eligibility dates. What begins as curiosity becomes genuinely practical across dozens of everyday situations.',
              )),
    ),
),

'bmi-calculator' => array(
    'sections' => array(
        array('h' => 'The Science Behind BMI',
              'p' => array(
                'Adolphe Quetelet developed the BMI formula in the 1830s — weight in kilograms divided by height in meters squared. Insurance actuary Ancel Keys popularized it in 1972 as population-level health screening, and WHO later standardized the categories we use today.',
                'Categories break down as: Underweight below 18.5, Normal 18.5 to 24.9, Overweight 25 to 29.9, and Obese at 30 or higher. These thresholds derive from mortality-curve analysis showing health risks climbing outside the normal band.',
                'BMI shines as a zero-cost first screen requiring only a bathroom scale and tape measure. It cannot measure body composition, but it reliably flags populations needing deeper assessment.',
              )),
        array('h' => 'Limitations Every User Should Know',
              'p' => array(
                'Muscle weighs more than fat per volume, so athletes frequently register "Overweight" despite visible fitness. Conversely, elderly adults losing muscle mass may show "Normal" BMI while carrying unhealthy fat proportions — called normal-weight obesity.',
                'Ethnic variations matter too: some Asian populations face elevated metabolic risks at lower BMIs, prompting alternative thresholds in certain countries. Children need age-and-sex percentile charts rather than adult cutoffs entirely.',
                'Treat BMI as a starting conversation with healthcare providers, not a diagnosis. Waist circumference, body fat percentage, bloodwork, and blood pressure complete the picture that any single number begins.',
              )),
    ),
),

'percentage-calculator' => array(
    'sections' => array(
        array('h' => 'Three Percentage Problems, One Tool',
              'p' => array(
                'Percentage questions come in exactly three flavors. Type one: finding a portion — what is 15% of 200? Multiply: 200 times 0.15 equals 30. Shopping discounts, tax amounts, tip calculations, and commission earnings all use this pattern.',
                'Type two: finding the rate — 45 is what percent of 180? Divide then multiply: (45 divided by 180) times 100 gives 25%. Test scores, market share, survey results, and completion progress follow this structure.',
                'Type three: change measurement — moving from 80 to 100 represents what change? The formula (New minus Old) divided by Old, times 100 yields +25%. Stock movements, price inflation, traffic growth, and performance metrics live here.',
              )),
        array('h' => 'Why Percentage Change Asymmetry Matters',
              'p' => array(
                'Here trips up everyone: gaining 25% then losing 25% does not return you to start. From 100, gaining 25% reaches 125; losing 25% of 125 drops to 93.75 — a net loss despite balanced percentages!',
                'The denominator shifts between calculations. Percentage increases divide by the smaller original; decreases divide by the larger new value. Financial literacy demands internalizing this asymmetry — investment losses require larger subsequent gains to recover fully.',
                'A 50% loss needs a 100% gain to break even. This single mathematical truth explains why wealth preservation obsesses professional investors more than aggressive growth chasing.',
              )),
    ),
),

'gpa-calculator' => array(
    'sections' => array(
        array('h' => 'How Weighted GPA Calculation Works',
              'p' => array(
                'GPA weights each course by credit hours because a five-credit chemistry course should influence your average more than a one-credit seminar. Multiply each grade’s point value by its credits to get quality points, sum everything, then divide by total credits attempted.',
                'Example: Biology earns A (4.0) across 4 credits = 16 quality points. English earns B+ (3.3) across 3 credits = 9.9 points. Combined: 25.9 points over 7 credits yields a 3.70 GPA — not the simple average of grades themselves.',
                'This weighting means strategic students protect GPAs by balancing demanding high-credit courses with manageable electives, understanding exactly which classes move the needle most.',
              )),
        array('h' => 'Plus/Minus Grades and Institutional Variations',
              'p' => array(
                'Our calculator includes full plus/minus granularity: A- counts 3.7, B+ counts 3.3, and so forth down the scale. These intermediate values significantly affect cumulative GPAs across many courses — half-point differences compound into scholarship-deciding gaps.',
                'Some institutions skip minus grades entirely; others weight honors courses above 4.0. Always verify your registrar’s official scale. Graduate programs typically recalculate using their own methods anyway, but accurate self-assessment guides realistic application targeting.',
                'Semester GPA combines with cumulative GPA differently: repeated courses sometimes replace original grades, sometimes average together. Academic advisors clarify replacement policies that dramatically affect recovery mathematics after rough semesters.',
              )),
    ),
),

'password-generator' => array(
    'sections' => array(
        array('h' => 'What Makes Passwords Truly Strong?',
              'p' => array(
                'Strength equals entropy measured in bits, multiplying with length and character variety. Each added character multiplies cracking difficulty by the character-set size. Eight lowercase letters offer roughly 38 bits; mixing cases, digits, and symbols pushes substantially higher.',
                'Length dominates complexity. Substitution patterns like replacing E with 3 follow predictable rules cracking software anticipates. Meanwhile four unrelated common words create memorable passphrases with enormous entropy — the famous correct-horse-battery-staple principle.',
                'Modern attacks run GPU rigs testing billions of guesses hourly against leaked password databases. Email passwords deserve maximum strength since email access resets everything else — the master-key vulnerability security professionals emphasize relentlessly.',
              )),
        array('h' => 'Cryptographic Randomness vs Fake Randomness',
              'p' => array(
                'JavaScript’s basic Math.random() is predictable — seeded algorithms reproduce sequences. Real security demands cryptographic generators drawing from hardware entropy sources, which browsers expose through the Web Crypto API our generator uses exclusively.',
                'Every character selection draws from crypto.getRandomValues(), making outputs suitable for actual security use rather than demonstrations. Refresh produces entirely new passwords; nothing persists anywhere.',
                'Pair generated passwords with a reputable password manager — humans cannot remember dozens of unique random strings, and reuse converts one breach into total compromise.',
              )),
    ),
),

'word-counter' => array(
    'sections' => array(
        array('h' => 'Who Uses Word Counters Daily?',
              'p' => array(
                'Students hit essay requirements where going under loses marks and going over irritates graders. Content writers match editorial briefs specifying exact ranges. Twitter users squeeze thoughts into character limits. SEO specialists craft meta descriptions under 160 characters for search display.',
                'Translators bill per word, making counts revenue-critical. Authors track daily writing goals measuring productivity in words. Lawyers watch page-limit equivalents. Speechwriters target reading-time windows for events with hard time slots.',
                'Journalists face headline character constraints; email marketers preview-text truncation; app developers UI label lengths. Wherever text meets limits, instant counting saves revision cycles.',
              )),
        array('h' => 'Reading Time Mathematics',
              'p' => array(
                'Average silent reading runs 200-250 words per minute for adult English readers, though comprehension-heavy technical material slows considerably. We use 200 wpm conservatively so spoken presentations fit their slots.',
                'Speaking rates differ dramatically: presenters average 130-150 wpm with pauses. A "five-minute read" blog post takes seven-plus minutes delivered aloud — knowledge speakers use when adapting written content for stages.',
              )),
    ),
),

'json-formatter' => array(
    'sections' => array(
        array('h' => 'Common JSON Errors and How to Fix Them',
              'p' => array(
                'Trailing commas top the error list — JavaScript objects tolerate them but strict JSON forbids commas before closing braces. Single quotes instead of double quotes rank second; JSON requires double quotes universally for keys and string values.',
                'Unquoted keys break parsers expecting strings everywhere. Unescaped newlines inside strings cause silent corruption; escape them as backslash-n. Comments are illegal in JSON despite appearing in config files everywhere — strip them before validation.',
                'Our validator reports descriptive errors pinpointing problems rather than cryptic codes. Fix top-to-bottom: early errors cascade into misleading downstream complaints.',
              )),
        array('h' => 'Beautify vs Minify: When Each Wins',
              'p' => array(
                'Beautification expands JSON with indentation and line breaks for human readability during development, code review, and debugging sessions. Nested structures become visually parseable — array boundaries and object hierarchies jump out clearly.',
                'Minification strips whitespace producing compact payloads for production transmission. API responses ship minified to cut bandwidth; configuration files often stay beautified for maintainability instead.',
                'Round-tripping between formats is lossless — same data, different presentation. Format freely without fearing content changes.',
              )),
    ),
),

'unit-converter' => array(
    'sections' => array(
        array('h' => 'Conversion Standards Behind the Numbers',
              'p' => array(
                'Our conversions use internationally standardized factors: one inch equals exactly 25.4 millimeters by 1959 international agreement, one pound equals exactly 0.45359237 kilograms, and temperature conversions follow defined scale relationships rather than approximations.',
                'Precision matters contextually. Cooking tolerates rounding; engineering tolerances do not. We display up to six decimal places, letting you choose appropriate precision per situation.',
                'Temperature deserves special mention: Celsius-Fahrenheit conversion requires offset plus scaling (multiply by 1.8, add 32), while Kelvin involves pure offset from Celsius. Mixing formulas produces famously wrong cooking instructions online.',
              )),
        array('h' => 'Everyday Conversion Scenarios',
              'p' => array(
                'International recipes list ingredients metrically while American kitchens measure by volume and ounces. Travelers convert luggage weight allowances, distance signage, and weather forecasts constantly across borders.',
                'Online shoppers encounter dimensions in unfamiliar units; fitness trackers report in whichever system their region prefers; children homework spans both systems in science class. Universal conversion ability quietly saves dozens of daily micro-frustrations.',
              )),
    ),
),

'zakat-calculator' => array(
    'sections' => array(
        array('h' => 'Understanding Nisab Thresholds',
              'p' => array(
                'Nisab marks minimum wealth triggering Zakat obligation: 87.48 grams of gold or 612.36 grams of silver held one lunar year. Since silver Nisab equals far less monetary value, using it obligates more people to give — which is why many scholars recommend it for maximum benefit to recipients.',
                'Rates fluctuate daily with precious metals markets, so entering current local rates ensures accurate thresholds. Under-Nisab wealth carries no Zakat regardless of amount.',
              )),
        array('h' => 'What Assets Count Toward Zakat?',
              'p' => array(
                'Zakatable wealth includes gold and silver jewelry beyond personal wear, cash holdings across accounts, investment portfolios, business inventory at current value, and receivable loans you expect recovered. Deduct immediate liabilities due within your Zakat year.',
                'Personal exemptions cover your residence, vehicles, clothing, furniture, and tools of trade. Retirement accounts spark scholarly debate — accessible balances generally count, locked funds vary by opinion. When uncertain, consult qualified local scholars; our calculator provides estimates supporting those conversations.',
              )),
    ),
),
);
