<?php
/* MT Long-form Part 1: IMEI Checker flagship article */
if (!defined('ABSPATH')) exit;

return array(

'imei-check' => array(
    'sections' => array(

        array('h' => 'What Is an IMEI Number?',
              'p' => array(
                'IMEI stands for International Mobile Equipment Identity. It is a unique 15-digit number assigned to every mobile phone and cellular device manufactured worldwide. Think of it as your device fingerprint — no two devices share the same IMEI, making it the single most reliable way to identify a specific handset.',
                'The number is far more than random digits. The first eight digits form the Type Allocation Code (TAC), identifying the device model and manufacturer. The next six digits represent the serial number assigned by the manufacturer, and the final digit is a check digit calculated using the Luhn algorithm to verify authenticity.',
                'When your phone connects to any cellular network worldwide, the carrier reads this IMEI during registration. This is how networks distinguish between millions of devices, how stolen phones get blocked globally, and why authorities can trace devices used in criminal activity. Understanding IMEI becomes essential when buying, selling, insuring, or troubleshooting any mobile device.',
                'Unlike your SIM card identifier which changes when you switch carriers, the IMEI stays permanently tied to physical hardware. Even a factory reset cannot change it. This permanence makes IMEI checks invaluable when verifying that a secondhand iPhone or Samsung Galaxy you are about to buy is exactly what the seller claims.',
              )),

        array('h' => 'How to Check an IMEI Number',
              'p' => array(
                'Checking an IMEI takes under thirty seconds with our free online tool. First retrieve your IMEI using any method described below. Then enter all fifteen digits into the checker above, select your preferred service type, and press the check button.',
                'Our system processes your request instantly and returns comprehensive device information including model identification, warranty estimates, carrier lock indicators, blacklist screening, and regional details. Each result field explains exactly what it means for your situation.',
                'For best results, always double-check that you entered fifteen digits correctly. A single mistyped digit produces an error or information belonging to a different device entirely. Copy-paste directly from your screen whenever possible instead of typing manually.',
                'Different service types reveal different information layers. Apple Basic Info gives model, storage, and lock status. Warranty checks show activation dates and coverage windows. Carrier checks reveal network locks crucial before buying imported devices, while Blacklist checks screen against stolen-device reports.',
              )),

        array('h' => 'How Does IMEI Validation Work?',
              'p' => array(
                'Every legitimate IMEI passes the Luhn algorithm — a checksum formula developed by Hans Peter Luhn at IBM in 1954. Before any database lookup happens, the mathematical structure itself reveals whether a number could even exist.',
                'Here is how: take the first fourteen digits and double every second digit moving left to right (positions two, four, six, and so on). If any doubled value exceeds nine, subtract nine. Sum everything, then calculate what single digit brings the total to the nearest multiple of ten. That digit must match position fifteen.',
                'Example: IMEI 490154203237518. Doubling alternate digits of 49015420323751 and summing yields 52. Since 52 needs an 8 to reach sixty, the final digit must be 8 — and indeed it is. This elegant system catches virtually every typo instantly.',
                'Validation alone does not guarantee legitimacy, though. Fraudsters generate structurally valid numbers too. Professional checking therefore combines three layers: structural validation first, TAC registry lookup confirming the model prefix exists officially, then live database screening against manufacturer records and stolen-device blacklists maintained by GSMA and carriers.',
              )),

        array('h' => 'How to Find Your IMEI Number',
              'p' => array(
                'Method one works on virtually every phone ever made: open your dialer as if making a call and enter *#06#. The IMEI appears instantly without pressing call. This universal code works on iPhones, Samsung Galaxy devices, Google Pixels, Xiaomi phones, and every GSM handset sold in decades.',
                'iPhone users have extra options. Go to Settings, General, About, then scroll to find both IMEI and IMEI2 beside the serial number. iOS 15+ also shows this on the startup screen during setup. Original packaging stickers show it too if you kept the box.',
                'Android users check Settings, then About Phone, where IMEI typically appears under Status Information. Samsung owners find it under Settings, About Phone, Status Information. Removing batteries from older removable-battery phones reveals labels printed inside.',
                'Physical locations include the SIM tray where laser-etched digits appear on many modern phones, original retail box barcode stickers, and purchase documentation from authorized retailers. Dual-SIM phones carry two IMEIs — one per slot — and our checker handles both since they reference the same device registration.',
              )),

        array('h' => 'What Does a Valid IMEI Mean?',
              'p' => array(
                'A valid IMEI confirms three things. First, structural integrity: it passes Luhn verification, meaning proper manufacturing generated it rather than random invention. Second, registry existence: its Type Allocation Code corresponds to a real registered model with authorities like BABT (UK), PTCRB (North America), or TAF (China).',
                'Third, most practically for buyers: validity plus clean database checks means no theft flags, no fraud markers, and no carrier blocking orders across participating global networks. This triple verification protects you from the millions of stolen devices circulating through secondhand markets annually.',
                'An invalid IMEI raises immediate red flags. Either someone mistyped it — the common innocent explanation — or the device was tampered with. Criminal operations reprogram stolen handsets with borrowed IMEIs from identical models, illegal in most jurisdictions and detectable when hardware specs mismatch that TAC registry entries.',
                'Before completing any secondhand purchase, verify the IMEI matches everywhere: *#06# display, Settings menu, SIM tray etching, box label should agree perfectly. Then run our free check above. Five minutes of diligence prevents losing thousands to scams victimizing buyers daily worldwide.',
              )),

        array('h' => 'Why Check IMEI Before Buying Used Phones?',
              'p' => array(
                'The used-phone market thrives on information asymmetry — sellers know device history while buyers rarely do. A pristine-looking flagship might hide active carrier locks binding it to foreign networks, unpaid installments making resale illegal, iCloud activation locks rendering it useless, or stolen status leading to confiscation months later.',
                'Carrier locks deserve special attention on imported devices. Phones bought cheaply abroad may reject local SIM cards entirely. Unlocking services charge significant fees with mixed success rates. Our carrier check reveals lock status before money changes hands.',
                'Blacklist screening guards against the cruelest scam: unknowingly buying stolen property. International registries shared among carriers mean phones reported stolen in London eventually stop working in Karachi, Dubai, or New York too. Victims lose purchase price AND the device itself.',
                'Warranty verification adds unexpected value. Many flagships retain partial coverage into second ownership; AppleCare sometimes transfers automatically. Knowing remaining warranty helps negotiate fair prices and plan repair costs realistically instead of discovering expensive surprises later.',
              )),

        array('h' => 'Common IMEI Scams — How Checking Protects You',
              'p' => array(
                'Cloned-IMEI scams copy numbers from legitimate devices onto stolen ones. Both phones then share identity until network conflicts disable service unpredictably. Comparing IMEI across locations — *#06# output versus SIM tray etching — reveals tampering immediately.',
                'Fake-replacement scams surface when repairs return swapped motherboards carrying different IMEIs than purchased. Documenting your IMEI at purchase and re-verifying after repairs catches unauthorized swaps before warranty claims fail mysteriously.',
                'Marketplace fraudsters supply genuine-looking screenshots of clean checks run on different devices entirely. Always verify independently yourself rather than trusting seller evidence. Legitimate sellers welcome verification; scammers discourage it aggressively.',
                'Remember: checking complements but never replaces meeting sellers safely, testing devices thoroughly, using escrow where available, and trusting instincts about deals too good to be true. Layered protection beats any single defense every time.',
              )),
    ),
),
);
