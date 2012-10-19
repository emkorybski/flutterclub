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
<?php echo $this->render('application/modules/Inviter/views/scripts/_providers_settings.tpl'); ?>
<?php if ($this->success) : ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            var success = '<?php echo $this->success; ?>';
            var message = '<?php echo $this->message; ?>';
            if(success == '2')
                he_show_message(message, '', 5000);
            if(success == '1')
                he_show_message(message, 'error', 5000);
        });
    </script>
<?php endif; ?>

<div class='inviter_layout_middle'>

    <?php if (!$this->form->_signup && $this->viewer->getIdentity() && $this->navigation): ?>
    <div class="headline">
        <div class="tabs">
            <?php
            // Render the menu
            echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->render();
            ?>
        </div>
    </div>
    <?php endif; ?>

    <div class='inviter-forms-conteiner global_form'>
        <div>
            <div>
                <?php if ($this->count > 0): ?>
                <div class='inviter-form-cont inviter-form-bg' id='inviter-importer-conteiner'>
                    <div id='inviter-importer-title' class="inviter-tab-title inviter-import-title"
                         onclick="if ($(this).hasClass('inviter-form-title')){tab_slider('importer');}"
                         onmouseover="if ($(this).hasClass('inviter-form-title')){$('inviter-importer-conteiner').addClass('inviter-form-hover')}"
                         onmouseout="if ($(this).hasClass('inviter-form-title')){$('inviter-importer-conteiner').removeClass('inviter-form-hover')}">
                        <h3 style="padding: 20px;"><?php echo $this->translate('INVITER_Import Your Contacts')?></h3>
                    </div>
                    <div class='inviter-form' id='inviter-importer-form'> <?php echo $this->form->render($this)?> </div>
                </div>
                <?php else: ?>
                <div class="tip">
                    <span><?php echo $this->translate('INVITER_No providers'); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($this->viewer->getIdentity()): ?>
                <div class='inviter-form-conteiner inviter-form-cont' id='inviter-uploader-conteiner'>
                    <div id='inviter-uploader-title' class='inviter-tab-title inviter-upload-title inviter-form-title'
                         onclick="if ($(this).hasClass('inviter-form-title')){tab_slider('uploader');}"
                         onmouseover="if ($(this).hasClass('inviter-form-title')){$('inviter-uploader-conteiner').addClass('inviter-form-hover')}"
                         onmouseout="if ($(this).hasClass('inviter-form-title')){$('inviter-uploader-conteiner').removeClass('inviter-form-hover')}">
                        <h3 style="padding: 20px;"><?php echo $this->translate('INVITER_Upload Your Contacts')?></h3>
                    </div>
                    <div class='inviter-form'
                         id='inviter-uploader-form'> <?php echo $this->form_upload->render($this)?> </div>
                </div>
                <div class='inviter-form-conteiner inviter-form-cont' id='inviter-writer-conteiner'>
                    <div id='inviter-writer-title' class='inviter-tab-title inviter-write-title inviter-form-title'
                         onclick="if ($(this).hasClass('inviter-form-title')){tab_slider('writer');}"
                         onmouseover="if ($(this).hasClass('inviter-form-title')){$('inviter-writer-conteiner').addClass('inviter-form-hover')}"
                         onmouseout="if ($(this).hasClass('inviter-form-title')){$('inviter-writer-conteiner').removeClass('inviter-form-hover')}">
                        <h3 style="padding: 20px;"><?php echo $this->translate('INVITER_Write Your Contacts')?></h3>
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