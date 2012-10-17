<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        $('inviter_password_box').value = 'password...';
    });
</script>
<?php echo $this->render('_providers_settings.tpl'); ?>
<div class='layout_middle'>

    <div class='inviter-forms-conteiner global_form'>
        <div class="backlink_wrapper" style="margin-bottom: 5px;">
            <a class="backlink"
               href="<?php echo $this->page->getHref(); ?>"><?php echo $this->translate('PAGE_INVITER_Back to Page'); ?></a>
        </div>
        <br>

        <div>
            <div>

                <?php echo $this->render('_pageTitle.tpl'); ?>

                <div class='inviter-form-cont inviter-form-bg' id='inviter-importer-conteiner'>
                    <div id='inviter-importer-title' class="inviter-tab-title inviter-import-title"
                         onclick="if ($(this).hasClass('inviter-form-title')){tab_slider('importer');}"
                         onmouseover="if ($(this).hasClass('inviter-form-title')){$('inviter-importer-conteiner').addClass('inviter-form-hover')}"
                         onmouseout="if ($(this).hasClass('inviter-form-title')){$('inviter-importer-conteiner').removeClass('inviter-form-hover')}">
                        <h3 style="padding: 20px;"><?php echo $this->translate('PAGE_INVITER_Import Your Contacts')?></h3>
                    </div>
                    <div class='inviter-form' id='inviter-importer-form'> <?php echo $this->form->render($this)?> </div>
                </div>

                <?php if ($this->viewer->getIdentity()): ?>
                <div class='inviter-form-conteiner inviter-form-cont' id='inviter-writer-conteiner'>
                    <div id='inviter-writer-title' class='inviter-tab-title inviter-write-title inviter-form-title'
                         onclick="if ($(this).hasClass('inviter-form-title')){tab_slider('writer');}"
                         onmouseover="if ($(this).hasClass('inviter-form-title')){$('inviter-writer-conteiner').addClass('inviter-form-hover')}"
                         onmouseout="if ($(this).hasClass('inviter-form-title')){$('inviter-writer-conteiner').removeClass('inviter-form-hover')}">
                        <h3 style="padding: 20px;"><?php echo $this->translate('PAGE_INVITER_Write Your Contacts')?></h3>
                    </div>
                    <div class='inviter-form'
                         id='inviter-writer-form'> <?php echo $this->form_write->render($this)?> </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>


</div>

<div id='default_provider_list' style='display:none;'></div>