$( document ).ready(function() {
	il.InteractiveVideoPlayerFunction.appendInteractionEvents();
});

var player = null;
(function ($) {

	il.Util.addOnLoad(function () {
		il.InteractiveVideo.last_stopPoint = -1;
		player = plyr.setup('#ilInteractiveVideo')[0];
		var interval = null;
		player.source({
			type:       'video',
			sources: [{
				src:    interactiveVideoVimeoId,
				type:   'vimeo'
			}]
		});
		il.InteractiveVideoPlayerAbstract.config = {
			pauseCallback              : (function (){player.pause();}),
			playCallback               : (function (){player.play();}),
			durationCallback           : (function (){return player.getDuration();}),
			currentTimeCallback        : (function (){return player.getCurrentTime();}),
			setCurrentTimeCallback     : (function (time){player.seek(time);}),
			external : false
		};

		il.InteractiveVideoPlayerComments.fillEndTimeSelector(il.InteractiveVideoPlayerAbstract.duration());

		player.on('seeked', function() {
			clearInterval(interval);
			il.InteractiveVideoPlayerFunction.seekingEventHandler();
		});

		player.on('pause', function() {
			clearInterval(interval);
			il.InteractiveVideo.last_time = il.InteractiveVideoPlayerAbstract.currentTime();
		});

		player.on('ended', function() {
			il.InteractiveVideoPlayerAbstract.videoFinished();
		});

		player.on('playing', function() {

			interval = setInterval(function () {
				il.InteractiveVideoPlayerFunction.playingEventHandler(interval, player);
			}, 500);
		});

		player.on('ready', function(e){
			il.InteractiveVideoPlayerAbstract.readyCallback();
		});

	});
})(jQuery);