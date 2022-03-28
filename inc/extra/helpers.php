<?php

add_action('pre_get_posts', function($query) {
  
  // product archive main
  if( $query->is_main_query() && !is_admin() && !is_search() && is_post_type_archive('product') ) {
    
    $count = 0; // start count at zero
    $the_products = new WP_Query(array(
      'post_type' => 'product',
      'post_status' => 'publish',
      'posts_per_page'   => -1,
    ));
    $count = $the_products->post_count; // count according to products
    
    $the_terms = get_terms(array(
      'taxonomy' => 'product_cat',
      'hide_empty' => true,
      'parent' => 0,
    ));
    if(is_countable($the_terms) && count($the_terms) > 0) $count = count($the_terms); // new count if terms > 0
    
    // set new paging variables
    $page = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $per_page = get_option('posts_per_page');
    $offset = ($page - 1) * $per_page;
    $paged_pages = $count / $per_page;
    
    // set new posts per page & offset values
    $query->set('posts_per_page', $per_page);
    $query->set('offset', $offset);
    
    // throw 404 where requested page is greater than the new paged_pages tally
    if($page > 1 && $page > ceil($paged_pages)){
      $query->set_404();
      status_header(404);
    }
    
  }
    
  // product cat archive
  if( $query->is_main_query() && !is_admin() && !is_search() && is_tax('product_cat') ) {
    
    // $count = 0;
    $count = $query->queried_object->count; // count starts at product count (the original query count of products assigned to the given term)
    
    $the_terms = get_terms(array(
      'taxonomy' => 'product_cat',
      'hide_empty' => true,
      'parent' => get_queried_object_id(),
    ));
    if(is_countable($the_terms) && count($the_terms) > 0) $count = count($the_terms); // new count if terms > 0
    
    // set new paging variables
    $page = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $per_page = get_option('posts_per_page');
    $offset = ($page - 1) * $per_page;
    $paged_pages = $count / $per_page;
    
    // set new posts per page & offset values
    $query->set('posts_per_page', $per_page);
    $query->set('offset', $offset);
    $tax_query = $query->get('tax_query');
    if(!empty($tax_query)) $query->set('tax_query', $tax_query);
    
    // throw 404 where requested page is greater than the new paged_pages tally
    if($page > 1 && $page > ceil($paged_pages)){
      $query->set_404();
      status_header(404);
    }
    
  }
  
  return $query;
  
}, 10, 1);

add_filter('found_posts', function($found_posts, $query) {
  
  // product archive main
  if( $query->is_main_query() && !is_admin() && !is_search() && is_post_type_archive( 'product' ) ) {
  
    $the_terms = count(get_terms(array(
      'taxonomy' => 'product_cat',
      'hide_empty' => true,
      'parent' => 0,
    )));
    
    if(!empty($the_terms)) $found_posts = $the_terms;
  
  }
  
  // product cat archive
  if( $query->is_main_query() && !is_admin() && !is_search() && is_tax('product_cat') ) {
  
    $the_terms = count(get_terms(array(
      'taxonomy' => 'product_cat',
      'hide_empty' => true,
      'parent' => get_queried_object_id(),
    )));
  
    if(!empty($the_terms)) $found_posts = $the_terms;
  
  }
  
  return $found_posts;

}, 10, 2);

add_action('init', function() {
    add_rewrite_rule(
      'loading-dock-products/products/(.*)/page/([0-9]+)/?',
      'index.php?product_cat=$matches[1]&paged=$matches[2]',
      'top'
    );
  }
);

/**
 * Post Type Link
 *
 */
add_filter( 'post_type_link', 'post_type_link', 10, 2 );
function post_type_link( $link, $post ) {
  if( 'product' != $post->post_type ) return $link;
  $term = ea_first_term( 'product_cat', false, $post->ID );
  $slug = !empty( $term ) && ! is_wp_error( $term ) ? $term->slug : 'uncategorized';
  $link = str_replace( '%product_cat%', $slug, $link );
  return $link;
}

/**
 * Post Type Archive Link
 *
 */
add_filter( 'post_type_archive_link', 'post_type_archive_link', 10, 2 );
function post_type_archive_link( $link, $post_type ) {
  if( 'product' == $post_type ) $link = str_replace( '/%product_cat%', '', $link );
  return $link;
}

/**
 * Get the first term attached to post
 *
 * @param string $taxonomy
 * @param string/int $field, pass false to return object
 * @param int $post_id
 * @return string/object
 */
function ea_first_term( $taxonomy = 'category', $field = false, $post_id = false ) {

	$post_id = $post_id ? $post_id : get_the_ID();
	$term = false;

	// Use WP SEO Primary Term
	// from https://github.com/Yoast/wordpress-seo/issues/4038
	if( class_exists( 'WPSEO_Primary_Term' ) ) {
		$term = get_term( ( new WPSEO_Primary_Term( $taxonomy,  $post_id ) )->get_primary_term(), $taxonomy );
	}

	// Fallback on term with highest post count
	if( ! $term || is_wp_error( $term ) ) {

		$terms = get_the_terms( $post_id, $taxonomy );

		if( empty( $terms ) || is_wp_error( $terms ) )
			return false;

		// If there's only one term, use that
		if( 1 == count( $terms ) ) {
			$term = array_shift( $terms );

		// If there's more than one...
		} else {

			// Sort by term order if available
			// @uses WP Term Order plugin
			if( isset( $terms[0]->order ) ) {
				$list = array();
				foreach( $terms as $term )
					$list[$term->order] = $term;
				ksort( $list, SORT_NUMERIC );

			// Or sort by post count
			} else {
				$list = array();
				foreach( $terms as $term )
					$list[$term->count] = $term;
				ksort( $list, SORT_NUMERIC );
				$list = array_reverse( $list );
			}

			$term = array_shift( $list );
		}
	}

	// Output
	if( $field && isset( $term->$field ) )
		return $term->$field;

	else
		return $term;
}

/**
 * Limit max menu depth in admin panel to 1 on certain menus on edit menu screen using js
 */
function main_menu_limit_depth( $hook ) {

  if ( $hook != 'nav-menus.php' ) return;

  wp_add_inline_script( 
    'nav-menu', 
    'jQuery(document).ready(function(){ var selected_menu_id = jQuery("#select-menu-to-edit option:selected").prop("value"); if ("3" === selected_menu_id || "5" === selected_menu_id) { wpNavMenu.options.globalMaxDepth = 0; } });', 
    'after' 
  );

}
// add_action( 'admin_enqueue_scripts', 'main_menu_limit_depth' );

// add uk-active to active menu items
add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);
function special_nav_class ($classes, $item) {
  if (in_array('current-menu-item', $classes) ){
    $classes[] = 'uk-active ';
  }
  return $classes;
}