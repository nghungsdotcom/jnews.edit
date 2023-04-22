<?php
/**
 * @author : Jegtheme
 */

namespace JNews\Module\Archive;

Class Archive_Pagination_View extends ArchiveViewAbstract {

	public function render_module_back( $attr, $column_class ) {
		return $this->build_pagination_module( $attr, 3, $column_class );
	}

	public function render_module_front( $attr, $column_class ) {
		return $this->build_pagination_module( $attr, false, $column_class );
	}

	public function build_pagination_module( $attr, $total, $column_class ) {
		$column_class .= ' ' . $this->unique_id . ' ' . $this->get_vc_class_name();
		return jnews_paging_navigation( $attr, $total, $column_class );
	}
}
