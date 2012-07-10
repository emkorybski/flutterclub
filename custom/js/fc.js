if (!window.fc) fc = {}
if (!fc.user) fc.user = {}

fc.user.updateAccountBalance = function (newBalance) {
	j('.account_info_user_points').html(parseInt(newBalance) || 0);
}

fc.user.updateBettingSlip = function (data) {
	var bettingSlip = j('.fc_betting_slip');
	bettingSlip.css({
		opacity: 0.5
	}).addClass('betting_upcoming_loading');
	j.ajax('/fc/widget/index/name/fc_betting_slip?format=html', {
		data: data || {},
		dataType: 'html',
		success: function (text) {
			bettingSlip.html(text)
		},
		complete: function () {
			bettingSlip.css({
				opacity: 1
			}).removeClass('betting_upcoming_loading');
		}
	});
}
fc.user.updateBettingPending = function (data) {
	var bettingPending = j('.fc_betting_pending');
	bettingPending.css({
		opacity: 0.5
	}).addClass('betting_upcoming_loading');
	j.ajax('/fc/widget/index/name/fc_betting_pending?format=html', {
		data: data || {},
		dataType: 'html',
		success: function (text) {
			bettingPending.html(text)
		},
		complete: function () {
			bettingPending.css({
				opacity: 1
			}).removeClass('betting_upcoming_loading');
		}
	});
}


fc.user.updateBettingRecent = function (data) {
	var bettingRecent = j('.fc_betting_recent');
	bettingRecent.css({
		opacity: 0.5
	}).addClass('betting_upcoming_loading');
	j.ajax('/fc/widget/index/name/fc_betting_recent?format=html', {
		data: data || {},
		dataType: 'html',
		success: function (text) {
			bettingRecent.html(text)
		},
		complete: function () {
			bettingRecent.css({
				opacity: 1
			}).removeClass('betting_upcoming_loading');
		}
	});
}



fc.user.confirmBet = function () {
	fc.user.updateBettingSlip();
	function confirmBet() {
		alert('Bet added to betting slip');
	}
}

fc.user.updateBettingUpcoming = function (data) {
	if (!fc.user.upcomingUrl) {
		return;
	}
	var bettingUpcoming = j('.fc_upcoming');
	bettingUpcoming.css({
		opacity: 0.5
	}).addClass('betting_upcoming_loading');
	j.ajax(fc.user.upcomingUrl, {
		data: data || {},
		dataType: 'html',
		success: function (text) {
			bettingUpcoming.html(text);
		},
		error: function () {
			alert('Internal error. Try again.');
		},
		complete: function () {
			bettingUpcoming.css({
				opacity: 1
			}).removeClass('betting_upcoming_loading');
		}
	});
}

fc.user.upcomingUrl = null;

setTimeout(function () {
	OverText.update();
}, 10);

