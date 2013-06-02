function component_EditProfile( auto_id )
{
    this.DOMConstruct( 'EditProfile', auto_id );
    this.progressBar = this.$( '#progressBar' );
    this.progressBarIndex = this.$( '#index' );
}

component_EditProfile.prototype = new SK_ComponentHandler(
{
    construct: function( param )
    {
        var items = $( 'td.value:visible', this.container_node );
        var handler = this;
        this.progressBarData = param;
        this.setProgressBarIndex( Math.round((param.complatedFieldsCount * 100) / param.totalFieldsCount) );

        items.each( function( i )
        {
            var fields = $( 'input:checkbox,input:text,input:radio,select,textarea', this );

            if ( fields.length )
            {
                switch ( fields[0].type )
                {
                    case 'text':
                    case 'textarea':
                        if ( fields[0].value.length > 0 )
                        {
                            $( fields[0] ).parent( 'td.value:first' ).data( 'action', 'inc' );
                        }
                        break;
                    case 'checkbox':
                    case 'radio':
                        if ( fields.filter( ':checked' ).length )
                        {
                            $( fields[0] ).parents( 'td.value' ).data( 'action', 'inc' );
                        }
                        else
                        {
                            $( fields[0] ).parents( 'td.value' ).data( 'action', 'dec' );
                        }
                        break;
                    case 'select-one':
                        if ( fields[0].value.length > 0 )
                        {
                            $( fields[0] ).parent( 'td.value:first' ).data( 'action', 'inc' );
                        }
                        else
                        {
                            $( fields[0] ).parent( 'td.value:first' ).data( 'action', 'dec' );
                        }
                        break;
                }
            }
        });

        items.on( 'change keyup', function( e )
        {
            var event = e.target || e.srcElement;

            switch ( event.type.toLowerCase() )
            {
                case 'text':
                case 'textarea':
                    if ( /^re_.+$/i.test(event.name) )
                    {
                        return
                    }
                    
                    if ( event.value.length > 0 )
                    {
                        handler.onBeforeChange( $(e.delegateTarget), 'inc' );
                    }
                    else
                    {
                        handler.onBeforeChange( $(e.delegateTarget), 'dec' );
                    }
                    break;
                case 'select-one':
                    if ( event.value.length <= 0 )
                    {
                        handler.onBeforeChange( $(e.delegateTarget), 'dec' );
                    }
                    else
                    {
                        var siblings = $( event ).siblings( 'select' );

                        if ( siblings.length > 0 )
                        {
                            var change = true;

                            siblings.each( function( i )
                            {
                                if ( this.value.length <= 0 )
                                {
                                    change = false;
                                    handler.onBeforeChange( $(e.delegateTarget), 'dec' );
                                    return false;
                                }
                            });

                            if ( change )
                            {
                                handler.onBeforeChange( $(e.delegateTarget), 'inc' );
                            }
                        }
                        else
                        {
                            handler.onBeforeChange( $(e.delegateTarget), 'inc' );
                        }
                    }
                    break;
                case 'checkbox':
                    var siblings = $( event ).parents( 'td.value:first' ).find( 'input:checkbox' );

                    if ( siblings.length == 1 )
                    {
                        if ( !event.checked )
                        {
                            handler.onBeforeChange( $(e.delegateTarget), 'dec' );
                        }
                        else
                        {
                            handler.onBeforeChange( $(e.delegateTarget), 'inc' );
                        }
                    }
                    else
                    {
                        var count = siblings.filter('input:checked').length;

                        if ( count >= 1 )
                        {
                            handler.onBeforeChange( $(e.delegateTarget), 'inc' );
                        }
                        else if ( count == 0 )
                        {
                            handler.onBeforeChange( $(e.delegateTarget), 'dec' );
                        }
                    }
                    break;
                case 'radio':
                    var siblings = $( event ).parents( 'td.value:first' ).find( 'input:radio' );

                    if ( siblings.length == 1 )
                    {
                        if ( !event.checked )
                        {
                            handler.onBeforeChange( $(e.delegateTarget), 'dec' );
                        }
                        else
                        {
                            handler.onBeforeChange( $(e.delegateTarget), 'inc' );
                        }
                    }
                    else
                    {
                        var count = siblings.filter('input:checked').length;

                        if ( count >= 1 )
                        {
                            handler.onBeforeChange( $(e.delegateTarget), 'inc' );
                        }
                        else if ( count == 0 )
                        {
                            handler.onBeforeChange( $(e.delegateTarget), 'dec' );
                        }
                    }
                    break;
            }
        });
    },

    onBeforeChange: function( element, action )
    {
        var fieldAction = element.data( 'action' );

        if ( !fieldAction )
        {
            this.incProgressBarIndex();
            element.data( 'action', 'inc' );
        }
        else if ( fieldAction != action )
        {
            switch ( action )
            {
                case 'inc':
                    this.incProgressBarIndex();
                    break;
                case 'dec':
                default:
                    this.decProgressBarIndex();
                    break;
            }
            
            element.data( 'action', action );
        }
    },

    setProgressBarIndex: function( value )
    {
        this.progressBarIndex.text( value );
        this.progressBar.css( 'width', value + '%' );
    },

    incProgressBarIndex: function()
    {
        var index = Math.round( (++this.progressBarData.complatedFieldsCount * 100) / this.progressBarData.totalFieldsCount)
        this.setProgressBarIndex( index );
    },

    decProgressBarIndex: function()
    {
        var index = Math.round( (--this.progressBarData.complatedFieldsCount * 100) / this.progressBarData.totalFieldsCount)
        this.setProgressBarIndex( index );
    }
});