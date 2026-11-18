<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valid Schema.org JSON-LD, emitted as one @graph in the head.
 *
 * Deliberately conservative. Everything here describes something that is
 * actually true and actually visible on the page:
 *   - WebSite + SearchAction (the homepage really does have a tool search)
 *   - Organization (name / url / logo only — no invented address, phone,
 *     founding date, social profiles or contact points)
 *   - WebPage for the current page
 *   - BreadcrumbList that mirrors the breadcrumb QCalc_PageEnhancer renders
 *   - SoftwareApplication (WebApplication) on tool pages, free of charge,
 *     with NO aggregateRating and NO review — the site has no review data,
 *     and inventing it is a manual-action risk, not an SEO win.
 *
 * FAQPage markup is not duplicated here: modules/mt-tool-articles already
 * emits it from the same data that renders the visible FAQ accordion.
 */
class ILT3_SEO_Schema {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'render' ), 20 );
	}

	private function site_id() {
		return ILT3_SEO::url( '/' ) . '#website';
	}

	private function org_id() {
		return ILT3_SEO::url( '/' ) . '#organization';
	}

	private function organization() {
		$org = array(
			'@type' => 'Organization',
			'@id'   => $this->org_id(),
			'name'  => get_bloginfo( 'name' ),
			'url'   => ILT3_SEO::url( '/' ),
			'logo' => 'https://ilovetool3.com/wp-content/uploads/2026/07/ChatGPT-Image-Jul-5-2026-06_36_29-PM.png',
		);

		$logo = ILT3_SEO::og_image();
		if ( $logo ) {
			$org['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $logo,
			);
		}

		return $org;
	}

	private function website() {
		$site = array(
			'@type'           => 'WebSite',
			'@id'             => $this->site_id(),
			'url'             => ILT3_SEO::url( '/' ),
			'name'            => get_bloginfo( 'name' ),
			'inLanguage'      => get_bloginfo( 'language' ),
			'publisher'       => array( '@id' => $this->org_id() ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => ILT3_SEO::url( '/' ) . '?s={search_term_string}',
				),
				'query-input' => 'required name=search_term_string',
			),
		);

		$tagline = get_bloginfo( 'description' );
		if ( $tagline ) {
			$site['description'] = $tagline;
		}

		return $site;
	}

	/**
	 * Mirrors the visible breadcrumb on tool pages:
	 * Home › <Category> › <Tool name>
	 */
	private function breadcrumbs( $post ) {
		if ( ! $post || ! class_exists( 'QCalc_Catalog' ) ) {
			return null;
		}

		$category      = QCalc_Catalog::categorize( $post->post_title );
		$category_slug = QCalc_Catalog::slugify_category( $category );

		$items = array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => ILT3_SEO::url( '/' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => wp_strip_all_tags( $category ),
				'item'     => ILT3_SEO::url( '/#' . $category_slug ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => $post->post_title,
				'item'     => get_permalink( $post ),
			),
		);

		return array(
			'@type'           => 'BreadcrumbList',
			'@id'             => get_permalink( $post ) . '#breadcrumb',
			'itemListElement' => $items,
		);
	}

	private function software_application( $post, $description ) {
		$app = array(
			'@type'               => 'WebApplication',
			'@id'                 => get_permalink( $post ) . '#tool',
			'name'                => $post->post_title,
			'url'                 => get_permalink( $post ),
			'applicationCategory' => 'UtilitiesApplication',
			'operatingSystem'     => 'Any (runs in a web browser)',
			'browserRequirements' => 'Requires JavaScript',
			'isAccessibleForFree' => true,
			'publisher'           => array( '@id' => $this->org_id() ),
			// The tools genuinely are free, so a zero-price offer is an
			// accurate statement rather than fabricated pricing.
			'offers'              => array(
				'@type'         => 'Offer',
				'price'         => '0',
				'priceCurrency' => 'USD',
			),
		);

		if ( $description ) {
			$app['description'] = $description;
		}

		return $app;
	}

	public function render() {
		if ( is_admin() || is_feed() || is_embed() || is_404() ) {
			return;
		}

		$data  = ILT3_SEO::current();
		$graph = array( $this->website(), $this->organization() );

		$page_url = $data['canonical'] ? $data['canonical'] : ILT3_SEO::url( '/' );

		$webpage = array(
			// The homepage genuinely is a listing of tools, so CollectionPage
			// describes it more accurately than a plain WebPage.
			'@type'      => is_front_page() ? 'CollectionPage' : 'WebPage',
			'@id'        => $page_url . '#webpage',
			'url'        => $page_url,
			'name'       => $data['title'],
			'isPartOf'   => array( '@id' => $this->site_id() ),
			'inLanguage' => get_bloginfo( 'language' ),
		);

		if ( $data['description'] ) {
			$webpage['description'] = $data['description'];
		}

		if ( $data['post'] ) {
			$webpage['datePublished'] = get_the_date( 'c', $data['post'] );
			$webpage['dateModified']  = get_the_modified_date( 'c', $data['post'] );
		}

		if ( $data['is_tool'] && $data['post'] ) {
			$crumbs = $this->breadcrumbs( $data['post'] );
			if ( $crumbs ) {
				$webpage['breadcrumb'] = array( '@id' => $crumbs['@id'] );
				$graph[]               = $crumbs;
			}
			$graph[] = $this->software_application( $data['post'], $data['description'] );
		}

		$graph[] = $webpage;

		$json = array(
			'@context' => 'https://schema.org',
			'@graph'   => array_values( array_filter( $graph ) ),
		);

		/** Lets other modules append valid nodes without editing this file. */
		$json = apply_filters( 'ilt3_seo_schema_graph', $json );

		echo "\n" . '<script type="application/ld+json">'
			. wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
			. '</script>' . "\n";
	}
}
