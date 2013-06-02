function component_VideoPlayer(auto_id)
{
	this.DOMConstruct('VideoPlayer', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_VideoPlayer.prototype =
	new SK_ComponentHandler({
		
		construct: function(video_src, src, width, height, watermark, preview, id){
			this.video_src = video_src;
			this.src = src;
			this.width = width;
			this.height = height;
			this.watermark = watermark;
			this.preview = preview;
			this.id = id;
			
		},
		
		 loadVideoPlayer: function(){

			var handler = this;

			var s1 = new SWFObject( handler.src, "single", handler.width, handler.height, "7" );
			s1.addParam("allowfullscreen","true");
			s1.addVariable("file", handler.video_src);
			s1.addVariable("showdownload","true");
			s1.addVariable("bufferlength","5");

			if (handler.watermark != undefined)
				s1.addVariable("logo", handler.watermark);

			if (handler.preview != undefined)
				s1.addVariable("image", handler.preview);

			s1.write("video_player_" + handler.id);
		}
	});