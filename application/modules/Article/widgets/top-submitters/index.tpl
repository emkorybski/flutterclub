<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<?php if (count($this->submitters)): ?>

  <?php 
    // speed this up :-)
    Engine_Api::_()->user()->getUserMulti(array_keys($this->submitters));
  ?>

  <ul class='article_top_submitters'>
    <?php foreach ($this->submitters as $submitter): ?>
      <?php $user = $this->user($submitter['user_id']); if (!$user->getIdentity()) continue; ?>
      <li>
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class'=>'article_submitter_photo')); ?>
        <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('class'=>'article_submitter_title'))?>
        <div class="article_submitter_meta">
          <span class="article_submitter_total">
            <?php $total = $this->translate(array('%d article', '%d articles', $submitter['total']), $this->locale()->toNumber($submitter['total']))?>
            <?php echo $this->htmlLink(array('route'=>'article_general', 'action'=>'browse', 'user'=> $user->getIdentity()), $total)?>
          </span>
        </div>  
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no submitters yet.');?>
    </span>
  </div>
<?php endif; ?>
