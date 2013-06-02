function component_FBCButton(auto_id)
{
	this.DOMConstruct('FBCButton', auto_id);

	this.actionUrl = '';
}

component_FBCButton.prototype =
	new SK_ComponentHandler({

		initParams: {},
		inited: false,

		construct : function( initParams, actionUrl ) {

			var self = this;
			this.initParams = initParams;
			this.actionUrl = actionUrl;

			self.init();

			this.$('#btn').click(function() {
                            FB.getLoginStatus(function(response)
                            {
                                    if(response.authResponse) {
                                            self.actionRedirect();
                                    } else {
                                            self.login();
                                    }
                            });
			});
		},

		init: function(){
			if (typeof window.fbInited == 'undefined')
			{
				$('body').prepend('<div id="fb-root"></div>');

				FB.init(this.initParams);
				window.fbInited = true;
			}
		},


		login: function() {
			var self = this;
			FB.login(function(response) {
                            if (response.authResponse) {
                                    self.actionRedirect();
                            }
			}, {scope: 'email,user_about_me,user_birthday'});
		},

		actionRedirect: function(){
			window.location.href = this.actionUrl;
		}

	});
