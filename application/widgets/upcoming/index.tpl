<?php if (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'html')) { ?>
<div class="generic_layout_container layout_upcoming"><h3>Upcoming events</h3>
	<?php } ?>

	<style type="text/css">
		.betting_upcoming {
			padding: 15px;
			background-color: #ffffff;
		}
		#global_content .betting_upcoming_sport_title {
			font-family: fc_bebas;
			border-top-left-radius: 5px;
			border-top-right-radius: 5px;
			background-color: #cd4849;
			font-size: 13px;
			color: #ffffff;
			padding: .4em .7em;
			word-spacing: 0.4em;
		}
		#global_content .betting_upcoming_sport {
			margin-bottom: 15px;
		}
		#global_content .betting_upcoming_sport:last-child {
			margin-bottom: 0;
		}
		#global_content .betting_event {
			cursor: pointer;
			border: 1px solid #cccccc;
			border-top: none;
			position: relative;
			overflow: visible;
		}
		.betting_event:hover {
			background-color: #f5f5f5;
		}
		.betting_event_title {
			padding: 7px 0;
			cursor: pointer;
			font-family: fc_pts;
			font-weight: bold;
			font-size: 13px;
			background-color: transparent;
		}
		#global_content .betting_event_title div {
			font-family: fc_pts;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			width: 100%;
		}
		.betting_event_last {
			border-bottom-left-radius: 5px;
			border-bottom-right-radius: 5px;
			margin-bottom: 15px;
		}
		#global_content .betting_event_focused .betting_event_title {
			color: #0000ff;
			background-color: #b9dbe5;
		}
		.betting_selection {
			margin-left: 30px;
		}
		.betting_selection:hover {
			text-decoration: underline;
			cursor: pointer;
		}
		.betting_outcoming_list {
			display: none;
			position: absolute;
			z-index: 50;
			right: 0;
			top: 0;
			width: 50%;
			left: 50%;
			background: #b9dbe5;
			padding: 0 10px;
		}
		.betting_outcoming {
			margin: 10px 0;
		}
		.betting_outcoming_title {
			text-align: right;
			font-weight: bold;
			font-family: fc_pts;
			padding: 7px 0;
			cursor: pointer;
		}
		#global_page_core-pages-betting .betting_outcoming_focused .betting_outcoming_title {
			color: #0000ff;
		}

		.betting_upcoming .overlay {
			display: none;
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: 999999;
		}
		.betting_upcoming_loading .overlay {
			display: block;
		}
	</style>

	<div class="betting_upcoming">
		<div class="overlay"></div>
		<?php foreach ($this->upcoming as $sport => $eventsList) { ?>

		<!-- SPORT -->
		<div class="betting_upcoming_sport_title"><?= $sport ?></div>
		<div class="betting_upcoming_sport">
			<?php
			$nr = 0;
			$last = count($eventsList) - 1;
			?>
			<?php foreach ($eventsList as $event) { ?>
			<?php if (!$event->visible()) continue; ?>

			<!-- EVENT -->
			<div class="betting_event <?= ($nr++ == $last) ? 'betting_event_last' : '' ?>" id="betting_event_<?= $event->id ?>">
				<div class="betting_event_title" style="float: left; text-align: left; width: 30%"><?=date('D, M jS', strtotime($event->ts))?></div>
				<div class="betting_event_title" style="float: left; text-align: right; width: 70%"><div style="padding-right: 8px; display: inline-block"><?= '#' . $event->id . ' ' . $event->name ?></div></div>
				<div class="clear"></div>
				<div class="betting_outcoming_list">
					<div class="betting_outcoming_title"><?= '#' . $event->id . ' ' . $event->name ?></div>
					<?php foreach ($event->getChildEvents() as $subEvent) { ?>
					<?php if (!$subEvent->visible()) continue; ?>
					<div class="betting_outcoming">
						<?= "{$subEvent->name} @ {$subEvent->ts}" ?>:
						<?php foreach ($subEvent->getChildSelections() as $sel) { /* ?>
						<div class="betting_selection">
							<span class="betting_selection_id"><?= $sel->id ?></span> -
							<b><?= $sel->name ?></b> (<?= round($sel->odds, 2) ?> : 1)
						</div>
						<?php */ } ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<!-- /EVENT -->

			<?php } ?>
		</div>
		<!-- /SPORT -->

		<?php } ?>
	</div>

	<script type="text/javascript">
		setTimeout(function () {
		var events = <?php
		$data = array();
		foreach ($this->upcoming as $sport => $events) {
		foreach ($events as $event) {
		$children = array();
		foreach ($event->getChildEvents() as $subEvent) {
		$selections = array();
		foreach ($subEvent->getChildSelections() as $sel) {
		$selections[] = $sel->dbFields();
	}
	$children[] = array_merge($subEvent->dbFields(), array('selections' => $selections));
}
$data[] = array_merge($event->dbFields(), array('childEvents' => $children));
}
}
echo json_encode($data);
?>;
if (!window.fc) window.fc = {};
if (window.fc.betting_events) {
for (var i in window.fc.betting_events) {
window.fc.betting_events[i].node && window.fc.betting_events[i].node.parentNode.removeChild(window.fc.betting_events[i].node);
}
}
window.fc.events = events;
var elements = window.fc.betting_events = {}
var j = jQuery;
for (var i = 0; i < events.length; ++i) {
(function (e) {
e.jNode = j('#betting_event_' + events[i].id + ' .betting_outcoming_list');
if (!e.jNode.length) {
return;
}
e.node = e.jNode[0];
e.style = e.node.style;
e.hovered = 0;
// hover betting_event
j('#betting_event_' + events[i].id).hover(
function () {
var pos = j(this).offset();
pos.width = j(this).width();
var width = pos.width * 0.7;
e.style.top = pos.top + 'px';
e.style.width = width + 'px';
e.style.left = (pos.left + pos.width - width - 17) + 'px';
++e.hovered;
e.style.display = 'block';
e.jNodeEvent = j(this);
j(this).addClass('betting_event_focused');
e.jNode.addClass('betting_outcoming_focused');
},
function () {
--e.hovered;
setTimeout(function () {
if (!e.hovered) {
e.style.display = 'none';
e.jNodeEvent.removeClass('betting_event_focused');
e.jNode.removeClass('betting_outcoming_focused');
}
}, 1);
}
);
// hover outcoming_list
e.jNode.hover(function () {
++e.hovered;
e.jNodeEvent.addClass('betting_event_focused');
e.jNode.addClass('betting_outcoming_focused');
},
function () {
--e.hovered;
setTimeout(function () {
if (!e.hovered) {
e.style.display = 'none';
e.jNodeEvent.removeClass('betting_event_focused');
e.jNode.removeClass('betting_outcoming_focused');
}
}, 1);
});
						
e.node.parentNode.removeChild(e.node);
e.node.className += ' betting_outcoming_list_moved betting_outcoming_' + events[i].id;
document.getElementById('global_page_core-pages-betting').appendChild(e.node);
						
})(elements[i] = {});
}
				
jQuery('.betting_selection').click(function (e) {
var name = jQuery(e.currentTarget).find('b')[0].innerHTML;
var amount = parseFloat(prompt('How many points do you want to bet on ' + name + '?'));
if (!amount) {
return;
}
if (amount < 1) {
alert('Invalid number of points (must be >=1)');
return;
}
var id = parseInt(jQuery(e.currentTarget).find('.betting_selection_id')[0].innerHTML);
jQuery('.betting_upcoming').css({opacity: 0.5}).addClass('betting_upcoming_loading');
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
},
complete: function () { jQuery('.betting_upcoming').css({opacity: 1}).removeClass('betting_upcoming_loading'); }
});
});

}, 1);
	</script>

	<?php if (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'html')) { ?>
</div>
<?php } ?>

