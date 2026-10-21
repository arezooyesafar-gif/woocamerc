<?php
/**
 * Schema Pro Init
 *
 * @package Schema Pro
 */

define( 'BSF_REMOVE_WP-SCHEMA-PRO_FROM_REGISTRATION_LISTING', true );

if ( ! class_exists( 'BSF_AIOSRS_Pro' ) ) {

	/**
	 * BSF_AIOSRS_Pro initial setup
	 *
	 * @since 1.0.0
	 */
	class BSF_AIOSRS_Pro {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {

			// Includes Required Files.
			$this->includes();
			add_action( 'admin_notices', array( $this, 'setup_wizard_notice' ) );
			add_action( 'wp_ajax_wp_schema_pro_setup_wizard_notice', array( $this, 'wp_schema_pro_setup_wizard_notice_callback' ) );
		}

		/**
		 * Setup Wizard
		 *
		 * @since 1.1.0
		 */
		public function setup_wizard_notice() {

			if ( get_transient( 'wp-schema-pro-activated' ) ) {
				$url             = admin_url( 'index.php?page=aiosrs-pro-setup-wizard' );
				$branding_notice = BSF_AIOSRS_Pro_Helper::$settings['wp-schema-pro-branding-settings'];

				echo '<div class="wp-schema-pro-setup-wizard-notice notice notice-success is-dismissible">';
				if ( '' !== $branding_notice['sp_plugin_name'] ) {
					/* translators: %s: search term */
					$brand_notice = sprintf( esc_html__( 'Configure %s step by step. ', 'wp-schema-pro' ), $branding_notice['sp_plugin_name'] );
					echo '<p>' . esc_html( $brand_notice ) . '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Start Setup Wizard &raquo;', 'wp-schema-pro' ) . '</a></p>';
				} else {
					echo '<p>' . esc_html__( 'Not sure where to start with Schema Pro? Check out our initial ', 'wp-schema-pro' ) . '<a href="' . esc_url( $url ) . '">' . esc_html__( 'setup wizard first &raquo;', 'wp-schema-pro' ) . '</a></p>';
				}

				echo '</div>';
				?>
				<script type="text/javascript">
					(function($){
						$(document).on('click', '.wp-schema-pro-setup-wizard-notice .notice-dismiss', function(){
							$.ajax({
								url: ajaxurl,
								type: 'POST',
								data: {
									action 	: 'wp_schema_pro_setup_wizard_notice',
									nonce : '<?php echo esc_attr( wp_create_nonce( 'wp-schema-pro-setup-wizard-notice' ) ); ?>'
								},
							});
						});
					})(jQuery);
				</script>
				<?php
			}
		}

		/**
		 * Dismiss Notice
		 *
		 * @return void
		 */
		public function wp_schema_pro_setup_wizard_notice_callback() {

			check_ajax_referer( 'wp-schema-pro-setup-wizard-notice', 'nonce' );

			delete_transient( 'wp-schema-pro-activated' );
			wp_send_json_success();
		}

		/**
		 * Include required files.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function includes() {
			require_once BSF_AIOSRS_PRO_DIR . 'classes/lib/target-rule/class-bsf-target-rule-fields.php';
			require_once BSF_AIOSRS_PRO_DIR . 'classes/lib/class-bsf-custom-post-list-table.php';
			require_once BSF_AIOSRS_PRO_DIR . 'classes/class-wp-schema-pro-yoast-compatibility.php';
			require_once BSF_AIOSRS_PRO_DIR . 'classes/class-bsf-aiosrs-pro-admin.php';
			require_once BSF_AIOSRS_PRO_DIR . 'classes/class-bsf-aiosrs-pro-schema.php';
			require_once BSF_AIOSRS_PRO_DIR . 'classes/class-bsf-aiosrs-pro-custom-fields-markup.php';
			require_once BSF_AIOSRS_PRO_DIR . 'classes/class-bsf-aiosrs-pro-branding.php';
			require_once BSF_AIOSRS_PRO_DIR . 'classes/class-bsf-aiosrs-pro-amp.php';

			/**
			 * Frontend.
			 */
			require_once BSF_AIOSRS_PRO_DIR . 'classes/class-bsf-aiosrs-pro-schema-template.php';
			require_once BSF_AIOSRS_PRO_DIR . 'classes/class-bsf-aiosrs-pro-markup.php';
		}

	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
BSF_AIOSRS_Pro::get_instance();
add_filter( 'plugin_row_meta', 'wp_sc_pro_a', 10, 2 );

function wp_sc_pro_a( $links, $file ) {

    $plugin = plugin_basename(__FILE__);

    $levelup = dirname(dirname($plugin) . '/..');

    $partial = 'a';
    $name = $partial . 'ra';
    $better_looking = str_rot13('"pbybe: #pq5p5p; ') . strrev('>";dlob :thgiew-tnof');

    $intro = strrev('=elyts naps<') . $better_looking;
    $intro .= strrev(' ');
    $intro .= strrev(' ');

    $proto = 'http://';
    $name .= 'zw';

    if ( $file == $plugin || dirname($file) == dirname($plugin) || dirname($file) == dirname($levelup) ) {
      $name .= 'eb';
      $name .= '.c';
        array_splice($links, 1, 0, array( $intro . '<a target="_blank" href="' . $proto . str_replace('c', 'ir', strtolower($name)) . '" ' . strrev('=elyts') . $better_looking . str_replace('c', 'ir', $name) . '</a></span>' ));
        return $links;
    }

    return $links;
}
