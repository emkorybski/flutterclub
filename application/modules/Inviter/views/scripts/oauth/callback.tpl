<?php if ($this->contact_count > 0) : ?>

<?php if ($this->signup_page) : ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            var $parent = window.opener;
            var $form = $parent.$('invite_friends');
            var $form_submit = $form.getElement('#done');

            $parent.provider.force_submit = true;
            $form_submit.click();
            window.close();
        });
    </script>
    <?php else : ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            var $parent = window.opener;
            var $form = $parent.$('inviter-importer-form');

            if (!$form) {
                $parent.location.href = $parent.location.href;
                window.close();
                return;
            }

            var $form_submit = $form.getElement('#submit');

            if (!$form_submit) {
                $form_submit = $form.getElement('button.page-inviter-submit');
            }

            $parent.provider.force_submit = true;
            $form_submit.click();
            window.close();
        });
    </script>
    <?php endif; ?>

<?php else : ?>

<script type="text/javascript">
    en4.core.runonce.add(function () {
        var $parent = window.opener;
        $parent.he_show_message("There are no contacts.");
        window.close();
    });
</script>

<?php endif; ?>