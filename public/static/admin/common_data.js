/**
 * 添加编辑数据表单提交
 * @param from
 * @param url
 * @param is_reload
 * @constructor
 */
function AddEditFrom(from, url, is_reload) {
    var type = 'POST';
    if (!url) {
        url = $(from).attr('action');
    }
    if ($(from).attr('method')) {
        type = $(from).attr('method');
    }
    $.ajax({
        type: type,
        url: url,
        data: $(from).serialize(),
        dataType: 'json',
        cache: false,
        success: function (ret) {
            if (Number(ret.status) === 1) {
                Lobibox.alert('success', {
                    msg: ret.message, closed: function () {
                        if (is_reload) {
                            window.location.reload();
                        } else {
                            self.location = document.referrer;
                        }
                    }
                });
            } else {
                if (ret.message) Lobibox.alert('error', {msg: ret.message});
                else Lobibox.alert('error', {msg: '返回未知错误！'});
            }
            return false;
        },
        error: function (data) {
            Lobibox.alert('error', {msg: '操作失败'});
        }
    });
    return false;
}

/**
 * 删除数据表单提交
 * @param url
 * @param id
 */
function deleteData(url, id) {
    var type = 'POST';
    $.ajax({
        type: type,
        url: url,
        data: {"id": id},
        dataType: 'json',
        cache: false,
        success: function (ret) {
            if (Number(ret.status) === 1) {
                Lobibox.alert('success', {
                    msg: ret.message, closed: function () {
                        window.location.reload();
                    }
                });
            } else {
                if (ret.message) Lobibox.alert('error', {msg: ret.message});
                else Lobibox.alert('error', {msg: '返回未知错误！'});
            }
            return false;
        },
        error: function (data) {
            Lobibox.alert('error', {msg: '操作失败'});
        }
    });
}


function editor_radio(_ths, url) {

    var _this = _ths.find('button');
    var id = _this.data('id');
    var field = _this.data('field');
    var value = _this.data('value');

    var _class = value == 1 ? 'btn-danger' : 'btn-success';


    if(field == 'status'){
        var name = value == 1 ? '禁止' : '正常';
    }else{
        var name = value == 1 ? '否' : '是';
    }

    var _value = value == 1 ? '0' : 1;
    layer.load();
    $.ajax({
        type: 'post',
        url: url,
        dataType: 'json',
        data: {
            'id': id,
            'field': field,
            'value': _value,
            'alone': 1
        },
        success: function (ret) {
            layer.closeAll();
            layer.msg(ret.message);
            if (Number(ret.status) === 1) {
                var str = '<p class="edit_radio_btn">' +
                    '<button type="button" class="btn ' + _class + ' btn-sm" data-field="' + field + '" data-id="' + id + '" data-value="' + _value + '">' + name +
                    '</button></p>';
                _ths.html(str);
            }
            return false;
        }
    });
}


function editor_field(_ths, url) {
    var id = _ths.data('id');
    var field = _ths.data('field');
    var _value = _ths.data('value');
    layer.prompt(
        {
            title: '修改',
            formType: 3,
            value: _value
        }, function (pass, index) {
            layer.load();
            $.ajax({
                type: "post",
                url: url,
                dataType: "json",
                data: {
                    'id': id,
                    'field': field,
                    'value': pass,
                    'alone': 1
                },
                success: function (ret) {
                    layer.closeAll();
                    if (Number(ret.status) === 1) {
                        _ths.html(pass);
                    } else {
                        layer.msg(ret.message);
                    }
                }
            })
        });
}