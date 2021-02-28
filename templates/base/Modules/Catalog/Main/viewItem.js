$(function() {
	require(['ui', 'equalCol', 'popupFilter', 'userForm', 'popupAlert', 'changeCount', 'carousel', 'gradienttext', "slider"], function(ui, equalCol, popupFilter, userForm, popupAlert) {
		
		function paintText(elements, colors) {
			var $elements = $(elements);
			$elements.gradientText({
				colors: colors
			});
		}
		
		//tooltips
		function createTooltip() {
			$(".has-tip").tooltip({
				tooltipClass: "simple-tip with-top-pointer",
				position: { my: "left top+15", at: "left-10 bottom" }
			});
		}
		createTooltip();
			
		// addToCart
		var addToCart = function(id, count, callback) {
			if (!id) return;
			count = count || 1;
			callback = callback || function() {};
			$.post('/order/addVariantToOrder/', {
				variant_id: id,
				count: count
			}, function(res) {
				if (res.data.order_id) {
					$('.popup-add-variant-to-cart').html(res.content).dialog('open');
					paintText(".popup-add-variant-to-cart .grad-text", ["#2f3d74", "#da4b65"]);
					updateCart(res.data.order_id, callback);
					$('.variant-buy .v'+id).add('.variants-buy-table .last-col .v'+id).each(function() {
						if($(this).hasClass("add-to-cart")) {
							$(this).addClass("a-hidden");
						} else if ($(this).hasClass("m-in-basket")) {
							$(this).removeClass("a-hidden");
						}
					});
					$(".variant-count.v"+id).addClass("a-hidden");
				}			
			}, 'json');
		};
		$('.add-to-cart').on('click', function() {
			var cont = $(this).closest('.item-variant');
			if ($('.change-count-arrows', cont).hasClass('a-hidden')) $('.count-selector', cont).trigger('countselected');
			var count = parseInt($('.count-text .num', cont).text()) || 1;
			addToCart($(this).data('id'), count);
			return false;
		});
		
				
		// change variant
		var animated = false;
		var chnageSpeed = 300;
		var variantsSelect = $('.variants-select-block');
		var currentVar = $('.item-code .variant-code.m-current').data('id');		
		var initFb = function() {
			var images = $('.item-gallery .item-images .img');
			var title = $('.variants-select-block .title.v' + currentVar).text();
			var imgList = $('.item-code .variant-code.v' + currentVar).hasClass('photo-merge')? '.item, .v' + currentVar : '.v' + currentVar;
			var curCol = images.filter(imgList);
			curCol.fancybox({
				minWidth: 600,
				padding: 0,
				tpl: {
					wrap: '<div class="fancybox-wrap" tabIndex="-1">' +
						'<div class="fancybox-skin">' +
							'<div class="fancybox-header">'+
								'<div class="fancybox-header-title">' + title + '</div>' +
							'</div>' + 
							'<div class="fancybox-outer">' +
								'<div class="fancybox-inner">' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>',
					closeBtn: '<a title="Закрыть" class="fancybox-item fancybox-close" href="javascript:;"></a>',
					next: '<a title="Вперед" class="fancybox-nav fancybox-next" href="javascript:;"></a><span class="fancybox-btn fancybox-next"><i></i></span>',
					prev: '<a title="Назад" class="fancybox-nav fancybox-prev" href="javascript:;"></a><span class="fancybox-btn fancybox-prev"><i></i></span>'
				},
				helpers: {
					title: {
						type: 'inside'
					}
				}
			});
		};
		var changeVariant = function(id) {
			id = id || $('.item-code .variant-code.m-current').data('id');
			var itemId = $('.item-code').data('item-id');
			if (id === currentVar) return;
			if (animated) return;
			animated = true;
			$('.var-switch.m-current').fadeOut(chnageSpeed, function() {
				$(this).removeClass('m-current').addClass('a-hidden');
				$(this).siblings().filter('.v' + id).fadeIn(chnageSpeed, function() {
					$(this).removeClass('a-hidden').addClass('m-current');
				});
			});
			currentVar = id;
			checkVarImages();
			initFb();
			if (window.history) {
				var newPath = location.pathname.replace(/(\/v\d+)?\/$/i, '/v' + id + '/');
				history.replaceState({}, '', newPath);
			}
			$('.page-header-admin .admin-btn-site').attr('href', '/catalog-item/edit/?id=' + itemId + '&tab=variants&v=' + id);
			setTimeout(function() {
				animated = false;
			}, chnageSpeed*2.2);
		};
		changeVariant();
		initFb();
		
		var itemId = $('.view-item-page').data('id');
		$('.variant-titles', variantsSelect).add($('.variants-select-cloud')).add($('.item-variant .variant-title')).click(function() {
			$.get('/catalog/variantsPopup/', {item_id: itemId}, function(res) {
				$('.popup-select-variant').html(res.content);
				popupFilter($('.aside-filter'));
				ui.initAll();
				$('.popup-select-variant').dialog('open');
				$('.search-variant-result').on('click', '.variant-title', function() {
					$('.popup-select-variant').dialog('close');
					changeVariant($(this).data('id'));
					ui.scrollTo(200);
					return false;
				});
				$('.search-variant-result').on('click', '.add-to-cart', function() {
					addToCart($(this).data('id'));
				}).on('click', '.m-in-basket', function() {
					location.href = $(this).attr('href');
				});;
			}, 'json');
		});
			
		$('.arrows DIV', variantsSelect).click(function() {
			var next = $(this).hasClass('arrow-next');
			var current = $('.title.m-current', variantsSelect);
			var newId = current.data('id');
			if (next) {
				if (current.next().length) newId = current.next().data('id');
				else newId =  $('.title:first', variantsSelect).data('id');
			} else {
				if (current.prev().length) newId = current.prev().data('id');
				else newId = $('.title:last', variantsSelect).data('id');
			}
			changeVariant(newId);
		});
		
		
		// gallery
		var checkVarImages = function() {
			var arrows = $('.item-gallery .arrows');
			var curCol = $('.item-gallery .item-images .v' + currentVar);
			if ($('.item-code .variant-code.v' + currentVar).hasClass('photo-merge')) {
				curCol = curCol.add($('.item-gallery .item-images .item'));
			}
			if (!curCol.filter('.m-current').length || $('.item-gallery .item-images .m-current').length > 1) {
				$('.item-gallery .item-images .m-current').removeClass('m-current').fadeOut(chnageSpeed, function() {
					$(this).addClass('a-hidden');
				});
				setTimeout(function() {
					if (curCol.length) {
						curCol.first().css({left: 0}).fadeIn(chnageSpeed).addClass('m-current').removeClass('a-hidden');
					} else {
						$('.item-gallery .item-images .empty-img').css({left: 0}).fadeIn(chnageSpeed).addClass('m-current').removeClass('a-hidden');
					}
				}, chnageSpeed);
			}
			if (curCol.length > 1) arrows.fadeIn();
			else arrows.fadeOut();
		};
		$('.item-gallery').each(function() {
			var speed = 300;
			var left = 522;
			var cont = $(this);
			var arrows = $('.arrows', cont);
			var images = $('.item-images .img', cont);
			var changeImage = function(current, next, forward) {
				animated = true;
				current.css({
					left: 0
				}).animate({
					left: (forward? -left : left)
				}, speed, 'linear', function() {
					$(this).removeClass('m-current').addClass('a-hidden');
				});
				next.css({
					left: (forward? left : -left),
					display: 'block'
				}).removeClass('a-hidden').animate({
					left: 0
				}, speed, 'linear', function() {
					$(this).addClass('m-current');
					animated = false;
				});
				if (window.getSelection) {
					window.getSelection().removeAllRanges();
				} else if (document.selection) {
					document.selection.empty();
				}
			};
			checkVarImages();
			$('.arrow-prev', arrows).on('click', function() {
				if (animated) return false;
				var imgList = $('.item-code .variant-code.v' + currentVar).hasClass('photo-merge')? '.item, .v' + currentVar : '.v' + currentVar;
				var curCol = images.filter(imgList);
				var current = images.filter('.m-current');
				var next = current.prevAll(imgList).length? current.prevAll(imgList).first() : curCol.last();
				changeImage(current, next, 0);
			});
			$('.arrow-next', arrows).on('click', function() {
				if (animated) return false;
				var imgList = $('.item-code .variant-code.v' + currentVar).hasClass('photo-merge')? '.item, .v' + currentVar : '.v' + currentVar;
				var curCol = images.filter(imgList);
				var current = images.filter('.m-current');
				var next = current.nextAll(imgList).length? current.nextAll(imgList).first() : curCol.first();
				changeImage(current, next, 1);
			});
		});
				
				
		equalCol($(".single-item"));
		
		// Вопрос по товару
		(function() {
			var $blurCollection = $(".page-wrap");
			$(".btn-item-question").click(function() {
				$blurCollection.addClass("blur-filter");
				$(".popup-item-question").dialog({
					"dialogClass":"ring-order-modal",
					"width":349,
					"close":function() {
						$blurCollection.removeClass("blur-filter");
					}
				}).dialog("open");
			});			
		})();		
		$('.ring-order.popup-window').dialog({
			open: function() {
				$('.popup-form').clearForm().removeClass('sended');
				$('.popup-form .default-data-field').each(function(){
					$(this).val($(this).data('default-data'));
				});
				if ($('.popup-form').hasClass('variant-request-form')) {
					$('.variant-request-form input[name="variant_id"]').val($('.btn.variant-request').data('id'));
				}
				$('.popup-form .m-error').removeClass('m-error');
			}
		});	
		
		userForm.init($('.item-question-form'), {
			data: {ajax: 1},
			beforeSubmit: function() {
				$('.item-question-form .m-error').removeClass('m-error');
			},
			errors: function(err) {
				for (var e in err) {
					$('.item-question-form [name="' + e + '"]').addClass('m-error');
				}
			},
			success: function(res) {
				$('.item-question-form').addClass('sended');
			},
			serverError: function(err) {
				popupAlert.error({
					text: 'Не удалось отправить заявку.',
					errors: 'Ошибка сервера: ' + err.status
				});
			}
		});
		
		// Запрос товара не в наличии
		$('.btn.variant-request').click(function(evt){
			evt.preventDefault();
			var wnd = $('.popup-window.popup-variant-request');
			$('input[name="variant_id"]', wnd).val($(this).data('id'));
			wnd.dialog({
				title: 'Запрос товара'
			}).dialog('open');
		});
		
		(function() {
			var $blurCollection = $(".page-wrap");
			$(".variant-request.btn").click(function() {
				$blurCollection.addClass("blur-filter");
				$(".popup-variant-request").dialog({
					"dialogClass":"ring-order-modal",
					"width":349,
					"close":function() {
						$blurCollection.removeClass("blur-filter");
					}
				}).dialog("open");
			});			
		})();		
		userForm.init($('.variant-request-form'), {
			data: {ajax: 1},
			beforeSubmit: function() {
				$('.variant-request-form .m-error').removeClass('m-error');
			},
			errors: function(err) {
				for (var e in err) {
					$('.variant-request-form [name="' + e + '"]').addClass('m-error');
				}
			},
			success: function() {
				$('.variant-request-form').addClass('sended');
			},
			serverError: function(err) {
				popupAlert.error({
					text: 'Не удалось отправить заявку.',
					errors: 'Ошибка сервера: ' + err.status
				});
			}
		});
		
		/*******************************Switch blur collection*********************/
		function switchClass(elem, className) {
			$(elem).toggleClass(className);
		}
		
		/********************************Show dialog about price changing***********/
		(function() {
			//var $blurCollection = $(".page-wrap");			
			$(".price-noty-handler").click(function() {
				var variant_id = $(this).data('variant_id');
				//$blurCollection.addClass("blur-filter");
				switchClass(".page-wrap", "blur-filter");
				$(".popup-price-changing").dialog({
					"dialogClass":"ring-order-modal",
					"width":349,
					"close":function() {
						switchClass(".page-wrap", "blur-filter");					
					}
				}).dialog("open");
				$(".popup-price-changing .variant-id-fld").val(variant_id);
			});			
		})();
		
		/********************************Show popup about more goods***********/
		(function() {
			//var $blurCollection = $(".page-wrap");
			$(".more-goods-handler").click(function() {
				var variant_id = $(this).data('variant_id');
				//$blurCollection.addClass("blur-filter");
				switchClass(".page-wrap", "blur-filter");
				$(".popup-more-goods").dialog({
					"dialogClass":"ring-order-modal",
					"width":369,
					"close":function() {
						//$blurCollection.removeClass("blur-filter");
						switchClass(".page-wrap", "blur-filter");
					}
				}).dialog("open");
				$(".popup-more-goods .variant-id-fld").val(variant_id);
			});			
		})();
		
		/******************************Get info about goods incoming****************/
		(function() {
			//var $blurCollection = $(".page-wrap");
			$(".income-info").click(function() {
				var variant_id = $(this).data('variant_id');
				//$blurCollection.addClass("blur-filter");
				switchClass(".page-wrap", "blur-filter");
				$(".popup-income-info").dialog({
					"dialogClass":"ring-order-modal",
					"width":369,
					"close":function() {
						//$blurCollection.removeClass("blur-filter");
						switchClass(".page-wrap", "blur-filter");
					}
				}).dialog("open");
				$(".popup-income-info .variant-id-fld").val(variant_id);
			});			
		})();		
		
		/****************************Cheap widget handler********************************/
		(function() {
			$("body").click(function(event) {
				$(".cheap-dialog").fadeOut(200);				
			});
			$(".cheap-dialog-open").click(function(event) {
				event.stopPropagation();
				$(".cheap-dialog").fadeIn(200);					
			});
			$(".cheap-dialog-close").click(function() {				
				$(".cheap-dialog").fadeOut(200);					
			});			
		})();
				
	});
});