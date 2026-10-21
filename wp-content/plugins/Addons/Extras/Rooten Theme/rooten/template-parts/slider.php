<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package rooten
 */


$rooten_show_rev_slider = get_post_meta( get_the_ID(), 'rooten_show_rev_slider', true );
$rooten_rev_slider = get_post_meta( get_the_ID(), 'rooten_rev_slider', true );

if(shortcode_exists("rev_slider") && ($rooten_show_rev_slider == 'yes') && !is_search()) : ?>

<div class="slider-wrapper" id="tmSlider">
	<div>
		<section class="tm-slider bdt-child-width-expand@s" bdt-grid>
			<div>
				<?php echo(do_shortcode('[rev_slider '.$rooten_rev_slider.']')); ?>
			</div>
		</section>
	</div>
</div>

<?php endif; ?>
