function component_MusPlayer(auto_id)
{
	this.DOMConstruct('MusPlayer', auto_id);
	
	var handler = this;
	
	this.delegates = {
		
	};
}

component_MusPlayer.prototype =
	new SK_ComponentHandler({
		
		construct: function(music_src, src, width, height, watermark, preview, id){
			this.music_src = music_src;
			this.src = src;
			this.width = width;
			this.height = height;
			this.watermark = watermark;
			this.preview = preview;
			this.id = id;
			
		},
		
		 loadMusPlayer: function(){

			var handler = this;

			var s1 = new SWFObject( handler.src, "single", handler.width, handler.height, "7" );
			s1.addParam("allowfullscreen","false");
			s1.addVariable("file", handler.music_src);
			s1.addVariable("showdownload","true");
			s1.addVariable("bufferlength","5");

			//if (handler.watermark != undefined)
			//	s1.addVariable("logo", handler.watermark);

			//if (handler.preview != undefined)
			//	s1.addVariable("image", handler.preview);

			s1.write("video_player_" + handler.id);
		}
	});