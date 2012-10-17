<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */


$this->headTranslate(array('HECORE_UPDATE_TITLE', 'HECORE_UPDATE_BODY', 'HECORE_UPDATE_LICENSE'));

?>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render()?>
</div>
<?php endif; ?>
<br/>

<script type="text/javascript">

    var $flag_filter = 'new';
    function hecore_plugin_filter($value) {
        $$('.hecore-all').setStyle('display', 'none');
        $$('.hecore-' + $value).setStyle('display', 'block');

        if ($value != 'updated') {
            $flag_filter = $value;
        }
    }

    function hecore_show_tab($key, $type, $el) {
        if (!$el.hasClass('active-tab')) {
            $$('.' + $key + '-content').removeClass('active-tab-content');
            $($key + '-content-' + $type).addClass('active-tab-content');

            $$('.' + $key + '-tab').removeClass('active-tab');
            $el.addClass('active-tab');
            $el.blur();
        }
    }

    function hecore_show_content($menu, $el) {

        hecore_license.quit();

        $li = $el.getParent('li');
        if (!$li.hasClass('active-menu-tab')) {
            $$('.admin_home_news').fade('out');

            setTimeout(function () {
                $$('.sep').setStyle('display', 'none');

                if ($menu == 'updates') {
                    hecore_plugin_filter('updated');
                } else {
                    hecore_plugin_filter($flag_filter);
                }


                $('hecore-' + $menu + '-conteiner').setStyle('display', '');
                $$('.admin_home_news').fade('in');
            }, '500');


            $$('.active-menu-tab').removeClass('active-menu-tab');
            $li.addClass('active-menu-tab');
        }

        $el.blur();
    }

    var hecore_license =
    {
        url:'',
        direction:500,
        start:false,
        init:function () {
            var self = this;
            self.$el = $('hecore_admin_licenses');
            self.$loader = self.$el.getElement('.loader');
            self.$list = self.$el.getElement('.list');
            if (self.start) {
                self.getList();
            }
        },
        getList:function (html) {
            var self = this;
            if (!self.$el) {
                return;
            }

            $$('.admin_home_news').fade('out', {'direction':self.direction});
            setTimeout(function () {
                $$('.admin_home_news').setStyle('display', 'none');
                self.$el.setStyle('display', 'block');
                self.$el.fade('in');
            }, self.direction);

            $$('ul.admin_home_dashboard_links .hecore-menu-tab')
                .removeClass('active-menu-tab');
            $$('ul.admin_home_dashboard_links .hecore-menu-tab.licenses')
                .addClass('active-menu-tab');

            self.$list.setProperty('html', '');

            if (html) {
                self.$list.setProperty('html', html);
            } else {
                this.request(this.url, {'format':'json'}, function (obj) {
                    if (obj.html) {
                        self.$list.setProperty('html', obj.html);
                    }
                });
            }
        },
        quit:function () {
            var self = this;

            self.$el.fade('out', {'direction':self.direction});
            setTimeout(function () {
                self.$el.setStyle('display', 'none');
                $$('.admin_home_news').setStyle('display', 'block');
                $$('.admin_home_news').fade('in');
            }, self.direction);

            $$('ul.admin_home_dashboard_links .hecore-menu-tab.licenses')
                .removeClass('active-menu-tab');
        },
        edit:function (form) {
            var self = this;
            var values = $(form).toQueryString() + '&format=json';
            self.$list.setProperty('html', '');
            this.request(this.url, values, function (obj) {
                if (obj.html) {
                    self.getList(obj.html);
                }
            });
        },
        showLoader:function () {
            this.$loader.setStyle('display', 'block');
        },
        hideLoader:function () {
            this.$loader.setStyle('display', 'none');
        },
        request:function (url, data, callback) {
            var self = this;
            this.showLoader();
            var request = new Request.JSON({
                secure:false,
                url:url,
                method:'post',
                data:data,
                onComplete:function (obj) {
                    self.hideLoader();
                    callback(obj);
                }
            }).send();
        }
    };

    function plugin_update(element) {
        var $link = $(element);
        he_show_confirm(
            en4.core.language.translate('HECORE_UPDATE_TITLE'),
            en4.core.language.translate('HECORE_UPDATE_BODY'),
            function () {
                window.location.href = $link.getProperty('href');
            }
        );
    }

    en4.core.runonce.add(function () {
        var myElements = $$('.hecore-plugin-item');
        var i = 0;
        for (i = 0; i < myElements.length; i++) {
            var plugin_key = myElements[i].getProperty('id');
            initImageZoom({rel:plugin_key});
        }

        hecore_license.url = '<?php echo $this->url(array('action' => 'licenses'))?>';
        hecore_license.start = <?php echo (int)$this->checkLicense?>;
        hecore_license.init();

    });

</script>

<div class="admin_home_wrapper">

<div class="admin_home_right" style="width:250px">
    <ul class="admin_home_dashboard_links">
        <li style="width:250px">
            <ul>

                <li class="hecore-menu-tab active-menu-tab">
                    <a href="javascript://" onclick="hecore_show_content('plugins', $(this))" class="hecore-menu-link">
                        <img src="<?php echo $this->baseUrl()?>/application/modules/Hecore/externals/images/plugin.png"
                             height="14px" style='vertical-align:top;margin-right:3px;'/>
                        <?php echo $this->translate('hecore_Plugins'); ?>
                    </a>
                </li>

                <?php if ($this->updated > 0): ?>
                <li class="hecore-menu-tab">
                    <a href="javascript://" onclick="hecore_show_content('updates', $(this))" class="hecore-menu-link">
                        <img src="<?php echo $this->baseUrl()?>/application/modules/Hecore/externals/images/update.png"
                             height="14px" style='vertical-align:top;margin-right:3px;'/>
                        <?php echo $this->translate('hecore_Plugin Updates') . ' (' . $this->updated . ')'; ?>
                    </a>
                </li>
                <?php endif; ?>

              <?php if(!$this->special_mode): ?>
                <li class="hecore-menu-tab licenses">
                    <a href="javascript://" onclick="hecore_license.getList();" class="hecore-menu-link">
                        <img src="<?php echo $this->baseUrl()?>/application/modules/Hecore/externals/images/license.png"
                             height="14px" style='vertical-align:top;margin-right:3px;'/>
                        <?php echo $this->translate('HECORE_LICENSES'); ?>
                    </a>
                </li>
              <?php endif; ?>

                <li>
                    <img src="<?php echo $this->baseUrl()?>/application/modules/Hecore/externals/images/news.png"
                         height="16px" style='vertical-align:top;margin-right:3px;'/>
                    <span
                        style="clear:inherit; color: #5BA1CD; font-weight:bold;"> <?php echo $this->translate('hecore_Hire-Experts News'); ?></span>
                    <script src="http://widgets.twimg.com/j/2/widget.js"></script>
                    <script>
                        new TWTR.Widget({
                            version:2,
                            type:'list',
                            rpp:5,
                            interval:6000,
                            title:'Hire-Experts',
                            subject:'Latest news',
                            width:'auto',
                            height:450,
                            theme:{
                                shell:{
                                    background:'#e9f4fa',
                                    color:'#717171'
                                },
                                tweets:{
                                    background:'#ffffff',
                                    color:'#444444',
                                    links:'#5ba1cd'
                                }
                            },
                            features:{
                                scrollbar:true,
                                loop:false,
                                live:true,
                                hashtags:true,
                                timestamp:true,
                                avatars:false,
                                behavior:'all'
                            }
                        }).render().setList('hireexperts', 'latest-news').start();
                    </script>
                </li>
            </ul>
        </li>
    </ul>
</div>

<div class="admin_home_middle">

<div id="hecore_admin_licenses" class="hecore_admin_licenses">
    <h3 class="header" style="margin-bottom:4.5em;">
        <span><img src="<?php echo $this->baseUrl()?>/application/modules/Hecore/externals/images/license.png"/>
            <?php echo $this->translate('HECORE_LICENSES')?>
        </span>

        <div style="position:absolute; padding-top:32px; font-size:10pt; letter-spacing:0;">
            <?php echo $this->translate("HECORE_LICENSES_DESCRIPTION") ?>
        </div>
    </h3>
    <?php if ($this->checkLicense): ?>
    <ul class="form-errors">
        <li><?php echo $this->translate('HECORE_UPDATE_LICENSE')?></li>
    </ul>
    <?php endif?>
    <div class="loader">
        <img src="<?php echo $this->baseUrl()?>/application/modules/Hecore/externals/images/he_contacts_loading.gif"
             alt=""/>
    </div>
    <div style="clear:both;"></div>

    <div class="list"></div>
</div>

<div class="admin_home_news">


<h3 class="sep" style="margin-bottom:2em" id='hecore-plugins-conteiner'>
        <span>
            <img src="<?php echo $this->baseUrl()?>/application/modules/Hecore/externals/images/plugin.png"
                 style='height:24px; vertical-align:top;margin-right:5px; float:left;'/>
        <span style='float: left;font-weight:bold;'><?php echo $this->translate("hecore_Hire-Experts Plugins") ?>

            <?php if ($this->installed > 0): ?>
            :&nbsp;&nbsp;</span>
        <div class="search" style="float:left; padding:5px;letter-spacing:0px">
            <select id='select-filter' onchange="hecore_plugin_filter($(this).value)">
                <option value='new' selected="true"><?php echo $this->translate('hecore_New'); ?></option>
                <option value='installed'><?php echo $this->translate('hecore_Installed'); ?></option>
                <option value='all'><?php echo $this->translate('hecore_All'); ?></option>
            </select>
        </div>
            <?php else: ?>
        </span>
        <?php endif;?>

            </span>
</h3>

<?php if ($this->updated > 0): ?>
<h3 class="sep" style="margin-bottom:3.5em;display:none;" id='hecore-updates-conteiner'>
        <span>
        <img src="<?php echo $this->baseUrl()?>/application/modules/Hecore/externals/images/update.png"
             style='height:24px; vertical-align:top;margin-right:5px;'/>
            <?php echo $this->translate("hecore_Hire-Experts Plugin Updates") ?>
        </span>

    <div style="position:absolute; padding-top:32px; font-size:10pt; letter-spacing:0;">
        <?php echo $this->translate("hecore_Hire-Experts Plugin Updates Description") ?>
    </div>
</h3>
    <?php endif; ?>

<?php if (!empty($this->plugins)): ?>
<ul>
    <?php foreach ($this->plugins as $item): ?>
    <li class='hecore-all <?php if (isset($item->installed)): ?>hecore-installed <?php if (isset($item->updated)): ?> hecore-updated <?php endif; ?> <?php else: ?>hecore-new<?php endif;?>'>
        <div class='hecore-plugin'>
            <table id="<?php echo $item->key;?>" class='hecore-plugin-item'>
                <tr>
                    <td width="220px" valign="top">
                        <div class="hecore-plugin-thumb">
                            <a href="<?php echo $item->link ?>" target="_blank">
                                <div>
                                    <?php echo $item->name ?>
                                    <img src="<?php echo $item->icon; ?>" border="0" class="hecore-plugin-icons"
                                         alt="<?php echo $item->name; ?>"/>
                                </div>
                            </a>
                        </div>
                    </td>
                    <td valign="top" width="100%">
                        <div class="admin_home_news_info" style="padding-right:0px">
                            <div class="hecore-plugin-header">
                                <div class="hecore-plugin-tabs">
                          <span class='hecore-plugin-tab <?php echo $item->key;?>-tab active-tab'
                                onclick="hecore_show_tab('<?php echo $item->key; ?>','info', $(this));">
                            <?php echo $this->translate('hecore_Information'); ?>
                          </span>
                                </div>

                                <div class="hecore-plugin-tabs ">
                          <span class='<?php echo $item->key;?>-tab hecore-plugin-tab'
                                onclick="hecore_show_tab('<?php echo $item->key; ?>', 'desc', $(this));">
                            <?php echo $this->translate('hecore_Description'); ?>
                          </span>
                                </div>

                                <?php if (((int)$item->pics) > 0): ?>
                                <div class="hecore-plugin-tabs">
                          <span class='<?php echo $item->key;?>-tab hecore-plugin-tab'
                                onclick="hecore_show_tab('<?php echo $item->key; ?>','screen', $(this));">
                            <?php echo $this->translate('hecore_Screenshots') . '(' . $item->pics . ')'; ?>
                          </span>
                                </div>
                                <?php endif; ?>
                            </div>


                            <div style='clear:both;'></div>

                            <div
                                class="hecore-plugin-tab-content <?php echo $item->key; ?>-content active-tab-content"
                                id="<?php echo $item->key; ?>-content-info">
                                <table>
                                    <tr>
                                        <td class='hecore-plugin-information'
                                            valign="top"><?php echo $this->translate('hecore_Version')?></td>
                                        <td>

                                            <?php if (isset($item->current_version) && $item->current_version && $this->isSuperAdmin): ?>
                                            <?php
                                            $link = $this->htmlLink(
                                                array(
                                                    'module' => 'hecore',
                                                    'controller' => 'index',
                                                    'action' => 'update',
                                                    'plugin' => $item->key
                                                ),
                                                $this->translate('HECORE_UPDATE'),
                                                array(
                                                    'onclick' => 'plugin_update(this);return false;',
                                                    'class' => 'hecore_button_update'
                                                )
                                            );
                                            if ($this->special_mode) {
                                                $link = $this->htmlLink(
                                                    array(
                                                        'module' => 'hecore',
                                                        'controller' => 'index',
                                                        'action' => 'update',
                                                        'plugin' => $item->key
                                                    ),
                                                    $this->translate('HECORE_UPDATE'),
                                                    array(
                                                        'href' => 'javascript:void(0);',
                                                        'class' => 'hecore_button_update'
                                                    )
                                                );
                                            }
                                            echo $this->translate('HECORE_NEWVERSION', array($item->current_version, $item->version, $link))
                                            ?>
                                            <?php else: ?>
                                            <?php echo $item->version ?>
                                            <?php endif?>

                                        </td>
                                    <tr></tr>
                                    <tr>
                                        <td class='hecore-plugin-information'
                                            valign="top"><?php echo $this->translate('hecore_Price') ?></td>
                                        <td><?php echo $item->price?> $</td>
                                    <tr></tr>
                                    <tr>
                                        <td class='hecore-plugin-information'
                                            valign="top"><?php echo $this->translate('hecore_Official Page')?></td>
                                        <td><a href="<?php echo $item->link ?>"
                                               target="_blank"><?php echo $item->link ?></a></td>
                                    <tr></tr>
                                    <tr>
                                        <td class='hecore-plugin-information' nowrap="tab"
                                            valign="top"><?php echo $this->translate('hecore_Link on Social Engine')?></td>
                                        <td><a href="<?php echo $item->link_on_se ?>"
                                               target="_blank"><?php echo $item->link_on_se ?></a></td>
                                    <tr></tr>
                                </table>
                            </div>

                            <div style='clear:both;'></div>

                            <div class="hecore-plugin-tab-content <?php echo $item->key; ?>-content"
                                 id="<?php echo $item->key; ?>-content-desc">
                                <?php echo $item->short_desc ?>

                                <nobr><a href="<?php echo $item->link; ?>" target="_blank">
                                    <?php echo $this->translate('read more'); ?>
                                </nobr>
                                </a>
                            </div>

                            <div style='clear:both;'></div>

                            <div class="hecore-plugin-tab-content <?php echo $item->key; ?>-content"
                                 id="<?php echo $item->key; ?>-content-screen">
                                <?php for ($i = 1; $i <= $item->pics; $i++): ?>
                                <div class='hecore-plugin-screenshot'>
                                    <a rel="<?php echo $item->key;?>[<?php echo $item->name; ?>]"
                                       title="<?php echo $i . '-' . $item->name; ?>"
                                       href="http://www.hire-experts.com/images/products/se4/<?php echo $item->key . '_' . $i; ?>.jpg">
                                        <img
                                            src="http://www.hire-experts.com/images/products/se4/<?php echo $item->key . '_' . $i; ?>_t.jpg">
                                    </a>
                                </div>
                                <?php endfor;?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </li>
    <?php endforeach; ?>
    <li>
        <div class="admin_home_news_date">
            &nbsp;
        </div>
        <div class="admin_home_news_info" style="text-align:right;">
            &#187; <a href="http://www.hire-experts.com/social-engine-plugins"
                      target="_blank"><?php echo $this->translate("hecore_More Hire-Experts Plugins") ?></a>
        </div>
    </li>
</ul>

    <?php else: ?>

<div>
    <?php echo $this->translate('hecore_There are no new plugins, or we were unable to fetch the new plugins.') ?>
</div>
    <?php endif;?>
</div>

</div>

</div>

