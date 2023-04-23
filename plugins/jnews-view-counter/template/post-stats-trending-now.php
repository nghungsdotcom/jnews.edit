<?php
if ( isset( $status ) && 'ok' === $status ) {
	$posts = $data['result'];
	if ( 0 < count( $posts ) ) {
		foreach ( $posts as $post ) {
			$post_status = get_post_status_object( get_post_status( $post->ID ) )->label;
			$view_count  = $post->pageviews;
			do_action( 'jnews_json_archive_push', $post->ID );
			?>
			<article <?php post_class( 'jeg_post jeg_pl_sm' ); ?>>
				<div class="jeg_thumb">
					<a href="<?php echo get_the_permalink( $post->ID ); ?>"><?php echo apply_filters( 'jnews_image_thumbnail', $post->ID, 'jnews-120x86' ); ?></a>
				</div>
				<div class="jeg_postblock_content">
					<h3 class="jeg_post_title">
						<a href="<?php echo get_the_permalink( $post->ID ); ?>"><?php echo get_the_title( $post->ID ); ?></a>
					</h3>
					<div class="jeg_post_meta">
						<div class="jeg_post_status <?php echo esc_attr( $post_status ); ?>"><?php echo esc_html( $post_status ); ?></div><span>–</span>
						<div class="jeg_meta_date"><a href="<?php echo get_the_permalink( $post->ID ); ?>"><?php echo esc_html( jeg_get_post_date() ); ?></a></div>
						<div class="jeg_meta_views"><a href="<?php echo get_the_permalink( $post->ID ); ?>"><i class="fa fa-eye"></i> <?php echo $view_count; ?> </a></div>
					</div>
					<div class="jeg_post_control">
						<a class="jeg_post_action edit" href="<?php echo get_the_permalink( $post->ID ); ?>"><?php esc_html_e( 'View Post', 'jnews-view-counter' ); ?></a>
					</div>
				</div>
			</article>
			<?php
		}
	} else {
		?>
			<p style="text-align: center;"><?php _e( "Looks like your site's activity is a little low right now.<br>Spread the word and come back later!", 'jnews-view-counter' ); ?></p>
		<?php
	}
}

