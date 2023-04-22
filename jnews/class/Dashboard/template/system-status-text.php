<?php
$revert_dashboard = apply_filters( 'jnews_revert_dashboard', false );

if ( $revert_dashboard ) {
	echo esc_html( $title ) . ' : ' . esc_html( $content ) . "\n";
} else {
	echo esc_html( $title ) . ' : ' . esc_html( $content ) . ( ! empty( $link_text ) ? ' ' . esc_html( $link_text ) : '' ) . ( ! empty( $additional_text ) ? ' - ' . esc_html( $additional_text ) : '' ) . "\n";
}
