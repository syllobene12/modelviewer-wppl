<?php

/*
 * Plugin Name: modelviewer-wppl
 * Plugin URI: https://github.com/syllobene12/modelviewer-wppl
 * Description: &lt;model-viewer&gt;をwordpressで使用するためのプラグインです。
 * Requires at least: 6.2
 * Version: 0.1.0
 * Author: syllobene12
 * Author URI: https://twitter.com/syllobene12
 * Text Domain: modelviewer-wppl
 */
defined( 'ABSPATH' ) || exit;

/**
 * 定数宣言
 */
if ( ! defined( 'MVPL_PATH' ) ) {
    define( 'MVPL_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'MVPL_URL' ) ) {
    define( 'MVPL_URL', plugins_url( '/', __FILE__ ) );
}

// プラグインのバージョン
$file_data = get_file_data( __FILE__, [ 'version' => 'Version' ] );
if ( ! defined( 'MVPL_VERSION' ) ) {
    define( 'MVPL_VERSION', $file_data['version'] );
}

/**
 * Reading class files
 */
require_once MVPL_PATH . 'class/data.php';
require_once MVPL_PATH . 'class/modelviewer-wrap.php';
require_once MVPL_PATH . 'class/shortcode.php';

/**
 * Main class
 */
class ModelViewer_WPPL {
    public function __construct() {
        MVPl_Data::init();
        MVPL_MVWrap::init();
        MVPL_Shortcode::init();
    }
}

/**
 * Run ModelViewer_WPPL
 */
add_action( 'plugins_loaded', function() {
    new ModelViewer_WPPL();
} );

