<footer class="pooshe-footer" <?php if (is_account_page() && !is_user_logged_in()){ echo 'style="display: none;"'; }?>>
<?php
    if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
        get_template_part( 'views/layouts/layout', 'footer');
    }
?>
</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>