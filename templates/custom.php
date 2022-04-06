<?php
  
/**
*
* Template Name: Custom Template
* Template Post Type: post, page, product
*
* @package NIFA-Theme
*
*/

// namespace stuff
namespace Rmcc;
use Timber\Post;

global $theme_config;

 // set the contexts
$context = Theme::context();
$context['post'] = new Post();

// templates variable as an array (using the $post stuff)
$templates = array(
  'single-' . $context['post']->post_type . '.twig', 
  'single.twig',
  'base.twig'
);

// add the custom template/s to the start of the templates array
array_unshift($templates, 'custom-' . $context['post']->post_type . '.twig', 'custom.twig',);

// and render
Theme::render($templates, $context);