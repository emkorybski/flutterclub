<?php if (count($this->navigation)): ?>
<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render()?>
</div>
<?php endif; ?>

<br/>

<h2><?php echo $this->translate("hecore_Featured Members") ?></h2><br/>

<script type="text/javascript">

    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';

    var changeOrder = function (order, default_direction) {
        if (order == currentOrder) {
            $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }
        $('filter_form').submit();
    }

    function selectAll() {
        var i;
        var multimodify_form = $('multimodify_form');
        var inputs = multimodify_form.elements;
        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>

<br/>

<div class='admin_results'>
    <div>
        <?php $memberCount = $this->paginator->getTotalItemCount() ?>
        <?php echo $this->translate(array("%s member found", "%s members found", $memberCount), ($memberCount)) ?>
    </div>
    <div>
        <?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->filters)); ?>
    </div>
</div>

<br/>

<div class="admin_table_form">
    <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action' => 'multi-modify'));?>">
        <table class='admin_table'>
            <thead>
            <tr>
                <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                <th style='width: 1%;'><a href="javascript:void(0);"
                                          onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a>
                </th>
                <th><a href="javascript:void(0);"
                       onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a>
                </th>
                <th><a href="javascript:void(0);"
                       onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a>
                </th>
                <th style='width: 1%;'><a href="javascript:void(0);"
                                          onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a>
                </th>
                <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);"
                                                                       onclick="javascript:changeOrder('level_id', 'ASC');"><?php echo $this->translate("User Level") ?></a>
                </th>
                <th style='width: 1%;'><a href="javascript:void(0);"
                                          onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Signup Date") ?></a>
                </th>
                <th style='width: 1%;'
                    class='admin_table_centered'><?php echo $this->translate("hecore_Featured") ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($this->paginator)): ?>
                <?php foreach ($this->paginator as $item): ?>
                <tr>
                    <td><input name='modify_<?php echo $item->getIdentity();?>'
                               value=<?php echo $item->getIdentity();?> type='checkbox' class='checkbox'>
                    </td>
                    <td><?php echo $item->user_id ?></td>
                    <td class='admin_table_bold'>
                        <?php
                        $display_name = $this->item('user', $item->user_id)->getTitle();
                        $display_name = Engine_String::strlen($display_name) > 10 ? Engine_String::substr($display_name, 0, 10) . '...' : $display_name;
                        echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $display_name, array('target' => '_blank'))
                        ?>
                    </td>
                    <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
                    <td class='admin_table_email'>
                        <?php if (!$this->hideEmails): ?>
                        <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
                        <?php else: ?>
                        (hidden)
                        <?php endif; ?>
                    </td>
                    <td class='admin_table_centered'>
                        <a href='<?php echo $this->url(array('module' => 'authorization', 'controller' => 'level', 'action' => 'edit', 'id' => $item->level_id)) ?>'>
                            <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $item->level_id)->getTitle()) ?>
                        </a>
                    </td>
                    <td><?php echo $item->creation_date ?></td>
                    <td class='admin_table_centered'>

                        <?php

                        $is_featured = (bool)$item->featured_id;

                        if ($is_featured) {

                            $href = $this->url(array(
                                'module' => 'hecore',
                                'controller' => 'featureds',
                                'action' => 'modify',
                                'user_id' => $item->getIdentity()
                            ));

                            $img = "application/modules/Hecore/externals/images/featured_icon.png";
                            $title = $this->translate('hecore_UnFeatured');

                        } else {

                            $href = $this->url(array(
                                'module' => 'hecore',
                                'controller' => 'featureds',
                                'action' => 'modify',
                                'user_id' => $item->getIdentity(),
                                'set_featured' => 1
                            ));

                            $img = "application/modules/Hecore/externals/images/featuredoff_icon.png";
                            $title = $this->translate('hecore_Featured');

                        }
                        if ($this->hideEmails) {
                            $href = "javascript:void(0);";
                        }
                        echo "<a href='{$href}' title='{$title}'><img src='{$img}' alt='{$title}'/></a>";

                        ?>

                    </td>
                </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <br/>

        <div class='buttons'>

            <button type='submit' name="submit_button"
                    value="set_featured"><?php echo $this->translate("hecore_Featured Selected") ?></button>

            <button type='submit' name="submit_button"
                    value="unset_featured"><?php echo $this->translate("hecore_UnFeatured Selected") ?></button>

        </div>

    </form>
</div>

