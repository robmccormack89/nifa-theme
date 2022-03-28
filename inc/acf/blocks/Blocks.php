<?php
namespace Rmcc;
use Timber\Timber;
use Timber\Post;
use Timber\PostQuery;

/*

  Blocks: Adds some ACF Blocks to the gutenberg editor & renders them with Twig/Timber...//
  
  How to use Blocks: 

    1. Add directory to main project's psr-4 autoload in composer.json

    2. Copy json data files to theme's local json data directory (replace when updated via the backend prior to git push)

    3. Init Blocks class (when ACF & Timber are available)
    
  Blocks assumes the use of:
  
    1. Timber library (loaded via theme's composer.json)
  
    2. ACF plugin (PRO version installed as a plugin)
    
    3. UiKit frontend framework (scripts & styles loaded via theme)
    
    4. Fontawesome icons (scripts & styles loaded via theme)

*/

array_unshift(
  Timber::$dirname, 
  'inc/acf/blocks/macros',
  
  // views
  'inc/acf/blocks/views',
    
  // blocks not using any ACF fields*****
  // 'inc/acf/blocks/views/no-acf',
  // 'inc/acf/blocks/views/no-acf/blog-posts-section',
    
  // blocks using repeater fields*****
  'inc/acf/blocks/views/featured-items-row-section',
  'inc/acf/blocks/views/slider-section',
  'inc/acf/blocks/views/testimonials-section',
  'inc/acf/blocks/views/buttons',
);

class Blocks {
  
  public function __construct() {

    add_action('acf/init', array($this, 'register_blocks'));
    add_action('enqueue_block_assets', array($this, 'acf_blocks_editor_scripts')); // use 'enqueue_block_editor_assets' for backend-only
    
    add_filter('style_loader_tag', array($this, 'preconnects_filter'), 10, 2);
    
  }
  
  public function register_blocks() {
    
    if(!function_exists('acf_register_block')) return;
    
    $example_block_data = array(
      
      // *required
      'name' => 'cover_section',
      'title' => 'Cover section',
      
      // the callback function
      'render_callback' => array($this, 'cover_section_render_callback'), // the callback function
      
      // what block settings does this block allow
      'supports' => array(
        'align' => true,
        'align_text' => true, // allow text align: left, center & right
        'align_content' => true, // allow content align: top, center(middle) & bottom
        'full_height' => true, // allow full height toggle
        'mode' => false, // allow toggle for edit/preview mode
        'jsx' => false // enable for when using <InnerBlocks /> component
      ),
      
      // the defaults for various block settings
      'align' => 'full',
      'align_text' => 'center',
      'align_content' => 'center',
      'full_height' => true,
      'mode' => 'preview',
      
      // *optional
      '__description' => 'All fields are optional.', // *optional
      
      // category & icon
      'category' => 'design',
      'icon' => 'cover-image',
      
      // keywords by which to search for the block
      'keywords' => array('cover', 'section'),
      
      // example data for block selection previews 
      // 'example' => array(
      //   'attributes' => array(
      //     'mode' => 'preview',
      //     'data' => array(
      //       'cover_heading' => "Lorem ipsum dolor sit amet",
      //       'cover_desc' => "Lorem ipsum dolor sit amet consectetur adipisicing",
      //       'cover_btn_txt' => "Lorem ipsum",
      //       'cover_btn_url' => "#",
      //       'cover_bottom' => "<span>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do tempor | </span><span>Tel: <a href=\"tel:01234567\">01234567</a> </span><span>Email: <a href=\"mailto:info@example.com\">info@example.com</a></span>",
      //     )
      //   )
      // )
      
    );
    
    /*
    Sections (with inners)
    */
    
    acf_register_block(array( // half & half section 
      
      // *required
      'name' => 'half_and_half_section',
      'title' => 'Half & Half section',
      
      // the callback function
      'render_callback' => array($this, 'half_and_half_section_render_callback'),
      
      // what block settings does this block allow
      'supports' => array(
        'align' => array('full', 'wide', 'center', ''), 
        'align_text' => false,
        'align_content' => true, 
        'full_height' => false, 
        // 'mode' => false,
        'jsx' => true
      ),
      
      // the defaults for various block settings
      'align' => 'full',
      'align_content' => 'center',
      
      // category & icon
      'category' => 'design',
      'icon' => 'align-pull-right',
      
      // keywords by which to search for the block
      'keywords' => array('half', 'section', 'rmcc'),
      
    ));
    
    acf_register_block(array( // cover section 
    
      // *required
      'name' => 'cover_section',
      'title' => 'Cover section',
    
      // the callback function
      'render_callback' => array($this, 'cover_section_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => array('full', 'wide', 'center', ''), 
        'align_text' => false,
        'align_content' => false,
        'full_height' => true,
        // 'mode' => false,
        'jsx' => true
      ),
    
      // the defaults for various block settings
      'align' => 'full',
      'full_height' => true,
      'mode' => 'preview',
    
      // category & icon
      'category' => 'design',
      'icon' => 'cover-image',
    
      // keywords by which to search for the block
      'keywords' => array('cover', 'section', 'rmcc'),
    
    ));
    
    acf_register_block(array( // cta section 
    
      // *required
      'name' => 'cta_section',
      'title' => 'Call-to-action section',
    
      // the callback function
      'render_callback' => array($this, 'cta_section_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => array('full', 'wide', 'center', ''), 
        'align_text' => false,
        'align_content' => false,
        'full_height' => false,
        // 'mode' => false,
        'jsx' => true
      ),
    
      // the defaults for various block settings
      'align' => 'full',
    
      // category & icon
      'category' => 'design', // what category the lock will be in 
      'icon' => 'editor-aligncenter', // icon used for the block (dashicons)
    
      // keywords by which to search for the block
      'keywords' => array('cta', 'call', 'to', 'action', 'section', 'rmcc'),
    
    ));
    
    acf_register_block(array( // Highlight card section 
    
      // *required
      'name' => 'highlight_card_section',
      'title' => 'Highlight card section',
    
      // the callback function
      'render_callback' => array($this, 'highlight_card_section_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => array('full', 'wide', 'center', ''), 
        'align_text' => false, 
        'align_content' => true, 
        'full_height' => false, 
        // 'mode' => false,
        'jsx' => true
      ),
    
      // the defaults for various block settings
      'align' => 'full',
      'align_text' => 'left',
      'align_content' => 'center',
    
      // category & icon
      'category' => 'design',
      'icon' => 'align-center',
    
      // keywords by which to search for the block
      'keywords' => array('highlight', 'card', 'section', 'rmcc'),
    
    ));
    
    // /*
    // Swiper & Repeater sections
    // */
    
    acf_register_block(array( // Slider 
    
      // *required
      'name' => 'slider_section',
      'title' => 'Slider section',
    
      // the callback function
      'render_callback' => array($this, 'slider_section_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => array('full', 'wide', 'center', ''), 
        'align_text' => true, 
        'align_content' => true, 
        'full_height' => true, 
        // 'mode' => false
      ),
    
      // the defaults for various block settings
      'align' => 'full',
      'align_text' => 'center',
      'align_content' => 'center',
    
      // category & icon
      'category' => 'design',
      'icon' => 'slides',
    
      // keywords by which to search for the block
      'keywords' => array('slider', 'section', 'rmcc'),
    
    ));
    
    acf_register_block(array( // Testimonials 
    
      // *required
      'name' => 'testimonials_section',
      'title' => 'Testimonials & Ratings section',
    
      // the callback function
      'render_callback' => array($this, 'testimonials_section_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => array('center'), 
        'align_text' => true, 
        'align_content' => false, 
        'full_height' => false, 
        // 'mode' => false
      ),
    
      // the defaults for various block settings
      'align' => 'center', 
      'align_text' => 'center', 
    
      // category & icon
      'category' => 'design',
      'icon' => 'editor-quote',
    
      // keywords by which to search for the block
      'keywords' => array('testimonials', 'rating', 'section', 'rmcc'),
    
    ));
    
    // /*
    // Buttons
    // */
    
    acf_register_block(array( // video popup button 
    
      // *required
      'name' => 'video_popup_button',
      'title' => 'Video popup button',
    
      // the callback function
      'render_callback' => array($this, 'video_popup_button_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => false, 
        'align_text' => true, 
        'align_content' => false, 
        'full_height' => false, 
        // 'mode' => false
      ),
    
      // the defaults for various block settings
      'align_text' => 'center',
    
      // category & icon
      'category' => 'design',
      'icon' => 'youtube',
    
      // keywords by which to search for the block
      'keywords' => array('video', 'popup', 'button', 'rmcc'),
    
    ));
    
    acf_register_block(array( // gallery button 
    
      // *required
      'name' => 'gallery_button',
      'title' => 'Gallery button',
    
      // the callback function
      'render_callback' => array($this, 'gallery_button_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => false, 
        'align_text' => true, 
        'align_content' => false, 
        'full_height' => false, 
        // 'mode' => false
      ),
    
      // the defaults for various block settings
      'align_text' => 'left',
    
      // category & icon
      'category' => 'design',
      'icon' => 'format-gallery',
    
      // keywords by which to search for the block
      'keywords' => array('gallery', 'button', 'rmcc'),
    
    ));
    
    // /*
    // Featured items - row
    // */
    
    acf_register_block(array( // Featured items row section 
    
      // *required
      'name' => 'featured_items_row',
      'title' => 'Featured items row',
    
      // the callback function
      'render_callback' => array($this, 'featured_items_row_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => array('full', 'wide', ''), 
        'align_text' => false, 
        'align_content' => 'matrix', 
        'full_height' => false, 
        // 'mode' => false
      ),
    
      // the defaults for various block settings
      'align' => 'full',
      'align_content' => 'center center',
    
      // category & icon
      'category' => 'design',
      'icon' => 'sticky',
    
      // keywords by which to search for the block
      'keywords' => array('featured', 'items', 'row', 'rmcc'),
    
    ));

    acf_register_block(array( // Products carousel section 
    
      // *required
      'name' => 'products_carousel_section',
      'title' => 'Products carousel section',
    
      // the callback function
      'render_callback' => array($this, 'products_carousel_section_render_callback'),
    
      // what block settings does this block allow
      'supports' => array(
        'align' => false, 
        'align_text' => false,
        'align_content' => false, 
        'full_height' => false, 
        // 'mode' => false,
        // 'jsx' => true
      ),
    
      // the defaults for various block settings
      // 'align_text' => 'center',
    
      // category & icon
      'category' => 'design',
      'icon' => 'align-pull-right',
    
      // keywords by which to search for the block
      'keywords' => array('products', 'carousel', 'section', 'rmcc'),
    
    ));
      
  }

  public function acf_blocks_editor_scripts() {
    
    // swiper
    wp_enqueue_style(
      'swiper',
      get_template_directory_uri() . '/inc/acf/blocks/assets/css/lib/swiper-bundle.min.css'
    );
    wp_enqueue_script(
      'swiper-js',
      get_template_directory_uri() . '/inc/acf/blocks/assets/js/lib/swiper-bundle.min.js',
      '',
      '1.0.0',
      false
    );
    
    // blocks (lightgallery)
    wp_enqueue_style(
      'rmcc-blocks',
      get_template_directory_uri() . '/inc/acf/blocks/assets/css/blocks.css'
    );
    wp_enqueue_script(
      'rmcc-blocks',
      get_template_directory_uri() . '/inc/acf/blocks/assets/js/blocks.js',
      '',
      '1.0.0',
      false
    );
    
    // preconnects
    wp_enqueue_style('picsum-preconnect', 'https://picsum.photos', '', null);
    wp_enqueue_style('lorem-picsum-preconnect', 'https://i.picsum.photos', '', null);
    wp_enqueue_style('picsum-prefetch', 'https://picsum.photos', '', null);
    wp_enqueue_style('lorem-picsum-prefetch', 'https://i.picsum.photos', '', null);
  
  }
  
  public function preconnects_filter($html, $handle) {
    if ($handle === 'picsum-preconnect') {
      return str_replace("rel='stylesheet'",
        "rel='preconnect'", $html);
    }
    if ($handle === 'lorem-picsum-preconnect') {
      return str_replace("rel='stylesheet'",
        "rel='preconnect'", $html);
    }
    if ($handle === 'picsum-prefetch') {
      return str_replace("rel='stylesheet'",
        "rel='dns-prefetch'", $html);
    }
    if ($handle === 'lorem-picsum-prefetch') {
      return str_replace("rel='stylesheet'",
        "rel='dns-prefetch'", $html);
    }
    return $html;
  }
  
  /*
  Sections (with inners)
  */
  
  public function half_and_half_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('half-and-half-section.twig', $context);
  }
  public function cover_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('cover-section.twig', $context);
  }
  public function cta_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('cta-section.twig', $context);
  }
  public function highlight_card_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('highlight-card-section.twig', $context);
  }
  public function products_carousel_section_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    // get products using Post()
    $products_ids = $context['fields']['select_products'];
    $products = null;
    if($products_ids){
      foreach($products_ids as $id){
        $product = new Post($id);
        $products[] = $product;
      }
    }
    $context['products'] = $products;
    
    Timber::render('products-carousel-section.twig', $context);
  }
  
  /*
  Swiper & Repeater sections
  */
  
  public function slider_section_render_callback($block, $content = '', $is_preview = false) { 
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;

    Timber::render('slider-section.twig', $context);
  }
  public function testimonials_section_render_callback($block, $content = '', $is_preview = false) { 
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('testimonials-section.twig', $context);
  }
  
  /*
  Buttons
  */
  
  public function gallery_button_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
  
    Timber::render('gallery-button.twig', $context);
  }
  
  public function video_popup_button_render_callback($block, $content = '', $is_preview = false) {
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
    
    Timber::render('video-popup-button.twig', $context);
  }
  
  /*
  Featured items - row
  */
  
  public function featured_items_row_render_callback($block, $content = '', $is_preview = false) { // 'featured_items' -> $context['items'] 
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;
  
    // join to the selected post to the repeater field item, if it exists. can be found at .post of featured_items item
    if($context['fields'] && $context['fields']['featured_items']){
      $items = null;
      foreach($context['fields']['featured_items'] as $item){
        if($item['select_post_object']){
          $item['post'] = new Post($item['select_post_object']);
        }
        $items[] = $item;
      }
      $context['items'] = $items;
    }
  
    Timber::render('featured-items-row-section.twig', $context);
  }
  
  // no ACF fields used. 2 posts arrays, first_posts & second_posts. uses stickies
  // public function blog_posts_render_callback($block, $content = '', $is_preview = false) {
  //   $context = Timber::context();
  //   $context['block'] = $block;
  //   $context['fields'] = get_fields();
  //   $context['is_preview'] = $is_preview;
  // 
  //   // get sticky posts before get_posts 
  //   $sticky = get_option('sticky_posts');
  // 
  //   // the first post (sticky enabled)
  //   $args1 = array(
  //     'post_type' => 'post',
  //     'post_status' => 'publish',
  //     'posts_per_page' => '1',
  //     'post__in'   => $sticky,
  //     'ignore_sticky_posts' => 1,
  //     'orderby' => 'date',
  //     'order' => 'DESC',
  //   );
  //   $context['first_posts'] = new PostQuery($args1);
  // 
  //   // the 2nd and/or 3rd post/s (with stickies enabled)
  //   $args2 = array(
  //     'post_type' => 'post',
  //     'post_status' => 'publish',
  //     'posts_per_page' => '2',
  //     'orderby' => 'date',
  //     'order' => 'DESC',
  //   );
  //   if($sticky) {
  //     $args2['post__not_in'] = $sticky;
  //   } 
  //   else {
  //     $args2['offset'] = '1';
  //   }
  //   $context['second_posts'] = new PostQuery($args2);
  // 
  //   Timber::render('blog-posts-section.twig', $context);
  // }

}