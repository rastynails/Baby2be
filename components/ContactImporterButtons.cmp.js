
function component_ContactImporterButtons(auto_id)
{
	this.DOMConstruct('ContactImporterButtons', auto_id);
}

component_ContactImporterButtons.prototype =
	new SK_ComponentHandler({

	construct: function()
	{

        },

        google: function( options )
        {
            var btn = this.$('#google');
            console.log(options.popupUrl);
            btn.click(function() {
                window.open(options.popupUrl, 'ContactImporter_Google', 'status=1,toolbar=0,width=550,height=615');
            });
        }

});
