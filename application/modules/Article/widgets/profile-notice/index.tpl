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
    <div class="tip">
      <span>
        <?php if ($this->is_owner): ?>
	        <?php if ($this->approval): ?>
	          <?php echo $this->translate('This article is in Draft mode. Administrator will review and manually publish it.'); ?>
	        <?php else: ?>
	          <?php echo $this->translate('No one will be able to view this article until you <a href=\'%1$s\'>publish</a> it.', $this->url(array('article_id' => $this->article->article_id), 'article_publish', true)); ?>
	        <?php endif; ?>
        <?php else: ?>
          <?php echo $this->translate('This article has not been published yet.')?>
        <?php endif; ?>
      </span>
    </div>  



