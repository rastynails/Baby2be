function component_ProfileView(auto_id)
{
	this.DOMConstruct('ProfileView', auto_id);

	this.references = [];

	this.$marker = $('<div></div').addClass( 'marker' );

	this.marker = {position:null,section:null,prevSection:null,prevPosition:null,icon:false,iconDrop:false};

	this.ddBoxes = {};

	this.auto_id = auto_id;

	this.delegates = {

	};
}

component_ProfileView.prototype =
	new SK_ComponentHandler({

		construct : function( param ){
			var handler = this;

			this.$('#photo_album .block_cap').css({cursor:'auto'});
			this.$('#photo_album .block_cap h3').css({cursor:'auto'});
			this.$('#profile_dtls .block_cap').css({cursor:'auto'});
			this.$('#profile_dtls .block_cap h3').css({cursor:'auto'});

			//console.debug( param );
			if( !param.view_mode )
			{
				$('.delete_cmp').css({display:'none'});
				$('.ddbox').removeClass('ddbox');
			}
			if( param.vcmps == null && param.icons == null ) return;

			this.vcmps = param.cmp_view;
			//this.icons = param.icons;
			//console.debug(param.cmp_view);
			$.each( this.vcmps,
				function( index, data ){
					handler.ddDragBind( data.id );
				}
			);
			/*
			$.each( param.icons,
				function(index,data){
					handler.bindIconsDrag( data.id );
				}
			);
			*/
			// TEMP
			this.ddDropBind( 'photo_album', 1, 1 );
			this.ddDropBind( 'profile_dtls', 2, 1 );



		},



// ---------- function to bind drag events for cmp items ---------- //

		ddDragBind: function( id ){

			var handler = this;
			var $id = handler.$( '#'+id );
			var $cap = $( '.block_cap:first', $id );
			var id = id;
			var visualHeight = $(window).height();
			var documentHeight = $(document).height();
			var $document = $(document);
			var dc = document.selection;


			$id
				.bind( 'mouseover', function(){$(this).addClass('ddboxHover');})
				.bind( 'mouseout', function(){$(this).removeClass('ddboxHover');})
				.unbind( 'dragstart' ).unbind( 'drag' ).unbind( 'dragend' )

				.bind( 'dragstart',
					function( e ){
						//if ($.browser.msie) dc.empty();
						if ( !$(e.target).is('.block_cap') && !$(e.target).is('.block_cap_title') ) return false;
						//if ( !$(e.target).is('.ddbox_cap') || !$(e.target).is('.ddbox_cap h3') ) return false;

						var widthF = $id.innerWidth();
						var heightF = $id.innerHeight();
						var offsetF = $id.offset({relativeTo:document.body,scroll:false});

						$(this).addClass( 'ghost' );

						$id.after( handler.$marker.css( {height:(heightF-4)} ) )
						.css({width:widthF, position:'absolute'});
						//alert( visualHeight );
						handler.marker.prevSection = handler.vcmps[id].section;
						handler.marker.prevPosition = handler.vcmps[id].position;
						handler.marker.position = null;
						handler.marker.section = null;

						//$id.removeClass('ddbox');
						handler.vcmps[id].active = true;
						handler.bindAllDdBoxesDrop();
						//this.vcmps[id].active = false;
						//$id.addClass('ddbox');

						$.dropManage({filter:'.ddbox'});
						//$.dropManage({ filter:'.drop' });
						if ($.browser.msie) dc.empty();
						return $(this);
					 }
				)
				.bind( 'drag',
					 function( e ){
						if ($.browser.msie) dc.empty();
						$( e.dragProxy ).css({left: e.offsetX, top: e.offsetY});

						var scrollTop = $document.scrollTop();
						//var heightF = $id.innerHeight();
						var diffB = e.offsetY + 50 - scrollTop;

						//console.debug(e.offsetY + '|' + scrollTop );
						if( diffB > visualHeight )
							$document.scrollTop( scrollTop + ( diffB  - visualHeight ) );

						if( scrollTop > 0 && e.offsetY < scrollTop )
							$document.scrollTop(  e.offsetY );

						if($.browser.msie) dc.empty();
					}
				)
				.bind( 'dragend',
					function( e ){
						if ($.browser.msie) dc.empty();
						$cap.trigger( 'mouseup' );
						$( this ).removeClass( 'ghost' ).trigger( 'mouseout' );
						handler.$marker.replaceWith( $id.css( {position:'static', width:'auto', height:'auto'} ) );

						handler.setCmpPosition(id);

						handler.vcmps[id].active = false;

						handler.bindAllDdBoxesDrag();

						if ($.browser.msie) dc.empty();
					}
				);

				$( '.delete_cmp', $id ).unbind( 'click');

				$( '.delete_cmp', $id ).bind( 'click',
					function(){

						var decrArray = [];

						$.each( handler.vcmps,
							function( index, data ){
								if( data.section == handler.vcmps[id].section && handler.vcmps[id].id != data.id && data.position > handler.vcmps[id].position ){
									data.position--;
									decrArray.push( data.nid );
								}

							}
						);
						delete handler.vcmps[id];
						//alert( JSON.stringify( decrArray ) );
						handler.ajaxCall('ajax_removeCmp',
							{id:id,decrArray:decrArray},
							{success: function( data ){handler.addCmpItem( data )}}
						);


						$id.slideUp(500, function(){$(this).remove();});
					}
				);

		},

		addCmpItem:function( data ){

			if( data.class_name == 'CustomHtml' )
				return;

			var children = this.children;

			var cmps;

			for (var i = 0; i < children.length; i++) {
				var child = children[i];

				if (child instanceof component_ProfileComponentSelect) {
					cmps = child.cmps;
					break;
				}
			}

			cmps[data.id] = {id:data.id, status:false};


			var icon = $('<div class="pvs_cmp_cont" id="'+ data.id +'"><div class="pvs_cmp '+ data.class_name +'"></div><div class="name">'+ data.label +'</div></div>');

			icon.bind( 'click', function(){
				if( cmps[data.id].status == true ){
					$( this ).removeClass('selected');
					cmps[data.id].status = false;
				}
				else{
					$( this ).addClass('selected');
					cmps[data.id].status = true;
				}

			} );

			$('.pvs_cmp_pp_cont').append( icon );
			$('.no_cmp').css( {display:'none'} );
			$('.pcv_add_cmps').removeAttr('disabled');

		},

// ---------- function to bind drop events for cmp items ---------- //

		ddDropBind: function( id, section, position ){
			//alert(section.toString()+position.toString());
			var handler = this;

			handler.$( '#'+id )
				.unbind( 'dropstart' ).unbind( 'drop' ).unbind( 'dropend' )
				.bind( 'dropstart',
					function( e ){

						if( handler.marker.icon ){
							handler.marker.iconDrop = true;
						}


						$(this).after( handler.$marker );

						handler.marker.section = null;
						handler.marker.position = null;

						if( handler.marker.prevSection == section && handler.marker.prevPosition == position ) return;

						handler.marker.section = section;
						handler.marker.position = position;
					}
				)
				.bind( 'drop',
					function( e ){
					}
				)
				.bind( 'dropend',
					function( e ){
					}
				);
		},

// ---------- function to rebind drag events for all cmp items ---------- //

		bindAllDdBoxesDrag: function(){
			var handler = this;
			$.each( this.vcmps,
				function( index, data ){
					//if( data.active ) return;
					handler.$(data.id).unbind( 'mouseover' ).unbind( 'mouseout' ).unbind( 'dropstart' ).unbind( 'drop' ).unbind( 'dropend' );
					handler.ddDragBind( data.id );
				}
			);
		},

// ---------- function to rebind drop events for all cmp items ---------- //

		bindAllDdBoxesDrop: function(){
			var handler = this;
			$.each( this.vcmps,
				function( index, data ){
					if( data.active ) return;
					handler.$(data.id).unbind( 'mouseover' ).unbind( 'mouseout' ).unbind( 'dragstart' ).unbind( 'drag' ).unbind( 'dragend' );
					handler.ddDropBind( data.id, data.section, ( data.position + 1 ) );
				}
			);
		},


		setCmpPosition: function( id ){
            //console.log(this.vcmps);
            //console.log(id);
			//alert( this.marker.prevSection + ' | ' + this.marker.section + ' | ' +  this.marker.prevPosition + ' | ' + this.marker.position );
			if( this.marker.section == null || this.marker.position == null ) return;

			if( this.marker.prevSection == this.marker.section && this.marker.prevPosition < this.marker.position ) this.marker.position--;

			if( this.marker.prevSection == this.marker.section && this.marker.prevPosition == this.marker.position ) return;

			var handler = this;

			var incrArray = [];
			var decrArray = [];

			this.vcmps[id].section = this.marker.section;
			this.vcmps[id].position = this.marker.position;

			if( this.marker.prevSection == this.marker.section )
			{
				if( this.marker.prevPosition > this.marker.position )
				{
					$.each( this.vcmps,
						function( index, data ){
							if( data.section == handler.marker.section && data.position < handler.marker.prevPosition && data.position > handler.marker.position && data.id != id){
								data.position++;
								incrArray.push(data.nid);

							}
						}
					);
				}
				else
				{
					$.each( this.vcmps,
						function( index, data ){
							if( data.section == handler.marker.section && data.position <= handler.marker.position && data.position > handler.marker.prevPosition  && data.id != id){
								data.position--;
								decrArray.push(data.nid);
							}
						}
					);
				}
			}
			else
			{
				$.each( this.vcmps,
					function( index, data ){
						if( data.section == handler.marker.section ){
							if( data.position >= handler.marker.position && data.id != id ){
								data.position++;
								incrArray.push(data.nid);
							}
						}
						else{
							if( data.position > handler.marker.prevPosition ){
								data.position--;
								decrArray.push(data.nid);
							}
						}
					}
				);
			}
			//alert(JSON.stringify(incrArray));alert(JSON.stringify(decrArray));
			this.ajaxCall('ajax_changeCmpPosition',
				{id:id, section:this.marker.section, position:this.marker.position, incrArray:incrArray, decrArray:decrArray},
				{error: function(){window.location.reload();}}
			);

		}
	});