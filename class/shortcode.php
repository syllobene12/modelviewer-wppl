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
         * メディアからGLTFファイル追加時にショートコードを自動入力
         */
        add_filter('media_send_to_editor', function( $html, $id, $attachment ) {
            $defaults = 'auto-rotate camera-controls';
            $fileinfo = pathinfo($attachment['url']);

            if (array_key_exists($fileinfo['extension'], MVPL_Data::UPLOAD_MODEL_TYPES)) {
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
                'src' => ''              // GTLFのパス
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
            $model_upload_dir = MVPL_Data::$model_upload_dir;
            $src_path = "{$upload_dir['baseurl']}/{$model_upload_dir}/{$src}";

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
    }
}
