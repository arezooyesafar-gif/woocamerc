<?php

defined( 'ABSPATH' ) || exit;

function pooshe_jsonld_breadcrumb() {
	if ( ! function_exists( 'WC' ) ) {
		return;
	}

	$items = array();

	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		$breadcrumb = new WC_Breadcrumb();
		$items      = $breadcrumb->generate();
	} elseif ( is_page() || is_singular( 'post' ) ) {
		$post = get_queried_object();
		if ( $post && ! is_front_page() ) {
			$items[]   = array( 'خانه', home_url( '/' ), 'home' );
			$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
			foreach ( $ancestors as $ancestor_id ) {
				$items[] = array( get_the_title( $ancestor_id ), get_permalink( $ancestor_id ), 'ancestor' );
			}
			$items[] = array( get_the_title( $post->ID ), get_permalink( $post->ID ), 'singular' );
		}
	} elseif ( is_home() && ! is_front_page() ) {
		$items[] = array( 'خانه', home_url( '/' ), 'home' );
		$items[] = array( get_the_title( get_option( 'page_for_posts' ) ), get_permalink( get_option( 'page_for_posts' ) ), 'archive' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$items[] = array( 'خانه', home_url( '/' ), 'home' );
		$items[] = array( single_term_title( '', false ), '', 'archive' );
	} elseif ( is_search() ) {
		$items[] = array( 'خانه', home_url( '/' ), 'home' );
		$items[] = array( 'نتایج جستجو: ' . get_search_query(), '', 'search' );
	}

	if ( count( $items ) < 2 ) {
		return;
	}

	$structured_data = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(),
	);

	$position = 1;
	foreach ( $items as $item ) {
		$name = ! empty( $item[0] ) ? wp_strip_all_tags( $item[0] ) : '';
		$url  = ! empty( $item[1] ) ? esc_url( $item[1] ) : '';

		if ( empty( $name ) ) {
			continue;
		}

		$node = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => $name,
		);

		if ( ! empty( $url ) ) {
			$node['item'] = $url;
		}

		$structured_data['itemListElement'][] = $node;
		$position++;
	}

	if ( $position > 2 ) {
		echo '<script type="application/ld+json">' .
			wp_json_encode( $structured_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) .
			'</script>' . "\n";
	}
}
add_action( 'wp_head', 'pooshe_jsonld_breadcrumb', 5 );
