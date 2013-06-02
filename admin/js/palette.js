var Palette = function( filedName, availableNode, selectedNode, availableValues, selectedValues )
{
	this.fieldName = filedName;
	this.availableNode = availableNode;
	this.selectedNode = selectedNode;
	
	var o = this;
	
	var strAvailableOptions ='';
	
	for(var i=0; i<availableValues.length; i++ ){
		strAvailableOptions += '<option value="'+availableValues[i]+'" >'+availableValues[i]+'</option>';
	}
	
	$jq(availableNode).append(strAvailableOptions);
	
	var strSelectedOptions = '';
	
	for(var i=0; i<selectedValues.length; i++)
	{
		strSelectedOptions += '<option value="'+selectedValues[i]+'" >'+selectedValues[i]+'</option>';	
	}
	$jq(selectedNode).append(strSelectedOptions);	

	this.update = function()
	{
		var value = '';
		$jq('option', this.selectedNode ).each(function(){
			value += this.value + "|";
		});
		value = (value == '')?'none':value;
			
		$jq('input[@name='+this.fieldName+']', '#'+this.fieldName+'_palette_cont').attr("value", value);
		//alert($jq('input[@name='+this.fieldName+']', '#'+this.fieldName+'_palette_cont').attr("value"));
	}
	
	this.move2selected = function()
	{
		$jq("option[@selected]", this.availableNode).each(function(){
			$jq(o.selectedNode).append($jq(this).attr('selected', '').remove());
		});
		this.update();
	}
	
	this.move2available = function()
	{
		$jq("option[@selected]", this.selectedNode).each(function(){
			$jq(o.availableNode).append($jq(this).attr('selected', '').remove());
		});
		this.update();
	}
	
	this.moveUp = function()
	{
		$jq($jq("option[@selected]", this.selectedNode).get(0)).prev().before($jq("option[@selected]", this.selectedNode));
		$jq($jq("option[@selected]", this.availableNode).get(0)).prev().before($jq("option[@selected]", this.availableNode));
		this.update();
	}
	
	this.moveDown = function()
	{
		var size;
		size = $jq("option[@selected]", this.selectedNode).size();
		if( size  > 0 )
		{
			$jq( $jq("option[@selected]", this.selectedNode).get(size-1) ).next().after($jq("option[@selected]", this.selectedNode));
		}
		
		size = $jq("option[@selected]", this.availableNode).size();
		if( size  > 0 )
		{
			$jq( $jq("option[@selected]", this.availableNode).get(size-1) ).next().after($jq("option[@selected]", this.availableNode));
		}
		this.update();
	}
		
	this.selectedNodeDeselect = function()
	{
		if($jq('option[@selected]', 'select.selected_values').size()>0)
			$jq('option[@selected]', 'select.selected_values').attr('selected','');
	};
	
	this.availableNodeDeselect = function()
	{
		if($jq('option[@selected]', 'select.admissible_values').size()>0)
			$jq('option[@selected]', 'select.admissible_values').attr('selected','');
	};
	this.selectedNodeDeselect();
	this.availableNodeDeselect();	
}
