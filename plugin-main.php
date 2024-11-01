<?php
/*
Plugin Name: Wordpress Tips Ultimate
Plugin URI: http://lazypersons.com/plugins/wp-tips-ultimate
Description: This plugin will enable tips hovercard features in your wordpress site. 
Author: Lazy Persons
Author URI: http://lazypersons.com
Version: 1.0
*/

/* Adding Latest jQuery from Wordpress */
function lazy_p_wp_tipshovercard_jquery() {
	wp_enqueue_script('jquery');
}
add_action('init', 'lazy_p_wp_tipshovercard_jquery');

/*Some Set-up*/
define('LAZY_P_WP_TIPS_HOVERCARD', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );



/* Including all files */
function lazy_p_wp_tipshovercard_files() {
wp_enqueue_script('lazy-p-tipshovercard-main-js', LAZY_P_WP_TIPS_HOVERCARD.'js/jquery.tip_cards.min.js', array('jquery'), 1.0, true);
wp_enqueue_style('lazy-p-tipshovercard-main-css', LAZY_P_WP_TIPS_HOVERCARD.'css/tip_cards.css');
}
add_action( 'wp_enqueue_scripts', 'lazy_p_wp_tipshovercard_files' );



// Registering Custom post
add_action( 'init', 'wap_tips_hovercard_create_custom_post' );
function wap_tips_hovercard_create_custom_post() {
	register_post_type( 'tips-item',
		array(
			'labels' => array(
				'name' => __( 'Tips Items' ),
				'singular_name' => __( 'Tips Item' ),
				'add_new_item' => __( 'Add New Tips Item' )
			),
			'public' => true,
			'supports' => array('title', 'editor', 'custom-fields'),
			'has_archive' => true,
			'rewrite' => array('slug' => 'tips-item'),
		)
	);
	
}

// Registering Custom post's category
function wap_tips_hovercard_post_taxonomy() {
	register_taxonomy(
		'tips_cat',  
		'tips-item',
		array(
			'hierarchical'          => true,
			'label'                         => 'Tips Category',
			'query_var'             => true,
			'show_admin_column'             => true,
			'rewrite'                       => array(
				'slug'                  => 'tips-category',
				'with_front'    => true
				)
			)
	);
}
add_action( 'init', 'wap_tips_hovercard_post_taxonomy');   




// Accordion form shortcode
function tips_hovercard_items_shortcode($atts){
	extract( shortcode_atts( array(
		'id' => '01',
		'category' => '',
		'items' => '10',		
		'column' => '2',		
	), $atts, 'wcp_testimonial' ) );
	
    $q = new WP_Query(
        array('posts_per_page' => $items, 'post_type' => 'tips-item', 'tips_cat' => $category)
        );	


			
	$list = '
	
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#tips'.$id.'").tip_cards({
					column: '.$column.',
					closeButton: "&nbsp;",
					flipButton: "&nbsp;"
				});
			});
		</script>		

		<ul class="tips" id="tips'.$id.'">	
		
	';
	while($q->have_posts()) : $q->the_post();
		$idd = get_the_ID();
		$content_main = do_shortcode(get_the_content());
		$content_autop = wpautop(trim($content_main));
		$title = get_post_meta($idd, 'title', true); 
		

		
		$list .= '
		
          <li>
            <div class="tc_front">
            	<a href="#tip'.$idd.'">' .do_shortcode( get_the_title() ). '</a>
            </div>
            <div class="tc_back"></div>

            <div id="tip'.$idd.'" class="tip">
              <div class="tc_front">
                <h1>' .do_shortcode( get_the_title() ). '</h1>
                ' .do_shortcode( $content_autop ). '
              </div>
              <div class="tc_back">
                <p>'.$title.'</p>
              </div>
            </div>
          </li>		

		

		'; 		

 		
	endwhile;
	$list.= '</div>';
	
	
	wp_reset_query();
	return $list;
}
add_shortcode('tips', 'tips_hovercard_items_shortcode');	



?>