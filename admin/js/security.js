
var securityCountry = (function( $ )
{
    var instance;

    function construct()
    {
        var _elements = {};
        var _methods = {};

        _methods.moveTo = function()
        {
            switch ( this.name )
            {
                case 'move-to-left':
                    _methods.moveFromTo( _elements.whiteList.find('li:has(input:checked)'), _elements.blackList );
                    break
                case 'move-to-right':
                    _methods.moveFromTo( _elements.blackList.find('li:has(input:checked)'), _elements.whiteList );
                    break;
            }
        }

        _methods.selectAll = function()
        {
            switch ( this.name )
            {
                case 'selectAllBlackList':
                    _elements.blackList.find( ':checkbox' ).attr( 'checked', this.checked );
                    break;
                case 'selectAllWhiteList':
                    _elements.whiteList.find( ':checkbox' ).attr( 'checked', this.checked );
                    break;
            }
        }

        _methods.moveFromTo = function(from, to)
        {
            if ( from.length )
            {
                from.appendTo( to ).bind( 'click', this.toggleClick );
                this.save();
                this.toggleClass();
            }
        }

        _methods.toggleClass = function()
        {
            _elements.whiteList.children().removeClass();
            _elements.whiteList.find( 'li:odd' ).addClass( 'tr_2' );
            _elements.whiteList.find( 'li:even' ).addClass( 'tr_1' );

            _elements.blackList.children().removeClass();
            _elements.blackList.find( 'li:odd' ).addClass( 'tr_2' );
            _elements.blackList.find( 'li:even' ).addClass( 'tr_1' );
        }

        _methods.toggleClick = function( e )
        {
            var event = e.target || e.srcElement;

            if ( event.nodeName == 'INPUT' )
            {
                return;
            }

            var $handler = $( this ).find( ':checkbox' );

            $handler.attr( 'checked', !$handler.attr('checked') );
        }
        
        _methods.toggleClick = function( event )
        {
            if ( event.target.tagName.toUpperCase() == 'INPUT' )
            {
                event.stopPropagation ? event.stopPropagation() : ( event.cancelBubble = true );

                return;
            }

            var $handler = $( this ).find( ':checkbox' );

            $handler.attr( 'checked', !$handler.attr('checked') );

        }

        _methods.save = function()
        {
            var data = {};

            data.whiteList = [];
            data.blackList = [];

            _elements.whiteList.find( ':hidden' ).each( function()
            {
                data.whiteList.push( this.value );
            });

            _elements.blackList.find( ':hidden' ).each( function()
            {
                data.blackList.push( this.value );
            });

            $.ajax(
            {
                cache: false,
                data: {
                    command: 'countriesListSave',
                    data: JSON.stringify( data )
                },
                type: 'POST',
                url: URL_ADMIN_RSP + 'security.rsp.php'
            });
        }

        _elements.panel = $( '#antispam-countries' );

        _elements.panel.find( '[name="selectAllBlackList"]' ).bind( 'click', _methods.selectAll );
        _elements.panel.find( '[name="selectAllWhiteList"]' ).bind( 'click', _methods.selectAll );

        _elements.blackList = _elements.panel.find( '#blackList' );
        _elements.whiteList = _elements.panel.find( '#whiteList' );

        _elements.panel.find( '[name="move-to-left"]' ).bind( 'click', _methods.moveTo );
        _elements.panel.find( '[name="move-to-right"]' ).bind( 'click', _methods.moveTo );

        _elements.whiteList.find( 'li' ).bind( 'click', _methods.toggleClick );
        _elements.blackList.find( 'li' ).bind( 'click', _methods.toggleClick );

        return {
            init: function()
            {

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
})( $jq );
