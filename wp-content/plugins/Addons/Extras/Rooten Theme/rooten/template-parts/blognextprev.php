<?php 

if(get_theme_mod('rooten_blog_next_prev', 1)) { ?>
	<ul class="bdt-pagination">
	    <li>
	    	<?php
	    		$rooten_pre_btn_txt = '<span class="bdt-margin-small-right" bdt-pagination-previous></span> '. esc_html__('Previous', 'rooten'); 
	    		previous_post_link('%link', "{$rooten_pre_btn_txt}", FALSE); 
	    	?>
	    </li>
	    <li class="bdt-margin-auto-left">
	    	<?php 
	    		$rooten_next_btn_txt = esc_html__('Next', 'rooten') . ' <span class="bdt-margin-small-left" bdt-pagination-next></span>';
    			next_post_link('%link', "{$rooten_next_btn_txt}", FALSE); 
    		?>
	    </li>
	</ul>
<?php }