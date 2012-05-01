<style type="text/css">
	.betting_event_title {
		margin-top: 2em;
		font-size: large;
		font-weight: bold;
	}
	.betting_event {
		cursor: pointer;
		padding: 3px;
		margin-left: 1em;
	}
	.betting_event:hover {
		background-color: #f5f5f5;
	}
	.betting_subevent {
		margin-top: 1em;
		margin-left: 2em;
	}
	.betting_selection {
		margin-left: 1em;
		cursor: pointer;
	}
	.betting_selection:hover {
		color: blue;
		text-decoration: underline;
	}
</style>

<div class="betting_upcoming">

	<?php foreach ($this->upcoming as $event) { ?>

		<div class="betting_event_title"><?=$event->name?></div>
		<?php foreach ($event->getChildEvents() as $subEvent) { ?>
			<div class="betting_subevent">

				<div class="betting_subevent_title"><?=$subEvent->name?></div>
				<?php foreach ($subEvent->getChildSelections() as $sel) { ?>
					<div class="betting_selection">
						<span class="betting_selection_id"><?=$sel->id?></span>
						Selection: <b><?=$sel->name?></b> (<?=$sel->odds?>)
					</div>
				<?php } ?>

			</div>
			<?php } ?>

	<?php } ?>

</div>

<script type="text/javascript">
	jQuery('.betting_selection').click(function (e) {
		var name = jQuery(e.currentTarget).find('b')[0].innerHTML;
		var amount = parseFloat(prompt('How many points do you want to bet on ' + name + '?'));
		if (amount < 1) {
			alert('Invalid number of points (must be >=1)');
			return;
		}
		var id = parseInt(jQuery(e.currentTarget).find('.betting_selection_id')[0].innerHTML);
		jQuery.ajax('/fc/widget/index/name/upcoming', {
			type: 'get',
			data: {
				format: 'html',
				vote_selection_id: id,
				vote_amount: amount
			},
			success: function (text) {
				if (!text.length) {
					alert('Internal error');
					return;
				}
				var el = jQuery('.account_info_user_points')[0];
				var val = Math.round((100 * (parseFloat(el.innerHTML) - amount)) / 100);
				jQuery(el).html(val);
			}
		});
	});
</script>

