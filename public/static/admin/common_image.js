/**
 * 上传图片
 * @param Ths
 * @param obj
 */
function uploadImgOne(Ths,url,obj) {
    $('body').append('<input type="file" name="file" id="uploadFile" style="display: none;" multiple="multiple"/>');
    $('#uploadFile').click();
    $('#uploadFile').change(function () {
        var imgBigArrar = [];
        imgBigArrar.push('uploadFile');
        //调用Js
        uploadImgF(imgBigArrar, function (ret) {
            if (is_json(ret)) {
                if (Number(ret.status) === 1) {
                    var img_id = ret.data.img_id;
                    var img_url = ret.data.img_url;
                    $(Ths).val(img_url);
                    $(Ths).next('.attach_id').val(img_id);
                    if ($(Ths).parent().children('p.m-top-sm').length >= 1) {
                        $(Ths).parent().children('p.m-top-sm').children('img').attr('src',  img_url);
                    } else {
                        $(Ths).parent().append('<p class="m-top-sm" style="margin-top: 10px;">\n' +
                            '<img src="' + img_url + '" width="120" />\n' +
                            '</p>');
                    }
                } else {
                    Lobibox.alert('error', {msg: ret.message});
                }
            }
            $('#uploadFile').remove();
        },obj,url);
    });
}

/**
 * 检查尺寸是否符合规范
 * @param f
 * @param callback
 * @param obj
 */
function uploadImgCheckedPx(f, callback, obj) {
    if(is_json(obj)) {
        var is_size = true;
        var reader = new FileReader();
        reader.onload = function (e) {
            // var data = e.target.result;
            //加载图片获取图片真实宽度和高度
            var image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                var width = image.width;
                var height = image.height;
                if (obj.maxwidth) {
                    if (obj.maxwidth > 0 ) {
                        if (width > obj.maxwidth ) {
                            Lobibox.alert('error', {msg: "请上传宽度小于等于" + obj.maxwidth +"px的图片"});
                            is_size = false;
                        }
                    }
                }
                if ( obj.maxheight) {
                    if (obj.maxheight > 0) {
                        if (height > obj.maxheight) {
                            Lobibox.alert('error', {msg: "请上传高度小于等于"  + obj.maxheight + "px的图片"});
                            is_size = false;
                        }
                    }
                }
                if (obj.width && obj.height) {
                    if (obj.width > 0 && obj.height > 0) {
                        if (width !== obj.width || height !== obj.height) {
                            Lobibox.alert('error', {msg: "请上传" + obj.width + "x" + obj.height + "px的图片"});
                            is_size = false;
                        }
                    }
                }
                if (obj.size) {
                    var s = obj.size * 1024 * 1024;
                    if (f.files[0].size > s) {
                        Lobibox.alert('error', {msg: '请上传小于等于 ' + obj.size + 'MB的图片'});
                        is_size = false;
                    }
                }
                callback && callback(is_size);
            };
        }
        var files = f.files;
        if (files.length > 0)
            reader.readAsDataURL(files[0]);
    }
}

/**
 * 上传图片到百度云（这里是向后台提交文件，然后后台进行上传百度云等云服务器）
 * @param inputArray
 * @param callback
 */
function uploadImgBackAjax(inputArray, callback, url) {
    var formData = new FormData();
    if(inputArray.length >1) {
        for (var i = 0; i < inputArray.length; i++) {
            formData.append('file['+i+']', $('#'+inputArray[i])[0].files[0]);
        }
    }else{
        formData.append('file', $('#uploadFile')[0].files[0]);
    }
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        beforeSubmit: function () {
            //上传图片之前的处理
        },
        uploadProgress: function (event, position, total, percentComplete) {
            //在这里控制进度条
        },
        success: function (ret) {
            callback && callback(ret);
        },
        error: function (data) {
            Lobibox.alert('error', {msg: '上传图片出错'});
        }
    });
}

/**
 * 上传图片
 * @param inputArray
 * @param callback
 * @param obj
 */
function uploadImgF(inputArray, callback, obj, url) {
    if (is_json(obj)) {
        var resStateArray = [];
        for (var i = 0; i < inputArray.length; i++) {
            var f = document.getElementById(inputArray[i]);
            uploadImgCheckedPx(f, function (state) {
                resStateArray.push(state);
            },obj);
        }
        var tempInterval = setInterval(function () {
            if (resStateArray.length === inputArray.length) {
                clearInterval(tempInterval);
                if (resStateArray.indexOf(false) !== -1) {
                    callback && callback(-1);
                } else {
                    uploadImgBackAjax(inputArray, function (res) {
                        callback && callback(res);
                    },url);
                }
            }
        }, 500);
    } else {
        uploadImgBackAjax(inputArray, function (res) {
            callback && callback(res);
        },url);
    }
}