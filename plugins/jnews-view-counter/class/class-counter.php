<?php
/**
 * @author Jegtheme
 */

namespace JNEWS_VIEW_COUNTER;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Counter
 *
 * @package JNEWS_VIEW_COUNTER
 */
class Counter {
	/**
	 * Counter Construct.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'check_cookie' ), 1 );
		add_action( 'jnews_do_first_load_action', array( $this, 'response_counter' ), null, 2 );
		add_action( 'admin_init', array( $this, 'purge_post_data' ) );
	}

	public function purge_post_data() {
		if ( current_user_can( 'delete_posts' ) ) {
			add_action( 'delete_post', array( $this, 'purge_post' ), 10 );
		}
	}

	public function purge_post( $post_id ) {
		global $wpdb;

		$prefix = $wpdb->prefix . 'popularposts';

		if ( $wpdb->get_var( $wpdb->prepare( "SELECT postid FROM {$prefix}data WHERE postid = %d", $post_id ) ) ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$prefix}data WHERE postid = %d", $post_id ) );

			$wpdb->query( $wpdb->prepare( "DELETE FROM {$prefix}summary WHERE postid = %d", $post_id ) );
		}

		return true;
	}

	/**
	 * Add view counter action to first load action
	 *
	 * @param  array $response
	 * @param  array $action
	 * @return array
	 */
	public function response_counter( $response, $action ) {
		if ( in_array( 'view_counter', $action, true ) ) {
			$response['view_counter'] = $this->check_post_js();
		}

		return $response;
	}

	/**
	 * Check whether to count visit via Javascript request.
	 */
	public function check_post_js() {
		if ( isset( $_POST['jnews_id'] ) && Helper::is_number( $_POST['jnews_id'] ) && ( $post_id = (int) sanitize_text_field( $_POST['jnews_id'] ) ) > 0 ) {
			$exec_time = 0;

			$start  = Helper::microtime_float();
			$result = $this->check_post( $post_id );
			$end    = Helper::microtime_float();

			$exec_time += round( $end - $start, 6 );

			if ( $result ) {
				switch ( (string) $result ) {
					case 'robots':
						return ( 'JNews View Counter: Oops, We don\'t count robots as views!' );
						break;
					case 'visited':
						return ( 'JNews View Counter: Oops, Post already viewed!' );
						break;
					default:
						return ( 'JNews View Counter: OK. Execution time: ' . $exec_time . ' seconds' );
						break;
				}
			}
		}
		return ( 'JNews View Counter: Oops, could not update the views count!' );
	}

	/**
	 * Initialize cookie session.
	 */
	public function check_cookie() {
		// do not run in admin except for ajax requests
		if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}

		// assign cookie name
		$cookie_name = 'jnews_view_counter_visits' . ( is_multisite() ? '_' . get_current_blog_id() : '' );

		// is cookie set?
		if ( isset( $_COOKIE[ $cookie_name ] ) && ! empty( $_COOKIE[ $cookie_name ] ) ) {
			$visited_posts = $expirations = array();

			foreach ( $_COOKIE[ $cookie_name ] as $content ) {
				// is cookie valid?
				if ( preg_match( '/^(([0-9]+b[0-9]+a?)+)$/', $content ) === 1 ) {
					// get single id with expiration
					$expiration_ids = explode( 'a', $content );

					// check every expiration => id pair
					foreach ( $expiration_ids as $pair ) {
						$pair                            = explode( 'b', $pair );
						$expirations[]                   = (int) $pair[0];
						$visited_posts[ (int) $pair[1] ] = (int) $pair[0];
					}
				}
			}

			$this->cookie = array(
				'exists'        => true,
				'visited_posts' => $visited_posts,
				'expiration'    => max( $expirations ),
			);
		}
	}

	/**
	 * Check whether user has excluded roles.
	 *
	 * @param string $option
	 * @return bool
	 */
	public function is_user_role_excluded( $user_id, $option ) {
		$user = get_user_by( 'id', $user_id );

		if ( empty( $user ) ) {
			return false;
		}

		$roles = (array) $user->roles;

		if ( ! empty( $roles ) ) {
			foreach ( $roles as $role ) {
				if ( in_array( $role, $option, true ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get user real IP address.
	 *
	 * @return string
	 */
	public function get_user_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';

		foreach ( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key ) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
					// trim for safety measures.
					$ip = trim( $ip );

					// attempt to validate IP.
					if ( $this->validate_user_ip( $ip ) ) {
						continue;
					}
				}
			}
		}

		return $ip;
	}

	/**
	 * Ensure an ip address is both a valid IP and does not fall within a private network range.
	 *
	 * @param $ip string IP address
	 * @return bool
	 */
	public function validate_user_ip( $ip ) {
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
			return false;
		}

		return true;
	}

	/**
	 * Encrypt user IP
	 *
	 * @param int $ip
	 * @return string $encrypted_ip
	 */
	public function encrypt_ip( $ip ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$auth_iv  = defined( 'NONCE_KEY' ) ? NONCE_KEY : '';

		// mcrypt strong encryption
		if ( function_exists( 'mcrypt_encrypt' ) && defined( 'MCRYPT_BLOWFISH' ) ) {
			// get max key size of the mcrypt mode
			$max_key_size = mcrypt_get_key_size( MCRYPT_BLOWFISH, MCRYPT_MODE_CBC );
			$max_iv_size  = mcrypt_get_iv_size( MCRYPT_BLOWFISH, MCRYPT_MODE_CBC );

			$encrypt_key = mb_strimwidth( $auth_key, 0, $max_key_size );
			$encrypt_iv  = mb_strimwidth( $auth_iv, 0, $max_iv_size );

			$encrypted_ip = strtr( base64_encode( mcrypt_encrypt( MCRYPT_BLOWFISH, $encrypt_key, $ip, MCRYPT_MODE_CBC, $encrypt_iv ) ), '+/=', '-_,' );
			// simple encryption
		} elseif ( function_exists( 'gzdeflate' ) ) {
			$encrypted_ip = base64_encode( convert_uuencode( gzdeflate( $ip ) ) );
		}
		// no encryption
		else {
			$encrypted_ip = strtr( base64_encode( convert_uuencode( $ip ) ), '+/=', '-_,' );
		}

		return $encrypted_ip;
	}

	/**
	 * Decrypt user IP
	 *
	 * @param int $encrypted_ip
	 * @return string $ip
	 */
	public function decrypt_ip( $encrypted_ip ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$auth_iv  = defined( 'NONCE_KEY' ) ? NONCE_KEY : '';

		// mcrypt strong encryption
		if ( function_exists( 'mcrypt_decrypt' ) && defined( 'MCRYPT_BLOWFISH' ) ) {
			// get max key size of the mcrypt mode
			$max_key_size = mcrypt_get_key_size( MCRYPT_BLOWFISH, MCRYPT_MODE_CBC );
			$max_iv_size  = mcrypt_get_iv_size( MCRYPT_BLOWFISH, MCRYPT_MODE_CBC );

			$encrypt_key = mb_strimwidth( $auth_key, 0, $max_key_size );
			$encrypt_iv  = mb_strimwidth( $auth_iv, 0, $max_iv_size );

			$ip = mcrypt_decrypt( MCRYPT_BLOWFISH, $encrypt_key, base64_decode( strtr( $encrypted_ip, '-_,', '+/=' ) ), MCRYPT_MODE_CBC, $encrypt_iv );
			// simple encryption
		} elseif ( function_exists( 'gzinflate' ) ) {
			$ip = gzinflate( convert_uudecode( base64_decode( $encrypted_ip ) ) );
			// no encryption
		} else {
			$ip = convert_uudecode( base64_decode( strtr( $encrypted_ip, '-_,', '+/=' ) ) );
		}

		return $ip;
	}

	/**
	 * Save cookie function.
	 *
	 * @param int   $id
	 * @param array $cookie
	 * @param bool  $expired
	 */
	private function save_cookie( $id, $cookie = array(), $expired = true ) {
		$set_cookie = apply_filters( 'jnews_view_counter_maybe_set_cookie', true );

		// Cookie Notice compatibility
		if ( function_exists( 'cn_cookies_accepted' ) && ! cn_cookies_accepted() ) {
			$set_cookie = false;
		}

		if ( true !== $set_cookie ) {
			return $id;
		}

		$expiration = Helper::get_timestamp( JNews_View_Counter()->options['general']['time_between_counts']['type'], JNews_View_Counter()->options['general']['time_between_counts']['number'] );

		// assign cookie name
		$cookie_name = 'jnews_view_counter_visits' . ( is_multisite() ? '_' . get_current_blog_id() : '' );

		// is this a new cookie?
		if ( empty( $cookie ) ) {
			// set cookie
			setcookie( $cookie_name . '[0]', $expiration . 'b' . $id, $expiration, COOKIEPATH, COOKIE_DOMAIN, ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? true : false ), true );
		} else {
			if ( $expired ) {
				// add new id or change expiration date if id already exists
				$cookie['visited_posts'][ $id ] = $expiration;
			}

			// create copy for better foreach performance
			$visited_posts_expirations = $cookie['visited_posts'];

			// get current gmt time
			$time = time();

			// check whether viewed id has expired - no need to keep it in cookie (less size)
			foreach ( $visited_posts_expirations as $post_id => $post_expiration ) {
				if ( $time > $post_expiration ) {
					unset( $cookie['visited_posts'][ $post_id ] );
				}
			}

			// set new last expiration date if needed
			$cookie['expiration'] = max( $cookie['visited_posts'] );

			$cookies = $imploded = array();

			// create pairs
			foreach ( $cookie['visited_posts'] as $id => $exp ) {
				$imploded[] = $exp . 'b' . $id;
			}

			// split cookie into chunks (4000 bytes to make sure it is safe for every browser)
			$chunks = str_split( implode( 'a', $imploded ), 4000 );

			// more then one chunk?
			if ( count( $chunks ) > 1 ) {
				$last_id = '';

				foreach ( $chunks as $chunk_id => $chunk ) {
					// new chunk
					$chunk_c = $last_id . $chunk;

					// is it full-length chunk?
					if ( strlen( $chunk ) === 4000 ) {
						// get last part
						$last_part = strrchr( $chunk_c, 'a' );

						// get last id
						$last_id = substr( $last_part, 1 );

						// add new full-lenght chunk
						$cookies[ $chunk_id ] = substr( $chunk_c, 0, strlen( $chunk_c ) - strlen( $last_part ) );
					} else {
						// add last chunk
						$cookies[ $chunk_id ] = $chunk_c;
					}
				}
			} else {
				// only one chunk
				$cookies[] = $chunks[0];
			}

			foreach ( $cookies as $key => $value ) {
				// set cookie
				setcookie( $cookie_name . '[' . $key . ']', $value, $cookie['expiration'], COOKIEPATH, COOKIE_DOMAIN, ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? true : false ), true );
			}
		}
	}

	/**
	 * Save user IP function.
	 *
	 * @param int $id
	 */
	private function save_ip( $id ) {
		$set_cookie = apply_filters( 'jnews_view_counter_maybe_set_cookie', true );

		// Cookie Notice compatibility
		if ( function_exists( 'cn_cookies_accepted' ) && ! cn_cookies_accepted() ) {
			$set_cookie = false;
		}

		if ( $set_cookie !== true ) {
			return $id;
		}

		// get IP cached visits
		$ip_cache = get_transient( 'jnews_view_counter_ip_cache' );

		if ( ! $ip_cache ) {
			$ip_cache = array();
		}

		// get user IP address
		$user_ip = $this->encrypt_ip( $this->get_user_ip() );

		// get current time
		$current_time = time();

		// visit exists in transient?
		if ( isset( $ip_cache[ $id ][ $user_ip ] ) ) {
			if ( $current_time > $ip_cache[ $id ][ $user_ip ] + Helper::get_timestamp( JNews_View_Counter()->options['general']['time_between_counts']['type'], JNews_View_Counter()->options['general']['time_between_counts']['number'], false ) ) {
				$ip_cache[ $id ][ $user_ip ] = $current_time;
			} else {
				return;
			}
		} else {
			$ip_cache[ $id ][ $user_ip ] = $current_time;
		}

		// keep it light, only 10 records per post and maximum 100 post records (=> max. 1000 ip entries)
		// also, the data gets deleted after a week if there's no activity during this time...
		if ( count( $ip_cache[ $id ] ) > 10 ) {
			$ip_cache[ $id ] = array_slice( $ip_cache[ $id ], -10, 10, true );
		}

		if ( count( $ip_cache ) > 100 ) {
			$ip_cache = array_slice( $ip_cache, -100, 100, true );
		}

		set_transient( 'jnews_view_counter_ip_cache', $ip_cache, WEEK_IN_SECONDS );
	}

	public function check_post( $id ) {
		global $wpdb;
		$wpdb->show_errors();

		// get post id.
		$id = (int) $id;

		// get user id, from current user or static var in rest api request.
		$user_id = get_current_user_id();

		// empty id?
		if ( empty( $id ) ) {
			return;
		}

		$prefix = $wpdb->prefix . 'popularposts';

		// WPML support, get original post/page ID.
		if ( defined( 'ICL_LANGUAGE_CODE' ) && function_exists( 'icl_object_id' ) ) {
			global $sitepress;
			if ( isset( $sitepress ) ) { // avoids a fatal error with Polylang.
				$id = icl_object_id( $id, get_post_type( $id ), true, $sitepress->get_default_language() );
			} elseif ( function_exists( 'pll_default_language' ) ) { // adds Polylang support.
				$id = icl_object_id( $id, get_post_type( $id ), true, pll_default_language() );
			}
		}

		$now     = Helper::now();
		$curdate = Helper::curdate();
		$views   = 1;

		// before updating views count.
		if ( has_action( 'jnews_pre_update_views' ) ) {
			do_action( 'jnews_pre_update_views', $id, $views );
		}

		// strict counts?
		if ( JNews_View_Counter()->options['general']['strict_counts'] ) {
			// get IP cached visits.
			$ip_cache = get_transient( 'jnews_view_counter_ip_cache' );

			if ( ! $ip_cache ) {
				$ip_cache = array();
			}

			// get user IP address.
			$user_ip = $this->encrypt_ip( $this->get_user_ip() );

			// get current time.
			$current_time = time();

			// visit exists in transient?
			if ( isset( $ip_cache[ $id ][ $user_ip ] ) ) {
				if ( $current_time < $ip_cache[ $id ][ $user_ip ] + Helper::get_timestamp( JNews_View_Counter()->options['general']['time_between_counts']['type'], JNews_View_Counter()->options['general']['time_between_counts']['number'], false ) ) {
					return 'visited';
				}
			}
		}

		// get groups to check them faster.
		$groups = JNews_View_Counter()->options['general']['exclude']['groups'];

		// whether to count this user.
		if ( ! empty( $user_id ) ) {
			// exclude logged in users?
			if ( in_array( 'users', $groups, true ) ) {
				return;

				// exclude specific roles?
			} elseif ( in_array( 'roles', $groups, true ) && $this->is_user_role_excluded( $user_id, JNews_View_Counter()->options['general']['exclude']['roles'] ) ) {
				return;
			}
			// exclude guests?
		} elseif ( in_array( 'guests', $groups, true ) ) {
			return;
		}

		// whether to count robots.
		if ( in_array( 'robots', $groups, true ) && JNews_View_Counter()->crawler_detect->is_crawler() ) {
			return 'robots';
		}

		// cookie already existed?
		if ( isset( $this->cookie ) && is_array( $this->cookie ) && isset( $this->cookie['exists'] ) && $this->cookie['exists'] ) {
			// post already viewed but not expired?
			if ( in_array( $id, array_keys( $this->cookie['visited_posts'] ), true ) && Helper::get_timestamp( 'minutes', 0 ) < $this->cookie['visited_posts'][ $id ] ) {
				// update cookie but do not count visit
				$this->save_cookie( $id, $this->cookie, false );

				return 'visited';
				// update cookie
			} else {
				$this->save_cookie( $id, $this->cookie );
			}
		} else {
			// set new cookie
			$this->save_cookie( $id );
		}

			// strict counts?
		if ( JNews_View_Counter()->options['general']['strict_counts'] ) {
			$this->save_ip( $id );
		}

		// Update all-time table
		$result1 = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$prefix}data
				(postid, day, last_viewed, pageviews) VALUES (%d, %s, %s, %d)
				ON DUPLICATE KEY UPDATE pageviews = pageviews + %d, last_viewed = %s;",
				$id,
				$now,
				$now,
				$views,
				$views,
				$now
			)
		);

		// Update range (summary) table
		$result2 = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$prefix}summary
				(postid, pageviews, view_date, view_datetime) VALUES (%d, %d, %s, %s)
				ON DUPLICATE KEY UPDATE pageviews = pageviews + %d, view_datetime = %s;",
				$id,
				$views,
				$curdate,
				$now,
				$views,
				$now
			)
		);

		if ( ! $result1 || ! $result2 ) {
			return false;
		}

		// after updating views count
		if ( has_action( 'jnews_post_update_views' ) ) {
			do_action( 'jnews_post_update_views', $id );
		}

		return true;
	}

	public function get_views( $id = null, $range = null, $number_format = true ) {
		// have we got an id?
		if ( empty( $id ) || is_null( $id ) || ! is_numeric( $id ) ) {
			return '-1';
		}

		global $wpdb;

		$prefix = $wpdb->prefix . 'popularposts';
		$args   = array(
			'range'         => 'all',
			'time_unit'     => 'hour',
			'time_quantity' => 24,
			'_postID'       => $id,
		);

		if ( is_array( $range ) ) {
			$args = Helper::merge_array_r( $args, $range );
		} else {
			$range         = is_string( $range ) ? trim( $range ) : null;
			$args['range'] = ! $range ? 'all' : $range;
		}

		$args['range'] = strtolower( $args['range'] );

		// Get all-time views count
		if ( 'all' == $args['range'] ) {
			$query = "SELECT pageviews FROM {$prefix}data WHERE postid = '{$id}'";
		} // Get views count within time range
		else {
			$start_date = new \DateTime(
				Helper::now(),
				new \DateTimeZone( Helper::get_timezone() )
			);

			// Determine time range
			switch ( $args['range'] ) {
				case 'last24hours':
				case 'daily':
					$start_date       = $start_date->sub( new \DateInterval( 'P1D' ) );
					$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
					$views_time_range = "view_datetime >= '{$start_datetime}'";
					break;
				case 'last7days':
				case 'weekly':
					$start_date       = $start_date->sub( new \DateInterval( 'P6D' ) );
					$start_datetime   = $start_date->format( 'Y-m-d' );
					$views_time_range = "view_date >= '{$start_datetime}'";
					break;
				case 'last30days':
				case 'monthly':
					$start_date       = $start_date->sub( new \DateInterval( 'P29D' ) );
					$start_datetime   = $start_date->format( 'Y-m-d' );
					$views_time_range = "view_date >= '{$start_datetime}'";
					break;
				case 'custom':
					$time_units = array( 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH' );

					// Valid time unit
					if ( isset( $args['time_unit'] ) && in_array( strtoupper( $args['time_unit'] ), $time_units ) && isset( $args['time_quantity'] ) && filter_var( $args['time_quantity'], FILTER_VALIDATE_INT ) && $args['time_quantity'] > 0 ) {
						$time_quantity = $args['time_quantity'];
						$time_unit     = strtoupper( $args['time_unit'] );

						if ( 'MINUTE' == $time_unit ) {
							$start_date       = $start_date->sub( new \DateInterval( 'PT' . ( 60 * $time_quantity ) . 'S' ) );
							$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
							$views_time_range = "view_datetime >= '{$start_datetime}'";
						} elseif ( 'HOUR' == $time_unit ) {
							$start_date       = $start_date->sub( new \DateInterval( 'PT' . ( ( 60 * $time_quantity ) - 1 ) . 'M59S' ) );
							$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
							$views_time_range = "view_datetime >= '{$start_datetime}'";
						} elseif ( 'DAY' == $time_unit ) {
							$start_date       = $start_date->sub( new \DateInterval( 'P' . ( $time_quantity - 1 ) . 'D' ) );
							$start_datetime   = $start_date->format( 'Y-m-d' );
							$views_time_range = "view_date >= '{$start_datetime}'";
						} elseif ( 'WEEK' == $time_unit ) {
							$start_date       = $start_date->sub( new \DateInterval( 'P' . ( ( 7 * $time_quantity ) - 1 ) . 'D' ) );
							$start_datetime   = $start_date->format( 'Y-m-d' );
							$views_time_range = "view_date >= '{$start_datetime}'";
						} else {
							$start_date       = $start_date->sub( new \DateInterval( 'P' . ( ( 30 * $time_quantity ) - 1 ) . 'D' ) );
							$start_datetime   = $start_date->format( 'Y-m-d' );
							$views_time_range = "view_date >= '{$start_datetime}'";
						}
					} // Invalid time unit, default to last 24 hours
					else {
						$start_date       = $start_date->sub( new \DateInterval( 'P1D' ) );
						$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
						$views_time_range = "view_datetime >= '{$start_datetime}'";
					}

					break;
				default:
					$start_date       = $start_date->sub( new \DateInterval( 'P1D' ) );
					$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
					$views_time_range = "view_datetime >= '{$start_datetime}'";
					break;
			}

			$query = $wpdb->prepare(
				"SELECT SUM(pageviews) AS pageviews FROM `{$prefix}summary` WHERE {$views_time_range} AND postid = %d;",
				$args['_postID']
			);
		}

		$results = $wpdb->get_var( $query );

		if ( ! $results ) {
			return 0;
		}

		return $number_format ? number_format_i18n( intval( $results ) ) : $results;
	}

	public function query( $instance ) {
		/**
		 * @var wpdb $wpdb
		 */
		global $wpdb;
		$default = array(
			'limit'            => 10,
			'offset'           => 0,
			'paged'            => 1,
			'range'            => 'all',
			'freshness'        => false,
			'order_by'         => 'views',
			'post_type'        => 'post',
			'include_post'     => '',
			'exclude_post'     => '',
			'include_category' => '',
			'exclude_category' => '',
			'include_tag'      => '',
			'exclude_tag'      => '',
			'author'           => '',
			'post_format'      => '',
		);

		if ( isset( $wpdb ) ) {
			$prefix = $wpdb->prefix . 'popularposts';

			$instance = Helper::merge_array_r(
				$default,
				(array) $instance
			);

			$now     = new \DateTime( Helper::now(), new \DateTimeZone( Helper::get_timezone() ) );
			$args    = array();
			$fields  = 'p.ID AS id, p.post_title AS title, p.post_author AS uid';
			$table   = '';
			$join    = '';
			$where   = 'WHERE 1 = 1';
			$groupby = '';
			$orderby = '';
			$limit   = 'LIMIT ' . ( filter_var( $instance['limit'], FILTER_VALIDATE_INT ) && $instance['limit'] > 0 ? $instance['limit'] : 10 ) . ( isset( $instance['offset'] ) && filter_var( $instance['offset'], FILTER_VALIDATE_INT ) !== false && $instance['offset'] >= 0 ? " OFFSET {$instance['offset']}" : '' );

			// Get post date
			$fields .= ', p.post_date AS date';

			// we don't need get get post excerpt $instance

			// we only support one post_type at once
			if ( isset( $instance['post_type'] ) && ! empty( $instance['post_type'] ) ) {

				$post_types = explode( ',', $instance['post_type'] );
				$pt         = '';
				$where     .= ' AND p.post_type IN(';

				foreach ( $post_types as $post_type ) {
					$pt .= '%s, ';
					array_push( $args, trim( $post_type ) );
				}

				$where .= rtrim( $pt, ', ' ) . ')';

			} else {
				$where .= " AND p.post_type IN('post')";
			}

			// Get entries from these authors
			if ( isset( $instance['author'] ) && ! empty( $instance['author'] ) ) {

				$author_IDs = explode( ',', $instance['author'] );
				$uid        = '';
				$where     .= ' AND p.post_author IN(';

				foreach ( $author_IDs as $author_ID ) {
					$uid .= '%d, ';
					array_push( $args, trim( $author_ID ) );
				}

				$where .= rtrim( $uid, ', ' ) . ')';

			}

			// Custom Get / exclude entries from this taxonomies
			if ( isset( $instance['include_category'] ) && ! empty( $instance['include_category'] ) || isset( $instance['exclude_category'] ) && ! empty( $instance['exclude_category'] ) || isset( $instance['include_tag'] ) && ! empty( $instance['include_tag'] ) || isset( $instance['exclude_tag'] ) && ! empty( $instance['exclude_tag'] ) ) {
				$taxonomies                  = array(
					'category',
					'post_tag',
				);
				$in_term_IDs_for_taxonomies  = array();
				$out_term_IDs_for_taxonomies = array();
				if ( isset( $instance['include_category'] ) && ! empty( $instance['include_category'] ) || isset( $instance['exclude_category'] ) && ! empty( $instance['exclude_category'] ) ) {
					$in_term_IDs_for_taxonomies[]  = isset( $instance['include_category'] ) && ! empty( $instance['include_category'] ) ? explode( ',', $instance['include_category'] ) : array();
					$out_term_IDs_for_taxonomies[] = isset( $instance['exclude_category'] ) && ! empty( $instance['exclude_category'] ) ? explode( ',', $instance['exclude_category'] ) : array();
				}
				if ( isset( $instance['include_tag'] ) && ! empty( $instance['include_tag'] ) || isset( $instance['exclude_tag'] ) && ! empty( $instance['exclude_tag'] ) ) {
					$in_term_IDs_for_taxonomies[]  = isset( $instance['include_tag'] ) && ! empty( $instance['include_tag'] ) ? explode( ',', $instance['include_tag'] ) : array();
					$out_term_IDs_for_taxonomies[] = isset( $instance['exclude_tag'] ) && ! empty( $instance['exclude_tag'] ) ? explode( ',', $instance['exclude_tag'] ) : array();
				}
				foreach ( $taxonomies as $taxIndex => $taxonomy ) {
					$in_term_IDs  = isset( $in_term_IDs_for_taxonomies[ $taxIndex ] ) ? $in_term_IDs_for_taxonomies[ $taxIndex ] : array();
					$out_term_IDs = isset( $out_term_IDs_for_taxonomies[ $taxIndex ] ) ? $out_term_IDs_for_taxonomies[ $taxIndex ] : array();

					$is_mixed = ! empty( $in_term_IDs ) && ! empty( $out_term_IDs );

					if ( $is_mixed ) {
						$where .= " AND p.ID IN (
								SELECT object_id
								FROM `{$wpdb->term_relationships}` AS r
								JOIN `{$wpdb->term_taxonomy}` AS x ON x.term_taxonomy_id = r.term_taxonomy_id
								WHERE x.taxonomy = %s";

						array_push( $args, $taxonomy );

						$inTID = '';

						foreach ( $in_term_IDs as $in_term_ID ) {
							$inTID .= '%d, ';
							array_push( $args, $in_term_ID );
						}

						$outTID = '';

						foreach ( $out_term_IDs as $out_term_ID ) {
							$outTID .= '%d, ';
							array_push( $args, $out_term_ID );
						}

						$where .= ' AND x.term_id IN(' . rtrim( $inTID, ', ' ) . ') AND x.term_id NOT IN(' . rtrim( $outTID, ', ' ) . ') )';
					} else {
						$pattern  = ! empty( $in_term_IDs ) ? 'IN' : 'NOT IN';
						$term_IDs = ! empty( $in_term_IDs ) ? $in_term_IDs : $out_term_IDs;
						if ( ! empty( $term_IDs ) ) {
							$where .= " AND p.ID {$pattern} (
										SELECT object_id
										FROM `{$wpdb->term_relationships}` AS r
										JOIN `{$wpdb->term_taxonomy}` AS x ON x.term_taxonomy_id = r.term_taxonomy_id";

							array_push( $args, $taxonomy );

							$TID = '';
							foreach ( $term_IDs as $term_ID ) {
								$TID .= '%d, ';
								array_push( $args, $term_ID );
							}

							$where .= ' AND x.term_id IN(' . rtrim( $TID, ', ' ) . ') )';
						}
					}
				}
			}

			// Custom Include these entries from the listing
			if ( isset( $instance['include_post'] ) && ! empty( $instance['include_post'] ) ) {
				$included_post_IDs = explode( ',', $instance['include_post'] );
				$ipid              = '';
				$where            .= ' AND p.ID IN(';

				foreach ( $included_post_IDs as $included_post_ID ) {
					$ipid .= '%d, ';
					array_push( $args, trim( $included_post_ID ) );
				}

				$where .= rtrim( $ipid, ', ' ) . ')';
			}

			// Custom Exclude these entries from the listing
			if ( isset( $instance['exclude_post'] ) && ! empty( $instance['exclude_post'] ) ) {
				$excluded_post_IDs = explode( ',', $instance['exclude_post'] );
				$xpid              = '';
				$where            .= ' AND p.ID NOT IN(';

				foreach ( $excluded_post_IDs as $excluded_post_ID ) {
					$xpid .= '%d, ';
					array_push( $args, trim( $excluded_post_ID ) );
				}

				$where .= rtrim( $xpid, ', ' ) . ')';
			}

			$table = "`{$wpdb->posts}` p";

			if ( isset( $instance['post_format'] ) && ! empty( $instance['post_format'] ) ) {
				array_push( $args, $instance['post_format'] );
				$where .= " AND p.ID IN ( SELECT object_id FROM {$wpdb->term_relationships} r INNER JOIN {$wpdb->terms} t ON t.term_id = r.term_taxonomy_id WHERE t.name = %s ) ";
			}

			if ( isset( $instance['content_type'] ) && ! empty( $instance['content_type'] ) ) {
				if ( 'review' === $instance['content_type'] ) {
					$where .= " AND p.ID IN ( SELECT m.post_id FROM {$wpdb->postmeta} m WHERE m.meta_key = 'enable_review' AND m.meta_value = 1 ) ";
				}

				if ( 'post' === $instance['content_type'] ) {
					$where .= " AND p.ID NOT IN ( SELECT m.post_id FROM {$wpdb->postmeta} m WHERE m.meta_key = 'enable_review' AND m.meta_value = 1 ) ";
				}
			}

			// All-time range
			if ( 'all' == $instance['range'] ) {

				// Order by views count
				if ( 'comments' != $instance['order_by'] ) {

					$join = "INNER JOIN `{$prefix}data` v ON p.ID = v.postid";

					// Order by views
					if ( 'views' == $instance['order_by'] ) {
						$fields .= ', v.pageviews AS total_count';
						$orderby = 'ORDER BY total_count DESC';
					}
					// Order by average views
					else {
						$fields .= ", ( v.pageviews/(IF ( DATEDIFF('{$now->format('Y-m-d')}', MIN(v.day)) > 0, DATEDIFF('{$now->format('Y-m-d')}', MIN(v.day)), 1) ) ) AS total_count";
						$groupby = 'GROUP BY v.postid';
						$orderby = 'ORDER BY total_count DESC';
					}

					// Display comments count, too
					$fields .= ', p.comment_count';
				}
				// Order by comments count
				else {
					$where  .= ' AND p.comment_count > 0';
					$orderby = 'ORDER BY p.comment_count DESC';

					// Display comment count
					$fields .= ', p.comment_count';

					// Display views count, too
					// if ( isset($instance['stats_tag']['views']) && $instance['stats_tag']['views'] ) {
					// $fields .= ", IFNULL(v.pageviews, 0) AS pageviews";
					// $join = "INNER JOIN `{$prefix}data` v ON p.ID = v.postid";
					// }
				}
			}
			// Custom time range
			else {
				$start_date = clone $now;

				// Determine time range
				switch ( $instance['range'] ) {
					case 'last24hours':
					case 'daily':
						$start_date       = $start_date->sub( new \DateInterval( 'P1D' ) );
						$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
						$views_time_range = "view_datetime >= '{$start_datetime}'";
						break;
					case 'last7days':
					case 'weekly':
						$start_date       = $start_date->sub( new \DateInterval( 'P6D' ) );
						$start_datetime   = $start_date->format( 'Y-m-d' );
						$views_time_range = "view_date >= '{$start_datetime}'";
						break;
					case 'last30days':
					case 'monthly':
						$start_date       = $start_date->sub( new \DateInterval( 'P29D' ) );
						$start_datetime   = $start_date->format( 'Y-m-d' );
						$views_time_range = "view_date >= '{$start_datetime}'";
						break;
					case 'custom':
						$time_units = array( 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH' );

						// Valid time unit
						if ( isset( $instance['time_unit'] ) && in_array( strtoupper( $instance['time_unit'] ), $time_units ) && isset( $instance['time_quantity'] ) && filter_var( $instance['time_quantity'], FILTER_VALIDATE_INT ) && $instance['time_quantity'] > 0 ) {
							$time_quantity = $instance['time_quantity'];
							$time_unit     = strtoupper( $instance['time_unit'] );

							if ( 'MINUTE' == $time_unit ) {
								$start_date       = $start_date->sub( new \DateInterval( 'PT' . ( 60 * $time_quantity ) . 'S' ) );
								$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
								$views_time_range = "view_datetime >= '{$start_datetime}'";
							} elseif ( 'HOUR' == $time_unit ) {
								$start_date       = $start_date->sub( new \DateInterval( 'PT' . ( ( 60 * $time_quantity ) - 1 ) . 'M59S' ) );
								$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
								$views_time_range = "view_datetime >= '{$start_datetime}'";
							} elseif ( 'DAY' == $time_unit ) {
								$start_date       = $start_date->sub( new \DateInterval( 'P' . ( $time_quantity - 1 ) . 'D' ) );
								$start_datetime   = $start_date->format( 'Y-m-d' );
								$views_time_range = "view_date >= '{$start_datetime}'";
							} elseif ( 'WEEK' == $time_unit ) {
								$start_date       = $start_date->sub( new \DateInterval( 'P' . ( ( 7 * $time_quantity ) - 1 ) . 'D' ) );
								$start_datetime   = $start_date->format( 'Y-m-d' );
								$views_time_range = "view_date >= '{$start_datetime}'";
							} else {
								$start_date       = $start_date->sub( new \DateInterval( 'P' . ( ( 30 * $time_quantity ) - 1 ) . 'D' ) );
								$start_datetime   = $start_date->format( 'Y-m-d' );
								$views_time_range = "view_date >= '{$start_datetime}'";
							}
						} // Invalid time unit, default to last 24 hours
						else {
							$start_date       = $start_date->sub( new \DateInterval( 'P1D' ) );
							$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
							$views_time_range = "view_datetime >= '{$start_datetime}'";
						}

						break;
					default:
						$start_date       = $start_date->sub( new \DateInterval( 'P1D' ) );
						$start_datetime   = $start_date->format( 'Y-m-d H:i:s' );
						$views_time_range = "view_datetime >= '{$start_datetime}'";
						break;
				}

				// Get entries published within the specified time range
				if ( isset( $instance['freshness'] ) && $instance['freshness'] ) {
					$where .= " AND p.post_date >= '{$start_datetime}'";
				}

				// Order by views count
				if ( 'comments' != $instance['order_by'] ) {
					// Order by views
					if ( 'views' == $instance['order_by'] ) {
						$fields .= ', v.pageviews AS total_count';
						$join    = "INNER JOIN (SELECT SUM(pageviews) AS pageviews, postid FROM `{$prefix}summary` WHERE {$views_time_range} GROUP BY postid) v ON p.ID = v.postid";
						$orderby = 'ORDER BY total_count DESC';
					}
					// Order by average views
					else {
						$fields .= ', v.avg_views AS total_count';
						$join    = "INNER JOIN (SELECT SUM(pageviews)/(IF ( DATEDIFF('{$now->format('Y-m-d H:i:s')}', '{$start_datetime}') > 0, DATEDIFF('{$now->format('Y-m-d H:i:s')}', '{$start_datetime}'), 1) ) AS avg_views, postid FROM `{$prefix}summary` WHERE {$views_time_range} GROUP BY postid) v ON p.ID = v.postid";
						$orderby = 'ORDER BY total_count DESC';
					}

					// Display comments count, too
					// if ( isset( $instance['stats_tag']['comment_count'] ) && $instance['stats_tag']['comment_count'] ) {
					// $fields .= ', IFNULL(c.comment_count, 0) AS comment_count';
					// $join   .= " LEFT JOIN (SELECT comment_post_ID, COUNT(comment_post_ID) AS comment_count FROM `{$wpdb->comments}` WHERE comment_date_gmt >= '{$start_datetime}' AND comment_approved = '1' GROUP BY comment_post_ID) c ON p.ID = c.comment_post_ID";
					// }
				}
				// Order by comments count
				else {
					$fields .= ', c.comment_count AS total_count';
					$join    = "INNER JOIN (SELECT COUNT(comment_post_ID) AS comment_count, comment_post_ID FROM `{$wpdb->comments}` WHERE comment_date_gmt >= '{$start_datetime}' AND comment_approved = '1' GROUP BY comment_post_ID) c ON p.ID = c.comment_post_ID";
					$orderby = 'ORDER BY total_count DESC';

					// Display views count, too
					// if ( isset( $instance['stats_tag']['views'] ) && $instance['stats_tag']['views'] ) {
					// $fields .= ', v.pageviews';
					// $join   .= " INNER JOIN (SELECT SUM(pageviews) AS pageviews, postid FROM `{$prefix}summary` WHERE {$views_time_range} GROUP BY postid) v ON p.ID = v.postid";
					// }
				}
			}

			// List only published, non password-protected posts
			$where .= " AND p.post_password = '' AND p.post_status = 'publish'";

			if ( ! empty( $args ) ) {
				$where = $wpdb->prepare( $where, $args );
			}

			$fields  = apply_filters( 'jnews_view_counter_query_fields', $fields, $instance );
			$table   = apply_filters( 'jnews_view_counter_query_table', $table, $instance );
			$join    = apply_filters( 'jnews_view_counter_query_join', $join, $instance );
			$where   = apply_filters( 'jnews_view_counter_query_where', $where, $instance );
			$groupby = apply_filters( 'jnews_view_counter_query_group_by', $groupby, $instance );
			$orderby = apply_filters( 'jnews_view_counter_query_order_by', $orderby, $instance );
			$limit   = apply_filters( 'jnews_view_counter_query_limit', $limit, $instance );

			// Finally, build the query
			$query = "SELECT {$fields} FROM {$table} {$join} {$where} {$groupby} {$orderby} {$limit};";
			$query = $wpdb->get_results( $query );

			if ( isset( $instance['no_found_rows'] ) && ! $instance['no_found_rows'] ) {
				$total_row = $wpdb->get_results( "SELECT COUNT(*) as total FROM {$table} {$join} {$where} {$groupby}" );
				$total_row = $groupby ? count( $total_row ) : $total_row[0]->total;
			} else {
				$total_row = 0;
			}

			$result_ids = array();
			$pagesviews = array();

			foreach ( $query as $result ) {
				$result_ids[]              = Helper::get_translate_id( $result->id );
				$pagesviews[ $result->id ] = $result->total_count;
			}

			$all_post = get_posts(
				array(
					'post__in'  => $result_ids,
					'post_type' => 'post',
					'showposts' => empty( $result_ids ) ? $instance['limit'] : count( $result_ids ),
				)
			);

			$results = Helper::arrange_index( $all_post, $result_ids, $pagesviews );

			return array(
				'result' => $results,
				'total'  => $total_row,
			);
		}
	}

}
