<?php
/**
 * @package WordPress
 * @subpackage Jobster
 */

while (have_posts()) : the_post();
    $job_id = get_the_ID();
    $job_date = get_the_date();

    $location = wp_get_post_terms($job_id, 'job_location');
    $location_id = $location ? $location[0]->term_id : '';

    $category = wp_get_post_terms(
        $job_id, 'job_category'
    );
    $category_id = $category ? $category[0]->term_id : '';
    $category_icon = 'fa fa-folder-o';
    if ($category_id != '') {
        $category_icon = get_term_meta(
            $category_id, 'job_category_icon', true
        );
    }

    $apply_action = get_post_meta($job_id, 'job_action', true);

    $search_jobs_submit = jobster_get_page_link('job-search.php');

    if (is_user_logged_in()) {
        global $current_user;

        $current_user = wp_get_current_user();
        $is_candidate = function_exists('jobster_user_is_candidate')
                        ? jobster_user_is_candidate($current_user->ID)
                        : false;

        if ($is_candidate) {
            $candidate_id = jobster_get_candidate_by_userid($current_user->ID);
            $visitors = get_post_meta($job_id, 'job_visitors', true);

            if (!is_array($visitors)) {
                $visitors = array();
            }

            if (!array_key_exists($candidate_id, $visitors)) {
                $visitors[$candidate_id] = current_time('mysql');

                update_post_meta($job_id, 'job_visitors', $visitors);
            }
        }
    }

    $jobs_settings = get_option('jobster_jobs_settings');
    $anonymous_apply =  isset($jobs_settings['jobster_job_anonymous_apply_field']) 
                        ? $jobs_settings['jobster_job_anonymous_apply_field'] 
                        : '';
    $show_valid =   isset($jobs_settings['jobster_job_expiration_field']) 
                    && $jobs_settings['jobster_job_expiration_field'] == '1'; ?>

    <section>
        <div class="pxp-container">
            <div class="pxp-single-job-content pxp-has-columns">
                <div class="row">
                    <div class="col-lg-7 col-xl-8 col-xxl-9">
                        <?php $job_cover_val = get_post_meta($job_id, 'job_cover', true);
                        $job_cover = wp_get_attachment_image_src($job_cover_val,'pxp-full');
                        $no_cover_class = '';
                        $no_cover_side_class = 'mt-lg-0';
                        $no_logo_class = 'mt-lg-5';

                        if (is_array($job_cover)) { ?>
                            <div 
                                class="pxp-single-job-cover pxp-cover" 
                                style="background-image: url(<?php echo esc_url($job_cover[0]); ?>);"
                            ></div>
                        <?php } else {
                            $no_cover_class = 'pxp-no-cover';
                            $no_cover_side_class = 'mt-lg-4';
                            $no_logo_class = 'mt-lg-0'; ?>
                            <div class="pxp-single-job-cover pxp-no-img"></div>
                        <?php }

                        $job_company_id = get_post_meta($job_id, 'job_company', true);

                        if (!empty($job_company_id)) {
                            $company_logo_val = get_post_meta(
                                $job_company_id,
                                'company_logo',
                                true
                            );
                            $company_logo = wp_get_attachment_image_src(
                                $company_logo_val,
                                'pxp-thmb'
                            );

                            if (is_array($company_logo)) {
                                if ($no_cover_class != '') {
                                    $no_logo_class = 'mt-lg-5';
                                } ?>
                                <div 
                                    class="pxp-single-job-cover-logo <?php echo esc_attr($no_cover_class); ?>" 
                                    style="background-image: url(<?php echo esc_url($company_logo[0]); ?>);"
                                ></div>
                            <?php } else {
                                if ($no_cover_class != '') {
                                    $no_logo_class = 'mt-lg-5';
                                } ?>
                                <div class="pxp-single-job-cover-logo pxp-no-img <?php echo esc_attr($no_cover_class); ?>">
                                    <?php $company_name = get_the_title($job_company_id);
                                    echo esc_html($company_name[0]); ?>
                                </div>
                            <?php }
                        } ?>

                        <div class="row justify-content-between align-items-center mt-4 <?php echo esc_attr($no_logo_class); ?>">
                            <div class="col-xl-8 col-xxl-6">
                                <h1><?php the_title(); ?></h1>
                                <div class="pxp-single-job-company-location">
                                    <?php if (!empty($job_company_id)) {
                                        esc_html_e('by', 'jobster'); ?>
                                        <a 
                                            href="<?php echo esc_url(get_permalink($job_company_id)); ?>" 
                                            class="pxp-single-job-company"
                                        >
                                            <?php echo esc_html(get_the_title($job_company_id)); ?>
                                        </a>
                                        <?php if ($location_id != '') { 
                                            $location_link = add_query_arg(
                                                'location',
                                                $location_id,
                                                $search_jobs_submit
                                            );
                                            esc_html_e('in', 'jobster'); ?>
                                            <a 
                                                href="<?php echo esc_url($location_link); ?>" 
                                                class="pxp-single-job-location"
                                            >
                                                <?php echo esc_html($location[0]->name); ?>
                                            </a>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="pxp-single-job-options mt-4 col-xl-0">
                                    <?php if (is_user_logged_in()) {
                                        if ($is_candidate) {
                                            $favs = get_post_meta(
                                                $candidate_id,
                                                'candidate_favs',
                                                true
                                            );
                                            $fav_saved_class = '';
                                            $fav_data_saved = 'false';
                                            $fav_icon_class = 'fa fa-heart-o';
                                            if (    !empty($favs)
                                                    && is_array($favs)
                                                    && in_array($job_id, $favs)) { 
                                                $fav_saved_class = 'pxp-saved';
                                                $fav_data_saved = 'true';
                                                $fav_icon_class = 'fa fa-heart';
                                            } ?>

                                            <button 
                                                class="btn pxp-single-job-save-btn <?php echo esc_attr($fav_saved_class); ?>" 
                                                data-pid="<?php echo esc_attr($job_id); ?>" 
                                                data-saved="<?php echo esc_attr($fav_data_saved); ?>"
                                            >
                                                <span class="<?php echo esc_attr($fav_icon_class); ?>"></span>
                                            </button>
                                        <?php } else { ?>
                                            <span 
                                                class="d-inline-block" 
                                                tabindex="0" 
                                                data-bs-toggle="tooltip" 
                                                title="<?php esc_attr_e('You need candidate account', 'jobster') ?>"
                                            >
                                                <button 
                                                    class="btn pxp-single-job-save-btn" 
                                                    disabled
                                                >
                                                    <span class="fa fa-heart-o"></span>
                                                </button>
                                            </span>
                                        <?php }
                                    } else { ?>
                                        <button 
                                            class="btn pxp-single-job-save-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#pxp-signin-modal" 
                                        >
                                            <span class="fa fa-heart-o"></span>
                                        </button>
                                    <?php }
                                    wp_nonce_field('favs_ajax_nonce', 'pxp-single-job-favs-security', true);

                                    if (function_exists('jobster_get_job_share_menu')) {
                                        jobster_get_job_share_menu($job_id);
                                    }

                                    if (!empty($apply_action)) { ?>
                                        <a 
                                            href="<?php echo esc_url($apply_action); ?>"
                                            class="btn ms-2 pxp-single-job-apply-btn pxp-section-cta rounded-pill" 
                                        >
                                            <?php esc_html_e('Apply Now', 'jobster'); ?>
                                        </a>
                                    <?php } elseif ($anonymous_apply == '1' && !is_user_logged_in()) {
                                        $anonymous_job_page_url = jobster_get_page_link('candidate-dashboard-apply.php');
                                        $anonymous_job_url = add_query_arg('id', $job_id, $anonymous_job_page_url); ?>

                                        <a 
                                            href="<?php echo esc_url($anonymous_job_url); ?>" 
                                            class="btn ms-2 pxp-section-cta rounded-pill"
                                        >
                                            <?php esc_html_e('Apply Now', 'jobster'); ?>
                                        </a>
                                    <?php } else {
                                        if (is_user_logged_in()) {
                                            $apps = get_post_meta(
                                                $job_id,
                                                'job_applications',
                                                true
                                            );

                                            if ($is_candidate) {
                                                $app_saved_class = '';
                                                $app_data_saved = 'false';
                                                $applied = false;
                                                $disabled = '';
                                                if (    !empty($apps)
                                                        && is_array($apps)
                                                        && array_key_exists($candidate_id, $apps)) {
                                                    $app_saved_class = 'pxp-saved';
                                                    $app_data_saved = 'true';
                                                    $applied = true;
                                                    $disabled = 'disabled';
                                                }

                                                $cv = get_post_meta(
                                                    $candidate_id,
                                                    'candidate_cv',
                                                    true
                                                );

                                                if ($cv != '') { ?>
                                                    <button 
                                                        class="btn ms-2 pxp-single-job-apply-btn rounded-pill <?php echo esc_attr($app_saved_class); ?>" 
                                                        data-pid="<?php echo esc_attr($job_id); ?>" 
                                                        data-saved="<?php echo esc_attr($app_data_saved); ?>" 
                                                        <?php echo esc_attr($disabled); ?>
                                                    >
                                                        <?php if ($applied) { ?>
                                                            <span class="fa fa-check"></span>
                                                            <?php esc_html_e('Applied', 'jobster');
                                                        } else {
                                                            esc_html_e('Apply Now', 'jobster');
                                                        } ?>
                                                    </button>
                                                <?php } else { ?>
                                                    <span 
                                                        class="d-inline-block" 
                                                        tabindex="0" 
                                                        data-bs-toggle="tooltip" 
                                                        title="<?php echo esc_attr('Upload your resume before applying', 'jobster') ?>"
                                                    >
                                                        <button 
                                                            class="btn ms-2 pxp-single-job-apply-btn rounded-pill" 
                                                            disabled
                                                        >
                                                            <?php esc_html_e('Apply Now', 'jobster'); ?>
                                                        </button>
                                                    </span>
                                                <?php }
                                            } else { ?>
                                                <span 
                                                    class="d-inline-block" 
                                                    tabindex="0" 
                                                    data-bs-toggle="tooltip" 
                                                    title="<?php esc_attr_e('You need candidate account', 'jobster') ?>"
                                                >
                                                    <button 
                                                        class="btn ms-2 pxp-single-job-apply-btn rounded-pill" 
                                                        disabled
                                                    >
                                                        <?php esc_html_e('Apply Now', 'jobster'); ?>
                                                    </button>
                                                </span>
                                            <?php }
                                        } else { ?>
                                            <button 
                                                class="btn ms-2 pxp-single-job-apply-btn rounded-pill" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#pxp-signin-modal" 
                                            >
                                                <?php esc_html_e('Apply Now', 'jobster'); ?>
                                            </button>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <?php wp_nonce_field('apps_ajax_nonce', 'pxp-single-job-apps-security', true); ?>

                        <div class="row mt-4 justify-content-between align-items-center">
                            <div class="col-6">
                                <?php if ($category_id != '') { 
                                    $category_link = add_query_arg(
                                        'category',
                                        $category_id,
                                        $search_jobs_submit
                                    ); ?>
                                    <a 
                                        href="<?php echo esc_url($category_link); ?>" 
                                        class="pxp-single-job-category"
                                    >
                                        <div class="pxp-single-job-category-icon">
                                            <span class="<?php echo esc_attr($category_icon); ?>"></span>
                                        </div>
                                        <div class="pxp-single-job-category-label">
                                            <?php echo esc_html($category[0]->name); ?>
                                        </div>
                                    </a>
                                <?php } ?>
                            </div>
                            <div class="col-auto">
                                <div class="pxp-single-job-date pxp-text-light">
                                    <?php echo esc_html($job_date); ?>
                                </div>
                            </div>
                        </div>

                        <div class="pxp-single-job-content-details mt-4 mt-lg-5">
                            <?php the_content(); ?>
                        </div>

                        <div class="mt-4 mt-lg-5">
                            <?php if (!empty($apply_action)) { ?>
                                <a 
                                    href="<?php echo esc_url($apply_action); ?>"
                                    class="btn pxp-single-job-apply-btn pxp-section-cta rounded-pill" 
                                >
                                    <?php esc_html_e('Apply Now', 'jobster'); ?>
                                </a>
                            <?php } elseif ($anonymous_apply == '1' && !is_user_logged_in()) {
                                $anonymous_job_page_url = jobster_get_page_link('candidate-dashboard-apply.php');
                                $anonymous_job_url = add_query_arg('id', $job_id, $anonymous_job_page_url); ?>

                                <a 
                                    href="<?php echo esc_url($anonymous_job_url); ?>" 
                                    class="btn ms-2 pxp-section-cta rounded-pill"
                                >
                                    <?php esc_html_e('Apply Now', 'jobster'); ?>
                                </a>
                            <?php } else {
                                if (is_user_logged_in()) {
                                    $apps = get_post_meta(
                                        $job_id,
                                        'job_applications',
                                        true
                                    );

                                    if ($is_candidate) {
                                        $app_saved_class = '';
                                        $app_data_saved = 'false';
                                        $applied = false;
                                        $disabled = '';
                                        if (    !empty($apps)
                                                && is_array($apps)
                                                && array_key_exists($candidate_id, $apps)) {
                                            $app_saved_class = 'pxp-saved';
                                            $app_data_saved = 'true';
                                            $applied = true;
                                            $disabled = 'disabled';
                                        }

                                        $cv = get_post_meta(
                                            $candidate_id,
                                            'candidate_cv',
                                            true
                                        );

                                        if ($cv != '') { ?>
                                            <button 
                                                class="btn pxp-single-job-apply-btn pxp-section-cta rounded-pill <?php echo esc_attr($app_saved_class); ?>" 
                                                data-pid="<?php echo esc_attr($job_id); ?>" 
                                                data-saved="<?php echo esc_attr($app_data_saved); ?>" 
                                                <?php echo esc_attr($disabled); ?>
                                            >
                                                <?php if ($applied) { ?>
                                                    <span class="fa fa-check"></span>
                                                    <?php esc_html_e('Applied', 'jobster');
                                                } else {
                                                    esc_html_e('Apply Now', 'jobster');
                                                } ?>
                                            </button>
                                        <?php } else { ?>
                                            <span 
                                                class="d-inline-block" 
                                                tabindex="0" 
                                                data-bs-toggle="tooltip" 
                                                title="<?php echo esc_attr('Upload your resume before applying', 'jobster') ?>"
                                            >
                                                <button 
                                                    class="btn pxp-single-job-apply-btn pxp-section-cta rounded-pill" 
                                                    disabled
                                                >
                                                    <?php esc_html_e('Apply Now', 'jobster'); ?>
                                                </button>
                                            </span>
                                        <?php }
                                    } else { ?>
                                        <span 
                                            class="d-inline-block" 
                                            tabindex="0" 
                                            data-bs-toggle="tooltip" 
                                            title="<?php esc_attr_e('You need candidate account', 'jobster') ?>"
                                        >
                                            <button 
                                                class="btn pxp-single-job-apply-btn pxp-section-cta rounded-pill" 
                                                disabled
                                            >
                                                <?php esc_html_e('Apply Now', 'jobster'); ?>
                                            </button>
                                        </span>
                                    <?php }
                                } else { ?>
                                        <button 
                                        class="btn pxp-single-job-apply-btn pxp-section-cta rounded-pill" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#pxp-signin-modal" 
                                    >
                                        <?php esc_html_e('Apply Now', 'jobster'); ?>
                                    </button>
                                <?php }
                            } ?>
                        </div>
                    </div>

                    <div class="col-lg-5 col-xl-4 col-xxl-3">
                        <div class="pxp-single-job-side-panel mt-5 <?php echo esc_attr($no_cover_side_class); ?>">
                            <?php $experience = get_post_meta(
                                $job_id, 'job_experience', true
                            );
                            if (!empty($experience)) { ?>
                                <div>
                                    <div class="pxp-single-job-side-info-label pxp-text-light">
                                        <?php esc_html_e('Experience', 'jobster'); ?>
                                    </div>
                                    <div class="pxp-single-job-side-info-data">
                                        <?php echo esc_html($experience); ?>
                                    </div>
                                </div>
                            <?php }

                            $level = wp_get_post_terms($job_id, 'job_level');
                            if ($level) { ?>
                                <div class="mt-4">
                                    <div class="pxp-single-job-side-info-label pxp-text-light">
                                        <?php esc_html_e('Work Level', 'jobster'); ?>
                                    </div>
                                    <div class="pxp-single-job-side-info-data">
                                        <?php for ($l = 0; $l < count($level); $l++) {
                                            if ($l != 0) echo ', ';
                                            echo esc_html($level[$l]->name);
                                        } ?>
                                    </div>
                                </div>
                            <?php }

                            $type = wp_get_post_terms($job_id, 'job_type');
                            if ($type) { ?>
                                <div class="mt-4">
                                    <div class="pxp-single-job-side-info-label pxp-text-light">
                                        <?php esc_html_e('Employment Type', 'jobster'); ?>
                                    </div>
                                    <div class="pxp-single-job-side-info-data">
                                        <?php for ($t = 0; $t < count($type); $t++) {
                                            if ($t != 0) echo ', ';
                                            echo esc_html($type[$t]->name);
                                        } ?>
                                    </div>
                                </div>
                            <?php }

                            $salary = get_post_meta(
                                $job_id, 'job_salary', true
                            );
                            if (!empty($salary)) { ?>
                                <div class="mt-4">
                                    <div class="pxp-single-job-side-info-label pxp-text-light">
                                        <?php esc_html_e('Salary', 'jobster'); ?>
                                    </div>
                                    <div class="pxp-single-job-side-info-data">
                                        <?php echo esc_html($salary); ?>
                                    </div>
                                </div>
                            <?php }

                            $valid = get_post_meta(
                                $job_id, 'job_valid', true
                            );
                            if (!empty($valid) && $show_valid) { ?>
                                <div class="mt-4">
                                    <div class="pxp-single-job-side-info-label pxp-text-light">
                                        <?php esc_html_e('Valid Until', 'jobster'); ?>
                                    </div>
                                    <div class="pxp-single-job-side-info-data">
                                        <?php esc_html_e(date(__('F j, Y', 'jobster'), strtotime($valid))); ?>
                                    </div>
                                </div>
                            <?php }

                            $jobs_fields_settings = get_option('jobster_jobs_fields_settings');

                            if (is_array($jobs_fields_settings)) {
                                uasort($jobs_fields_settings, "jobster_compare_position");

                                foreach ($jobs_fields_settings as $jfs_key => $jfs_value) {
                                    $jf_label = $jfs_value['label'];

                                    $job_field_value = get_post_meta($job_id, $jfs_key, true);

                                    if ($job_field_value != '') { ?>
                                        <div class="mt-4">
                                            <div class="pxp-single-job-side-info-label pxp-text-light">
                                                <?php echo esc_html($jf_label); ?>
                                            </div>
                                            <div class="pxp-single-job-side-info-data">
                                                <?php if ($jfs_value['type'] == 'list_field') {
                                                    $list = explode(',', $jfs_value['list']);
                                                    echo esc_html($list[$job_field_value]);
                                                } else if ($jfs_value['type'] == 'date_field') {
                                                    esc_html_e(date(__('F j, Y', 'jobster'), strtotime($job_field_value)));
                                                } else {
                                                    echo esc_html($job_field_value);
                                                } ?>
                                            </div>
                                        </div>
                                    <?php }
                                }
                            } ?>
                        </div>

                        <?php if (!empty($job_company_id)) { ?>
                            <div class="mt-3 mt-lg-4 pxp-single-job-side-panel">
                                <div class="pxp-single-job-side-company">
                                    <?php $company_logo_val = get_post_meta(
                                        $job_company_id,
                                        'company_logo',
                                        true
                                    );
                                    $company_logo = wp_get_attachment_image_src(
                                        $company_logo_val,
                                        'pxp-thmb'
                                    );

                                    if (is_array($company_logo)) { ?>
                                        <div 
                                            class="pxp-single-job-side-company-logo pxp-cover" 
                                            style="background-image: url(<?php echo esc_url($company_logo[0]); ?>);"
                                        ></div>
                                    <?php } else { ?>
                                        <div class="pxp-single-job-side-company-logo pxp-no-img">
                                            <?php $company_name = get_the_title($job_company_id);
                                            echo esc_html($company_name[0]); ?>
                                        </div>
                                    <?php } ?>

                                    <div class="pxp-single-job-side-company-profile">
                                        <div class="pxp-single-job-side-company-name">
                                            <?php echo esc_html(get_the_title($job_company_id)); ?>
                                        </div>
                                        <a href="<?php echo esc_url(get_permalink($job_company_id)); ?>">
                                            <?php esc_html_e('View profile', 'jobster'); ?>
                                        </a>
                                    </div>
                                </div>

                                <?php $company_industry = wp_get_post_terms(
                                    $job_company_id, 'company_industry'
                                );
                                if ($company_industry) { ?>
                                    <div class="mt-4">
                                        <div class="pxp-single-job-side-info-label pxp-text-light">
                                            <?php esc_html_e('Industry', 'jobster'); ?>
                                        </div>
                                        <div class="pxp-single-job-side-info-data">
                                            <?php echo esc_html($company_industry[0]->name); ?>
                                        </div>
                                    </div>
                                <?php }

                                $company_size = get_post_meta(
                                    $job_company_id, 'company_size', true
                                );
                                if (!empty($company_size)) { ?>
                                    <div class="mt-4">
                                        <div class="pxp-single-job-side-info-label pxp-text-light">
                                            <?php esc_html_e('Company size', 'jobster'); ?>
                                        </div>
                                        <div class="pxp-single-job-side-info-data">
                                            <?php printf(
                                                __('%s employees', 'jobster'),
                                                esc_html($company_size)) 
                                            ?>
                                        </div>
                                    </div>
                                <?php }

                                $company_founded = get_post_meta(
                                    $job_company_id, 'company_founded', true
                                );
                                if (!empty($company_founded)) { ?>
                                    <div class="mt-4">
                                        <div class="pxp-single-job-side-info-label pxp-text-light">
                                            <?php esc_html_e('Founded in', 'jobster'); ?>
                                        </div>
                                        <div class="pxp-single-job-side-info-data">
                                            <?php echo esc_html($company_founded); ?>
                                        </div>
                                    </div>
                                <?php }

                                $company_phone = get_post_meta(
                                    $job_company_id, 'company_phone', true
                                );
                                if (!empty($company_phone)) {
                                    $company_phone_short = substr_replace(
                                        $company_phone, '****', -4
                                    ); ?>
                                    <div class="mt-4">
                                        <div class="pxp-single-job-side-info-label pxp-text-light">
                                            <?php esc_html_e('Phone', 'jobster'); ?>
                                        </div>
                                        <div class="pxp-single-job-side-info-data">
                                            <div class="pxp-single-job-side-info-phone">
                                                <a class="d-none"
                                                    href="tel:<?php echo esc_attr($company_phone); ?>"
                                                >
                                                    <?php echo esc_html($company_phone); ?>
                                                </a>
                                                <span 
                                                    class="d-flex align-items-center" 
                                                    onclick="this.parentNode.classList.add('pxp-show');"
                                                >
                                                    <?php echo esc_html($company_phone_short); ?>
                                                    <span class="btn btn-sm rounded-pill">
                                                        <?php esc_html_e('Show', 'jobster'); ?>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php }

                                $company_email = get_post_meta(
                                    $job_company_id, 'company_email', true
                                );
                                if (!empty($company_email)) { ?>
                                    <div class="mt-4">
                                        <div class="pxp-single-job-side-info-label pxp-text-light">
                                            <?php esc_html_e('Email', 'jobster'); ?>
                                        </div>
                                        <div class="pxp-single-job-side-info-data">
                                            <a href="mailto:<?php echo esc_attr($company_email); ?>">
                                                <?php echo esc_html($company_email); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php }

                                $company_location = wp_get_post_terms(
                                    $job_company_id, 'company_location'
                                );
                                if ($company_location) { ?>
                                    <div class="mt-4">
                                        <div class="pxp-single-job-side-info-label pxp-text-light">
                                            <?php esc_html_e('Location', 'jobster'); ?>
                                        </div>
                                        <div class="pxp-single-job-side-info-data">
                                            <?php echo esc_html($company_location[0]->name); ?>
                                        </div>
                                    </div>
                                <?php }

                                $company_website = get_post_meta(
                                    $job_company_id, 'company_website', true
                                );
                                if (!empty($company_website)) { ?>
                                    <div class="mt-4">
                                        <div class="pxp-single-job-side-info-label pxp-text-light">
                                            <?php esc_html_e('Website', 'jobster'); ?>
                                        </div>
                                        <div class="pxp-single-job-side-info-data">
                                            <a href="<?php echo esc_url($company_website); ?>">
                                                <?php echo esc_url($company_website); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php } 
                                
                                $company_facebook = get_post_meta(
                                    $job_company_id, 'company_facebook', true
                                );
                                $company_twitter = get_post_meta(
                                    $job_company_id, 'company_twitter', true
                                );
                                $company_instagram = get_post_meta(
                                    $job_company_id, 'company_instagram', true
                                );
                                $company_linkedin = get_post_meta(
                                    $job_company_id, 'company_linkedin', true
                                );

                                if (!empty($company_facebook)
                                    || !empty($company_twitter)
                                    || !empty($company_instagram)
                                    || !empty($company_linkedin)) { ?>
                                    <div class="mt-4">
                                        <div class="pxp-single-job-side-info-data">
                                            <ul class="list-unstyled pxp-single-job-side-info-social">
                                                <?php if (!empty($company_facebook)) { ?>
                                                    <li>
                                                        <a href="<?php echo esc_url($company_facebook); ?>">
                                                            <span class="fa fa-facebook"></span>
                                                        </a>
                                                    </li>
                                                <?php }
                                                if (!empty($company_twitter)) { ?>
                                                    <li>
                                                        <a href="<?php echo esc_url($company_twitter); ?>">
                                                            <span class="fa fa-twitter"></span>
                                                        </a>
                                                    </li>
                                                <?php }
                                                if (!empty($company_instagram)) { ?>
                                                    <li>
                                                        <a href="<?php echo esc_url($company_instagram); ?>">
                                                            <span class="fa fa-instagram"></span>
                                                        </a>
                                                    </li>
                                                <?php }
                                                if (!empty($company_linkedin)) { ?>
                                                    <li>
                                                        <a href="<?php echo esc_url($company_linkedin); ?>">
                                                            <span class="fa fa-linkedin"></span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $show_similar = isset($jobs_settings['jobster_job_page_similar_field']) 
                        ? $jobs_settings['jobster_job_page_similar_field'] 
                        : false;

    if ($show_similar) {
        if (function_exists('jobster_get_similar_jobs')) { ?>
            <section class="mt-100">
                <div class="pxp-container">
                    <?php jobster_get_similar_jobs(); ?>
                </div>
            </section>
        <?php }
    }

    $allowed_tags = array(
        'br' => array(),
        'p' => array(),
        'ul' => array(),
        'li' => array()
    ); ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "JobPosting",
        "title": "<?php the_title(); ?>",
        "description": "<?php echo wp_kses(get_the_content($job_id), $allowed_tags); ?>",
        "datePosted": "<?php the_date('Y-m-d'); ?>"
        <?php if (!empty($job_company_id)) {
            $company_logo_val = get_post_meta(
                $job_company_id,
                'company_logo',
                true
            );
            $company_logo = wp_get_attachment_image_src(
                $company_logo_val,
                'pxp-thmb'
            );
            $company_website = get_post_meta(
                $job_company_id, 'company_website', true
            );  ?>
            ,"hiringOrganization": {
                "@type": "Organization",
                "name": "<?php echo esc_html(get_the_title($job_company_id)); ?>"
                <?php if (!empty($company_website)) { ?>
                    ,"sameAs": "<?php echo esc_url($company_website); ?>"
                <?php }
                if (is_array($company_logo)) { ?>
                    ,"logo": "<?php echo esc_url($company_logo[0]); ?>"
                <?php } ?>
            }
            <?php if ($location_id != '') { ?>
                ,"jobLocation":{
                    "@type": "Place",
                    "address": "<?php echo esc_html($location[0]->name); ?>"
                }
            <?php }
        } ?>
    }
    </script>
<?php endwhile;
?>