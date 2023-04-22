<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Module\Element;

use JNews\Module\ModuleViewAbstract;
use JNews\Util\VideoAttribute;

class Element_Embedplaylist_View extends ModuleViewAbstract {
	/**
	 * @var string
	 */
	private $meta_name = 'jnews_video_cache';

	/**
	 * @var string
	 */
	private $meta_playlist = 'jnews_playlist_cache';

	public function youtube_api() {
		return get_theme_mod( 'jnews_youtube_api' );
	}

	public function get_video_detail( $results ) {
		$vimeo = $youtube = $youtube_playlist = $video_detail = [];

		foreach ( $results as $key => $result ) {
			$result      = preg_replace( '/\s+/', '', $result );
			$provider    = VideoAttribute::getInstance()->get_video_provider( $result );
			$video_id    = VideoAttribute::getInstance()->get_video_id( $result );
			$playlist_id = is_array( $video_id ) ? $video_id['playlist'] : '';

			if ( $provider === 'vimeo' ) {
				$video_detail[ $video_id ]['type'] = 'vimeo';
				$vimeo[ $key ]                     = $video_id;
			} elseif ( $provider === 'youtube' ) {
				if ( empty( $playlist_id ) ) {
					$youtube[ $key ] = $video_id;
				} else {
					$youtube[ $key ]          = $playlist_id;
					$youtube_playlist[ $key ] = $playlist_id;
				}
			}
		}
		// proceed youtube playlist
		if ( ! empty( $youtube_playlist ) ) {
			$lists = $youtube_playlist;
			foreach ( $lists as $list => $id ) {
				$playlist_results = $this->get_playlist_item( $id );
				$playlist_cache[ $id ] = $playlist_results;
				$playlist_cache['expired'] = current_time( 'timestamp' );
				update_option( $this->meta_playlist, $playlist_cache );
				$youtube          = $this->insert_playlist( $youtube, $id, $playlist_results );
			}
		}

		// proceed youtube
		if ( ! empty( $youtube ) ) {
			$lists = array_chunk( $youtube, 50 );
			foreach ( $lists as $list => $id ) {
				$url            = 'https://www.googleapis.com/youtube/v3/videos?id=' . implode( ',', $id ) . '&part=id,contentDetails,snippet&key=' . $this->youtube_api();
				$youtube_remote = wp_remote_get( $url );

				if ( ! is_wp_error( $youtube_remote ) && $youtube_remote['response']['code'] == '200' ) {
					$youtube_remote = json_decode( $youtube_remote['body'] );

					foreach ( $youtube_remote->items as $item ) {
						$video_detail[ $item->id ]['type']      = isset( $video_detail[ $item->id ]['type'] ) ? $video_detail[ $item->id ]['type'] : 'youtube';
						$video_detail[ $item->id ]['url']       = isset( $video_detail[ $item->id ]['url'] ) ? $video_detail[ $item->id ]['url'] : 'https://www.youtube.com/watch?v=' . $item->id;
						$video_detail[ $item->id ]['id']        = isset( $video_detail[ $item->id ]['id'] ) ? $video_detail[ $item->id ]['id'] : $item->id;
						$video_detail[ $item->id ]['title']     = $item->snippet->title;
						$video_detail[ $item->id ]['thumbnail'] = $item->snippet->thumbnails->default->url;
						$video_detail[ $item->id ]['duration']  = jeg_video_duration( $item->contentDetails->duration );
					}
				}
			}
		}

		// proceed vimeo
		if ( ! empty( $vimeo ) ) {
			foreach ( $vimeo as $item ) {
				$url          = 'https://vimeo.com/api/oembed.json?url=https://vimeo.com/' . $item . '&width=1920&height=1080';
				$vimeo_remote = wp_remote_get( $url, [ 'base_url' => 'https://vimeo.com' ] );

				if ( ! is_wp_error( $vimeo_remote ) && $vimeo_remote['response']['code'] == '200' ) {
					$vimeo_remote    = json_decode( $vimeo_remote['body'], true );
					$thumbnail_1080p = $vimeo_remote['thumbnail_url'];
					if ( ! empty( $thumbnail_1080p ) ) {
						preg_match( '/((?:https?:)?\/\/i\.vimeocdn\.com\/(?:video)\/)((?:[A-Za-z0-9\-_]+)_(\d+)(?:|\.(?:[A-Za-z0-9\-_]+)))/', $thumbnail_1080p, $thumbnail_1080p );
						if ( is_array( $thumbnail_1080p ) ) {
							$thumbnail_1080p[2] = str_replace( $thumbnail_1080p[3], '1920', $thumbnail_1080p[2] );
							$thumbnail_1080p    = $thumbnail_1080p[1] . $thumbnail_1080p[2];
						}
					}

					$vimeo_id        = isset( $vimeo_remote[0]['id'] ) ? $vimeo_remote[0]['id'] : $vimeo_remote['video_id'];
					$vimeo_thumbnail = isset( $vimeo_remote['thumbnail_medium'] ) ? $vimeo_remote['thumbnail_medium'] : $vimeo_remote['thumbnail_url'];

					$video_detail[ $vimeo_id ]['title']     = $vimeo_remote['title'];
					$video_detail[ $vimeo_id ]['thumbnail'] = $vimeo_thumbnail;
					$video_detail[ $vimeo_id ]['duration']  = gmdate( 'H:i:s', intval( $vimeo_remote['duration'] ) );
					$video_detail[ $vimeo_id ]['url']       = 'https://vimeo.com/' . $item;
					$video_detail[ $vimeo_id ]['id']        = $video_id;
				}
			}
		}

		return $video_detail;
	}

	/**
	 * Build result, merge already retrieved post meta
	 *
	 * @param $results
	 *
	 * @return array
	 */
	public function build_result( $results ) {
		$post_id        = $this->get_post_id();
		$video_retrieve = $video_result = [];

		$now            = current_time( 'timestamp' );
		/** Test purpose */
		// delete_option( $this->meta_name );
		// delete_option( $this->meta_playlist );

		$video_cache = get_option( $this->meta_name, array() );

		// Update purpose, Delete it later.
		$force_clear_cache = get_option( 'jnews_clear_video_cache', true );
		if ( $force_clear_cache ) {
			update_option( 'jnews_clear_video_cache', false );
			delete_option( $this->meta_name );
		}

		if ( ! $video_cache ) {
			$video_cache = [];
		}
		$playlist_cache = get_option( $this->meta_playlist, [] );

		$expired = get_theme_mod( 'jnews_youtube_playlist_cache', 1 );

		if ( $expired != 'no' ) {
			$expired = $expired * 60 * 60;
		} else {
			$expired = 0;
		}

		if ( isset( $playlist_cache['expired'] ) && $playlist_cache['expired'] < ( $now - $expired ) ) {
			delete_option( $this->meta_playlist );
		}

		foreach ( $results as $key => $result ) {
			$result   = trim( $result );
			$video_id = VideoAttribute::getInstance()->get_video_id( $result );
			if ( is_array( $video_id ) ) {
				if ( ! array_key_exists( $video_id['playlist'], $playlist_cache ) ) {
					$video_retrieve[] = $result;
				}
			} else {
				if ( ! array_key_exists( $video_id, $video_cache ) ) {
					$video_retrieve[] = $result;
				} else {
					if ( ! isset( $video_cache[ $video_id ]['title'] ) ) {
						$video_retrieve[] = $result;
					}
				}
			}
		}

		if ( ! empty( $video_retrieve ) ) {
			$video_detail = $this->get_video_detail( $results );
			$results      = [];
			foreach ( $video_detail as $id => $detail ) {
				$results[] = $detail['url'];
			}
			$video_cache  = $video_detail + $video_cache;
			update_option( $this->meta_name, $video_detail );
		}

		$is_video_invalid = false;
		foreach ( $results as $key => $result ) {
			$result   = trim( $result );
			$video_id = VideoAttribute::getInstance()->get_video_id( $result );
			if ( is_array( $video_id ) ) {
				if ( array_key_exists( $video_id['playlist'], $playlist_cache ) ) {
					foreach ( $playlist_cache[ $video_id['playlist'] ] as $pkey => $plist ) {
						if ( isset( $video_cache[ $plist ] ) ) {
							$video_result[] = $video_cache[ $plist ];
						} else {
							$is_video_invalid = true;
						}
					}
				}
			} else {
				$video_result[] = $video_cache[ $video_id ];
			}
		}
		if ( $is_video_invalid ) {
			delete_option( $this->meta_name );
			delete_option( $this->meta_playlist );
			$video_result = $this->build_result( $results );
		}

		return $video_result;
	}

	/**
	 * Build playlist element
	 *
	 * @param $results
	 *
	 * @return string
	 */
	public function build_playlist( $results ) {
		$output = '';

		foreach ( $results as $key => $post ) {
			$active = $key === 0 ? 'active' : '';

			if ( ! isset( $post['thumbnail'] ) || ! isset( $post['duration'] ) || ! isset( $post['title'] ) ) {
				continue;
			}

			$output .=
				"<div class=\"jeg_video_playlist_item_wrapper\">
				<a class=\"jeg_video_playlist_item {$active}\" href=\"" . $post['url'] . '" data-id="' . $key . "\">
                    <div class=\"jeg_video_playlist_thumbnail\">
                        <img src='" . apply_filters( 'jnews_empty_image', '' ) . "' class=\"lazyload\" data-src='{$post['thumbnail']}'/>
                    </div>
                    <div class=\"jeg_video_playlist_description\">
                        <h3 class=\"jeg_video_playlist_title\">" . $post['title'] . '</h3>
                        <span class="jeg_video_playlist_category">' . $post['duration'] . '</span>
                    </div>
                </a>
				</div>';
		}

		return $output;
	}

	/**
	 * Build Video Wrapper
	 *
	 * @param $post_id
	 * @param $result
	 * @param $autoplay
	 *
	 * @return string
	 */
	public function get_video_wrapper( $post_id, $result, $autoplay, $autoload = false ) {
		$output   = '';
		$autoplay = $autoplay ? '&amp;autoplay=1;' : '';

		if ( empty( $result ) ) {
			return false;
		}

		$video_id = $result[ $post_id ]['id'];
		$wrapper  = $autoload ? 'div' : 'iframe';

		if ( $result[ $post_id ]['type'] === 'youtube' ) {

			$output .=
				"<div class=\"jeg_video_container\">
                    <{$wrapper} src=\"//www.youtube.com/embed/" . $video_id . '?showinfo=1' . $autoplay . "&amp;autohide=1&amp;rel=0&amp;wmode=opaque\" allowfullscreen=\"\" height=\"500\" width=\"700\"></{$wrapper}>
                </div>";
		} elseif ( $result[ $post_id ]['type'] === 'vimeo' ) {
			$output .=
				"<div class=\"jeg_video_container\">
                    <{$wrapper} src=\"//player.vimeo.com/video/" . $video_id . '?wmode=opaque' . $autoplay . "\" allowfullscreen=\"\" height=\"500\" width=\"700\"></{$wrapper}>
                </div>";
		}

		return $output;
	}

	/**
	 * Build data for json need. we remove ajax capability so video can load faster
	 *
	 * @param $results
	 *
	 * @return mixed|string
	 */
	public function build_data( $results ) {
		$json = [];

		foreach ( $results as $key => $post ) {
			$json[ $key ] = [
				'type' => $results[ $key ]['type'],
				'tag'  => $this->get_video_wrapper( $key, $results, true ),
			];
		}

		return wp_json_encode( $json );
	}

	public function explode_playlist( $playlist ) {
		$results = explode( ',', $playlist );
		$videos  = [];

		foreach ( $results as $result ) {
			$result = trim( $result );
			if ( ! empty( $result ) ) {
				$videos[] = $result;
			}
		}

		return $videos;
	}

	/**
	 * @param array      $array
	 * @param int|string $position
	 * @param mixed      $insert
	 */
	public function insert_playlist( &$youtube, $playlist_id, $playlists ) {

		if ( ! empty( $playlist_id ) ) {
			$key = array_search( $playlist_id, $youtube, true );
			unset( $youtube[ $key ] );
			$youtube = array_merge(
				array_slice( $youtube, 0, $key ),
				$playlists,
				array_slice( $youtube, $key )
			);
		}

		return $youtube;
	}

	/**
	 * @param $playlist_id
	 * @param $page_token
	 *
	 * @return array
	 */
	public function get_playlist_item( $playlist_id, $page_token = null ) {
		/**
		 * @todo https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=50&playlistId=PLAYLISTID&key=APIKEY
		 */
		$page_token     = ! is_null( $page_token ) ? '&pageToken=' . $page_token : '';
		$url            = 'https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=50&playlistId=' . $playlist_id . '&key=' . $this->youtube_api() . $page_token;
		$youtube_remote = wp_remote_get( $url );
		$youtube        = [];
		if ( ! is_wp_error( $youtube_remote ) && $youtube_remote['response']['code'] == '200' ) {
			$youtube_remote = json_decode( $youtube_remote['body'] );
			foreach ( $youtube_remote->items as $key => $item ) {
				$youtube[ $key ] = $item->contentDetails->videoId;
			}
			if ( isset( $youtube_remote->nextPageToken ) ) {
				$youtube = array_merge( $youtube, $this->get_playlist_item( $playlist_id, $youtube_remote->nextPageToken ) );
			}
		}

		return $youtube;
	}

	public function render_module( $attr, $column_class ) {

		$results  = $this->explode_playlist( $attr['playlist'] );
		$results  = $this->build_result( $results );
		$playlist = $this->build_playlist( $results );

		$col_width_raw         = isset( $attr['column_width'] ) && $attr['column_width'] != 'auto' ? $attr['column_width'] : $this->manager->get_current_width();
		$layout                = ( $attr['layout'] === 'vertical' ) ? 'jeg_horizontal_playlist' : 'jeg_vertical_playlist';
		$schema                = ( $attr['scheme'] === 'dark' ) ? 'jeg_dark_playlist' : '';
		$current_playlist_info = '';

		if ( ! empty( $results[0]['url'] ) && ! empty( $results[0]['title'] ) ) {
			$current_playlist_info = "<h2><a href='{$results[0]['url']}'>{$results[0]['title']}</a></h2>";
		}

		$output =
			"<div {$this->element_id($attr)} class=\"jeg_video_playlist embedplaylist jeg_col_{$col_width_raw} {$layout} {$schema} {$this->unique_id} {$this->get_vc_class_name()} {$attr['el_class']}\" data-unique='{$this->unique_id}'>
                <div class=\"jeg_video_playlist_wrapper\">
                    <div class=\"jeg_video_playlist_video_content\">
                        <div class=\"jeg_video_holder\">
                        	<div class='jeg_preview_slider_loader'><div class='jeg_preview_slider_loader_circle'></div>
                        	</div>
                            {$this->get_video_wrapper(0, $results, false, true)}
                        </div>
                    </div><!-- jeg_video_playlist_video_content -->

                    <div class=\"jeg_video_playlist_list_wrapper\">
                        <div class=\"jeg_video_playlist_current\">
                            <div class=\"jeg_video_playlist_play\">
                                <div class=\"jeg_video_playlist_play_icon\">
                                    <i class=\"fa fa-play\"></i>
                                </div>
                                <span>" . jnews_return_translation( 'Currently Playing', 'jnews', 'currently_playing' ) . "</span>
                            </div>
                            <div class=\"jeg_video_playlist_current_info\">{$current_playlist_info}</div>
                        </div>
                        <div class=\"jeg_video_playlist_list_inner_wrapper\">
                            {$playlist}
                        </div>
                    </div><!-- jeg_video_playlist_list_wrapper -->
                    <div style=\"clear: both;\"></div>
                </div><!-- jeg_video_playlist_wrapper -->
                <script> var {$this->unique_id} = {$this->build_data($results)}; </script>
            </div>";

		if ( ( SCRIPT_DEBUG || get_theme_mod( 'jnews_load_necessary_asset', false ) ) && ! is_user_logged_in() ) {
			wp_dequeue_style( 'jnews-scheme' );
			wp_enqueue_script( 'jnews-videoplaylist' );
			wp_enqueue_style( 'jnews-videoplaylist' );
			wp_enqueue_style( 'jnews-scheme' );
		}

		return $output;
	}

	public function render_column_alt( $result, $column_class ) {
	}

	public function render_column( $result, $column_class ) {
	}
}
