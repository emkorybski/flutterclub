<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>

<?php
$this->headLink()->appendStylesheet($this->baseUrl() . '/application/css.php?request=application/modules/Inviter/externals/styles/main.css');
?>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<h2>
    <?php echo $this->translate("INVITER_Manage Providers") ?>
</h2>

<p>
    <?php echo $this->translate("INVITER_MANAGE_PROVIDERS_DESC") ?>
</p>

<br/>
<br/>


<script type="text/javascript">
    en4.core.runonce.add(function () {
        $$('a.admin_enable_provider_btn').addEvent('click', function () {
            var $node = this;
            var provider_id = $(this).getProperty('id').substr(16);
            en4.core.request.send(new Request.JSON({
                url:'<?php echo $this->url(array('module' => 'inviter', 'controller' => 'settings', 'action' => 'enable-provider'), 'admin_default'); ?>',
                data:{
                    format:'json',
                    provider_id:provider_id
                },
                onSuccess:function (response) {
                    if (response.status) {
                        $node.getParent('td').toggleClass('admin_inviter_provider_disabled');
                        if (response.message) {
                            $node.set('text', '<?php echo $this->translate('INVITER_disable'); ?>');
                        } else {
                            $node.set('text', '<?php echo $this->translate('INVITER_enable'); ?>');
                        }
                    }
                }
            }));
        });

        $$('a.admin_show_provider_btn').addEvent('click', function () {
            var $node = this;
            var provider_id = $(this).getProperty('id').substr(14);
            en4.core.request.send(new Request.JSON({
                url:'<?php echo $this->url(array('module' => 'inviter', 'controller' => 'settings', 'action' => 'show-provider'), 'admin_default'); ?>',
                data:{
                    format:'json',
                    provider_id:provider_id
                },
                onSuccess:function (response) {
                    if (response.status) {
                        $node.getParent('td').toggleClass('admin_inviter_provider_shown');
                        if (response.message) {
                            $node.set('text', '<?php echo $this->translate('INVITER_hide'); ?>');
                        } else {
                            $node.set('text', '<?php echo $this->translate('INVITER_show'); ?>');
                        }
                    }
                }
            }));
        });
    });
</script>

<div class="admin_table_form">
    <table class='admin_table' width="50%">
        <thead>
        <tr>
            <th width='60%' class='admin_table_centered'><?php echo $this->translate("INVITER_Provider") ?></th>
            <th width='20%' class='admin_table_centered'><?php echo $this->translate("INVITER_Enabled") ?></th>
            <th width='20%' class='admin_table_centered'><?php echo $this->translate("INVITER_Show on widget") ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($this->providers)): ?>
            <?php foreach ($this->providers as $item): if ($item->isopeninviter) continue; ?>
            <tr>
                <td class='admin_table_bold'><?php echo $item->provider_title; ?></td>
                <td class='admin_table_centered <?php echo ($item->provider_enabled) ? '' : 'admin_inviter_provider_disabled'; ?>'>
                    <a class="admin_enable_provider_btn" id="enable_provider_<?php echo $item->provider_id; ?>"
                       href="javascript://">
                        <?php echo ($item->provider_enabled) ? $this->translate('INVITER_disable') : $this->translate('INVITER_enable'); ?>
                    </a>
                </td>
                <td class='admin_table_centered <?php echo ($item->provider_default) ? 'admin_inviter_provider_shown' : ''; ?>'>
                    <a class="admin_show_provider_btn" id="show_provider_<?php echo $item->provider_id; ?>"
                       href="javascript://">
                        <?php echo ($item->provider_default) ? $this->translate('INVITER_hide') : $this->translate('INVITER_show'); ?>
                    </a>
                </td>
            </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <br/>

    <h2>
        <?php echo $this->translate('INVITER_Deprecated providers'); ?>
    </h2>

    <p>
        <?php echo $this->translate('INVITER_Deprecated providers description'); ?>
    </p>
    <br/><br/>
    <table class='admin_table_deprecated' width="50%">
        <tr>
            <td width='60%' class='header'><?php echo $this->translate("INVITER_Provider") ?></td>
            <td width='20%' class='header'><?php echo $this->translate("INVITER_Enabled") ?></td>
            <td width='20%' class='header'><?php echo $this->translate("INVITER_Show on widget") ?></td>
        </tr>
        <?php if (count($this->providers)): ?>
        <?php foreach ($this->providers as $item): if (!$item->isopeninviter) continue; ?>
            <tr>
                <td class='title gray'><?php echo $item->provider_title; ?></td>
                <td class='<?php echo ($item->provider_enabled) ? '' : 'admin_inviter_provider_disabled'; ?>'>
                    <a class="admin_enable_provider_btn" id="enable_provider_<?php echo $item->provider_id; ?>"
                       href="javascript://">
                        <?php echo ($item->provider_enabled) ? $this->translate('INVITER_disable') : $this->translate('INVITER_enable'); ?>
                    </a>
                </td>
                <td class='<?php echo ($item->provider_default) ? 'admin_inviter_provider_shown' : ''; ?>'>
                    <a class="admin_show_provider_btn" id="show_provider_<?php echo $item->provider_id; ?>"
                       href="javascript://">
                        <?php echo ($item->provider_default) ? $this->translate('INVITER_hide') : $this->translate('INVITER_show'); ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>