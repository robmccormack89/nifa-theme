<?php
/**
 * The 404 template
 *
 * @package Rmcc_Theme
 */
 
 // namespace stuff
 namespace Rmcc;
 use Timber\PostQuery;

// set the contexts
$context = Theme::context();

// and render
Theme::render('404.twig', $context);