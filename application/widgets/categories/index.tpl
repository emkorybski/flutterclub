<style type="text/css">
	.betting_category {
		cursor: pointer;
		padding: 5px;
	}
	.betting_category:hover {
		background-color: #e5e5e5;
	}
</style>

<div class="betting_categories">
	<div class="betting_category">
		<input type="hidden" value="0" />
		All
	</div>
	<hr style="border-bottom: 1px solid #cccccc" />
	<?php foreach ($this->categories as $sport) { ?>
		<div class="betting_category">
			<input type="hidden" value="<?=$sport->id?>" />
			<?=$sport->name?>
		</div>
		<hr style="border-bottom: 1px solid #cccccc" />
	<?php } ?>
</div>

<xscript type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js"></xscript>

<script type="text/javascript">
	function loadCateg(idCateg) {
		var fill = $('.layout_upcoming')[0];
		$.ajax('/fc/widget/index/name/upcoming?format=html&idsport=' + encodeURIComponent(idCateg), {
			success: function (text) { fill.innerHTML = text; }
		});
	}
	$$('.betting_category').each(function (categ) {
		$(categ).click(function () {
			loadCateg($(categ).find('input')[0].value);
		});
	});
</script>

<style type="text/css">
  .layout_categories > div
  {
    text-align: center;
    padding: 15px;
    margin-bottom: 15px;
  }
  .layout_categories > div > span
  {
    display: block;
    font-size: 1.4em;
  }
</style>

