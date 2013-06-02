
var adminForum = 
{
    actions		: [],
    group_list	: [],
    forum_list	: [],
    group_items : [],
    forum_items : [],
    editgroup_box	: [],
    editforum_box	: [],
	
    construct: function() {
        var handler = this;
        for(var key in handler.actions) {
            handler.actions[key](handler);
        }
        handler.group_construct();
        handler.forum_construct();
    },
	
    ajaxCall: function(apply_func, params, callback) {
        var handler = this;
        $jq.ajax({
            url: URL_ADMIN_RSP + "forum.rsp.php?apply_func="+apply_func,
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

    group_construct: function() {
        var handler = this;
        //delete up link of first group
        $jq(".forum_group_title .forum_up:first").addClass("forum_up_i");
        $jq(".forum_group_title .forum_up:first").removeClass("forum_up");
        //delete down link of last group
        $jq(".forum_group_title .forum_down:last").addClass("forum_down_i");
        $jq(".forum_group_title .forum_down:last").removeClass("forum_down");

        if (handler.group_list != null)
        {
            $jq.each( handler.group_list, function( group_id, order ){
                var $item_node = $jq("#forum_group_"+group_id);
                handler.group_items[group_id] = $item_node;
                $jq(".edit_forum_group", $item_node).bind("click.group_edit", function(){
                    handler.showEditGroupBox(group_id);
                });
                $jq(".delete_forum_group", $item_node).bind("click.group_del", function(){
                    handler.showConfirmGroupBox(group_id, order);
                });
                $jq(".forum_group_title .forum_up", $item_node).bind("click.group_up", function(){
                    handler.upForumGroup(group_id, order);
                });
                $jq(".forum_group_title .forum_down", $item_node).bind("click.group_down", function(){
                    handler.downForumGroup(group_id, order);
                });
            } );
        }
    },
	
    group_destruct: function() {
        var handler = this;
        handler.group_items = [];
        handler.editgroup_box = [];
		
        //add up link for all groups
        $jq(".forum_group_title .forum_up_i").addClass("forum_up");
        $jq(".forum_group_title .forum_up_i").removeClass("forum_up_i");
        //add down link for all groups
        $jq(".forum_group_title .forum_down_i").addClass("forum_down");
        $jq(".forum_group_title .forum_down_i").removeClass("forum_down_i");

        if (handler.group_list != null)
        {
            $jq.each( handler.group_list, function( group_id, order ){
                var $item_node = $jq("#forum_group_"+group_id);
                handler.group_items[group_id] = $item_node;
			
                $jq(".forum_up", $item_node).unbind("click.group_up");
                $jq(".forum_down", $item_node).unbind("click.group_down");
                $jq(".delete_forum_group", $item_node).unbind("click.group_del");
                $jq(".edit_forum_group", $item_node).unbind("click.group_edit");
            } );
        }
		
    },
	
    forum_construct: function() {
        var handler = this;
        //delete up link of first forum
        var firstGroupId = $jq(".forum_title .forum_up:first").parents(".forum_group:eq(0)").attr("id");
        if ( firstGroupId==$jq(".forum_group:first").attr("id") ) {
            $jq(".forum_title .forum_up:first").addClass("forum_up_i");
            $jq(".forum_title .forum_up:first").removeClass("forum_up");
        }
        //delete down link of last forum
        var lastGroupId = $jq(".forum_title .forum_up:last").parents(".forum_group:eq(0)").attr("id");
        if ( lastGroupId==$jq(".forum_group:last").attr("id") ) {
            $jq(".forum_title .forum_down:last").addClass("forum_down_i");
            $jq(".forum_title .forum_down:last").removeClass("forum_down");
        }

        if (handler.forum_list != null)
        {
            $jq.each( handler.forum_list, function( forum_id, order ){
                var $item_node = $jq("#forum_"+forum_id);
                handler.forum_items[forum_id] = $item_node;
                $jq(".edit_forum", $item_node).bind("click.forum_edit", function(){
                    handler.showEditForumBox(forum_id);
                });
                $jq(".delete_forum", $item_node).bind("click.forum_del", function(){
                    handler.showConfirmForumBox(forum_id, order);
                });
                $jq(".forum_title .forum_up", $item_node).bind("click.forum_up", function(){
                    handler.upForum(forum_id, order);
                });
                $jq(".forum_title .forum_down", $item_node).bind("click.forum_down", function(){
                    handler.downForum(forum_id, order);
                });
            } );
        }
    },
	
    forum_destruct: function() {
        var handler = this;
        handler.forum_items = [];
        handler.editforum_box = [];
		
        //add up link for all forums
        $jq(".forum_title .forum_up_i").addClass("forum_up");
        $jq(".forum_title .forum_up_i").removeClass("forum_up_i");
        //add down link for all forums
        $jq(".forum_title .forum_down_i").addClass("forum_down");
        $jq(".forum_title .forum_down_i").removeClass("forum_down_i");

        if (handler.forum_list != null)
        {
            $jq.each( handler.forum_list, function( forum_id, order ){
                var $item_node = $jq("#forum_"+forum_id);

                $jq(".forum_up", $item_node).unbind("click.forum_up");
                $jq(".forum_down", $item_node).unbind("click.forum_down");
                $jq(".delete_forum", $item_node).unbind("click.forum_del");
                $jq(".edit_forum", $item_node).unbind("click.forum_edit" );
            } );
        }

		
    },
	
    showEditGroupBox : function(group_id){
        var handler = this;
        var group_name = $jq(".forum_group_name", handler.group_items[group_id]).text();
        $jq("#edit_forum_group_name").val(group_name);
        $jq("#edit_forum_group_id").val(group_id);
		
        handler.editgroup_box[group_id] = new SK_FloatBox({
            $title		: $jq(".edit_forum_group_title").text(),
            $contents	: $jq(".edit_forum_group_content")
        });
    },
	
    hideEditGroupBox: function(group_id) {
        this.editgroup_box[group_id].close();
    },
	
    showConfirmGroupBox: function(group_id, order) {
        var handler = this;
		
        handler.confirm_box = new SK_confirm( $jq("#confirm_group_title").text(), $jq("#confirm_group_content").show(), function(){
            handler.ajaxCall( 'deleteForumGroup', {
                group_id: group_id,
                order: order
            }, function(result){
                handler.forum_destruct();
                for( forum_id in result ){
                    delete handler.forum_list[forum_id];
                }
                handler.forum_construct();
            });
            $jq("#new_forum_group option[value='"+group_id+"']").remove();
            handler.group_items[group_id].remove();
            handler.group_destruct();
            delete handler.group_list[group_id];
            handler.group_construct();
        });
    },
	
    showEditForumBox : function(forum_id){
        var handler = this;
        var forum_name = $jq(".forum_name", handler.forum_items[forum_id]).text();
        var forum_description = $jq(".forum_description", handler.forum_items[forum_id]).text();
        $jq("#edit_forum_name").val(forum_name);
        $jq("#edit_forum_description").val(forum_description);
        $jq("#edit_forum_id").val(forum_id);
		
        handler.editforum_box[forum_id] = new SK_FloatBox({
            $title		: $jq(".edit_forum_title").text(),
            $contents	: $jq(".edit_forum_content")
        });
    },
	
    hideEditForumBox: function(forum_id) {
        this.editforum_box[forum_id].close();
    },
	
    showConfirmForumBox: function(forum_id, order) {
        var handler = this;
		
        handler.confirm_box = new SK_confirm( $jq("#confirm_forum_title").text(), $jq("#confirm_forum_content").show(), function(){
            handler.ajaxCall( 'deleteForum', {
                forum_id: forum_id,
                order: order
            }, function(forum_id){
                handler.forum_items[forum_id].remove();
                handler.forum_destruct();
                delete handler.forum_list[forum_id];
                handler.forum_construct();
            });
        });
    },
	
    upForumGroup: function(up_group_id, up_order) {
        var handler = this;
        handler.ajaxCall( 'upForumGroup', {
            group_id: up_group_id,
            order: up_order
        }, function(down){
            if (down) {
                handler.group_destruct();
                handler.group_list[up_group_id] = down.group.order;
                handler.group_list[down.group.group_id] = up_order;
                handler.group_items[up_group_id].hide();
                handler.group_items[up_group_id].insertBefore(handler.group_items[down.group.group_id]);
                handler.group_items[up_group_id].fadeIn("slow");
                handler.group_construct();
                handler.forum_destruct();
				
                for ( forum_id in down.forum_list ){
                    handler.forum_list[forum_id] = down.forum_list[forum_id];
                }
                handler.forum_construct();
            }
        });
    },
	
    downForumGroup: function(down_group_id, down_order) {
        var handler = this;
        handler.ajaxCall( 'downForumGroup', {
            group_id: down_group_id,
            order: down_order
        }, function(up){
            if (up) {
                handler.group_destruct();
				
                handler.group_list[down_group_id] = up.group.order;
                handler.group_list[up.group.group_id] = down_order;
                handler.group_items[down_group_id].hide();
                handler.group_items[down_group_id].insertAfter(handler.group_items[up.group.group_id]);
                handler.group_items[down_group_id].fadeIn("slow");
                handler.group_construct();
                handler.forum_destruct();
				
                for ( forum_id in  up.forum_list) {
                    handler.forum_list[forum_id] = up.forum_list[forum_id];
                }
                handler.forum_construct();
            }
        });
    },
	
    upForum: function(up_forum_id, up_order) {
        var handler = this;

        handler.ajaxCall( 'upForum', {
            forum_id: up_forum_id
        }, function(down){
            if (down) {
                if (down.forum_group_id) {
                    handler.forum_items[up_forum_id].hide();
                    handler.forum_items[up_forum_id].appendTo(handler.group_items[down.forum_group_id]);
                    handler.forum_items[up_forum_id].fadeIn("slow");
					
                    $jq.each(down.forum_list, function(forum_id, order) {
                        handler.forum_list[forum_id] = order;
                    });
                }
                else {
                    handler.forum_list[up_forum_id] = down.order;
                    handler.forum_list[down.forum_id] = up_order;
                    handler.forum_items[up_forum_id].hide();
                    handler.forum_items[up_forum_id].insertBefore(handler.forum_items[down.forum_id]);
                    handler.forum_items[up_forum_id].fadeIn("slow");
                }
                handler.forum_destruct();
                handler.forum_construct();
            }
        });
    },
	
    downForum: function(down_forum_id, down_order) {
        var handler = this;
	
        handler.ajaxCall( 'downForum', {
            forum_id: down_forum_id
        }, function(up){
            if (up) {
                if ( up.forum_group_id ) {
                    handler.forum_items[down_forum_id].hide();
                    handler.forum_items[down_forum_id].insertAfter($jq(".forum_group_title", handler.group_items[up.forum_group_id]));
                    handler.forum_items[down_forum_id].fadeIn("slow");
					
                    $jq.each(up.forum_list, function(forum_id, order) {
                        handler.forum_list[forum_id] = order;
                    });
                }
                else {
                    handler.forum_list[down_forum_id] = up.order;
                    handler.forum_list[up.forum_id] = down_order;
                    handler.forum_items[down_forum_id].hide();
                    handler.forum_items[down_forum_id].insertAfter(handler.forum_items[up.forum_id]);
                    handler.forum_items[down_forum_id].fadeIn("slow");
                }
                handler.forum_destruct();
                handler.forum_construct();
            }
        });
    },
	
    print_arr: function( $object, $id ) {
        var $out_put;
        for ( $item in $object ){
            $out_put += '<div>['+$item+"]\t => "+$object[$item]+"</div>";
        }
        $jq("#print_arr_block").css("display", "block");
        $jq("#print_arr_"+$id).append($out_put);
    }
};

//New Forum Group
adminForum.actions.push(function(handler){
    var $node = $jq("#new_group");
    var $group_name = $jq("#new_forum_group_name");
    $node.click(function(){
        handler.ajaxCall("addNewGroup",{
            group_name: $group_name.val()
        }, function(result){
            if (result) {
                $inner_text = '<div class="forum_group" id="forum_group_'+result.group_id+'">'+
                '<div class="forum_group_title">'+
                '<a href="javascript://" title="delete" class="forum_delete delete_forum_group"></a>'+
                '<a href="javascript://" title="edit" class="edit_forum_group"></a>'+
                '<a href="javascript://" title="up" class="forum_up"></a>'+
                '<a href="javascript://" title="down" class="forum_down" ></a>'+
                '<span class="forum_group_name">'+$group_name.val()+'</span>'+
                '</div>'+
                '</div>';
                $inner_option = '<option value="'+result.group_id+'">'+$group_name.val()+'</option>';
                $group_name.val('');
                handler.group_destruct();
                $jq(".forum_group_list").append($inner_text);
                $jq("#new_forum_group").append($inner_option);
                handler.group_list[result.group_id] = result.order;
				
                handler.group_construct();
            }
        });
    });
	
});

//New Forum
adminForum.actions.push(function(handler){
    var $node = $jq("#new_forum");
    var $group_id = $jq("#new_forum_group");
    var $name = $jq("#new_forum_name");
    var $description = $jq("#new_forum_description");
    $node.click(function(){
        handler.ajaxCall("addNewForum",{
            group_id: $group_id.val(),
            name: $name.val(),
            description: $description.val()
        },
        function(result){
            if (result) {
                $inner_text = '<div id="forum_'+result.forum_id+'" class="forum">'+
                '<div class="forum_title">'+
                '<a href="javascript://" title="delete" class="forum_delete delete_forum"></a>'+
                '<a href="javascript://" title="edit" class="open_forum_edit_cont edit_forum"></a>'+
                '<a href="javascript://" title="up" class="forum_up"></a>'+
                '<a href="javascript://" title="down" class="forum_down"></a>'+
                '<span class="forum_name">'+$name.val()+'</span>'+
                '</div>'+
                '<div class="forum_description">'+$description.val()+'</div>'+
                '</div>';
                $name.val('');
                $description.val('');
                handler.forum_destruct();
                $jq("#forum_group_"+$group_id.val()).append($inner_text);
                handler.forum_list[result.forum_id] = result.order;
                handler.forum_construct();
            }
        });
    });
	
});

//Edit Forum Group
adminForum.actions.push(function(handler){
    var $node = $jq("#edit_forum_group");
    $node.click(function(){
        var $name = $jq("#edit_forum_group_name").val();
        var $group_id = $jq("#edit_forum_group_id").val();
        handler.ajaxCall("editForumGroup",{
            group_id: $group_id,
            name: $name
        },
        function(result){
            if (result) {
                $jq(".forum_group_name", handler.group_items[$group_id]).text($name);
                handler.hideEditGroupBox($group_id);
            }
            else {
                handler.hideEditGroupBox($group_id);
                alert("Forum Group not changed");
            }
        });
    });
	
    $jq("#cancel_edit_forum_group").click(function(){
        var $group_id = $jq("#edit_forum_group_id").val();
        handler.hideEditGroupBox($group_id);
    });
	
});

//Edit Forum
adminForum.actions.push(function(handler){
    var $node = $jq("#edit_forum");
    $node.click(function(){
        var $name = $jq("#edit_forum_name").val();
        var $desc = $jq("#edit_forum_description").val();
        var $forum_id = $jq("#edit_forum_id").val();
        handler.ajaxCall("editForum",{
            forum_id: $forum_id,
            name: $name,
            desc: $desc
        },
        function(result){
            if (result) {
                $jq(".forum_name", handler.forum_items[$forum_id]).text($name);
                $jq(".forum_description", handler.forum_items[$forum_id]).text($desc);
                handler.hideEditForumBox($forum_id);
            }
            else {
                handler.hideEditForumBox($forum_id);
                alert("Forum fields not changed");
            }
        });
    });
	
    $jq("#cancel_edit_forum").click(function(){
        var $forum_id = $jq("#edit_forum_id").val();
        handler.hideEditForumBox($forum_id);
    });
	
});
