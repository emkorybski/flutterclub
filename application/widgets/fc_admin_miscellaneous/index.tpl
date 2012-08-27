<div class="fc_admin_miscellaneous">
	<div class="update_balance">
		Competition start balance: <?=number_format($this->competitionStartBalance, 2, '.', ',')?>
		<input type="text" id="balance_update_value" value=""/>
		<button class="action_update_balance">Update Balance</button>
		<p></p>
	</div>
</div>

<script type="text/javascript">
	j('.update_balance .action_update_balance').live('click', function (evt) {
		evt.preventDefault();

		var fcAdminMiscellaneous = j('.fc_admin_miscellaneous');

		var balanceUpdateValue = j('#balance_update_value').val();
		j.ajax(WEB_ROOT + "widget?name=fc_admin_miscellaneous&action=updateBalance&format=html", {
			data:{ "balance_update_value":balanceUpdateValue },
			dataType:'html',
			success:function (text) {
				var content = j('.fc_admin_miscellaneous', text);
				fcAdminMiscellaneous.html(text)
			},
			complete:function () {
				fcAdminMiscellaneous.css({
					opacity:1
				}).removeClass('betting_upcoming_loading');
			}
		});
	});
</script>
