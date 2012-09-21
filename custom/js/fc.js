var j = jQuery;
var WEB_ROOT = '/fc/';

if (!window.fc) fc = {}
if (!fc.user) fc.user = {}

fc.user.updateAccountBalance = function () {
    var accountInfo = j('.account_info');
    accountInfo.css({
        opacity:0.5
    }).addClass('betting_upcoming_loading');

    j.ajax(WEB_ROOT + 'widget?name=fc_account_info&format=html', {
        data:{balance:1},
        dataType:'html',
        success:function (text) {
            var content = j('.account_info_user_points', accountInfo);
            content.html('FB$ ' + text);
        },
        complete:function () {
            accountInfo.css({
                opacity:1
            }).removeClass('betting_upcoming_loading');
        }
    });
}

fc.user.updateBettingSlip = function () {
    var bettingSlip = j('.fc_betting_slip');
    bettingSlip.css({
        opacity:0.5
    }).addClass('betting_upcoming_loading');

    j.ajax(WEB_ROOT + 'widget?name=fc_betting_slip&format=html', {
        dataType:'html',
        success:function (text) {
            var content = j('.fc_betting_slip', text);
            bettingSlip.html(content)
        },
        complete:function () {
            bettingSlip.css({
                opacity:1
            }).removeClass('betting_upcoming_loading');
        }
    });
}
fc.user.updateBettingPending = function (data) {
    var bettingPending = j('.fc_betting_pending');
    bettingPending.css({
        opacity:0.5
    }).addClass('betting_upcoming_loading');

    j.ajax(WEB_ROOT + 'widget?name=fc_betting_pending&format=html', {
        data:data || {},
        dataType:'html',
        success:function (text) {
            var content = j('.fc_betting_pending', text);
            bettingPending.html(content)
        },
        complete:function () {
            bettingPending.css({
                opacity:1
            }).removeClass('betting_upcoming_loading');
        }
    });
}


fc.user.updateBettingRecent = function (data) {
    var bettingRecent = j('.fc_betting_recent');
    bettingRecent.css({
        opacity:0.5
    }).addClass('betting_upcoming_loading');

    j.ajax(WEB_ROOT + 'widget?name=fc_betting_recent&format=html', {
        data:data || {},
        dataType:'html',
        success:function (text) {
            var content = j('.fc_betting_recent', text);
            bettingRecent.html(content)
        },
        complete:function () {
            bettingRecent.css({
                opacity:1
            }).removeClass('betting_upcoming_loading');
        }
    });
}

fc.user.updateBettingMarkets = function (data) {
    var bettingMarkets = j('.fc_betting_markets');
    bettingMarkets.css({
        opacity:0.5
    }).addClass('betting_upcoming_loading');

    j.ajax(WEB_ROOT + 'widget?name=fc_betting_markets&format=html', {
        data:data || {},
        dataType:'html',
        success:function (text) {
            var content = j('.fc_betting_markets', text);
            bettingMarkets.html(content);
            Smoothbox.bind();
        },
        error:function () {
            alert('Internal error. Try again.');
        },
        complete:function () {
            bettingMarkets.css({
                opacity:1
            }).removeClass('betting_upcoming_loading');
        }
    });
}

setTimeout(function () {
    OverText.update();
}, 10);