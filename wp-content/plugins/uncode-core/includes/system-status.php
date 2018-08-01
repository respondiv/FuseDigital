<?php
/**
 * System Status functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! is_admin() ) {
	return;
}

function uncode_core_system_status_scripts() {
	$screen = get_current_screen();

	if ( isset( $screen->id ) && $screen->id == 'toplevel_page_uncode-system-status' ) {
		wp_enqueue_script( 'uncode_system_status_js', plugins_url( 'assets/js/uncode_system_status.js', __FILE__ ), array( 'jquery' ), UncodeCore_Plugin::VERSION , true );

		$system_status_parameters = array(
			'test_memory_path' => esc_url( plugins_url( 'test-memory.php', __FILE__ ) )
		);

		wp_localize_script( 'uncode_system_status_js', 'SystemStatusParameters', $system_status_parameters );
	}
}
add_action( 'admin_enqueue_scripts', 'uncode_core_system_status_scripts' );

function uncode_core_system_status_print_memory() {
	?>

	<tr>
		<td data-export-label="Server Memory Limit"><?php esc_html_e( 'Server Memory Limit', 'uncode' ); ?>
		<?php echo '<span class="toggle-description"></span><small class="description">' . esc_attr__( 'This is actually the real memory available for your installation despite the WP memory limit.', 'uncode' ) . '</small>'; ?></td>
		<td class="real-memory">
			<span class="calculating"><?php esc_html_e( 'Calculating…', 'uncode' ); ?></span>
			<mark class="yes" style="display: none;">%d% MB</mark>
			<mark class="error" style="display: none;"><?php esc_html_e( 'You only have %d% MB available and it\'s not enough to run the system. If you have already increased the memory limit please check with your hosting provider for increase it (at least 96MB is required).','uncode' ); ?></mark>
		</td>
	</tr>

	<?php
}
add_action( 'uncode_server_memory_limit', 'uncode_core_system_status_print_memory' );
