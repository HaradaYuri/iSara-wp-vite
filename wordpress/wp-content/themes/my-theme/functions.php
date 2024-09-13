<?php
if (!defined('ABSPATH')) exit;

define('THEME_VERSION', '1.0.0');
define('IS_DEVELOPMENT', true);

/**
 * Enqueue Vite built assets
 */
function enqueue_vite_assets() {
    if (IS_DEVELOPMENT) {
        // Development mode: Load from Vite dev server
        wp_enqueue_script('vite-client', 'http://localhost:3000/@vite/client', [], null);
        wp_enqueue_script('main-js', 'http://localhost:3000/src/main.js', [], null, true);
    } else {
        // Production mode: Load built assets
        $manifest_path = get_stylesheet_directory() . '/dist/manifest.json';
        if (file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);
            if (isset($manifest['src/main.js'])) {
                $main_js = $manifest['src/main.js'];
                $version = isset($main_js['file']) ? filemtime(get_stylesheet_directory() . '/dist/' . $main_js['file']) : null;
                wp_enqueue_script('main-js', get_stylesheet_directory_uri() . '/dist/' . $main_js['file'], [], $version, true);
                if (isset($main_js['css'])) {
                    foreach ($main_js['css'] as $css_file) {
                        $css_version = filemtime(get_stylesheet_directory() . '/dist/' . $css_file);
                        wp_enqueue_style('main-css', get_stylesheet_directory_uri() . '/dist/' . $css_file, [], $css_version);
                    }
                }
            }
        } else {
            // Error handling if manifest file is not found
            error_log('Vite manifest file not found: ' . $manifest_path);
        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_vite_assets');

/**
 * Enqueue theme assets
 */
function theme_enqueue_assets() {
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], '6.5.1');

    // Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Marcellus&family=Parisienne&display=swap', [], null);

    // Typekit Fonts
    wp_enqueue_style('typekit-fonts', 'https://use.typekit.net/gzw7lod.css', [], null);

    // Slick Carousel
    wp_enqueue_style('slick-carousel', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', [], '1.8.1');
    wp_enqueue_script('slick-carousel', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], '1.8.1', true);

    // Stickyfill
    wp_enqueue_script('stickyfill', 'https://cdn.jsdelivr.net/npm/stickyfill@2.1.0/dist/stickyfill.min.js', ['jquery'], '2.1.0', true);

    // jQuery
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'theme_enqueue_assets');

/**
 * Custom title for Yoast SEO
 */
function custom_wpseo_title($title) {
    if (is_front_page()) return 'CompanyName | Title';
    if (is_page('about')) return 'About | CompanyName';
    if (is_page('price')) return 'Price | CompanyName';
    return $title;
}
add_filter('wpseo_title', 'custom_wpseo_title');
add_filter('wpseo_opengraph_title', 'custom_wpseo_title');

/**
 * Custom meta description for Yoast SEO
 */
function custom_wpseo_metadesc($description) {
    if (is_front_page() || is_page('about') || is_page('price')) return 'ディスクリプション';
    return $description;
}
add_filter('wpseo_metadesc', 'custom_wpseo_metadesc');
add_filter('wpseo_opengraph_desc', 'custom_wpseo_metadesc');

/**
 * Add theme support
 */
function theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
}
add_action('after_setup_theme', 'theme_setup');

/**
 * Register navigation menus
 */
function register_theme_menus() {
    register_nav_menus([
        'primary' => __('Primary Menu', 'theme-textdomain'),
        'footer' => __('Footer Menu', 'theme-textdomain'),
    ]);
}
add_action('init', 'register_theme_menus');

/**
 * Disable WordPress emoji script
 */
function disable_wp_emojicons() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
add_action('init', 'disable_wp_emojicons');

/**
 * Remove unnecessary meta tags
 */
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

/**
 * Custom excerpt length
 */
function custom_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'custom_excerpt_length', 999);

/**
 * Custom excerpt more
 */
function custom_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'custom_excerpt_more');