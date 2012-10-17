

var radcodesDoubleSelect = new Class({
	Implements : Options,

	options : {
		parentOptions : {},
		multiChildOptions : {},
		defaultParentMessage : null,
		defaultChildMessage : '',
		defaultValue : null
	},
	
	element_id : null,
	
	elementInput : null,
	parentSelect : null,
	childSelect : null,

    initialize : function(element_id, options) {
		// set the custom options
		this.setOptions(options);
		
		this.elementInput = $(element_id);
		this.parentSelect = $('parent_' + element_id);
		this.childSelect = $('child_' + element_id);

		this.parentSelect.addEvent('change', this.changeParent.bind(this));
		this.childSelect.addEvent('change', this.changeChild.bind(this));
		
		this.initParentOptions();
		
		this.initDefaultValue(this.options.defaultValue);
		
    },

    findParentValue : function(search) {
    	
    	var parent_value = null;
    	
		var multiChildItems = new Hash(this.options.multiChildOptions);
		multiChildItems.each(function(childOptions, parent_id) {
			var children = new Hash(childOptions);
			if (children.has(search)) {
				parent_value = parent_id;
			}
		}, this);	
    	
    	return parent_value;
    },
    
    initDefaultValue : function(value) {

    	var parent_value = value;
    	
    	var parentItems = new Hash(this.options.parentOptions);
    	
    	if (!parentItems.has(value)) {    		
    		parent_value = this.findParentValue(value);
    	}

		this.parentSelect.value = parent_value;
		this.loadChildOptions(parent_value);
		
		if (value !== parent_value && null !== parent_value) {
			//alert("this.childSelect.value");
			//alert(value);
			//alert(parent_value);
			this.childSelect.value = value;
		}
		
		if (value && (parentItems.has(value) || null !== parent_value)) {
			this.setInputValue(value);
		}
		
    },
    
    initParentOptions : function() {
    	this.parentSelect.empty();
    	if (null !== this.options.defaultParentMessage) {
    		new Element('option', {'value' : '', 'text' : this.options.defaultParentMessage}).inject(this.parentSelect);
    	}
		var items = new Hash(this.options.parentOptions);

		items.each(function(label, value){
			new Element('option', {'value' : value, 'text' : label}).inject(this.parentSelect);
		}, this);
    },
    
    loadChildOptions : function(parent_id) {
    	this.childSelect.empty();
    	if (null !== this.options.defaultChildMessage) {
    		new Element('option', {'value' : '', 'text' : this.options.defaultChildMessage}).inject(this.childSelect);
    	}
    	
    	var items = new Hash(this.options.multiChildOptions);
    	if (items.has(parent_id)) {
    		var children = new Hash(items.get(parent_id));
    		
    		if (children.getLength()) {
        		children.each(function(label, value){
        			new Element('option', {'value' : value, 'text' : label}).inject(this.childSelect);
        		}, this);
        		this.childSelect.show();
    		}
    		else {
    			this.childSelect.hide();
    		}
    	}
    	else {
    		this.childSelect.hide();
    	}
    	
    },
    
    changeParent : function() {
    	var selected_value = this.parentSelect.get('value');
    	this.setInputValue(selected_value);
    	
    	this.childSelect.set('value', '');
    	this.loadChildOptions(selected_value);
    },
    
    changeChild : function() {
    	var selected_value = this.childSelect.get('value');
    	if (selected_value) {
    		this.setInputValue(selected_value);
    	}
    	else {
    		this.setInputValue(this.parentSelect.get('value'));
    	}
    },
    
    setInputValue : function(value) {
    	this.elementInput.set('value', value);
    }
    

});

