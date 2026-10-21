<?php

defined( 'ABSPATH' ) || exit;

function pooshe_lazy_loading_attributes( $attr, $attachment ) {
	if ( is_admin() ) {
		return $attr;
	}

	if ( empty( $attr['loading'] ) ) {
		$attr['loading'] = 'lazy';
	}

	if ( empty( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'pooshe_lazy_loading_attributes', 10, 2 );

function pooshe_lazy_loading_post_thumbnail( $html ) {
	if ( is_admin() || false !== strpos( $html, 'loading=' ) ) {
		return $html;
	}
	return str_replace( '<img ', '<img loading="lazy" decoding="async" ', $html );
}
add_filter( 'the_post_thumbnail', 'pooshe_lazy_loading_post_thumbnail' );

function pooshe_lazy_loading_content_images( $content ) {
	if ( is_admin() ) {
		return $content;
	}
	return preg_replace_callback(
		'/<img\s([^>]*?)>/i',
		function ( $matches ) {
			if ( false !== strpos( $matches[1], 'loading=' ) ) {
				return $matches[0];
			}
			return '<img loading="lazy" ' . $matches[1] . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'pooshe_lazy_loading_content_images', 100 );

function pooshe_lazy_loading_css() {
	echo '<style>img[loading="lazy"]{background:#f5f5f5}img.loaded,img[loading="lazy"]:-webkit-load-complete{opacity:1}</style>' . "\n";
}
add_action( 'wp_head', 'pooshe_lazy_loading_css', 1 );
