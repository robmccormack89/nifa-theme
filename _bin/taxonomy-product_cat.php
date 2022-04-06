<?php

/**
*
* The template for displaying all general archive pages (apart from the main blog posts page)
*
* @package NIFA-Theme
*
*/

// namespace stuff
namespace Rmcc;
use Timber\PostQuery;
use Timber\Term;
use Timber\Pagination;

global $snippets;
 
// templates variable as an array
$templates = array('archive-product-cat.twig', 'archive.twig', 'base.twig');
 
// set the contexts
$context = Theme::context();
$context['posts'] = new PostQuery();

$page = ( get_query_var('paged') ) ? get_query_var( 'paged' ) : 1;
$per_page = get_option( 'posts_per_page' );
$offset = ( $page-1 ) * $per_page;
$terms = get_terms(array(
  'taxonomy' => 'product_cat',
  'hide_empty' => true,
  'parent' => get_queried_object_id(),
  'number' => $per_page,
  'offset' => $offset
));
$number_of_cats = count( get_terms(array(
  'taxonomy' => 'product_cat',
  'hide_empty' => true,
  'parent' => get_queried_object_id(),
)) );
$data = null;
foreach($terms as $term){
  $the_term = new Term($term->term_id);
  $data[] = $the_term;
}
$context['terms'] = $data;

if(!empty($context['terms'])){
  $big = 999999999;
  $terms_pag_args = array(
    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
    'format' => '?paged=%#%',
    'current' => $page,
    'total' => ceil($number_of_cats / $per_page)
  );
  $context['posts']['pagination'] = new Pagination($terms_pag_args);
}

$title = single_term_title('', false);

// the title, modified for paging
$context['title'] = (is_paged()) ? $title . ' - Page ' . get_query_var('paged') : $title;

// and render
Theme::render($templates, $context);