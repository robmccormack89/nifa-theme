<?php
/**
 * The search
 *
 * @package NIFA-Theme
 */
 
// namespace stuff
// namespace stuff
namespace Rmcc;
use Timber\PostQuery;
use Timber\Term;
use Timber\Pagination;

global $snippets;

// templates variable as an array
$templates = array('search.twig', 'archive.twig', 'base.twig');

$context = Theme::context();
$context['posts'] = new PostQuery();

$context['title'] = $snippets['search_results_title'];
$context['description'] = $snippets['search_results_description'] . ' "' . get_search_query() . '"';

Theme::render($templates, $context);