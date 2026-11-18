<?php
/*
Plugin Name: iLoveTool3 Blog Generator
Description: Auto-generates one unique blog post (real WP "post", shows on /blog/) per tool on Ilovetool3.com. Content is written from a different angle than the tool-page SEO text (mt-tool-articles) — scenario/guide style, comparisons, pro tips, and different FAQs — so it doesn't read as duplicate content. Generates in small batches from the admin panel to avoid timeouts.
Version: 1.0.0
Author: iLoveTool3

WHY A SEPARATE ANGLE FROM THE TOOL PAGES
------------------------------------------------------------------
Each tool page (via mt-tool-articles) already has an "About / How to
Use / Key Features / FAQ" article embedded on the page itself. If a
blog post repeated that structure with the tool name swapped in,
Google would likely treat 60+ posts as near-duplicate content, which
hurts rankings for BOTH the page and the post. So every post here
uses a different structure: a real-world scenario, a step-by-step
walkthrough, a "vs doing it manually" comparison, practical tips, and
a different FAQ — then links back to the actual tool page for the CTA.

HOW TO USE
------------------------------------------------------------------
WP Admin → Tools → iLoveTool3 Blog Generator. Click "Generate next
batch" to create posts for the next 8 tools that don't have one yet.
Keep clicking until "0 tools remaining". Safe to click repeatedly —
tools that already have a generated post are always skipped.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============================================================
   1. MASTER TOOL LIST — title, page slug, category key
   (taken from the live site's homepage listing)
   ============================================================ */
function ilt3_blog_tools() {
	return array(
		// Phone & IMEI
		array( 'title' => 'IMEI Check', 'slug' => 'imei-check', 'cat' => 'phone' ),
		array( 'title' => 'IMEI Number Generator', 'slug' => 'imei-generator', 'cat' => 'phone' ),
		array( 'title' => 'IMEI Validator', 'slug' => 'imei-validator', 'cat' => 'phone' ),
		// Islamic
		array( 'title' => 'Zakat Calculator', 'slug' => 'zakat-calculator', 'cat' => 'islamic' ),
		// Health & Fitness
		array( 'title' => 'BMI Calculator', 'slug' => 'bmi-calculator', 'cat' => 'health' ),
		array( 'title' => 'Body Fat Calculator', 'slug' => 'body-fat-calculator', 'cat' => 'health' ),
		array( 'title' => 'Calorie Calculator', 'slug' => 'calorie-calculator', 'cat' => 'health' ),
		array( 'title' => 'Ideal Weight Calculator', 'slug' => 'ideal-weight-calculator', 'cat' => 'health' ),
		array( 'title' => 'Water Intake Calculator', 'slug' => 'water-intake-calculator', 'cat' => 'health' ),
		// Education
		array( 'title' => 'CGPA to Percentage Calculator', 'slug' => 'cgpa-to-percentage', 'cat' => 'education' ),
		array( 'title' => 'GPA Calculator', 'slug' => 'gpa-calculator', 'cat' => 'education' ),
		array( 'title' => 'Grade Calculator', 'slug' => 'grade-calculator', 'cat' => 'education' ),
		array( 'title' => 'Percentage Calculator', 'slug' => 'percentage-calculator', 'cat' => 'education' ),
		// Finance & Money
		array( 'title' => 'Compound Interest Calculator', 'slug' => 'compound-interest-calculator', 'cat' => 'finance' ),
		array( 'title' => 'Currency Converter', 'slug' => 'currency-converter', 'cat' => 'finance' ),
		array( 'title' => 'Discount Calculator', 'slug' => 'discount-calculator', 'cat' => 'finance' ),
		array( 'title' => 'Income Tax Calculator', 'slug' => 'tax-calculator', 'cat' => 'finance' ),
		array( 'title' => 'Loan Calculator', 'slug' => 'loan-calculator', 'cat' => 'finance' ),
		array( 'title' => 'SIP Calculator', 'slug' => 'sip-calculator', 'cat' => 'finance' ),
		array( 'title' => 'Tip Calculator', 'slug' => 'tip-calculator', 'cat' => 'finance' ),
		// Image
		array( 'title' => 'Image Background Remover', 'slug' => 'image-background-remover', 'cat' => 'image' ),
		array( 'title' => 'Image Compressor', 'slug' => 'image-compressor', 'cat' => 'image' ),
		array( 'title' => 'Image Resizer', 'slug' => 'image-resizer', 'cat' => 'image' ),
		// PDF
		array( 'title' => 'PDF to CSV', 'slug' => 'pdf-to-csv', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to EPUB', 'slug' => 'pdf-to-epub', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to Excel', 'slug' => 'pdf-to-excel', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to HTML', 'slug' => 'pdf-to-html', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to JPG', 'slug' => 'pdf-to-jpg', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to PDF/A', 'slug' => 'pdf-to-pdf-a', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to PNG', 'slug' => 'pdf-to-png', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to PowerPoint', 'slug' => 'pdf-to-powerpoint', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to SVG', 'slug' => 'pdf-to-svg', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to Text', 'slug' => 'pdf-to-text', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to WebP', 'slug' => 'pdf-to-webp', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to Word', 'slug' => 'pdf-to-word', 'cat' => 'pdf' ),
		array( 'title' => 'PDF to XML', 'slug' => 'pdf-to-xml', 'cat' => 'pdf' ),
		// Document (X to PDF)
		array( 'title' => 'Excel to PDF', 'slug' => 'excel-to-pdf', 'cat' => 'document' ),
		array( 'title' => 'HTML to PDF', 'slug' => 'html-to-pdf', 'cat' => 'document' ),
		array( 'title' => 'JPG to PDF', 'slug' => 'jpg-to-pdf', 'cat' => 'document' ),
		array( 'title' => 'PNG to PDF', 'slug' => 'png-to-pdf', 'cat' => 'document' ),
		array( 'title' => 'PowerPoint to PDF', 'slug' => 'powerpoint-to-pdf', 'cat' => 'document' ),
		array( 'title' => 'Text to PDF', 'slug' => 'text-to-pdf', 'cat' => 'document' ),
		array( 'title' => 'WebP to PDF', 'slug' => 'webp-to-pdf', 'cat' => 'document' ),
		array( 'title' => 'Word to PDF', 'slug' => 'word-to-pdf', 'cat' => 'document' ),
		// Developer
		array( 'title' => 'Base64 Encoder / Decoder', 'slug' => 'base64-encoder-decoder', 'cat' => 'developer' ),
		array( 'title' => 'Color Converter (HEX, RGB, HSL)', 'slug' => 'color-converter', 'cat' => 'developer' ),
		array( 'title' => 'Hash Generator (MD5, SHA)', 'slug' => 'hash-generator', 'cat' => 'developer' ),
		array( 'title' => 'JSON Formatter', 'slug' => 'json-formatter', 'cat' => 'developer' ),
		array( 'title' => 'Password Generator', 'slug' => 'password-generator', 'cat' => 'developer' ),
		array( 'title' => 'URL Encoder / Decoder', 'slug' => 'url-encoder-decoder', 'cat' => 'developer' ),
		array( 'title' => 'UUID Generator', 'slug' => 'uuid-generator', 'cat' => 'developer' ),
		// Text
		array( 'title' => 'Duplicate Line Remover', 'slug' => 'duplicate-line-remover', 'cat' => 'text' ),
		array( 'title' => 'Fancy Text Generator', 'slug' => 'fancy-text-generator', 'cat' => 'text' ),
		array( 'title' => 'Lorem Ipsum Generator', 'slug' => 'lorem-ipsum-generator', 'cat' => 'text' ),
		array( 'title' => 'Remove Line Breaks', 'slug' => 'remove-line-breaks', 'cat' => 'text' ),
		array( 'title' => 'Stylish Text Generator', 'slug' => 'stylish-text-generator', 'cat' => 'text' ),
		array( 'title' => 'Text Reverser', 'slug' => 'text-reverser', 'cat' => 'text' ),
		array( 'title' => 'Word Counter', 'slug' => 'word-counter', 'cat' => 'text' ),
		// Date & Time
		array( 'title' => 'Age Calculator', 'slug' => 'age-calculator', 'cat' => 'datetime' ),
		array( 'title' => 'Birthday Countdown & Age Calculator', 'slug' => 'birthday-countdown', 'cat' => 'datetime' ),
		array( 'title' => 'Date Calculator', 'slug' => 'date-calculator', 'cat' => 'datetime' ),
		array( 'title' => 'Time Zone Converter', 'slug' => 'time-zone-converter', 'cat' => 'datetime' ),
		// Converters
		array( 'title' => 'Case Converter', 'slug' => 'case-converter', 'cat' => 'converters' ),
		array( 'title' => 'Number to Words Converter', 'slug' => 'number-to-words', 'cat' => 'converters' ),
		array( 'title' => 'Unit Converter', 'slug' => 'unit-converter', 'cat' => 'converters' ),
		// Fun
		array( 'title' => 'Love Calculator', 'slug' => 'love-calculator', 'cat' => 'fun' ),
		array( 'title' => 'Username Generator', 'slug' => 'username-generator', 'cat' => 'fun' ),
		// Utilities
		array( 'title' => 'Electricity Bill Calculator (Pakistan)', 'slug' => 'electricity-bill-calculator', 'cat' => 'utilities' ),
		array( 'title' => 'Random Number Generator', 'slug' => 'random-number-generator', 'cat' => 'utilities' ),
	);
}

/* ============================================================
   2. CATEGORY LABELS + WP "category" taxonomy term to file each
      post under, plus scenario/tip content blocks used to build
      each post. Each block has 3 phrasing variants; which variant
      a given tool gets is picked deterministically from its slug
      (crc32 % 3), so tools in the same category don't all read
      identically even though they share a template.
   ============================================================ */
function ilt3_blog_categories() {
	return array(
		'phone'      => 'Phone & IMEI Tools',
		'islamic'    => 'Islamic Tools',
		'health'     => 'Health & Fitness',
		'education'  => 'Education',
		'finance'    => 'Finance & Money',
		'image'      => 'Image Tools',
		'pdf'        => 'PDF Tools',
		'document'   => 'Document Conversion',
		'developer'  => 'Developer Tools',
		'text'       => 'Text Tools',
		'datetime'   => 'Date & Time',
		'converters' => 'Converters',
		'fun'        => 'Fun Tools',
		'utilities'  => 'Utilities',
	);
}

function ilt3_blog_scenarios() {
	return array(
		'phone' => array(
			"You're about to buy a used phone from a stranger and you don't want to end up with a blacklisted or stolen device.",
			"A customer walks into your repair shop asking you to confirm a phone's IMEI status before you agree to fix it.",
			"You just bought a phone online and the listing photos didn't show the IMEI sticker — now you need to verify it yourself.",
		),
		'islamic' => array(
			"Ramadan is approaching and you're trying to work out exactly how much Zakat you owe on savings, gold, and investments.",
			"You've never calculated Zakat yourself before and the nisab thresholds and asset categories feel confusing.",
			"Your family's assets are split across cash, gold jewelry, and a small business — and you want one clean number for this year's Zakat.",
		),
		'health' => array(
			"You started a new fitness routine three months ago and want an honest number to check if it's actually working.",
			"Your doctor mentioned a target range during your last checkup, and you want to track where you stand between visits.",
			"You're planning meals for the week and need a starting number before you can plan anything else.",
			"A checkup this week left you wanting the same kind of number on your own terms, without waiting for the next appointment.",
			"You're trying a new routine and want a real baseline to compare against a month from now.",
		),
		'education' => array(
			"Report cards just came out and you're trying to convert a messy set of marks into something a university application actually wants.",
			"It's the night before a scholarship deadline and you need an accurate number, not a rough guess.",
			"A parent is helping their child understand exactly where they stand this semester, and a calculator beats arguing over mental math.",
			"You're deciding whether one more assignment is even worth doing, and the only way to know is to run the actual numbers.",
		),
		'finance' => array(
			"Two loan offers landed in your inbox with different rates and terms, and the only way to compare them fairly is to run the numbers.",
			"You're staring at a big purchase and trying to decide whether paying it off over 12 months or 24 months actually makes a difference.",
			"A bank ad promised a return that sounded too good, so you wanted to check the real numbers yourself before signing anything.",
			"You're building a budget spreadsheet and keep re-doing the same calculation by hand every time a number changes.",
			"A relative asked you to double-check a number from a financial ad before they signed anything, and you wanted to be sure before answering.",
			"You're comparing two ways of paying for the same purchase and want the actual figures side by side, not just a gut feeling.",
			"Payday's a week away and you're trying to work out if a specific expense fits before it becomes a problem.",
		),
		'image' => array(
			"You need a product photo ready for an online listing in the next ten minutes, and Photoshop is overkill for the job.",
			"A client sent over an image that's far too large for your website and it's dragging down your page speed score.",
			"You're building a presentation and need a clean, background-free headshot without opening any desktop software.",
		),
		'pdf' => array(
			"A client emailed a contract as a PDF and you need the actual text back in an editable document before your meeting.",
			"You've got a scanned PDF report and someone on your team just asked for \"the numbers in Excel, please.\"",
			"You're archiving old paperwork and want it in a format you can actually search and copy from later.",
			"A vendor sent a PDF invoice and your accounting software only imports spreadsheets.",
			"You need to quote a paragraph from a PDF report in an email without retyping it by hand.",
			"A scanned form needs a couple of fields updated, and printing-signing-scanning it again feels like overkill.",
			"Your team's tool only accepts one specific file type, and the source document you have is a PDF.",
			"A PDF report has a chart you need to drop into a slide deck without a screenshot's blurry edges.",
			"You need to share a document as a clean image preview without sending the whole file.",
			"A long PDF manual needs to become something a search bar can actually find text inside.",
			"Someone asked for \"just the slides\" and what you have is a PDF export from months ago.",
			"A form needs to go back out in the exact format it arrived in, after one small correction.",
			"You're converting a batch of old files before a migration and don't want to open each one by hand.",
		),
		'document' => array(
			"Your boss wants the slide deck as a single PDF attachment, not a PowerPoint file that might look different on their laptop.",
			"You just finished a Word document and need to send a version nobody downstream can accidentally edit.",
			"A form needs to go out looking identical on every device, so you're converting it before it leaves your inbox.",
			"A form needs signatures from people using different devices, and you want the layout to survive all of them.",
			"You're archiving a project and want every file in one consistent format before it goes into long-term storage.",
			"A client asked for \"just a PDF\" and what you have is a folder of mismatched file types.",
			"You're submitting an application that explicitly says no editable files, and yours currently is one.",
			"The print shop needs one file format for the whole job, and your source material didn't cooperate.",
		),
		'developer' => array(
			"You're debugging an API response at 1 a.m. and the raw JSON is one unreadable line of text.",
			"A teammate pasted a hash into Slack and you need to double-check it matches before trusting the file.",
			"You're wiring up a quick prototype and need a throwaway UUID or password without opening your terminal.",
			"You're reviewing a pull request and need to sanity-check a value before approving it.",
			"A support ticket includes a string that looks encoded, and you need to know what it actually says before replying.",
			"You're setting up test data and need something that looks realistic without touching a real record.",
			"A build failed over a formatting issue that would've taken two seconds to catch with the right tool open.",
		),
		'text' => array(
			"You pasted a list from a spreadsheet and half the rows are exact duplicates you need gone before anyone sees it.",
			"An assignment brief says \"under 500 words\" and you're not sure if you're actually under that limit yet.",
			"You're designing a poster and the placeholder text your template shipped with just doesn't look right anymore.",
			"You copied text from a PDF and every sentence somehow ended up on its own broken line.",
			"A style guide calls for a specific tone and you want a quick way to check the draft against it.",
			"You're prepping sample content for a mockup and the placeholder text needs to look intentional, not lorem-ipsum-lazy.",
			"Someone flagged that your document reads inconsistently, and you want to check it in one place before sending it back.",
		),
		'datetime' => array(
			"Someone asked exactly how many days are left until an event and \"a few weeks\" wasn't a good enough answer.",
			"You're filling out a form that wants your exact age in years, months, and days — not just the year you were born.",
			"A meeting is being scheduled across three time zones and somebody has to work out what time that actually is for everyone.",
			"You need to know exactly what day a deadline lands on, and counting on your fingers stopped being reliable weeks ago.",
		),
		'converters' => array(
			"A recipe from an American cookbook lists everything in cups and ounces, and your kitchen scale only understands grams.",
			"You're filling out an official form that suddenly wants a number spelled out in words, not digits.",
			"Someone sent you text in ALL CAPS and you need it back to normal sentence case before it goes anywhere public.",
		),
		'fun' => array(
			"It's late, you and a friend are bored, and someone suggested testing an old-school love calculator just for laughs.",
			"You're setting up a new account and every username you try is already taken.",
			"A group chat started joking about compatibility scores and now everyone wants to try it themselves.",
		),
		'utilities' => array(
			"Your electricity bill jumped again this month and you want to sanity-check it before assuming the meter is wrong.",
			"You're building a small app and need a genuinely random number, not one that just looks random.",
			"A budgeting spreadsheet needs a rough estimate before the actual bill arrives.",
		),
	);
}

function ilt3_blog_tip_intros() {
	return array(
		"A few things make this easier once you know them:",
		"Here's what tends to trip people up, and how to avoid it:",
		"A handful of small habits make this noticeably smoother:",
	);
}

/* ============================================================
   3. CONTENT BUILDER — assembles one post's HTML from the
      category data + tool title. Structure is deliberately
      different from the tool-page article (mt-tool-articles):
      scenario → what it actually does → walkthrough → manual
      comparison → tips → who uses it → different FAQ → CTA.
   ============================================================ */
/* FIX (root cause of duplicate blog-post intros): variant selection used to
   be crc32(slug) % count, a hash that distributes very unevenly across a
   small modulus. Verified: with only 3 scenarios for 7 finance tools, 5 of
   them (tax, discount, sip, tip, compound-interest) hashed into the SAME
   bucket, so they all got the identical "Two loan offers..." opening
   paragraph. Now uses each tool's fixed position within its own category
   list (round-robin), which spreads reuse as evenly as possible and never
   clusters — collisions only happen when a category has more tools than
   scenario variants, and even then each variant is reused an equal number
   of times instead of some being picked 5x and others never picked. */
function ilt3_blog_variant_index_for_tool( $tool, $count ) {
	static $position_cache = null;
	if ( $position_cache === null ) {
		$position_cache = array();
		$counters = array();
		foreach ( ilt3_blog_tools() as $t ) {
			$cat = $t['cat'];
			if ( ! isset( $counters[ $cat ] ) ) { $counters[ $cat ] = 0; }
			$position_cache[ $t['slug'] ] = $counters[ $cat ];
			$counters[ $cat ]++;
		}
	}
	$pos = isset( $position_cache[ $tool['slug'] ] ) ? $position_cache[ $tool['slug'] ] : 0;
	return $pos % $count;
}
function ilt3_blog_variant_index( $slug, $count ) {
	return crc32( $slug ) % $count; // kept for the tip-intro pick (low collision risk, only 3 short variants, cosmetic text)
}

function ilt3_blog_build_post( $tool ) {
	$title    = $tool['title'];
	$slug     = $tool['slug'];
	$cat_key  = $tool['cat'];
	$cats     = ilt3_blog_categories();
	$cat_name = isset( $cats[ $cat_key ] ) ? $cats[ $cat_key ] : 'Tools';

	$scenarios = ilt3_blog_scenarios();
	$scn_list  = isset( $scenarios[ $cat_key ] ) ? $scenarios[ $cat_key ] : array( "You need a quick, reliable {$title} and don't want to install anything for it." );
	$scenario  = $scn_list[ ilt3_blog_variant_index_for_tool( $tool, count( $scn_list ) ) ];

	$tip_intros = ilt3_blog_tip_intros();
	$tip_intro  = $tip_intros[ ilt3_blog_variant_index( $slug . '-tip', count( $tip_intros ) ) ];

	$tool_url = home_url( '/' . $slug . '/' );
	$t        = esc_html( $title );
	$lt       = esc_html( strtolower( $title ) );

	ob_start();
	?>
	<p><?php echo esc_html( $scenario ); ?> That's exactly the situation the <a href="<?php echo esc_url( $tool_url ); ?>"><?php echo $t; ?></a> on Ilovetool3 is built for — free, in your browser, no account required.</p>

	<h2>What the <?php echo $t; ?> Actually Does</h2>
	<p>Instead of digging through menus in desktop software or trusting a rough mental estimate, the <?php echo $t; ?> takes your input, runs it through the correct method for <?php echo esc_html( strtolower( $cat_name ) ); ?>, and gives you a straight answer — no ads to click through, no waiting for a download link in your inbox.</p>

	<h2>Using It in Practice</h2>
	<ol>
		<li>Open the <a href="<?php echo esc_url( $tool_url ); ?>"><?php echo $t; ?></a> page — it loads instantly, no sign-up screen first.</li>
		<li>Enter the details it asks for. There's nothing to configure beyond that.</li>
		<li>Read the result immediately on the same page.</li>
		<li>Copy it, screenshot it, or come back and rerun it any time — your previous entries aren't saved anywhere.</li>
	</ol>

	<h2><?php echo $t; ?> vs. Doing It the Old Way</h2>
	<p>Before tools like this existed, the alternative was usually a spreadsheet formula you had to remember, a downloaded app you had to trust with permissions, or a manual calculation that's easy to get slightly wrong under time pressure. A dedicated browser-based tool skips all three problems: nothing to install, nothing stored on a server, and the underlying method is fixed and consistent every time you use it — no version drift, no "which formula did I use last time."</p>

	<h2>Tips That Make This Easier</h2>
	<p><?php echo esc_html( $tip_intro ); ?></p>
	<ul>
		<li>Double-check the exact values you're entering — most wrong results trace back to a typo in the input, not the tool itself.</li>
		<li>If your first result looks off, try it again with rounder numbers to confirm you understand what each field expects.</li>
		<li>Bookmark the page if this is something you'll need again — there's no account to log into, so a bookmark is the fastest way back.</li>
	</ul>

	<h2>Who Actually Uses This</h2>
	<p>The <?php echo $t; ?> gets used by a wide mix of people — some running it once for a single decision, others coming back to it regularly as part of a routine task. If your situation matches the scenario above, it's built for exactly that.</p>

	<h2>Frequently Asked Questions</h2>
	<p><strong>Does anything get uploaded or stored?</strong><br>
	No — the tool runs in your browser session and doesn't keep a copy of what you enter after you leave the page.</p>
	<p><strong>Is there a limit on how many times I can use it?</strong><br>
	No daily limit and no paid tier — it's free for one-off use and for repeated daily use alike.</p>
	<p><strong>Does it work the same on mobile?</strong><br>
	Yes, the page is responsive and the <?php echo $t; ?> works the same way on a phone browser as it does on desktop.</p>

	<p>Try it here: <a href="<?php echo esc_url( $tool_url ); ?>"><?php echo $t; ?></a>.</p>
	<?php
	return trim( ob_get_clean() );
}

/* ============================================================
   4. GENERATION — batches, idempotent, ties each post to its
      tool via post meta so re-running never creates duplicates.
   ============================================================ */
function ilt3_blog_post_exists_for( $slug ) {
	$existing = get_posts( array(
		'post_type'      => 'post',
		'post_status'    => 'any',
		'meta_key'       => '_ilt3_source_tool_slug',
		'meta_value'     => $slug,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	return ! empty( $existing );
}

function ilt3_blog_get_or_create_category( $cat_key ) {
	$cats = ilt3_blog_categories();
	$name = isset( $cats[ $cat_key ] ) ? $cats[ $cat_key ] : 'Tools';
	$term = get_term_by( 'name', $name, 'category' );
	if ( $term ) {
		return $term->term_id;
	}
	$new = wp_insert_term( $name, 'category' );
	if ( is_wp_error( $new ) ) {
		return 0;
	}
	return $new['term_id'];
}

function ilt3_blog_generate_one( $tool ) {
	if ( ilt3_blog_post_exists_for( $tool['slug'] ) ) {
		return false; // already generated, skip
	}
	$content   = ilt3_blog_build_post( $tool );
	$cat_id    = ilt3_blog_get_or_create_category( $tool['cat'] );
	$post_id   = wp_insert_post( array(
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'post_title'   => $tool['title'] . ': A Practical Guide',
		'post_name'    => 'guide-' . $tool['slug'],
		'post_content' => $content,
		'post_category'=> $cat_id ? array( $cat_id ) : array(),
	) );
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_ilt3_source_tool_slug', $tool['slug'] );
		return $post_id;
	}
	return false;
}

/* ============================================================
   4b. HIDE DATE / AUTHOR BYLINE ON BLOG POSTS
   The theme (Astra, per the site-chrome notes) prints its own
   "Posted by [author] on [date]" line above/below post content.
   These posts are auto-generated, so that byline isn't wanted —
   this hides it with CSS on the blog listing and single post
   views only (tool pages are untouched).
   ============================================================ */
add_action( 'wp_head', function () {
	if ( is_single() || is_home() || is_category() ) {
		echo '<style>
			.entry-meta, .posted-on, .byline, .post-meta, .meta-date, .meta-author,
			time.entry-date, .updated, .published, .ast-header-meta,
			.entry-meta-list-item, a.author, span.author, .fl-post-meta
				{ display: none !important; }
		</style>' . "\n";
	}
} );

/* ============================================================
   5. ADMIN UI — now under the shared "iLoveTool3 Suite" menu
      (FIX / consolidation: was under Tools → iLoveTool3 Blog
      Generator, a separate place from every other module's admin page)
   ============================================================ */
add_action( 'admin_menu', function () {
	add_submenu_page( 'imei-master', 'iLoveTool3 Blog Generator', 'Blog Generator', 'manage_options', 'ilt3-blog-generator', 'ilt3_blog_admin_page' );
} );

define( 'ILT3_BLOG_BATCH_SIZE', 8 );

function ilt3_blog_admin_page() {
	$tools = ilt3_blog_tools();
	$total = count( $tools );

	$generated_now = array();
	if ( isset( $_POST['ilt3_generate_batch'] ) && check_admin_referer( 'ilt3_blog_nonce' ) ) {
		$done_count = 0;
		foreach ( $tools as $tool ) {
			if ( $done_count >= ILT3_BLOG_BATCH_SIZE ) {
				break;
			}
			if ( ilt3_blog_post_exists_for( $tool['slug'] ) ) {
				continue; // already has a post — don't count it against this batch
			}
			$post_id = ilt3_blog_generate_one( $tool );
			if ( $post_id ) {
				$generated_now[] = $tool['title'];
				$done_count++;
			}
		}
	}

	$remaining = array();
	foreach ( $tools as $tool ) {
		if ( ! ilt3_blog_post_exists_for( $tool['slug'] ) ) {
			$remaining[] = $tool['title'];
		}
	}
	$done_total = $total - count( $remaining );

	echo '<div class="wrap"><h1>iLoveTool3 Blog Generator</h1>';
	echo '<p>' . intval( $done_total ) . ' of ' . intval( $total ) . ' tools have a blog post. ' . intval( count( $remaining ) ) . ' remaining.</p>';

	if ( ! empty( $generated_now ) ) {
		echo '<div class="notice notice-success"><p><b>Generated this batch:</b> ' . esc_html( implode( ', ', $generated_now ) ) . '</p></div>';
	}

	if ( count( $remaining ) > 0 ) {
		echo '<form method="post">';
		wp_nonce_field( 'ilt3_blog_nonce' );
		echo '<input type="hidden" name="ilt3_generate_batch" value="1">';
		echo '<p><button class="button button-primary">Generate next batch (' . min( ILT3_BLOG_BATCH_SIZE, count( $remaining ) ) . ' posts)</button></p>';
		echo '</form>';
		echo '<p><b>Next up:</b> ' . esc_html( implode( ', ', array_slice( $remaining, 0, ILT3_BLOG_BATCH_SIZE ) ) ) . '</p>';
	} else {
		echo '<p><b>All tools have a blog post. Nothing left to generate.</b></p>';
	}

	echo '<p><a href="' . esc_url( home_url( '/blog/' ) ) . '" target="_blank">View blog →</a></p>';
	echo '</div>';
}
