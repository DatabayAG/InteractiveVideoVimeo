il.InteractiveVideoVimeoPlayer = (function (scope) {
	'use strict';

	var pub = {}, pro = {};

	pub.config = {
		vimeo_player	: null,
		time			: 0,
		duration		: 0,
		time_end_filled	:false
	};

	pub.protect = pro;
	return pub;

}(il));

(function ($) {

	il.Util.addOnLoad(function () {
		il.InteractiveVideo.last_stopPoint = -1;
		il.InteractiveVideoPlayerFunction.appendInteractionEvents();
		var conf = il.InteractiveVideoVimeoPlayer.config;
		var options = {
			"techOrder": ["Vimeo"],
			"sources": [{ "type": "video/vimeo", "src": "https://vimeo.com/"+interactiveVideoVimeoId+""}], "vimeo": { "color": "#fbc51b"} 
		};

		var player = videojs('ilInteractiveVideo', options, function onPlayerReady() {

			var interval = null;

			il.InteractiveVideoPlayerAbstract.config = {
				pauseCallback           : (function (){player.pause();}),
				playCallback            : (function (){player.play();}),
				durationCallback        : (function (){return player.duration();}),
				currentTimeCallback     : (function (){return player.currentTime();}),
				setCurrentTimeCallback  : (function (time){player.setCurrentTime(time);})
			};

			il.InteractiveVideoPlayerComments.fillEndTimeSelector(il.InteractiveVideoPlayerAbstract.duration());
			$('#ilInteractiveVideo').prepend($('#ilInteractiveVideoOverlay'));

			this.on('timeupdate', function(data) {
				var conf = il.InteractiveVideoVimeoPlayer.config;
				conf.duration	= data.duration;
				conf.time		= data.seconds;

				if(!conf.time_end_filled && conf.duration > 0)
				{
					il.InteractiveVideoPlayerComments.fillEndTimeSelector(conf.duration)
					conf.time_end_filled = true;
				}
			});


			this.on('seeked', function() {
				clearInterval(interval);
				il.InteractiveVideoPlayerFunction.seekingEventHandler();
			});

			this.on('pause', function() {
				clearInterval(interval);
				il.InteractiveVideo.last_time = il.InteractiveVideoPlayerAbstract.currentTime();
			});

			this.on('ended', function() {
				il.InteractiveVideoPlayerAbstract.videoFinished();
			});

			this.on('play', function() {
				il.InteractiveVideoPlayerAbstract.play();

				interval = setInterval(function () {
					il.InteractiveVideoPlayerFunction.playingEventHandler(interval, il.InteractiveVideoVimeoPlayer.config.vimeo_player);
				}, 500);
			});

			this.on('contextmenu', function(e) {
				e.preventDefault();
			});

			this.on('ready', function(e){
				il.InteractiveVideoPlayerAbstract.readyCallback();
			});
		});
	});
})(jQuery);
