<?php
	// $license = JNews\Util\ValidateLicense::getInstance();
	// $license = $license->is_license_validated();
	$license = true;
?>

<div class="jnews-container jnews-import-demo about-wrap">

	<h1 class="jnews-title"><?php esc_html_e( 'Import Demo & Style', 'jnews' ); ?></h1>

	<div class="about-text">
		<?php
		if ( $license ) {
			wp_enqueue_script( 'waypoint', apply_filters( 'jnews_get_asset_uri', get_parent_theme_file_uri( 'assets/' ) ) . 'js/jquery.waypoints.js', null, wp_get_theme()->get( 'Version' ), true );
			echo wp_kses( sprintf( __( 'The imported demo will show you about the website structure, theme setting, content structure, design template and etc. In this case, you will be more familiar working with JNews. The imported demo is also fully customizable. Feel free to play around with your own design. Read <a target="_blank" href="%s">here</a> for more information.', 'jnews' ), 'http://support.jegtheme.com/documentation/import-content-style/' ), wp_kses_allowed_html() );
		} else {
			echo wp_kses( sprintf( __( 'Please activate the license of JNews theme to unlock import demo & style feature. Read <a target="_blank" href="%s">here</a> for more information.', 'jnews' ), 'https://support.jegtheme.com/documentation/activate-license/' ), wp_kses_allowed_html() );
		}
		?>
	</div>

	<div class="license-plugin-notice hide-notice" data-btn="<?php esc_html_e( 'Activate Now', 'jnews' ); ?>">
		<div class="jnews-modal-message message-info">
			<h3><?php esc_html_e( 'Activate License', 'jnews' ); ?></h3>
			<ul>
				<li>
					<?php esc_html_e( 'Please activate your copy of JNews to unlock this demo. Click button bellow to activate:', 'jnews' ); ?>
				</li>
			</ul>
		</div>
	</div>

	<div class="install-plugin-notice hide-notice">
		<div class="jnews-modal-message message-info">
			<h3><?php esc_html_e( 'Important Notice', 'jnews' ); ?></h3>
			<ul>
				<li>
					<strong><?php esc_html_e( 'Before Import', 'jnews' ); ?></strong>
					<?php esc_html_e( 'Although we already perform automatic backup before importing content, we recommend you to create your own backup before importing content.', 'jnews' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'System Status', 'jnews' ); ?></strong>
					<?php echo wp_kses( sprintf( __( 'We highly recommend you to check your <b>System Status</b> on <a href="%s">this</a> page before importing content in order to make importing process runs as expected.', 'jnews' ), esc_url( menu_page_url( 'jnews_system', false ) ) ), wp_kses_allowed_html() ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Automatic Backup', 'jnews' ); ?></strong>
					<?php esc_html_e( 'Before doing import, we will back up your widget setting, menu location, and customizer setting. We will not back up your menu, post, page, taxonomy (category / tag) or image. When you uninstall your setup, we will revert backup previously saved content and remove installed content.', 'jnews' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Import - Style & Content', 'jnews' ); ?></strong>
					<?php esc_html_e( 'When you import both style & content we will import demo content into your server. Content includes image, taxonomy, post & page (including landing page example), menu, widget, and customizer setting. We will also install required plugin to replicate the demo.', 'jnews' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Import - Only Style ', 'jnews' ); ?></strong>
					<?php esc_html_e( 'When you import style only, we will only import customizer setting with no content. Just customizer setting that will be affected by this kind of import.', 'jnews' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Import - WPBakery Page Builder Content ', 'jnews' ); ?></strong>
					<?php esc_html_e( 'When you choose import Visual Composer Content, we will only import all dummy pages that created with WPBakery Page Builder plugin.', 'jnews' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Import - Elementor Content ', 'jnews' ); ?></strong>
					<?php esc_html_e( 'When you choose import Elementor Content, we will only import all dummy pages that created with Elementor plugin.', 'jnews' ); ?>
				</li>
			</ul>
		</div>
	</div>

	<div class="uninstall-plugin-notice hide-notice">
		<div class="jnews-modal-message message-warning">
			<h3><?php esc_html_e( 'Uninstall Warning', 'jnews' ); ?></h3>
			<p><?php esc_html_e( 'This will remove dummy content and also all widgets you add on this demo. We highly recommend you to create backup before continue.', 'jnews' ); ?></p>
		</div>
	</div>

	<div class="finish-install-plugin-notice hide-notice">
		<div class="jnews-modal-message message-success">
			<h3><?php esc_html_e( 'Congratulations!', 'jnews' ); ?></h3>
			<p><?php esc_html_e( 'Install process success.', 'jnews' ); ?></p>
		</div>
	</div>

	<div class="finish-uninstall-plugin-notice hide-notice">
		<div class="jnews-modal-message message-success">
			<h3><?php esc_html_e( 'Congratulations!', 'jnews' ); ?></h3>
			<p><?php esc_html_e( 'Uninstall process success.', 'jnews' ); ?></p>
		</div>
	</div>

	<div class="jnews-required-plugin-list">

		<?php
			$demos = $license ? array_chunk( $content, 3 ) : array();
			$demos = array_slice( $demos, 0, 4 );

		foreach ( $demos as $demo ) {
			echo "<div class='jnews-row'>";

			foreach ( $demo as $value ) {
				$install_class  = ( $value['id'] === $installed_style ) ? 'imported' : '';
				$install_class .= $license || 'default' === $value['id'] ? ' activated' : ' unactivated';
				?>
					<div class="jnews-item <?php echo esc_attr( $install_class ); ?>" data-id="<?php echo esc_attr( $value['id'] ); ?>">
						<input type="hidden" value="<?php echo wp_create_nonce( 'jnews_import' ); ?>" class="nonce"/>
						<div class="jnews-plugin-image">
							<div class="thumbnail-container" style="padding-bottom: 71.4%;">
								<img src="<?php echo esc_url( $value['image'] ); ?>">
							</div>
							<div class="jnews-item-installed">
								<span><?php esc_html_e( 'Imported', 'jnews' ); ?></span>
							</div>
							<div class="jnews-item-installing">
								<span><i class="fa fa-warning"></i> <?php esc_html_e( 'Don’t refresh page while processing', 'jnews' ); ?></span>
							</div>

						<?php if ( $value['category'] != 'coming-soon' ) : ?>
								<div class="jnews-demo-hover">
									<div class="demo-option">
										<div class="jnews-item-button-checkbox">
											<label>
												<input class="input include-content" name="install-plugin" checked="checked" type="checkbox">
												<span></span>
												<em> <?php esc_html_e( 'Install Plugins', 'jnews' ); ?></em>
											</label>
										</div>
										<div class="jnews-item-button-checkbox">
											<label>
												<input class="input include-content" name="include-content" checked="checked" type="checkbox">
												<span></span>
												<em class="only-style"> <?php esc_html_e( 'Only Style', 'jnews' ); ?></em>
												<em class="import-content"> <?php esc_html_e( 'Style & Content', 'jnews' ); ?></em>
											</label>
										</div>
										<div class="jnews-item-button-checkbox">
											<label>
												<?php
												if ( isset( $value['support'] ) && is_array( $value['support'] ) ) {
													$length = count( $value['support'] );
													if ( 1 === $length ) {
														$style   = 'style="position:absolute;opacity:0;"';
														$checked = true;
														foreach ( $value['support'] as $support ) {
															if ( 'elementor' === $support ) {
																$checked = false;
																$label   = esc_html__( 'Elementor Only', 'jnews' );
															} else {
																$checked = true;
																$label   = esc_html__( 'Visual Composer Only', 'jnews' );
															}
														}
														$label = '<center><em><strong>' . strtoupper( $label ) . '</strong></em><center>';
														?>
																<input class="input include-content" name="builder-content" <?php echo esc_html( ( $checked ? 'checked="checked"' : '' ) ); ?> <?php echo esc_html( $style ); ?> type="checkbox">
															<?php echo jnews_sanitize_output( $label ); ?>
															<?php
													} else {
														?>
															<input class="input include-content" name="builder-content" checked="checked" type="checkbox">
															<span></span>
														<?php
														foreach ( $value['support'] as $support ) {
															if ( 'elementor' === $support ) {
																?>
																			<em class="elementor-content"> <?php esc_html_e( 'Elementor Content', 'jnews' ); ?></em>
																	<?php
															} else {
																?>
																			<em class="vc-content"> <?php esc_html_e( 'Visual Composer Content', 'jnews' ); ?></em>
																	<?php
															}
														}
														?>
															<?php
													}
												} else {
													?>
															<input class="input include-content" name="builder-content" checked="checked" type="checkbox">
															<span></span>
															<em class="vc-content"> <?php esc_html_e( 'WPBakery Content', 'jnews' ); ?></em>
															<em class="elementor-content"> <?php esc_html_e( 'Elementor Content', 'jnews' ); ?></em>
														<?php
												}
												?>
											</label>
										</div>
									</div>
									<a class="jnews-demo-link" href="<?php echo esc_url( $value['demo'] ); ?>" target="_blank">
										<i class="fa fa-external-link"></i> <strong><?php esc_html_e( 'Live Demo', 'jnews' ); ?></strong>
									</a>
								</div>
							<?php endif; ?>
						</div>
						<div class="jnews-item-control">
							<?php if ( $value['category'] === 'coming-soon' ) : ?>
								<div class="jnews-item-description">
									<h3 class="jnews-item-title"> <?php echo esc_html( $value['name'] ); ?> </h3>
								</div>
							<?php else : ?>
								<div class="jnews-item-description">
									<span class="<?php echo esc_attr( $value['category-slug'] ); ?>-demo"><?php echo esc_html( $value['category'] ); ?></span>
									<h3 class="jnews-item-title"> <?php echo esc_html( $value['name'] ); ?> </h3>
								</div>

								<div class="jnews-item-button before-import">
									<div class="jnews-item-button-second">
										<a class="jnews-demo-link" href="<?php echo esc_url( $value['demo'] ); ?>" target="_blank">
											<i class="fa fa-external-link"></i> <strong><?php esc_html_e( 'Live Demo', 'jnews' ); ?></strong>
										</a>
									</div>
									<div class="jnews-item-button-first">
										<a class="import-style button button-import" href="#">
											<?php esc_html_e( 'Import Demo', 'jnews' ); ?>
										</a>
									</div>
								</div>

								<div class="jnews-item-button after-import">
									<a class="import-demo button-uninstall" href="#"><?php esc_html_e( 'Uninstall', 'jnews' ); ?></a>
								</div>

								<div class="jnews-item-button while-import">
									<div class="jeg-progress-bar">
										<div class="progress-line"><span class="progress"></span></div>
									</div>
									<div class="import-demo progress-text" data-text="<?php esc_attr_e( 'Preparing', 'jnews' ); ?>" data-finish="<?php esc_attr_e( 'Finished', 'jnews' ); ?>">
										<i class="fa fa-refresh fa-spin"></i>
										<span>
											<?php esc_html_e( 'Preparing', 'jnews' ); ?>
										</span>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<div class="overlay"></div>
					</div>

					<?php
			}

			echo '</div>';
		}
		$demos = array_slice( $content, 12 );
		?>
		<div class="jeg_block_navigation">
			<div class="navigation_overlay">
				<div class="module-preloader jeg_preloader">
					<span></span><span></span><span></span>
				</div>
			</div>
			<div class="jeg_block_loadmore "> <a href="#" class="" data-load="Load More" data-loading="Loading..."> Load More</a></div>
		</div>
		<script>var jnewsdemo={list:<?php echo wp_json_encode( $demos, true ); ?>, installed_style: '<?php echo $installed_style; ?>'};</script>
		<script type="text/html" id="tmpl-jnews-demo-item">
			<# var installed_style = data.id == data.installed_style ? 'imported' : '' #>
			<# installed_style += true | 'default' == data.id ? ' activated' : ' unactivated' #>
			<div class="jnews-item {{ installed_style }}" data-id="{{ data.id }}">
				<input type="hidden" value="<?php echo wp_create_nonce( 'jnews_import' ); ?>" class="nonce"/>
				<div class="jnews-plugin-image">
					<div class="thumbnail-container" style="padding-bottom: 71.4%;">
						<img src="{{ data.image }}">
					</div>
					<div class="jnews-item-installed">
						<span><?php esc_html_e( 'Imported', 'jnews' ); ?></span>
					</div>
					<div class="jnews-item-installing">
						<span><i class="fa fa-warning"></i> <?php esc_html_e( 'Don’t refresh page while processing', 'jnews' ); ?></span>
					</div>
					<# if ( 'coming-soon' != data.category ) { #>
						<div class="jnews-demo-hover">
							<div class="demo-option">
								<div class="jnews-item-button-checkbox">
									<label>
										<input class="input include-content" name="install-plugin" checked="checked" type="checkbox">
										<span></span>
										<em> <?php esc_html_e( 'Install Plugins', 'jnews' ); ?></em>
									</label>
								</div>
								<div class="jnews-item-button-checkbox">
									<label>
										<input class="input include-content" name="include-content" checked="checked" type="checkbox">
										<span></span>
										<em class="only-style"> <?php esc_html_e( 'Only Style', 'jnews' ); ?></em>
										<em class="import-content"> <?php esc_html_e( 'Style & Content', 'jnews' ); ?></em>
									</label>
								</div>
								<div class="jnews-item-button-checkbox">
									<label>
										<# if ( data.support ) { #>
											<# if ( data.support.length ) { #>
												<# var length = data.support.length #>
													<# if ( 1 === length ) { #>
														<# for ( support in data.support ) { #>
															<# if ( 'elementor' === data.support[support] ) { #>
																<# var checked = false; #>
																<# var label = '<?php echo esc_html__( 'Elementor Only', 'jnews' ); ?>' #>
															<# } else { #>
																<# var checked = true #>
																<# var label = '<?php echo esc_html__( 'WPBakery Page Builder Only', 'jnews' ); ?>' #>
															<# } #>
														<# } #>
														<# var label = label.toUpperCase() #>
														<# var checked = checked ? 'checked="checked"' : '' #>
														<input class="input include-content" name="builder-content" {{ checked }} style="position:absolute;opacity:0;" type="checkbox">
														<center><em><strong>{{ label }}</strong></em><center>
													<# } #>
											<# } else { #>
												<input class="input include-content" name="builder-content" checked="checked" type="checkbox">
												<span></span>
												<# for( support in data.support ) { #>
													<# if ( 'elementor' === data.support[support] ) { #>
														<em class="elementor-content"> <?php esc_html_e( 'Elementor Content', 'jnews' ); ?></em>
													<# } else { #>
														<em class="vc-content"> <?php esc_html_e( 'Visual Composer Content', 'jnews' ); ?></em>
													<# } #>
												<# } #>
											<# } #>
										<# } else { #>
											<input class="input include-content" name="builder-content" checked="checked" type="checkbox">
											<span></span>
											<em class="vc-content"> <?php esc_html_e( 'Visual Composer Content', 'jnews' ); ?></em>
											<em class="elementor-content"> <?php esc_html_e( 'Elementor Content', 'jnews' ); ?></em>
										<# } #>
									</label>
								</div>
							</div>
							<a class="jnews-demo-link" href="{{ data.demo }}" target="_blank">
								<i class="fa fa-external-link"></i> <strong><?php esc_html_e( 'Live Demo', 'jnews' ); ?></strong>
							</a>
						</div>
					<# } #>
				</div>
				<div class="jnews-item-control">
					<# if ( 'coming-soon' === data.category ) { #>
						<div class="jnews-item-description">
							<h3 class="jnews-item-title">{{ data.name }}</h3>
						</div>
					<# } else { #>
						<div class="jnews-item-description">
							<span class="{{ data.category_slug }}-demo">{{ data.category }}</span>
							<h3 class="jnews-item-title">{{ data.name }}</h3>
						</div>

						<div class="jnews-item-button before-import">
							<div class="jnews-item-button-second">
								<a class="jnews-demo-link" href="{{ data.demo }}" target="_blank">
									<i class="fa fa-external-link"></i> <strong><?php esc_html_e( 'Live Demo', 'jnews' ); ?></strong>
								</a>
							</div>
							<div class="jnews-item-button-first">
								<a class="import-style button button-import" href="#"><?php esc_html_e( 'Import Demo', 'jnews' ); ?></a>
							</div>
						</div>

						<div class="jnews-item-button after-import">
							<a class="import-demo button-uninstall" href="#"><?php esc_html_e( 'Uninstall', 'jnews' ); ?></a>
						</div>

						<div class="jnews-item-button while-import">
							<div class="jeg-progress-bar">
								<div class="progress-line"><span class="progress"></span></div>
							</div>
							<div class="import-demo progress-text" data-text="<?php esc_attr_e( 'Preparing', 'jnews' ); ?>" data-finish="<?php esc_attr_e( 'Finished', 'jnews' ); ?>">
								<i class="fa fa-refresh fa-spin"></i>
								<span><?php esc_html_e( 'Preparing', 'jnews' ); ?></span>
							</div>
						</div>
					<# } #>
				</div>
				<div class="overlay"></div>
			</div>
		</script>
	</div>

</div>
