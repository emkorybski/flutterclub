
<style type="text/css">
	.fc_admin {
		padding-top: 0;
		padding: 15px;
		border: 1px solid #ff6600;
	}
	.fc_admin h3 {
		margin: 15px 0;
	}
	.fc_admin_box {
		padding: 5px;
		border: 1px solid #cd4849;
	}
	.fc_admin button {
		padding: 5px;
		margin: 5px;
	}
	.fc_admin_sports_sport {
		width: 24%;
		float: left;
		padding: 5px;
	}
	#fc_admin_sports_set {
		padding: 5px;
		margin: 5px;
	}
	.fc_admin_sports_sport label:hover {
		color: #3aaacf;
		font-weight: bold;
	}
</style>
<h3>Enabled sports</h3>
<div class="fc_admin_box fc_admin_sports">
<?php
	foreach ($this->sports as $sport) :
?>
	<div class="fc_admin_sports_sport">
		<input type="checkbox" name="cb_sport_<?=$sport->id?>" id="cb_sport_<?=$sport->id?>" value="<?=$sport->id?>" <?= $sport->enabled == 'y' ? 'checked="checked"' : '' ?> />
		<label for="cb_sport_<?=$sport->id?>">&nbsp;<?=$sport->name?></label>
	</div>
<?php
	endforeach;
?>
	<div class="clear"></div>
	<button id="fc_admin_sports_set">Apply</button>
</div>
<script type="text/javascript">
	j('#fc_admin_sports_set').click(function () {
		var sportIds = [];
		j('.fc_admin_sports input[type="checkbox"]').each(function (index, input) {
			if (input.checked) sportIds.push(input.value);
		});
		j.ajax(WEB_ROOT + 'widget?name=fc_admin&action=sports_toggle', {
			data: {sportIds:sportIds},
			success:function () {
				alert('Enabled sports list updated successfully.');
			},
			error:function () {
				alert('Something went wrong!');
			}
		});
	});
</script>

<style type="text/css">
	.fc_admin_competitions_comp {
		width: 32%;
		float: left;
		padding: 5px;
	}
	.fc_admin_competitions_comp label:hover {
		color: #3aaacf;
		font-weight: bold;
	}
	.fc_admin_competitions label {
		display: inline-block;
		cursor: pointer;
	}
	.fc_admin_competitions .comp_start, .fc_admin_competitions .comp_end {
		width: 18ex
	}
</style>
<h3>Competitions</h3>
<div class="fc_admin_competitions fc_admin_box">
	<?php
		foreach ($this->comps as $comp) { ?>
	<div class="fc_admin_competitions_comp">
		<div class="box_readonly">
			<input type="hidden" class="comp_id" value="<?=$comp->id?>"/>
			Name: <label class="comp_name"><?=htmlentities($comp->name)?></label><br/>
			Period: <span class="comp_start comp_ts"><?=substr($comp->ts_start, 0, 10)?></span> ~ <span
				class="comp_end comp_ts"><?=substr($comp->ts_end, 0, 10)?></span>
		</div>
		<div class="box_edit" style="display: none">
			Name: <input class="comp_name" type="text" name="competition_<?=$comp->id?>"
						 value="<?=htmlentities($comp->name)?>"/><br/>
			Period: <input type="text" class="comp_start" value="<?=substr($comp->ts_start, 0, 10)?>"/> ~ <input
				type="text" class="comp_end" value="<?=substr($comp->ts_end, 0, 10)?>"/><br/>
			<button class="edit_ok">Update</button>
			<a href="<?=WEB_ROOT?>widget/index/name/fc_admin?action=compEvents&idcompetition=<?=$comp->id?>"
			   class="smoothbox edit_events ">Events</a>
			<a href="#" class="edit_delete">Delete</a>
			<a href="#" class="edit_cancel">Cancel</a>
		</div>
	</div>
	<?php
		} ?>
	<div class="clear"></div>
	<div class="fc_admin_competitions_comp box_add" style="display: none">
		Name: <input class="comp_name" type="text" value=""/><br/>
		Period: <input type="text" class="comp_start" value=""/> ~ <input type="text" class="comp_end" value=""/><br/>
		<button class="edit_ok">Apply</button>
		<a href="#" class="edit_cancel">Cancel</a>
	</div>
	<div class="clear"></div>
	<button id="fc_admin_competitions_set">Apply</button>
	<button id="fc_admin_competitions_add" class="add_button">Add</button>
</div>
<script type="text/javascript">
	setTimeout(function () {
		var j = jQuery;

		var calendar = new dhtmlXCalendarObject();
		calendar.customDateFormat = '%Y-%m-%d';
		calendar.setDateFormat(calendar.customDateFormat);

		j('.box_readonly .comp_name').click(function (e) {
			e.stopPropagation();
			e.preventDefault();
			var edit = j(this.parentNode.parentNode).find('.box_edit')[0];
			this.parentNode.style.display = 'none';
			edit.style.display = 'block';

			var ts_start = j(edit).find('.comp_start')[0];
			var ts_end = j(edit).find('.comp_end')[0];
			ts_start.onclick = function () {
				calendar.i = {'item': ts_start};
				setTimeout(function () {
					calendar._show('item');
				}, 1);
			}
			ts_end.onclick = function () {
				calendar.i = {'item': ts_end};
				setTimeout(function () {
					calendar._show('item');
				}, 1);
			}
		});
		j('.box_edit .edit_ok').click(function (e) {
			e.stopPropagation();
			e.preventDefault();
			this.parentNode.style.display = 'none';
			var readonly = j(this.parentNode.parentNode).find('.box_readonly');
			readonly[0].style.display = 'block';

			readonly.find('comp_name').html(j(this.parentNode).find('.comp_name').val());
			readonly.find('.comp_start').html(j(this.parentNode).find('.comp_start').val());
			readonly.find('.comp_end').html(j(this.parentNode).find('.comp_end').val());

			j.ajax(WEB_ROOT + 'widget?name=fc_admin&action=compUpdate', {
				data:{
					comp_id:readonly.find('.comp_id').val(),
					comp_name:j(this.parentNode).find('.comp_name').val(),
					comp_start:j(this.parentNode).find('.comp_start').val(),
					comp_end:j(this.parentNode).find('.comp_end').val()
				},
				success:function (text) {
					text.length || alert('Internal error')
				},
				failure:function () {
					alert('Internal error')
				}
			});
		});
		j('.box_edit .edit_delete').click(function (e) {
			e.stopPropagation();
			e.preventDefault();
			if (!confirm('Are you sure you want to delete this competition?')) {
				return;
			}

			j.ajax(WEB_ROOT + 'widget?name=fc_admin&action=compDelete', {
				data:{
					comp_id:j(this.parentNode.parentNode).find('.box_readonly .comp_id').val()
				},
				success:function (text) {
					(text.length && (location.reload() || 1)) || alert('Internal error')
				},
				failure:function () {
					alert('Internal error')
				}
			});
		});
		j('.box_edit .edit_cancel').click(function (e) {
			e.stopPropagation();
			e.preventDefault();
			var readonly = j(this.parentNode.parentNode).find('.box_readonly');
			this.parentNode.style.display = 'none';
			readonly[0].style.display = 'block';

			j(this.parentNode).find('.comp_name').val(readonly.find('.comp_name').html());
			j(this.parentNode).find('.comp_start').val(readonly.find('.comp_start').html());
			j(this.parentNode).find('.comp_end').val(readonly.find('.comp_end').html());
		});

		j('.add_button').click(function (e) {
			e.stopPropagation();
			e.preventDefault();
			j(this.parentNode).find('.box_add')[0].style.display = 'block';
			this.style.display = 'none';

			var ts_start = j(this.parentNode).find('.box_add .comp_start')[0];
			var ts_end = j(this.parentNode).find('.box_add .comp_end')[0];
			ts_start.onclick = function () {
				calendar.i = {'item': ts_start};
				setTimeout(function () {
					calendar._show('item');
				}, 1);
			}
			ts_end.onclick = function () {
				calendar.i = {'item': ts_end};
				setTimeout(function () {
					calendar._show('item');
				}, 1);
			}
		});
		j('.box_add .edit_ok').click(function (e) {
			e.stopPropagation();
			e.preventDefault();
			this.parentNode.style.display = 'none';
			var readonly = j(this.parentNode.parentNode).find('.box_readonly');
			readonly[0].style.display = 'block';

			readonly.find('comp_name').html(j(this.parentNode).find('.comp_name').val());
			readonly.find('.comp_start').html(j(this.parentNode).find('.comp_start').val());
			readonly.find('.comp_end').html(j(this.parentNode).find('.comp_end').val());

			j.ajax(WEB_ROOT + 'widget?name=fc_admin&action=compAdd', {
				data:{
					comp_name:j(this.parentNode).find('.comp_name').val(),
					comp_start:j(this.parentNode).find('.comp_start').val(),
					comp_end:j(this.parentNode).find('.comp_end').val()
				},
				success:function (text) {
					(text.length && (location.reload() || 1)) || alert('Internal error')
				},
				failure:function () {
					alert('Internal error')
				}
			});
		});
		j('.box_add .edit_cancel').click(function (e) {
			e.stopPropagation();
			e.preventDefault();
			this.parentNode.style.display = 'none';
			j(this.parentNode.parentNode).find('.add_button')[0].style.display = '';
		});
	}, 1);
</script>
