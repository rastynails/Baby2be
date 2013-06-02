
var SK_CustomPageEditor = (function( $ )
{
    var editInstance;
    var addInstance;

    function editConstruct()
    {
        var editor;

        var _methods = {
            _save: function()
            {
                $.ajax(
                {
                    cache: false,
                    data: {
                        "command": "saveDocumentCustomPage",
                        "lang_id": editor.lang_id,
                        "lang_key_id": editor.lang_key_id,
                        "data": JSON.stringify( editor.data.getData() )
                    },
                    type: 'POST',
                    url: URL_ADMIN_RSP + 'custom_page_editor.rsp.php'
                });

                _methods._cancel();
            },

            _cancel: function( event )
            {
                if ( event )
                {
                    editor.data.setData( editor.data._.data );
                }

                editor.data.destroy();
                editor.data = null;
                editor.editor_action
                    .parents( 'tr:first' )
                    .hover( function(){$(this).find('.editor-edit').show()},
                            function(){$(this).find('.editor-edit').hide()} );
                editor.editor_action.remove();
            },

            _drawEditorLinks: function( btn_node, data )
            {
                var $form = $(
                    '<input name="lang_key_id" type="hidden" value="' + data.lang_key_id + '" />' +
                    '<table width="100%" class="lang_values_edit_form_tbl">' +
                        '<tbody></tbody>' +
                    '</table>'
                );

                for ( var lang_id in data.languages )
                {
                    var $template = $( '<tr><td width="16"></td><td style="padding: 5px;"><div style="position: relative;"><span class="editor-edit">Edit</span><div class="editor"></div></div></td></tr>' );

                    $template.find( 'td:first' ).text( data.languages[lang_id].abbrev );

                    var eventData = {
                        'lang_id': +data.languages[lang_id].lang_id,
                        'lang_key_id': +data.lang_key_id,
                        'value': data.values[lang_id]
                    }

                    $template.find( '.editor-edit' ).bind( 'click', eventData, this._replaceEditor );

                    $template
                        .hover( function(){$(this).find('.editor-edit').show();},
                                function(){$(this).find('.editor-edit').hide();} )
                        .find( '.editor' ).html( data.values[lang_id] );

                    $template.appendTo( $form.find('tbody:first') );
                }

                $form.appendTo( $(btn_node.parentNode) );
            },

            _replaceEditor: function( event )
            {
                $( this ).hide().parents( 'tr:first' ).unbind();

                var editor_action;

                if ( editor && editor.data )
                {
                    editor_action = $( editor.data.container.$ ).next( '.editor-action' );

                    if ( confirm('You opened another editor. Would you keep it?') )
                    {
                        _methods._save();
                    }

                    _methods._cancel();
                }

                editor_action = editor_action || $( '<div class="editor-action"><input type="button" name="editor-save" value="Save" />&nbsp;&nbsp;&nbsp;<input type="button" name="editor-cancel" value="Cancel" /></div>' );
                editor_action.find( '[name="editor-save"]' ).bind( 'click', _methods._save );
                editor_action.find( '[name="editor-cancel"]' ).bind( 'click', _methods._cancel );
                $( this.nextSibling ).after( editor_action );

                editor = {
                    'lang_id': event.data.lang_id,
                    'lang_key_id': event.data.lang_key_id,
                    'data': CKEDITOR.replace( this.nextSibling ),
                    'editor_action': editor_action
                };
            }
        };

        CKEDITOR.config.customConfig = 'admin_config.js';
        
        return {
            getLinks: function ( btn_node, lang_section, lang_key, lang_id )
            {
                $.ajax(
                {
                    url: URL_ADMIN + 'lang_responder.php',
                    type: 'POST',
                    data: {
                        function_: 'loadKeyValuesForEdit',
                        lang_section: lang_section,
                        lang_key: lang_key,
                        lang_id: lang_id || null,
                        get_languages: true
                    },
                    dataType: 'json',
                    success: function( data )
                    {                        
                        _methods._drawEditorLinks( btn_node, data );
                        
                        $( btn_node ).remove();
                    }
                });
            }
        }
    }

    function addConstruct()
    {
        var editor;

        CKEDITOR.config.customConfig = 'admin_config.js';
        
        return {
            changeTab: function( sender, lang_id )
            {
                if ( editor )
                {
                    if ( editor.lang_id == lang_id )
                    {
                        return;
                    }
                    
                    var current_textarea = $( editor.data.element.$ ).val( editor.data.getData() );

                    editor.data.destroy();
                    editor.data = null;                    
                    editor.tab ? editor.tab.removeClass( 'active_lang_tab' ).addClass( 'lang_tab' ) : null;

                    current_textarea.hide();
                }

                var tab = sender ? $( sender ).addClass( 'active_lang_tab' ) : null;

                editor = {
                    "data": CKEDITOR.replace( $('[name="custom_code[' + lang_id + ']"]')[0] ),
                    "lang_id": lang_id,
                    "tab": tab
                };
                
                editor.data.on('blur', function()
                {
                    $( this.element.$ ).val( this.getData() );
                });
            }
            ,

            activateDefaultTab: function()
            {
                var tab, sender;
                
                tab = $( '.custom_code_container .language_tab_cont');
                tab.length ? sender = tab.children('div:first')[0] : null;

                this.changeTab( sender, defaultLangId );
            }
        }
    }

    return {
        getEditInstance: function ()
        {
            if ( !editInstance )
            {
                editInstance = editConstruct();
            }

            return editInstance;
        },

        getAddInstance: function()
        {
            if ( !addInstance )
            {
                addInstance = addConstruct();
            }

            return addInstance;
        }
    }
})( $jq );
