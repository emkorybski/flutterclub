<div class="page-inviter-page-block">
     <table width="100%">
       <tbody>
        <tr valign="top">
               <td width="140" align="center">
                   <?php echo $this->htmlLink($this->page->getHref(), $this->itemPhoto($this->page, 'thumb.profile', '', array('width' => 135))); ?>
                   <div style="margin-top: 5px; text-align: center;">
                       <?php if ( $this->page->isStore()) : ?>
                           <img class="icon" src="application/modules/Page/externals/images/store.png" title="<?php echo $this->translate('Store'); ?>">
                       <?php endif; ?>
                       <?php if ($this->page->sponsored) : ?>
                           <img class="icon" src="application/modules/Page/externals/images/sponsored.png" title="<?php echo $this->translate('Sponsored'); ?>">
                       <?php endif; ?>
                       <?php if ($this->page->featured) : ?>
                           <img class="icon" src="application/modules/Page/externals/images/featured.png" title="<?php echo $this->translate('Featured'); ?>">
                       <?php endif; ?>
                   </div>
                </td>
               <td width="20"></td>
                <td>
                    <div class="page_list_title"><a href="<?php echo $this->page->getHref(); ?>"><?php echo $this->page->getTitle(); ?></a></div>
                    <div class="page-inviter-list-info">
                        <div class="r">
                              <?php if ($this->page->country || $this->page->city || $this->page->state): ?>
                                <div class="page_list_address"><?php echo $this->page->displayAddress(); ?></div>
                              <?php endif; ?>
                              <?php if ($this->page->phone): ?>
                                <div class="page_list_phone"><?php echo $this->page->phone; ?></div>
                              <?php endif; ?>
                              <?php if ($this->page->website): ?>
                                <div class="page_list_website">
                                  <?php echo $this->page->getWebsite(); ?>
                                </div>
                              <?php endif; ?>
                        <div class="clr"></div>
                    </div>
                    <div class="clr"></div>
                    <div class="page_list_desc">
                        <?php
//                            echo $this->page->getDescription(true, true, false, 300);
                            echo Engine_Api::_()->getApi('core', 'hecore')->truncate($this->page->description, 300);
                        ?>
                    </div>
                    <div class="clr"></div>
            </td>
         </tr>
       </tbody>
     </table>
</div>