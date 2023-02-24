(function() {

    this.wpModelViewer = {

        onload : function(e) {
            var modelViewer = e.target;

            // 記事のプレビュー中か判定します
            // プレビュー中なら、マテリアルとアニメーションの一覧をコンソールに表示します。
            // 引数の参考にしてください。
            var previewf = false;
            var queris = window.location.search.slice(1).split('&');
            if (queris.indexOf('preview=true')) previewf = true;

            // マテリアルの操作
            if (previewf) {
                console.log("Material", modelViewer.model.materials);
            }

            // アニメーションの操作
            if (previewf) {
                console.log('Animations', modelViewer.availableAnimations);
            }

       },
   }

}).call(window);
