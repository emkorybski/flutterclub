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

<script type="text/javascript">
    en4.core.runonce.add(function () {

        $('clear_fb').addEvent('click', function () {
            clear_data('facebook', new Array('inviter_facebook_consumer_key', 'inviter_facebook_consumer_secret'))
        })
        $('save_fb').addEvent('click', function () {
            save_data('facebook', new Array('inviter_facebook_consumer_key', 'inviter_facebook_consumer_secret'))
        })

        $('clear_tw').addEvent('click', function () {
            clear_data('twitter', new Array('inviter_twitter_consumer_key', 'inviter_twitter_consumer_secret'))
        })
        $('save_tw').addEvent('click', function () {
            save_data('twitter', new Array('inviter_twitter_consumer_key', 'inviter_twitter_consumer_secret'))
        })

        $('clear_ld').addEvent('click', function () {
            clear_data('linkedin', new Array('inviter_linkedin_consumer_key', 'inviter_linkedin_consumer_secret'))
        })
        $('save_ld').addEvent('click', function () {
            save_data('linkedin', new Array('inviter_linkedin_consumer_key', 'inviter_linkedin_consumer_secret'))
        })

        $('clear_gm').addEvent('click', function () {
            clear_data('gmail', new Array('inviter_gmail_consumer_key', 'inviter_gmail_consumer_secret'))
        })
        $('save_gm').addEvent('click', function () {
            save_data('gmail', new Array('inviter_gmail_consumer_key', 'inviter_gmail_consumer_secret'))
        })

        $('clear_ya').addEvent('click', function () {
            clear_data('yahoo', new Array('inviter_yahoo_consumer_key', 'inviter_yahoo_consumer_secret'))
        })
        $('save_ya').addEvent('click', function () {
            save_data('yahoo', new Array('inviter_yahoo_consumer_key', 'inviter_yahoo_consumer_secret'))
        })

        $('clear_ms').addEvent('click', function () {
            clear_data('hotmail', new Array('inviter_hotmail_consumer_key', 'inviter_hotmail_consumer_secret'))
        })
        $('save_ms').addEvent('click', function () {
            save_data('hotmail', new Array('inviter_hotmail_consumer_key', 'inviter_hotmail_consumer_secret'))
        })

        $('clear_lf').addEvent('click', function () {
            clear_data('lastfm', new Array('inviter_lastfm_api_key', 'inviter_lastfm_secret'))
        })
        $('save_lf').addEvent('click', function () {
            save_data('lastfm', new Array('inviter_lastfm_api_key', 'inviter_lastfm_secret'))
        })

        $('clear_16').addEvent('click', function () {
            clear_data('foursquare', new Array('inviter_foursquare_consumer_key', 'inviter_foursquare_consumer_secret'))
        })
        $('save_16').addEvent('click', function () {
            save_data('foursquare', new Array('inviter_foursquare_consumer_key', 'inviter_foursquare_consumer_secret'))
        })

        $('clear_mr').addEvent('click', function () {
            clear_data('mailru', new Array('inviter_mailru_id', 'inviter_mailru_private_key', 'inviter_mailru_secret_key'))
        })
        $('save_mr').addEvent('click', function () {
            save_data('mailru', new Array('inviter_mailru_id', 'inviter_mailru_private_key', 'inviter_mailru_secret_key'))
        })

        function save_data(provider, values) {
            var empty_flag = true;

            for (var i = 0; i < values.length; i++) {
                if ($(values[i]).value.length == 0)
                    empty_flag = false;
            }

            if (empty_flag) {
                var params = {};
                for (i = 0; i < values.length; i++) {
                    params[values[i]] = $(values[i]).value;
                }
                flip_loader(provider, true);
                var r = new Request.JSON({
                    url:en4.core.baseUrl + 'admin/inviter/settings/providers-save',
                    data:{
                        format:'json',
                        provider:provider,
                        values:params
                    },
                    onSuccess:function (response) {
                        if (response.error) {
                            flip_loader(provider, false);
                        } else {
                            flip_loader(provider, false);
                        }
                    },
                    onRequest:function () {
                        flip_loader(provider, false);
                    },
                    onFailure:function () {
                        flip_loader(provider, false);
                    },
                    onCancel:function () {
                        flip_loader(provider, false);
                    },
                    onException:function () {
                        flip_loader(provider, false);
                    }
                });
                r.send();
            }
        }

        function clear_data(provider, values) {
            var empty_flag = false;

            for (var i = 0; i < values.length; i++) {
                if ($(values[i]).value.length != 0)
                    empty_flag = true;
            }

            if (empty_flag) {

                if (!confirm('<?php echo $this->string()->escapeJavascript($this->translate("INVITER_Clear Credentials confirmation")); ?>'))
                    return;
                flip_loader(provider, true);
                var r = new Request.JSON({
                    url:en4.core.baseUrl + 'admin/inviter/settings/providers-clear',
                    data:{
                        format:'json',
                        provider:provider,
                        values:values
                    },
                    onSuccess:function (response) {
                        if (response.error) {
                            flip_loader(provider, false);
                        } else {
                            flip_loader(provider, false);
                            for (var i = 0; i < values.length; i++) {
                                $(values[i]).value = '';
                            }
                        }
                    },
                    onRequest:function () {
                        flip_loader(provider, false);
                    },
                    onFailure:function () {
                        flip_loader(provider, false);
                    },
                    onCancel:function () {
                        flip_loader(provider, false);
                    },
                    onException:function () {
                        flip_loader(provider, false);
                    }

                });
                r.send();
            }
        }

        function flip_loader(provider, flag) {
            if (flag) {
                $(provider).removeClass('inviter_loader_hidden');
                $(provider).addClass('inviter_loader');
            } else {
                $(provider).removeClass('inviter_loader');
                $(provider).addClass('inviter_loader_hidden');
            }

        }

    });
</script>
<?php if (count($this->navigation)): ?>
<div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>