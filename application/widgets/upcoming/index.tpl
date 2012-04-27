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
						Selection: <b><?=$sel->name?></b> (<?=$sel->odds?>)
					</div>
				<?php } ?>

			</div>
			<?php } ?>

	<?php } ?>

</div>

