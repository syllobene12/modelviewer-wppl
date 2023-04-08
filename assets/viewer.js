(function() {

    function parse_material_para(materialData) {
        var material_list = [];

        if (materialData == '') {
            return [];
        }

        var material_para = materialData.split(';');

        for (let m = 0; m < material_para.length; m++) {
            var material = material_para[m];
            if (material == "") continue;

            material_p = material.split(':');

            material_list.push({
                name: material_p[0],
                view: material_p.length == 1 ? '' : material_p[1],
            })
        }

        return material_list;
    }

    function swapNode(node1, node2) {

        function isNode (node) {
            return Object(node) === node && typeof node.nodeType === 'number';
        }

        if (!isNode(node1)) {
            throw new TypeError('parameter 1 is not of type \'Node\'');
        }

        if (!isNode(node2)) {
            throw new TypeError('parameter 2 is not of type \'Node\'');
        }

        if (node1 === node2) {
            return 1;
        }

        var parentNode1 = node1.parentNode,
            parentNode2 = node2.parentNode;

        if (!parentNode1) {
            throw new TypeError('parentNode of parameter 1 is ' + parentNode1);
        }

        if (!parentNode2) {
            throw new TypeError('parentNode of parameter 2 is ' + parentNode2);
        }

        var nextNode1 = node1.nextSibling;

        if (nextNode1 === node2) {
            parentNode1.insertBefore(node2, node1);
        } else {
            parentNode2.replaceChild(node1, node2);
            nextNode1 ? parentNode1.insertBefore(node2, nextNode1) : parentNode1.appendChild(node2);
        }
        return 0;
    }

    function addCanvasOperation(mvBackCanvas, canvasNode) {

        const ctx = canvasNode.getContext('2d');

        // ドラッグ状態かどうか
        let isDragging = false;
        // ドラッグ開始位置
        let start = {
            x: 0,
            y: 0
        };
        // ドラッグ中の位置
        let diff = {
            x: 0,
            y: 0
        };
        // ドラッグ終了後の位置
        let end = {
            x: 0,
            y: 0
        }
        // 拡大・縮小範囲
        var scale = 1;

        var touchstart_bar = 0;
        var touchmove_bar = 0;
        var touch_zoom = false;

        const redraw = () => {
            //描画リセット
            ctx.clearRect(0, 0, canvasNode.width, canvasNode.height);
            //描画状態保存
            ctx.save();
            //画像拡大・縮小
            ctx.scale(scale, scale);
            //画像再描画
            ctx.drawImage(canvasNode.image, diff.x, diff.y)
            //描画状態復元
            ctx.restore();
        };

        // 描画移動処理
        canvasNode.addEventListener('mousedown', mdown, false);
        canvasNode.addEventListener('touchstart', mdown, false);
        canvasNode.addEventListener('mousemove', mmove, false);
        canvasNode.addEventListener('touchmove', mmove, false);
        canvasNode.addEventListener('mouseup', mup, false);
        canvasNode.addEventListener('touchend', mup, false);

        function mdown(e) {
            isDragging = true;
            if(e.type === "mousedown") {
                var event = e;
            } else {
                var event = e.changedTouches[0];
                touchstart_bar = 0;
                touchmove_bar = 0;

                if (e.touches.length > 1) {
                    touch_zoom = true;

                    //絶対値を取得
                    w_abs_start = Math.abs(e.touches[1].pageX - e.touches[0].pageX);
                    h_abs_start = Math.abs(e.touches[1].pageY - e.touches[0].pageY);
                    //はじめに2本指タッチした時の面積
                    touchstart_bar = w_abs_start*h_abs_start;
                }
            }

            start.x = event.clientX;
            start.y = event.clientY;
        }
        function mmove(e) {
            //フリックしたときに画面を動かさないようにデフォルト動作を抑制
            e.preventDefault();

            //console.log(e)
            if(e.type === "mousemove") {
                var event = e;
            } else {
                var event = e.changedTouches[0];
            }

            if (e.touches && e.touches.length > 1) {
                if (e.touches.length > 1 && touch_zoom) {
                    //絶対値を取得
                    w_abs_move = Math.abs(e.touches[1].pageX - e.touches[0].pageX);
                    h_abs_move = Math.abs(e.touches[1].pageY - e.touches[0].pageY);
                    //ムーブした時の面積
                    touchmove_bar = w_abs_move * h_abs_move;
                    //はじめに2タッチ面積からムーブした時の面積を引く
                    area_bar = touchstart_bar - touchmove_bar;

                    if(area_bar < 0){ //拡大する
                        scale *= 1.02;
                    } else if (area_bar > 0){//縮小する
                        scale *= 0.98;
                    }
                    redraw();
                }
            } else {
                if (isDragging) {
                    diff.x = (event.clientX - start.x) + end.x;
                    diff.y = (event.clientY - start.y) + end.y;
                    redraw();
                }
            }
        }
        function mup(e) {
            touch_zoom = false;
            isDragging = false;
            end.x = diff.x;
            end.y = diff.y;
        }

        // 拡大・縮小処理
        canvasNode.addEventListener('wheel', mwheel, false);
        function mwheel(e) {
            //ページがスクロールしないようにイベントキャンセル
            e.preventDefault();
            //マウスホイールの Up/Down でscaleを0.1増減
            scale += e.deltaY * -0.001;
            //最小値(0.5)・最大値(2)を指定
            scale = Math.min(Math.max(0.3, scale), 4);
            redraw();
        }
    }

    class MVCanvasOperater {

        constructor() {
            this.mvBackCanvas = null;
            this.canvasNode = null;
            this.target = null;
        }

        imgPreView(e) {
            var target = e.target;

            var file = target.files[0];
            var reader = new FileReader();

            var root = target.closest('wp-model-viewer');
            const modelViewer = root.querySelector('model-viewer');

            var mvBackCanvas = root.querySelector('.mv-back-canvas');
            var canvasNode = document.createElement('canvas');
            canvasNode.classList.add('mv-back');

            var _this = this;
            reader.onload = () => {

                var image = new Image();
                image.src = reader.result;
                image.crossOrigin = 'anonymous';
                image.onload = (e) => {

                    mvBackCanvas.classList.add('show');

                    //var image = e.target;
                    var width = image.naturalWidth;
                    var height = image.naturalHeight;

                    canvasNode.width = modelViewer.clientWidth;
                    canvasNode.height = modelViewer.clientHeight;
                    canvasNode.image = image;

                    const ctx = canvasNode.getContext('2d');
                    ctx.drawImage(e.target, 0, 0, width, height);

                    addCanvasOperation(mvBackCanvas, canvasNode);

                    if (_this.canvasNode != null) {
                        _this.canvasNode.remove();
                    }

                    mvBackCanvas.appendChild(canvasNode);

                    _this.mvBackCanvas = mvBackCanvas;
                    _this.canvasNode = canvasNode;
                    _this.target = target;
                }
                image.onerror = () => {
                    console.log('画像読み込み失敗');
                }
            };
            reader.readAsDataURL(file);
        }

        clear() {
            this.canvasNode.remove();
            this.canvasNode = null;

            this.mvBackCanvas.classList.remove('show');
            this.mvBackCanvas = null;

            this.target.value = "";
            this.target = null;
        }

        toggleIndex() {
            this.canvasNode.classList.toggle('prev');
        }

        switchMoveMode() {
            this.mvBackCanvas.classList.toggle('move');
            this.canvasNode.classList.toggle('move');
        }
    }

    this.wpModelViewer = {

        onload : function(e) {
            var modelViewer = e.target;

            // もとに戻すボタン
            var undoButton = modelViewer.querySelector('button.undoButton');
            modelViewer.undoColor = function() {
                undoButton.classList.remove('show');
                for (let i = 0; i < modelViewer.model.materials.length; i++) {
                    let _material = modelViewer.model.materials[i];
                    if (_material.pbrMetallicRoughness.orgColor == undefined) continue;
                    //console.log(_material.pbrMetallicRoughness.orgColor);
                    _material.pbrMetallicRoughness.setBaseColorFactor(_material.pbrMetallicRoughness.orgColor);
                }
                let selectButtons = modelViewer.querySelectorAll('.select_button button');
                for (let i = 0; i < selectButtons.length; i++) {
                    selectButtons[i].undo();
                }
            }
            undoButton.addEventListener('click', modelViewer.undoColor)

            // 記事のプレビュー中か判定します
            // プレビュー中なら、マテリアルとアニメーションの一覧をコンソールに表示します。
            // 引数の参考にしてください。
            var previewf = false;
            var queris = window.location.search.slice(1).split('&');
            for (let q = 0; q < queris.length; q++) {
                if (queris[q] === 'preview=true') {
                    previewf = true;
                    break;
                }
            }

            // カラー変更ボタンの追加
            if (previewf) {
                console.log("Material", modelViewer.model.materials);
            }
            var material_list = parse_material_para(modelViewer.dataset.material)
            var colorSelector = modelViewer.querySelector('.select_colors');

            function rgbaToString(rgba) {
                r = Math.round(rgba[0] * 255);
                g = Math.round(rgba[1] * 255);
                b = Math.round(rgba[2] * 255);
                a = rgba[3];
                return `rgba(${r}, ${g}, ${b}, ${a})`;
            }
            for (let i = 0; i < material_list.length; i++) {

                const material = modelViewer.model.getMaterialByName(material_list[i].name);

                if (material != undefined) {

                    const rgbaString = rgbaToString(material.pbrMetallicRoughness.baseColorFactor);
                    material.pbrMetallicRoughness.orgColor = rgbaString;

                    let newDivEle = document.createElement('div');
                    newDivEle.classList.add('select_button');

                    let newTitleEle = document.createElement('span');
                    newTitleEle.classList.add('title');
                    newTitleEle.textContent = material_list[i].view;

                    let newButtonEle = document.createElement('button');
                    newButtonEle.style.backgroundColor = rgbaString;

                    function colorchange(color) {
                        const colorString = color.toHexString();
                        material.pbrMetallicRoughness.setBaseColorFactor(colorString);
                        newButtonEle.style.backgroundColor = colorString;
                        undoButton.classList.add('show');
                    }
                    $(newButtonEle).spectrum({
                        color: rgbaString,
                        showInput: true,
                        preferredFormat: "hex",
                        showInitial: true,
                        showButtons: false,
                        showPalette: true,
                        showPaletteOnly: true,
                        togglePaletteOnly: true,
                        togglePaletteMoreText: 'もっと！',
                        togglePaletteLessText: 'とじる！',
                        palette: [
                            ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
                            ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
                            ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
                            ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
                            ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
                            ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
                            ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
                            ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"],
                        ],
                        change: colorchange,
                        move: colorchange,
                        show: function() {
                            newTitleEle.classList.add('show');
                        },
                        hide: function() {
                            newTitleEle.classList.remove('show');
                        }
                    });
                    newButtonEle.undo = function() {
                        this.style.backgroundColor = rgbaString;
                        $(newButtonEle).spectrum('set', rgbaString);
                    }

                    newDivEle.appendChild(newTitleEle);
                    newDivEle.appendChild(newButtonEle);
                    colorSelector.appendChild(newDivEle);
                }
            }

            // アニメーション操作ボタン
            if (previewf) {
                console.log('Animations', modelViewer.availableAnimations);
            }
            var animationOperater = modelViewer.querySelector('.animation_operater');
            var animationToggle = animationOperater.querySelector('.toggle');
            animationToggle.addEventListener('click', function(){
                if (modelViewer.paused) {
                    modelViewer.play();
                    this.classList.remove('play')
                    this.classList.add('pause')
                } else {
                    modelViewer.pause();
                    this.classList.remove('pause')
                    this.classList.add('play')
                } 
            });
            if (modelViewer.autoplay) {
                animationOperater.classList.add('show');
                animationToggle.classList.add('pause');
            }
        },

        /*
         * 画像データの取得
         */
        download3DImage : function (e, type, encoderOptions) {

            var root = e.target.closest('wp-model-viewer');

            var modelViewer = root.querySelector('model-viewer');
            var mvDataURL = modelViewer.toDataURL(type, encoderOptions);

            var drawCanvas = document.createElement('canvas');

            var backCanvas = root.querySelector('.mv-back-canvas canvas.mv-back');

            var win = window.open();
            var image = new Image();
            image.src = mvDataURL;
            image.onload = () => {
                drawCanvas.width = image.width;
                drawCanvas.height = image.height;

                const ctx = drawCanvas.getContext('2d');

                if (backCanvas == undefined) {
                    ctx.drawImage(image, 0, 0, image.width, image.height);
                } else {
                    if (backCanvas.classList.contains('prev')) {
                        ctx.drawImage(image, 0, 0, image.width, image.height);
                        ctx.drawImage(backCanvas, 0, 0, image.width, image.height);
                    } else {
                        ctx.drawImage(backCanvas, 0, 0, image.width, image.height);
                        ctx.drawImage(image, 0, 0, image.width, image.height);
                    }
                }

                var base64 = drawCanvas.toDataURL('image/png');
                if (win) {
                    win.document.write(`<img src="${base64}"></img>`);
                    win.document.body.style.backgroundColor = '#000';
                }
            }
        },

        /*
         * 3Dデータの取得
         */
        download3DFormat : function (e, type) {
            var modelViewer = e.target.closest('wp-model-viewer').querySelector('model-viewer');
            const a = document.createElement("a");
            a.href = modelViewer.src;
            a.download = modelViewer.src.split("/").reverse()[0].split('.')[0] + '.' + type;
            a.click();
        },

        /*
        * メインビューの変更
        */
        changeMainModelView : function (e) {
            if (e.target.tagName !== 'MODEL-VIEWER') return;

            var subViewer = e.target;

            // メインビューだったら変更しません
            var classList = subViewer.parentNode.className.split(' ');
            if(/^mv_main-*/.test(classList)) return;

            // グループ取得
            var type = classList.find(value => value.match(/^mv_sub-*/g));
            var group = type.split('-')[1];

            // 同グループのメインノード取得
            var mainParent = subViewer.closest('.post_content').querySelector('wp-model-viewer.mv_main-' + group);
            if (mainParent == null) return;

            var mainViewer = mainParent.querySelector('model-viewer');

            // ビューの入れ替え
            if (swapNode(mainViewer, subViewer) == 0) {
                // メインビューリセット
                mainViewer.undoColor();
                mainViewer.autoRotate = true;
                mainViewer.cameraControls = false;

                /*
                wpModelViewer.canvas.clear();
                */

                subViewer.autoRotate = false;
                subViewer.cameraControls = true;
            }
        },

        canvas : new MVCanvasOperater(),
    }

}).call(window);
