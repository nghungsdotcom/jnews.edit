<?php
/*
 * Workflow Submission Report
 *
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$action      = ( isset( $_REQUEST["action"] ) && sanitize_text_field( $_REQUEST["action"] ) ) ? sanitize_text_field( $_REQUEST["action"] ) : "in-workflow"; // phpcs:ignore
$post_type   = ( isset( $_REQUEST["type"] ) && sanitize_text_field( $_REQUEST["type"] ) ) ? sanitize_text_field( $_REQUEST["type"] ) : "all"; // phpcs:ignore
// phpcs:ignore WordPress.Security.NonceVerification
$page_number = ( isset( $_GET['paged'] ) && sanitize_text_field( $_GET["paged"] ) ) ? intval( sanitize_text_field( $_GET["paged"] ) ) : 1;

// Filter post/page by team
$team_filter = ( isset( $_REQUEST["team-filter"] ) ) ? intval( $_REQUEST["team-filter"] ) : - 1; // phpcs:ignore WordPress.Security.NonceVerification

$ow_report_service = new OW_Report_Service();
$ow_process_flow   = new OW_Process_Flow();

$submitted_posts = $ow_process_flow->get_submitted_articles( $team_filter, $page_number, $post_type );

$submitted_post_count    = $ow_process_flow->get_submitted_article_count( $team_filter, $post_type );
$un_submitted_posts      = $ow_process_flow->get_unsubmitted_articles( $page_number, $post_type );
$un_submitted_post_count = $ow_process_flow->get_unsubmitted_article_count( $post_type );

if ( $action == "in-workflow" ) {
	$ow_posts       = $submitted_posts;
	$count_posts = $submitted_post_count;
} else {
	$ow_posts       = $un_submitted_posts;
	$count_posts = $un_submitted_post_count;
	$action      = 'not-workflow'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
}

$ow_per_page = OASIS_PER_PAGE;
?>
<div class="wrap">
    <div id="view-workflow">
        <form id="submission_report_form" method="post"
              action="<?php echo esc_url( admin_url( 'admin.php?page=oasiswf-reports&tab=workflowSubmissions' ) ); ?>">
            <div class="tablenav top">
                <input type="hidden" name="page" value="oasiswf-submission"/>
                <input type="hidden" id="action" name="action" value="<?php echo esc_attr( $action ); ?>"/>
                <div class="alignleft actions">
                    <select name="type">
                        <option value="all" <?php echo ( $post_type == "all" ) ? "selected" : ""; ?> >
		                    <?php esc_html_e( "All Post Types", 'oasisworkflow' ); ?>
                        </option>
						<?php OW_Utility::instance()->owf_dropdown_post_types_multi( $post_type ); ?>
                    </select>
					<?php
					if ( has_filter( 'owf_report_team_filter' ) ) {
						if ( $action == 'in-workflow' ) { ?>
                            <select name="team-filter">
                                <option value="-1" <?php echo ( $team_filter == - 1 ) ? "selected" : ""; ?> >
		                            <?php esc_html_e( "Select Team", 'oasisworkflow' ); ?>
                                </option>
                                <option value="0" <?php echo ( $team_filter == 0 ) ? "selected" : ""; ?> >
		                            <?php esc_html_e( "All Teams", 'oasisworkflow' ); ?>
                                </option>
								<?php
								$team_options = apply_filters( 'owf_report_team_filter', $team_filter );
								echo $team_options; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
                            </select>
							<?php
						}
					}
					?>
                    <input type="submit" class="button action" value="Filter"/>
                </div>
                <div>
                    <ul class="subsubsub">
						<?php
						$all       = ( $action == "all" ) ? "class='current'" : "";
						$not_in_wf = ( $action == "not-workflow" ) ? "class='current'" : "";
						$in_wf     = ( $action == "in-workflow" ) ? "class='current'" : "";
						echo '<li class="all"><a id="notInWorkflow" href="#" ' . esc_attr( $not_in_wf ) . '>' .
						     esc_html__( 'Not in Workflow', 'oasisworkflow' ) .
						     '<span class="count"> (' . esc_attr( $un_submitted_post_count ) . ')</span></a> </li>';
						echo ' | <li class="all"><a id="inWorkflow" href="#" ' . esc_attr( $in_wf ) . '>' .
						     esc_html__( 'In Workflow', 'oasisworkflow' ) .
						     '<span class="count"> (' . esc_attr( $submitted_post_count ) . ')</span></a> </li>';
						?>
                    </ul>
                </div>
                <div class="tablenav-pages">
					<?php
					$filters                = array();
					$filters["type"]        = $post_type;
					$filters["team-filter"] = $team_filter;
					OW_Utility::instance()->get_page_link( $count_posts, $page_number, $ow_per_page, $action, $filters );
					?>
                </div>
            </div>
			<?php wp_nonce_field( 'owf_workflow_abort_nonce', 'owf_workflow_abort_nonce' ); ?>
        </form>
        <table class="wp-list-table widefat posts" cellspacing="0" border=0>
			<?php $report_column_header = $ow_report_service->get_submission_report_table_header( $action,
				$post_type ); ?>
            <thead>
            <tr>
				<?php
				echo implode( '', $report_column_header ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
            </tr>
            </thead>
            <tfoot>
            <tr>
				<?php
				echo implode( '', $report_column_header ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
            </tr>
            </tfoot>
            <tbody id="coupon-list">
			<?php
			$ow_report_service->get_submission_report_table_rows( $ow_posts, $action, $report_column_header );
			?>
            </tbody>
        </table>

		<?php if ( $action == 'in-workflow' && current_user_can( 'ow_abort_workflow' ) ) : ?>

            <div class="tablenav bottom">
                <!-- Bulk Actions Start -->
                <div class="alignleft actions">
                    <select name="action_type" id="action_type">
                        <option value="none"><?php esc_html_e( "-- Select Action --", "oasisworkflow" ); ?></option>
                        <option value="abort"><?php esc_html_e( "Abort", "oasisworkflow" ); ?></option>
                    </select>
                    <input type="button" class="button action" id="apply_action" value="<?php esc_attr_e('Apply', 'oasisworkflow'); ?>"><span
                        class='loading owf-hidden' class='inline-loading'></span>
                </div>
                <!-- Bulk Actions End -->
                <!-- Display pages Start -->
                <div class="tablenav-pages">
					<?php
					$filters                = array();
					$filters["type"]        = $post_type;
					$filters["team-filter"] = $team_filter;
					OW_Utility::instance()->get_page_link( $count_posts, $page_number, $ow_per_page, $action, $filters );
					?>
                </div>
                <!-- Display pages End -->
            </div>

		<?php endif; ?>
    </div>
    <div id="out"></div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#notInWorkflow').click(function (event) {
            jQuery('#action').val('not-workflow');
            jQuery('#submission_report_form').submit();
        });

        jQuery('#inWorkflow').click(function (event) {
            jQuery('#action').val('in-workflow');
            jQuery('#submission_report_form').submit();
        });

        jQuery('input[name=abort-all]').click(function (event) {
            jQuery('input[type=checkbox]').prop('checked', jQuery(this).prop('checked'));
        });

        jQuery('#apply_action').click(function () {
            if (jQuery('#action_type').val() == 'none')
                return;

            var arr = jQuery('input[name=abort]:checked');
            var post_ids = new Array();
            jQuery.each(arr, function (k, v) {
                post_ids.push(jQuery(this).val());
            });
            if (post_ids.length === 0)
                return;

            data = {
                action: 'multi_workflow_abort',
                post_ids: post_ids,
                security: jQuery('#owf_workflow_abort_nonce').val()
            };

            jQuery('.loading').show();
            jQuery.post(ajaxurl, data, function (response) {
                if (response.success) {
                    jQuery('.loading').hide();
                    jQuery('#inWorkflow').click();
                }
            });
        });

    });
</script>