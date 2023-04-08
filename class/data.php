<?php

class MVPL_Data {

    /**
     * 外部からのインスタンス化を防ぐ
     */
    private function __construct() {}

    // 3Dモデル形式の一覧
    const UPLOAD_MODEL_TYPES = [
        'gltf' => [ 'type' => 'model/gltf+json' ],
        'glb'  => [ 'type' => 'model/gltf-binary' ],
        'usdz' => [ 'type' => 'model/vnd.usdz+zip' ]
    ];

    // 画像形式の一覧：skybox-imageやenvironment-imageのアップロードに使用します
    const UPLOAD_IMAGE_TYPES = [
        'hdr'  => [ 'type' => 'image/vnd.radiance' ]
    ];

    const MODEL_UPLOAD_ROOT = "/model";

    public static $model_upload_dir = '';

    /**
     * Set Data
     */
    public static function init() {
        self::$model_upload_dir = self::MODEL_UPLOAD_ROOT;
    }

}

