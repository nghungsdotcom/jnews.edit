<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Widget\Normal\Element;

use JNews\Widget\Normal\NormalWidgetInterface;
use Abraham\TwitterOAuth\TwitterOAuth;

Class SocialCounterWidget implements NormalWidgetInterface {
	/**
	 * @var string
	 */
	private $fb_key;
	private $gg_key;
	private $bh_key;
	private $vk_id;
	private $vk_token;
	private $tw_consumer_key;
	private $tw_consumer_secret;
	private $tw_access_token;
	private $tw_access_token_secret;
	private $cache_key = "jnews_social_counter_widget_cache";
	private $newtab;

	/**
	 * @var array
	 */
	private $data_cache;
	private $content;

	/**
	 * @var integer
	 */
	private $rss_count = 10;

	/**
	 * @var array
	 */
	private $queue;

	public function __construct() {
	}

	public function get_options() {
		$fields = array(
			'title' => array(
				'title' => esc_html__( 'Title', 'jnews' ),
				'desc'  => esc_html__( 'Title on widget header.', 'jnews' ),
				'type'  => 'text'
			),

			'column' => array(
				'title'   => esc_html__( 'Number of Column', 'jnews' ),
				'desc'    => esc_html__( 'Set the number of widget column.', 'jnews' ),
				'type'    => 'select',
				'default' => 'col1',
				'options' => array(
					'col1' => esc_html__( '1 Column', 'jnews' ),
					'col2' => esc_html__( '2 Columns', 'jnews' ),
					'col3' => esc_html__( '3 Columns', 'jnews' ),
					'col4' => esc_html__( '4 Columns', 'jnews' ),
				)
			),

			'style' => array(
				'title'   => esc_html__( 'Social Style', 'jnews' ),
				'desc'    => esc_html__( 'Choose your social counter style.', 'jnews' ),
				'type'    => 'select',
				'default' => 'light',
				'options' => array(
					'light'   => esc_html__( 'Light', 'jnews' ),
					'colored' => esc_html__( 'Colored', 'jnews' ),
				)
			),

			'newtab' => array(
				'title' => esc_html__( 'Open New Tab', 'jnews' ),
				'desc'  => esc_html__( 'Open social account page on new tab.', 'jnews' ),
				'type'  => 'checkbox',
			),

			'tw_consumer_key' => array(
				'title' => esc_html__( 'Twitter Consumer Key', 'jnews' ),
				'desc'  => sprintf( __( 'You can create an application and get Twitter Consumer Key <a href="%s" target="_blank">here</a>.', 'jnews' ), 'https://apps.twitter.com/' ),
				'type'  => 'text'
			),

			'tw_consumer_secret' => array(
				'title' => esc_html__( 'Twitter Consumer Secret', 'jnews' ),
				'desc'  => sprintf( __( 'You can create an application and get Twitter Consumer Secret <a href="%s" target="_blank">here</a>.', 'jnews' ), 'https://apps.twitter.com/' ),
				'type'  => 'text'
			),

			'tw_access_token' => array(
				'title' => esc_html__( 'Twitter Access Token', 'jnews' ),
				'desc'  => sprintf( __( 'You can create an application and get Twitter Access Token <a href="%s" target="_blank">here</a>.', 'jnews' ), 'https://apps.twitter.com/' ),
				'type'  => 'text'
			),

			'tw_access_token_secret' => array(
				'title' => esc_html__( 'Twitter Access Token Secret', 'jnews' ),
				'desc'  => sprintf( __( 'You can create an application and get Twitter Access Token Secret <a href="%s" target="_blank">here</a>.', 'jnews' ), 'https://apps.twitter.com/' ),
				'type'  => 'text'
			),

			'bh_key' => array(
				'title' => esc_html__( 'Behance API Key', 'jnews' ),
				'desc'  => sprintf( __( 'You can register Behance API Key <a href="%s" target="_blank">here</a>.', 'jnews' ), 'https://www.behance.net/dev/register' ),
				'type'  => 'text'
			),

			'vk_id' => array(
				'title' => esc_html__( 'VK User ID', 'jnews' ),
				'desc'  => esc_html__( 'Insert your VK user id.', 'jnews' ),
				'type'  => 'text'
			),

			'vk_token' => array(
				'title' => esc_html__( 'VK Service Token', 'jnews' ),
				'desc'  => esc_html__( 'Insert your VK service token.', 'jnews' ),
				'type'  => 'text'
			),

			'rss_count' => array(
				'title' => esc_html__( 'RSS Subscriber', 'jnews' ),
				'desc'  => esc_html__( 'Insert the number of RSS subscribers.', 'jnews' ),
				'type'  => 'text'
			),

			'account' => array(
				'title'     => esc_html__( 'Social Account', 'jnews' ),
				'desc'      => esc_html__( 'Add your social account list.', 'jnews' ),
				'type'      => 'repeater',
				'choices'   => array(
					'limit' => ''
				),
				'default'   => array(
					array(
						'social_icon' => 'facebook',
						'social_url'  => 'https://www.facebook.com/jegtheme/',
					),
					array(
						'social_icon' => 'twitter',
						'social_url'  => 'https://twitter.com/jegtheme',
					),
				),
				'row_label' => array(
					'type'  => 'text',
					'value' => esc_attr__( 'Social Account', 'jnews' ),
					'field' => false,
				),
				'fields'    => array(
					'social_icon' => array(
						'type'        => 'select',
						'label'       => esc_attr__( 'Social Account', 'jnews' ),
						'description' => esc_attr__( 'Choose your social account.', 'jnews' ),
						'default'     => '',
						'id'          => 'social_icon',
						'choices'     => array(
							''            => esc_attr__( 'Choose Icon', 'jnews' ),
							'facebook'    => esc_attr__( 'Facebook Page', 'jnews' ),
							'twitter'     => esc_attr__( 'Twitter', 'jnews' ),
							'pinterest'   => esc_attr__( 'Pinterest', 'jnews' ),
							'behance'     => esc_attr__( 'Behance', 'jnews' ),
							'flickr'      => esc_attr__( 'Flickr', 'jnews' ),
							'soundcloud'  => esc_attr__( 'Soundcloud', 'jnews' ),
							'instagram'   => esc_attr__( 'Instagram', 'jnews' ),
							'vimeo'       => esc_attr__( 'Vimeo', 'jnews' ),
							'youtube'     => esc_attr__( 'Youtube', 'jnews' ),
							'twitch'      => esc_attr__( 'Twitch', 'jnews' ),
							'vk'          => esc_attr__( 'VK', 'jnews' ),
							'rss'         => esc_attr__( 'RSS', 'jnews' ),
							'tiktok'      => esc_attr__( 'TikTok', 'jnews' ),
						),
					),
					'social_url'  => array(
						'type'        => 'text',
						'label'       => esc_attr__( 'Social URL', 'jnews' ),
						'description' => esc_attr__( 'Insert your social account url.', 'jnews' ),
						'default'     => '',
						'id'          => 'social_url',
					)
				),
			),
		);

		return $fields;
	}

	/**
	 * Initialize Widget
	 *
	 * @param  array $instance
	 */
	public function render_widget( $instance, $text_content = null ) {

		$facebook_key                 = get_option( 'jnews_option[jnews_facebook]', array() );
		$this->content                = $this->data_cache = null;
		$this->vk_id                  = isset( $instance['vk_id'] ) ? str_replace( 'id', '', $instance['vk_id'] ) : '';
		$this->vk_token               = isset( $instance['vk_token'] ) ? str_replace( 'token', '', $instance['vk_token'] ) : '';
		$this->fb_key                 = isset( $facebook_key['token'] ) ? $facebook_key['token'] : '';
		$this->gg_key                 = isset( $instance['gg_key'] ) ? $instance['gg_key'] : '';
		$this->bh_key                 = isset( $instance['bh_key'] ) ? $instance['bh_key'] : '';
		$this->rss_count              = isset( $instance['rss_count'] ) ? $instance['rss_count'] : '';
		$this->tw_consumer_key        = isset( $instance['tw_consumer_key'] ) ? $instance['tw_consumer_key'] : '';
		$this->tw_consumer_secret     = isset( $instance['tw_consumer_secret'] ) ? $instance['tw_consumer_secret'] : '';
		$this->tw_access_token        = isset( $instance['tw_access_token'] ) ? $instance['tw_access_token'] : '';
		$this->tw_access_token_secret = isset( $instance['tw_access_token_secret'] ) ? $instance['tw_access_token_secret'] : '';

		$this->render_content( $instance );
	}

	/**
	 * Render widget content
	 *
	 * @param  array $instance
	 */
	protected function render_content( $instance ) {

		/** For debugging */
		//delete_option( $this->cache_key, array() );

		$this->data_cache = get_option( $this->cache_key, array() );

		$this->newtab = isset( $instance['newtab'] ) ? 'target="_blank"' : '';

		$this->init_social( $instance );

		$output =
			"<ul class=\"jeg_socialcounter {$instance['column']} {$instance['style']}\">
                {$this->content}
            </ul>";

		echo jnews_sanitize_output( $output );
	}

	/**
	 * Init function
	 *
	 * @param  array $instance
	 */
	protected function init_social( $instance ) {
		if ( ! empty( $instance['account'] ) ) {
			$instance['account'] = json_decode( urldecode( $instance['account'] ) );

			if ( is_array( $instance['account'] ) ) {
				foreach ( $instance['account'] as $social ) {
					if ( empty( $social ) || ( empty( $social->social_url ) && $social->social_icon !== 'rss' ) ) {
						continue;
					}

					$this->service_social( $social );
				}
			}

		}
	}

	/**
	 * Build content for each social account
	 *
	 * @param  array $data
	 */
	protected function build_content( $data ) {
		$count = jnews_number_format( $data['social_data'] );

		if ( $count > 1 ) {
			switch ( $data['social_type'] ) {
				case 'facebook':
					$data['social_text'] = jnews_return_translation( 'Fans', 'jnews', 'fans' );
					break;
				case 'twitter':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
				case 'instagram':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
				case 'pinterest':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
				case 'vimeo':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
				case 'soundcloud':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
				case 'behance':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
				case 'flickr':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
				case 'twitch':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
				case 'vk':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
				case 'youtube':
					$data['social_text'] = jnews_return_translation( 'Subscribers', 'jnews', 'subscribers' );
					break;
				case 'rss':
					$data['social_text'] = jnews_return_translation( 'Subscribers', 'jnews', 'subscribers' );
					break;
				case 'tiktok':
					$data['social_text'] = jnews_return_translation( 'Followers', 'jnews', 'followers' );
					break;
			}
        }

		$this->content .=
			"<li class=\"jeg_{$data['social_type']}\">
                <a href=\"{$data['social_url']}\" {$this->newtab}><i class=\"fa fa-{$data['social_type']}\"></i>
                    <span>{$count}</span>
                    <small>{$data['social_text']}</small>
                </a>
            </li>";
	}

	/**
	 * Checking social type
	 *
	 * @param object $data
	 */
	protected function service_social( $data ) {
		switch ( $data->social_icon ) {
			case 'facebook':
				$social_id = wp_parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );
				if ( ! empty( $social_id ) && ! empty( $this->fb_key ) ) {
					$array = array(
						'social_type' => 'facebook',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Fan', 'jnews', 'fan' ),
						'social_url'  => $data->social_url,
						'social_grab' => 'https://graph.facebook.com/v11.0/' . $social_id . '?access_token=' . apply_filters( 'jnews_facebook_token_access', $this->fb_key ) . '&fields=followers_count',
					);

					$this->check_cache( $array );
				}
				break;

			case 'twitter':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );

				if ( ! empty( $social_id ) ) {
					$array = array(
						'social_type' => 'twitter',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
						'social_grab' => $data->social_url,
					);
					$this->check_cache( $array );
				}
				break;

			case 'instagram':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );

				if ( ! empty( $social_id ) ) {
					$array = array(
						'social_type' => 'instagram',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
						'social_grab' => $data->social_url,
					);
					$this->check_cache( $array );
				}
				break;

			case 'pinterest':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );

				if ( ! empty( $social_id ) ) {
					$array = array(
						'social_type' => 'pinterest',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
						'social_grab' => $data->social_url,
					);
					$this->check_cache( $array );
				}
				break;

			case 'vimeo':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );

				if ( ! empty( $social_id ) ) {
					$array = array(
						'social_type' => 'vimeo',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
						'social_grab' => 'https://vimeo.com/' . $social_id . '/following/followers/',
					);
					$this->check_cache( $array );
				}
				break;

			case 'soundcloud':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );

				if ( ! empty( $social_id ) ) {
					$array = array(
						'social_type' => 'soundcloud',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
						'social_grab' => $data->social_url,
					);
					$this->check_cache( $array );
				}
				break;

			case 'behance':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );

				if ( ! empty( $social_id ) && ! empty( $this->bh_key ) ) {
					$array = array(
						'social_type' => 'behance',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
						'social_grab' => 'https://api.behance.net/v2/users/' . $social_id . '?client_id=' . apply_filters( 'jnews_behance_token_access', $this->bh_key ),
					);
					$this->check_cache( $array );
				}
				break;

			case 'flickr':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );
				$social_id = str_replace( 'photos/', '', $social_id );

				if ( ! empty( $social_id ) ) {
					$array = array(
						'social_type' => 'flickr',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
						'social_grab' => $data->social_url,
					);
					$this->check_cache( $array );
				}
				break;

			case 'twitch':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );

				if ( ! empty( $social_id ) ) {
					$array = array(
						'social_type' => 'twitch',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
					);
					$this->check_cache( $array );
				}
				break;

			case 'vk':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );

				if ( ! empty( $social_id ) ) {
					$array = array(
						'social_type' => 'vk',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
						'social_grab' => 'https://api.vk.com/method/users.getFollowers?user_id=' . $this->vk_id . '&v=5.74&access_token=' . $this->vk_token,
					);
					$this->check_cache( $array );
				}
				break;

			case 'youtube':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );
				$social_grab = 'https://www.googleapis.com/youtube/v3/channels?part=statistics&key=' . apply_filters( 'jnews_youtube_token_access', $this->gg_key );

				if ( ! empty( $social_id ) && ! empty( $this->gg_key )) {
					$array = array(
						'social_type' => 'youtube',
						'social_text' => jnews_return_translation( 'Subscriber', 'jnews', 'subscriber' ),
						'social_url'  => $data->social_url,
						'social_grab' => $social_grab,
					);

					$social_id = explode( "/", $social_id );

					if ( is_array( $social_id ) ) {
						if ( $social_id[0] == 'channel' ) {
							$array['social_grab'] .= '&id=' . $social_id[1];
						} else {
							$array['social_grab'] .= '&forUsername=' . $social_id[1];
						}

						$array['social_id'] = $social_id[1];
					}

					$this->check_cache( $array );
				}
				break;

			case 'rss':
				if ( is_numeric( $this->rss_count ) ) {
					$array = array(
						'social_text' => jnews_return_translation( 'Subscriber', 'jnews', 'subscriber' ),
						'social_url'  => empty( $data->social_url ) ? esc_url( jnews_home_url_multilang( '/feed' ) ) : $data->social_url,
						'social_data' => $this->rss_count,
						'social_type' => 'rss',
					);

					$this->build_content( $array );
				}
				break;
			
			case 'tiktok':
				$social_id = parse_url( $data->social_url );
				$social_id = trim( $social_id['path'], '/' );

				if ( ! empty( $social_id ) ) {
					$array = array(
						'social_type' => 'tiktok',
						'social_id'   => $social_id,
						'social_text' => jnews_return_translation( 'Follower', 'jnews', 'follower' ),
						'social_url'  => $data->social_url,
						'social_grab' => 'https://www.tiktok.com/node/share/user/' . $social_id,
					);
					$this->check_cache( $array );
				}
				break;
		}
	}

	/**
	 * Check available data cached
	 *
	 * @param  array $data
	 */
	protected function check_cache( $data ) {
		$now          = current_time( 'timestamp' );
		$add_cache    = true;
		$cache_expire = apply_filters( 'jnews_social_counter_widget_expired', 60 * 60 * 24 );
		$data_count   = $update_cache = null;

		if ( ! empty( $this->data_cache ) && is_array( $this->data_cache ) ) {
			foreach ( $this->data_cache as &$social_data ) {
				if ( $data['social_type'] == $social_data['social_type'] && $data['social_id'] == $social_data['social_id'] ) {
					$add_cache = false;

					if ( $social_data['social_expire'] < ( $now - $cache_expire ) ) {
						$count = $this->fetch_data( $data );

						if ( ! empty( $count ) ) {
							$social_data['social_expire'] = current_time( 'timestamp' );
							$social_data['social_data']   = $count;
							$update_cache                 = true;
						}
					}

					$data_count = $social_data['social_data'];
				}
			}
		}

		if ( $add_cache ) {
			$data_count = $this->fetch_data( $data );

			if ( ! empty( $data_count ) ) {
				$this->data_cache[] = array(
					'social_type'   => $data['social_type'],
					'social_id'     => $data['social_id'],
					'social_expire' => current_time( 'timestamp' ),
					'social_data'   => $data_count,
				);
			} else {
				$add_cache = false;
			}
		}

		if ( $add_cache || $update_cache ) {
			update_option( $this->cache_key, $this->data_cache );
		}

		// call build content
		if ( ! empty( $data_count ) ) {
			$data['social_data'] = $data_count;
			$this->build_content( $data );
		}
	}

	/**
	 * Fetch data
	 *
	 * @param  array $data
	 *
	 * @return int
	 *
	 */
	protected function fetch_data( $data ) {
		if ( $data['social_type'] === 'twitter' ) {
			return $this->get_twitter_counter( $data['social_id'] );
        } elseif ( $data['social_type'] === 'instagram' ) {
            $response = jnews_get_instagram_data( $data['social_id'], 'user' );
        } elseif ( $data['social_type'] === 'twitch' ) {
            return jnews_get_twitch_data( $data['social_id'] );
		} else {
			$status_code = @get_headers( $data['social_grab'], 1 );
			$response = array();	
			if( strpos($status_code[0], '200') ) {
				$response = wp_remote_get( $data['social_grab'], array(
					'timeout' => 10,
				) );
			}
		}

		if ( ! is_wp_error( $response ) && isset( $response['response'] ) && isset( $response['response']['code'] ) && $response['response']['code'] == '200' ) {
			switch ( $data['social_type'] ) {
				case 'twitter':
					$pattern = "/<div class=\"statnum\">(.*?)<\/div>/";
					preg_match_all( $pattern, $response['body'], $matches );

					if ( ! empty( $matches[1][2] ) ) {
						$result = '';
						foreach ( str_split( $matches[1][2] ) as $char ) {
							if ( is_numeric( $char ) ) {
								$result .= $char;
							}
						}

						return (int) $result;
					}
					break;

				case 'instagram':
					if ( ! empty( $response['counts']['followed_by'] ) ) {
						return $response['counts']['followed_by'];
					}
					break;

				case 'pinterest':
					$pattern = "/name=\"pinterestapp:followers\" content=\"(.*?)\"/";
					preg_match( $pattern, $response['body'], $matches );

					if ( ! empty( $matches[1] ) ) {
						return (int) $matches[1];
					}
					break;

				case 'vimeo':
					$pattern = "/data-title=\"(.*?) Follower(s?)\"/";
					preg_match( $pattern, $response['body'], $matches );

					if ( ! empty( $matches[1] ) ) {
						$result = '';
						foreach ( str_split( $matches[1] ) as $char ) {
							if ( is_numeric( $char ) ) {
								$result .= $char;
							}
						}

						return (int) $result;
					}
					break;

				case 'soundcloud':
					$pattern = "/<meta property=\"soundcloud:follower_count\" content=\"(.*?)\">/";
					preg_match( $pattern, $response['body'], $matches );

					if ( ! empty( $matches[1] ) ) {
						return (int) $matches[1];
					}
					break;

				case 'youtube':
					$result = json_decode( $response['body'] );
					if ( ! empty( $result->items[0] ) ) {
						if ( ! $result->items[0]->statistics->hiddenSubscriberCount ) {
							return (int) $result->items[0]->statistics->subscriberCount;
						}
					}
					break;

				case 'facebook':
                    $result = json_decode( $response['body'] );
                    if ( ! empty($result->followers_count) ) {
                        return (int) $result->followers_count;
                    }
					break;

				case 'behance':
					$result = json_decode( $response['body'] );
					if ( ! empty( $result->user->stats->followers ) ) {
						return (int) $result->user->stats->followers;
					}
					break;

				case 'flickr':
					$pattern = "/\"followerCount\":(.*?),\"/";
					preg_match( $pattern, $response['body'], $matches );

					if ( ! empty( $matches[1] ) ) {
						return (int) $matches[1];
					}
					break;

				case 'twitch':
					$result = json_decode( $response['body'] );
					if ( ! empty( $result->followers ) ) {
						return $result->followers;
					}
					break;

				case 'vk':
					$result = json_decode( $response['body'] );
					if ( ! empty( $result->response->count ) ) {
						return $result->response->count;
					}
					break;
				
				case 'tiktok':
					$result = json_decode( $response['body'] );
					if ( ! empty( $result->body->userData->fans ) ) {
						return $result->body->userData->fans;
					}
					break;
			}
		}

		return null;
	}

	protected function get_twitter_counter( $id ) {
		$counter = 0;

		if ( isset( $this->tw_consumer_key ) && isset( $this->tw_consumer_secret ) && isset( $this->tw_access_token ) && isset( $this->tw_access_token_secret ) ) {
			if ( class_exists( 'Abraham\TwitterOAuth\TwitterOAuth' ) ) {
				$twitter  = new TwitterOAuth( $this->tw_consumer_key, $this->tw_consumer_secret, $this->tw_access_token, $this->tw_access_token_secret );
				$userinfo = $twitter->get( 'users/lookup', array( 'screen_name' => $id ) );

				if ( empty( $userinfo ) || $userinfo->errors ) {
					return $counter;
				}

				if ( $userinfo[0]->followers_count ) {
					$counter = (int) $userinfo[0]->followers_count;
				}
			}
		}

		return $counter;
	}

	protected function get_widget_template() {
	}
}
