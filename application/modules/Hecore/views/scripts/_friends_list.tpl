<?php $counter = 0; ?>
<?php foreach ($this->friends as $friend): ?>
<?php if ($counter % 3 == 0): ?><div class="row"><?php endif; ?>
  <?php $counter++; ?>
  <div class="he_item">
    <div class="photo"><?php echo $this->htmlLink($friend->getHref(), $this->itemPhoto($friend, 'thumb.icon')); ?></div>
    <div class="clr"></div>
    <div class="title"><?php echo $this->htmlLink($friend->getHref(), $friend->getTitle()); ?></div>
  </div>
<?php if ($counter % 3 == 0 || $counter == $this->friends->getItemCountPerPage() || $counter == $this->friends->getTotalItemCount()): ?></div><div class="clr"></div><?php endif; ?>
<?php  endforeach; ?>