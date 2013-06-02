/* -- Language JS File -- */

function checkIfPlansSelected( F )
{
	if ( checked_multicheckbox( F.elements['plan_id_arr[]'] ) )
		return true;
	else
	{
		alert('Please, select plan(s).' );
		return false;
	}
}


function checkPlansForm( F, type )
{
	for( var i in F.elements )
		if(typeof(F.elements[i])=='object' && F.elements[i]!=null && F.elements[i].name && F.elements[i].type=='text')
		{
			var object_value=F.elements[i].value;
			var object_name=F.elements[i].name;
			var name_reg=/^price/;
			if(name_reg.test(object_name))
			{
				var reg=/^\d+[\.0-9]{0,3}$/;
				if(!reg.test(object_value))
				{
					alert('Price textbox incorrect data. Please correct.');
					F.elements[i].select();
					return false;
				}
			}
			else
			if(type==0)
			{
				var reg=/^\d+$/;
				if(!reg.test(object_value))
				{
					alert('Period textbox incorrect data. Please correct.');
					F.elements[i].select();
					return false;
				}
			}
		}

	return true;
}

function checkAddNewPlan(F, type)
{
	for( var i in F.elements )
		if(typeof(F.elements[i])=='object' && F.elements[i]!=null && F.elements[i].name && F.elements[i].type=='text')
		{
			var object_value=F.elements[i].value;
			if(F.elements[i].name=="price")
			{
				var reg=/^\d+[\.0-9]{0,3}$/;
				if(!reg.test(object_value))
				{
					alert('Price textbox incorrect data. Please correct.');
					F.elements[i].select();
					return false;
				}
			}
			else
			if(type==0)
			{
				var reg=/^\d+$/;
				if(!reg.test(object_value))
				{
					alert('Period textbox incorrect data. Please correct.');
					F.elements[i].select();
					return false;
				}
			}
		}
}

function checkSubmitForm( F )
{
	for( var i in F.elements )
		if(typeof(F.elements[i])=='object' && F.elements[i]!=null && F.elements[i].name && F.elements[i].type=='text')
		{
			var reg=/^\d{0,2}$/;
			if(!reg.test(F.elements[i].value))
			{
				alert('Please correct limit.');
				F.elements[i].select();
				return false;
			}
		}
	
	if (document.getElementById('action').value == 'stop')
	{
		document.getElementById('action').value='post';
		return false;
	}
	
	return true;
}

function deleteMembershipType()
{
	if( !confirm( 'Are you sure you want to delete this membership type?' ) )
		document.getElementById('action').value='stop';
	else
		document.getElementById('action').value='post';
}
