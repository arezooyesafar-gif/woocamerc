<?php
/**
 * Plugin Name: Whitespace Fixer for XML Sitemap
 * Plugin URI: https://wp-turbo.com/whitespace-fixer-for-xml-sitemap/
 * Description: Automatically removes whitespace before XML declarations in WordPress sitemaps, RSS, and feed outputs to prevent 'XML declaration not at start' errors.
 * Version: 1.1.1
 * Author: Turbo Addons
 * Author URI: https://turbo-addons.com
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: xml-auto-fixer-for-wordpress-sitemap
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// wp-pulse integration — must run at top level (not inside plugins_loaded) so that
// register_activation_hook() inside the SDK is registered in time. During plugin
// activation, plugins_loaded has already fired before the plugin file is included,
// so initializing the SDK on plugins_loaded would skip the activation hook entirely
// and the "activated" status would never be sent.
if ( ! class_exists( 'WPPulse_SDK' ) ) {
    $sdk_file = __DIR__ . '/wppulse/wppulse-plugin-analytics-engine-sdk.php';
    if ( file_exists( $sdk_file ) ) {
        require_once $sdk_file;
    }
}

if ( class_exists( 'WPPulse_SDK' ) ) {
    $plugin_data = get_file_data( __FILE__, [
        'Name'    => 'Plugin Name',
        'Version' => 'Version',
    ] );
    WPPulse_SDK::init( __FILE__, [
        'name'     => $plugin_data['Name'],
        'slug'     => dirname( plugin_basename( __FILE__ ) ),
        'version'  => $plugin_data['Version'],
        'endpoint' => 'https://wp-turbo.com/wp-json/wppulse/v1/collect',
    ] );
}

if ( ! class_exists( 'WFXS_XML_Whitespace_Fix' ) ) :

$wfxs_plugin_version = '1.1.1';

final class WFXS_XML_Whitespace_Fix {

    /**
     * Register admin page.
     */
    public function register_admin_page() {

        add_management_page(
            __( 'XML Whitespace Fixer', 'xml-auto-fixer-for-wordpress-sitemap' ),
            __( 'XML Whitespace Fixer', 'xml-auto-fixer-for-wordpress-sitemap' ),
            'manage_options',
            'wfxs-status',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Holds the singleton instance.
     *
     * @var WFXS_XML_Whitespace_Fix|null
     */
    private static $instance = null;

    /**
     * Get the singleton instance.
     *
     * @return WFXS_XML_Whitespace_Fix
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor — hooks early into plugins_loaded.
     */
    private function __construct() {

        add_action( 'plugins_loaded', array( $this, 'start_output_buffering' ), 0 );

        add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
    }

    /**
     * Start output buffering only for XML/feed requests.
     */
    public function start_output_buffering() {
        if ( ! $this->should_buffer() ) {
            return;
        }

        if ( ob_get_length() ) {
            @ob_clean(); // Optional: clean previous output if started too early.
        }

        ob_start( array( $this, 'clean_whitespace_output' ) );
    }

    /**
     * Whether the current request is likely to output XML (sitemap or feed).
     *
     * @return bool
     */
    private function should_buffer() {
        if ( is_admin() ) {
            return false;
        }

        if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return false;
        }

        if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
            return false;
        }

        $request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );

        // Sitemaps (core + SEO plugins) and other .xml endpoints.
        if ( preg_match( '/sitemap|\.xml($|[?#])/i', $request_uri ) ) {
            return true;
        }

        // RSS / Atom / RDF feeds.
        if ( preg_match( '/(^|\/)(feed|comments\/feed)(\/|$)|[?&]feed=/i', $request_uri ) ) {
            return true;
        }

        return false;
    }

    /**
     * Callback to clean output buffer from leading whitespace.
     *
     * @param string $output The buffered output.
     * @return string
     */

    /**
     * Render admin page.
     */
    public function render_admin_page() {

        global $wfxs_plugin_version;

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $sitemap_url = home_url( '/wp-sitemap.xml' );

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'XML Whitespace Fixer Status', 'xml-auto-fixer-for-wordpress-sitemap' ); ?></h1>

            <div class="card" style="max-width:800px;padding:20px;">
                <h2><?php esc_html_e( 'Plugin Status', 'xml-auto-fixer-for-wordpress-sitemap' ); ?></h2>

                <table class="widefat striped">
                    <tbody>

                        <tr>
                            <td><strong><?php esc_html_e( 'Plugin Version', 'xml-auto-fixer-for-wordpress-sitemap' ); ?></strong></td>
                            <td><?php echo esc_html( $wfxs_plugin_version ); ?></td>
                        </tr>

                        <tr>
                            <td><strong><?php esc_html_e( 'WordPress Version', 'xml-auto-fixer-for-wordpress-sitemap' ); ?></strong></td>
                            <td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
                        </tr>

                        <tr>
                            <td><strong><?php esc_html_e( 'PHP Version', 'xml-auto-fixer-for-wordpress-sitemap' ); ?></strong></td>
                            <td><?php echo esc_html( PHP_VERSION ); ?></td>
                        </tr>

                        <tr>
                            <td><strong><?php esc_html_e( 'Output Buffering', 'xml-auto-fixer-for-wordpress-sitemap' ); ?></strong></td>
                            <td><?php esc_html_e( 'Enabled (frontend XML & feed requests only)', 'xml-auto-fixer-for-wordpress-sitemap' ); ?></td>
                        </tr>

                        <tr>
                            <td><strong><?php esc_html_e( 'XML Sitemap URL', 'xml-auto-fixer-for-wordpress-sitemap' ); ?></strong></td>
                            <td>
                                <a href="<?php echo esc_url( $sitemap_url ); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html( $sitemap_url ); ?>
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>

                <p style="margin-top:20px;">
                    <?php esc_html_e(
                        'This plugin automatically removes unwanted whitespace before XML declarations in sitemaps and feed outputs.',
                        'xml-auto-fixer-for-wordpress-sitemap'
                    ); ?>
                </p>
            </div>
        </div>
        <?php
    }

    public function clean_whitespace_output( $output ) {
        $allowed = false;

        foreach ( headers_list() as $header ) {
            if ( preg_match(
                '/^content-type:\s*(?:text\/(?:html|xml)|application\/(?:xml|xhtml\+xml|atom\+xml|rss\+xml|rdf\+xml))/i',
                trim( $header )
            ) ) {
                $allowed = true;
                break;
            }
        }

        if ( $allowed ) {
            // Remove leading whitespace only (before the XML declaration).
            return ltrim( $output );
        }

        return $output;
    }

    /**
     * Prevent cloning.
     */
    private function __clone() {}

    /**
     * Prevent unserializing.
     *
     * PHP 8+ requires magic methods to be public.
     */
    public function __wakeup() {}
}

WFXS_XML_Whitespace_Fix::get_instance();

endif;
