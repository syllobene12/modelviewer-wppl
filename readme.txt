=== modelviewr-wppl ===
Contributors: syllobenex1007
Tags: 3D model viewer, 3D viewer, model viewer, gltf
Requires at least: 6.1.1
Tested up to: 6.1.1
Stable tag: 0.1.0
Requires PHP: 7.0
License: Apache-2.0 or later
License URI: https://www.apache.org/licenses/LICENSE-2.0

modelviewer-wpplはwordpressで作成したWebサイトに3Dモデルを表示するためのプラグインです。

== Description ==

##  3Dモデルのデータファイルをアプロード可能にする

wordpressのメディアアップローダで3Dモデルのデータファイルを選択可能にします。
<br>

対応フォーマットは以下のとおりです。

| フォーマット | MIMEタイプ         | 拡張子 | 備考 |
| ----------- | ----------------- | ------ | --- |
| GLTF        | model/gltf+json   | .gltf  | テキストベースのglTFデータ形式 |
| GLB         | model/gltf-binary | .glb   | バイナリベースのglTFデータ形式 |

GLTFは3Dモデルのデータフォーマットの1つで、Web上で3Dコンテンツを効率的に表示するためのオープンスタンダードであり、&lt;model-viewer>はGLTF形式を推奨しています。

なお、最初に3Dモデルファイルをアップロードした時点でwordpressのuploadsフォルダ下にmodelフォルダを作成します。<br>
```
[root folder]/wp-content/uploads/model
```
アップロードした3Dモデルファイルは全てmodelフォルダに格納されます。<br>
重複する名前のファイルは自動的に連番が割り振られます。

##  &lt;model-viewer>を使用して3Dモデルを表示可能にする

&lt;model-viewer>は、Webブラウザ上で3Dモデルを表示することができます。具体的には、以下のような機能があります。

* 様々な3Dモデルフォーマット（GLTF, OBJ, FBXなど）に対応
* リアルタイムレンダリング、アニメーション、照明、環境マッピング、物理ベースレンダリングなどの機能を提供
* 様々な表示オプション（スピン、ズーム、カメラアングル、背景、レンダリング設定など）をカスタマイズ可能
* クリック、ドラッグ、ピンチジェスチャーなどのインタラクションに対応
* スマートフォンのAR機能に対応し、ARコンテンツを作成可能

これらの機能を活用することで、Webサイトやモバイルアプリケーション上で、高品質な3Dコンテンツを表示することができます。

詳細は[公式ドキュメント](https://modelviewer.dev/)をご覧ください。

わたしのブログでも解説をおこなっています。[まわすブログ](https://mawasu-blog.com/)

##  投稿ページでメディア追加時に自動的にショートコードを挿入する（旧エディタのみ）

投稿ページで「メディアを追加」から3Dモデルを選択すると、自動的にショートコードを挿入します。<br>

```
[model-viewer src=modelfile.glb auto-rotate camera-controls]
```

Webページに3Dモデルを表示するだけならデフォルトのままで使用可能です。

&lt;model-viewer>タグをダイレクトに使用するのとの違いは、srcに3Dモデルファイルのフルパス(URL)を入れる必要がないことです。

引数の解説
| 引数 | 機能 |
| --- | --- |
| auto-rotate | 3DモデルをWebページに表示した際に、自動的に回転させます |
| camera-controls | Webページ上でマウス、指を使って3Dモデルを回転・拡大／縮小等ができるようになります |


== Installation ==

1. From the WP admin panel, click “Plugins” -> “Add new”.
2. In the browser input box, type “modelviewer-wppl”.
3. Select the “My Custom Style Css Manager” plugin and click “Install”.
4. Activate the plugin.

OR…

1. Download the plugin from this page.
2. Save the .zip file to a location on your computer.
3. Open the WP admin panel, and click “Plugins” -> “Add new”.
4. Click “upload”.. then browse to the .zip file downloaded from this page.
5. Click “Install”.. and then “Activate plugin”.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

== Upgrade Notice ==

