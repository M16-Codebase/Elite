define(function() {
	
	var defaultOpt = {
		url: '/files/upload/',
		portion: 1024*1024*2,
		timeout: 15000,
		hash: '',
		name: '',
		maxsize: 0, 
		format: '',
		progressbar: '',
		progresstext: '',
		progresstextHandler: function(progressData) {},
		start: function(progressData) {},
		progress: function(progressData) {},
		complete: function(data) {},
		error: function(err) {}
	};
	
	var uploadFile = function(input, userOpt) {
		input = $(input).first();
		userOpt = userOpt || {};
		
		var options = {};
		for (var i in defaultOpt) {
			options[i] = (typeof userOpt[i] !== 'undefined')? userOpt[i] : 
				(input.data(i) !== undefined)? input.data(i) : defaultOpt[i];
		}
		if (options.maxsize) options.maxsize = parseInt(options.maxsize);
		if (!options.maxsize) options.maxsize = Number.POSITIVE_INFINITY;
		if (typeof options.format === 'string') {
			options.format = options.format.replace(/\s/g, '').split(',');
		} else if (!(options.format instanceof Array) || !options.format.length) {
			options.format = '';
		}
		
		if (!options.url || !options.hash) {
			options.error('no required options');
			return false;
		}
		if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
			options.error('old browser');
			return false;
		}
		if (!input.length) {
			options.error('no input');
			return false;
		}
		if (!input[0].files.length) {
			options.error('no file selected');
			return false;
		}
		
		var file = input[0].files[0];
		var filesize = file.size;
		
		if (filesize > options.maxsize) {
			options.error('over max size');
			return false;
		}
		if (options.format) {
			var allowedExt = false;
			for (var i in options.format) {
				var ext = new RegExp('.' + options.format[i] + '$', 'i');
				if (file.name.match(ext)) {
					allowedExt = true;
					break;
				}
			}
			if (!allowedExt) {
				options.error('not allowed format');
				return false;
			}
		}
		if (input.data('sending')) {
			options.error('sending');
			return false;
		}
		
		var position = 0;
		var uploadStarted = false;
		input.data('sending', true);
		uploadPortion(0);
		
		// считываем и отправляем на сервер порцию файла
		function uploadPortion(from) {
			var reader = new FileReader();
			var loadfrom = from;
			var xhrHttpTimeout = 0;
			
			// отправляем на сервер считанную порцию файла
			reader.onloadend = function(e) {
				if (e.target.readyState !== FileReader.DONE) return;
				var xhr = new XMLHttpRequest();
				xhr.open('POST', options.url, true);
				xhr.setRequestHeader('Content-Type', 'application/x-binary; charset=x-user-defined');
				xhr.setRequestHeader('Upload-Id', options.hash);
				xhr.setRequestHeader('Portion-From', from);
				xhr.setRequestHeader('Portion-Size', options.portion);
				xhrHttpTimeout = setTimeout(function() {
					xhr.abort();
				}, options.timeout);

				// прогресс загрузки порции
				var progressBar = (options.progressbar && $(options.progressbar).length)? $(options.progressbar) : false;
				var progressText = (options.progresstext && $(options.progresstext).length)? $(options.progresstext) : false;
				xhr.upload.addEventListener('progress', function(evt) {
					if (evt.lengthComputable) {
						var loaded = loadfrom + evt.loaded;
						var percent = Math.round(loaded * 1000 / filesize) / 10;
						var progressData = {
							event: evt,
							file: file,
							input: input,
							percent: percent,
							loaded: loaded,
							filesize: filesize,
							loadedStr: sizeStr(loaded),
							filesizeStr: sizeStr(filesize)
						};
						if (!uploadStarted) {
							// начало загрузки первой порции
							uploadStarted = true;
							options.start(progressData);
						}
						if (progressText) {
							var text = options.progresstextHandler(progressData);
							if (typeof text === 'string' || typeof text === 'number') progressText.text(text);
							else if (text !== false) progressText.text(percent + '%');
						}
						if (progressBar) {
							progressBar.css({width: percent + '%'});
						}
						options.progress(progressData);
					}
				}, false);

				// окончание загрузки порции
				xhr.addEventListener('load', function(evt) {
					clearTimeout(xhrHttpTimeout);
					if (evt.target.status !== 200) {
						options.error('Last part. Server error: ' + evt.target.status);
						input.data('sending', false);
						uploadStarted = false;
						return;
					}
					position += options.portion;
					// проверяем, остались ли ещё порции
					if (filesize > position) {
						uploadPortion(position);
					} else {
						// сообщаем серверу о конце загрузки
						var gxhr = new XMLHttpRequest();
						var sendingParams = {
							action: 'done',
							file_name: file.name,
							name: options.name || input.attr('name') || 'file'
						};
						gxhr.open('GET', options.url + '?' + $.param(sendingParams), true);
						gxhr.setRequestHeader('Upload-Id', options.hash);
						gxhr.addEventListener('load', function(event) {
							if (event.target.status !== 200) {
								options.error('Finish. Server error: ' + event.target.status);
								input.data('sending', false);
								uploadStarted = false;
							} else {
								var res = JSON.parse(event.target.responseText);
								if (res.errors) {
									options.error(res.errors);
								} else {
									options.complete({
										response: res,
										path: res.data.file_path,
										input: input,
										file: file
									});
								}
								input.data('sending', false);
								uploadStarted = false;
							}
						}, false);
						gxhr.sendAsBinary('');
					}
				}, false);

				// ошибка при загрузке
				xhr.addEventListener('error', function() {
					clearTimeout(xhrHttpTimeout);
					gxhr.sendAsBinary('');
					options.error('upload error');
					input.data('sending', false);
					uploadStarted = false;
					// сообщаем серверу об ошибке
					var gxhr = new XMLHttpRequest();
					gxhr.open('GET', options.url + '?action=abort', true);
					gxhr.setRequestHeader('Upload-Id', options.hash);
					gxhr.addEventListener('load', function (evt) {
						if (evt.target.status !== 200) {
							options.error('Error message. Server error: ' + evt.target.status);
							input.data('sending', false);
							uploadStarted = false;
						}
					}, false);
				}, false);

				// отмена загрузки
				xhr.addEventListener('abort', function() {
					clearTimeout(xhrHttpTimeout);
					uploadPortion(position);
				}, false);

				// загружаем порцию
				xhr.sendAsBinary(e.target.result);
			};
			
			// читаем порцию файла
			var blob = null;
			if (file.slice) {
				blob = file.slice(from, from + options.portion);
			} else if (file.webkitSlice) {
				blob = file.webkitSlice(from, from + options.portion);
			} else if (file.mozSlice) {
				blob = file.mozSlice(from, from + options.portion);
			}
			reader.readAsBinaryString(blob);
		};
	};
	
	// человеческое обозначение размера файла
	function sizeStr(size) {
		var str = '';
		if (size > 1000000) {
			str = (size / 1000000).toFixed(2) + ' Мб';
		} else if (size > 1000) {
			str = (size / 1000).toFixed(2) + ' Кб';
		} else {
			str = size + ' бит';
		}
		return str;
	};
	
	// фикс для хрома
	try {
		if (typeof XMLHttpRequest.prototype.sendAsBinary === 'undefined') {
			XMLHttpRequest.prototype.sendAsBinary = function(text){
				var data = new ArrayBuffer(text.length);
				var ui8a = new Uint8Array(data, 0);
				for (var i = 0; i < text.length; i++) ui8a[i] = (text.charCodeAt(i) & 0xff);
				this.send(ui8a);
			};
		}
	} catch(e) {}
	
	return uploadFile;
});