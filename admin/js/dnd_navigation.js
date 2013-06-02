
jQuery( function()
{
    var isAllowed = function ( sort_table, parent )
    {
        if ( sort_table.currentItem.hasClass('no-nesting') )
        {
            return false;
        }

        if ( parent && parent.hasClass('no-nesting') )
        {
            return false;
        }

        if ( !parent && sort_table.placeholder.prev().length == 0 )
        {
            return false;
        }

        if ( (parent && +parent.find('[name="menu_id"]:first').val() > 1) || (+sort_table.placeholder.prev().find('[name="menu_id"]:first').val() > 1) )
        {
            sort_table.options.maxLevels = 1;
        }
        else
        {
            sort_table.options.maxLevels = 2;
        }

        return true;
    }

    var update = function( event, ui)
    {
        var parent = ui.item.prev();
        var menu_id;
        var parent_menu_item_id;

        if ( parent.length )
        {
            parent_menu_item_id = parent.find( '[name="parent_menu_item_id"]:first' ).val() || 0;
        }
        else
        {
            parent = ui.item.parents( 'li:first' );
            parent_menu_item_id = parent.find( '[name="menu_item_id"]:first' ).val();
        }

        menu_id = parent.find( '[name="menu_id"]:first' ).val();

        ui.item.find( '[name="menu_id"]:first' ).val( menu_id );
        ui.item.find( '[name="parent_menu_item_id"]:first' ).val( parent_menu_item_id );

        var menuList = [];
        var $menuList = $('#sort-table > li.menu-item');

        findChildNode( $menuList, menuList );

        $.ajax(
        {
            cache: false,
            data: {menuList: menuList},
            type: 'POST',
            url: URL_ADMIN_RSP + 'navigation.rsp.php'
        });

        $( this )
            .find( 'div.clearfix:even' )
                .css( 'background', '#DFDFDF' )
            .end()
            .find( 'div.clearfix:odd' )
                .css( 'background', '#EFEFEF' );


        $menuList.each( function()
        {
            var $handler = $( this );

            if ( $handler.prev().hasClass('no-nesting') )
            {
                $handler.find( 'a.up' ).removeClass().addClass( 'up up-dis' ).attr( 'href', 'javascript://' );
            }
            else
            {
                $handler.find( 'a.up' ).removeClass().addClass( 'up up-enb' ).attr( 'href', URL_ADMIN + 'nav_menu.php?move=up&item=' + $handler.find('[name="menu_item_id"]:first').val() );
            }

            var $next = $handler.next();

            if ( $next.hasClass('no-nesting') || !$next.length)
            {
                $handler.find( 'a.down' ).removeClass().addClass( 'down down-dis' ).attr( 'href', 'javascript://' );
            }
            else
            {
                $handler.find( 'a.down' ).removeClass().addClass( 'down down-enb' ).attr( 'href', URL_ADMIN + 'nav_menu.php?move=down&item=' + $handler.find('[name="menu_item_id"]:first').val() );
            }

            var $childe_items = $handler.find( 'ol' );

            if ( $childe_items.length )
            {
                var $only_child = $childe_items.find( 'li:only-child' );

                if ( $only_child.length )
                {
                    $only_child.find( 'a.up' ).removeClass().addClass( 'up up-dis' ).attr( 'href', 'javascript://' );
                    $only_child.find( 'a.down' ).removeClass().addClass( 'down down-dis' ).attr( 'href', 'javascript://' );
                }
                else
                {
                    var $first_childe = $childe_items.find( 'li:first-child' )

                    $first_childe.find( 'a.up' ).removeClass().addClass( 'up up-dis' ).attr( 'href', 'javascript://' );
                    $first_childe.find( 'a.down' ).removeClass().addClass( 'down down-enb' ).attr( 'href', URL_ADMIN + 'nav_menu.php?move=down&item=' + $first_childe.find('[name="menu_item_id"]:first').val() );

                    var $last_child = $childe_items.find( 'li:last-child' );

                    $last_child.find( 'a.up' ).removeClass().addClass( 'up up-enb' ).attr( 'href', URL_ADMIN + 'nav_menu.php?move=up&item=' + $last_child.find('[name="menu_item_id"]:first').val() );
                    $last_child.find( 'a.down' ).removeClass().addClass( 'down down-dis' ).attr( 'href', 'javascript://' );

                    $childe_items.children().not( $first_childe.add($last_child) ).each( function()
                    {
                        var $handler = $( this );

                        $handler.find( 'a.up' ).removeClass().addClass( 'up up-enb' ).attr( 'href', URL_ADMIN + 'nav_menu.php?move=up&item=' + $handler.find('[name="menu_item_id"]:first').val() );
                        $handler.find( 'a.down' ).removeClass().addClass( 'down down-enb' ).attr( 'href', URL_ADMIN + 'nav_menu.php?move=down&item=' + $handler.find('[name="menu_item_id"]:first').val() );
                    });
                }
            }
        });
    }

    var findChildNode = function( node, menuList )
    {
        node.each( function(index)
        {
            var $handler = $( this );

            menuList.push(
            {
                'order': ++index,
                'menu_id': +$handler.find( '[name="menu_id"]:first' ).val(),
                'menu_item_id': +$handler.find( '[name="menu_item_id"]:first').val(),
                'parent_menu_item_id': +$handler.find( '[name="parent_menu_item_id"]:first' ).val()
            });

            var $childNode = $handler.find( 'ol li' );

            if ( $childNode.length )
            {
                findChildNode( $childNode, menuList );
            }
        });
    }

    $( '#sort-table' ).nestedSortable(
    {
        forcePlaceholderSize: true,
        disableNesting: 'no-nesting',
        handle: 'div',
        helper:	'clone',
        items: 'li',
        opacity: 0.7,
        placeholder: 'placeholder',
        revert: 250,
        tabSize: 25,
        tolerance: 'pointer',
        toleranceElement: '> div',
        maxLevels: 2,
        isAllowed: isAllowed,
        update: update
    });
});
