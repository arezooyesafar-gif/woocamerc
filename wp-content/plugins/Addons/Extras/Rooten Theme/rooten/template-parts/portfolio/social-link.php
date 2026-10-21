<?php
$rooten_social_link = get_post_meta( get_the_ID(), 'bdthemes_portfolio_social_link', true );
if($rooten_social_link != null and is_array($rooten_social_link)) : ?>
	<div class="bdt-portfolio-social bdt-position-absolute bdt-position-bottom-center">
		<ul class="bdt-list bdt-margin-remove-bottom">
	    <?php foreach ($rooten_social_link as $rooten_link) : ?>
	        <?php $rooten_tooltip = ucfirst(rooten_helper::icon($rooten_link)); ?>
	        <li class="bdt-display-inline-block">
	            <a<?php rooten_helper::attrs(['href' => $rooten_link, 'class' => 'bdt-margin-small-right']); ?> bdt-icon="icon: <?php echo rooten_helper::icon($rooten_link); ?>" title="<?php echo esc_html($rooten_tooltip); ?>" bdt-tooltip></a>
	        </li>
	    <?php endforeach ?>
	    </ul>
	</div>
<?php endif; ?>