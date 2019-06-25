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

$( document ).ready(function() {
	$.each(il.InteractiveVideo, function (player_id, value) {
		if (value.hasOwnProperty("player_type") && value.player_type === "meo") {
			il.InteractiveVideoPlayerFunction.appendInteractionEvents(player_id);
			console.log('player', player_id)
			var conf = il.InteractiveVideoVimeoPlayer.config;
			conf.vimeo_player = new Vimeo.Player(player_id, {loop: false});

			il.InteractiveVideoPlayerAbstract.config[player_id] = {
				pauseCallback:          (function () {
					conf.vimeo_player.pause(player_id);
				}),
				playCallback:           (function () {
					conf.vimeo_player.play(player_id);
				}),
				durationCallback:       (function () {
					return conf.duration;
				}),
				currentTimeCallback:    (function () {
					return conf.time;
				}),
				setCurrentTimeCallback: (function (time) {
					conf.vimeo_player.setCurrentTime(time, player_id);
				})
			};
			conf.vimeo_player.on('timeupdate', function (data) {
				var conf = il.InteractiveVideoVimeoPlayer.config[player_id];
				conf.duration = data.duration;
				conf.time = data.seconds;

				if (!conf.time_end_filled && conf.duration > 0) {
					il.InteractiveVideoPlayerComments.fillEndTimeSelector(conf.duration)
					conf.time_end_filled = true;
				}
			});

			conf.vimeo_player.on('seeked', function () {
				clearInterval(interval);
				il.InteractiveVideoPlayerFunction.seekingEventHandler();
				interval = setInterval(function () {
					il.InteractiveVideoPlayerFunction.playingEventHandler(interval, il.InteractiveVideoVimeoPlayer.config.vimeo_player);
				}, 100);
			});

			conf.vimeo_player.on('pause', function () {
				clearInterval(interval);
				il.InteractiveVideo.last_time = il.InteractiveVideoPlayerAbstract.currentTime();
			});

			conf.vimeo_player.on('ended', function () {
				il.InteractiveVideoPlayerAbstract.videoFinished();
			});

			conf.vimeo_player.on('play', function () {
				il.InteractiveVideoPlayerAbstract.play();

				interval = setInterval(function () {
					il.InteractiveVideoPlayerFunction.playingEventHandler(interval, il.InteractiveVideoVimeoPlayer.config.vimeo_player);
				}, 500);
			});

			il.InteractiveVideoPlayerComments.fillEndTimeSelector(il.InteractiveVideoPlayerAbstract.duration(player_id));
		}
	});
});