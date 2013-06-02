
jQuery( function()
{
    var isAllowed = function ( sort_table, parent )
    {
        if ( sort_table.currentItem.hasClass('no-nesting') && !sort_table.currentItem.find('li').length )
        {
            sort_table.options.maxLevels = 1;
        }
        else
        {
            sort_table.options.maxLevels = 2;
        }

        if ( sort_table.currentItem.hasClass('no-nesting') )
        {
            return true;
        }

        if ( !parent && sort_table.placeholder.prev().length == 0 )
        {
            return false;
        }

        return true;
    }

    var update = function( event, ui )
    {
        var $handler = $( this );
        var $prev = ui.item.prev();

        if ( !ui.item.hasClass('no-nesting') )
        {
            if ( $prev.hasClass('no-nesting') )
            {
                ui.item.detach();
                ui.item.appendTo( $prev.find('ol:first') );
            }

            ui.item.find( '[name="profile_field_section_id"]' ).val( ui.item.parents('li.no-nesting:first').find('[name="profile_field_section_id"]:first').val() );
        }

        var fieldList = [];
        var $sectionList = $handler.find('li.no-nesting');

        findChildNode( $sectionList, fieldList );

        $.ajax(
        {
            cache: false,
            data: {fieldList: fieldList},
            type: 'POST',
            url: URL_ADMIN_RSP + 'profile_section_list.rsp.php'
        });

        $handler.find( 'div.clearfix:even' ).css( 'background', '#DFDFDF' )
        $handler.find( 'div.clearfix:odd' ).css( 'background', '#EFEFEF' );

        $sectionList.each( function(index)
        {
            var $handler = $( this );
            
            if ( index == 0 )
            {
                $handler.find( 'a.up:first' ).removeClass().addClass( 'up up-dis' ).attr( 'href', 'javascript://' );
                $handler.find( 'a.down:first' ).removeClass().addClass( 'down down-enb' ).attr( 'href', URL_ADMIN + 'profile_field_list.php?mov_sec_order=down&section_id=' + $handler.find('[name="profile_field_section_id"]:first').val() );
            }
            else if( index == $sectionList.length - 1 )
            {
                $handler.find( 'a.up:first' ).removeClass().addClass( 'up up-enb' ).attr( 'href', URL_ADMIN + 'profile_field_list.php?mov_sec_order=up&section_id=' + $handler.find('[name="profile_field_section_id"]:first').val() );
                $handler.find( 'a.down:first' ).removeClass().addClass( 'down down-dis' ).attr( 'href', 'javascript://' );
            }
            else
            {
                $handler.find( 'a.up:first' ).removeClass().addClass( 'up up-enb' ).attr( 'href', URL_ADMIN + 'profile_field_list.php?mov_sec_order=up&section_id=' + $handler.find('[name="profile_field_section_id"]:first').val() );
                $handler.find( 'a.down:first' ).removeClass().addClass( 'down down-enb' ).attr( 'href', URL_ADMIN + 'profile_field_list.php?mov_sec_order=down&section_id=' + $handler.find('[name="profile_field_section_id"]:first').val() );
            }

            var $childList = $handler.find( 'ol' );

            if ( $childList.length )
            {
                var $onlyChild = $childList.find( 'li:only-child' );

                if ( $onlyChild.length )
                {
                    $onlyChild.find( 'a.up' ).removeClass().addClass( 'up up-dis' ).attr( 'href', 'javascript://' );
                    $onlyChild.find( 'a.down' ).removeClass().addClass( 'down down-dis' ).attr( 'href', 'javascript://' );
                }
                else
                {
                    var $firstChilde = $childList.find( 'li:first-child' );

                    $firstChilde.find( 'a.up' ).removeClass().addClass( 'up up-dis' ).attr( 'href', 'javascript://' );
                    $firstChilde.find( 'a.down' ).removeClass().addClass( 'down down-enb' ).attr( 'href', URL_ADMIN + 'profile_field_list.php?mov_field_order=down&field_id==' + $handler.find('[name="profile_field_id"]').val() );

                    var $lastChild = $childList.find( 'li:last-child' );

                    $lastChild.find( 'a.up' ).removeClass().addClass( 'up up-enb' ).attr( 'href', URL_ADMIN + 'profile_field_list.php?mov_field_order=up&field_id==' + $handler.find('[name="profile_field_id"]').val() );
                    $lastChild.find( 'a.down' ).removeClass().addClass( 'down down-dis' ).attr( 'href', 'javascript://' );

                    $childList.children( 'li.menu-item' ).not( $firstChilde.add($lastChild) ).each( function()
                    {
                        var $handler = $( this );

                        $handler.find( 'a.up' ).removeClass().addClass( 'up up-enb' ).attr( 'href', URL_ADMIN + 'profile_field_list.php?mov_field_order=up&field_id==' + $handler.find('[name="profile_field_id"]:first').val() );
                        $handler.find( 'a.down' ).removeClass().addClass( 'down down-enb' ).attr( 'href', URL_ADMIN + 'profile_field_list.php?mov_field_order=down&field_id==' + $handler.find('[name="profile_field_id"]:first').val() );
                    });
                }
            }
        });
    }

    var findChildNode = function( node, fieldList )
    {
        node.each( function(index)
        {
            var $handler = $( this );

            fieldList[index] =
            {
                'order': index,
                'profile_field_section_id': +$handler.find( '[name="profile_field_section_id"]:first' ).val()
            } ;

            var $childNode = $handler.find( 'ol li' );

            if ( $childNode.length )
            {
                fieldList[index].fields = [];

                $childNode.each( function(childeIndex)
                {
                    var $handler = $( this );

                    fieldList[index].fields.push(
                    {
                        'order': childeIndex,
                        'profile_field_id': +$handler.find( '[name="profile_field_id"]:first' ).val(),
                        'profile_field_section_id': +$handler.find( '[name="profile_field_section_id"]:first' ).val()
                    });
                });
            }
        });
    }

    $( '#sort-table' ).nestedSortable(
    {
        forcePlaceholderSize: true,
        handle: 'div',
        helper:	'clone',
        items: 'li',
        opacity: 0.7,
        placeholder: 'placeholder',
        revert: 250,
        tabSize: 10000,
        tolerance: 'pointer',
        toleranceElement: '> div',
        maxLevels: 2,
        isAllowed: isAllowed,
        update: update,
        doNotClear: true
    });
});
