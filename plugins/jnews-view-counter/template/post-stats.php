<?php
$loader = get_theme_mod( 'jnews_module_loader', 'dot' );
?>
<div class="jnews-view-stats-chart-wrapper">
	<form method="post" action="" class="jnews-view-stats-chart-nav">
		<!-- time range -->
		<div class="time-range-field">
			<div class="form-group">
				<select name="time-range" id="time-range">
					<?php
					$time_range      = JNews_View_Counter()->options['config']['range'];
					$item_time_range = array(
						'today'       => __( 'Today', 'jnews-view-counter' ),
						'last24hours' => __( '24H', 'jnews-view-counter' ),
						'last7days'   => __( '7D', 'jnews-view-counter' ),
						'last30days'  => __( '30D', 'jnews-view-counter' ),
						'custom'      => __( 'Custom', 'jnews-view-counter' ),
					);
					foreach ( $item_time_range as $key => $value ) {
						?>
							<option value="<?php echo $key; ?>" <?php echo selected( $key, $time_range ); ?>><?php echo $value; ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<div class="form-group custom-range-nav-field">
				<select name="custom-range-nav" id="custom-range-nav">
					<option value="custom-time-range"><?php esc_html_e( 'Time Range', 'jnews-view-counter' ); ?></option>
					<option value="date-range"><?php esc_html_e( 'Date Range', 'jnews-view-counter' ); ?></option>
				</select>
			</div>
		</div>
		<div class="custom-range-field">
			<div class="form-group custom-time-range active">
				<div class="form-group">
					<input type="number" id="time-quantity" name="time-quantity" value="<?php echo JNews_View_Counter()->options['config']['time_quantity']; ?>">
				</div>
				<div class="form-group">
					<select name="time-unit" id="time-unit">
						<?php
							$time_unit      = JNews_View_Counter()->options['config']['time_unit'];
							$item_time_unit = array(
								'minute' => __( 'Minute(s)', 'jnews-view-counter' ),
								'hour'   => __( 'Hour(s)', 'jnews-view-counter' ),
								'day'    => __( 'Day(s)', 'jnews-view-counter' ),
							);
							foreach ( $item_time_unit as $key => $value ) {
								?>
									<option value="<?php echo $key; ?>" <?php echo selected( $key, $time_unit ); ?>><?php echo $value; ?></option>
								<?php
							}
							?>
					</select>
				</div>
			</div>
			<div class="form-group date-range">
				<div class="form-group">
					<i class='fa fa-calendar-o' aria-hidden='true'></i>
					<input type="text" name="date-range-start" size="10">
				</div>
				<label for="date-range"><?php esc_html_e( 'To', 'jnews-view-counter' ); ?></label>
				<div class="form-group">
					<i class='fa fa-calendar-o' aria-hidden='true'></i>
					<input type="text" name="date-range-end" size="10"> 
				</div>
			</div>
		</div>
	</form>
	<div class='jnews-view-stats-chart-content'></div>
</div>
<div class="jnews-view-stats-post-wrapper">
	<ul class="jnews-view-stats-post-nav">
		<li data-content="most-viewed" class="active"><?php esc_html_e( 'Most Viewed', 'jnews-view-counter' ); ?></li>
		<li data-content="trending-now"><?php esc_html_e( 'Trending Now', 'jnews-view-counter' ); ?></li>
	</ul>
	<div class="jnews-view-stats-post-content-wrapper">
		<div class="jnews-view-stats-post-content"></div>
		<div class='module-overlay stats-post'>
			<div class='preloader_type preloader_<?php echo $loader; ?>'>
				<div class="module-preloader jeg_preloader dot">
					<span></span><span></span><span></span>
				</div>
				<div class="module-preloader jeg_preloader circle">
					<div class="jnews_preloader_circle_outer">
						<div class="jnews_preloader_circle_inner"></div>
					</div>
				</div>
				<div class="module-preloader jeg_preloader square">
					<div class="jeg_square">
						<div class="jeg_square_inner"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
