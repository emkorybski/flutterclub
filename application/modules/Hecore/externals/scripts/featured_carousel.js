
/* $Id: feature_carousel.js 2010-05-25 01:44 michael $ */

function he_featured_carousel(el)
{
  this.init(el);
}

he_featured_carousel.prototype = {

  item_width: 85,
  rows: 5,
  duration: 500,
  type: 'middle_1',

  _navigation_process: false,

  init: function (el){

    var self = this;

    self.$container = $(el);

    if (self.$container.getParent('.layout_left') || self.$container.getParent('.layout_right')){
      self.type = 'sidebar';
    } else  {
      var $middle = self.$container.getParent('.layout_middle');
      if ($middle){
        var width = $middle.getSize().x;
        if (width <= 550){
          self.type = 'middle_1';
        } else if (width <=  800){
          self.type = 'middle_2';
        } else if (width <= 970){
          self.type = 'middle_3';
        }
      }
    }

    if (self.type == 'sidebar'){
      self.rows = 1;
      self.$container.addClass('sidebar');
    } else if (self.type == 'middle_1'){
      self.rows = 5;
      self.$container.addClass('middle_1');
    } else if (self.type == 'middle_2'){
      self.rows = 8;
      self.item_width = 81;
      self.$container.addClass('middle_2');
    } else if (self.type == 'middle_3'){
      self.rows = 10;
      self.$container.addClass('middle_3');
    }

    self.$items = self.$container.getElements('.item');
    self.count_items = self.$items.length;

    self.$list = self.$container.getElement('.list');
    if (self.$list){ self.$list.setStyle('width', ( self.item_width * self.count_items )); }

    var list_width = ( self.item_width * self.rows );

    self.$listing = self.$container.getElement('.listing');
    if (self.$listing){ self.$listing.setStyle('width', list_width); }

    var $content = self.$container.getElement('.content');
    if ($content){ $content.setStyle('width', list_width+50); }

    self.$container.getElements('.prev').addEvent('click', function (){ self.toggle(self.rows); });
    self.$container.getElements('.next').addEvent('click', function (){ self.toggle(-self.rows); });

    self.$container.getElements('.prev').addClass('disabled');
    if (self.count_items <= self.rows){ self.$container.getElements('.next').addClass('disabled'); }

    self.$thumbnails = self.$container.getElements('.thumbnail img');

    for (var i=0; i<self.$thumbnails.length; i++){

      var $item = self.$thumbnails[i];
      if (!$item || typeof($item) != 'object'){ continue; }

      var $parent = $item.getParent('.item');

      if ($parent){
        var $title = $parent.getElement('.tip_title');
        var $text = $parent.getElement('.tip_text');

        if ($title && $text){
          $item.store('tip:title', $title.getProperty('html'));
          $item.store('tip:text', $text.getProperty('html'));
        }
      }

    }

    var tips = new Tips(self.$thumbnails, {'className': 'he_carousel_list_tips'});
  },

  toggle: function (direction){

    var self = this;

    if (!self._navigation_process || !self.$list){

      self._navigation_process = true;
      self.$container.getElements('.prev, .next').addClass('process');

      var newLeft = self.$list.getStyle('left').toInt() + (direction*self.item_width);
      var rowWidth = self.item_width * self.rows;
      var listWidth = self.item_width * self.count_items;

      var $prev = self.$container.getElements('.prev');
      var $next = self.$container.getElements('.next');

      if (newLeft <= 0 && newLeft > -(listWidth)){

        if (newLeft >= 0){ $prev.addClass('disabled'); } else { $prev.removeClass('disabled'); }
        if (newLeft-rowWidth <= -(listWidth)){ $next.addClass('disabled'); } else { $next.removeClass('disabled');  }

        self.$list.set('tween', {duration: self.duration});
        self.$list.tween('left', newLeft);
      }

      setTimeout(function (){

        self._navigation_process = false;
        self.$container.getElements('.prev, .next').removeClass('process');

      }, self.duration);

    }

  }

};