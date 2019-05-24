/**
 * 判断是否是json数据
 * @param ret
 * @returns {boolean}
 */
function is_json(ret) {
    if(typeof(ret) === "object" && Object.prototype.toString.call(ret).toLowerCase() === "[object object]" && !ret.length) {
        return true;
    }
    return false;
}

/**
 * 弹框页面
 * @param title
 * @param url
 * @param id
 * @param height
 * @constructor
 */
function pageTable(title,url,id,height) {
    var  params;
    if (id) {
        params = 'id=' + id;
    }

    if(!height) height = 600;
    Lobibox.window({
        title: title,
        width:1000,
        height:height,
        url: url,
        autoload: true,
        loadMethod: 'get',
        params: params,
        buttons: {
            close: {
                text: '关闭',
                closeOnClick: true
            }
        },
        callback: function (res) {
            window.location.reload();
        }
    });
}