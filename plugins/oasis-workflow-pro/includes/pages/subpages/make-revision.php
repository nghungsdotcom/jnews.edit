<?php
/*
 * Make Revision Popup
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

// FIXED: instead of getting the value from url, use global $post object to get id of current post
global $post;
$ow_admin_post = new OW_Admin_Post();
$post_object   = $ow_admin_post->get();
$the_post_id       = '';
$post_title    = '';

if ( isset( $post_object ) && ! empty( $post_object ) ) {
	$the_post_id    = esc_attr( $post_object->ID );
	$post_title = esc_attr( $post_object->post_title );
} else if ( isset( $post ) && ! empty( $post ) ) {
	$the_post_id    = esc_attr( $post->ID );
	$post_title = esc_attr( $post->post_title );
}
echo '<script type="text/javascript">
         var ajaxurl = "' . esc_url( admin_url( 'admin-ajax.php' ) ) . '";
         var post_id = "' . esc_js( $the_post_id ) . '";
         var post_title = "' . esc_js( $post_title ) . '";
         var ow_admin_url = "' . esc_url( admin_url() ) . '";
      </script>';
?>

<input type="hidden" id="hi_post_id" value="<?php echo esc_attr( $the_post_id ); ?>"/>

<div class="info-setting extra-wide owf-hidden" id="make-revision-submit-div">
    <div class="dialog-title"><strong><?php echo esc_html__( "Revision Already Exists", "oasisworkflow" ); ?></strong></div>
    <div>
        <div class="select-part revision-wrap">
            <p>
				<?php echo esc_html__( "An active revision already exists for this article. Do you want to delete the existing revised article and create a new revision?",
					"oasisworkflow" ); ?>
            </p>
            <div class="ow-btn-group changed-data-set">
                <input class="button revision revision-ok button-primary" type="button"
                       value="<?php echo esc_attr__( "Yes, delete it and create new one", "oasisworkflow" ); ?>"/>
                <span>&nbsp;</span>
                <div class="btn-spacer"></div>
                <input class="button revision revision-no button-primary" type="button"
                       value="<?php echo esc_attr__( "No, take me to the revision", "oasisworkflow" ); ?>"/>
                <div class="btn-spacer"></div>
                <input class="button revision revision-cancel" type="button"
                       value="<?php echo esc_attr__( "Cancel", "oasisworkflow" ); ?>"/>
            </div>
        </div>
    </div>
</div>