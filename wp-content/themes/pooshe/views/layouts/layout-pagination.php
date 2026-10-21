<?php
$posts_pagination = get_the_posts_pagination(
	array(
		'mid_size'  => 1,
	)
);
if ( $posts_pagination ) { ?>

	<div class="pagination-wrapper section-inner">

		<?php echo $posts_pagination; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped during generation. ?>

	</div><!-- .pagination-wrapper -->

	<?php
}
