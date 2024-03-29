<?php
/*
 * Workflow Add-ons
 *
 * @copyright   Copyright (c) 2016, Nugget Solutions, Inc
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Add-ons Page
 *
 * Renders the add-ons page content.
 *
 * @since 4.2
 */

ob_start();
?>

   <div class="wrap" id="owf-add-ons">
      <h1>
		   <?php esc_html_e( 'Add-ons For Oasis Workflow', 'oasisworkflow' ); ?>
         <span>
         &nbsp;<a href="https://www.oasisworkflow.com/add-ons"
                        class="button button-primary button-large"
                        title="<?php esc_attr_e( 'Browse All Add-ons', 'oasisworkflow' ); ?>"
                        target="_blank"><?php esc_html_e( 'Browse All Add-ons', 'oasisworkflow' ); ?>
            </a>
      </span>
      </h1>

      <div id="tab_container">
         <div class="addon-section">
			 <?php echo get_oasis_workflow_add_ons(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
         </div>
         <div class="clear"></div>
         <div class="addon-footer">
            <a href="https://www.oasisworkflow.com/add-ons"
               class="button button-primary button-large"
               title="<?php esc_attr_e( 'Browse All Add-ons', 'oasisworkflow' ); ?>"
               target="_blank"><?php esc_html_e( 'Browse All Add-ons', 'oasisworkflow' ); ?>
            </a>
         </div>
      </div>
   </div>

<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

/**
 * To get the add-ons list
 *
 * @return $display
 * @since 4.2
 */
function get_oasis_workflow_add_ons() {

	$url = 'https://oasisworkflow.com/add-ons.php';
	$display = '';

	$get_list = wp_remote_get( esc_url_raw( $url ) ); // phpcs:ignore
	if ( ! is_wp_error( $get_list ) ) {
		if ( isset( $get_list['body'] ) && strlen( $get_list['body'] ) > 0 ) {
			$display = wp_remote_retrieve_body( $get_list );
		}
	} else {
		$display = '<div class="error"><p>' .
		           esc_html__( 'There was an error retrieving the Add-ons list from the server. Please try again later.',
			           'oasisworkflow' ) . '</div>';
	}

	return $display;
}

?>