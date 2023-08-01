/*
 * function to invoke ACF validation, if ACF Pro plugin is installed and activated, [and validation is active].
 */
function workflowSubmitWithACF (fnParam = '') {
    /*
     * if ACf validation is not active, then by passs the ACF validation
     */
    if (acf.validation.active == 0) {
        normalWorkFlowSubmit(fnParam);
        return;
    }

    var $form = jQuery('#post');
    if( $form.length === 0 ) {
        $form = jQuery('#editor');
    }
    acf.doAction('submit', $form);
    var data = acf.serialize($form);

    // append AJAX action
    data.action = 'acf/validate_save_post';

    // prepare
    data = acf.prepareForAjax(data);

    // set busy
    $form.busy = 1;

    jQuery.ajaxSetup({async:false});

    jQuery.post(acf.get('ajaxurl'), data, function (json) {

        if (json == -1) {
            return false; // Invalid nonce
        }

        // bail early if not json success
        if (!acf.isAjaxSuccess(json)) {
            return;
        }

        var json = json.data;

        // filter for 3rd party customization
        json = acf.applyFilters('validation_complete', json, $form);

        // validate json
        if (!json || json.valid || !json.errors) {
            // set valid (allows fetch_complete to run)
            acf.validation.valid = true;

            // end function
            return;
        }

        // set valid (prevents fetch_complete from running)
        acf.validation.valid = false;

        // reset trigger
        acf.validation.$trigger = null;

        // vars
        var $scrollTo = false;
        var errorCount = 0;

        // show field error messages
        if (json.errors && json.errors.length > 0) {
            for (var i in json.errors) {
                // get error
                var error = json.errors[i];

                // is error for a specific field?
                if (!error.input) {

                    // update message
                    message += '. ' + error.message;

                    // ignore following functionality
                    continue;
                }
                // get input
                var $input = $form.find('[name="' + error.input + '"]').first();

                // if $_POST value was an array, this $input may not exist
                if (!$input.exists()) {
                    $input = $form.find('[name^="' + error.input + '"]').first();
                }

                // bail early if input doesn't exist
                if (!$input.exists()) {
                    continue;
                }

                // increase
                errorCount++;

                // now get field
                var field = acf.getClosestField($input);

                // show error
                field.showError(error.message);

                // errorMessage
                var errorMessage = acf.__('Validation failed');
                if (errorCount == 1) {
                    errorMessage += '. ' + acf.__('1 field requires attention');
                } else if (errorCount > 1) {
                    errorMessage += '. ' + acf.__('%d fields require attention').replace('%d', errorCount);
                }

                // set $scrollTo
                if (!$scrollTo) {
                    $scrollTo = field.$el;
                }
            }

            // errorMessage
            var errorMessage = acf.__('Validation failed');
            if (errorCount == 1) {
                errorMessage += '. ' + acf.__('1 field requires attention');
            } else if (errorCount > 1) {
                errorMessage += '. ' + acf.__('%d fields require attention').replace('%d', errorCount);
            }

            // notice
            if ($form.notice) {
                $form.notice.update({
                    type: 'error',
                    text: errorMessage
                });
            } else {
                $form.notice = acf.newNotice({
                    type: 'error',
                    text: errorMessage,
                    target: $form
                });
            }

            // if no $scrollTo, set to message
            if (!$scrollTo) {
                $scrollTo = $form.notice.$el;
            }

            // timeout
            setTimeout(function () {
                var scrollelm = jQuery('html, body');
                if( $form.is("#editor") && jQuery('.interface-interface-skeleton__content').length !== 0 ) {
                    scrollelm = jQuery('.interface-interface-skeleton__content');
                }
                scrollelm.animate({ scrollTop: $scrollTo.offset().top - (jQuery(window).height() / 2) }, 500);
            }, 10);
        }
    }).done(function() {
        jQuery.ajaxSetup({async:true});
        if (acf.validation.valid && $form.is("#post")) {
            normalWorkFlowSubmit(fnParam);
        }
    }).fail(function() {
        jQuery.ajaxSetup({async:true});
        console.log( 'validate_save_post failed' );
    });

    jQuery.ajaxSetup({async:true});

    return acf.validation.valid === false ? 'invalid' : 'valid';
}