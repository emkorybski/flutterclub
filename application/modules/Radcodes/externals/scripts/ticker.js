/*
	Class:    	cvNewsTicker
	Author:   	Crispijn Verkade
	Website:    http://crispijnverkade.nl
	Version:  	1.0,1
	Date:     	07/12/2009
	Built For:  MooTools 1.2.0
*/


var radcodesNewsTicker = new Class({
	Implements: [Options],

	options: {
		ticker_ul: 'newsticker', //element for the ticker
		duration:200, //duration for the animation
		delay:5000, //delay per news item
		transition: Fx.Transitions.Quad.easeIn, //transition
		direction:'vertical' //horzontal or vertical
	},
	
	initialize: function(el,options) {
		//set options
		this.setOptions(options);
		this.el = $(el);
		this.items = this.el.getChildren('li');
		var w = 0;
		var h = 0;
		var self = this;
		
		if(this.options.direction == 'vertical'){
			w = this.el.getWidth();
			this.items.each(function(li,index) {
				h += li.getSize().y;
			});
		}else if(this.options.direction == 'horizontal'){
			h = this.el.getWidth();
			this.items.each(function(li,index) {
				w += li.getSize().x;
			});
		}else{
			alert('no direction defined!');
		}
		
		this.el.setStyles({
			position: 'absolute',
			top: 0,
			left: 0,
			width: w,
			height: h
		});
		
		this.fx = new Fx.Morph(this.el, {
			transition: this.options.transition,
			duration: this.options.duration,
			onComplete: function(){
				var i = (this.current==0)?this.items.length:this.current;
				this.items[i-1].injectInside(this.el);
				this.el.setStyles({
					left:0,
					top:0
				});
			}.bind(this)
		});
		
		this.current = 0;
			(function(){ 
				self.next();
			}).delay(this.options.delay);

		
	},
	
	next: function(){
		this.current++;
		if (this.current >= this.items.length) this.current = 0;
		var pos = this.items[this.current];
		this.fx.start({
		    top: -pos.offsetTop,
		    left: -pos.offsetLeft
		});

		this.next.bind(this).delay(this.options.delay+this.options.speed);
	}
});