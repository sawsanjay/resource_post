<?php 
/**
 * Plugin Name: Resource Post
 * Description: A plugin for adding resource custom post type functionality to your WordPress site. when you activate this plugin, create a Resource page, as an archive page for resources.
 * Version: 1.0
 * Author: sanjay saw
 * Author URI: http://google.com
 * Text Domain: resource post
 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

register_activation_hook( __FILE__, 'add_my_custom_page' );

function add_my_custom_page() {
  $page_title = 'Resource';
	$page_content = '[resource_archive]';
	$page = array(
		'post_title'     => $page_title,
		'post_content'   => $page_content,
		'post_status'    => 'publish',
		'post_type'      => 'page',
		'post_author'    => 1,
		'post_name'      => 'resource'
	);
	wp_insert_post( $page );
	
}

register_deactivation_hook(__FILE__, 'delete_my_custom_page');

function delete_my_custom_page() {
   
    wp_delete_post(get_page_by_path('resource')->ID, true);
}

add_action('init', function() {
  register_post_type('resource', [
      'label' => __('Resources', 'txtdomain'),
      'public' => true,
      'menu_position' => 5,
      'menu_icon' => 'dashicons-book',
      'supports' => ['title', 'editor', 'thumbnail', 'author', 'revisions', 'comments'],
      'show_in_rest' => true,
      'rewrite' => ['slug' => 'resource'],
      'taxonomies' => ['resource_type', 'resource_topic'],
      'labels' => [
          'singular_name' => __('Resources', 'txtdomain'),
          'add_new_item' => __('Add new resource', 'txtdomain'),
          'new_item' => __('New resource', 'txtdomain'),
          'view_item' => __('View resource', 'txtdomain'),
          'not_found' => __('No resources found', 'txtdomain'),
          'not_found_in_trash' => __('No resources found in trash', 'txtdomain'),
          'all_items' => __('All resources', 'txtdomain'),
          'insert_into_item' => __('Insert into resource', 'txtdomain')
      ],		
  ]);

  register_taxonomy('resource_type', ['resource'], [
      'label' => __('Type', 'txtdomain'),
      'hierarchical' => true,
      'rewrite' => ['slug' => 'resource-type'],
      'show_admin_column' => true,
      'show_in_rest' => true,
      'labels' => [
          'singular_name' => __('Type', 'txtdomain'),
          'all_items' => __('All Types', 'txtdomain'),
          'edit_item' => __('Edit Type', 'txtdomain'),
          'view_item' => __('View Type', 'txtdomain'),
          'update_item' => __('Update Type', 'txtdomain'),
          'add_new_item' => __('Add New Type', 'txtdomain'),
          'new_item_name' => __('New Type Name', 'txtdomain'),
          'search_items' => __('Search Types', 'txtdomain'),
          'parent_item' => __('Parent Type', 'txtdomain'),
          'parent_item_colon' => __('Parent Type:', 'txtdomain'),
          'not_found' => __('No Types found', 'txtdomain'),
      ]
  ]);
  register_taxonomy_for_object_type('resource_type', 'resource');

  register_taxonomy('resource_topic', ['resource'], [
      'label' => __('Topic', 'txtdomain'),
      'hierarchical' => false,
      'rewrite' => ['slug' => 'resource-topic'],
      'show_admin_column' => true,
      'labels' => [
          'singular_name' => __('Topic', 'txtdomain'),
          'all_items' => __('All Topics', 'txtdomain'),
          'edit_item' => __('Edit Topic', 'txtdomain'),
          'view_item' => __('View Topic', 'txtdomain'),
          'update_item' => __('Update Topic', 'txtdomain'),
          'add_new_item' => __('Add New Topic', 'txtdomain'),
          'new_item_name' => __('New Topic Name', 'txtdomain'),
          'search_items' => __('Search Topics', 'txtdomain'),
          'popular_items' => __('Popular Topics', 'txtdomain'),
          'separate_items_with_commas' => __('Separate Topics with comma', 'txtdomain'),
          'choose_from_most_used' => __('Choose from most used Topics', 'txtdomain'),
          'not_found' => __('No Topics found', 'txtdomain'),
      ]
  ]);
  register_taxonomy_for_object_type('resource_topic', 'resource');

});

function custom_archive_shortcode() {
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'page/archive-resource.php';
    return ob_get_clean();
}
add_shortcode( 'resource_archive', 'custom_archive_shortcode' );

function resource_plugin_enqueue_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-validate', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', array( 'jquery' ), '1.19.3', true );
}
add_action( 'wp_enqueue_scripts', 'resource_plugin_enqueue_scripts' );

function get_ajax_posts()
{
  $post_id = $_POST['id'];
  if ('search_resource' == $_POST['task']) {
    $resource_title = $_POST['search_value'];
    $page = $_POST['page'];
    $limit = 9;
    $resource_serch_count = 0;
    $args_search = array('post_type' => 'resource', 'post_status' => 'publish', "s" => $resource_title, 'order'   => 'DESC', 'posts_per_page' => $limit, 'paged' => $page,);
    $args_only_six = array('post_type' => 'resource', 'post_status' => 'publish', "s" => $resource_title, 'order'   => 'DESC', 'paged' => $page,);
    $result_only_six = new WP_Query($args_only_six);
    $only_six = $result_only_six->found_posts;
    $result_search = new WP_Query($args_search);
    if ($result_search->have_posts()) :
      while ($result_search->have_posts()) : $result_search->the_post(); ?>
        <div class="col-lg-4 col-sm-6">
          <div class="complet_pro_bx">
            <a href="<?php echo get_the_permalink(); ?>">
              <div class="complet_pro_img">
                <img src="<?php if (!empty(get_the_post_thumbnail_url(get_the_ID(), 'full'))) {
                            echo get_the_post_thumbnail_url(get_the_ID(), 'full');
                          } else {
                            echo get_site_url(); ?>/wp-content/uploads/2022/08/defualt.jpg<?php } ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true); ?>">
              </div>
              <div class="complet_pro_body">
                <h5><?php echo get_the_title(); ?></h5>
                <div class="resource_date mb-3"><?php echo get_the_date(); ?></div>
                <p><?php if (!empty(get_the_excerpt())) {
                      echo wp_trim_words(get_the_excerpt(), 20, '...');
                    } else {
                      echo wp_trim_words(get_the_content(), 20, '...');
                    } ?></p>
                <div class="complet_pro_footer d-flex align-items-center justify-content-end">
                  <a href="<?php echo get_the_permalink(); ?>" class="redmore_btn">Read More</a>
                </div>
              </div>
            </a>
          </div>
        </div>
    <?php
        $resource_serch_count++;
      endwhile;
      if ($only_six  == 9) {
        echo '<span class="only_six" data-id="' . $only_six . '"></span>';
      } else {
      }
      if ($resource_serch_count < '10') {
        echo '<span class="r_count' . $page . '" data-id="' . $resource_serch_count . '"></span>';
      } else {
      }
    else :
      echo '<p class="noMorePostsFound text-center text-danger result-found bls_not_found" data-id="0">No More Search Results Found...</p>';
    endif;
    wp_reset_postdata(); ?>

    <?php
    die(1);
    exit;
  }
  if ('resource_cat' == $_POST['task']) {
    $resource_id = $_POST['cate_id'];
    $cat_page = $_POST['cat_page'];
    $limit_cat = 9;
    $resource_cat_count = 0;
    $args_cat = array('post_type' => 'resource', 'orderby' => 'post_date', 'post_status' => 'publish', 'tax_query' =>  array(array('taxonomy' => 'resource_type', 'field' => 'term_id',  'terms'  => $resource_id)), 'order' => 'DESC',  'posts_per_page' => $limit_cat, 'paged' => $cat_page);
    $result_search = new WP_Query($args_cat);
    if ($result_search->have_posts()) :
      while ($result_search->have_posts()) : $result_search->the_post(); ?>

        <div class="col-lg-4 col-sm-6">
          <div class="complet_pro_bx">
            <div class="complet_pro_img">
              <img src="<?php if (!empty(get_the_post_thumbnail_url(get_the_ID(), 'full'))) {
                          echo get_the_post_thumbnail_url(get_the_ID(), 'full');
                        } else {
                          echo get_site_url(); ?>/wp-content/uploads/2022/08/defualt.jpg<?php } ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true); ?>">
            </div>
            <div class="complet_pro_body">
              <a href="<?php echo get_the_permalink(); ?>">
                <h5><?php echo get_the_title(); ?></h5>
              </a>
              <div class="resource_date mb-3"><?php echo get_the_date(); ?></div>
              <p><?php if (!empty(get_the_excerpt())) {
                    echo wp_trim_words(get_the_excerpt(), 20, '...');
                  } else {
                    echo wp_trim_words(get_the_content(), 20, '...');
                  } ?></p>
              <div class="complet_pro_footer d-flex align-items-center justify-content-end">
                <a href="<?php echo get_the_permalink(); ?>" class="redmore_btn">Read More</a>
              </div>
            </div>
          </div>
        </div>
      <?php
        $resource_cat_count++;
      endwhile;
      if ($resource_cat_count < '10') {
        echo '<span class="r_count' . $cat_page . '" data-id="' . $resource_cat_count . '"></span>';
      } else {
      }
    else :
      echo '<p class="noMorePostsFound text-center text-danger result-found blc_not_found" data-id="0">No More resources Found...</p>';
    endif;
    wp_reset_postdata();
    die(1);
    exit;
  }
  if ('resource_topic' == $_POST['task'] || 'ready_resource' == $_POST['task']) {
    $resource_id = $_POST['cate_id']; 
    $m_page = $_POST['m_page'];
    $limit = 9;
    $resource_mcat_count = 0;
    if ($resource_id == 'all' || !empty($_POST['post_type'])) {
       
      $args_cat = array('post_type' => 'resource', 'orderby' => 'post_date', 'post_status' => 'publish', 'order' => 'DESC',  'posts_per_page' => $limit, 'paged' => $m_page);
    } else {
       
       $args_cat = array('post_type' => 'resource', 'orderby' => 'post_date', 'post_status' => 'publish', 'tax_query' =>  array(array('taxonomy' => 'resource_topic', 'field' => 'term_id',  'terms'  => $resource_id)),'order' => 'DESC',  'posts_per_page' => $limit, 'paged' => $m_page);
       $args_only_six = array('post_type' => 'resource', 'orderby' => 'post_date', 'post_status' => 'publish', 'tax_query' =>  array(array('taxonomy' => 'resource_topic', 'field' => 'term_id',  'terms'  => $resource_id)), 'order' => 'DESC',  'paged' => $m_page);
      $result_only_six = new WP_Query($args_only_six);
      $only_six = $result_only_six->found_posts;
    }
    $result_search = new WP_Query($args_cat);
    if ($result_search->have_posts()) :
      while ($result_search->have_posts()) : $result_search->the_post(); ?>
        <div class="col-lg-4 col-sm-6">
          <div class="complet_pro_bx">
            <div class="complet_pro_img">
              <img src="<?php if (!empty(get_the_post_thumbnail_url(get_the_ID(), 'full'))) {
                          echo get_the_post_thumbnail_url(get_the_ID(), 'full');
                        } else {
                          echo get_site_url(); ?>/wp-content/uploads/2022/08/defualt.jpg<?php } ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true); ?>">
            </div>
            <div class="complet_pro_body">
              <a href="<?php echo get_the_permalink(); ?>">
                <h5><?php echo get_the_title(); ?></h5>
              </a>
              <div class="resource_date mb-3"><?php echo get_the_date(); ?></div>
              <p><?php if (!empty(get_the_excerpt())) {
                    echo wp_trim_words(get_the_excerpt(), 20, '...');
                  } else {
                    echo wp_trim_words(get_the_content(), 20, '...');
                  } ?></p>
              <div class="complet_pro_footer d-flex align-items-center justify-content-end">
                <a href="<?php echo get_the_permalink(); ?>" class="redmore_btn">Read More</a>
              </div>
            </div>
          </div>
        </div>
      <?php
        $resource_mcat_count++;
      endwhile;
      if ($only_six  == 9) {
        echo '<span class="only_six" data-id="' . $only_six . '"></span>';
      } else {
      }
      if ($resource_mcat_count < '10') {
        echo '<span class="r_count' . $m_page . '" data-id="' . $resource_mcat_count . '"></span>';
      } else {
      }
    else :
      echo '<p class="noMorePostsFound text-center text-danger result-found mlc_not_found" data-id="0">No More resources Found...</p>';
    endif;
    wp_reset_postdata();
    die(1);
    exit;
  }
}
add_action('wp_ajax_get_ajax_posts', 'get_ajax_posts');
add_action('wp_ajax_nopriv_get_ajax_posts', 'get_ajax_posts');

?>