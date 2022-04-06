<?php
/**
 * The 404 template
 *
 * @package NIFA-Theme
 */
 
 // namespace stuff
 namespace Rmcc;
 use Timber\PostQuery;

// set the contexts
$context = Theme::context();

$context['post']['title'] = 'Error, page not found';
$context['post']['description'] = 'Sorry, we couldnt find what you were looking for...';

// and render
Theme::render('404.twig', $context);