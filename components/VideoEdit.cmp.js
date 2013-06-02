
function component_VideoEdit(auto_id)
{
	this.DOMConstruct('VideoEdit', auto_id);
	
	var handler = this;
}

component_VideoEdit.prototype =
	new SK_ComponentHandler({
	
	construct: function()
	{
		var handler = this;
		
		var $cont = this.$("#video_edit");
		$cont.find("select[name=privacy_status]").change(function(){
			if ( $(this).val() == 'password_protected' )
			{
				$cont.find("tr.password_tr").css("display", "table-row");
			}
			else
			{
				$cont.find("tr.password_tr").css("display", "none");
			}
		});
	}
});
