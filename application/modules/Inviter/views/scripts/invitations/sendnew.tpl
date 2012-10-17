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

<script type='text/javascript'>

en4.core.runonce.add(function()
{
	if ($('email_box')){
		$('email_box').value = "<?php echo $this->invitation['sender']; ?>";
	}
});
</script>
<div style='width: 320px'>
	<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
</div>