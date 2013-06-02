
function deleteLanguage(id, name)
{
	if( confirm( "Are you sure want to delete `"+name+"` language?\r\nAll language values will be deleted as well" ) )
		location.href = '?command=delete_lang&lang_id='+id;
	
	return;
}

function checkLangName()
{
	var label_input = $jq("#new_language_input");
	var abbr_input = $jq("#new_language_abbr");
	
	if (!label_input.val()) {
		label_input.focus();
		return false;
	}
	
	if (!abbr_input.val()) {
		abbr_input.focus();
		return false;
	}
 
	return true;
}

var langsAutoSel = (function()
{
    var instance;
    
    function construct()
    {
        var _elements = {};
        var _methods = {};
        
        _elements.panel = $jq( '#autoSelPanel' );

        _elements.moveToLeftAll  = _elements.panel.find( '[name="move-to-left-all"]' );
        _elements.moveToLeft     = _elements.panel.find( '[name="move-to-left"]' );
        _elements.moveToRight    = _elements.panel.find( '[name="move-to-right"]' );
        _elements.moveToRightAll = _elements.panel.find( '[name="move-to-right-all"]' );

        _elements.fixedCountry = _elements.panel.find( '#fixedCountry' );
        _elements.freeCountry  = _elements.panel.find( '#freeCountry' );

        _elements.selLang = _elements.panel.find( '[name="languages"]' );

        _elements.template = _elements.panel.find( '.template:first' );

        _methods.enableAutoSel = function()
        {
            $jq.ajax(
            {
                cache: false,
                data: {
                    command: 'enable',
                    checked: +this.checked
                },
                type: 'POST',
                dataType: 'json',
                url: URL_ADMIN_RSP + 'langs_auto_select.rsp.php'
            });

            this.checked ? _methods.isEnable() : _methods.isDisable()
        };

        _methods.isEnable = function()
        {
            _elements.panel.show();

            _elements.moveToLeftAll.bind( 'click', this.moveToLeftAll );
            _elements.moveToLeft.bind( 'click', this.moveToLeft );
            _elements.moveToRight.bind( 'click', this.moveToRight );
            _elements.moveToRightAll.bind( 'click', this.moveToRightAll );

            _elements.selLang.bind( 'change', this.loadCountry );
        };

        _methods.isDisable = function()
        {
            _elements.selLang.unbind();
            
            _elements.moveToLeftAll.unbind();
            _elements.moveToLeft.unbind();
            _elements.moveToRight.unbind();
            _elements.moveToRightAll.unbind();
            
            _elements.panel.hide();
        };

        _methods.moveToLeftAll = function()
        {
            _methods.moveFromTo( _elements.freeCountry.find('li'), _elements.fixedCountry );
        }

        _methods.moveToLeft = function()
        {
            _methods.moveFromTo( _elements.freeCountry.find('li:has(input:checked)'), _elements.fixedCountry );
        }

        _methods.moveToRight = function()
        {
            _methods.moveFromTo( _elements.fixedCountry.find('li:has(input:checked)'), _elements.freeCountry );
        }

        _methods.moveToRightAll = function()
        {
            _methods.moveFromTo( _elements.fixedCountry.find('li:visible'), _elements.freeCountry );
        }

        _methods.moveFromTo = function(from, to)
        {
            if ( from.length )
            {
                from.clone().appendTo( to ).bind( 'click', this.toggleClick );
                from.remove();
                this.save();
                this.toggleClass();
            }
        }

        _methods.loadCountry = function()
        {
            _elements.fixedCountry.empty();
            _elements.freeCountry.empty();

            if ( +_elements.selLang.val() > 0)
            {
                $jq.ajax(
                {
                    cache: false,
                    data: {
                        command: 'loadCountry',
                        lang_id: +this.value
                    },
                    type: 'POST',
                    url: URL_ADMIN_RSP + 'langs_auto_select.rsp.php',
                    dataType: 'json',
                    success: function( data )
                    {
                        if ( data.fixedCountry )
                        {
                            countryBuilder( data.fixedCountry, _elements.fixedCountry );
                        }

                        if ( data.freeCountry )
                        {
                            countryBuilder( data.freeCountry, _elements.freeCountry );
                        }

                        _elements.fixedCountry.find( 'li' ).add( _elements.freeCountry.find('li') ).removeClass().bind( 'click', _methods.toggleClick );
                        
                        _methods.toggleClass();

                        function countryBuilder( list, owner )
                        {
                            for ( var key in list )
                            {
                                var country = _elements.template.clone();
                                var value = list[key];

                                country.find( '[name="code"]' ).attr(
                                {
                                    'name': 'code[' + value.code + ']',
                                    'value': value.code
                                });

                                country.find( 'span' ).text( value.country );
                                country.appendTo( owner );
                            }
                        }
                    }
                });
            }
        }

        _methods.toggleClass = function()
        {
            _elements.fixedCountry.children().removeClass();
            _elements.fixedCountry.find( 'li:odd' ).addClass( 'tr_1' );
            _elements.fixedCountry.find( 'li:even' ).addClass( 'tr_2' );

            _elements.freeCountry.children().removeClass();
            _elements.freeCountry.find( 'li:odd' ).addClass( 'tr_1' );
            _elements.freeCountry.find( 'li:even' ).addClass( 'tr_2' );
        }

        _methods.toggleClick = function( e )
        {
            var event = e.target || e.srcElement;

            if ( event.nodeName == 'INPUT' )
            {
                return;
            }

            var $handler = $jq( this ).find( ':checkbox' );

            $handler.attr( 'checked', !$handler.attr('checked') );
        }

        _methods.save = function()
        {
            var data = {};

            data.lang_id = +_elements.selLang.val();
            data.fixedCountry = [];
            data.freeCountry = [];

            _elements.fixedCountry.find( ':hidden' ).each( function()
            {
                data.fixedCountry.push( this.value );
            });

            _elements.freeCountry.find( ':hidden' ).each( function()
            {
                data.freeCountry.push( this.value );
            });
            
            $jq.ajax(
            {
                cache: false,
                data: {
                    command: 'save',
                    data: JSON.stringify( data )
                },
                type: 'POST',
                url: URL_ADMIN_RSP + 'langs_auto_select.rsp.php'
            });
        }

        $jq( '#enableAutoSel' ).bind( 'click', _methods.enableAutoSel );
        
        return {
            init: function()
            {
                $jq.ajax(
                {
                    cache: false,
                    data: {
                        command: 'isEnable'
                    },
                    type: 'POST',
                    dataType: 'json',
                    url: URL_ADMIN_RSP + 'langs_auto_select.rsp.php',
                    success: function( data )
                    {
                        data ? _methods.isEnable() : _methods.isDisable();
                        $jq( '#enableAutoSel' ).attr( 'checked', data );
                    }
                });
            }
        }
    }

    return {
        getInstance: function ()
        {
            if ( !instance )
            {
                instance = construct();
            }

            return instance;
        }
    }
})();

$jq( function()
{
    langsAutoSel.getInstance().init();
});
