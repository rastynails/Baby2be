function component_InviteFriends(auto_id)
{
	this.DOMConstruct('InviteFriends', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_InviteFriends.prototype =
	new SK_ComponentHandler({
		
		float_box:{},
		
		$import_container :{},
		
		contacts:{},
		
		$list_block: {},
		
		construct : function(){
			var handler = this;
			
			this.$import_container = this.$("#box_content").children(".import_container");
									
			this.$("#open_box_btn").click(function(){
				handler.showImportContactsBox();
			});
			
			this.$list_block = this.$block("#contact_list");
			
			this.$list_block.bind("expand", function(){
				handler.showList();
			});
			
			this.$list_block.bind("collapse", function(){
				handler.hideList();
			});
			
		},
		
		showImportContactsBox: function(){
			var handler = this;
			var $title = this.$("#box_title").children().clone();
			var $content = this.$import_container;
			box = new SK_FloatBox({
				$title: $title,
				$contents: $content,
				width: 550			
			});
			this.float_box = box;

			var $list_node = this.$import_container.find('.contact_list');
			$list_node.find('input[name=add_contact]')
					.click(function(){
						$checked = $list_node.find('input:checked');
						if ($checked.length>0){
							var checked_contacts = [];
							$.each($checked, function(i, obj){
								checked_contacts[i] = $(obj).val();
							});
							handler.addContacts(checked_contacts);
						}
					});	
					
			var $import_form = this.$import_container.find("form");
			$import_form.submit(function(){
				handler.loadContacts($import_form);	
				return false;
			});	

			/*this.$import_container.find('div.open_list').unbind().toggle(
			function(){
				handler.hideList();
			},
			function(){
				handler.showList();
			});*/
			
			$list_node.find('.contact_item .info').click(function(){
				var $checkbox = $(this).parent().find(':checkbox');
				$checkbox.attr('checked',!$checkbox.attr('checked'));
			});
			
			$list_node.find('.check_all input:checkbox').click(function(){
				$list_node.find('input:checkbox').attr('checked', $(this).attr('checked'));
			});
					
		},
		
		loadContacts: function($form){
			var handler = this;
			var email = $form.get(0).email.value;
			var password = $form.get(0).password.value;
			var provider = $form.get(0).provider.value;
			
			this.clearList();
			var $preloader = $('<div class="preloader" style="display:none">').prependTo(this.$import_container.find('.auth_cont'));
			
			$preloader.css('height',this.$import_container.height()); 
			$preloader.fadeIn('normal');
			
			this.ajaxCall('ajax_loadContacts', {email: email, password: password, provider: provider},{success: function(){
				$preloader.fadeOut('fast',function(){$preloader.remove();});
				handler.$import_container.find("form").get(0).reset();
			}});
		},
		
		drawContact: function(name, address){
			if (!this.$list_note) {
				this.$list_note = this.$import_container.find('.contact_list');
				this.$prototype_node = this.$list_note.find('.prototype_node');
				this.$contact_node_parent = this.$list_note.find('.prototype_node').parent();
			}
					
			if ( (name==undefined || name=="") || (address==undefined || address=="") ) {
				return false;
			}
			var handler = this;
						
			var $list_node = this.$list_note;
			var $contact_node = this.$prototype_node.clone();
			var $contact_node_parent = this.$contact_node_parent;
			$contact_node.removeClass('prototype_node');
			var $checbox = $contact_node.find(".check :checkbox");
			$checbox.val(name);
			$contact_node.find('.info').click(function(){
				$checbox.attr('checked',!$checbox.attr('checked'));
			});
			$contact_node.find(".name").text(name);
			
			$contact_node.find(".address").text(address);
			
			this.cicle = !this.cicle;
			
			this.contacts[name] = {
				name: name,
				address: address,
				$node: $contact_node 
			};
			
			$contact_node_parent.append($contact_node);

		},
		
		drawList: function(items){
			var handler = this;
			
			$.each(items, function(i, item){
				handler.drawContact(item.name, item.address);
			});
			
			this.$import_container.find('.contact_list table tr:odd').addClass('list_odd');
								
			this.$list_block.expand();
		},
		
		showList: function(){
			
			var handler = this;
			var $list_node = this.$import_container.find('.contact_list').show();
			var $auth_cont = this.$import_container.find('.auth_cont');
			$list_node.find(".empty_list").hide();
									
			if (this.contacts.length == 0 ) {
				$list_node.find(".empty_list").show();
			}
			
			$auth_cont.find('.mobile_cont').slideUp('fast');
		},
		
		hideList: function(){
			var handler = this;
			var $list_node = this.$import_container.find('.contact_list');
			var $auth_cont = this.$import_container.find('.auth_cont');	
			$list_node.find(".empty_list").hide();
				
			$auth_cont.find('.mobile_cont').slideDown('fast');
		},
		
		addContacts : function(names)
		{
			var $addr_field = this.$('.invite_form_cont').find("form textarea[name=email_addr]");
			$addr_field.val('');
			for(var i=0 ; i < names.length ; i++){
				
				var name = names[i];
				if (this.contacts[name]!=undefined){
					
					var last_val = $addr_field.val();
					last_val += this.contacts[name].address+"\n"
					$addr_field.val(last_val);
				}
			}
			
			this.float_box.close();
		},
		
		clearList:function(){
			for(var key in this.contacts){
				this.contacts[key].$node.remove();
			}
		},
		
		debug: function(obj) {
			console.debug(obj);
		}
	});