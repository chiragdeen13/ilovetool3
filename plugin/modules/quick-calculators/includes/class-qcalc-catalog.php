<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central place for:
 *  - the 15-category taxonomy + keyword rules used to auto-sort ANY
 *    published page into the right category by its title (no manual
 *    editing needed when a new page is created)
 *  - duplicate-page detection (same/near-identical titles)
 *
 * Pages that don't match any rule fall into "🧩 Other Tools" instead of
 * being silently dropped.
 */
class QCalc_Catalog {

	/* FIX: the real slugs on this site are 'privacy', 'terms-and-conditions'
	   and 'terms-of-service' — none of which matched the old list (it had
	   'privacy-policy' and 'terms'), so those pages were falling through
	   into the "🧩 Other Tools" tool grid instead of being skipped. Both
	   the real slugs and the old guesses are kept here so this keeps
	   working even if the pages get renamed later. */
	const SKIP_SLUGS = array(
		'about', 'about-us',
		'contact', 'contact-us',
		'privacy', 'privacy-policy',
		'terms', 'terms-and-conditions', 'terms-of-service', 'terms-conditions',
		'home', 'blog',
	);

	/**
	 * Turn a category name like "💰 Finance & Money Tools" into a clean
	 * anchor-safe slug like "finance-money-tools" (emoji + punctuation
	 * stripped). Used for in-page "#cat-..." anchors from the nav dropdown.
	 */
	public static function slugify_category( $name ) {
		$clean = preg_replace( '/[^\p{L}\p{N}\s-]/u', '', $name );
		$clean = trim( $clean );
		$clean = strtolower( preg_replace( '/\s+/', '-', $clean ) );
		return 'cat-' . $clean;
	}

	/**
	 * Ordered category rules. Order matters — first matching rule wins,
	 * so more specific keywords (e.g. "pdf to") are listed before more
	 * generic ones (e.g. "converter").
	 */
	public static function get_rules() {
		return array(
			array( 'name' => '📱 Phone & IMEI Tools', 'keywords' => array( 'imei' ) ),
			array( 'name' => '☪️ Islamic Tools', 'keywords' => array( 'zakat', 'islamic', 'qibla', 'prayer time' ) ),
			array(
				'name'     => '❤️ Health & Fitness Tools',
				'keywords' => array( 'bmi', 'body fat', 'calorie', 'bmr', 'ideal weight', 'water intake', 'pregnancy' ),
			),
			array(
				'name'     => '🎓 Education Tools',
				'keywords' => array( 'gpa', 'cgpa', 'grade calculator', 'marks percentage', 'percentage calculator' ),
			),
			array(
				'name'     => '💰 Finance & Money Tools',
				'keywords' => array( 'interest calculator', 'loan', 'emi', 'currency converter', 'discount calculator', 'income tax', 'sip calculator', 'tip calculator', 'salary calculator', 'hourly wage', 'wage calculator', 'vat', 'gst', 'time card', 'freelance', 'stock average calculator', 'iban', 'invoice' , 'profit margin'),
			),
			array(
				'name'     => '🖼️ Image Tools',
				'keywords' => array( 'image background', 'image compressor', 'image resizer', 'resize image', 'background remover' ),
			),
			array(
				'name'        => '📕 PDF Tools',
				'starts_with' => array( 'pdf to' ),
				'ends_with'   => array( 'to pdf' ),
				'keywords'    => array( 'pdf tools' ),
			),
			array( 'name' => '🎨 Media Tools', 'keywords' => array( 'media tools' ) ),
			array(
				'name'     => '💻 Developer Tools',
				'keywords' => array( 'base64', 'json formatter', 'hash generator', 'uuid generator', 'url encoder', 'url decoder', 'color converter', 'password generator', 'binary', 'number base', 'roman numeral', 'qr code', 'username generator', 'password strength' ),
			),
			array(
				'name'     => '✍️ Text Tools',
				'keywords' => array( 'word counter', 'character counter', 'lorem ipsum', 'duplicate line', 'line break', 'text reverser', 'fancy text', 'stylish text' ),
			),
			array(
				'name'     => '📅 Date & Time Tools',
				'keywords' => array( 'age calculator', 'birthday', 'date difference', 'date calculator', 'time zone' ),
			),
			array(
				'name'     => '🔄 Converters',
				'keywords' => array( 'case converter', 'unit converter', 'number to words', 'length converter', 'weight converter', 'temperature converter', 'area calculator','marla','kanal', 'tola' ),
			),
			array( 'name' => '❤️ Fun Tools', 'keywords' => array( 'love calculator', 'username generator' ) ),
			array( 'name' => '⚡ Utilities', 'keywords' => array( 'electricity bill', 'random number' ) ),
			array(
				'name'     => '➗ Math Tools',
				'keywords' => array( 'average calculator', 'percentage change', 'speed distance', 'ratio calculator' ),
			),
			array( 'name' => '🧩 Other Tools', 'keywords' => array( 'paint', 'tile' ) ),
			
		);
	}

	public static function get_category_icons() {
		$icons = array();
		foreach ( self::get_rules() as $rule ) {
			$icons[ $rule['name'] ] = true;
		}
		$icons['🧩 Other Tools'] = true;
		return array_keys( $icons );
	}

	/**
	 * Return the category name for a given page title, or "🧩 Other Tools"
	 * if nothing matches.
	 */
	public static function categorize( $title ) {
		$t = strtolower( trim( $title ) );

		foreach ( self::get_rules() as $rule ) {
			if ( ! empty( $rule['starts_with'] ) ) {
				foreach ( $rule['starts_with'] as $needle ) {
					if ( 0 === strpos( $t, $needle ) ) {
						return $rule['name'];
					}
				}
			}
			if ( ! empty( $rule['ends_with'] ) ) {
				foreach ( $rule['ends_with'] as $needle ) {
					if ( substr( $t, -strlen( $needle ) ) === $needle ) {
						return $rule['name'];
					}
				}
			}
			if ( ! empty( $rule['keywords'] ) ) {
				foreach ( $rule['keywords'] as $needle ) {
					if ( false !== strpos( $t, $needle ) ) {
						return $rule['name'];
					}
				}
			}
		}

		return '🧩 Other Tools';
	}

	/**
	 * Return all published pages (excluding SKIP_SLUGS and, optionally,
	 * one page ID to exclude — e.g. the homepage itself), each tagged
	 * with its auto-detected category.
	 */
	public static function get_categorized_pages( $exclude_id = 0 ) {
		$pages  = get_pages( array( 'sort_column' => 'post_title' ) );
		$result = array();

		foreach ( $pages as $page ) {
			if ( in_array( $page->post_name, self::SKIP_SLUGS, true ) ) {
				continue;
			}
			if ( $exclude_id && (int) $exclude_id === (int) $page->ID ) {
				continue;
			}
			$result[] = array(
				'id'       => $page->ID,
				'name'     => $page->post_title,
				'slug'     => $page->post_name,
				'category' => self::categorize( $page->post_title ),
			);
		}

		return $result;
	}

	/**
	 * Normalize a title for duplicate comparison: lowercase, strip
	 * common "copy" suffixes and trailing numbers/punctuation.
	 */
	public static function normalize_title( $title ) {
		$t = strtolower( trim( $title ) );
		$t = preg_replace( '/\s*\(copy\)\s*$/', '', $t );
		$t = preg_replace( '/\s*-?\s*copy\s*\d*\s*$/', '', $t );
		$t = preg_replace( '/\s*-?\s*\d+\s*$/', '', $t );
		$t = preg_replace( '/\s+/', ' ', $t );
		return trim( $t );
	}

	/**
	 * Group all published pages by normalized title and return only the
	 * groups that have more than one page (i.e. likely duplicates).
	 * Returns: array of arrays, each containing the duplicate page objects.
	 */
	public static function find_duplicate_pages() {
		$pages  = get_pages( array( 'sort_column' => 'post_title' ) );
		$groups = array();

		foreach ( $pages as $page ) {
			$key = self::normalize_title( $page->post_title );
			if ( '' === $key ) {
				continue;
			}
			if ( ! isset( $groups[ $key ] ) ) {
				$groups[ $key ] = array();
			}
			$groups[ $key ][] = $page;
		}

		$duplicates = array();
		foreach ( $groups as $group ) {
			if ( count( $group ) > 1 ) {
				$duplicates[] = $group;
			}
		}

		return $duplicates;
	}
}
