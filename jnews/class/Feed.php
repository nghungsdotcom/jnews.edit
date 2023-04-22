<?php

namespace JNews;

class Feed {
	public function __construct( $feed_object, $attr ) {
	    $this->attr = $attr;
		$this->ID = jnews_get_rss_post_id();
		$this->title = $feed_object->get_title();
		$this->permalink = $feed_object->get_link();
		$this->description = $this->excerpt( $feed_object->get_description(), isset( $attr['excerpt_length'] ) ? $attr['excerpt_length'] : 20 );
		$this->post_author_name = isset( $feed_object->get_author()->name ) ? $feed_object->get_author()->name : '';
		$this->publish_date = $feed_object->get_date('U');
		$this->update_date = $feed_object->get_updated_date('U');
		$this->featured = $attr['thumbnail'] ? $this->thumbnail( $feed_object->get_thumbnail() ) : '';
	}

	private function excerpt( $description, $length ) {
	    return wp_trim_words( $description, isset( $length['size'] ) ? $length['size'] : $length );
    }

    private function thumbnail( $image ) {

        if ( is_array( $image ) ) {
            $image = $image['url'];
        }

        return $image ? '<img src="' . $image . '">' : '';
    }

    public function get_thumbnail( $size ) {
        $image_size = \JNews\Image\Image::getInstance()->get_image_size( $size );
		if ( isset( $this->attr['fallimage']['id'] ) ) {
			$fallimage = $this->attr['fallimage']['id'];
		} else {
			$fallimage = $this->attr['fallimage'];
		}
	    if ( ! $this->featured && $this->attr['fallback'] ) {
	        return "<div class=\"thumbnail-container size-{$image_size['dimension']} \">" . ( wp_get_attachment_image( $fallimage, $size ) ?: $this->featured ) . "</div>";
        }

	    return $this->featured;
    }
}