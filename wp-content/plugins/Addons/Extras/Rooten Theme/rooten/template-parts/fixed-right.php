<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package rooten
 */

?>

<?php if (is_active_sidebar('fixed-right')) : ?>
<div id="tmFixedRight" class="bdt-position-center-right">
	<div class="bdt-fixed-r-wrapper">
		<?php dynamic_sidebar('fixed-right'); ?>
	</div>
</div>
<?php endif; ?>