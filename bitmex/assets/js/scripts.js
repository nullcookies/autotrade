$(document).ready(function() {
	if ($('.container').length > 0)
	{
		// --------------------------------------------------------------------------------------------------- //
		
		// var _loadContent = 0;
		// var _loadingContent = 0;
		// var loadContent = function(div, act, options) {
		// 	var options = options || {};
		// 	if ($(div).length <= 0) {
		// 		return false;
		// 	}

		// 	var reload_time = options.reload_time || 0;
		// 	if (reload_time) {
		// 		setTimeout(function(){loadContent(div, act, {reload_time:reload_time})}, reload_time);
		// 		if ($(div).length > 0) $(div).data('reload-time', reload_time);
		// 	}
			
		// 	// _loadContent++;
		// 	// if (_loadContent <= 1) return false;
			
		// 	if (_loadingContent) return false;
		// 	_loadingContent = 1;
			
		// 	var request = $.ajax({
		// 		url: "process.php",
		// 		method: "GET",
		// 		data: {rtype: 'ajax', act: act},
		// 		dataType: "html"
		// 	});

		// 	request.done(function(response) {
		// 		$(div).find('.panel-body').empty().html(response);
		// 		$(div).find('table').effect("highlight", {}, 500);
		// 		_loadingContent = 0;
		// 	});

		// 	request.fail(function(jqXHR, textStatus) {
		// 		console.warn("Request failed: " + textStatus);
		// 	});

		// 	return false;
		// };
		// loadContent('.panel-main-info', 'load-main-info', {reload_time:(Math.floor(Math.random() * 2) + 2) * 1000});
		// loadContent('.panel-main-info', 'load-main-info');

		// --------------------------------------------------------------------------------------------------- //

		var _loadCurrentPrice = 0;
		var _loadingCurrentPrice = 0;
		var loadCurrentPrice = function(div, act, options) {
			var options = options || {};
			if ($(div).length <= 0) {
				return false;
			}

			var reload_time = options.reload_time || 0;
			if (reload_time) {
				setTimeout(function(){loadCurrentPrice(div, act, {reload_time:reload_time})}, reload_time);
				if ($(div).length > 0) $(div).data('reload-time', reload_time);
			}
			
			// _loadCurrentPrice++;
			// if (_loadCurrentPrice <= 1) return false;
			
			if (_loadingCurrentPrice) return false;
			_loadingCurrentPrice = 1;
			
			var request = $.ajax({
				url: "process.php",
				method: "GET",
				data: {rtype: 'ajax', act: act},
				dataType: "html"
			});

			request.done(function(response) {
				$(div).find('.panel-body').empty().html(response);
				$(div).find('table').effect("highlight", {}, 500);
				_loadingCurrentPrice = 0;
			});

			request.fail(function(jqXHR, textStatus) {
				console.warn("Request failed: " + textStatus);
			});

			return false;
		};
		loadCurrentPrice('.panel-current-price', 'load-current-price', {reload_time:(Math.floor(Math.random() * 2) + 2) * 1000});
		
		var _loadAccount = 0;
		var _loadingAccount = 0;
		var loadAccount = function(div, act, options) {
			var options = options || {};
			if ($(div).length <= 0) {
				return false;
			}

			var reload_time = options.reload_time || 0;
			if (reload_time) {
				setTimeout(function(){loadAccount(div, act, {reload_time:reload_time})}, reload_time);
				if ($(div).length > 0) $(div).data('reload-time', reload_time);
			}
			
			// _loadAccount++;
			// if (_loadAccount <= 1) return false;
			
			if (_loadingAccount) return false;
			_loadingAccount = 1;

			var request = $.ajax({
				url: "process.php",
				method: "GET",
				data: {rtype: 'ajax', act: act},
				dataType: "html"
			});

			request.done(function(response) {
				// if ($(div).length <= 0)
				// 	$('.panel-group').append('<div class="panel panel-default panel-main-info"></div>');
				$(div).find('.panel-body').empty().html(response);
				_loadingAccount = 0;
			});

			request.fail(function(jqXHR, textStatus) {
				console.warn("Request failed: " + textStatus);
			});

			return false;
		};
		loadAccount('.panel-account', 'load-account', {reload_time:(Math.floor(Math.random() * 11) + 6) * 1000});

		var _loadAccount2 = 0;
		var _loadingAccount2 = 0;
		var loadAccount2 = function(div, act, options) {
			var options = options || {};
			if ($(div).length <= 0) {
				return false;
			}

			var reload_time = options.reload_time || 0;
			if (reload_time) {
				setTimeout(function(){loadAccount2(div, act, {reload_time:reload_time})}, reload_time);
				if ($(div).length > 0) $(div).data('reload-time', reload_time);
			}
			
			// _loadAccount2++;
			// if (_loadAccount2 <= 1) return false;
			
			if (_loadingAccount2) return false;
			_loadingAccount2 = 1;

			var request = $.ajax({
				url: "process.php",
				method: "GET",
				data: {rtype: 'ajax', act: act},
				dataType: "html"
			});

			request.done(function(response) {
				// if ($(div).length <= 0)
				// 	$('.panel-group').append('<div class="panel panel-default panel-main-info"></div>');
				$(div).find('.panel-body').empty().html(response);
				_loadingAccount2 = 0;
			});

			request.fail(function(jqXHR, textStatus) {
				console.warn("Request failed: " + textStatus);
			});

			return false;
		};
		loadAccount2('.panel-account2', 'load-account2', {reload_time:(Math.floor(Math.random() * 11) + 6) * 1000});

		var _loadWallet = 0;
		var _loadingWallet = 0;
		var loadWallet = function(div, act, options) {
			var options = options || {};
			if ($(div).length <= 0) {
				return false;
			}

			var reload_time = options.reload_time || 0;
			if (reload_time) {
				setTimeout(function(){loadWallet(div, act, {reload_time:reload_time})}, reload_time);
				if ($(div).length > 0) $(div).data('reload-time', reload_time);
			}
			
			// _loadWallet++;
			// if (_loadWallet <= 1) return false;
			
			if (_loadingWallet) return false;
			_loadingWallet = 1;
			
			var request = $.ajax({
				url: "process.php",
				method: "GET",
				data: {rtype: 'ajax', act: act},
				dataType: "html"
			});

			request.done(function(response) {
				$(div).find('.panel-body').empty().html(response);
				$(div).find('table').effect("highlight", {}, 500);
				_loadingWallet = 0;
			});

			request.fail(function(jqXHR, textStatus) {
				console.warn("Request failed: " + textStatus);
			});

			return false;
		};
		loadWallet('.panel-wallet', 'load-wallet', {reload_time:(Math.floor(Math.random() * 12) + 7) * 1000});

		var _loadWallet2 = 0;
		var _loadingWallet2 = 0;
		var loadWallet2 = function(div, act, options) {
			var options = options || {};
			if ($(div).length <= 0) {
				return false;
			}

			var reload_time = options.reload_time || 0;
			if (reload_time) {
				setTimeout(function(){loadWallet2(div, act, {reload_time:reload_time})}, reload_time);
				if ($(div).length > 0) $(div).data('reload-time', reload_time);
			}
			
			// _loadWallet2++;
			// if (_loadWallet2 <= 1) return false;
			
			if (_loadingWallet2) return false;
			_loadingWallet2 = 1;
			
			var request = $.ajax({
				url: "process.php",
				method: "GET",
				data: {rtype: 'ajax', act: act},
				dataType: "html"
			});

			request.done(function(response) {
				$(div).find('.panel-body').empty().html(response);
				$(div).find('table').effect("highlight", {}, 500);
				_loadingWallet2 = 0;
			});

			request.fail(function(jqXHR, textStatus) {
				console.warn("Request failed: " + textStatus);
			});

			return false;
		};
		loadWallet2('.panel-wallet2', 'load-wallet2', {reload_time:(Math.floor(Math.random() * 12) + 7) * 1000});

		var _loadOpenOrder = 0;
		var _loadingOpenOrder = 0;
		var loadOpenOrder = function(div, act, options) {
			var options = options || {};
			if ($(div).length <= 0) {
				return false;
			}

			var reload_time = options.reload_time || 0;
			if (reload_time) {
				setTimeout(function(){loadOpenOrder(div, act, {reload_time:reload_time})}, reload_time);
				if ($(div).length > 0) $(div).data('reload-time', reload_time);
			}
			
			// _loadOpenOrder++;
			// if (_loadOpenOrder <= 1) return false;
			
			if (_loadingOpenOrder) return false;
			_loadingOpenOrder = 1;
			
			var request = $.ajax({
				url: "process.php",
				method: "GET",
				data: {rtype: 'ajax', act: act},
				dataType: "html"
			});

			request.done(function(response) {
				$(div).find('.panel-body').empty().html(response);
				$(div).find('table').effect("highlight", {}, 500);
				_loadingOpenOrder = 0;
			});

			request.fail(function(jqXHR, textStatus) {
				console.warn("Request failed: " + textStatus);
			});

			return false;
		};
		loadOpenOrder('.panel-open-order', 'load-open-order', {reload_time:(Math.floor(Math.random() * 12) + 7) * 1000});

		var _loadOpenOrder2 = 0;
		var _loadingOpenOrder2 = 0;
		var loadOpenOrder2 = function(div, act, options) {
			var options = options || {};
			if ($(div).length <= 0) {
				return false;
			}

			var reload_time = options.reload_time || 0;
			if (reload_time) {
				setTimeout(function(){loadOpenOrder2(div, act, {reload_time:reload_time})}, reload_time);
				if ($(div).length > 0) $(div).data('reload-time', reload_time);
			}
			
			// _loadOpenOrder2++;
			// if (_loadOpenOrder2 <= 1) return false;
			
			if (_loadingOpenOrder2) return false;
			_loadingOpenOrder2 = 1;
			
			var request = $.ajax({
				url: "process.php",
				method: "GET",
				data: {rtype: 'ajax', act: act},
				dataType: "html"
			});

			request.done(function(response) {
				$(div).find('.panel-body').empty().html(response);
				$(div).find('table').effect("highlight", {}, 500);
				_loadingOpenOrder2 = 0;
			});

			request.fail(function(jqXHR, textStatus) {
				console.warn("Request failed: " + textStatus);
			});

			return false;
		};
		loadOpenOrder2('.panel-open-order2', 'load-open-order2', {reload_time:(Math.floor(Math.random() * 12) + 7) * 1000});

		// --------------------------------------------------------------------------------------------------- //
		
		

		// var _loadOpenPositions = 0;
		// var loadOpenPositions = function(div, act, options) {
		// 	var options = options || {};
		// 	if ($(div).length <= 0) {
		// 		return false;
		// 	}

		// 	var reload_time = options.reload_time || 0;
		// 	if (reload_time)
		// 		setTimeout(function(){loadOpenPositions(div, act, {reload_time:reload_time})}, reload_time);

		// 	_loadOpenPositions++;
		// 	if (_loadOpenPositions <= 1) return false;
			
		// 	var request = $.ajax({
		// 		url: "process.php",
		// 		method: "GET",
		// 		data: {rtype: 'ajax', act: act},
		// 		dataType: "html"
		// 	});

		// 	request.done(function(response) {
		// 		$(div).find('.panel-body').empty().html(response);
		// 		$(div).find('table').effect("highlight", {}, 500);
		// 	});

		// 	request.fail(function(jqXHR, textStatus) {
		// 		console.log("Request failed: " + textStatus);
		// 	});

		// 	return false;
		// };
		// loadOpenPositions('.panel-open-positions', 'load-open-positions', {reload_time:(Math.floor(Math.random() * 21) + 11) * 1000});

		// var _loadActions = 0;
		// var loadActions = function(div, act, options) {
		// 	var options = options || {};
		// 	if ($(div).length <= 0) {
		// 		return false;
		// 	}

		// 	var reload_time = options.reload_time || 0;
		// 	if (reload_time)
		// 		setTimeout(function(){loadActions(div, act, {reload_time:reload_time})}, reload_time);

		// 	// _loadActions++;
		// 	// if (_loadActions <= 1) return false;
			
		// 	var request = $.ajax({
		// 		url: "process.php",
		// 		method: "GET",
		// 		data: {rtype: 'ajax', act: act},
		// 		dataType: "html"
		// 	});

		// 	request.done(function(response) {
		// 		$(div).find('.panel-body').empty().html(response);
		// 		$(div).find('table').effect("highlight", {}, 500);
		// 	});

		// 	request.fail(function(jqXHR, textStatus) {
		// 		console.log("Request failed: " + textStatus);
		// 	});

		// 	return false;
		// };
		// loadActions('.panel-actions', 'load-actions');

		// var _loadMargin = 0;
		// var loadMargin = function(div, act, options) {
		// 	var options = options || {};
		// 	if ($(div).length <= 0) {
		// 		return false;
		// 	}

		// 	var reload_time = options.reload_time || 0;
		// 	if (reload_time)
		// 		setTimeout(function(){loadMargin(div, act, {reload_time:reload_time})}, reload_time);

		// 	_loadMargin++;
		// 	if (_loadMargin <= 1) return false;
			
		// 	var request = $.ajax({
		// 		url: "process.php",
		// 		method: "GET",
		// 		data: {rtype: 'ajax', act: act},
		// 		dataType: "html"
		// 	});

		// 	request.done(function(response) {
		// 		$(div).find('.panel-body').empty().html(response);
		// 		$(div).find('table').effect("highlight", {}, 500);
		// 	});

		// 	request.fail(function(jqXHR, textStatus) {
		// 		console.log("Request failed: " + textStatus);
		// 	});

		// 	return false;
		// };
		// loadMargin('.panel-margin', 'load-margin', {reload_time:(Math.floor(Math.random() * 21) + 11) * 1000});

		// var _loadOrderbook = 0;
		// var loadOrderbook = function(div, act, options) {
		// 	var options = options || {};
		// 	if ($(div).length <= 0) {
		// 		return false;
		// 	}

		// 	var reload_time = options.reload_time || 0;
		// 	if (reload_time)
		// 		setTimeout(function(){loadOrderbook(div, act, {reload_time:reload_time})}, reload_time);

		// 	_loadOrderbook++;
		// 	if (_loadOrderbook <= 1) return false;
			
		// 	var request = $.ajax({
		// 		url: "process.php",
		// 		method: "GET",
		// 		data: {rtype: 'ajax', act: act},
		// 		dataType: "html"
		// 	});

		// 	request.done(function(response) {
		// 		$(div).find('.panel-body').empty().html(response);
		// 		$(div).find('table').effect("highlight", {}, 500);
		// 	});

		// 	request.fail(function(jqXHR, textStatus) {
		// 		console.log("Request failed: " + textStatus);
		// 	});

		// 	return false;
		// };
		// loadOrderbook('.panel-orderbook', 'load-orderbook', {reload_time:(Math.floor(Math.random() * 21) + 11) * 1000});

		// var _loadOrders = 0;
		// var loadOrders = function(div, act, options) {
		// 	var options = options || {};
		// 	if ($(div).length <= 0) {
		// 		return false;
		// 	}

		// 	var reload_time = options.reload_time || 0;
		// 	if (reload_time)
		// 		setTimeout(function(){loadOrders(div, act, {reload_time:reload_time})}, reload_time);
			
		// 	_loadOrders++;
		// 	if (_loadOrders <= 1) return false;

		// 	var request = $.ajax({
		// 		url: "process.php",
		// 		method: "GET",
		// 		data: {rtype: 'ajax', act: act},
		// 		dataType: "html"
		// 	});

		// 	request.done(function(response) {
		// 		$(div).find('.panel-body').empty().html(response);
		// 		$(div).find('table').effect("highlight", {}, 500);
		// 	});

		// 	request.fail(function(jqXHR, textStatus) {
		// 		console.log("Request failed: " + textStatus);
		// 	});

		// 	return false;
		// };
		// loadOrders('.panel-orders', 'load-orders', {reload_time:(Math.floor(Math.random() * 21) + 11) * 1000});

		// var _loadOrder = 0;
		// var loadOrder = function(div, act, options) {
		// 	var options = options || {};
		// 	if ($(div).length <= 0) {
		// 		return false;
		// 	}

		// 	var reload_time = options.reload_time || 0;
		// 	if (reload_time)
		// 		setTimeout(function(){loadOrder(div, act, {reload_time:reload_time})}, reload_time);
			
		// 	_loadOrder++;
		// 	if (_loadOrder <= 1) return false;

		// 	var request = $.ajax({
		// 		url: "process.php",
		// 		method: "GET",
		// 		data: {rtype: 'ajax', act: act},
		// 		dataType: "html"
		// 	});

		// 	request.done(function(response) {
		// 		$(div).find('.panel-body').empty().html(response);
		// 		$(div).find('table').effect("highlight", {}, 500);
		// 	});

		// 	request.fail(function(jqXHR, textStatus) {
		// 		console.log("Request failed: " + textStatus);
		// 	});

		// 	return false;
		// };
		// loadOrder('.panel-order', 'load-order', {reload_time:(Math.floor(Math.random() * 21) + 11) * 1000});

		// --------------------------------------------------------------------------------------------------- //
		
		// var submitChat = function() {
		// 	var inputBox = $(".helpdotcom-post");
		// 	var data = inputBox.val();
		// 	// console.log('Chat ' + data);

		// 	if (!data || data.length <= 0) {
		// 		alert('Please input into the chat box.');
		// 		return false;
		// 	}

		// 	var page_id_send = 0;
		// 	if (typeof page_id !== typeof undefined)
		// 		page_id_send = parseInt(page_id);

		// 	var request = $.ajax({
		// 		url: "/chat/chat.php",
		// 		method: "POST",
		// 		data: {rtype: 'ajax', act: 'add', content: data, id: page_id_send},
		// 		dataType: "json"
		// 	});

		// 	request.done(function(msg) {
		// 		// var chat_id = msg.chat_id || 0;
		// 		// var user_id = msg.user_id || 0;
		// 		// var oauth_uid = msg.oauth_uid || 0;
		// 		// var user_name = msg.user_name || 'Member';
		// 		// var chat_time = msg.chat_time || '---';
		// 		// var content = msg.chat || '---';
		// 		// var photo = msg.photo || '';
		// 		// var background_color = 'background-color: rgba(0, 160, 105, 0.04);';
		// 		// if ($('.helpdotcom-message').length && ($('.helpdotcom-message').length + 1) % 2 == 0)
		// 		// 	background_color = 'background-color: rgba(100, 100, 100, 0.01);';
		// 		// var html = '<div data-id="' + chat_id + '" class="helpdotcom-message helpdotcom-left helpdotcom-background helpdotcom-timestamp" style="' + background_color + '"> \
		// 		// 				<div class="helpdotcom-message-content" style="min-height: 10px;"><i class="helpdotcom-thumbnail helpdotcom-section-avatar" name="Visitor #' + user_id + '" style="background-image: url(&quot;' + photo + '&quot;), url(&quot;' + photo + '&quot;);"></i><span class="helpdotcom-name">' + user_name + '</span><span class="helpdotcom-time">' + chat_time + '</span></div> \
		// 		// 				<div class="helpdotcom-message-content"><span class="helpdotcom-message-align">' + content + '</span></div> \
		// 		// 			</div>';
		// 		// $('.helpdotcom-messages').append(html);
		// 		// console.log('submitChat');
		// 		// console.log(scrollTop: $('.helpdotcom-message').last().data('id'));
		// 		// $('.helpdotcom-messages').animate({
		// 		// 	scrollTop: $('.helpdotcom-message').last().offset().top
		// 		// }, 100);
		// 	});

		// 	request.fail(function(jqXHR, textStatus) {
		// 		console.log("Request failed: " + textStatus);
		// 	});

		// 	inputBox.val('').focus();
		// 	// console.log('Submit ' + data);
		// 	return false;
		// };

	    // $(".helpdotcom-post").keydown(function(event) {
		// 	var code = (event.keyCode ? event.keyCode : event.which);
		// 	if (code == 13) {
		// 		return submitChat();
		// 	}
		// });

		// 

	}

	// --------------------------------------------------------------------------------------------------- //
	
	// var isMobile = function() {
	// 	var isMobile = false; //initiate as false
	// 	// device detection
	// 	if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
	// 	    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
	// 	    isMobile = true;
	// 	}
	// 	return isMobile;
	// }
	// if (isMobile()) {
	// 	// 
	// }

	// 
});
