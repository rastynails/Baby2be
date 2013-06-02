var ConfigCounter = function($object, type) {
	this.type = type || 'int';
	this.$input_node = $object;
	this.$up_control = {};
	this.$down_control = {};
	this.construct();
};

ConfigCounter.prototype = {
	
	construct: function() {
		var self = this;
	
		var $controls_cont = this.$input_node.parent().siblings(".controls");
		
		this.$up_control = $controls_cont.find('.up'); 
		this.$down_control = $controls_cont.find('.down'); 
		
		this.$input_node.keyup(function(){
			self.controlValue();
		});
		
		this.$up_control.click(function(){
			self.increase();
		});
		
		this.$down_control.click(function(){
			self.decrease();
		});
	},
	
	controlValue: function() {
		var regexp;		
	
		if(this.type=='int') {
			var regexp = /[\d]+/;
		} else {
			var regexp = /[\d\.]+/;
		}
		
		var value = this.$input_node.val();
		value = regexp.exec(value);
		this.$input_node.val(value);
	},
	
	increase: function() {
		var value = this.$input_node.val();
		if(value < 10 ^ (this.$input_node.attr('size')-1)) {
			value++;
		}
		this.$input_node.val(value);
	},
	
	decrease: function() {
		var value = this.$input_node.val();
		if(value > 0) {
			value--;
		}
		this.$input_node.val(value);
	}
	
};




$jq(function(){
	
	$jq('.integer_config').each(function(i, obj){
		$jq(obj).removeClass('integer_config');
		var counter = new ConfigCounter($jq(obj));
	});
	
	$jq('.float_config').each(function(i, obj){
		$jq(obj).removeClass('float_config');
		var counter = new ConfigCounter($jq(obj), 'float');
	});
		
});