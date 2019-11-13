var apis = {
    Index: '登录注册模块',
    Order: '订单模块',
    Code: '二维码模块',
    Home: '首页抢单模块',
    User: '用户模块',
    BankCard:'银行卡',
    Belongs:'资产模块',
    SystemTool:'系统工具模块',
    Withdraw:'提现模块',
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
