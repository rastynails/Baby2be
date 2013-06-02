function component_LatestActivity(auto_id)
{
    this.DOMConstruct('LatestActivity', auto_id);

    var handler = this;

    this.autoId = auto_id;

    this.$viewMore = this.$('.latest_activity_view_more_c');

    this.$viewMore.find('input.latest_activity_view_more').click(function(){
        $(this).addClass('in_process');
        handler.loadMore();
    });

}

component_LatestActivity.prototype = new SK_ComponentHandler({

        construct: function(actor, userId, counter)
        {
            this.actor = actor;
            this.userId = userId;
            this.counter = counter;
        },

        loadMore: function()
        {
            var handler = this;
            
            this.counter++;
            handler.reload({counter: this.counter, userId: this.userId,  actor: this.actor});
        },

        /**
	     * @return jQuery
	     */
        $: function(selector)
        {
            return $(selector, this.containerNode);
        }


});