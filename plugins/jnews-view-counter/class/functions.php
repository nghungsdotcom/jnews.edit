<?php
if ( ! function_exists( 'jnews_get_views' ) ) {
	/**
	 * Gets views count.
	 *
	 * @param   int          $id             The Post ID.
	 * @param   string|array $range          Either an string (eg. 'last7days') or -since 5.3- an array (eg. ['range' => 'custom', 'time_unit' => 'day', 'time_quantity' => 7])
	 * @param   bool         $number_format  Whether to format the number (eg. 9,999) or not (eg. 9999)
	 * @return  string
	 */
	function jnews_get_views( $id = null, $range = null, $number_format = true ) {
		$attr       = array(
			'id'            => $id,
			'range'         => $range,
			'number_format' => $number_format,
		);
		$query_hash = 'query_hash_' . md5( serialize( $attr ) );
		$views      = wp_cache_get( $query_hash, 'jnews-view-counter' );
		if ( false === $views ) {
			$views = JNews_View_Counter()->counter->get_views( $id, $range, $number_format );
			wp_cache_set( $query_hash, $views, 'jnews-view-counter' );
		}
		return $views;
	}
}

if ( ! function_exists( 'jnews_view_counter_query' ) ) {
	/**
	 * Do Query
	 *
	 * @param $instance
	 * @return array
	 */
	function jnews_view_counter_query( $instance ) {
		$query_hash = 'query_hash_' . md5( serialize( $instance ) );
		$query      = wp_cache_get( $query_hash, 'jnews-view-counter' );
		if ( false === $query ) {
			$query = JNews_View_Counter()->counter->query( $instance );
			wp_cache_set( $query_hash, $query, 'jnews-view-counter' );
		}
		return $query;
	}
}
