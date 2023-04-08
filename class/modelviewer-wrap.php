<?php

class MVPL_MVWrap {

    /**
     * 外部からのインスタンス化を防ぐ
     */
    private function __construct() {}

    /**
     * init
     */
    public static function init() {

        /**
         * head内に<model-viewer>スクリプトの読み込みコードを追加する
         */
        // 記事画面用
        add_action( 'wp_head', function() {
            echo '<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.0.1/model-viewer.min.js"></script>';
        }, 99);

        // 管理画面用
        add_action( 'admin_head', function() {
            echo '<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.0.1/model-viewer.min.js"></script>';
        }, 99);


        /**
         * スクリプト スタイルシートの読み込み
         */
        add_action('wp_enqueue_scripts', function() {

            // JS
            wp_enqueue_script(
                'modelviewer-wppl',
                MVPL_URL . 'assets/viewer.js',
                array(),
                MVPL_VERSION,
                true
            );

            // CSS
            wp_enqueue_style(
                'modelviewer-wppl',
                MVPL_URL . 'assets/viewer.css',
                array(),
                MVPL_VERSION
            );

        });

        /**
         * アップロード可能ファイルタイプにGLTFを追加
         */
        add_filter('upload_mimes', function( $mime_types ) {

            $ext_list = array_keys(MVPL_Data::UPLOAD_MODEL_TYPES);
            foreach($ext_list as $ext) {
                $mime_types[$ext] = MVPL_Data::UPLOAD_MODEL_TYPES[$ext][$type];
            }

            //global $image_types;
            $ext_list = array_keys(MVPL_Data::UPLOAD_IMAGE_TYPES);
            foreach($ext_list as $ext) {
                $mime_types[$ext] = MVPL_Data::UPLOAD_IMAGE_TYPES[$ext][$type];
            }

            return $mime_types;
        });

        add_filter('wp_check_filetype_and_ext', function( $info, $tmpfile, $filename, $mimes ) {
            //global $model_types, $image_types;

            $fileinfo = pathinfo($filename);
            $ext = $fileinfo['extension'];

            if (array_key_exists($ext, MVPL_Data::UPLOAD_MODEL_TYPES)) {
                $info['ext'] = $ext;
                $info['type'] = MVPL_Data::UPLOAD_MODEL_TYPES[$ext]['type'];
            } else if (array_key_exists($ext, MVPL_Data::UPLOAD_IMAGE_TYPES)) {
                $info['ext'] = $ext;
                $info['type'] = MVPL_Data::UPLOAD_IMAGE_TYPES[$ext]['type'];
            }

            return $info;
        }, 10, 4);

        /**
         * 3Dモデルファイルのアップロードフォルダを変更
         */
        add_filter( 'wp_handle_upload_prefilter', function( $file ) {
            //global $model_types;

            $fileinfo = pathinfo($file['name']);
            $ext = $fileinfo['extension'];

            if (array_key_exists($ext, MVPL_Data::UPLOAD_MODEL_TYPES)) {
                add_filter( 'upload_dir', function( $param ) {
                    // ひとまず"/model"以下に保存します。
                    #$param['subdir']  = "/model" . $param['subdir'];
                    $param['subdir']  = MVPL_Data::$model_upload_dir;
                    $param['path'] = $param['basedir'] . $param['subdir'];
                    $param['url']  = $param['baseurl'] . $param['subdir'];
                    return $param;
                });
            }
            return $file;
        });
    }

}
