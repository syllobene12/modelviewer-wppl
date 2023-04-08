<?php

class MVPL_Mawasu {

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
            echo '<link href="https://fonts.googleapis.com/earlyaccess/nicomoji.css" rel="stylesheet"/>';
            echo '<script src="https://code.jquery.com/jquery-3.6.4.slim.min.js" integrity="sha256-a2yjHM4jnF9f54xUQakjZGaqYs/V1CYvWpoqZzC2/Bw=" crossorigin="anonymous"></script>';
            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js"></script>';
            echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css">';
        }, 99);

        // 管理画面用
        add_action( 'admin_head', function() {
            echo '<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.0.1/model-viewer.min.js"></script>';
        }, 99);
    }
}
