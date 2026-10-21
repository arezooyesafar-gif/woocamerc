<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package rooten
 */

get_header(); ?>



<div<?php rooten_helper::section(); ?>>
	<div<?php rooten_helper::container(); ?>>
		<div<?php rooten_helper::grid('bdt-flex bdt-flex-middle'); ?>>
			
			<div class="bdt-width-expand">
				<main class="tm-content">

					<section class="error-404-section not-found">


						<div class="bdt-vertical-align-middle bdt-margin-large-bottom bdt-margin-large-top bdt-background-default bdt-padding-large bdt-margin-auto">

							<h1><?php esc_html_e("404", "rooten") ?></h1>
							<h3><?php esc_html_e("Page Doesn't Exists", "rooten") ?></h3>

							<p class="bdt-margin-medium-top"><?php 
								$rooten_err_history_link = '<a href="javascript:history.go(-1)">'.esc_html__("Go back", "rooten").'</a>';
								$rooten_err_home_link = '<a href="'.home_url('/').'">'.get_bloginfo('name').'</a>';

								printf(esc_html__("The Page you are looking for doesn't exist or an other error occurred. %1\$s or head over to %2\$s %3\$s homepage to choose a new direction.", "rooten"), wp_kses_post($rooten_err_history_link) , '<br class="bdt-visible@l">', wp_kses_post($rooten_err_home_link)); ?></p>

						</div>

					</section><!-- .error-404 -->

				</main><!-- #main -->
			</div><!-- #primary -->
		</div>
	</div>
</div>

<?php
get_footer();
