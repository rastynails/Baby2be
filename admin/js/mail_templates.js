jQuery(function(){
    new AdminMailTemplates();
});

var AdminMailTemplates = function()
{
    var handler = this;

    this.load_template_list = false;
    this.send_request = false;
    this.template_list = {};
    this.is_edit_template = {};

    $jq("#link_predefine_mail_templates").click(
                function(){
                    handler.get_TemplateList();
                } );
}

AdminMailTemplates.prototype =
{
    get_TemplateList: function()
    {
        var handler = this;

        if( handler.send_request == false )
        {
            var floatbox = '<div id="floatbox_overlay" class="floatbox_overlayBG"/>';
            var mail_template_box = $jq('#mail_template_box')
            if( handler.load_template_list == false )
            {
                handler.send_request = true;
                var $loading_img = $jq('#predefine_mail_templates > .loading');
                $loading_img.toggle();
                $jq.ajax({
                    url: URL_ADMIN+'mail_template_responder.php',
                    type: 'POST',
                    data: { function_: 'loadTemplateList' },
                    dataType: 'json',
                    success: function( data ) {
                        handler.send_request = false;
                        handler.template_list = data.result.template_list;
                        if( handler.displayTemplateList(data.result.template_list) )
                        {
                            handler = null;
                            $jq( floatbox ).appendTo( mail_template_box.parent("div") );
                       }
                       $loading_img.toggle();
                    }
                });
            }
            else
            {
                var $table = $jq('#mail_template_list > table');

                $table.find("tr.tpl_button").hide();
                $table.find(".selected").removeClass("selected");
                mail_template_box.show();
                $jq( floatbox ).appendTo( mail_template_box.parent("div") );
            }
        }
    },

    delete_Template: function( $id )
    {
        var handler = this;

        var $template = handler.template_list[$id];

        if( !$id && !$template.mail_template_id  )
        {
            alert("No mail template with id ".$id);
            return;
        }

        var $table = $jq('#mail_template_list > table');
        var $template_tr = $table.find(".mail_template_" + $id);
        var $buttons = $template_tr.next().find("input");
        $buttons.attr("disabled","disabled");
        
        $jq.ajax({
            url: URL_ADMIN+'mail_template_responder.php',
            type: 'POST',
            data: { function_: 'deleteTemplate', template_id: $template.mail_template_id },
            dataType: 'json',
            success: function( data ) {
                if( data.result == true )
                {                    
                    var $template_tr = $table.find(".mail_template_" + $id);
                    $template_tr.next().remove();
                    $template_tr.remove();

                    if( $jq("#mail_template_id").val() == $template.mail_template_id )
                        $jq("#mail_template_id").val('');

                    $table.find('tr').removeClass('tr_1');
                    $table.find('tr').removeClass('tr_2');

                    var $tr_all = $table.find('tr').not(':first, .tpl_button');
                    $tr_all.filter(':even').addClass('tr_1');
                    $tr_all.filter(':odd').addClass('tr_2');

                    alert("Mail template delete success.");

                    handler = null;
                }
                else
                {
                    alert("Mail template delete fail.");
                    $buttons.removeAttr("disabled","disabled");
                    return;
                }
            }
        });
    },

    displayTemplateList: function( list )
    {
        var handler = this;
        if( !list || !list.length > 0 )
        {
            alert("No save mail templates.");
            return false;
        }

        handler.load_template_list = true

        var $table = $jq('#mail_template_list > table');
        //console.log($table);
        var $tr = $table.find("tr:first");
        var $buttons = $table.find("tr.tpl_button:first");
        //console.log($tr);

        $jq.each( list,
            function( i, item )
            {
                if( item && item.text )
                {                    
                    var text = item.text;
                    var $item_tr = $tr.clone();
                    var $item_buttons = $buttons.clone();

                    $item_tr.show();

                    $item_tr.addClass("mail_template_" + i);
                    $item_tr.find("td.td_1").text(i + 1);
                    $item_tr.find("td.td_2 div.tpl_text").text(text);

                    $item_buttons.find("input[name='delete']").click(function(){
                        handler.delete_Template( i );
                    });

                    $item_buttons.find("input[name='load']").click(function(){
                        handler.LoadTemplate( i, true );
                    });

                    $item_buttons.find("input[name='edit']").click(function(){
                        handler.EditTemplate( i );
                    });

                    $table.append($item_tr);
                    $table.append($item_buttons);

                    $item_tr.click(function() {
                        $table.find(".selected").removeClass('selected');
                        $table.find("tr.tpl_button").hide();
                        $item_tr.addClass('selected');
                        $item_buttons.addClass('selected');
                        if( !$item_tr.find('form').length )
                            $item_buttons.show();
                    });
                }
            }

        );
            
        $jq('#mail_template_box .mail_template_box_close_link').click(
            function() {
                handler.closeTemplateList();
            } );
        var $tr_all = $table.find('tr').not(':first, .tpl_button');
        $tr_all.filter(':even').addClass('tr_1');
        $tr_all.filter(':odd').addClass('tr_2');

        $jq('#mail_template_box').show();
        $table.find(".tpl_button").hide();
        return true;
    },

    closeTemplateList: function()
    {
        var $table = $jq('#mail_template_list > table');
        $table.find('form').remove();
        $table.find('div.tpl_text').show();
        $jq('#mail_template_box').hide();
        $jq( '#floatbox_overlay' ).remove();
    },

    createTemplateEditForm: function( $id, $subject, $text )
	{
        var handler = this;
      
        var $template = handler.template_list[$id];

        if( $subject != undefined && $text != undefined )
        {
            var $table = $jq('#mail_template_list > table');
            var $tr = $table.find("tr.mail_template_" + $id);
            var $td = $table.find(".mail_template_" + $id + " > td.td_2");
            var $text_div = $td.find("div.tpl_text");
            var $buttons = $tr.next(".tpl_button");

            $text_div.hide();
            $buttons.hide();

            var $form = $jq(
                '<form method="post">'+
                    '<input name="mail_template_id" type="hidden" value="' + $template.mail_template_id + '" />'+
                    '<div>Subject:</div>' +
                    '<input name="subject" class="input_text" type="text" style="width: 200px;" value="' + $subject + '" name="subject"/>' +
                    '<div style="margin-top:5px;">Text:</div>' +
                    '<textarea name="text" type="text" class="lang_key_input" rows="15" cols="45" >' + $text + '</textarea><br />'+
                    '<div class="tfoot_td" style="margin-top: 1px">'+
                        '<input type="submit" value="Save" />'+
                        '<input name="cancel" type="button" value="Cancel" />'+
                    '</div>'+
                '</form>'
            ).appendTo($td);

            $jq('input[@name=cancel]', $form.get(0)).click(function() {
                    $form.remove();
                    $text_div.show();
                    $buttons.show();
                });

            $form.submit(function()
            {
                var form_data = $jq(this).serializeArray();
                var $_text, $_subject;
                for ( var i = 0, item; item = form_data[i]; i++ )
                {
                    if ( item.name == 'text' ) {
                        $_text = item.value;
                    }
                    if ( item.name == 'subject' ) {
                        $_subject = item.value;
                    }
                }

                if ( !$_subject.length ) {
                    $jq('input[@name=subject]', this).focus();
                    return false;
                }

                if ( !$_text.length ) {
                    $jq('input[@name=text]', this).focus();
                    return false;
                }

                var $form_buttons = $form.find("input[type=submit],input[type=button]");
                $form_buttons.attr("disabled","disabled");
                $jq.ajax({
                    url: URL_ADMIN+'mail_template_responder.php',
                    type: 'POST',
                    data: { function_: 'editTemplate', template_id: $template.mail_template_id, text: $_text, subject: $_subject },
                    dataType: 'json',
                    success: function( data )
                    {
                        $buttons.removeAttr("disabled");
                        if( data.result == true )
                        {
                            $form.remove();

                            if( $_text.length > 200 )
                               $_text = $_text.substring(0,200) + '...';
                           
                            $template.text = $_text;
                            $text_div.empty();
                            $text_div.text($_text);
                            $buttons.show();
                            $text_div.show();
                        }
                        else
                        {
                            alert("Mail template update fail.");
                        }
                    }
                });

                return false;
            });

            $jq('input[@name=key]').focus();

            return true;
        }
        
        return false;
    },

    EditTemplate: function( $id )
	{
        var handler = this;

        if( handler.load_template_list == false )
        {
            alert("No save mail templates.");
            return false;
        }

        var $template = handler.template_list[$id];

        if( $id === undefined || !$template || !$template.mail_template_id )
        {
            alert("No mail template with id " + $template.mail_template_id);
            return false;
        }

        var $table = $jq('#mail_template_list > table');
        var $tr = $table.find("tr.mail_template_" + $id);

        var $template_tr = $table.find(".mail_template_" + $id);
        var $buttons = $template_tr.next().find("input");
        $buttons.attr("disabled","disabled");

        if( !$tr.find('form').length )
        {
            $jq.ajax({
                    url: URL_ADMIN+'mail_template_responder.php',
                    type: 'POST',
                    data: { function_: 'loadTemplate', template_id: $template.mail_template_id },
                    dataType: 'json',
                    success: function( data )
                    {
                        $buttons.removeAttr("disabled");
                        var $subject = data.result.template.subject;
                        var $text = data.result.template.text;
                        if( handler.createTemplateEditForm( $id, $subject, $text ) )
                        {
                            handler = null;
                            return true;
                        }
                        else
                        {
                            handler = null;
                            alert("Mail template load fail.");
                            return false;
                        }
                    }
            });
        }
	},

    LoadTemplate: function( $id, $is_edit )
    {
        var handler = this;

        if( handler.load_template_list == false )
        {
            alert("No save mail templates.");
            return false;
        }
        
        var $template = handler.template_list[$id];

        if( $id === undefined || !$template || !$template.mail_template_id )
        {
            alert("No mail template with id " + $template.mail_template_id);
            return false;
        }

        var $table = $jq('#mail_template_list > table');
        var $template_tr = $table.find(".mail_template_" + $id);
        var $buttons = $template_tr.next().find("input");
        $buttons.attr("disabled","disabled");

        $jq.ajax({
                url: URL_ADMIN+'mail_template_responder.php',
                type: 'POST',
                data: { function_: 'loadTemplate', template_id: $template.mail_template_id },
                dataType: 'json',
                success: function( data ) {
                    $buttons.removeAttr("disabled");
                    var $subject = data.result.template.subject;
                    var $text = data.result.template.text;

                    if( $subject != undefined && $text != undefined )
                    {
                        if( $is_edit == true )
                            $jq("#mail_template_id").val($template.mail_template_id );
                        else
                            $jq("#mail_template_id").val('');

                        $jq("#msg_subject").attr( 'value', $subject );
                        $jq("#msg_text").attr( 'value', $text );

                        handler.closeTemplateList();
                        handler = null;
                        $jq('input[name=edit_template]').removeAttr('disabled');
                        
                        return true;
                    }
                    else
                    {
                        handler = null;
                        alert("Mail template load fail.");

                        return false;
                    }
                }
        });
    }
}
