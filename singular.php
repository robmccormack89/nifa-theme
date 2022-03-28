<?php

/**
*
* The default template for displaying all singlular items (posts, pages & cpt singles)
*
* @package Rmcc_Theme
*
*/

// namespace stuff
namespace Rmcc;
use Timber\Post;
 
 // set the contexts
$context = Theme::context();
$context['post'] = new Post();

// templates variable as an array (using the $post stuff)
$templates  = array(
	'single-' . $context['post']->ID . '.twig', 
  'single-' . $context['post']->slug . '.twig', 
	'single-' . $context['post']->post_type . '.twig', 
	'single.twig',
  'base.twig'
);

// and render
Theme::render($templates, $context);