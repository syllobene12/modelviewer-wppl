<?php

class MVPL_Shortcode {
	/**
	 * 外部からのインスタンス化を防ぐ
	 */
	private function __construct() {}

	/**
	 * init
	 */
	public static function init() {
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
    }
}
