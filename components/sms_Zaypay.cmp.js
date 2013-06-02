function component_sms_Zaypay(auto_id)
{
	this.DOMConstruct('sms_Zaypay', auto_id);
	
	var handler = this;
}

component_sms_Zaypay.prototype =
	new SK_ComponentHandler({
		
		construct: function(params) {
			var handler = this;
						
			var $pay_link = this.$("#pay_link");
			var $iframe_content = this.$("#iframe_cont").children(); 
			var $prov_title = this.$("#pay_by").text();
			
			var $i_frame = this.$("#i_frame");
			
			$pay_link.click(function(){
			
				$i_frame.attr("src", params.widget_url);	
				$i_frame.attr("width", params.width);
				$i_frame.attr("height", params.height);
				
				window.pay_iframe_floatbox = new SK_FloatBox({
					$title: $prov_title,
					$contents: $iframe_content,
					width: params.width + 25
				});
				
				handler.ajaxCall("ajax_registerPaymentAttempt", {
					hash: params.hash,
					service_key: params.service_key 
				});
				
				window.pay_iframe_floatbox.bind('close', function(){
					$i_frame.attr("src", "");
				});
			});
		}
		
});