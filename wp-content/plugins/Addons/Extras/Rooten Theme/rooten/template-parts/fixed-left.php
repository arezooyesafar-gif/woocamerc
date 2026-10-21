<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package rooten
 */

?>

<?php if (is_active_sidebar('fixed-left')) : ?>
<div id="tmFixedLeft" class="bdt-position-center-left">
	<div class="bdt-fixed-l-wrapper">
		<?php dynamic_sidebar('fixed-left'); ?>
	</div>
</div>
<?php endif; ?>