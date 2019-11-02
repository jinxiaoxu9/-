var apis = {
    Index: '用户个人中心模块',
    Order: '订单模块',
}

var comments = {
}

var apiSelect = {};
function runGroup(group) {
    if (typeof apiSelect[group] === 'undefined') {
        return;
    }

    var selectHtml = '';
    for (i in apiSelect[group]) {
        selectHtml += '<option value="' + group + '-' + i + '">' + apiSelect[group][i].title + '</option>';
    }
    $('#api_select').html(selectHtml).select2().change();
}
