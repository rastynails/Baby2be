function component_IndexPhotoList(auto_id)
{
	this.DOMConstruct('IndexPhotoList', auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_IndexPhotoList.prototype =
	new SK_ComponentHandler({
		
		$list_prototype: undefined,
		
		$lists_cont: undefined,
		
		$menu_cont: undefined,
		
		$menu_node: undefined,
		
		$menu_prototype: undefined,
		
		active_list: undefined,
		
		items_count: 6,
		
		lists: {},
		
		construct : function(items_count) {
			
			if (items_count != undefined) {
				this.items_count = items_count;
			}
			
			this.$lists_cont = this.$(".photo_list_cont");
			this.$list_prototype = this.$lists_cont.find(".prototype_node")
			.removeClass("prototype_node")
			.remove();
			
			this.$menu_cont = this.$(".menu").removeClass("prototype_node");
			this.$menu_node = this.$menu_cont.find("ul");
			this.$menu_prototype = this.$menu_cont.find(".tab").remove();
			
			this.$(".block_toolbar").hide();
			
		},
		
		addList: function(list_name, label, url){
			
			var handler = this;
			
			var $menu_node = this.$menu_prototype.clone();
			$menu_node.find("span").text(label);
						
			$menu_node.appendTo(this.$menu_node);
			
			$menu_node.find("a").click(function(){
				handler.activateList(list_name);
			}).addClass(list_name);
			
			this.lists[list_name] = {
				list_name: list_name,
				$menu_node: $menu_node,
				list_url: url
			}
		},
		
		activateList: function(list_name) {
			var handler = this;
			
			if(this.lists[list_name].$list_node != undefined) {
				if (this.active_list != undefined) {
					this.active_list.active = false;
					this.active_list.$list_node.hide();
					this.active_list.$menu_node.removeClass("active");
				}
				
				this.active_list = this.lists[list_name];
				this.lists[list_name].active = true;
				this.active_list.$list_node.show();
				this.active_list.$menu_node.addClass("active");
				this.$("#view_more_btn").attr("href", this.lists[list_name].list_url);
				
				var ping_count = 0;
				var ping = function() {
					if (ping_count < 100) {
						for (var key in handler.lists[list_name].items) {
								if (!handler.lists[list_name].items[key].loaded) {
									ping_count++;
									return false;
								}
						}
					}
					return true;
				}
				
				if (ping()) {
					handler.hidePreloader();
					return true;
				}
				
				var interval = window.setInterval(function(){
					if (ping()) {
						window.clearInterval(interval);
						handler.hidePreloader();
					}
				}, 5);			
				
				return true
			}
			this.active_list.$menu_node.removeClass("active");
			this.lists[list_name].$menu_node.addClass("active");
			this.showPreloader();
			this.ajaxCall('ajax_LoadList', {list_name: list_name, count: this.items_count});
		},
		
		$preloader: undefined,
		preloader_height: undefined,
		showPreloader: function() {
			if (this.$preloader == undefined) {
				this.$preloader = $('<div class="content_preloader preloader"></div>');
				//alert(this.preloader_height);
				if (this.preloader_height != undefined) {
					this.$preloader.height(this.preloader_height);
				}
				
				this.$lists_cont.hide().before(this.$preloader);
			}
			
		},
		
		hidePreloader: function() {
			if (this.$preloader != undefined) {
				this.$preloader.remove();
				this.$preloader = undefined;
				this.$lists_cont.show();
				this.preloader_height = this.$lists_cont.height();
			}
		},
		
		fillList: function(list_name, items) {
			var handler = this;
			
			var $list_node = this.$list_prototype.clone();
			
			this.lists[list_name].$list_node = $list_node;
			
			this.lists[list_name].items = [];
						
			var $item_prototype = $list_node.find(".item").remove();
			
			this.showPreloader();
			
			if (items.length != 0) {
				$.each(items, function(i, item) {
					
					var $node = $item_prototype.clone();
								
					var list_item = {
						$node: $node,
						loaded: false
					}
									
					var $img = $("<img>");
					
					$img.load(function(){
						list_item.loaded = true;
					});
					
					$img.attr("src", item.img_src);
					
					$node.find(".image").attr("href", item.img_url).append($img);
					
					handler.lists[list_name].items.push(list_item);
					
					$node.appendTo($list_node);
					handler.$(".block_toolbar").show();
				});
			} else {
				$list_node.append(this.$(".empty_list_msg:eq(0)").clone().show());
				this.$(".block_toolbar").hide();
				if (this.lists.length == 1) {
					this.lists[list_name].$menu_node.hide();
				}
			}
			
			$list_node.prependTo(this.$lists_cont);		
					
			if (!this.lists[list_name].active) {
				$list_node.hide();
			}
		},
		
		complete: function() {
			this.$menu_node.find('.tab').removeClass('first').removeClass('last');
			this.$menu_node.find('.tab:first').addClass('first');
			this.$menu_node.find('.tab:last').addClass('last');
		},
		
		debug: function(x) {
			
			window.setTimeout(function(){
				console.debug(x);
			}, 500);
		}
		
	});