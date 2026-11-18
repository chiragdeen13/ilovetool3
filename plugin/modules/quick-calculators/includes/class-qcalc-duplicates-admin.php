<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QCalc_DuplicatesAdmin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	public function add_page() {
		// FIX (consolidation): was under Settings -> Duplicate Pages; now a
		// submenu of the shared "iLoveTool3 Suite" parent menu.
		add_submenu_page(
			'imei-master',
			'Duplicate Pages',
			'Duplicate Pages',
			'manage_options',
			'quick-calculators-duplicates',
			array( $this, 'render_page' )
		);
	}

	public function render_page() {
		$duplicate_groups = QCalc_Catalog::find_duplicate_pages();
		?>
		<div class="wrap">
			<h1>Duplicate Pages Checker</h1>
			<p>Scans all published pages and groups ones with the same (or near-identical) title — helpful on a large site where the same tool may have been created more than once.</p>
			<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=quick-calculators-duplicates' ) ); ?>" class="button button-primary">🔄 Re-scan for Duplicates</a></p>

			<?php if ( empty( $duplicate_groups ) ) : ?>
				<p style="margin-top:20px;">✅ No duplicate pages found.</p>
			<?php else : ?>
				<p style="margin-top:20px;"><strong><?php echo count( $duplicate_groups ); ?> possible duplicate group(s) found:</strong></p>

				<?php foreach ( $duplicate_groups as $group ) : ?>
					<table class="widefat striped" style="max-width:800px;margin-bottom:20px;">
						<thead>
							<tr>
								<th colspan="3">Group: "<?php echo esc_html( $group[0]->post_title ); ?>" (<?php echo count( $group ); ?> pages)</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $group as $page ) : ?>
								<tr>
									<td><?php echo esc_html( $page->post_title ); ?></td>
									<td><code>/<?php echo esc_html( $page->post_name ); ?>/</code></td>
									<td>
										<a href="<?php echo esc_url( get_edit_post_link( $page->ID ) ); ?>">Edit</a> |
										<a href="<?php echo esc_url( home_url( '/' . $page->post_name . '/' ) ); ?>" target="_blank">View</a> |
										<a href="<?php echo esc_url( get_delete_post_link( $page->ID ) ); ?>" style="color:#a4222c;">Move to Trash</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endforeach; ?>

				<p style="color:#8a8a9a;">Review each group and keep only the page that's actually live/working — trash the rest. This won't delete anything automatically.</p>
			<?php endif; ?>
		</div>
		<?php
	}
}
