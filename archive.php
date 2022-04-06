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

global $snippets;
 
// templates variable as an array
$templates = array('archive.twig', 'base.twig');
 
// set the contexts
$context = Theme::context();
$context['posts'] = new PostQuery();

// modify the title & the templates array depending on the type of archive
if (is_day()) {
	$title = $snippets['general_archive_title'] . ': ' . get_the_date('D M Y');
}
elseif (is_month()) {
	$title = $snippets['general_archive_title'] . ': ' . get_the_date('M Y');
}
elseif (is_year()) {
	$title = $snippets['general_archive_title'] . ': ' . get_the_date('Y');
}
elseif (is_tag()) {
	$title = single_tag_title('', false);
}
elseif (is_category()) {
	$title = single_cat_title('', false);
	array_unshift($templates, 'archive-' . get_query_var('cat') . '.twig');
}

// the title, modified for paging
$context['title'] = (is_paged()) ? $title . ' - Page ' . get_query_var('paged') : $title;

// and render
Theme::render($templates, $context);