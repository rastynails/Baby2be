function component_sms_ZaypayWidget(auto_id)
{
	this.DOMConstruct('sms_ZaypayWidget', auto_id);
	
	var handler = this;
}

component_sms_ZaypayWidget.prototype =
	new SK_ComponentHandler({
		
		construct: function() {
			var handler = this;
			
			var $locale_language = this.$("#locale_language");
			var $locale_country = this.$("#locale_country");
			
			var $close_tb = this.$("#close_tb");
			
			$locale_language.change(function()
			{
				if ( $(this).val() != '')
				{
					document.forms["form_locale"].submit();
				}
			});
			
			$locale_country.change(function()
			{
				if ( $(this).val() != '')
				{
					document.forms["form_locale"].submit();
				}
			});
			
			$close_tb.click(function(){
				parent.document.location.href = parent.document.location.href;
			});
		}
});