define(function() {
	var actionsInit = function() {
		
		$('.actions-panel').each(function() {
			var cont = $(this);
			if (cont.data('inited')) return;
			cont.data('inited', true);
			
			// действия с выборкой
			if (cont.hasClass('multiple')) {
				var actions = $('.m-inactive', cont).addClass('multi-action');
				var actionsCont = $(this).closest('.actions-cont');
				if (!actionsCont.length) actionsCont = $(this).closest('.tab-page');
				if (!actionsCont.length) actionsCont = $(this).closest('.edit-content');
				if (!actionsCont.length) actionsCont = $(this).closest('.view-content');
				if (!actionsCont.length) actionsCont = $('.main-content');
				var inputs = $('INPUT.check-item:checked', actionsCont);
				if (inputs.length) {
					$('INPUT.check-item:not(:disabled)', actionsCont).prop('checked', true);
					actions.removeClass('m-inactive');
				} else {
					$('INPUT.check-all', actionsCont).prop('checked', false);
					actions.addClass('m-inactive');
				}
			}
			
			// раскрывающийся блок
			if (cont.hasClass('expanded')) {
				var inner = $('.actions-panel-inner', cont);
				var hidden = $('.hidden-action', cont);
				var animated = false;
				var inited = false;
				var openTimer = 0;
				var time = 600;
				var min = 0;
				var max = 0;
				cont.on('mouseenter', '.action-expand', function() {
					if (!inited) {
						min = cont.width();
						max = min;
						cont.width(min);
						hidden.each(function() {
							max += $(this).innerWidth();
						});
						inner.width(max+1);
						hidden.removeClass('hidden-action');
						inited = true;
					}
					cont.width(max-1).addClass('m-open');
					clearTimeout(openTimer);
					openTimer = setTimeout(function() {
						animated = false;
					}, time);
					animated = true;
				}).on('mouseleave', function() {
					if (inited) {
						cont.width(min).removeClass('m-open');
						clearTimeout(openTimer);
						openTimer = setTimeout(function() {
							animated = false;
						}, time);
						animated = true;
					}
				}).on('click', function(e) {
					if ($(e.target).is(hidden) || $(e.target).closest(hidden).length) {
						if (animated) return false;
					}
				});
			}
		});
		
		$('.actions-cont').each(function() {
			var cont = $(this);
			if (cont.data('inited')) return;
			cont.data('inited', true);
			
			// снимаем/убираем все отметки
			cont.on('change', 'INPUT.check-all', function() {
				var actions = $(this).closest('.tab-page').length? 
					$('.actions-panel .multi-action', $(this).closest('.tab-page')) : $('.actions-panel .multi-action');
				var actionsCont = $(this).closest('.actions-cont');
				if ($(this).is(':checked')) {
					$('INPUT.check-item:not(:disabled)', actionsCont).prop('checked', true);
					actions.removeClass('m-inactive');
				} else {
					$('INPUT.check-item', actionsCont).prop('checked', false);
					actions.addClass('m-inactive');
				}
			});
			
			// отдельные позиции
			cont.on('change', 'INPUT.check-item', function() {
				var actions = $(this).closest('.tab-page').length? 
					$('.actions-panel .multi-action', $(this).closest('.tab-page')) : $('.actions-panel .multi-action');
				var actionsCont = $(this).closest('.actions-cont');
				if (!$('INPUT.check-item:checked', actionsCont).length) {
					$('INPUT.check-all', actionsCont).prop('checked', false);
					actions.addClass('m-inactive');
				} else if ($('INPUT.check-item:checked', actionsCont).length === $('INPUT.check-item:not(:disabled)', actionsCont).length) {
					$('INPUT.check-all', actionsCont).prop('checked', true);
					actions.removeClass('m-inactive');
				} else {
					$('INPUT.check-all', actionsCont).prop('checked', false);
					actions.removeClass('m-inactive');
				}
			});
		});
	};
	
	actionsInit();
	return actionsInit;
});