<?php

/*
 * Plugin Name: modelviewer-wppl
 * Plugin URI: https://github.com/syllobene12/modelviewer-wppl
 * Description: &lt;model-viewer&gt;をwordpressで使用するためのプラグインです。
 * Requires at least: 6.1.1
 * Version: 0.1.0
 * Author: syllobene12
 * Author URI: https://twitter.com/syllobene12
 * Text Domain:       modelviewer-wppl
 */

/**
 * 必要な定数を定義しておく
 */
define('MY_PLUGIN_VERSION', '0.1.0');
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugins_url('/', __FILE__));

/**
 * head内に<model-viewer>スクリプトの読み込みコードを追加する
 */
// 記事画面用
add_action( 'wp_head', function() {
    $headcustomtag = <<<EOM
    <script type='module' src='https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js'></script>
    <script nomodule src='https://unpkg.com/@google/model-viewer/dist/model-viewer-legacy.js'></script>
    EOM;
    echo $headcustomtag;
}, 99);
// 管理画面用
add_action( 'admin_head', function() {
    $headcustomtag = <<<EOM
    <script type='module' src='https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js'></script>
    <script nomodule src='https://unpkg.com/@google/model-viewer/dist/model-viewer-legacy.js'></script>
    EOM;
    echo $headcustomtag;
}, 99);


/**
 * スクリプト スタイルシートの読み込み
 */
add_action('wp_enqueue_scripts', function() {

    /** JS */
    wp_enqueue_script(
        'modelviewer-wppl',
        MY_PLUGIN_URL . 'assets/viewer.js',
        array(),
        MY_PLUGIN_VERSION,
        true
    );

    /** CSS */
    wp_enqueue_style(
        'modelviewer-wppl',
        MY_PLUGIN_URL . 'assets/viewer.css',
        array(),
        MY_PLUGIN_VERSION
    );

});

/**
 * アップロード可能ファイルタイプにGLTFを追加
 */
add_filter('upload_mimes', function( $mime_types ) {
    $mime_types['gltf'] = 'model/gltf+json';
    $mime_types['glb'] = 'model/gltf-binary';
    return $mime_types;
});
add_filter('wp_check_filetype_and_ext', function( $info, $tmpfile, $filename, $mimes ) {
    if ( strpos( $filename, '.gltf' ) !== false ) {
        $info['ext'] = 'gltf';
        $info['type'] = 'model/gltf+json';
    } else if ( strpos( $filename, '.glb' ) !== false ) {
        $info['ext'] = 'glb';
        $info['type'] = 'model/gltf-binary';
    }
    return $info;
}, 10, 4);

/**
 * GLTFファイルのアップロードフォルダを変更
 */
add_filter( 'wp_handle_upload_prefilter', function( $file ) {
    if ( strpos( $file['name'], '.gltf' ) !== false || strpos( $file['name'], '.glb' ) !== false ) {
        add_filter( 'upload_dir', function( $param ) {
            // ひとまず"/gltf"以下に保存します。
            #$param['subdir']  = "/model" . $param['subdir'];
            $param['subdir']  = "/model";
            $param['path'] = $param['basedir'] . $param['subdir'];
            $param['url']  = $param['baseurl'] . $param['subdir'];
            return $param;
        });
    }
    return $file;
});

/**
 * メディアからGLTFファイル追加時にショートコードを自動入力
 */
add_filter('media_send_to_editor', function( $html, $id, $attachment ) {
    $defaults = 'auto-rotate camera-controls';
    $fileinfo = pathinfo($attachment['url']);
    if ($fileinfo['extension'] == 'gltf' || $fileinfo['extension'] == 'glb') {
        $html = "[model-viewer src={$fileinfo['basename']} $defaults]";
    }
	return $html;
}, 10, 3);

/**
 * ショートコードメイン処理
 */
add_shortcode('model-viewer', function( $args ) {
    ob_start();
    //console_log($args);
    
    // ショートコード用引数解析
    extract(shortcode_atts(array(
        'src' => '',              // GTLFのパス
    ), $args));

    // model-viewer用引数解析
    $mv_args_s = '';
    {
        // 引数セット
        $mv_args = array();
        foreach ($args as $key => $value) {
            if ($key === 'src') {
                continue;
            }
            if (gettype($key) === 'integer') {
                // キーが数値(配列インデックス)だったらフラグONとして判定
                $mv_args = array_merge($mv_args, array($value => true));
            } else {
                // キーが文字列BooleanだったらBoolean型に変換
                if ($value === 'true') {
                    $value = true;
                } else if ($value === 'false') {
                    $value = false;
                }
                $mv_args = array_merge($mv_args, array($key => $value));
            }
        }
        //console_log($mv_args);

        // model-viewer用引数 array -> string
        foreach ($mv_args as $key => $value) {
            //console_log($key . ':' . $value . ':' . gettype($value));
            if (gettype($value) == 'boolean') {
                if ($value) {
                    $mv_args_s .=  "{$key} ";
                }
            } else {
                $mv_args_s .=  "{$key}={$value} ";
            }
        }
        //console_log($mv_args_s);
    }

    // GLTFファイルパス取得
    $upload_dir = wp_upload_dir();
    $src_path = "{$upload_dir['baseurl']}/model/{$src}";

    // メイン処理
    $response = @file_get_contents($src_path, NULL, NULL, 0, 1);   // ファイルの存在チェック
    if ($response !== false) {

        // 指定したファイルが存在すれば、表示します
        echo "<wp-model-viewer>";
        echo "  <model-viewer src='$src_path' $mv_args_s onload='wpModelViewer.onload(event)'>";
        echo "  </model-viewer>";
        echo "</wp-model-viewer>";

    } else {
        // 指定したファイルが存在しなければ、エラーメッセージを表示します
        echo "<p style='color:red;'>3D data file not found.</p>";
    }

    return ob_get_clean();
});

/**
 * modelviewer-wpplの管理メニュー画面(未実装) 
 */
/*
function test_function() {
  echo '<h1>Hello World</h1>';
}
function modelviewer-wppl_setup_menu(){
  add_menu_page(
    __( 'modelviewer_wppl', 'textdomain' ), // ページタイトル
    'ModelViewer',  // メニュータイトル
    'manage_options', // 
    'modelviewer_wppl_setup_menu',  // メニューslug
    'test_function',  // 実行する関数
    'dashicons-chart-pie',  // メニューに表示するアイコン
    6 // メニューの表示位置
  );
}
add_action('admin_menu', 'modelviewer_wppl_setup_menu');
*/
/*
 * デバッグ用関数
 */
function console_log($data) {
    echo '<script>';
    echo 'console.log('.json_encode($data).')';
    echo '</script>';
}

function debug_log($data) {
    error_log(print_r($data,true) . "\n", "3", './debug.txt');
}
