<?php 
/**
 * Template Name: Shift
 */

get_header('shift'); ?>
<div class="content-outer-wrapper">
	<?php 
		// Check and get Sidebar Class
		global $sidebar;
		$sidebar = get_post_meta($post->ID,'page-option-sidebar-template',true);
		$sidebar_array = gdl_get_sidebar_size( $sidebar );
		if( $sidebar == 'no-sidebar' ){
			get_template_part('page', 'fullshift');
		}else{
			get_template_part('page', 'normalshift');
		}
	?>

				
			
</div> <!-- content outer wrapper -->
<?php get_footer('shift'); ?>