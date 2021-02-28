define(['ui'], function(ui) {
	// IMAGE PREVIEW
	var initPreview = function(el){
		var el = el || '.img-preview';
		el = $(el); 
		if (!el.length) return;
		el.each(function() {
			var block = $('.img-preview-body', el);
			if(el.data('imgpreview-init')) return;
			else el.data('imgpreview-init', true);
			var input = $('input[type=file]', el);
			var addBtn = $('.add-btn', el);
			var icon = $('I', addBtn);
			var textField = $('SPAN', addBtn);
			$('body').on('change', input, function(evt){
				if (!evt.target.files)  return;
				var origin = $('.origin', el).clone().removeClass('origin');
				var originImg = origin.find('img');
				var files = evt.target.files;
				var reader = {};
				$.each(files, function(index){
					reader = new FileReader();
					reader.onloadstart = function() {
						$('.preloader', block).fadeIn(100);
					};
					reader.onprogress  = (function(t){
						if (event.lengthComputable) {
							var total = t.total;
							var loaded = t.loaded;
							var perc = Math.floor((loaded/total)*100);
							$('.preloader DIV', block).css({width:perc + '%'});
						}
					});
					reader.onload = (function(theFile) {
						return function(e) {
							origin.attr('href', e.target.result);
							originImg.attr('src', e.target.result);
							if(input.attr('multiple')){
								block.append(origin);
							} else {
								block.html(origin);
							};
							ui.fancybox.init(origin);
						};
					})(files[index]);
					reader.onloadend = function() {
						$('.preloader', block).remove();
						if( icon.hasClass('icon-add') ) {
							icon.attr('class', 'icon-replace');
							textField.text('Заменить изображение');
							$('.delete-photo', el).css({display: 'inline-block'});
							block.closest('.row').removeClass('a-hidden');
						} 
					};
					reader.readAsDataURL(files[index]);
				});
			});
		});
	};
	return initPreview;
});
	