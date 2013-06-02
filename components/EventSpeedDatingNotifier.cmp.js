function component_EventSpeedDatingNotifier(auto_id)
{
	this.DOMConstruct('EventSpeedDatingNotifier', auto_id);

	var handler = this;

	this.references = [];

	this.cmps = {};
}

component_EventSpeedDatingNotifier.prototype =
	new SK_ComponentHandler({
		construct : function(){

                    var handler = this;
                    this.floatBox = null;
                    ESD_currentIM = null;
                    this.profileNote = null;
                    this.username = null;

                    handler.$('#esd_notifier_submit').bind( 'click', function() {
                        handler.floatBox.close();
                    });

                    $('#esd_notifier_next_member').click( function() {
                        if( confirm("Do you really want to stop private session with current member and search next?") )
                        {
                            handler.ajaxCall('ajax_StopDating');
                        }
                    });

                    $('#esd_notifier_reopen').click( function(){
                        handler.ajaxCall('ajax_StartDating');
                    });


                    this.pingCommand = SK_Ping.getInstance().addCommand('speedDating', {
                        params: {

                        },
                        before: function()
                        {
                            if (typeof is_123wm != "undefined" && is_123wm)
                            {
                                if (typeof handler.username != "undefined" && handler.username)
                                {
                                    if (document.getElementById("container_" + handler.username) && document.getElementById("container_" + handler.username ).innerHTML != "")
                                    {
                                        document.getElementById("container_" + handler.username ).innerHTML = "";
                                    }
                                }
                                if (typeof FC_windowObject != "undefined" && FC_windowObject !== null)
                                {
                                    handler.params = { im_closed: FC_windowObject.closed }

                                    if( FC_windowObject.closed )
                                            FC_windowObject = undefined;
                                }
                                else if (typeof handler.profileNote != "undefined" && handler.profileNote !== null)
                                {
                                    this.params = { note_closed: handler.profileNote.closed }

                                    if (handler.profileNote.closed)
                                        handler.profileNote = undefined;
                                }

                            }
                            else
                            {
                                if (typeof ESD_currentIM != "undefined" && ESD_currentIM !== null)
                                {
                                    this.params = { im_closed: ESD_currentIM.closed }
                                    $('#esd_notifier_reopen_container').css("display", "none");
                                }
                                else
                                {
                                    if (typeof this.profileNote != "undefined" && handler.profileNote !== null)
                                    {
                                        this.params = { note_closed: handler.profileNote.closed }

                                        if (handler.profileNote.closed)
                                            handler.profileNote = undefined;
                                    }
                                    else
                                    {
                                        $('#esd_notifier_reopen_container').css("display", "block");
                                    }
                                }
                            }
                        },
                        after: function( res )
                        {
                            if ( res.js )
                            {
                                (new Function(res.js)).call(handler);
                            }
                        }
                    });

                    this.pingCommand.start(5000);

		},

		/**
		 * Start session with profile
		 */
		startDating: function( username, chat ){
			this.username = username;

			ESD_currentIM = eval(chat);
		},

		/**
		 * Stop current session with profile
		 */
		stopDating: function( event_id, opponent_id ){
			if (typeof ESD_currentIM != "undefined" && ESD_currentIM !== null)
			{
                ESD_currentIM.close();
				ESD_currentIM = undefined;
				this.username = undefined;
			}
			this.profileNote = SK_profileNote( event_id, opponent_id );
		},

		/**
		 * Start 123 session with profile
		 */
		start123FlashChatDating: function( username ){
			this.username = username.toLowerCase();
			window.initiate123wm( this.username );
		},

		/**
		 * Accept invitation in 123 Flash chat
		 */
		popup123FlashChatDating: function( username )
		{
			this.username = username.toLowerCase();
		},

		stop123FlashChatDating: function ( event_id, opponent_id )
		{
			if (typeof FC_windowObject != "undefined" && FC_windowObject !== null)
			{
                FC_windowObject.close();
				FC_windowObject = undefined;
				this.username = undefined;
			}
			this.profileNote = SK_profileNote( event_id, opponent_id );
		},



		/**
		 * Immediately stop pinging.
		 */
		stop: function() {
			this.pingCommand.stop();
		},

		// debug
		drawCurrentStatus: function( info )
		{
			this.$(".block_info").html( info );
		},

		drawProfileStatus: function( username, elapsed_time )
		{
			$("#esd_profile_in_session").html( username );
			$("#esd_elapsed_time").html( elapsed_time );
            if (elapsed_time == "")
            {
                $('#esd_notifier_next_member').attr("disabled", "disabled");
            }
            else
            {
                $('#esd_notifier_next_member').removeAttr("disabled");
            }
		},


		/**
		 * Show float box with notification about beginning and ending of speed dating event
		 */
		drawSpeedDatingNotifications: function( event, message)
		{
			var handler = this;
			if (handler.floatBox)
				handler.floatBox.close();

			var $event = this.$("#event_speed_dating_notifier_content");
				$event.find('.block_body').html(message);

				var $data = handler.$( '#event_speed_dating_notifier_content' );


				handler.floatBox = new SK_FloatBox({
					$title		: $( '.title', $data ),
					$contents	: $( '.content', $data ),
					width		: 690
				});
		},

		showNotifier: function()
		{
			$(".event_speed_dating_container").css("display", "block");
		},

		hideNotifier: function()
		{
			$(".event_speed_dating_container").css("display", "none");
		}

	});