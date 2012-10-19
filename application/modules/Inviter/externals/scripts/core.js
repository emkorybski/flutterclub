function postActivityResponse(response) {
    if (response.hadError()) {
        alert("requestCreateActivity failed.");
        return;
    }
    else {
        //activity posted successfully, handle appropriate to your app
    }
}
/* $Id: core.js 2010-05-25 01:44 mirlan $ */

var provider_select;

en4.core.runonce.add(function () {
    if ($('done-label') != undefined) {
        $('done-label').getElements('.optional').set('text', '');
        $('done-label').setStyle('display', 'inline');
    }

    if ($('provider_list') != undefined) {
        $('provider_list').clone().inject($('default_provider_list'));
        provider_select = new Fx.Slide('provider_list');
        provider_select.slideOut();
    }

});

function sendInvitations() {
    $recipients = $('recipients').value.trim();
    $message = $('message').value;
    var data = {};
    data.format = 'json';
    data.recipients = $recipients;
    data.message = $message;

    $page_id = $('page_id');
    if ($page_id) {
        if ($page_id.value != '') {
            data.page_id = $page_id.value;
        }
    }

    if ($recipients.length == 0) {
        he_show_message(en4.core.language.translate('INVITER_Failed!, please check your contacts and try again.'), 'error');
        $('recipients').focus();
    }
    else {
        new Request.JSON({
            'url':'inviter/index/write-contacts',
            'method':'post',
            'data':data,
            'onRequest':function () {
                $('writer_contacts_loading').setStyle('display', 'inline');
                $$('.writer_textarea').setProperty('disabled', true);
            },
            'onSuccess':function ($resp) {
                if ($resp != null) {
                    switch ($resp.status) {
                        case 0:
                            he_show_message($resp.message, 'error');
                            break;

                        case 1:
                            he_show_message($resp.message);
                            $('recipients').value = '';
                            break;

                        case 2:
                            he_show_message($resp.message, 'notice');
                            break;
                    }
                }

                $$('.writer_textarea').setProperty('disabled', false);
                $('writer_contacts_loading').setStyle('display', 'none');
                $('recipients').focus();
            }
        }).send();
    }
}

var inviter = {

    page_id:0,

    tabs:'div.layout_core_container_tabs',
    $tabs:{},
    page_inviter_forms:'page-inviter-forms-wrapper',
    $page_inviter_forms:{},

    inviter_tab:'page-inviter-tab-container',
    $inviter_tab:{},


    friend_request:function () {
        var $users = he_contacts.contacts;

        if ($users.length > 0) {
            var $el = new Element('a', {'href':'inviter/index/friendrequest?user_ids=' + $users, 'class':'smoothbox'});
            Smoothbox.open($el);
        }
    },

    invitation_send:function (provider, url) {
        var $contacts = he_contacts.contacts;
        var $message = $('message_box').value;

        var $el = new Element('a', {
            'href': url + '?contact_ids=' + $contacts + '&message=' + $message,
            'class':'smoothbox'
        });

        Smoothbox.open($el);
    },

    send_to_fb_on_signup:function (link, host, form) {
        var $contacts = he_contacts.contacts;
        var caption = $('fb-caption').value;
        var $message = $('messageBox').value;
        FB.ui({
                method:'send',
                display:'popup',
                to:$contacts,
                link:link,
                name:caption,
                description:$message + ' ' + link,
                show_error:true
            }, function (response) {
                if (response != null)
                    $('invitation_send').submit();
            }
        );
    },

    send_to_fb:function (user_id) {
        var link = $('fb-invitation-url').value;
        var host = $('fb-host').value;
        var invite_code = $('fb-invite-code').value;
        var caption = $('fb-caption').value;
        var message = $('fb-message').value;
        var picture = $('fb-picture').value;
        var fail_message = $('fb-fail-message').value;

        var redirect_url = false;
        if ($('fb-redirect-url') != null)
            redirect_url = $('fb-redirect-url').value;

        var page_id = false;
        if ($('fb-page-id') != null)
            page_id = $('fb-page-id').value;
        var signup = false;
        if ($('fb-signup') != null)
            signup = $('fb-signup').value;


        FB.login(function (response) {
            if (response.authResponse) {
                if (!page_id) {
                    FB.api('/me', function (response) {
                        redirect_url += '?code=' + invite_code + '&message' + message;
                        redirect_url += '&name=' + response.name;
                    });
                }
                FB.ui({
                        method:'send',
                        display:'popup',
                        to:user_id,
                        link:host,
                        name:caption,
                        description:message + ' ' + link,
                        picture:picture,
                        show_error:true
                    }, function (response) {
                        if(!response) {
                            he_show_message(fail_message, 'error', 5000);
                        } else {
                            if(signup) {
                                document.getElementById("skip").value = "skipFormInviter";
                                document.getElementById("invite_friends").submit();
                            } else if(user_id) {

                            } else {
                                if (redirect_url) {
                                    window.location.href = redirect_url;
                                }
                                else {
                                    window.location.href = 'inviter';
                                }
                            }
                        }
                    }
                );
            } else {
                console.log('User cancelled login or did not fully authorize.');
            }
        });
        return false;
    },

    page_inviter_init:function () {
        var self = this;
        self.$tabs = $$('div.layout_core_container_tabs')[0];
        self.$inviter_tab = new Element('div', {'id':self.inviter_tab, 'class':'page-inviter-tab-container hidden'});
        self.$page_inviter_forms = $('page-inviter-forms-wrapper');
        self.$inviter_tab.set('html', self.$page_inviter_forms.innerHTML);
        var $items = new Element('div', {'id':self.tab, 'class':'page-search-tab'});
        self.$inviter_tab.appendChild($items);
        self.$tabs.appendChild(self.$inviter_tab);
        self.$page_inviter_forms.dispose();
    },

    page_inviter_open:function () {
        var self = this;
        $$(self.$tabs.getElements('.generic_layout_container')).each(function ($element) {
            $element.setStyle('display', 'none');
        })
        self.$inviter_tab.removeClass('hidden');
        self.$inviter_tab.setStyle('display', 'block');
        $$($('main_tabs').getElements('li.active')).each(function ($li) {
            $li.removeClass('active');
        });
    },

    page_invitation_send:function (provider, $page_id, url) {

        var $contacts = he_contacts.contacts;
        var $message = $('message_box').value;

        var $el = new Element('a', {
            'href': url + '?contact_ids=' + $contacts + '&message=' + $message + '&page_id=' + this.page_id,
            'class':'smoothbox'
        });
        Smoothbox.open($el);

    },

    page_inviter_submit:function () {
        var self = this;
        var form_data = $('invite_friends').toQueryString();
        var r = new Request.HTML({
            url:en4.core.baseUrl + 'page-inviter',
            method:'post',
            data:form_data,
            evalScripts:true,
            onSuccess:function (responseTree, responseElements, responseHTML, responseJavaScript) {
                if (self.$inviter_tab) {
                    self.$inviter_tab.innerHTML = responseHTML;
                }
                en4.core.runonce.trigger();
            }
        });
        r.post();
    },

    live_logout:function (logout_url, redirect_url) {
        var logout_window = window.open(logout_url, '', 'HEIGHT=500,WIDTH=500')
        setTimeout(function () {
            logout_window.close();
            window.location.href = redirect_url;
        }, 6000);
    },

    share_to:function (provider) {

    }
};

var provider = {

    integrated_providers:{},

    value_flag:'',

    force_submit:false,

    fb_enabled:false,

    fb_signup_url:'',

    fb_check_session:false,

    oauth_url:'',

    show_providers:function () {
        provider_select.element.removeClass('providers_hidden');
        provider_select.toggle();
        $('provider_box-toggle_providers').toggleClass('hide_provider_btn');
    },

    select_provider:function ($el, $show_suggest) {

        var label = $el.getElement('.provider_name').get('text').trim();
        var $photo = $el.getElement('.provider_logo img').clone();

        $('provider_box-selected_provider').empty().grab($photo);
        $('provider_box').addClass('active_provider_box_input');
        $('provider_box').value = label;

        var invite_page = $('email_box-wrapper') ? true : false;
        var $main_container = $('inviter-importer-form');

        if (this.integrated_providers[label]) {
            if (invite_page) {
                $('email_box-wrapper').addClass('display_none');
                $('password_box-wrapper').addClass('display_none');
            } else {
                $main_container.getElement('#inviter_email_box').addClass('display_none');
                $main_container.getElement('#inviter_password_box').addClass('display_none');
            }
        } else {
            if (invite_page) {
                $('email_box-wrapper').removeClass('display_none');
                $('password_box-wrapper').removeClass('display_none');
            } else {
                $main_container.getElement('#inviter_email_box').removeClass('display_none');
                $main_container.getElement('#inviter_password_box').removeClass('display_none');
            }
        }

        if ($show_suggest == true) {
            //provider_suggest($('provider_box'));
        }
        else {
            $('provider_box-toggle_providers').toggleClass('hide_provider_btn');
            provider_select.toggle();
        }
    },

    provider_suggest:function ($el) {
        var self = this;

        $value = $el.value.trim();
        var $result = Array(Array());
        var $j = 0;

        if ($el.value.trim().length == 0) {
            self.value_flag = '';
            provider_select.element.addClass('providers_hidden');
            provider_select.slideOut();
            $('provider_list').empty().set('html', $('default_provider_list').getElements('.provider_list').get('html'));
            $('provider_box-toggle_providers').removeClass('hide_provider_btn');
        }

        else if ($value.length > 0 && self.value_flag != $el.value.trim()) {
            $('provider_list').empty();
            self.value_flag = $value;
            for (var $i = 0; $i < $providers.length; $i++) {
                if ($value.toLowerCase() == $providers[$i].title.trim().substr(0, $value.length).toLowerCase()) {
                    $div = new Element('div',
                        {
                            'class':'provider'
                        });

                    $provider_logo = new Element('div',
                        {
                            'class':'provider_logo'
                        });

                    $img = new Element('img',
                        {
                            'src':'application/modules/Inviter/externals/images/providers/' + $providers[$i].logo
                        });

                    $provider_name = new Element('div',
                        {
                            'class':'provider_name'
                        });

                    $div_clear = new Element('div',
                        {
                            'styles':{'clear':'both'}
                        });

                    $provider_name.set('text', $providers[$i].title);
                    $provider_logo.grab($img);
                    $div.grab($provider_logo);
                    $div.grab($provider_name);
                    $div.grab($div_clear);
                    $div.addEvent('click', function () {
                        provider.select_provider(this, false)
                    });

                    $('provider_list').grab($div, 'bottom');
                    $j++;
                }
            }
            if ($j > 0) {
                provider_select.element.removeClass('providers_hidden');
                provider_select.slideIn();
                $('provider_box-toggle_providers').addClass('hide_provider_btn');
            }
        }
    },

    provider_blur:function ($el) {
        $value = $el.value.trim();
        for (var $i = 0; $i < $providers.length; $i++) {
            if ($value.toLowerCase() == $providers[$i].title.toLowerCase()) {
                $img = new Element('img',
                    {
                        'src':'application/modules/Inviter/externals/images/providers/' + $providers[$i].logo
                    });
                $('provider_box-selected_provider').empty().grab($img);
                $('provider_box-selected_provider').setStyle('display', '');
                $('provider_box').value = $providers[$i].title;

                provider_select.element.addClass('providers_hidden');
                provider_select.slideOut();

                $('provider_box-toggle_providers').removeClass('hide_provider_btn');
            }
        }
    },

    submit_form:function ($node) {
        $node = $($node);

        var self = this;
        var $form = $($node).getParent('form');
        var label = $form.getElement('input[name="provider_box"]').value.trim();

        if (this.force_submit) {
            return true;
        }
        if (this.integrated_providers[label]) {
            if (label == 'Facebook') {
                inviter.send_to_fb('');
                return false;
            }
            this.open_connect(label);
            return false;
        }
        return true;
    },

    signup_submit_form:function ($node) {
        $node = $($node);

        var self = this;
        var $form = $($node).getParent('form');
        var label = $form.getElement('input[name="provider_box"]').value.trim();

        finishForm();

        if (this.force_submit && this.integrated_providers[label] && label == 'Facebook') {
            $form.setProperty('action', this.fb_signup_url);
        } else {
            $form.setProperty('action', '');
        }
        $form.setProperty('action', '');
        if (this.force_submit) {
            return true;
        }

        if (this.integrated_providers[label]) {
            if (label == 'Facebook') {
                inviter.send_to_fb('');
                return false;
            }
            this.open_connect(label+'/signup/1');
            return false;
        }

        return true;
    },

    check_fb_session:function () {
        FB.getLoginStatus(function (response) {
            if (response.status == "connected") {
                alert(1);
            } else {
                alert(2);
            }
        });
    },

    open_connect:function (label) {
        label = label.toLowerCase();
        label = label.replace('!', '');

        if (label == 'live/hotmail' || label == 'msn') {
            label = 'hotmail';
        }

        var url = this.oauth_url + '/provider/' + label;
        window.open(url, '', 'HEIGHT=500,WIDTH=800');
    },

    set_enabled:function (label, status) {
        this.integrated_providers[label] = status;
    }
}

var suggest = {
    li:null,
    suggest_url:'inviter/ajax/suggest',
    current_suggest:{},

    remove_suggest:function ($noneFriend_id, $widget) {
        var self = this;

        new Request.JSON({
            'url':self.suggest_url,
            'method':'post',
            'data':{'format':'json', 'current_suggests':self.current_suggests, 'nonefriend_id':$noneFriend_id, 'widget':$widget},
            'onRequest':function () {
                self.li = $('suggest_' + $noneFriend_id).getParent('li');
                self.li.fade('out');
            },
            'onSuccess':function ($response) {
                if ($response != null) {
                    self.current_suggests = $response.current_suggests;
                    $html = $response.html;

                    if ($html.trim() == '""') {
                        setTimeout('suggest.suggest_destroy()', '100');
                    }
                    else {
                        setTimeout('suggest.suggest_replace(' + $html + ')', '100');
                    }
                }
            }
        }).send();
    },

    suggest_destroy:function () {
        var self = this;
        self.li.destroy();
        self.li = null;
    },

    suggest_replace:function ($html) {
        var self = this;
        self.li.set('html', $html);
        self.li.fade('in');
        self.li = null;
    },

    suggest_more:function ($el) {
        $('suggest_more_button').destroy();
        $('suggest_list').adopt($('more_suggests').getElements('li'));
    },

    edit_suggest:function () {
        he_contacts.box('inviter',
            'getNonefriends',
            'suggest.add_to_suggest',
            en4.core.language.translate('INVITER_Edit Suggest List'),
            {'button_label':en4.core.language.translate('INVITER_Add to Suggest')},
            0);
    },

    add_to_suggest:function ($user_ids) {
        if ($user_ids.length > 0) {
            new Request.JSON({
                'url':'inviter/ajax/addtosuggest',
                'method':'post',
                'data':{'format':'json', 'user_ids':$user_ids},
                'onSuccess':function ($response) {
                    if ($response.result == 1) {
                        he_show_message(en4.core.language.translate('INVITER_Members successfully have been added to suggest list.'));
                    } else {
                        he_show_message(en4.core.language.translate('INVITER_Failed! Please check and try again later.'), 'error');
                    }
                }
            }).send();
        }
    },

    show_mutual_friends:function ($user_id) {
        he_list.box('inviter',
            'getMutualfriends',
            en4.core.language.translate('INVITER_Mutual Friends'),
            {'user_id':$user_id, 'hide_options':true},
            0);
    }
}

/* Member Introduce */
var InviterIntroduce =
{
    showForm:function (uid) {
        var $widget_box = $(uid);
        var $smoothbox_cont = $widget_box.getElement('.introduce_form_tpl');
        Smoothbox.open($smoothbox_cont, {mode:'Inline', width:400, height:400});
    },

    save:function (node, mode) {
        var self = this;
        var $node = $(node);

        var body_txt = $node.getParent().getElement('textarea.introduce_body').value;
        var widget_id = $node.getParent().getElement('input[name="widget_id"]').value;

        new Request.JSON({
            'url':self.url,
            'method':'post',
            'data':{'format':'json', 'task':'save', 'body':body_txt},
            'onSuccess':function (response) {
                if (response.result == 1) {
                    he_show_message(en4.core.language.translate('INVITER_Your information successfully saved'));
                    Smoothbox.close();
                    if (mode == 'profile')
                        self.hideBox2(widget_id, body_txt);
                    else
                        self.hideBox(widget_id);
                } else {
                    he_show_message(en4.core.language.translate('INVITER_Failed! Please type few words about you.'), 'error');
                }
            }
        }).send();
    },

    hideBox2:function (uid, body_txt) {
        var edit_block = $(uid).getElement('div#profile-introduce');
        var introduce_block = $(uid).getElement('div.introduce_yourself_cont');


        edit_block.setStyle('display', 'block');
        edit_block.getElement('div.introduce_member_body').appendText(body_txt);
        introduce_block.setStyle('display', 'none');
    },

    hideBox:function (uid, parent_sel) {
        var $box = $(uid);
        var $widget_cont = (parent_sel) ? $box.getParent(parent_sel) : $box.getParent('.layout_inviter_introduce_yourself');

        $widget_cont.set('tween', {'duration':300});
        $widget_cont.fade('out');

        window.setTimeout(function () {
            $widget_cont.addClass('display_none');
        }, 301);
    },

    hide:function (uid) {
        var self = this;

        new Request.JSON({
            'url':self.url,
            'method':'post',
            'data':{'format':'json', 'task':'hide'},
            'onSuccess':function (response) {
                self.hideBox(uid);
            }
        }).send();
    },

    hideMember:function (node, user_id) {
        var self = this;
        var $node = $(node);
        var $box = $node.getParent('.introduce_member_box');
        var uid = $box.getProperty('id');

        new Request.JSON({
            'url':self.url,
            'method':'post',
            'data':{'format':'json', 'task':'hide_member', 'user_id':user_id},
            'onSuccess':function (response) {
                if (response.result == 0) {
                    self.hideBox(uid, '.layout_inviter_introduce_member');
                } else {
                    var $cur_user = $box.getElement('.introduce_member_cont');
                    var $new_user = new Element('div', {'class':'introduce_member_cont', 'html':response.html});
                    $new_user.addClass('display_none');
                    $new_user.setStyles({'visibility':'hidden', 'opacity':0});

                    $box.grab($new_user);

                    $cur_user.set('tween', {'duration':300});
                    $cur_user.fade('out');
                    $new_user.set('tween', {'duration':300});

                    window.setTimeout(function () {
                        $cur_user.dispose();
                        $new_user.removeClass('display_none');
                        $new_user.fade('in');
                    }, 300);
                }
            }
        }).send();
    },

    hideMoreFriends:function (uid) {
        var self = this;
        new Request.JSON({
            'url':self.url,
            'method':'post',
            'data':{'format':'json', 'task':'hide_more_friends'},
            'onSuccess':function (response) {
                if (response.result == 1) {
                    self.hideBox(uid, '.layout_inviter_find_more_friends');
                }
            }
        }).send();
    },

    showAddFriendsBox:function (provider) {
        he_contacts.box('inviter', 'getNoneFriendContactsBox', 'InviterIntroduce.save_friends', en4.core.language.translate('Choose Friends'), {'provider':provider}, 0);
    },

    showConnectedFriends:function (provider, label) {
        he_list.box('inviter', 'getAlreadyFriendContactsBox', label, {'disable_list2':true, 'provider':provider});
    },

    save_friends:function (users) {
        var self = this;

        if (!users || users.length == 0) {
            return;
        }

        new Request.JSON({
            'url':self.url,
            'method':'post',
            'data':{'format':'json', 'task':'add_friends', 'users':users},
            'onSuccess':function (response) {
                if (response.result == 1) {
                    he_show_message(response.message);
                    window.location.href = window.location.href;
                }
            }
        }).send();
    }
}

var facebook_inviter = {

    buffer:'',
    loader:'',

    request:function (param, url) {
        var self = this;
        var data = {'provider':'facebook'};
        if (param == 1) {
            data.new_param = 1;
        }
        return new Request.JSON({
            'url':url,
            'method':'post',
            'data':data,
            'format':'json',
            onSuccess:function (response) {
                if (response != null) {
                    if (response.login_url != null) {
                        self.open_connect(response.login_url);
                    }
                    if (response.logout_url != null) {
                        self.open_connect(response.logout_url);
                    }
                }
            }
        }).send();
    },

    get_contacts:function (url, loader) {
        var self = this;
        var data = {'state':1};
        if (loader == 0)
            this.show_loader();
        return new Request.HTML({
            url:url,
            method:'post',
            data:data,
            format:'html',
            evalScripts:true,
            onSuccess:function (responseTree, responseElements, responseHTML, responseJavaScript) {
                var script = '<script type="text/javascript">' + responseJavaScript + '</script>'
                $('fb-tab').innerHTML = script + responseHTML;
                en4.core.runonce.trigger();
            }
        }).send();
    },

    open_connect:function (url) {
        window.open(url, '', 'HEIGHT=500,WIDTH=800');
    },

    show_loader:function () {
        $('fb-tab').getElementsByTagName('form')[0].destroy();
        $('inviter-loader').setStyle('display', 'block');
    }

}