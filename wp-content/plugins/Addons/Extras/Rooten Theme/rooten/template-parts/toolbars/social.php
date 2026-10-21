<?php 

$rooten_attrs['class']        = get_theme_mod( 'rooten_toolbar_social_style' ) ? 'bdt-icon-button' : 'bdt-icon-link';
$rooten_attrs['target']       = get_theme_mod( 'rooten_toolbar_social_target' ) ? '_blank' : '';

// Grid
$rooten_attrs_grid            = [];
$rooten_attrs_grid['class'][] = 'bdt-grid-small bdt-flex-middle';
$rooten_attrs_grid['bdt-grid'] = true;

$rooten_links = (get_theme_mod( 'rooten_toolbar_social' )) ? explode(',', get_theme_mod( 'rooten_toolbar_social' )) : null;
if (count($rooten_links)) : ?>
	<div class="social-link">
		<ul<?php rooten_helper::attrs($rooten_attrs_grid) ?>>
		    <?php foreach ($rooten_links as $rooten_link) : ?>
		    <li>
		        <a<?php rooten_helper::attrs(['href' => $rooten_link], $rooten_attrs); ?> bdt-icon="icon: <?php echo rooten_helper::icon($rooten_link); ?>; ratio: 0.8" title="<?php echo ucfirst(rooten_helper::icon($rooten_link)); ?>" bdt-tooltip="pos: bottom"></a>
		    </li>
		    <?php endforeach ?>
		</ul>
	</div>
<?php endif; ?>
