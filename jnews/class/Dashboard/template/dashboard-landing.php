<?php
$menu                = apply_filters( 'jnews_get_admin_slug', '' );
$theme               = wp_get_theme();
$home_url            = home_url();
$jnews_dashboard_url = menu_page_url( 'jnews', false );
$callback            = str_replace( $home_url, '', $jnews_dashboard_url );
?>
<div class="jnews-wrap wrap about-wrap">
	<h1>
		<?php esc_html_e( 'Welcome to', 'jnews' ); ?> <strong><?php echo esc_html( $theme->get( 'Name' ) ); ?></strong>
		<span class="jnews-version"><?php esc_html_e( 'Version', 'jnews' ); ?> <?php echo esc_html( $theme->get( 'Version' ) ); ?></span>
	</h1>

	<?php
		$is_license_validated = apply_filters( 'jnews_check_is_license_validated', array() );
	if ( ! $is_license_validated ) :
		?>
		<div class="about-text">
		<?php
			esc_html_e( 'Please activate the license of JNews theme to get automatic theme update, official support service and all benefits from JNews.', 'jnews' );
		?>
		</div>
		<div class="jnews-registration-wrap jnews-panel jnews-one-click">
			<div class="activate-license">
				<?php
					$url = add_query_arg(
						array(
							'siteurl'  => $home_url,
							'callback' => $callback,
							'item_id' => JNEWS_THEME_ID,
						),
						JEGTHEME_SERVER . '/activate/'
					);

					echo '<a href="' . $url . '" class="button button-primary button-hero">' . esc_html__( 'Activate JNews License', 'jnews' ) . '</a>';
				?>
			</div>
			<p class="description">
				<?php esc_html_e( 'Tips: You must be logged into the same Themeforest account that purchased JNews. If you already logged in, look in the top menu bar to ensure it is the right account.', 'jnews' ); ?>
			</p>
		</div>

	<?php else : ?>
		<div class="about-text">
			<?php echo wp_kses( __( 'Congratulations! JNews is <strong>activated</strong> and ready to use. Get ready to create awesome news, magazine, or blog site using this theme. Read below for additional information. We hope you enjoy it!', 'jnews' ), wp_kses_allowed_html() ); ?>
		</div>
		<?php $license = jnews_get_license(); ?>
		<?php if ( ! empty( $license ) ) : ?>
			<?php if ( ( ! isset( $license['purchase_code'] ) ) && ( ! isset( $license['refresh'] ) ) && isset( $license['item'] ) ) : ?>
			<div class="jnews-registration-wrap jnews-panel jnews-one-click">
				<label><?php esc_html_e( 'Please migrate your JNews to new license system', 'jnews' ); ?></label>	
				<div class="activate-license">
					<?php
						$url = add_query_arg(
							array(

								'siteurl'  => $home_url,
								'callback' => $callback,
								'item_id' => JNEWS_THEME_ID,
							),
							JEGTHEME_SERVER . '/activate/'
						);

						echo '<a href="' . $url . '" class="button button-primary button-hero">' . esc_html__( 'Migrate JNews License', 'jnews' ) . '</a>';
					?>
				</div>
				<p class="description">
					<?php esc_html_e( 'Tips: You must be logged into the same Themeforest account that purchased JNews. If you already logged in, look in the top menu bar to ensure it is the right account.', 'jnews' ); ?>
				</p>
			</div>
			<?php endif; ?>
		<?php endif; ?>

		<div class="jnews-dashboard-box jnews-panel">
			<div class="jnews-dashboard-video" style="margin-right: 20px; width: 700px;">
				<img src="<?php echo esc_url( JNEWS_THEME_URL . '/assets/css/thankyou.jpg' ); ?>"/>
			</div>
			<div class="jnews-welcome">
				<h3><?php esc_html_e( 'Thank you for choosing JNews', 'jnews' ); ?></h3>
				<p>
				<?php
					printf(
						wp_kses(
							__( 'We would like to thank you for purchasing JNews! Before you get started, please be sure to always check out <a href="%s">Documentation</a>', 'jnews' ),
							wp_kses_allowed_html()
						),
						'http://support.jegtheme.com/theme/jnews/'
					);
				?>
				</p>
				<p><?php echo wp_kses( __( 'We provide video guidance on every page to help you understand how to use this theme. You can see it by clicking icon <span class="fa-lightbulb-o fa"></span> at bottom right.', 'jnews' ), wp_kses_allowed_html() ); ?></p>
				<p><?php esc_html_e( 'We outline all kinds of good information, and provide you with all the details you need to use JNews.', 'jnews' ); ?></p>
			</div>
		</div>

		<div class="jnews-feature-list jnews-panel">
			<div class="three-col">
				<div class="col">
					<h3 class="jnews-item-title"><i class="fa fa-plug"></i> <?php esc_html_e( 'Supported Plugin', 'jnews' ); ?></h3>
					<p><?php esc_html_e( 'We provide list of all plugins that supported JNews. However, you are not limited to only install plugin from this list. To speed up your website performance please choose only necessary plugin.', 'jnews' ); ?></p>
					<div class="jnews-item-button">
						<a class="button button-primary" href="<?php echo esc_url( menu_page_url( $menu['plugin'], false ) ); ?>"><?php esc_html_e( 'Install Plugins', 'jnews' ); ?></a>
					</div>
				</div>

				<div class="col">
					<h3 class="jnews-item-title"><i class="fa fa-cubes"></i> <?php esc_html_e( 'Install Demo', 'jnews' ); ?></h3>
					<p><?php esc_html_e( 'Installing demo and style as easy as one click. Our import system will also backup widget & customizer setting. Also, will restore both widget and customizer setting during uninstallation.', 'jnews' ); ?></p>
					<div class="jnews-item-button">
						<a class="button button-primary" href="<?php echo esc_url( menu_page_url( $menu['import'], false ) ); ?>"><?php esc_html_e( 'Install Demo', 'jnews' ); ?></a>
					</div>
				</div>

				<div class="col">
					<h3 class="jnews-item-title"><i class="fa fa-life-buoy"></i> <?php esc_html_e( 'Any Questions?', 'jnews' ); ?></h3>
					<p><?php esc_html_e( 'Our online documentation is an incredible resource for learning how to use JNews. But if you still have any question, please don\'t hesitate to ask through our dedicated support forum.', 'jnews' ); ?></p>
					<div class="jnews-item-button">
						<a class=" button button-primary" href="http://support.jegtheme.com/forums/forum/jnews/"><?php esc_html_e( 'Documentation & Support', 'jnews' ); ?></a>
					</div>
				</div>
			</div>
		</div>

	<?php endif; ?>
</div>
