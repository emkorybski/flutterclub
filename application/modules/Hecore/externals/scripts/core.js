
/* $Id: core.js 2010-07-30 18:00 ermek $ */

/**
 * @param message String
 * @param type String 'text'|'error'|'notice'
 * @param delay Integer
 */

function he_show_message(message, type, delay) {
  var text = '';
  var duration = 400;
  var delay = (delay == undefined) ? 3000 : delay;

  text = message;

  if (window.$message_container == undefined) {
    window.$message_container = new Element('div', {'class': 'he_message_container'});
    $(document.body).adopt(window.$message_container);
  }

  var className = 'he_msg_text';
  if (type == 'error') {
    className = 'he_msg_error';
  } else if (type == 'notice') {
    className = 'he_msg_notice';
  } else {
    className = 'he_msg_text';
  }

  var $message = new Element('div', {
    'class': className,
    'styles': {
      'opacity': 0
    }
  });
  var $close_btn = new Element('a', {
    'class': 'he_close',
    'href': 'javascript://',
    'events': {
      'click': function(){
        $message.fade('out');
        $message.removeClass('visible');

        window.setTimeout(function(){
          $message.dispose();
          if (window.$message_container.getElements('.visible').length == 0) {
            window.$message_container.empty();
          };
        }, duration);
      }
    }
  });

  $message.addClass('visible');
  $message.adopt($close_btn);
  $message.adopt('html', new Element('span', {'html': message}));
  window.$message_container.adopt($message);

  $message.set('tween', {duration: duration});
  $message.fade('in');

  window.setTimeout(function(){
    $message.fade('out');
    $message.removeClass('visible');
    window.setTimeout(function(){
      if (window.$message_container.getElements('.visible').length == 0) {
        window.$message_container.empty();
      };
    }, duration);
  }, delay);
}

function he_show_image(src, element)
{
  if (!src) {
    return false;
  }

  var $element = (element) ? $(element) : $('he_temp_photo_start');
  if (!$element) {
    $element = new Element('div', {'id': 'he_temp_photo_start'});
    $element.setStyles({'position': 'fixed', 'top': '50%', 'left': '50%'});

    $$('body')[0].grab($element);
  }

  var close_title = (typeof(en4.core.language.translate) == 'undefined')
    ? language.translate('close')
    : en4.core.language.translate('close');

  var instance  = new Imagezoom({
    'image': src,
    'startElement': $element,
    'closeText': close_title
  });

  instance.show();
}

function he_replace_form_error(msg)
{
  var $container = $$('.global_form .errors li');

  if ($container.length != 1) {return false;}

  $container.set('html', msg);

  var $error_link = $container.getElement('a.smoothbox');
  if ($error_link) {
    Smoothbox.bind($error_link);
  }
}

function he_add_lang_vars(data)
{
  if (!data) {return;}

  var obj_link = en4.core.language.options.lang;

  for(var key in data) {
    obj_link[key] = data[key];
  }
}

function object_to_query_string(object)
{
  var query = "";
  for(key in object){
    query += '&params['+key+']='+object[key];
  }

  return query;
}

/*
NEW HECORE CONTACTS MODULE
* */

var HEContacts = new Class({

  Implements: [Events, Options],

  options: {
    m: 'hecore',
    l: '',
    c: '',
    t: '',
    params: {},
    nli: 0,
    keyword: '',
    p: 1,
    ipp: 30,
    total: 0,
    contacts: [],
    itemClass: 'item',
    filterField: 'contacts_filter',
    filterSubmit: 'contacts_filter_submit',
    container: 'he_contacts_list',
    activeClass: 'active',
    hiddenClass: 'hidden',
    disabledClass: 'disabled',
    visibleClass: 'visible',
    listType: 'all',
    contactsCountNode: 'selected_contacts_count',
    submitButtonNode: 'submit_contacts',
    selectAllNode: 'select_all_contacs',
    moreNode: 'contacts_more',
    listTypeAll: 'he_contacts_list_all',
    listTypeSelected: 'he_contacts_list_selected',
    format: 'json'
  },

  url: 'hecore/index/contacts',

  block: false,

  $filter: null,

  $container: null,

  $items: null,

  $selectedCount: null,

  $submit: null,

  $filterSubmit: null,

  $selectAll: null,

  $more: null,

  $listAll: null,

  $listSelected: null,

  needPagination: false,

  onLoad: false,
  onClose: false,

  width: 550,
  height: 390,

  initialize: function(options) {
    this.setOptions(options);
  },

  box: function() {
    var self = this;
    var url = this.url + this.getQuery();
    var $element = new Element('a', {'href': url, 'class': 'smoothbox'});

    Smoothbox.open($element, {'mode': 'Request', width: this.width, height: this.height,
      'onLoad': function() {
        self.init();
        if (self.onLoad) {
          self.onLoad();
          self.onLoad = false;
        }
      },
      'onClose': function() {
        if (self.onClose) {
          self.onClose();
          self.onClose = false;
        }
      }
    });
  },

  getQuery: function() {
    var query = object_to_query_string(this.options.params); // @todo need to chanege this
    return '?m='+this.options.m+'&l='+this.options.l+'&c='+this.options.c+'&t='+this.options.t+'&nli='+this.options.nli+query;
  },

  init: function() {
    this.$filter = $(this.options.filterField);
    this.$container = $(this.options.container);
    this.$selectedCount = $(this.options.contactsCountNode);
    this.$submit = $(this.options.submitButtonNode);
    this.$filterSubmit = $(this.options.filterSubmit);
    this.$selectAll = $(this.options.selectAllNode);
    this.$more = $(this.options.moreNode);
    this.$listAll = $(this.options.listTypeAll);
    this.$listSelected = $(this.options.listTypeSelected);
    this.$items = $$(this.$container.getElements('.'+this.options.itemClass));

    $('TB_ajaxContent').addClass('hecore_he_list_window');

    var self = this;
    if (this.$items.length > 0) {
      this.$items.removeEvents('click').addEvent('click', function(){
        this.blur();
        self.chooseContact(this);
      });
    }

    if (this.$submit) {
      this.$submit.removeEvents('click').addEvent('click', function(){
        self.submit();
      });
    }

    if (this.$more) {
      this.$more.removeEvents('click').addEvent('click', function(){
        self.more();
      });
    }

    if (this.$listAll) {
      this.$listAll.removeEvents('click').addEvent('click', function(){
        self.chooseList('all');
      });
    }

    if (this.$listSelected) {
      this.$listSelected.removeEvents('click').addEvent('click', function(){
        self.chooseList('selected');
      });
    }

    if (this.$filterSubmit) {
      this.$filterSubmit.removeEvents('click').addEvent('click', function(){
        self.search();
      });
    }

    if (this.$selectAll) {
      this.$selectAll.removeEvents('click').addEvent('click', function(){
        var value = this.checked ? 1 : 0;
        self.chooseAll(value);
      });
    }

    if (this.$filter) {
      this.$filter.removeEvents('keyup').addEvent('keyup', function(event) {
        if (event.code == 13) {
          self.search();
        }
      });
    }
  },

  chooseList: function(type) {
    this.options.listType = type;

    switch (type) {
      case 'all':
        this.$listSelected.removeClass(this.options.activeClass);
        this.$listAll.addClass(this.options.activeClass);
        this.$items.removeClass(this.options.hiddenClass);
        this.$items.setStyle('opacity', 1);
        if (this.needPagination) {
          this.$more.removeClass(this.options.hiddenClass);
        }
        //this.$filter.getParent().removeClass(this.options.hiddenClass);
      break;
      case 'selected':
        this.$listSelected.addClass(this.options.activeClass);
        this.$listAll.removeClass(this.options.activeClass);
        this.$items.addClass(this.options.hiddenClass);
        if (this.$more) {
          this.$more.addClass(this.options.hiddenClass);
        }
        var $items = $$(this.$container.getElements('.'+this.options.activeClass));
        $items.removeClass(this.options.hiddenClass);
        //this.$filter.getParent().addClass(this.options.hiddenClass);
      break;
    }
  },

  chooseContact: function($node) {
    $node = $($node);

    if (!$node) {
      return ;
    }

    if ($node.hasClass(this.options.disabledClass)) {
      return ;
    }

    var contactId = parseInt($node.id.substr(8));
    if (this.options.contacts.indexOf(contactId) == -1) {
      this.select($node, contactId);
    } else {
      this.deselect($node, contactId);
    }

    var self = this;
		this.initCount();
    setTimeout(function(){self.no_result();}, 650);
  },

  select: function($node, contactId) {
    $node = $($node);
    if (!$node) {
      return ;
    }

    if ($node.hasClass(this.options.disabledClass)) {
      return ;
    }

    if (!contactId) {
      contactId = parseInt($node.id.substr(8));
    }

    $node = $($node);
    if (!$node.hasClass(this.options.activeClass)) {
      $node.addClass(this.options.activeClass);
    }

    if (this.options.contacts.indexOf(contactId) == -1) {
      this.options.contacts.push(contactId);
    }

    if (this.options.listType == 'selected') {
      this.show($node, true);
    }
  },

  deselect: function($node, contactId) {
    $node = $($node);
    if (!$node) {
      return ;
    }
    if ($node.hasClass(this.options.disabledClass)) {
      return ;
    }

    if (!contactId) {
      contactId = parseInt($node.id.substr(8));
    }

    $node = $($node);
    if ($node.hasClass(this.options.activeClass)) {
      $node.removeClass(this.options.activeClass);
    }

    if (this.options.contacts.indexOf(contactId) > -1) {
      this.options.contacts.splice(this.options.contacts.indexOf(contactId), 1);
    }

    if (this.options.listType == 'selected') {
      this.hide($node, true);
    }
  },

  initCount: function() {
    if (this.$selectedCount) {
			this.$selectedCount.set('text', this.options.contacts.length)
		}
  },

  submit: function() {
    eval("window.parent."+this.options.c+"(["+this.options.contacts.toString()+"])");
    window.parent.Smoothbox.close();
  },

  chooseAll: function(choose) {
    var self = this;
    if (choose) {
      $$(this.$items).each(function($item){
        $item = $($item);
        if ($item.hasClass(self.options.disabledClass)) {
          return ;
        }
        self.select($item);
      });
    } else {
      $$(this.$items).each(function($item){
        $item = $($item);
        if ($item.hasClass(self.options.disabledClass)) {
          return ;
        }
        self.deselect($item);
      });
    }
    this.initCount();
  },

  search: function() {
    this.options.keyword = this.$filter.value;
    this.options.p = 1;
    if (this.options.listType != 'all'){
      this.chooseList('all');
    }
    this.getItems(true);
  },

  more: function() {
    this.options.p++;
    this.getItems();
  },

  getItems: function(replace) {
    if (replace === undefined) {
      replace = false;
    }

    if (this.block) {
      return false;
    }

    this.block = true;
    var self = this;
    new Request.JSON({
      url: self.url,
      method: 'post',
      data: self.options,
      onSuccess: function(response) {
        self.block = false;
        self.respond(response, replace);
      }
    }).send();
  },

  respond: function(response, replace) {
    if (replace === undefined) {
      replace = false;
    }

    if (replace) {
      this.$container.set('html', response.html);
    } else {
      var html = this.$container.get('html') + response.html;
      this.$container.set('html', html);
    }

    if (this.$more) {
      this.needPagination = response.need_pagination;
      if (!this.needPagination) {
        this.$more.addClass(this.options.hiddenClass);
      } else {
        this.$more.removeClass(this.options.hiddenClass);
      }
    }

    this.init();
  },

  hide: function($node, fx) {
    var self = this;
    $node = $($node);
    if (!$node) {
      return ;
    }
    var func = function() {
      if (!$node.hasClass(self.options.hiddenClass)){
        $node.addClass(self.options.hiddenClass);
      }
    }

    if (fx) {
      setTimeout(func, 650);
      $node.tween('opacity', [1, 0]);
    } else {
      $node.setStyle('opacity', 0);
      func();
    }
  },

  show: function($node, fx) {
    var self = this;
    $node = $($node);
    if (!$node) {
      return ;
    }
    var func = function() {
      if ($node.hasClass(self.options.hiddenClass)) {
        $node.removeClass(self.options.hiddenClass);
      }
      if ($node.hasClass(self.options.disabledClass)){
        $node.setStyle('opacity', .5);
      }
    }

    $node.setStyle('visibility', 'visible');
    if (fx) {
      setTimeout(func, 650);
      if ($node.hasClass(this.options.disabledClass)){
        $node.tween('opacity', [0, .5]);
      } else {
        $node.tween('opacity', [0, 1]);
      }
    } else {
      $node.setStyle('opacity', 1);
      func();
    }
  },

  no_result: function(){
    if ($$('.visible').length == 0){
      this.show($('no_result'));
    }else{
      this.hide($('no_result'));
    }
  }

});

var he_contacts = {

  callback : '',
  contacts : [],
  title : '',
  list_type : '',
  keyword : '',
  params : {},
  onLoad: false,
  onClose: false,
  width: 550,
  height: 390,

  box: function(module, list, callback, title, params, not_logged_in) {
    this.params = params;
    not_logged_in = not_logged_in === undefined ? 0 : 1;
    var options = {
      c: callback,
      listType: "all",
      m: module,
      l: list,
      p: 1,
      params: params,
      nli: not_logged_in
    };

    window.HE_CONTACTS = new HEContacts(options);
    if (this.onLoad) {
      window.HE_CONTACTS.onLoad = this.onLoad;
    }

    if (this.onClose) {
      window.HE_CONTACTS.onClose = this.onClose;
    }

    window.HE_CONTACTS.width = this.width;
    window.HE_CONTACTS.height = this.height;

    window.HE_CONTACTS.box();
  },

  init : function(){
    var self = this;
    if (!$('contacts_filter')){
      return ;
    }
    $('contacts_filter').addEvent('keyup', function(){
      self.keyword = this.value;
      self.search_contacts();
    });

    var contacts = [];
    for (var i = 0; i < this.contacts.length; i++){
      contacts.push(parseInt(this.contacts[i]));
    }

    this.contacts = contacts;
  },

  search_contacts: function(){
    var self = this;
    var selector = "";

    if (this.list_type == 'selected'){
      selector = '#he_contacts_list .active';
      $$('#he_contacts_list .item').removeClass('visible');
      $$('#he_contacts_list .item').removeClass('hidden');
      $$('#he_contacts_list .item').addClass('hidden');
      $$('#he_contacts_list .active').addClass('visible');
      $$('#he_contacts_list .active').removeClass('hidden');
    }else{
      selector = '#he_contacts_list .item';
    }
    var $items = $$(selector);

    $items.each(function($item){
      if (!$item.getElement('span.name').get('html').test(self.keyword, 'i')){
        self.hide($item);
      }else{
        self.show($item);
      }
    });

    self.no_result();
  },

  choose_contact : function( contact_id ){
    if ($("contact_" + contact_id).hasClass("disabled")){
      return ;
    }

    if( this.contacts.indexOf(contact_id) == -1 ) { //add contact
      $("contact_" + contact_id).addClass("active");
      this.contacts[this.contacts.length] = contact_id;
    }
    else { //remove contact
      $("contact_" + contact_id).removeClass("active");
      this.contacts.splice(this.contacts.indexOf(contact_id), 1);
      if (this.list_type == 'selected'){
        this.hide($("contact_" + contact_id), true);
      }
    }

    var self = this;

		if ($('selected_contacts_count') != undefined){
			$('selected_contacts_count').set('text', self.contacts.length)
		}

    setTimeout(function(){self.no_result();}, 650);
  },

  choose_all_contacts : function($el){

    var $list_items = $('he_contacts_list').getChildren('a');

    for (var i in $list_items)
    {
      if (typeof $list_items[i].getProperty == 'function'){

        $id = parseInt($list_items[i].getProperty('id').substr(8));

        if($el.checked){
          if( this.contacts.indexOf($id) == -1 ){
            this.choose_contact($id);
          }

          if ($('checkbox_'+$id) != undefined && !$('checkbox_'+$id).checked){
            $('checkbox_'+$id).checked = true;
          }
        }
        else
        if( this.contacts.indexOf($id) != -1 ){
            this.choose_contact($id);

            if ($('checkbox_'+$id) != undefined && $('checkbox_'+$id).checked){
              $('checkbox_'+$id).checked = false;
            }
        }
      }
    }

		if ($('selected_contacts_count') != undefined){
			$('selected_contacts_count').set('text', this.contacts.length)
		}
  },

  add_class: function($el, css_class, $el2){
    $el.addClass(css_class);
    $el2.removeClass(css_class);
  },

  no_result: function(){
    if ($$('.visible').length == 0){
      this.show($('no_result'));
    }else{
      this.hide($('no_result'));
    }
  },

  send: function(){
    eval("window.parent."+this.callback+"(["+this.contacts.toString()+"])");
    window.parent.Smoothbox.close();
  },

  hide: function($element, fx){
    var func = function(){
      if (!$element.hasClass('hidden')){
        $element.addClass('hidden');
      }
      $element.removeClass('visible');
    }

    if (fx){
      setTimeout(func, 650);
      $element.tween('opacity', [1, 0]);
    }else{
      $element.setStyle('visibility', 'hidden');
      $element.setStyle('opacity', 0);
      func();
    }
  },

  show: function($element, fx){
    var func = function(){
      if (!$element.hasClass('visible')){
        $element.addClass('visible');
      }
      $element.removeClass('hidden');
      if ($element.hasClass('disabled')){
        $element.setStyle('opacity', .5);
      }
    }

    if (fx){
      setTimeout(func, 650);
      if ($element.hasClass('disabled')){
        $element.tween('opacity', [0, .5]);
      }else{
        $element.tween('opacity', [0, 1]);
      }
    }else{
      $element.setStyle('visibility', 'visible');
      $element.setStyle('opacity', 1);
      func();
    }
  },

  showElements: function($elements){
    var self = this;
    $elements.each(function($element){
      self.show($element);
    });
  },

  hideElements: function($elements){
    var self = this;
    $elements.each(function($element){
      self.hide($element);
    });
  },

  select: function(type){
    var self = this;

    if (this.list_type == type){
      return ;
    }
    this.list_type = type;

    this.search_contacts();
  }

}

function form_redirect_level(url, $formElement)
{
  $$($formElement.form.elements).each(function($el){
    $el.disabled = true;
  });

  window.location.href = url + '?level_id=' + $formElement.value;
}

var he_list = {

  list_type: '',
  keyword: '',
  page: 1,
  ajax_url: '',
  module: '',
  list: '',
  params: {},
  onLoad: false,
  onClose: false,
  width: 550,
  height: 390,

  box: function(module, list, title, params){
    this.params = params;
    var self = this;
    var not_logged_in = 0;
    var query = object_to_query_string(params);
    var $el = new Element('a', {'href': 'hecore/index/list?m='+module+'&l='+list+'&t='+title+'&nli='+not_logged_in+query, 'class': 'smoothbox'});

    Smoothbox.open($el, {'mode': 'Request', width: this.width, height: this.height, 'onLoad': function() {
      self.init();
      if (self.onLoad) {
        self.onLoad();
        self.onLoad = false;
      }

      if (self.onClose) {
        self.onClose();
        self.onClose = false;
      }
    }});
  },

  init: function(){
    var self = this;

    $('TB_ajaxContent').addClass('hecore_he_list_window');

    $('list_filter_btn').addEvent('click', function(){
      self.page = 1;
      self.get_items();
    });

    $('list_filter').addEvent('keydown', function(event){
      if(event.key == 'enter') {
        self.page = 1;
        self.get_items();
      }
    });
  },

  select: function(list_type) {
    if (list_type == this.list_type){
      return ;
    }

    this.keyword = '';
    this.page = 1;
    this.list_type = list_type;
    this.get_items();
  },

  get_items: function() {
    var self = this;
    this.keyword = $('list_filter').value;
    $('he_list').innerHTML = '';
    $('he_contacts_loading').setStyle('display', 'block');
    if (this.params.list_type){
      this.params.list_type = self.list_type;
    }
    var query = object_to_query_string(this.params);
    new Request.JSON({
      'url' : self.ajax_url+'?nocache='+Math.random()+query,
      'method' : 'post',
      'data' : {
        'format' : 'json',
        'keyword' : self.keyword,
        'list_type' : self.list_type,
        'p' : self.page,
        'm' : self.module,
        'l' : self.list
      },
      onSuccess: function(response){
        $('he_list').innerHTML = response.html;
        $('he_contacts_loading').setStyle('display', 'none');
      }
    }).send();
  },

  set_page: function(page){
    this.page = page;
    this.get_items();
  }
}

function he_show_confirm(title, message, callback, options)
{
  if (window.$he_confirm_container == undefined) {
    window.$he_confirm_container = new Element('div', {'class': 'he_confirm_container'});
    var $link = window.$he_confirm_container;

    var $title = new Element('div', {'class': 'he_confirm_title'});
    var $description = new Element('div', {'class': 'he_confirm_desc'});

    if (typeof(en4.core.language.translate) == 'undefined') {
      var confirm_label = language.translate('Confirm');
      var or_label = language.translate('or');
      var cancel_label = language.translate('Cancel');
    } else {
      var confirm_label = en4.core.language.translate('Confirm');
      var or_label = en4.core.language.translate('or');
      var cancel_label = en4.core.language.translate('Cancel');
    }

    var $tools = new Element('div', {'class': 'he_confirm_tools'});
    var $confirm_btn = new Element('button', {'class': 'confirm_btn', 'html': confirm_label});
    var $or_text = new Element('span', {'class': 'or_btn', 'html': or_label});
    var $cancel_btn = new Element('a', {'class': 'cancel_btn', 'href': 'javascript://', 'html': cancel_label});

    $tools.adopt($confirm_btn, $or_text, $cancel_btn);
    $link.adopt($title, $description, $tools);

    var $hidden_cont = new Element('div', {'class': 'display_none'});
    $hidden_cont.adopt($link);
    $(document.body).adopt($hidden_cont);
  }

  var $link = window.$he_confirm_container;

  if (title && title.length > 0) {
    $link.getElement('.he_confirm_title').removeClass('display_none').set('html', title);
  } else {
    $link.getElement('.he_confirm_title').addClass('display_none');
  }

  if (message && message.length > 0) {
    $link.getElement('.he_confirm_desc').removeClass('display_none').set('html', message);
  } else {
    $link.getElement('.he_confirm_desc').addClass('display_none');
  }

  if (options && options.confirm_label != undefined)
    $link.getElement('.he_confirm_tools .confirm_btn').set('html', options.confirm_label);
  if (options && options.cancel_label != undefined)
    $link.getElement('.he_confirm_tools .cancel_btn').set('html', options.cancel_label);
  if (options && options.or_label != undefined)
    $link.getElement('.he_confirm_tools .or_btn').set('html', options.or_label);

  var width = (options && options.width) ? options.width : 500;
  var height = (options && options.height) ? options.height : 100;

  Smoothbox.open($link, {mode: 'Inline', width: width, height: height});

  $('TB_ajaxContent').getElement('.he_confirm_tools .cancel_btn').addEvent('click', function() {
    Smoothbox.close();
  });

  if (callback && typeof(callback) == 'function') {
    $('TB_ajaxContent').getElement('.he_confirm_tools .confirm_btn').addEvent('click', function() {
      Smoothbox.close();
      callback();
    });
  }
}

var he_friend = {

  link: 'settings_toggler',
  $link: null,

  form: 'settings_form',
  $form: null,

  container: 'he_friend_conatainer',
  $container: null,

  ipp: 'friends_ipp',
  $ipp: null,

  loader: 'he_friends_loader',
  $loader: null,

  privacy: 'friends_privacy',
  $privacy: null,

  list: 'friend_list',
  $list: null,

  url: {},

  init: function(){
    var self = this;
    this.$link = $(this.link);
    this.$form = $(this.form);
    this.$container = $(this.container);
    this.$ipp = $(this.ipp);
    this.$loader = $(this.loader);
    this.$privacy = $(this.privacy);
    this.$list = $(this.list);

    this.$link.addEvent('click', function(){
      self.toggle_settings();
    });

    this.$link.addEvent('focus', function(){
      this.blur();
    });

    this.$ipp.addEvent('change', function(){
      self.change_ipp(this.value);
    });

    this.$privacy.addEvent('change', function(){
      self.change_privacy(this.value);
    });

    this.$list.addEvent('click', function(){
      self.list_box();
    });
  },

  list_box: function(){
    he_contacts.box('hecore', 'getFriends', 'he_friend.save_friends', en4.core.language.translate('Choose Friends'),
      {
        'object': window.en4.core.subject.type,
        'object_id': window.en4.core.subject.id
      },
    0);
  },

  see_all: function(list_type){
    he_list.box('hecore', 'getMutualFriends', en4.core.language.translate('Friends'),
      {
        'object': window.en4.core.subject.type,
        'object_id': window.en4.core.subject.id,
        'list_type': list_type
      }
    );
  },

  toggle_settings: function(){
    if (this.$link.getParent().hasClass('active')){
      this.$link.getParent().removeClass('active');
    }else{
      this.$link.getParent().addClass('active');
    }
    if (this.$form.hasClass('hidden')){
      this.$form.removeClass('hidden');
    }else{
      this.$form.addClass('hidden');
    }
  },

  change_ipp: function(value){
    var self = this;
    var url = this.url.change_ipp;
    this.show_loader();

    new Request.JSON({
      'url': url+'?&rand='+Math.random(),
      'method': 'post',
      'data': {
        'format': 'json',
        'value': value
      },
      onSuccess: function(response){
        self.$container.set('html', response.html);
        self.hide_loader();
      }
    }).send();
  },

  change_privacy: function(value){
    var self = this;
    var url = this.url.change_privacy;
    this.$privacy.disabled = true;

    new Request.JSON({
      'url': url+'?&rand='+Math.random(),
      'method': 'post',
      'data': {
        'format': 'json',
        'value': value
      },
      onSuccess: function(response){
        self.$privacy.disabled = false;
      }
    }).send();
  },

  show_loader: function(){
    this.$loader.removeClass('hidden');
    this.$container.addClass('hidden');
  },

  hide_loader: function(){
    this.$loader.addClass('hidden');
    this.$container.removeClass('hidden');
  },

  save_friends: function(user_ids){
    var self = this;
    var url = this.url.save_list;
    this.show_loader();

    new Request.JSON({
      'url': url+'?&rand='+Math.random(),
      'method': 'post',
      'data': {
        'format': 'json',
        'value': user_ids
      },
      onSuccess: function(response){
        self.$container.set('html', response.html);
        self.hide_loader();
      }
    }).send();
  }

}

var HETips = new Class({

	Implements : [Events, Options],

	options : {
		url : '',
		ajax : true,
		cache : true,
		delay : 300,
		className : 'he-tool-tip',
		id : 'he-tool-tip-id',
		display : 'top',
		htmlElement : '',
		visibleOnHover : true
		/*
		 * onShow : $event
		 * onHide : $event
		 * onRequestSuccess : $event
		 */
	},

	elements : null,

	cache : {},

	tip : null,

	timeout : null,

	block : false,

	request : null,

	show : true,

	initialize : function(elements, options) {
		try {
			if (Hetips && !Hetips.isCustomTipsEnabled(elements, options)) { return false; }
		} catch (e){}

		this.elements = elements;
		this.setOptions(options);
		this.tip = this.getTipBlock();
		this.bind();
	},

	getTipBlock : function() {
		var self = this;

		if ($(this.options.id)) {
			return $(this.options.id);
		}

		var $container = new Element('div', {'class':self.options.className + ' hidden', 'id':self.options.id, 'style':'position:absolute;'});
		var $header = new Element('div', {'class':'he-tip-title'});
		var $content = new Element('div', {'class':'he-tip'});
		var $footer = new Element('div', {'class':'he-tip-footer'});
		var $clr = new Element('div', {'class':'clr'});

		$container.appendChild($header);
		$container.appendChild($content);
		$container.appendChild($footer);
		$container.appendChild($clr);

		$$('body')[0].appendChild($container);

		return $container;
	},

	bind : function() {
		var self = this;
		$$(this.elements).each(function($item){
			self.bindItem($item);
		});
	},

	bindItem : function($item) {
		var self = this;
		$item = $($item);
		$item.addEvents({

			'mouseover' : function() {
				var id = this.id;
				var element = this;
				self.show = true;
				self.timeout = window.setTimeout(function() {
					self.hint(id, element);
				}, self.options.delay);
			},

			'mouseout' : function() {
				window.clearTimeout(self.timeout);
				self.show = false;
				self.timeout = window.setTimeout(function() {
					self.hideTip();
				}, self.options.delay);
			}

		});
	},

	hint : function(id, element) {
		var self = this;

		if (this.options.htmlElement) {
			this.show = true;
			var html = $(element).getParent().getElement(this.options.htmlElement).innerHTML;
			this.showTip(html, element);
			this.block = false;
			return ;
		}

		if (this.cache[id] && this.options.cache) {
			this.show = true;
			this.showTip(this.cache[id], element);
			this.block = false;
			return ;
		}

		if (this.block && this.options.ajax) {
			return ;
		}

		this.block = true;
    self.showTip('', element);
		this.request = new Request.JSON({
			'method' : 'post',
			'url' : self.options.url + '?rand='+Math.random(),
			'data' : {
				'id' : id,
				'format' : 'json'
			},
			onSuccess : function(response) {
				self.cache[id] = response.html;
				self.showTip(response.html, element);
				self.block = false;
				self.fireEvent('onRequestSuccess', [response, element]);
			}
		}).send();
	},

  getPos : function($element) {
    var data = $($element).getPosition();
    return data;
  },

	showTip : function(html, element) {
    if (html == '') {
      html = '<span class="loader_icon">&nbsp;</span>';
      this.tip.addClass('he_loader');
    } else {
      this.tip.removeClass('he_loader');
    }
		this.tip = $(this.tip);
		element = $(element);

    var data = this.getPos(element);
		var x = data.x;
		var y = data.y;

		this.tip.getElements('.he-tip')[0].set('html', html);
    this.tip.style.left = x+'px';
    this.tip.style.top = y+'px';

		if (html != '' && this.show) {
			this.tip.removeClass('hidden');
		}
    this.tip.style.top = (parseInt(this.tip.style.top) - $(this.tip).getSize().y)+'px';

		var self = this;
		this.tip
			.removeEvents('mouseover')
			.removeEvents('mouseout')
			.addEvents({
				'mouseover' : function() {
					if (self.options.visibleOnHover) {
						window.clearTimeout(self.timeout);
					}
				},
				'mouseout' : function() {
					self.timeout = window.setTimeout(function(){
						self.hideTip();
					}, self.options.delay);
				}
			});

		this.fireEvent('onShow', [this.tip, element]);
	},

	hideTip : function() {
		this.tip.addClass('hidden');
		this.fireEvent('onHide', [this.tip]);
	},

  // Deprecated function need to fix
	getDocHeight : function() {
		var D = document;
		return Math.max(
			Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
			Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
			Math.max(D.body.clientHeight, D.documentElement.clientHeight)
		);
	}

});