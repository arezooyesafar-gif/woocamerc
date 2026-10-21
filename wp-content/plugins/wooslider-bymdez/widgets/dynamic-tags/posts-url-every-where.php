<?php
//namespace ElementorPro\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use ElementorPro\Modules\DynamicTags\Tags\Base\Tag;
use ElementorPro\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Get Deps

/***/

class Woosbymdz_PostsUrl_EveryWhere extends Tag {

	public function get_name() {
		return 'wooslider-post-url';
	}

	public function get_title() {
		return esc_html__( 'Post Url (WooSlider)', 'wooslider-bymdez' );
	}

	public function get_group() {
		return Module::POST_GROUP;
	}

	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
		];
	}

	protected function register_controls() {

	}

	public function render() {

		$post_id = get_the_ID();
        $post_url = get_permalink($post_id);

        echo $post_url;
	}
}
