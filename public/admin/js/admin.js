/*
author: kudy
site: www.kudystudio.com
date: 2012-06-20
版权所有：本岛工作室 [使用此后台模板必须保留此信息]
*/

var Admin = {};
var Login = {};
var Index = {};
var Pager = {};
var CheckBox = {};

/* -----  Admin ----- */

Admin.Loaded = false;
Admin.Init = function () {
    // 其它初始化
    $('a').live('focus', function () {
        $(this).blur();
    });
    $('input[type=radio]').live('focus', function () {
        $(this).blur();
    });
    $('input[type=checkbox]').live('focus', function () {
        $(this).blur();
    });
    $('input[type=checkbox]').css('border', 'none');
    $('.btn-middle').css({
        'margin-bottom': (J.IsIE6 ? 1 : 3) + 'px'
    }); //修改按钮水平对齐
    // 隔行变色
    $('table.data-table tr:even').addClass('even');

    // 三态按钮
    $('.btn')
    .live('mousedown', function () {
        $(this).addClass('btn-active');
    })
    .live('mouseup', function () {
        $(this).removeClass('btn-active');
    })
    .live('mouseover', function () {
        $(this).addClass('btn-hover');
    })
    .live('mouseout', function () {
        $(this).removeClass('btn-active');
        $(this).removeClass('btn-hover');
    });
    $('.btn-lit')
    .live('mousedown', function () {
        $(this).addClass('btn-lit-active');
    })
    .live('mouseup', function () {
        $(this).removeClass('btn-lit-active');
    })
    .live('mouseover', function () {
        $(this).addClass('btn-lit-hover');
    })
    .live('mouseout', function () {
        $(this).removeClass('btn-lit-active');
        $(this).removeClass('btn-lit-hover');
    });

    // 浏览改大小
    $(window).bind('resize', function () {
        if ((Admin.Loaded || $.browser.mozilla) && (Admin.IsLoginPage || Admin.IsIndexPage)) {
            var url = window.location.href;
            var pos = url.indexOf('#'); // 有#时不起作用
            window.location = pos > -1 ? url.substring(0, pos) : url;
        }
        Admin.Loaded = true;
    });
};

Admin.Logout = function () {
    jBox.confirm('确定要退出系统吗？', '提示', function (v, h, f) {
        if (v == 'ok') {
            location.href = Admin.LogoutUrl;
        }
        return true;
    });
};

Admin.SubmitForm = function (name) {
    var form = (name == undefined || name == '') ? document.forms[0] : document.forms[name];
    if (J.IsObject(form)) {
        $('.btn-lit').setDisabled(true).click(function () {
            return false;
        });
        if (jBox != undefined) {
            jBox.tip('正在提交...', 'loading');
        }
        form.submit();
    }
};

/* -----  Login ----- */

Login.Init = function () {
    // IE情况下修正自适应高度
    if ($.browser.msie) {
        var height = $(document).height() - 338;
        if (J.IsIE6) {
            height = height - 20;
        }
        $('#login_auto_height').css({
            height: height - ($('#login_form tr').length - 2) * 38 - 53 + 30
        });
    }
    // 验证码
    $('#refresh_verify_image').click(Login.LoadCaptcha);
    $('#verify_image').click(Login.LoadCaptcha).load(function () {
        $(this).show();
    });
    Login.LoadCaptcha();
    // 提交登录
    $('#login_btn').click(Login.Submit);
    $('#password').keydown(function (event) {
        J.EnterSubmit(event, Login.Submit);
    });
    $('#verifycode').keydown(function (event) {
        J.EnterSubmit(event, Login.Submit);
    });
    $('#username').keydown(function (event) {
        J.EnterSubmit(event, Login.Submit);
    }).focus();
};

Login.LoadCaptcha = function () {
    $('#verify_image').attr('src', Admin.VerifyImageUrl + '?t=' + Math.random());
};

Login.Tip = function (text, icon) {
    jBox.tip(text, icon, {
        top: '100px'
    });
};

Login.Submit = function () {
    var username = $('#username').trim();
    var password = $('#password').trim();
    var verifycode = $('#verifycode').trim();
    var rememberMe = $('#rememberMe').get(0).checked;

    if (username == '') {
        Login.Tip('请输入用户！', 'warning');
        $('#username').focus();
        return;
    }
    if (password == '') {
        Login.Tip('请输入密码！', 'warning');
        $('#password').focus();
        return;
    }
    if (verifycode == '') {
        Login.Tip('请输入验证码！', 'warning');
        $('#verifycode').focus();
        return;
    }
    var data = {
        username: username, 
        password: password, 
        rememberMe: rememberMe, 
        verifycode: verifycode
    };

    //直接到首页

    top.location.href = Admin.IndexUrl;
    return;

    Login.Tip('正在登录，请稍后...', 'loading');
    J.PostJson(Admin.LoginUrl, data, function (data) {
        if (data.code == 1) {
            top.location.href = Admin.IndexUrl;
        } else {
            Login.LoadCaptcha();
            if (data.code == 2) {
                Login.Tip('验证码已失效，请重新输入！', 'error');
                $('#verifycode').val('').focus();
            } else if (data.code == 3) {
                Login.Tip('验证码错误，请重新输入！', 'error');
                $('#verifycode').select();
            } else {
                Login.Tip(data.message, 'error');
                $('#username').select();
            }
        }
    }, function () {
        Login.Tip('登录出错！', 'error');
    });
};

/* -----  Index ----- */
Index.MenuIndex = 0;
Index.MenuSpeed = 250;
Index.Init = function () {
    // 初始化高与宽
    var win = $(window);
    var width = win.width() - 182 - 0 - 2;
    var height = win.height() - 64 - 2 - 33;
    var height_c = height - 29 - 8 - 7;
    $('#left').height(height);
    $('#right').height(height).width(width);
    $('#menu-container').height(height - 3);
    $('#content-container').height(height_c);
    $('#loading').css('padding-top', height_c / 2);

    // 菜单切换效果
    var tits = $('.menu-tit');
    var lists = $('.menu-list').hide();
    $(lists[0]).slideDown(Index.MenuSpeed);
    $.each(tits, function (i, el) {
        $(el).attr('index', i);
    });
    tits.click(function (e) {
        var me = $(this);
        var index = parseInt(me.attr('index'));
        if (index != Index.MenuIndex) {
            var last = Index.MenuIndex;
            Index.MenuIndex = index;
            $(lists[last]).slideUp(Index.MenuSpeed);
            $(lists[index]).slideDown(Index.MenuSpeed);
        }
    });

    // 菜单点击事件
    var links = $('a[target=content]');
    if (links.length > 0) {
        links.bind('click', function (e) {
            if (e.preventDefault)
                e.preventDefault();
            else
                e.returnValue = false;

            Index.Open($(this).attr('href'));
        });
    //links[0].click();
    }
    Index.Open(Admin.IndexStartPage);
};

Index.OutputIframe = function () {
    var scrolling = $.isIE6 == true ? 'yes' : 'auto';
    document.write('<iframe id="content" width="100%" height="100%" class="hide" marginwidth="0" marginheight="0" frameborder="0" scrolling="' + scrolling + '" onload="$(\'#loading\').hide();$(this).show();" src=""></iframe>');
};

Index.Open = function (url) {
    if (url != '') {
        top.$('#loading').show();
        if (url.indexOf('#') == -1) {
            url = url + (url.indexOf('?') == -1 ? '?___t=' : '&___t=') + Math.random();
        } else {
            var arr = url.split('#');
            url = arr[0] + (arr[0].indexOf('?') == -1 ? '?___t=' : '&___t=') + Math.random() + '#' + arr[1];
        }
        top.$('#content').hide().attr('src', url);

        // 可能在jBox里点击跳转，同时关闭它
        if (jBox) {
            jBox.close();
        }
    }
};

Index.SetTitle = function (title) {
    top.$('#index-title').html(title);
};

Pager.Data = {};
Pager.Output = function (urlFormat, pageSize, pageIndex, pageCount, recordCount) {
    pageSize = parseInt(pageSize, 10);
    pageIndex = parseInt(pageIndex, 10);
    pageCount = parseInt(pageCount, 10);
    recordCount = parseInt(recordCount, 10);

    if (pageIndex < 1)
        pageIndex = 1;
    if (pageIndex > pageCount)
        pageIndex = pageCount;

    Pager.Data.urlFormat = urlFormat;
    Pager.Data.pageCount = pageCount;

    function _getLink(text, enabled, urlFormat, index) {
        if (enabled == false)
            return J.FormatString(' <a class="button-white" style="filter:Alpha(Opacity=60);opacity:0.6;" href="javascript:void(0);"><span>{0}</span></a>', text);
        else
            return J.FormatString(' <a class="button-white" href="javascript:Index.Open(\'' + urlFormat + '\');"><span>{1}</span></a>', index, text);
    }

    var html = [];
    html.push('<div class="pager-bar">');
    html.push(J.FormatString('<div class="msg">共{0}条记录，当前第{1}/{2}，每页{3}条记录</div>', recordCount, pageIndex, pageCount, pageSize));
    html.push(_getLink('首页', pageIndex > 1, urlFormat, 1));
    html.push(_getLink('上一页', pageIndex > 1, urlFormat, pageIndex - 1));
    html.push(_getLink('下一页', pageCount > 0 && pageIndex < pageCount, urlFormat, pageIndex + 1));
    html.push(_getLink('未页', pageCount > 0 && pageIndex < pageCount, urlFormat, pageCount));
    html.push('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
    html.push('第<input id="current-index" onkeydown="J.EnterSubmit(event, Pager.Jump);" class="input-small" style="text-align:center;" type="text" value="' + (pageIndex > 0 ? pageIndex : '') + '" />页');
    html.push('&nbsp;&nbsp;&nbsp;&nbsp;');
    html.push('<a class="button-white"' + (pageCount <= 1 ? ' style="filter:Alpha(Opacity=60);opacity:0.6;" href="javascript:void(0);"' : ' href="javascript:Pager.Jump();"') + '><span>跳转</span></a>');
    html.push('</div>');

    document.write(html.join(''));
};
Pager.Jump = function () {
    var index = $('#current-index').trim();
    if (J.IsIntPositive(index) == false || parseInt(index) < 1 || parseInt(index) > Pager.Data.pageCount) {
        $('#current-index').val('').focus();
        return;
    }
    Index.Open(J.FormatString(Pager.Data.urlFormat, index));
}

CheckBox.CheckAll = function (checkBox, containerId) {
    if (containerId == undefined)
        $("input[type=checkbox]").each(function () {
            this.checked = checkBox.checked;
        });
    else {
        $("#" + containerId + " input[type=checkbox]").each(function () {
            this.checked = checkBox.checked;
        });
    }
};
CheckBox.GetCheckedIds = function (containerId) {
    if (containerId == undefined)
        return $("input.check-box:checked").map(function () {
            return $(this).attr("rel");
        }).get();
    else
        return $("#" + containerId + " input.check-box:checked").map(function () {
            return $(this).attr("rel");
        }).get();
};

// 初始化
$(function () {
    Admin.Init();
    if (Admin.IsLoginPage) {
        Login.Init();
    }
    if (Admin.IsIndexPage) {
        Index.Init();
    }
});

// 只调用最顶的jBox
if (top.jBox != undefined) {
    window.jBox = top.jBox;
}