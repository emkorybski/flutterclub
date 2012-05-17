<div class="generic_layout_container fc_admin">

	<h3>Enabled sports</h3>

	<style type="text/css">
		#global_wrapper #global_content .fc_admin h3 {
			font-family: fc_bebas;
			border-top-left-radius: 5px;
			border-top-right-radius: 5px;
			background-color: #cd4849;
			font-size: 13px;
			color: #ffffff;
			padding: .4em .7em;
			word-spacing: 0.4em;
		}
		.fc_admin {
			padding: 15px;
			border: 1px solid #ff6600;
		}
		.fc_admin_sports {
			padding: 5px;
			border: 1px solid #cd4849;
		}
		.fc_admin_sports_sport {
			width: 21%;
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

	<div class="fc_admin_sports">
		<?php
		foreach ($this->sports as $sport) {
		if ($sport->visible()) {
		?>
		<div class="fc_admin_sports_sport">
			<input type="checkbox" name="cb_sport_<?=$sport->id?>" id="cb_sport_<?=$sport->id?>" value="<?=$sport->id?>" <?= $sport->enabled ? 'checked="checked"' : '' ?> />
				   <label for="cb_sport_<?=$sport->id?>">&nbsp;<?=$sport->name?></label>
		</div>
		<?php
		}
		}
		?>
		<div class="clear"></div>
		<button id="fc_admin_sports_set">Apply</button>
	</div>

	<script type="text/javascript">
		(function () {
			var j = jQuery;
			j('#fc_admin_sports_set').click(function () {
				var sportsList = [];
				j('.fc_admin_sports input[type="checkbox"]').each(function (nr, input) {
					if (input.checked) {
						sportsList.push(input.value);
					}
				});
				console.log(sportsList);
				j.ajax('/fc/widget/index/name/fc_admin?action=setSports', {
					data: {'sports': sportsList},
					success: function () {
						alert('Enabled sports list updated successfully.');
					},
					error: function () {
						alert('Something went wrong!');
					}
				});
			});
		})();
	</script>
	
</div>

