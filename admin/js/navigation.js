
//Menu navigation

function CheckAddItemForm( F )
{
	if ( !jQuery.trim(F.item_name.value) )
		return fail_alert( F.item_name, "Missing item name");

	if ( !jQuery.trim(jQuery('[name="item_label[1]"]').val()) )
		return fail_alert( jQuery('[name="item_label[1]"]').get(0), "Missing item label");

	return true;
}

function CheckDeleteItemInput( field )
{
	if ( checked_multicheckbox( field.elements['item_arr[]'] ) )
	{
		return confirm( "Are you sure you want to delete selected items?" );
	}
	else
	{
		alert( 'Select item' );
		return false;
	}
}


function convert_item_name( id )
{
    $( id ).value = str2lower( $( id ).value );
}

// Document system

function CheckAddDocumentForm( F )
{
	if ( !F.doc_key.value.trim() )
		return fail_alert( F.doc_key, "Insert document key");
	if ( !$('doc_label_input_id').value.trim() )
		return fail_alert( $('doc_label_input_id'), "Insert document label");
	if ( !F.doc_url.value.trim() )
		return fail_alert( F.doc_url, "Insert document url");

	return true;
}

function CheckEditDocumentForm( F )
{
	if ( !F.doc_key.value.trim() )
		return fail_alert( F.doc_key, "Insert document key");
	if ( !F.doc_url.value.trim() )
		return fail_alert( F.doc_url, "Insert document url");

	return true;
}

function manage_doc_access()
{
	if ( $('status').checked )
	{
		$( 'access_member' ).disabled = false;
		$( 'access_guest' ).disabled = false;
	}
	else
	{
		$( 'access_member' ).disabled = true;
		$( 'access_guest' ).disabled = true;
	}
}
function CheckDeleteDocInput( field )
{
	if ( checked_multicheckbox( field.elements['doc_arr[]'] ) )
	{
		return confirm( "Are you sure you want to delete selected items?" );
	}
	else
	{
		alert( 'Select document' );
		return false;
	}
}
