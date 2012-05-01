<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9660 2012-03-21 20:55:42Z john $
 * @author     John
 */
?>

<script type="text/javascript">
  (function() { // Start namespace
    
  var Drag = new Class({

      Implements: [Events, Options],

      options: {/*
          onBeforeStart: $empty(thisElement),
          onStart: $empty(thisElement, event),
          onSnap: $empty(thisElement)
          onDrag: $empty(thisElement, event),
          onCancel: $empty(thisElement),
          onComplete: $empty(thisElement, event),*/
          snap: 6,
          unit: 'px',
          grid: false,
          style: true,
          limit: false,
          handle: false,
          invert: false,
          preventDefault: false,
          stopPropagation: false,
          modifiers: {x: 'left', y: 'top'}
      },

      initialize: function(){
          var params = Array.link(arguments, {'options': Object.type, 'element': $defined});
          this.element = document.id(params.element);
          this.document = this.element.getDocument();
          this.setOptions(params.options || {});
          var htype = $type(this.options.handle);
          this.handles = ((htype == 'array' || htype == 'collection') ? $$(this.options.handle) : document.id(this.options.handle)) || this.element;
          this.mouse = {'now': {}, 'pos': {}};
          this.value = {'start': {}, 'now': {}};

          this.selection = (Browser.Engine.trident) ? 'selectstart' : 'mousedown';

          this.bound = {
              start: this.start.bind(this),
              check: this.check.bind(this),
              drag: this.drag.bind(this),
              stop: this.stop.bind(this),
              cancel: this.cancel.bind(this),
              eventStop: $lambda(false)
          };
          this.attach();
      },

      attach: function(){
          this.handles.addEvent('mousedown', this.bound.start);
          return this;
      },

      detach: function(){
          this.handles.removeEvent('mousedown', this.bound.start);
          return this;
      },

      start: function(event){
          if (event.rightClick) return;
          if (this.options.preventDefault) event.preventDefault();
          if (this.options.stopPropagation) event.stopPropagation();
          this.mouse.start = event.page;
          this.fireEvent('beforeStart', this.element);
          var limit = this.options.limit;
          this.limit = {x: [], y: []};
          var styles = this.element.getStyles('left', 'right', 'top', 'bottom');
          this._invert = {
              x: this.options.modifiers.x == 'left' && styles.left == 'auto' &&
                !isNaN(styles.right.toInt()) && (this.options.modifiers.x = 'right'),
              y: this.options.modifiers.y == 'top' && styles.top == 'auto' &&
                !isNaN(styles.bottom.toInt()) && (this.options.modifiers.y = 'bottom')
          };

          var z, coordinates;
          for (z in this.options.modifiers){
              if (!this.options.modifiers[z]) continue;

              var style = this.element.getStyle(this.options.modifiers[z]);

              // Some browsers (IE and Opera) don't always return pixels.
              if (style && !style.match(/px$/)){
                  if (!coordinates) coordinates = this.element.getCoordinates(this.element.getOffsetParent());
                  style = coordinates[this.options.modifiers[z]];
              }

              if (this.options.style) this.value.now[z] = (style || 0).toInt();
              else this.value.now[z] = this.element[this.options.modifiers[z]];

              if (this.options.invert) this.value.now[z] *= -1;
              if (this._invert[z]) this.value.now[z] *= -1;

              this.mouse.pos[z] = event.page[z] - this.value.now[z];

              if (limit && limit[z]){
                  for (var i = 2; i--; i){
                      if ($chk(limit[z][i])) this.limit[z][i] = $lambda(limit[z][i])();
                  }
              }
          }

          if ($type(this.options.grid) == 'number') this.options.grid = {x: this.options.grid, y: this.options.grid};
          this.document.addEvents({mousemove: this.bound.check, mouseup: this.bound.cancel});
          this.document.addEvent(this.selection, this.bound.eventStop);
      },

      check: function(event){
          if (this.options.preventDefault) event.preventDefault();
          var distance = Math.round(Math.sqrt(Math.pow(event.page.x - this.mouse.start.x, 2) + Math.pow(event.page.y - this.mouse.start.y, 2)));
          if (distance > this.options.snap){
              this.cancel();
              this.document.addEvents({
                  mousemove: this.bound.drag,
                  mouseup: this.bound.stop
              });
              this.fireEvent('start', [this.element, event]).fireEvent('snap', this.element);
          }
      },

      drag: function(event){
          if (this.options.preventDefault) event.preventDefault();
          this.mouse.now = event.page;
          for (var z in this.options.modifiers){
              if (!this.options.modifiers[z]) continue;
              this.value.now[z] = this.mouse.now[z] - this.mouse.pos[z];
              if (this.options.invert) this.value.now[z] *= -1;
              if (this._invert[z]) this.value.now[z] *= -1;
              if (this.options.limit && this.limit[z]){
                  if ($chk(this.limit[z][1]) && (this.value.now[z] > this.limit[z][1])){
                      this.value.now[z] = this.limit[z][1];
                  } else if ($chk(this.limit[z][0]) && (this.value.now[z] < this.limit[z][0])){
                      this.value.now[z] = this.limit[z][0];
                  }
              }
              if (this.options.grid[z]) this.value.now[z] -= ((this.value.now[z] - (this.limit[z][0]||0)) % this.options.grid[z]);
              if (this.options.style) {
                  this.element.setStyle(this.options.modifiers[z], this.value.now[z] + this.options.unit);
              } else {
                  this.element[this.options.modifiers[z]] = this.value.now[z];
              }
          }
          this.fireEvent('drag', [this.element, event]);
      },

      cancel: function(event){
          this.document.removeEvent('mousemove', this.bound.check);
          this.document.removeEvent('mouseup', this.bound.cancel);
          if (event){
              this.document.removeEvent(this.selection, this.bound.eventStop);
              this.fireEvent('cancel', this.element);
          }
      },

      stop: function(event){
          this.document.removeEvent(this.selection, this.bound.eventStop);
          this.document.removeEvent('mousemove', this.bound.drag);
          this.document.removeEvent('mouseup', this.bound.stop);
          if (event) this.fireEvent('complete', [this.element, event]);
      }

  });

  Drag.Move = new Class({

      Extends: Drag,

      options: {/*
          onEnter: $empty(thisElement, overed),
          onLeave: $empty(thisElement, overed),
          onDrop: $empty(thisElement, overed, event),*/
          droppables: [],
          container: false,
          precalculate: false,
          includeMargins: true,
          checkDroppables: true
      },

      initialize: function(element, options){
          this.parent(element, options);
          element = this.element;

          this.droppables = $$(this.options.droppables);
          this.container = document.id(this.options.container);

          if (this.container && $type(this.container) != 'element')
              this.container = document.id(this.container.getDocument().body);

          if (this.options.style){
              if (this.options.modifiers.x == "left" && this.options.modifiers.y == "top"){
                  var parentStyles,
                      parent = document.id(element.getOffsetParent());
                  if (parent) parentStyles = parent.getStyles('border-top-width', 'border-left-width');

                  var styles = element.getStyles('left', 'top');
                  if (parent && (styles.left == 'auto' || styles.top == 'auto')){
                      var parentPosition = element.getPosition(parent);
                      parentPosition.x = parentPosition.x - (parentStyles['border-left-width'] ? parentStyles['border-left-width'].toInt() : 0);
                      parentPosition.y = parentPosition.y - (parentStyles['border-top-width'] ? parentStyles['border-top-width'].toInt() : 0);
                      element.setPosition(parentPosition);
                  }
              }
              if (element.getStyle('position') == 'static') element.setStyle('position', 'absolute');
          }

          this.addEvent('start', this.checkDroppables, true);

          this.overed = null;
      },

      start: function(event){
          if (this.container) this.options.limit = this.calculateLimit();

          if (this.options.precalculate){
              this.positions = this.droppables.map(function(el){
                  return el.getCoordinates();
              });
          }

          this.parent(event);
      },

      calculateLimit: function(){
          var offsetParent = document.id(this.element.getOffsetParent()) || document.body,
              containerCoordinates = this.container.getCoordinates(offsetParent),
              containerBorder = {},
              elementMargin = {},
              elementBorder = {},
              containerMargin = {},
              offsetParentBorder = {},
              offsetParentPadding = {};

          ['top', 'right', 'bottom', 'left'].each(function(pad){
              containerBorder[pad] = this.container.getStyle('border-' + pad).toInt();
              elementBorder[pad] = this.element.getStyle('border-' + pad).toInt();
              elementMargin[pad] = this.element.getStyle('margin-' + pad).toInt();
              containerMargin[pad] = this.container.getStyle('margin-' + pad).toInt();
              offsetParentPadding[pad] = offsetParent?offsetParent.getStyle('padding-' + pad).toInt():0;
                          offsetParentBorder[pad] = offsetParent?offsetParent.getStyle('border-' + pad).toInt():0;
          }, this);

          var width = this.element.offsetWidth + elementMargin.left + elementMargin.right,
              height = this.element.offsetHeight + elementMargin.top + elementMargin.bottom,
              left = 0,
              top = 0,
              right = containerCoordinates.right - containerBorder.right - width,
              bottom = containerCoordinates.bottom - containerBorder.bottom - height;

          if (this.options.includeMargins){
              left += elementMargin.left;
              top += elementMargin.top;
          } else {
              right += elementMargin.right;
              bottom += elementMargin.bottom;
          }

          if (this.element.getStyle('position') == 'relative'){
              var coords = this.element.getCoordinates(offsetParent);
              coords.left -= this.element.getStyle('left').toInt();
              coords.top -= this.element.getStyle('top').toInt();

              left += containerBorder.left - coords.left;
              top += containerBorder.top - coords.top;
              right += elementMargin.left - coords.left;
              bottom += elementMargin.top - coords.top;

              if (this.container != offsetParent){
                  left += containerMargin.left + offsetParentPadding.left;
                  top += (Browser.Engine.trident4 ? 0 : containerMargin.top) + offsetParentPadding.top;
              }
          } else {
              left -= elementMargin.left;
              top -= elementMargin.top;
              if (this.container == offsetParent){
                  right -= containerBorder.left;
                  bottom -= containerBorder.top;
              } else {
                  left += containerCoordinates.left + containerBorder.left - offsetParentBorder.left;
                  top += containerCoordinates.top + containerBorder.top - offsetParentBorder.top;
                  right -= offsetParentBorder.left;
                  bottom -= offsetParentBorder.top;
              }
          }

          return {
              x: [left, right],
              y: [top, bottom]
          };
      },

      checkAgainst: function(el, i){
          el = (this.positions) ? this.positions[i] : el.getCoordinates();
          var now = this.mouse.now;
          return (now.x > el.left && now.x < el.right && now.y < el.bottom && now.y > el.top);
      },

      checkDroppables: function(){
          var overed = this.droppables.filter(this.checkAgainst, this).getLast();
          if (this.overed != overed){
              if (this.overed) this.fireEvent('leave', [this.element, this.overed]);
              if (overed) this.fireEvent('enter', [this.element, overed]);
              this.overed = overed;
          }
      },

      drag: function(event){
          this.parent(event);
          if (this.options.checkDroppables && this.droppables.length) this.checkDroppables();
      },

      stop: function(event){
          this.checkDroppables();
          this.fireEvent('drop', [this.element, this.overed, event]);
          this.overed = null;
          return this.parent(event);
      }

  });

  var Sortables = new Class({

      Implements: [Events, Options],

      options: {/*
          onSort: $empty(element, clone),
          onStart: $empty(element, clone),
          onComplete: $empty(element),*/
          snap: 4,
          opacity: 1,
          clone: false,
          revert: false,
          handle: false,
          constrain: false,
          preventDefault: false
      },

      initialize: function(lists, options){
          this.setOptions(options);
          this.elements = [];
          this.lists = [];
          this.idle = true;

          this.addLists($$(document.id(lists) || lists));
          if (!this.options.clone) this.options.revert = false;
          if (this.options.revert) this.effect = new Fx.Morph(null, $merge({duration: 250, link: 'cancel'}, this.options.revert));
      },

      attach: function(){
          this.addLists(this.lists);
          return this;
      },

      detach: function(){
          this.lists = this.removeLists(this.lists);
          return this;
      },

      addItems: function(){
          Array.flatten(arguments).each(function(element){
              this.elements.push(element);
              var start = element.retrieve('sortables:start', this.start.bindWithEvent(this, element));
              (this.options.handle ? element.getElement(this.options.handle) || element : element).addEvent('mousedown', start);
          }, this);
          return this;
      },

      addLists: function(){
          Array.flatten(arguments).each(function(list){
              this.lists.push(list);
              this.addItems(list.getChildren());
          }, this);
          return this;
      },

      removeItems: function(){
          return $$(Array.flatten(arguments).map(function(element){
              this.elements.erase(element);
              var start = element.retrieve('sortables:start');
              (this.options.handle ? element.getElement(this.options.handle) || element : element).removeEvent('mousedown', start);

              return element;
          }, this));
      },

      removeLists: function(){
          return $$(Array.flatten(arguments).map(function(list){
              this.lists.erase(list);
              this.removeItems(list.getChildren());

              return list;
          }, this));
      },

      getClone: function(event, element){
          if (!this.options.clone) return new Element(element.tagName).inject(document.body);
          if ($type(this.options.clone) == 'function') return this.options.clone.call(this, event, element, this.list);
          var clone = element.clone(true).setStyles({
              margin: '0px',
              position: 'absolute',
              visibility: 'hidden',
              'width': element.getStyle('width')
          });
          //prevent the duplicated radio inputs from unchecking the real one
          if (clone.get('html').test('radio')) {
              clone.getElements('input[type=radio]').each(function(input, i) {
                  input.set('name', 'clone_' + i);
                  if (input.get('checked')) element.getElements('input[type=radio]')[i].set('checked', true);
              });
          }

          return clone.inject(this.list).setPosition(element.getPosition(element.getOffsetParent()));
      },

      getDroppables: function(){
          var droppables = this.list.getChildren();
          if (!this.options.constrain) droppables = this.lists.concat(droppables).erase(this.list);
          return droppables.erase(this.clone).erase(this.element);
      },

      insert: function(dragging, element){
          var where = 'inside';
          if (this.lists.contains(element)){
              this.list = element;
              this.drag.droppables = this.getDroppables();
          } else {
              where = this.element.getAllPrevious().contains(element) ? 'before' : 'after';
          }
          this.element.inject(element, where);
          this.fireEvent('sort', [this.element, this.clone]);
      },

      start: function(event, element){
          if (
              !this.idle ||
              event.rightClick ||
              ['button', 'input'].contains(document.id(event.target).get('tag'))
          ) return;

          this.idle = false;
          this.element = element;
          this.opacity = element.get('opacity');
          this.list = element.getParent();
          this.clone = this.getClone(event, element);

          this.drag = new Drag.Move(this.clone, {
              preventDefault: this.options.preventDefault,
              snap: this.options.snap,
              container: this.options.constrain && this.element.getParent(),
              droppables: this.getDroppables(),
              onSnap: function(){
                  event.stop();
                  this.clone.setStyle('visibility', 'visible');
                  this.element.set('opacity', this.options.opacity || 0);
                  this.fireEvent('start', [this.element, this.clone]);
              }.bind(this),
              onEnter: this.insert.bind(this),
              onCancel: this.reset.bind(this),
              onComplete: this.end.bind(this)
          });

          this.clone.inject(this.element, 'before');
          this.drag.start(event);
      },

      end: function(){
          this.drag.detach();
          this.element.set('opacity', this.opacity);
          if (this.effect){
              var dim = this.element.getStyles('width', 'height');
              var pos = this.clone.computePosition(this.element.getPosition(this.clone.getOffsetParent()));
              this.effect.element = this.clone;
              this.effect.start({
                  top: pos.top,
                  left: pos.left,
                  width: dim.width,
                  height: dim.height,
                  opacity: 0.25
              }).chain(this.reset.bind(this));
          } else {
              this.reset();
          }
      },

      reset: function(){
          this.idle = true;
          this.clone.destroy();
          this.fireEvent('complete', this.element);
      },

      serialize: function(){
          var params = Array.link(arguments, {modifier: Function.type, index: $defined});
          var serial = this.lists.map(function(list){
              return list.getChildren().map(params.modifier || function(element){
                  return element.get('id');
              }, this);
          }, this);

          var index = params.index;
          if (this.lists.length == 1) index = 0;
          return $chk(index) && index >= 0 && index < this.lists.length ? serial[index] : serial;
      }

  });

  NestedDragMove = new Class({
    Extends : Drag.Move,
    
    checkDroppables: function() {
      //var overed = this.droppables.filter(this.checkAgainst, this).getLast();
      var overedMulti = this.droppables.filter(this.checkAgainst, this);
      overedMulti = overedMulti.filter(function(el) {
        return el && 'get' in el && 
          (el.get('tag') == 'ul' || el.get('tag') == 'li') &&
          el != this.element && el != this.clone;
      }.bind(this));
      //console.log(overedMulti);
      
      // Pick the smallest one
      var overed;
      var smallestOvered = false;
      var overedSizes = [];
      overedMulti.each(function(currentOvered, index) {
        var overedSize = currentOvered.getSize().x * currentOvered.getSize().y;
        if( smallestOvered === false || overedSize < smallestOvered ) {
          overed = currentOvered;
          smallestOvered = overedSize;
        }
      });
      
      if (this.overed != overed){
        if (this.overed) {
          this.fireEvent('leave', [this.element, this.overed]);
        }
        if (overed) {
          this.fireEvent('enter', [this.element, overed]);
        }
        this.overed = overed;
      }
    }
  });


  NestedSortables = new Class({
    Extends : Sortables,

    getDroppables: function(){
            var droppables = this.list.getChildren('ul, li');
//            var droppables = new Elements();
//            $$(this.lists).each(function(el) {
//              droppables.combine(el);
//              droppables.combine(el.getChildren('ul, li'));
//            });
            droppables = droppables.filter(function(el) {
              return el && 'get' in el && 
                (el.get('tag') == 'ul' || el.get('tag') == 'li') &&
                el != this.element && el != this.clone;
            }.bind(this));
            if (!this.options.constrain) {
              droppables = this.lists.concat(droppables);
              if( !this.list.hasClass('sortablesForceInclude') ) droppables.erase(this.list);
            }
            return droppables.erase(this.clone).erase(this.element);
    },
    
    start: function(event, element){
            if (!this.idle) return;
            this.idle = false;
            this.element = element;
            this.opacity = element.get('opacity');
            this.list = element.getParent();
            this.clone = this.getClone(event, element);
            
            this.drag = new NestedDragMove(this.clone, {
                    snap: this.options.snap,
                    container: this.options.constrain && this.element.getParent(),
                    droppables: this.getDroppables(),
                    onSnap: function(){
                            event.stop();
                            this.clone.setStyle('visibility', 'visible');
                            this.element.set('opacity', this.options.opacity || 0);
                            this.fireEvent('start', [this.element, this.clone]);
                    }.bind(this),
                    onEnter: this.insert.bind(this),
                    onCancel: this.reset.bind(this),
                    onComplete: this.end.bind(this)
            });

            this.clone.inject(this.element, 'before');
            this.drag.start(event);
    },

    insert : function(dragging, element) {
      if( this.element.hasChild(element) ) return;
      //this.parent(dragging, element);
      
      //insert: function(dragging, element){
      var where = 'inside';
      if (this.lists.contains(element)){
        if( element.hasClass('sortablesForceInclude') && element == this.list ) return;
        this.list = element;
        this.drag.droppables = this.getDroppables();
      } else {
        where = this.element.getAllPrevious().contains(element) ? 'before' : 'after';
      }
      this.element.inject(element, where);
      this.fireEvent('sort', [this.element, this.clone]);
      //},
    }
  })
  
  })(); // end namespace;
</script>

<script type="text/javascript">
  var currentPage = '<?php echo $this->page ?>';
  var newContentIndex = 1;
  var currentParent;
  var currentNextSibling;
  var contentByName = <?php echo Zend_Json::encode($this->contentByName) ?>;
  var currentModifications = [];
  var currentLayout = '<?php echo $this->pageObject->layout ?>';
  var ContentSortables;
  var ContentTooltips;

  window.onbeforeunload = function(event) {
    if( currentModifications.length > 0 ) {
      return '<?php echo $this->string()->escapeJavascript($this->translate(' - All unsaved changes to pages or widgets will be lost - ')) ?>'
      //return 'I\'m sorry Dave, I can\'t do that.';
    }
  }

  /* modifications */
  var pushModification = function(type) {
    if( !currentModifications.contains(type) ) {
      currentModifications.push(type);

      // Add CSS class for save button while active modifications
      if( type == 'info' ) {
        $('admin_layoutbox_menu_pageinfo').addClass('admin_content_modifications_active');
      } else if( type == 'main' ) {
        $('admin_layoutbox_menu_savechanges').addClass('admin_content_modifications_active');
      }
    }
  }

  var eraseModification = function(type) {
    currentModifications.erase(type);
    // Remove active notifications CSS class
      if( type == 'info' ) {
        $('admin_layoutbox_menu_pageinfo').removeClass('admin_content_modifications_active');
      } else if( type == 'main' ) {
        $('admin_layoutbox_menu_savechanges').removeClass('admin_content_modifications_active');
      }
  }

  /* Attach javascript to existing elements */
  window.addEvent('load', function() {
    // Add info
    $$('li.admin_content_draggable').each(function(element) {
      var elClass = element.get('class');
      var matches = elClass.match(/admin_content_widget_([^ ]+)/i);
      if( !$type(matches) || !$type(matches[1])) return;
      var name = matches[1];
      var info = contentByName[name] || {};

      element.store('contentInfo', info);

      // Add info for tooltips
      element.store('tip:title', info.title || 'Missing widget: ' + matches[1]);
      element.store('tip:text', info.description || 'Missing widget: ' + matches[1]);
    });

    // Monitor form inputs for changes
    $$('#admin_layoutbox_menu_pageinfo input').addEvent('change', function(event) {
      if( event.target.get('tag') != 'input' ) return;
      pushModification('info');
    });

    // Add tooltips
    ContentTooltips = new Tips($$('ul#column_stock li.admin_content_draggable'), {
      
    });

    // Make sortable
    ContentSortables = new NestedSortables($$('ul.admin_content_sortable'), {
      constrain : false,
      clone: function(event, element, list) {
        var tmp = element.clone(true).setStyles({
          margin: '0px',
          position: 'absolute',
          visibility: 'hidden',
          zIndex: 9000,
          'width': element.getStyle('width')
        }).inject(this.list).setPosition(element.getPosition(element.getOffsetParent()));
        return tmp;
      },
      onStart : function(element, clone) {
        element.addClass('admin_content_dragging');
        currentParent = element.getParent();
        currentNextSibling = element.getNext();
      },
      onComplete : function(element, clone) {
        element.removeClass('admin_content_dragging');
        if( !currentParent ) {
          //alert('missing parent error');
          return;
        }
        
        // If it's coming from stock and going into stock, destroy and insert back into original location
        if( currentParent.hasClass('admin_content_stock_sortable') && element.getParent().hasClass('admin_content_stock_sortable') ) {
          if( currentNextSibling ) {
            element.inject(currentNextSibling, 'before');
          } else {
            element.inject(currentParent);
          }
        }

        // If it's not coming from stock, and going into stock, just destroy it
        else if( element.getParent().hasClass('admin_content_stock_sortable') ) {
          element.destroy();

          // Signal modification
          pushModification('main');
        }

        // If it's coming from stock, and not going into stock, put back into stock, clone, and insert
        else if( currentParent.hasClass('admin_content_stock_sortable') && !element.getParent().hasClass('admin_content_stock_sortable') ) {
          var elClone = element.clone();

          // Make it buildable, add info, and give it a temp id
          elClone.inject(element, 'after');
          elClone.addClass('admin_content_buildable');
          elClone.addClass('admin_content_cell');
          elClone.removeClass('admin_content_stock_draggable');
          elClone.getElement('span').setStyle('display', '');
          // @todo
          elClone.set('id', 'admin_content_new_' + (newContentIndex++));

          // Make it draggable
          ContentSortables.addItems(elClone);

          // Remove tips
          ContentTooltips.detach(elClone);

          // Put original back
          if( currentNextSibling ) {
            element.inject(currentNextSibling, 'before');
          } else {
            element.inject(currentParent);
          }

          // Try to expand special blocks
          expandSpecialBlock(elClone);

          // Check for autoEdit
          checkForAutoEdit(elClone);

          // Signal modification
          pushModification('main');
        }

        // It's coming from cms to cms
        else if( !currentParent.hasClass('admin_content_stock_sortable') && !element.getParent().hasClass('admin_content_stock_sortable') ) {
          // Signal modification
          pushModification('main');
        }
        
        // Something strange happened
        else {
          alert('error in widget placement');
        }

        currentParent = false;
        currentNextSibling = false;
      }
    });

    // Remove disabled stock items
    ContentSortables.removeItems($$('#column_stock li.disabled'));
  });

  /* Lazy confirm box */
  var confirmPageChangeLoss = function() {
    if( currentModifications.length == 0 ) return true; // Don't ask if nothing to lose
    // @todo check if there are any changes that would be lost
    return confirm("<?php echo $this->string()->escapeJavascript($this->translate("Any unsaved changes will be lost. Are you sure you want to leave this page?")); ?>");
  }

  /* Remove widget */
  var removeWidget = function(element) {
    if( !element.hasClass('admin_content_buildable') ) {
      element = element.getParent('.admin_content_buildable');
    }
    element.destroy();

    // Signal modification
    pushModification('main');
  }

  /* Switch the active menu item */
  var switchPageMenu = function(event, activator) {
    var element = activator.getParent('li');
    $$('.admin_layoutbox_menu_generic').each(function(otherElement) {
      var otherWrapper = otherElement.getElement('.admin_layoutbox_menu_wrapper_generic');
      if( otherElement.get('id') == element.get('id') && !otherElement.hasClass('active') ) {
        otherElement.addClass('active');
        otherWrapper.setStyle('display', 'block');
        var firstInput = otherElement.getElement('input');
        if( firstInput ) {
          firstInput.focus();
        }
      } else {
        otherElement.removeClass('active');
        otherWrapper.setStyle('display', 'none');
      }
    });
  }

  /* Load a different page */
  var loadPage = function(page_id) {
    if( confirmPageChangeLoss() ) {
      window.location.search = '?page=' + page_id;
      //window.location = window.location.href
    }
  }

  /* Save current page changes */
  var saveChanges = function()
  {
    var data = [];
    $$('.admin_content_buildable').each(function(element) {
      var parent = element.getParent('.admin_content_buildable');

      var elData = {
        'element' : {},
        'parent' : {},
        'info' : {},
        'params' : {}
      };

      // Get element identity
      elData.element.id = element.get('id');
      if( elData.element.id.indexOf('admin_content_new_') === 0 ) {
        elData.tmp_identity = elData.element.id.replace('admin_content_new_', '');
      } else {
        elData.identity = elData.element.id.replace('admin_content_', '');
      }

      // Get element class
      elData.element.className = element.get('class');

      // Get element type and name
      if( element.hasClass('admin_content_cell') ) {
        var m = element.get('class').match(/admin_content_widget_([^ ]+)/i);
        if( $type(m) && $type(m[1]) ) {
          elData.type = 'widget';
          elData.name = m[1];
        }
      } else if( element.hasClass('admin_content_block') ) {
        var m = element.get('class').match(/admin_content_container_([^ ]+)/i);
        if( $type(m) && $type(m[1]) ) {
          elData.type = 'container';
          elData.name = m[1];
        }
      } else if( element.hasClass('admin_content_column') ) {
        var m = element.get('class').match(/admin_content_container_([^ ]+)/i);
        if( $type(m) && $type(m[1]) ) {
          elData.type = 'container';
          elData.name = m[1];
        }
      } else {
        
      }


      if( parent ) {
        // Get parent identity
        elData.parent.id = parent.get('id');
        if( elData.parent.id.indexOf('admin_content_new_') === 0 ) {
          elData.parent_tmp_identity = elData.parent.id.replace('admin_content_new_', '');
        } else {
          elData.parent_identity = elData.parent.id.replace('admin_content_', '');
        }
      }

      elData.info = element.retrieve('contentInfo');
      elData.params = (element.retrieve('contentParams') || {params:{}}).params;

      // Merge with defaults
      if( $type(contentByName[elData.name]) && $type(contentByName[elData.name].defaultParams) ) {
        elData.params = $merge(contentByName[elData.name].defaultParams, elData.params);
      }
      
      data.push(elData);
    });

    var url = '<?php echo $this->url(array('action' => 'update', 'controller' => 'content', 'module' => 'core'), 'admin_default', true)?>';
    var request = new Request.JSON({
      'url' : url,
      'data' : {
        'format' : 'json',
        'page' : currentPage,
        'structure' : '(' + JSON.encode(data) + ')',
        'layout' : currentLayout
      },
      onComplete : function(responseJSON) {
        $H(responseJSON.newIds).each(function(data, index) {
          var newContentEl = $('admin_content_new_' + index);
          if( !newContentEl ) throw "missing new content el";
          newContentEl.set('id', 'admin_content_' + data.identity);
          newContentEl.store('contentParams', data);
        });
        eraseModification('main');
        alert('<?php echo $this->string()->escapeJavascript($this->translate("Your changes to this page have been saved.")) ?>');
      }
    });

    request.send();
  }

  /* Open the edit page for a widget */
  var currentEditingElement;
  var openWidgetParamEdit = function(name, element) {
    //event.stop();
    
    currentEditingElement = $(element);
    var content_id;
    if( element.get('id').indexOf('admin_content_new_') !== 0 && element.get('id').indexOf('admin_content_') === 0 ) {
      content_id = element.get('id').replace('admin_content_', '');
    }

    var url = '<?php echo $this->url(array('action' => 'widget', 'controller' => 'content', 'module' => 'core'), 'admin_default', true)?>';
    var urlObject = new URI(url);

    var fullParams = element.retrieve('contentParams');
    if( $type(fullParams) && $type(fullParams.params) ) {
      //urlObject.setData(fullParams.params);
    }

    urlObject.setData({'name' : name}, true);

    Smoothbox.open(urlObject.toString());
  }

  var pullWidgetParams = function() {
    if( currentEditingElement ) {
      var fullParams = currentEditingElement.retrieve('contentParams');
      if( $type(fullParams) && $type(fullParams.params) ) {
        return fullParams.params;
      }
    }
    return {};
  }

  var pullWidgetTypeInfo = function() {
    if( currentEditingElement ) {
      var info = currentEditingElement.retrieve('contentInfo');
      if( $type(info) ) {
        return info;
      }
    }
    return {};
  }

  /* Set the params in the widget */
  var setWidgetParams = function(params) {
    if( !currentEditingElement ) return;
    var oldParams = currentEditingElement.retrieve('contentParams') || {};
    oldParams.params = params
    currentEditingElement.store('contentParams', oldParams);
    currentEditingElement = false;

    // Signal modification
    pushModification('main');
  }

  /* Save the page info */
  var saveCurrentPageInfo = function(formElement) {
    var url = '<?php echo $this->url(array('action' => 'save', 'controller' => 'content', 'module' => 'core'), 'admin_default', true)?>';
    var request = new Form.Request(formElement, formElement.getParent(), {
      requestOptions : {
        url : url
      },
      onComplete: function() {
        eraseModification('info');
      }
    });

    request.send();
  }

  /* Change the layout */
  var changeCurrentLayoutType = function(type) {
    var availableAreas = ['top', 'bottom', 'left', 'middle', 'right'];
    var types = type.split(',');


    // Build negative areas
    var negativeAreas = [];
    availableAreas.each(function(currentAvailableArea) {
      if( !types.contains(currentAvailableArea) ) {
        negativeAreas.push(currentAvailableArea);
      }
    });

    // Build positive areas
    var positiveAreas = [];
    types.each(function(currentType) {
      var el = document.getElement('.admin_content_container_'+currentType);
      if( !el ) {
        positiveAreas.push(currentType);
      }
    });
    
    // Check to see if any columns containing widgets are going to be destroyed
    var contentLossCount = 0;
    negativeAreas.each(function(currentType) {
      var el = document.getElement('.admin_content_container_'+currentType);
      if( el && el.getChildren().length > 0 ) {
        contentLossCount++;
      }
    });

    // Notify user of potential data loss
    if( contentLossCount > 0 ) {
      <?php $replace = $this->translate("Changing to this layout will cause %s area(s) containing widgets to be destroyed. Are you sure you want to continue?", "' + contentLossCount + '") ?>
      <?php // if( !confirm('<?php echo $this->string()->escapeJavascript($replace) ?\>') ) {?>
        if( !confirm('<?php echo $replace ?>') ) {
        return false;
      }
    }

    // Destroy areas
    negativeAreas.each(function(currentType) {
      var el = document.getElement('.admin_content_container_'+currentType);
      if( el ) {
        el.destroy();
      }
    });

    // Create areas
    var levelOneReference = document.getElement('.admin_layoutbox table.admin_content_container_main');
    
    // Create level one areas
    $H({'top' : 'before', 'bottom' : 'after'}).each(function(placement, currentType) {
      if( !positiveAreas.contains(currentType) ) return;

      var newTable = new Element('table', {
        'id' : 'admin_content_new_' + (newContentIndex++),
        'class' : 'admin_content_block admin_content_buildable admin_content_container_' + currentType
      }).inject(levelOneReference, placement);

      var newTbody = new Element('tbody', {
      }).inject(newTable);

      var newTr = new Element('tr', {
      }).inject(newTbody);

      // L2
      var newTdContainer = new Element('td', {
        'id' : 'admin_content_new_' + (newContentIndex++),
        'class' : 'admin_content_column admin_content_buildable admin_content_container_middle'
      }).inject(newTr);

      // L3
      var newUlContainer = new Element('ul', {
        'class' : 'admin_content_sortable'
      }).inject(newTdContainer);

      ContentSortables.addLists(newUlContainer);
    });

    // Create level two areas
    var mainParent = document.getElement('.admin_layoutbox .admin_content_container_main tr');
    $H({'left' : 'top', 'right' : 'bottom'}).each(function(placement, currentType) {
      if( !positiveAreas.contains(currentType) ) return;
      
      // L2
      var newTdContainer = new Element('td', {
        'id' : 'admin_content_new_' + (newContentIndex++),
        'class' : 'admin_content_column admin_content_buildable admin_content_container_' + currentType
      }).inject(mainParent, placement);

      // L3
      var newUlContainer = new Element('ul', {
        'class' : 'admin_content_sortable'
      }).inject(newTdContainer);

      ContentSortables.addLists(newUlContainer);
    });

    // Signal modification
    pushModification('main');
  }

  /* Tab container and other special block handling */
  var expandSpecialBlock = function(element)
  {
    if( element.hasClass('admin_content_widget_core.container-tabs') ) {
      element.addClass('admin_layoutbox_widget_tabbed_wrapper');
      // Empty
      element.empty();
      // Title/edit
      new Element('span', {
        'class' : 'admin_layoutbox_widget_tabbed_top',
        'html' : '<?php echo $this->string()->escapeJavascript($this->translate("Tab Container")) ?><span class="open"> | <a href=\'javascript:void(0);\' onclick="openWidgetParamEdit(\'core.container-tabs\', $(this).getParent(\'li.admin_content_cell\')); (new Event(event).stop()); return false;"><?php echo $this->string()->escapeJavascript($this->translate("edit")) ?></a></span> <span class="remove"><a href="javascript:void(0)" onclick="removeWidget($(this));">x</a></span>'
      }).inject(element);
      // Desc
      new Element('span', {
        'class' : 'admin_layoutbox_widget_tabbed_overtext',
        'html' : contentByName["core.container-tabs"].childAreaDescription
      }).inject(element);
      // Edit area
      var tmpDivContainer = new Element('div', {
        'class' : 'admin_layoutbox_widget_tabbed'
      }).inject(element);
      var list = new Element('ul', {
        'class' : 'sortablesForceInclude admin_content_sortable admin_layoutbox_widget_tabbed_contents'
      }).inject(tmpDivContainer);
      
      ContentSortables.addLists(list);
    }
  }

  /* Checks for autoEdit */
  var checkForAutoEdit = function(element) {
    var m = element.get('class').match(/admin_content_widget_([^ ]+)/i);
    if( $type(m) && $type(m[1]) ) {
      //console.log(m[1], contentByName[m[1]]);
      if( $type(contentByName[m[1]].autoEdit) && contentByName[m[1]].autoEdit ) {
        openWidgetParamEdit(m[1], element);
      }
    }
  }

  /* This will hide (or show) the global layout for this page */
  var toggleGlobalLayout = function(element) {
    pushModification('main');

    var headerContainer = $$('div.admin_layoutbox_header');
    var footerContainer = $$('div.admin_layoutbox_footer');

    // Hide
    if( currentLayout == 'default' || currentLayout == '' ) {
      headerContainer.addClass('admin_layoutbox_header_hidden');
      footerContainer.addClass('admin_layoutbox_footer_hidden');
      headerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate('show on this page')) ?>)');
      footerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate('show on this page')) ?>)');
      currentLayout = 'default-simple';
    }

    // Show
    else
    {
      headerContainer.removeClass('admin_layoutbox_header_hidden');
      footerContainer.removeClass('admin_layoutbox_footer_hidden');
      headerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate('hide on this page')) ?>)');
      footerContainer.getElement('a').set('html', '(<?php echo $this->string()->escapeJavascript($this->translate('hide on this page')) ?>)');
      currentLayout = 'default';
    }
  }

  /* Delete the current page */
  var deleteCurrentPage = function() {
     
    if( !confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this page?")) ?>') ) {
      return false;
    }

    var redirectUrl = '<?php echo $this->url(array()) ?>';
    var url = '<?php echo $this->url(array('action' => 'delete', 'controller' => 'content', 'module' => 'core'), 'admin_default', true)?>';
    var request = new Request.JSON({
      'url' : url,
      'data' : {
        'format' : 'json',
        'page' : currentPage
      },
      onComplete : function(responseJSON) {
        window.location.href = redirectUrl;
      }
    });

    request.send();
  }


</script>

<h2><?php echo $this->translate('Layout Editor'); ?></h2>
<p>
  <?php echo $this->translate('CORE_LAYOUT_DESC'); ?>
</p>

<div id='admin_cms_wrapper'>

  <div class="admin_layoutbox_menu">
    <ul>
      <li class="admin_layoutbox_menu_generic" id="admin_layoutbox_menu_openpage">
        <div class="admin_layoutbox_menu_wrapper_generic admin_layoutbox_menu_pages_wrapper" id="admin_layoutbox_menu_pages_wrapper">
          <ul class="admin_layoutbox_menu_pages">
            <?php foreach( $this->pageListAssoc as $pageType => $pageListSub ): ?>
              <?php foreach( $pageListSub as $page ): ?>
                <li onclick="loadPage(<?php echo $page->page_id ?>);">
                  <?php echo $page->displayname ?>
                </li>
              <?php endforeach; ?>
              <li class="sep" style="height: 0px; padding: 0;border-top-color: #999;border-bottom-color: #999;"></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));">
          <span><?php echo $this->translate("Editing:"); ?></span>
            <?php echo $this->pageObject->displayname ?><span class="more">&nbsp;</span>
        </a>
      </li>
      <li id="admin_layoutbox_menu_savechanges">
        <a href="javascript:void(0);" onClick="saveChanges()">
          <?php echo $this->translate("Save Changes") ?>
        </a>
      </li>
      <li id="admin_layoutbox_menu_deletepage">
        <a href="javascript:void(0);" onclick="deleteCurrentPage();">
          <?php echo $this->translate("Delete Page") ?>
        </a>
      </li>
      <li class="admin_layoutbox_menu_generic" id="admin_layoutbox_menu_newpage">
        <div class="admin_layoutbox_menu_wrapper_generic admin_layoutbox_menu_makepage_wrapper" id="admin_layoutbox_menu_makepage_wrapper">
          <div class="admin_layoutbox_menu_makepage">
            <form class="admin_layoutbox_menu_makepage_form" action="<?php echo $this->url(array('action' => 'create')) ?>" method="post">
              <div>
                <?php echo $this->translate('Give your new page a unique name. This will appear in the list of pages in the "Open Page" tab.') ?>
              </div>
              <div class="admin_layoutbox_menu_makepage_form_name">
                <span><?php echo $this->translate("Page Name") ?></span>
                <?php echo $this->formText('name', '', array('id' => 'admin_layoutbox_menu_makepage_form_input', 'maxlength' => '50')) ?>
              </div>
              <div>
                <button type="submit"><?php echo $this->translate("Create Page") ?></button>
                <?php echo $this->translate("or") ?>
                <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));">
                <?php echo $this->translate("cancel") ?></a>
              </div>
              <?php echo $this->formHidden('page', 'new') ?>
            </form>
          </div>
        </div>
        <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));">
          <?php echo $this->translate("New Page") ?>
        </a>
      </li>
    </ul>
  </div>

  <div class="admin_layoutbox_wrapper">

    <div class="admin_layoutbox_sub_menu">
      <h3>
        <?php echo $this->translate('Page Block Placement') ?>
      </h3>
      <ul>
        
        <?php if( substr($this->pageObject->name, 0, 6) !== 'header' && substr($this->pageObject->name, 0, 6) !== 'footer'): ?>
        <li class="admin_layoutbox_menu_generic" id="admin_layoutbox_menu_pageinfo">
          <div class="admin_layoutbox_menu_wrapper_generic admin_layoutbox_menu_editinfo_wrapper" id="admin_layoutbox_menu_editinfo_wrapper">
            <div class="admin_layoutbox_menu_editinfo">
              <span>
                <?php echo $this->pageForm->render($this) ?>
              </span>
              <div class="admin_layoutbox_menu_editinfo_submit">
                <button onclick="saveCurrentPageInfo($('admin_content_pageinfo')); return false;"><?php echo $this->translate("Save Changes") ?></button> or <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));"><?php echo $this->translate("cancel") ?></a>
              </div>
            </div>
          </div>
          <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));"><?php echo $this->translate("Edit Page Info") ?></a>
        </li>
        <li class="admin_layoutbox_menu_generic" id="admin_layoutbox_menu_editcolumns">
          <div class="admin_layoutbox_menu_wrapper_generic admin_layoutbox_menu_columnchoices_wrapper" id="admin_layoutbox_menu_columnchoices_wrapper">
            <div class="admin_layoutbox_menu_columnchoices">
              <div class="admin_layoutbox_menu_columnchoices_instructions">
                <?php echo $this->translate("Select a new column layout for this page.") ?>
              </div>
              <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols1_3.png', '3 columns', array('onClick' => "changeCurrentLayoutType('left,middle,right');switchPageMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols1_2left.png', '2 columns - Left', array('onClick' => "changeCurrentLayoutType('left,middle');switchPageMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols1_2right.png', '2 columns - Right', array('onClick' => "changeCurrentLayoutType('middle,right');switchPageMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols1_1.png', '1 columns', array('onClick' => "changeCurrentLayoutType('middle');switchPageMenu(new Event(event), $(this));")) ?></li>
              </ul>
              <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols2_3.png', '3 columns', array('onClick' => "changeCurrentLayoutType('top,left,middle,right');switchPageMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols2_2left.png', '2 columns - Left', array('onClick' => "changeCurrentLayoutType('top,left,middle');switchPageMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols2_2right.png', '2 columns - Right', array('onClick' => "changeCurrentLayoutType('top,middle,right');switchPageMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols2_1.png', '1 columns', array('onClick' => "changeCurrentLayoutType('top,middle');switchPageMenu(new Event(event), $(this));")) ?></li>
              </ul>
              <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols3_3.png', '3 columns', array('onClick' => "changeCurrentLayoutType('left,middle,right,bottom');switchPageMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols3_2left.png', '2 columns - Left', array('onClick' => "changeCurrentLayoutType('left,middle,bottom');switchPageMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols3_2right.png', '2 columns - Right', array('onClick' => "changeCurrentLayoutType('middle,right,bottom');switchPageMenu(new Event(event), $(this));")) ?></li>
                <li><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/content/cols3_1.png', '1 columns', array('onClick' => "changeCurrentLayoutType('middle,bottom');switchPageMenu(new Event(event), $(this));")) ?></li>
              </ul>
              <div class="admin_layoutbox_menu_columnchoices_cancel">
                Or, <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));"><?php echo $this->translate("cancel") ?></a> <?php echo $this->translate("and keep your current layout.") ?>
              </div>
            </div>
          </div>
          <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));"><?php echo $this->translate("Edit Columns") ?></a>
        </li>
        <?php endif ;?>
      </ul>
    </div>


    <?php // Normal editing ?>
    <?php if( substr($this->pageObject->name, 0, 6) !== 'header' && substr($this->pageObject->name, 0, 6) !== 'footer' && ($this->pageObject->layout == 'default' || $this->pageObject->layout == 'default-simple' || $this->pageObject->layout == '')): ?>

      <div class='admin_layoutbox'>
        <div class='admin_layoutbox_header<?php echo ( empty($this->pageObject->layout) || $this->pageObject->layout == 'default' ? '' : ' admin_layoutbox_header_hidden' ) ?>'>
          <span>
            <?php echo $this->translate('Global Header') ?>
            <span>
              <a href="javascript:void(0);" onclick="toggleGlobalLayout($(this).getParent('div.admin_layoutbox_header'));">
                <?php echo ( empty($this->pageObject->layout) || $this->pageObject->layout == 'default' ? "({$this->translate('hide on this page')})" : "({$this->translate('show on this page')})" ) ?>
              </a>
            </span>
          </span>
        </div>

        <?php // LEVEL 0 - START (SANITY) ?>
        <?php
          ob_start();
          try {
        ?>

          <?php
            // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
            <table id="admin_content_<?php echo $structOne['identity'] ?>" class="admin_content_block admin_content_buildable admin_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <script type="text/javascript">
                    window.addEvent('domready', function() {
                      $("admin_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                    });
                  </script>
                  <?php
                    // LEVEL 2 - START (LEFT, MIDDLE, RIGHT)
                    foreach( (array) @$structOne['elements'] as $structTwo ):
                      $structTwoNE = $structTwo;
                      unset($structTwoNE['elements']);
                  ?>
                    <td id="admin_content_<?php echo $structTwo['identity'] ?>" class="admin_content_column admin_content_buildable admin_content_<?php echo $structTwo['type'] . '_' . $structTwo['name'] ?>">
                      <script type="text/javascript">
                        window.addEvent('domready', function() {
                          $("admin_content_<?php echo $structTwo['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structTwoNE) ?>);
                        });
                      </script>
                      <ul class="admin_content_sortable">
                        <?php
                          // LEVEL 3 - START (WIDGETS)
                          foreach( (array) $structTwo['elements'] as $structThree ):
                            $structThreeNE = $structThree;
                            $structThreeInfo = @$this->contentByName[$structThree['name']];
                            unset($structThreeNE['elements']);
                        ?>
                          <script type="text/javascript">
                            window.addEvent('domready', function() {
                              $("admin_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                            });
                          </script>
                          <?php if( empty($structThreeInfo) ): // Missing widget ?>
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="disabled admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                              <?php echo $this->translate('Missing widget: %s', $structThree['name']) ?>
                              <span class="open"></span>
                              <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                            </li>
                          <?php elseif( empty($structThreeInfo['canHaveChildren']) ): ?>
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                              <?php echo $this->translate($this->contentByName[$structThree['name']]['title']) ?>
                              <span class="open">
                                |
                                <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">
                                  <?php echo $this->translate('edit') ?>
                                </a>
                              </span>
                              <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                            </li>
                          <?php else: ?>
                            <!-- tabbed widgets -->
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_layoutbox_widget_tabbed_wrapper admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?>">
                              <span class="admin_layoutbox_widget_tabbed_top">
                                <?php echo $this->translate('Tab Container') ?>
                                <span class="open">
                                  <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">
                                    <?php echo $this->translate('edit') ?>
                                  </a>
                                </span>
                                <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                              </span>
                              <span class="admin_layoutbox_widget_tabbed_overtext">
                                <?php echo $this->translate($structThreeInfo['childAreaDescription']) ?>
                              </span>
                              <div class="admin_layoutbox_widget_tabbed">
                                <ul class="sortablesForceInclude admin_content_sortable admin_layoutbox_widget_tabbed_contents">
                                  <?php
                                    // LEVEL 4 - START (WIDGETS)
                                    foreach( (array) $structThree['elements'] as $structFour ):
                                      $structFourNE = $structFour;
                                      $structFourInfo = @$this->contentByName[$structFour['name']];
                                      unset($structFourNE['elements']);
                                  ?>
                                    <script type="text/javascript">
                                      window.addEvent('domready', function() {
                                        $("admin_content_<?php echo $structFour['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structFourNE) ?>);
                                      });
                                    </script>
                                    <?php if( empty($structFourInfo) ): ?>
                                      <li id="admin_content_<?php echo $structFour['identity'] ?>" class="disabled admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                        <?php echo $this->translate('Missing widget: %s', $structFour['name']) ?>
                                        <span></span>
                                      </li>
                                    <?php else: ?>
                                      <li id="admin_content_<?php echo $structFour['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                        <?php echo $this->translate($this->contentByName[$structFour['name']]['title']) ?>
                                        <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structFour['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">edit</a></span>
                                        <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                                      </li>
                                    <?php endif; ?>
                                  <?php
                                    endforeach;
                                    // LEVEL 4 - END
                                  ?>
                                </ul>
                              </div>
                            </li>
                            <!-- end tabbed widgets -->
                          <?php endif; ?>

                        <?php
                          endforeach;
                          // LEVEL 3 - END
                        ?>

                      </ul>
                    </td>
                  <?php
                    endforeach;
                    // LEVEL 2 - END
                  ?>

                </tr>
              </tbody>
            </table>
          <?php
            endforeach;
            // LEVEL 1 - END
          ?>

        <?php // LEVEL 0 - END (SANITY) ?>
        <?php
            ob_end_flush();
          } catch( Exception $e ) {
            ob_end_clean();
            echo "An error has occurred.";
          }
        ?>

        <div class='admin_layoutbox_footer<?php echo ( empty($this->pageObject->layout) || $this->pageObject->layout == 'default' ? '' : ' admin_layoutbox_footer_hidden' ) ?>'>
          <span>
            <?php echo $this->translate('Global Footer') ?>
            <span>
              <a href="javascript:void(0);" onclick="toggleGlobalLayout($(this).getParent('div.admin_layoutbox_footer'));">
                <?php echo ( empty($this->pageObject->layout) || $this->pageObject->layout == 'default' ? "({$this->translate('hide on this page')})" : "({$this->translate('show on this page')})" ) ?>
              </a>
            </span>
          </span>
        </div>
      </div>

    <?php // Header/Footer editing ?>
    <?php elseif( (substr($this->pageObject->name, 0, 6) == 'header' || substr($this->pageObject->name, 0, 6) == 'footer') && ($this->pageObject->layout == 'default' || $this->pageObject->layout == 'default-simple' || $this->pageObject->layout == '')): ?>

      <div class='admin_layoutbox'>
        <?php if( substr($this->pageObject->name, 0, 6) == 'footer' ): ?>
          <div class='admin_layoutbox_header'>
            <span>Global Header</span>
          </div>
        <?php else: ?>
          <?php
            // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
            <table id="admin_content_<?php echo $structOne['identity'] ?>" class="admin_content_block admin_content_block_headerfooter admin_content_buildable admin_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <td class="admin_content_column_headerfooter">
                    <span class="admin_layoutbox_note">
                      Drop things here to add them to the global header.
                    </span>
                    <script type="text/javascript">
                      window.addEvent('domready', function() {
                        $("admin_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                      });
                    </script>
                    <ul class="admin_content_sortable">
                      <?php
                        // LEVEL 3 - START (WIDGETS)
                        foreach( (array) $structOne['elements'] as $structThree ):
                          $structThreeNE = $structThree;
                          $structThreeInfo = $this->contentByName[$structThree['name']];
                          unset($structThreeNE['elements']);
                      ?>
                        <script type="text/javascript">
                          window.addEvent('domready', function() {
                            $("admin_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                          });
                        </script>
                        <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                          <?php echo $this->translate($this->contentByName[$structThree['name']]['title']) ?>
                          <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">edit</a></span>
                          <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </td>
                </tr>
              </tbody>
            </table>
          <?php
            endforeach;
            // LEVEL 1 - END
          ?>
        <?php endif; ?>

        <div class='admin_layoutbox_center_placeholder'>
          <span><?php echo $this->translate("Main Content Area") ?></span>
        </div>

        <?php if( substr($this->pageObject->name, 0, 6) == 'header' ): ?>
        <div class='admin_layoutbox_footer'>
          <span><?php echo $this->translate("Global Footer") ?></span>
        </div>
        <?php else: ?>
          <?php
            // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
            <table id="admin_content_<?php echo $structOne['identity'] ?>" class="admin_content_block admin_content_block_headerfooter admin_content_buildable admin_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <td class="admin_content_column_headerfooter">
                    <span class="admin_layoutbox_note">
                      <?php echo $this->translate("Drop things here to add them to the global footer.") ?>
                    </span>
                    <script type="text/javascript">
                      window.addEvent('domready', function() {
                        $("admin_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                      });
                    </script>
                    <ul class="admin_content_sortable">
                      <?php
                        // LEVEL 3 - START (WIDGETS)
                        foreach( (array) $structOne['elements'] as $structThree ):
                          $structThreeNE = $structThree;
                          $structThreeInfo = $this->contentByName[$structThree['name']];
                          unset($structThreeNE['elements']);
                      ?>
                        <script type="text/javascript">
                          window.addEvent('domready', function() {
                            $("admin_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                          });
                        </script>
                        <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                          <?php echo $this->translate((string) $this->contentByName[$structThree['name']]['title']) ?>
                          <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate("edit") ?></a></span>
                          <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  </td>
                </tr>
              </tbody>
            </table>
          <?php
            endforeach;
            // LEVEL 1 - END
          ?>
        <?php endif; ?>
      </div>


    <?php // Non-standard layout editing ?>
    <?php elseif( substr($this->pageObject->name, 0, 6) != 'header' && substr($this->pageObject->name, 0, 6) != 'footer' && $this->pageObject->layout != 'default' && $this->pageObject->layout != 'default-simple' && $this->pageObject->layout != ''): ?>

      <div class='admin_layoutbox'>

        <?php // LEVEL 0 - START (SANITY) ?>
        <?php
          ob_start();
          try {
        ?>

          <?php
            // LEVEL 1 - START (TOP, MAIN, BOTTOM)
            foreach( (array) @$this->contentStructure as $structOne ):
              $structOneNE = $structOne;
              unset($structOneNE['elements']);
          ?>
            <table id="admin_content_<?php echo $structOne['identity'] ?>" class="admin_content_block admin_content_buildable admin_content_<?php echo $structOne['type'] . '_' . $structOne['name'] ?>">
              <tbody>
                <tr>
                  <script type="text/javascript">
                    window.addEvent('domready', function() {
                      $("admin_content_<?php echo $structOne['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structOneNE) ?>);
                    });
                  </script>
                  <?php
                    // LEVEL 2 - START (LEFT, MIDDLE, RIGHT)
                    foreach( (array) @$structOne['elements'] as $structTwo ):
                      $structTwoNE = $structTwo;
                      unset($structTwoNE['elements']);
                  ?>
                    <td id="admin_content_<?php echo $structTwo['identity'] ?>" class="admin_content_column admin_content_buildable admin_content_<?php echo $structTwo['type'] . '_' . $structTwo['name'] ?>">
                      <script type="text/javascript">
                        window.addEvent('domready', function() {
                          $("admin_content_<?php echo $structTwo['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structTwoNE) ?>);
                        });
                      </script>
                      <ul class="admin_content_sortable">
                        <?php
                          // LEVEL 3 - START (WIDGETS)
                          foreach( (array) $structTwo['elements'] as $structThree ):
                            $structThreeNE = $structThree;
                            $structThreeInfo = @$this->contentByName[$structThree['name']];
                            unset($structThreeNE['elements']);
                        ?>
                          <script type="text/javascript">
                            window.addEvent('domready', function() {
                              $("admin_content_<?php echo $structThree['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structThreeNE) ?>);
                            });
                          </script>
                          <?php if( empty($structThreeInfo) ): // Missing widget ?>
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="disabled admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                              <?php echo $this->translate('Missing widget: %s', $structThree['name']) ?>
                              <span class="open"></span>
                              <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                            </li>
                          <?php elseif( empty($structThreeInfo['canHaveChildren']) ): ?>
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?><?php if( !empty($structThreeInfo['special']) ) echo ' htmlblock' ?>">
                              <?php echo $this->translate($this->contentByName[$structThree['name']]['title']) ?>
                              <span class="open">
                                |
                                <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">
                                  <?php echo $this->translate('edit') ?>
                                </a>
                              </span>
                              <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                            </li>
                          <?php else: ?>
                            <!-- tabbed widgets -->
                            <li id="admin_content_<?php echo $structThree['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_layoutbox_widget_tabbed_wrapper admin_content_<?php echo $structThree['type'] . '_' . $structThree['name'] ?>">
                              <span class="admin_layoutbox_widget_tabbed_top">
                                <?php echo $this->translate('Tab Container') ?>
                                <span class="open">
                                  <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structThree['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">
                                    <?php echo $this->translate('edit') ?>
                                  </a>
                                </span>
                                <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                              </span>
                              <span class="admin_layoutbox_widget_tabbed_overtext">
                                <?php echo $this->translate($structThreeInfo['childAreaDescription']) ?>
                              </span>
                              <div class="admin_layoutbox_widget_tabbed">
                                <ul class="sortablesForceInclude admin_content_sortable admin_layoutbox_widget_tabbed_contents">
                                  <?php
                                    // LEVEL 4 - START (WIDGETS)
                                    foreach( (array) $structThree['elements'] as $structFour ):
                                      $structFourNE = $structFour;
                                      $structFourInfo = @$this->contentByName[$structFour['name']];
                                      unset($structFourNE['elements']);
                                  ?>
                                    <script type="text/javascript">
                                      window.addEvent('domready', function() {
                                        $("admin_content_<?php echo $structFour['identity'] ?>").store('contentParams', <?php echo Zend_Json::encode($structFourNE) ?>);
                                      });
                                    </script>
                                    <?php if( empty($structFourInfo) ): ?>
                                      <li id="admin_content_<?php echo $structFour['identity'] ?>" class="disabled admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                        <?php echo $this->translate('Missing widget: %s', $structFour['name']) ?>
                                        <span></span>
                                      </li>
                                    <?php else: ?>
                                      <li id="admin_content_<?php echo $structFour['identity'] ?>" class="admin_content_cell admin_content_buildable admin_content_draggable admin_content_<?php echo $structFour['type'] . '_' . $structFour['name'] ?>">
                                        <?php echo $this->translate($this->contentByName[$structFour['name']]['title']) ?>
                                        <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $structFour['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;">edit</a></span>
                                        <span class="remove"><a href='javascript:void(0)' onclick="removeWidget($(this));">x</a></span>
                                      </li>
                                    <?php endif; ?>
                                  <?php
                                    endforeach;
                                    // LEVEL 4 - END
                                  ?>
                                </ul>
                              </div>
                            </li>
                            <!-- end tabbed widgets -->
                          <?php endif; ?>

                        <?php
                          endforeach;
                          // LEVEL 3 - END
                        ?>

                      </ul>
                    </td>
                  <?php
                    endforeach;
                    // LEVEL 2 - END
                  ?>

                </tr>
              </tbody>
            </table>
          <?php
            endforeach;
            // LEVEL 1 - END
          ?>

        <?php // LEVEL 0 - END (SANITY) ?>
        <?php
            ob_end_flush();
          } catch( Exception $e ) {
            ob_end_clean();
            echo "An error has occurred.";
          }
        ?>

      </div>

    <?php endif; ?>

    <div class="admin_layoutbox_footnotes">
      <?php echo $this->translate("Note: Some blocks won't appear if you're not signed-in or if they don't belong on this page."); ?>
    </div>
  </div>


  <div class="admin_layoutbox_pool_wrapper">
    <h3><?php echo $this->translate("Available Blocks") ?></h3>
    <div class='admin_layoutbox_pool'>
      <div id='stock_div'></div>
      <ul id='column_stock'>
        <?php foreach( $this->contentAreas as $category => $categoryAreas ): ?>
          <li>
            <div class="admin_layoutbox_pool_category_wrapper" onclick="$(this); $(this).getElement('.admin_layoutbox_pool_category_show').toggle(); $(this).getElement('.admin_layoutbox_pool_category_hide').toggle(); this.getParent('li').getElement('ul').style.display = ( this.getParent('li').getElement('ul').style.display == 'none' ? '' : 'none' );">
              <div class="admin_layoutbox_pool_category">
                <div class="admin_layoutbox_pool_category_hide">
                  &nbsp;
                </div>
                <div class="admin_layoutbox_pool_category_show">
                  &nbsp;
                </div>
                <div class="admin_layoutbox_pool_category_label">
                  <?php echo $this->translate($category) ?>
                </div>
              </div>
            </div>
            <ul class='admin_content_sortable admin_content_stock_sortable'>
              <?php foreach( $categoryAreas as $info ):
                $class = 'admin_content_widget_' . $info['name'];
                $class .= ' admin_content_draggable admin_content_stock_draggable';
                $onmousedown = false;
                if( !empty($info['disabled']) ) {
                  $class .= ' disabled';
                  if( !empty($info['requireItemType']) ) {
                    $onmousedown = 'alert(\'Disabled due to missing item type(s): '.join(', ', (array)$info['requireItemType']) . '\'); return false;';
                  } else {
                    $onmousedown = 'alert(\'Disabled due to missing dependency.\'); return false;';
                  }
                }
                if( !empty($info['special']) ) {
                  $class .= ' htmlblock special';
                }
                if( !empty($info['adminCssClass']) ) {
                  $class .= ' ' . $info['adminCssClass'];
                }

                ?>
                <?php //if( empty($info['canHaveChildren']) ): ?>
                  <li class="<?php echo $class ?>" title="<?php echo $this->escape($info['description']) ?>"<?php if( $onmousedown ): ?> onmousedown="<?php echo $onmousedown ?>"<?php endif; ?>>
                      <?php echo $this->translate($info['title']) ?>
                    <span class="open"> | <a href='javascript:void(0);' onclick="openWidgetParamEdit('<?php echo $info['name'] ?>', $(this).getParent('li.admin_content_cell')); (new Event(event).stop()); return false;"><?php echo $this->translate("edit") ?></a></span>
                    <span class="remove"><a href='javascript:void(0);' onclick="removeWidget($(this));">x</a></span>
                  </li>
                <?php /* //else: ?>
                  <li class="admin_layoutbox_widget_tabbed_wrapper">
                    <span class="admin_layoutbox_widget_tabbed_top">
                      Tabbed Blocks <a href="#">(edit)</a>
                    </span>
                    <div class="admin_layoutbox_widget_tabbed">
                      <ul class="admin_layoutbox_widget_tabbed_contents">
                        <?php echo $structThreeInfo['childAreaDescription'] ?>
                      </ul>
                    </div>
                  </li>
                <?php //endif; */ ?>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

</div>
