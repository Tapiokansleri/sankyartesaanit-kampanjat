<?php
/**
 * Single template for Kampanja landing pages.
 *
 * @package Kansleri_Sankyartesaanit
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="site-main kampanja-landing__main">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();
