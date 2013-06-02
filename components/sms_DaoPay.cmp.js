function component_sms_DaoPay(auto_id)
{
	this.DOMConstruct('sms_DaoPay', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_sms_DaoPay.prototype =
	new SK_ComponentHandler({
		
		construct: function(params) {
			var handler = this;
						
			var $pay_link = this.$("#pay_link");
			var $iframe_content = this.$("#iframe_cont").children(); 
			var $prov_title = this.$("#pay_by").text();
			
			var $i_frame = this.$("#i_frame");
			
			$pay_link.click(function(){
			
				$i_frame.attr("src", "http://daopay.com/pay/?appcode=" + params.appcode + "&prodcode=" + params.prodcode + "&custom=" + params.hash + "&service=" + params.service_key + "&successurl=" + params.url);	
				$i_frame.attr("width", params.width);
				$i_frame.attr("height", params.height);
				
				window.pay_iframe_floatbox = new SK_FloatBox({
					$title: $prov_title,
					$contents: $iframe_content,
					width: params.width + 28
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