<div id='profile_status'>
	<h2>
		<?php echo $this->subject()->getTitle() ?>
		<br>
	</h2>
</div>

<?php if( !$this->auth ): ?>
<div class="tip">
    <span>
      <?php echo $this->translate('This profile is private - only friends of this member may view it.');?>
    </span>
</div>
<br/>
<?php endif; ?>