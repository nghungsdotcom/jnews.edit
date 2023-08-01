var jQueryOWElementor = jQuery.noConflict();

(function (jQuery) {
    window.onload = (event) => {

        //If post is in workflow and any user like editor edit the post
        if (elementor_is_in_workflow === 'true') {
            jQuery('#elementor-panel-saver-button-publish').addClass('owf-elementor-hidden');
        }

        // Display submit to workflow button
        if (owf_process === 'submit') {
            jQuery('#elementor-panel-saver-button-publish').addClass('owf-elementor-hidden');
            jQuery('#elementor-panel-footer-sub-menu-item-save-template').after('<div id="elementor-panel-footer-sub-menu-item-submit-workflow" class="elementor-panel-footer-sub-menu-item"><input type="hidden" id="hi_is_team" name="hi_is_team" /><i class="elementor-icon eicon-folder" aria-hidden="true"></i><span class="elementor-title">' + owf_submit_workflow_vars.submitToWorkflowButton + '</span></div>');
        }

        // Display sign-off button
        if ( typeof owf_reassign !== 'undefined' && owf_reassign == 'reassign-task' ) {
            if( ! jQuery('#elementor-panel-saver-button-publish').hasClass('owf-elementor-hidden') ) {
                jQuery('#elementor-panel-saver-button-publish').addClass('owf-elementor-hidden');
            }

            jQuery('#elementor-panel-footer-sub-menu-item-save-template').after('<div id="elementor-panel-footer-sub-menu-item-reassign-workflow" class="elementor-panel-footer-sub-menu-item ow-elmentor-tool reassign"><i class="elementor-icon eicon-preferences" aria-hidden="true"></i><span class="elementor-title">' + owf_submit_step_vars.reassign + '</span></div>');
        }

        if ( typeof owf_abort !== 'undefined' && owf_abort == 'abort-workflow' ) {
            if( ! jQuery('#elementor-panel-saver-button-publish').hasClass('owf-elementor-hidden') ) {
                jQuery('#elementor-panel-saver-button-publish').addClass('owf-elementor-hidden');
            }
            jQuery('#elementor-panel-footer-sub-menu-item-save-template').after('<div id="elementor-panel-footer-sub-menu-item-abort-workflow" class="elementor-panel-footer-sub-menu-item ow-elmentor-tool abort_workflow"><i class="elementor-icon eicon-trash-o" aria-hidden="true"></i><span class="elementor-title">' + owf_submit_step_vars.abortButton + '</span></div>');
        }

        if (owf_process === 'sign-off') {
            if( ! jQuery('#elementor-panel-saver-button-publish').hasClass('owf-elementor-hidden') ) {
                jQuery('#elementor-panel-saver-button-publish').addClass('owf-elementor-hidden');
            }
            jQuery('#elementor-panel-footer-sub-menu-item-save-template').after('<div id="elementor-panel-footer-sub-menu-item-signoff-workflow" class="elementor-panel-footer-sub-menu-item"><i class="elementor-icon  eicon-sign-out" aria-hidden="true"></i><span class="elementor-title">' + owf_submit_step_vars.signOffButton + '</span></div>');
        }

    };
}(jQueryOWElementor));
