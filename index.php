<?php

/**
*
* The main blog posts archive template. This template will display the main blog posts archive, whether set to the homepage or any other page
* This template will also get used when all other template are missing. which shouldnt be the case anyway
*
* @package Rmcc_Theme
*
*/

// namespace stuff
namespace Rmcc;
use Timber\PostQuery;
use Timber\Post;
 
// templates variable as an array
$templates = array('blog.twig', 'archive.twig', 'base.twig');

// set the contexts
$context = Theme::context();
$context['posts'] = new PostQuery();

// set the title for the blog page
$post = new Post();
$title =  get_the_title($post->id);
if (is_home() && is_front_page()) $title = get_bloginfo('name');

// the title, modified for paging
$context['title'] = (is_paged()) ? $title . ' - Page ' . get_query_var('paged') : $title;

// and render
Theme::render($templates, $context);