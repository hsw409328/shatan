/**
 * Created by MrHao on 2017/2/14.
 */
var AjaxCommon = {
    url: '/c/',
    data: '',
    method: '',
    callback_func: null,
    post: function () {
        $.post(AjaxCommon.url, AjaxCommon.data, AjaxCommon.callback);
    },
    get: function () {
        $.get(AjaxCommon.url, AjaxCommon.callback);
    },
    callback: function (data, status) {
        AjaxCommon.callback_func(data, status);
    }
};

/**
 AjaxCommon.data = {"action":"b","run":"c"};
 AjaxCommon.callback_func = function(data,status){
      console.log(data);
    };
 AjaxCommon.post();
 */

var UtilCommon = {
    website: 'http://wx.quhaibian.cn',
    href: function (url) {
        window.location.href = UtilCommon.website + url;
    },
    reload: function () {
        window.location.reload();
    },
    back: function () {
        window.history.back();
    },
    parseJson: function (data) {
        return eval('(' + data + ')');
    },
};

