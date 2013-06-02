
function component_UserSelectorPopup(auto_id)
{
    this.DOMConstruct('UserSelectorPopup', auto_id);

    this.emails = {};
}

component_UserSelectorPopup.prototype =
    new SK_ComponentHandler({

    construct: function()
    {
        var self = this;

        this.$emails = $('input[name=emails]');

        $(document).on('click', '.usi-selected', function() {
            $(this).removeClass('usi-selected');

            self.removeEmail($(this).data('email'));
        });

        $(document).on('click', '.user-selector-item:not(.usi-selected)', function() {
            $(this).addClass('usi-selected');

            self.addEmail($(this).data('email'));
        });
    },

    updateCount: function()
    {
        var count = $('.usi-selected').length;
        $('.us-selected-count').text(count);
    },

    addEmail: function( email )
    {
        this.emails[email] = email;
        this.saveEmailList();

        this.updateCount();
    },

    removeEmail: function ( email )
    {
        delete this.emails[email];
        this.saveEmailList();

        this.updateCount();
    },

    saveEmailList: function()
    {
        var list = [];

        $.each(this.emails, function(i, email) {
            if ( email ) list.push(email);
        });

        this.$emails.val(list.join(','));
    }
});
