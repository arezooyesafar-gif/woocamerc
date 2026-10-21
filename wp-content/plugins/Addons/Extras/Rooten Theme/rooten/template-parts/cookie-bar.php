<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package rooten
 */

$rooten_message        = get_theme_mod( 'rooten_cookie_message' );
$rooten_accept_button  = get_theme_mod( 'rooten_cookie_accept_button', true );
$rooten_decline_button = get_theme_mod( 'rooten_cookie_decline_button', false );
$rooten_policy_button  = get_theme_mod( 'rooten_cookie_policy_button', false );
$rooten_policy_url     = get_theme_mod( 'rooten_cookie_policy_url', '/privacy-policy/' );
$rooten_expire_days    = get_theme_mod( 'rooten_cookie_expire_days', 365 );
$rooten_position       = get_theme_mod( 'rooten_cookie_position' );
$rooten_dev_mode       = get_theme_mod( 'rooten_cookie_debug' );


$rooten_cookie_settings = [
	'message'       => ($rooten_message) ? esc_html( $rooten_message ) : esc_html__( 'We use cookies to ensure that we give you the best experience on our website.', 'rooten' ),
	'acceptButton'  => $rooten_accept_button,
	'acceptText'    => esc_html__( 'I Understand', 'rooten' ),
	'declineButton' => $rooten_decline_button,
	'declineText'   => esc_html__( 'Disable Cookies', 'rooten' ),
	'policyButton'  => $rooten_policy_button,
	'policyText'    => esc_html__( 'Privacy Policy', 'rooten' ),
	'policyURL'     => esc_url( $rooten_policy_url),
	'expireDays'    => $rooten_expire_days,
	'bottom'        => ($rooten_position) ? true : false,
	'forceShow'     => ($rooten_dev_mode) ? true : false,
]


?>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery.cookieBar(<?php echo json_encode($rooten_cookie_settings); ?>);
	});
</script>