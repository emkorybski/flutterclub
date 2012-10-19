var noobSlide = new Class({
    initialize: function (a) {
        this.items = a.items;
        this.mode = a.mode || 'horizontal';
        this.modes = {
            horizontal: ['left', 'width'],
            vertical: ['top', 'height']
        };
        this.size = a.size || 240;
        this.box = a.box.setStyle(this.modes[this.mode][1], (this.size * this.items.length) + 'px');
        this.button_event = a.button_event || 'click';
        this.handle_event = a.handle_event || 'click';
        this.onWalk = a.onWalk || null;
        this.currentIndex = null;
        this.previousIndex = null;
        this.nextIndex = null;
        this.leftIndex = null;
        this.rightIndex = null;
        this.interval = a.interval || 9000;
        this.autoPlay = a.autoPlay || false;
        this._play = null;
        this.handles = a.handles || null;
        if (this.handles) {
            this.addHandleButtons(this.handles)
        }
        this.buttons = {
            previous: [],
            next: [],
            moveleft: [],
            moveright: [],
            play: [],
            playback: [],
            stop: []
        };
        if (a.addButtons) {
            for (var b in a.addButtons) {
                this.addActionButtons(b, $type(a.addButtons[b]) == 'array' ? a.addButtons[b] : [a.addButtons[b]])
            }
        }
        this.fx = new Fx.Tween(this.box, $extend((a.fxOptions || {
            duration: 1000,
            wait: false
        }), {
            property: this.modes[this.mode][0]
        }));
        this.walk((a.startItem || 0), true, true)
    },
    addHandleButtons: function (a) {
        for (var i = 0; i < a.length; i++) {
            a[i].addEvent(this.handle_event, this.walk.bind(this, [i, true]))
        }
    },
    addActionButtons: function (a, b) {
        for (var i = 0; i < b.length; i++) {
            switch (a) {
            case 'previous':            	
                b[i].addEvent(this.button_event, this.previous.bind(this, [true]));
                break;
            case 'next':            	
                b[i].addEvent(this.button_event, this.next.bind(this, [true]));
                break;
            case 'moveleft':            	
                b[i].addEvent(this.button_event, this.moveleft.bind(this, [true]));
                break;
            case 'moveright':
                b[i].addEvent(this.button_event, this.moveright.bind(this, [true]));
                break;
            case 'play':
                b[i].addEvent(this.button_event, this.play.bind(this, [this.interval, 'next', false]));
                break;
            case 'playback':
                b[i].addEvent(this.button_event, this.play.bind(this, [this.interval, 'previous', false]));
                break;
            case 'stop':
                b[i].addEvent(this.button_event, this.stop.bind(this));
                break
            }
            this.buttons[a].push(b[i])
        }
    },
    previous: function (a) {    	
        this.walk((this.currentIndex > 0 ? this.currentIndex - 1 : this.items.length - 1), a)       
    },
    next: function (a) {    	
        this.walk((this.currentIndex < this.items.length - 1 ? this.currentIndex + 1 : 0), a)        
    },
    moveleft: function (a) {
    	    	
    	var pixel = $('list_item').style.marginLeft;
    	var i = pixel.replace('px', '');
    	if(parseInt(i) < 0)
    	{    		
    		var index = parseInt(i) + 480;
    		$('list_item').style.marginLeft = index + "px";
    	}
    	//get total item
    	var totalItem = $('totalItem').value;
    	var totalSlide = Math.round(parseInt(totalItem)/4);
    	
    	//get and set current slide
    	var currentSlide = $('currentSlide').value;
    	if(parseInt(currentSlide) > 1)
    	{
    		$('currentSlide').value = parseInt(currentSlide) - 1;
    		
    		//set item info
	    	var currentSlide = $('currentSlide').value;
	    	var itemInfo = $('itemInfo');
	    	var start = (parseInt(currentSlide)*4) - 3;
	    	var end = (parseInt(currentSlide)*4);
	    	var text = start + " - " + end + " of " + totalItem;
	    	itemInfo.innerHTML = text;
	    	this.handles[this.currentIndex].removeClass('selected');
    	}
    	
    },
    moveright: function (a) {    	   	
    	//get total item
    	var totalItem = $('totalItem').value;
    	var totalSlide = Math.ceil(parseInt(totalItem)/4);
    	//get and set current slide
    	var currentSlide = $('currentSlide').value;
    	if(parseInt(currentSlide) < parseInt(totalSlide))
    	{
	    	$('currentSlide').value = parseInt(currentSlide) + 1;
	    	
	    	var pixel = $('list_item').style.marginLeft;
	    	var i = pixel.replace('px', '');
	    	//var mode = parseInt(i)/500;
	    	//mode = parseInt(mode)*2;
	    	var index = parseInt(i) - (480);
	    	$('list_item').style.marginLeft = index + "px";
	    	
	    	//set item info
	    	var currentSlide = $('currentSlide').value;
	    	var itemInfo = $('itemInfo');
	    	var start = (parseInt(currentSlide)*4) - 3;
	    	var end = parseInt(currentSlide)*4;
	    	if(parseInt(end) > parseInt(totalItem))
	    		end = totalItem;
	    	var text = start + " - " + end + " of " + totalItem;
	    	itemInfo.innerHTML = text;
	    	this.handles[this.currentIndex].removeClass('selected');
    	}
    },
    play: function (a, b, c) {
        this.stop();
        if (!c) {
            this[b](false)
        }
        this._play = this[b].periodical(a, this, [false])
    },
    stop: function () {
        $clear(this._play)
    },
    walk: function (a, b, c) {
        if (a != this.currentIndex) {
            this.currentIndex = a;
            this.previousIndex = this.currentIndex + (this.currentIndex > 0 ? -1 : this.items.length - 1);
            this.nextIndex = this.currentIndex + (this.currentIndex < this.items.length - 1 ? 1 : 1 - this.items.length);
            if (b) {
                this.stop()
            }
            if (c) {
                this.fx.cancel().set((this.size * -this.currentIndex) + 'px')
            } else {
                this.fx.start(this.size * -this.currentIndex)
            }
            if (b && this.autoPlay) {
                this.play(this.interval, 'next', true)
            }
            if (this.onWalk) {
                this.onWalk((this.items[this.currentIndex] || null), (this.handles && this.handles[this.currentIndex] ? this.handles[this.currentIndex] : null))
                $clear(this._play)
            }
        }
    }
});