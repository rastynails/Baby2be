
var adminClassifieds= 
{	
	construct: function( groups, entity ) {		
		var handler = this;
		
		this.entity = entity;		
		this.groups = groups;
		this.editgroup_box = [];
		this.preloader = '<div class="cls_loading">Please, wait...</div>';
		
		for(var key in handler.actions) {
			handler.actions[key](handler);
		}
				
		this.bindGroups();
		
		$jq("#add_group").bind("click", function() {
			handler.addNewGroup();
		});
		
		$jq("#move_group").bind("click", function() {
			handler.moveGroup();
		});	
	},
	
	ajaxCall: function(apply_func, params, callback) {
		var handler = this;
		$jq.ajax({
				url: URL_ADMIN_RSP + "classifieds.rsp.php?apply_func="+apply_func+'&entity='+this.entity,
				type: "post",
				dataType: "json",
				data: params,
				success: function(result){
					if ( callback != undefined ) {
						callback(result.result, handler);	
					}
					new Function(result.script)();
				}
			});
	},	
	
	bindGroups: function() {
		var handler = this;
		
		this.setGroups();	
		
		$jq.each(this.groups, function(group_id, group) {
			var $group_node = $jq("#group_" + group_id);
	
			//bind move group checkboxes
			$jq(":checkbox", $group_node).unbind("click");
			
			$jq(":checkbox", $group_node).bind("click", function() {
				handler.checkGroups();
			});			
			
			//bind edit and delete buttons of group
			$jq(".edit_forum_group", $group_node).unbind("click");
			$jq(".delete_forum_group", $group_node).unbind("click");
			
			$jq(".edit_forum_group", $group_node).bind("click", function() {
				handler.showEditBox( group_id );
			});
			
			$jq(".delete_forum_group", $group_node).bind("click", function() {
				handler.showConfirmBox( group_id );
			});
			
			$jq(".forum_title .forum_up", $group_node).unbind("click");
			
			if ( group.is_top ) {
				$jq(".forum_title .forum_up", $group_node).addClass("forum_up_i");
				$jq(".forum_title .forum_up", $group_node).removeClass("forum_up");
			}
			else {
				$jq(".forum_title .forum_up", $group_node).bind("click", function() {
					handler.upGroup( group_id, group.parent_id, group.order );
				});
			}
			
			$jq(".forum_title .forum_down", $group_node).unbind("click");
			if ( group.is_bottom ) {
				$jq(".forum_title .forum_down", $group_node).addClass("forum_down_i");
				$jq(".forum_title .forum_down", $group_node).removeClass("forum_down");
			}
			else {
				$jq(".forum_title .forum_down", $group_node).bind("click", function() {
					handler.downGroup( group_id, group.parent_id, group.order );
				});
			}						
		});		
	},
	
	drawGroupSelect: function(groups, only_move) {
		var select_groups = (groups==undefined) ? this.groups : groups;
		var options = '<option value="0">Root</option>';
		$jq.each(select_groups, function(index, group) {
			options += '<option value="'+group.group_id+'">'+group.label+'</option>';
		});
		
		$jq("#move_parent_id").empty();
		$jq("#move_parent_id").append(options);
		
		if ( only_move==undefined ) {
			$jq("#new_parent_id").empty();
			$jq("#new_parent_id").append(options);	
		}
		
	},
	
	drawGroups: function() {
		var inner_text = '';
		$jq.each(this.groups, function(index, group) {
			inner_text += '<div class="category" id="group_'+group.group_id+'">' +
								'<div class="forum_title">' +
									'<input type="checkbox" name="groups[]" value="'+group.group_id+'" class="float_left"/>' +
									'<a href="javascript://" title="delete" class="forum_delete delete_forum_group"></a>' +
									'<a href="javascript://" title="edit" class="edit_forum_group"></a>' +
									'<div style="margin-left: '+group.margin+'px;">' +
										'<a href="javascript://" title="up" class="forum_up" ></a>' +
										'<a href="javascript://" title="down" class="forum_down" ></a>' +	
										'&nbsp;&nbsp;&nbsp;<span class="cls_groupname">'+group.name+'</span>'+
									'</div>' +
								'</div>' +
							'</div>';		
		});
		
		$jq(".classifieds_cat").empty();
		$jq(".classifieds_cat").append(inner_text);

	},
	
	addNewGroup: function() {
		var handler = this;		
		var name = $jq("#new_group_name").val();
		var parent_id = $jq("#new_parent_id").val();
		
		if( !name ) {
			alert('Please, type group name.');
			return ;
		}

		this.showPreloader();
		
		this.ajaxCall( 'addNewGroup', {name: name, parent_id: parent_id}, function(groups){
			if(groups) {
				handler.groups = groups;
				$jq("#new_group_name").val('');
				$jq("#new_parent_id").val(0);
				
				handler.drawGroups();
				handler.drawGroupSelect();
				handler.bindGroups();
			}	
		});
		
	},
	
	moveGroup: function() {
		var groups = '';
		var handler = this;		
		var parent_id = $jq("#move_parent_id").val();
		var checked_groups = $jq(".category :checkbox[checked]");
		
		if ( !checked_groups.length ) {
			alert("Please, select groups.");
			return ;
		}
		
		$jq.each(checked_groups, function(index, checkbox) {
			groups += checkbox.value + ',';
		});
		
		this.showPreloader();
		
		this.ajaxCall( 'moveGroup', {parent_id: parent_id, groups: groups}, function(groups) {
			if (groups) {
				handler.groups = groups;
				$jq("#move_parent_id").val(0);
				
				handler.drawGroups();
				handler.drawGroupSelect();
				handler.bindGroups();
			}
		});
	},
	
	showConfirmBox: function(group_id) {
		var handler = this;
		this.confirm_box = new SK_confirm( $jq("#confirm_group_title"), $jq("#confirm_group_content").show(), function(){
			handler.ajaxCall( 'deleteGroup', {group_id: group_id}, function(groups){
				handler.groups = groups;
				
				handler.showPreloader();
				
				handler.drawGroups();
				handler.drawGroupSelect();
				handler.bindGroups();
			});
		});
	},
	
	showEditBox: function(group_id) {
		var handler = this;
		
		$jq("#edit_group_name").val( this.groups[group_id].name );
		$jq("#edit_group").unbind("click");
		$jq("#edit_group").bind("click", function() {
			handler.editGroup( group_id, $jq("#edit_group_name").val() );
		});
		
		
		handler.editgroup_box[group_id] = new SK_FloatBox({
			$title		: $jq(".edit_group_title").text(),
			$contents	: $jq(".edit_group_content")
		});		
	},	
	
	editGroup: function(group_id, name) {
		var handler = this;
		this.ajaxCall('editGroup', {group_id: group_id, name: name}, function(result) {
			if (result) {
				$node = $jq("#group_" + group_id);
				$jq(".cls_groupname", $node).text( name );
				
				handler.groups[group_id].label = handler.groups[group_id].name.replace(handler.groups[group_id].name, name); 
				handler.groups[group_id].name = name;
								
				handler.editgroup_box[group_id].close();
				
				handler.drawGroupSelect();
			}
			else {
				handler.editgroup_box[group_id].close();
			}
		});
	},
	
	upGroup: function(group_id, parent_id, order) {
		var handler = this;
		this.ajaxCall('upGroup', {group_id: group_id, parent_id: parent_id, order: order}, function(groups) {
			handler.groups = groups;
			
			handler.showPreloader();
			
			handler.drawGroups();
			handler.drawGroupSelect();
			handler.bindGroups();
		});
	},
	
	downGroup: function(group_id, parent_id, order) {
		var handler = this;
		this.ajaxCall('downGroup', {group_id: group_id, parent_id: parent_id, order: order}, function(groups) {
			handler.groups = groups;
			
			handler.showPreloader();
			
			handler.drawGroups();
			handler.drawGroupSelect();
			handler.bindGroups();
		});
	},
	
	showPreloader: function() {
		$jq(".classifieds_cat .category").addClass("cls_opacity");
		$jq(".classifieds_cat").append( this.preloader );
	},
		
	setGroups: function() {
		var handler = this;
		$jq.each(this.groups, function(cur_id, cur_group) {
			
			cur_group['is_top'] = true;
			cur_group['is_bottom'] = true;
			
			$jq.each(handler.groups, function(group_id, group) {
				if (cur_id != group_id && cur_group.parent_id == group.parent_id && parseInt(cur_group.order) > parseInt(group.order)) {
					cur_group.is_top = false;
				}
				if (cur_id != group_id && cur_group.parent_id == group.parent_id && parseInt(cur_group.order) < parseInt(group.order)) {
					cur_group.is_bottom = false;
				}
			});
			
		});
	},
	
	checkGroups: function() {
		var handler = this;
		var select_groups = handler.clone(handler.groups);
		var checked_groups = $jq(".category :checkbox[checked]");
		$jq.each(checked_groups, function(index, checkbox) {
			var checked = handler.groups[checkbox.value];
			var delete_flag = false;
			$jq.each(select_groups, function(sel_group_id, sel_group) {
				if ( checked.group_id==sel_group_id ) {
					delete_flag = true;
				}
				if ( delete_flag && checked.group_id!=sel_group_id && parseInt(checked.margin)>=parseInt(sel_group.margin) ) {
					delete_flag = false;
				}
				if ( delete_flag ) {
					delete select_groups[sel_group_id];
				}
			});
		});
		
		this.drawGroupSelect(select_groups, true);
		
	},
	
	clone: function(obj){
	    if(obj == null || typeof(obj) != 'object') {
	        return obj;
	    }
	    var temp = new obj.constructor(); // changed (twice)
	    for(var key in obj) {
	        temp[key] = this.clone(obj[key]);
	    }

	    return temp;
	}
}

