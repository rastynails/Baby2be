function component_NewsfeedFeedItem(auto_id)
{
    this.DOMConstruct('NewsfeedFeedItem', auto_id);

    var handler = this;
    this.feed = window.sk_newsfeed_feed_list;
    window.sk_newsfeed_feed_list.actionsById[auto_id] = this;
    window.sk_newsfeed_feed_list.actionsCount++;
    if ( window.sk_newsfeed_feed_list.actionsCount == (window.sk_newsfeed_feed_list.viewMoreCounter * window.sk_newsfeed_feed_list.data.data.displayCount + 1) )
    {
        window.sk_newsfeed_feed_list.lastItem = this;
    }

    this.delegates = {

    };
}

component_NewsfeedFeedItem.prototype =
    new SK_ComponentHandler({

        construct: function(data)
        {
            var self = this;

            this.item_auto_id = data.item_auto_id;
            this.containerNode = $('#'+data.item_auto_id);
            this.entityType = data.entityType;
            this.entityId = data.entityId;
            this.id = data.id;
            this.updateStamp = data.updateStamp;
            this.profile_id = data.profile_id;

            this.likes = data.likes;

            this.comments = data.comments;

            this.cycle = data.cycle || {
                lastSection: false,
                lastItem: false
            };

            this.$featuresCont = $('#'+this.item_auto_id+' .newsfeed-features');

            this.$commentBtn = $('#'+this.item_auto_id+' .newsfeed_comment_btn');
            this.$likeBtn = $('#'+this.item_auto_id+' .newsfeed_like_btn');
            this.$unlikeBtn = $('#'+this.item_auto_id+' .newsfeed_unlike_btn');
            this.$removeBtn = $('#'+this.item_auto_id+' .newsfeed_remove_btn');
            this.$delim = $('#'+this.item_auto_id+' .newsfeed-item-delim');

            this.$commentBtn.click(function(){
                self.showComments();
            });

            this.$likeBtn.click(function(){
                self.like();
            });

            this.$unlikeBtn.click(function(){
                self.unlike();
            });

            this.$removeBtn.click(function(){
                if ( confirm(this.rel) )
                {
                    self.remove();
                }
            });

            this.$('.showLikesUserlist').click(function(){
                var users = $('.showLikesUserlistValue').attr('value');

                self.ajaxCall("ajax_LoadProfilesInfo", {users: users});

            });

            this.$('.newsfeed_features_btn').click(function(){
                if ( self.$featuresCont.is(':visible') )
                {
                    self.$featuresCont.slideUp('fast');
                }
                else
                {
                    self.$featuresCont.slideDown('fast');
                }

            });

            SK_EventManager.bind(self.item_auto_id+'_onNewsfeedCommentListCommentDelete', function(){
                if (self.comments > 0 )
                {
                    self.comments--;
                }
                self.refreshCounter();
            });

        },

        refreshCounter: function() {
            var $c = $('#'+this.item_auto_id+' .newsfeed_counter').hide(),
            $likes = $c.find('.newsfeed_counter_likes').hide(),
            $comments = $c.find('.newsfeed_counter_comments').hide(),
            $delim = $c.find('.newsfeed_counter_delim').hide();


            if ( this.likes > 0 && this.comments > 0 )
            {
                $delim.show();
            }

            if ( this.likes > 0 || this.comments > 0 )
            {
                $c.show();
            }

            if ( this.likes > 0 )
            {
                $likes.show().text(this.likes);
            }

            if ( this.comments > 0 )
            {
                $comments.show().text(this.comments);
            }
        },

        showComments: function()
        {
            this.$commentBtn.parents('.newsfeed_control:eq(0)');
            var $c = this.$featuresCont.show();
            $c.show().find('.newsfeed_comments').show().find('.newsfeed_comment_input').focus();
            var $delim = $c.find('.newsfeed_delimiter');

            if ( this.likes != 0 )
            {
                $delim.show();
            }

        },

        like: function()
        {
            var self = this;

            this.$unlikeBtn.show();
            this.$likeBtn.hide();

            self.ajaxCall("ajax_Like", {
                entityType: self.entityType,
                entityId: self.entityId
            });
        /*

             **/
        },

        updateLikes: function(count, markup)
        {
            this.likes = count;
            this.showLikes(markup);
            this.refreshCounter();
        },

        unlike: function()
        {
            var self = this;

            this.$unlikeBtn.hide();
            this.$likeBtn.show();

            self.ajaxCall("ajax_Unlike", {
                entityType: self.entityType,
                entityId: self.entityId
            });
        },

        showLikes: function( likesHtml )
        {
            var $c = this.$featuresCont;

            if ( this.comments != 0 || this.likes != 0 )
            {
                $c.show();
            }
            else
            {
                $c.hide();

                return;
            }

            var $delim = $c.find('.newsfeed_delimiter').hide();
            var $likes = $c.find('.newsfeed_likes').hide();
            $likes.empty().html(likesHtml);

            if ( this.likes != 0 )
            {
                $likes.show();
            }

            if ( this.comments != 0 && this.likes != 0 )
            {
                $delim.show();
            }
        },

        remove: function()
        {
            var self = this;

            self.ajaxCall("ajax_Delete", {
                actionId: this.id,
                profile_id: this.profile_id
            });

        },

        hide: function()
        {
            var self = this;

            $('#'+this.item_auto_id).animate({
                opacity: 'hide',
                height: 'hide'
            }, 'fast', function() {
                $(this).remove();
                self.feed.adjust();
            });
        },

        reload: function()
        {
            window.location.reload();
        },

        showLikesUserList: function( markup )
        {
            $(".newsfeed_likes_userlist").html(markup);
            var box = new SK_FloatBox({
                $title		: $(".newsfeed_likes_userlist_cap"),
                $contents	: $(".newsfeed_likes_userlist"),
                width		: 450
            });

        },

        /**
     * @return jQuery
     */
        $: function(selector)
        {
            return $(selector, this.containerNode);
        }
    });