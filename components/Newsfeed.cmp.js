window.sk_newsfeed_const = {};
window.sk_newsfeed_feed_list = {};

function component_Newsfeed(auto_id)
{
    this.DOMConstruct('Newsfeed', auto_id);

    var handler = this;

    window.sk_newsfeed_feed_list = this;


    this.autoId = auto_id;
    this.containerNode = $('#' + auto_id).get(0);
    this.$listNode = this.$('.newsfeed');

    this.totalItems = 0;
    this.actionsCount = 0;
    this.viewMoreCounter = 0;

    this.actions = {};
    this.actionsById = {};


    this.$viewMore = this.$('.newsfeed_view_more_c');

    this.$viewMore.find('input.newsfeed_view_more').click(function(){
        $(this).addClass('in_process');
        handler.loadMore();
    });


    $(document).bind('base.comments_list_init', function(p){

        if ( handler.actions[p.entityType + '.' + p.entityId] )
        {
            handler.actions[p.entityType + '.' + p.entityId].comments = this.totalCount;
            handler.actions[p.entityType + '.' + p.entityId].refreshCounter();
        }
    });

    this.delegates = {

    };
}

component_Newsfeed.prototype =
    new SK_ComponentHandler({

        construct: function(total, data)
        {
            this.totalItems = total;
            this.data = data;
        },

        adjust: function()
        {
            this.$('.newsfeed_section').each(function() {
                if ( !$(this).next().is('.newsfeed_item') )
                {
                    $(this).remove();
                }
            });

            if ( this.$listNode.find('.newsfeed_item:not(.newsfeed_nocontent)').length )
            {
                this.$listNode.find('.newsfeed_nocontent').hide();
            }
            else
            {
                this.$listNode.find('.newsfeed_nocontent').show();
            }
        },

        reloadItem: function( actionId )
        {
            var action = this.actionsById[actionId];

            if ( !action )
            {
                return false;
            }

            this.loadItemMarkup({
                actionId: actionId,
                cycle: action.cycle
            }, function($m){
                $(action.containerNode).replaceWith($m);
            });
        },
        /*
        loadItemMarkup: function(params, callback)
        {
            var self = this;

            params.feedData = this.data;
            params.cycle = params.cycle || {
                lastSection: false,
                lastItem: false
            };

            params = JSON.stringify(params);

            $.getJSON(window.sk_newsfeed_const.LOAD_ITEM_RSP, {
                p: params
            }, function( markup ) {

                var $m = $(markup.html);
                callback.apply(self, [$m]);
                //OW.bindAutoClicks($m);

                self.processMarkup(markup);
            });
        },

        loadNewItem: function(params, preloader)
        {
            if ( typeof preloader == 'undefined' )
            {
                preloader = true;
            }

            var self = this;
            if (preloader)
            {
                var $ph = self.getPlaceholder();
                this.$listNode.prepend($ph);
            }
            this.loadItemMarkup(params, function($a) {
                this.$listNode.prepend($a.hide());

                self.adjust();
                if ( preloader )
                {
                    var h = $a.height();
                    $a.height($ph.height());
                    $ph.replaceWith($a.css('opacity', '0.1').show());
                    $a.animate({
                        opacity: 1,
                        height: h
                    }, 'fast');
                }
                else
                {
                    $a.animate({
                        opacity: 'show',
                        height: 'show'
                    }, 'fast');
                }
            });
        },*/

        appendList: function(markup)
        {
            var self = this;
            var li = this.lastItem;
            var $m = $(markup.html).filter('li');

            self.$viewMore.hide();
            li.$delim.show();

            self.$listNode.append($m);
            if ( self.totalItems > self.actionsCount)
            {
                self.$viewMore.show();
            }

            this.$viewMore.find('input.newsfeed_view_more').removeClass('in_process');

            jQuery('.block_expand, .block_collapse')
            .each(function() {
                var block_node = this.parentNode.parentNode.parentNode.parentNode;
                if (typeof block_node.sk_block_handler == 'undefined')
                {
                    if (jQuery(block_node).hasClass('block')) {
                        block_node.sk_block_handler = new SK_BlockHandler(block_node);
                    }
                }
            });
        },

        loadMore: function()
        {
            var self = this;
            var li = this.lastItem;

            var params = this.data;
                params.startTime = li.updateStamp;

            self.ajaxCall("ajax_LoadItemList", {
                p: params
            });

            this.viewMoreCounter++;

        },

        getPlaceholder: function()
        {
            return $('<li class="newsfeed_placeholder preloader"></li>');
        },

        processMarkup: function( markup )
        {
            if ( markup.styleDeclarations )
            {
                $('head').append($('<style type="text/css">'+markup.styleDeclarations+'</style>'));
            }

            if ( markup.onloadScript )
            {
                eval(markup.onloadScript);
            }
        },

        /**
	     * @return jQuery
	     */
        $: function(selector)
        {
            return $(selector, this.containerNode);
        }


});