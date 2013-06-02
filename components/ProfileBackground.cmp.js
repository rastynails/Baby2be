function component_ProfileBackground(auto_id)
{
	this.DOMConstruct('ProfileBackground', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ProfileBackground.prototype =
	new SK_ComponentHandler({
		
		construct : function(color, image, owner, mode){
			
			var handler = this;
			
			var $no_image = handler.$('#no_image');
			
			handler.mode = mode;
			
			if( color != null && color != '' )
				$('body').css({backgroundColor:color, backgroundImage:'none'});

			if( image != null && mode != null )
				$('body').css({backgroundImage:'url('+image+')', backgroundRepeat:'repeat'});
			
			
			if(!owner) return;
			
			var $input_file_cont = handler.$('#input_file_cont');
			var $img_file_cont = handler.$('#img_file_cont');
			
			var cpCallback = function(color){handler.setBgColor(color); handler.bgFloatBox.close();}
			
			this.cp = new ColorPicker(this.$('#color_picker'), cpCallback);
			
			if(color != '' && color != null)
				this.cp.setColor(color);
				
			this.$('#bg_color').bind('click',
				function(){
					handler.bgFloatBox = new SK_FloatBox({
						$title		: handler.$( '#cp_cap_label' ),
						$contents	: handler.$( '#color_picker_cont' ),
						width		: 277
					});		
				}
			);
		
			this.$('#cancel').bind('click', function(){handler.bgFloatBox.close();});
			this.$('#no_color').bind('click', function(){handler.setBgColor(null);handler.bgFloatBox.close();})
			
			this.ss = handler.$( '#bg_image_cont' );
			
			this.$('#bg_image').bind('click',
				function(){
					handler.bgFloatBox = new SK_FloatBox({
						$title		: handler.$( '#image_cap_label' ),
						$contents	: handler.$( '#bg_image_cont' ),
						width		: 330
					});	
					
					handler.forms[0].fields.file.construct($(handler.forms[0].$form[0].file), handler.forms[0]);
				}
			);
			
			
			this.$('#img_file_cont a').bind( 'click', function(){
				$img_file_cont.css({display:'none'});
				
				handler.ajaxCall('ajax_deleteImage', {id:owner}, {success: function(){
					$input_file_cont.css({display:'block'});
				}} );
			} );

			var $mode = $(this.forms[0].$form[0].image_mode);
			
			this.$('#upload_radio').bind('click', function(){
					$mode.attr('value', '1');
				}
			);
			
			this.$('#url_radio').bind('click', function(){
					$mode.attr('value', '2');
				}
			);
			
			if( handler.mode == 1 )
				this.$('#upload_radio').attr('checked', true);
			else
				this.$('#url_radio').attr('checked', true);
				
				
			$no_image.bind('click', 
				function(){
					$mode.attr('value', '');
					handler.forms[0].$form.submit();
				}
			);
			
		},
		
		setBgColor : function(color){
			
			if(color == null)
				$('body').removeAttr('style');
			else		
				$('body').css({backgroundColor:color});
				
			this.ajaxCall('ajax_setBgColor',{color:color});
		},
		
		setBgImage: function(bgImageMode){
			
			var handler = this;
			
			handler.ajaxCall('ajax_getBgImage', {mode:bgImageMode}, {success:function(data){
				$('body').css({backgroundImage:'url('+data.image+')'});
			}});
		}
	});