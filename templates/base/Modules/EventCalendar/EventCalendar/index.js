$(function(){
	require(['ui', 'popupAlert'], function(ui, popupAlert) {
	
		var evtPopup = $('#evt-popup');
		evtPopup.click(function(evt){
			evt.stopPropagation();
		});
		evtPopup.on('click', '.close-evt-popup', function(){
			evtPopup.addClass('a-hidden').removeClass('edit-event').empty();
			return false;
		});
		evtPopup.on('click', '.edit-evt-btn', function(){
			$.ajax({
				url: $(this).attr('href'),
				dataType: 'json',
				success: function(data){
					if (data.errors === null){
						evtPopup.addClass('edit-event').html(data.content);
						if ($('.starttime-input', evtPopup).val() !== '') $('.starttime-select', evtPopup).val($('.starttime-input', evtPopup).val());
						if ($('.endtime-input', evtPopup).val() !== '') $('.endtime-select', evtPopup).val($('.endtime-input', evtPopup).val());
						evtPopup.removeClass('a-hidden');
						ui.initAll();
					} else {
						popupAlert.error({
							text: 'Произошла ошибка при загрузке мероприятия. Перезагрузите страницу и попробуйте еще раз.'
						});
					}
				},
				error: function(jqXHR, textStatus, errorThrown){
					popupAlert.error({
						text: 'Произошла ошибка при загрузке мероприятия. Перезагрузите страницу и попробуйте еще раз.',
						errors: 'Ошибка сервера: ' + textStatus
					});
				}
			});
			return false;
		});
		evtPopup.on('click', '.remove-evt-btn', function(){
			if (!confirm('Удалить мероприятие?')) return false;
			$.ajax({
				url: $(this).attr('href'),
				dataType: 'json',
				success: function(data){
					if (data.errors === null){
						$('#evt-calendar').fullCalendar('removeEvents', data.data.id);
						evtPopup.html('').addClass('a-hidden').removeClass('edit-event');
					} else {
						popupAlert.error({
							text: 'Произошла ошибка при удалении мероприятия. Перезагрузите страницу и попробуйте еще раз.'
						});
					}
				},
				error: function(jqXHR, textStatus, errorThrown){
					popupAlert.error({
						text: 'Произошла ошибка при удалении мероприятия. Перезагрузите страницу и попробуйте еще раз.',
						errors: 'Ошибка сервера: ' + textStatus
					});
				}
			});
			return false;
		});
		evtPopup.on('submit', 'form', function(evt){
			if ($(this).hasClass('sending')) return false;
			var form = $(this);
			form.addClass('sending');
			form.ajaxSubmit({
				dataType: 'json',
				success: function(data){
					form.removeClass('sending');
					if (data.errors === null){
						evtPopup.html(data.content).removeClass('a-hidden');
						if (data.data.action === 'update') {
							var currentEvent = $('#evt-calendar').fullCalendar('clientEvents', data.data.event.id)[0];
							currentEvent.title = data.data.event.title;
							currentEvent.start = new Date(data.data.event.start * 1000);
							currentEvent.end = new Date(data.data.event.end);
							currentEvent.allDay = false;
							currentEvent.textColor = '#000';
							currentEvent.backgroundColor = data.data.event_colors[data.data.event.properties.type_id];
							$('#evt-calendar').fullCalendar('updateEvent', currentEvent);
						} else {
							var eventSource = {
								events: [
									{
										id: data.data.event.id,
										title: data.data.event.title,
										start: new Date(data.data.event.start * 1000),
										end: new Date(data.data.event.end * 1000),
										allDay: false,
										textColor: '#000',
										backgroundColor: data.data.event_colors[data.data.event.properties.type_id]
									}
								]
							};
							$('#evt-calendar').fullCalendar('addEventSource', eventSource);
						}
						evtPopup.addClass('a-hidden').removeClass('edit-event').empty();
						popupAlert.ok({
							title: 'Редактирование мероприятия',
							text: 'Мероприятие успешно сохранено.'
						});
					} else {
						var errors = [];
						var errKeys = _.keys(data.errors);
						_.each(errKeys, function(i) {
							switch(i) {
								case 'title':
									errors.push('Не указано название');
									break;
								case 'location':
									errors.push('Не указано место');
									break;
								case 'type':
									errors.push('Не указан тип мероприятия');
									break;
								case 'attenders':
									errors.push('Не выбраны приглашенные лица');
									break;
							}
						});
						popupAlert.error({
							title: 'Редактирование мероприятия',
							text: 'Не удалось сохранить мероприятие.',
							errors: errors
						});
					}
				},
				error: function(err) {
					form.removeClass('sending');
					popupAlert.error({
						title: 'Редактирование мероприятия',
						text: 'Не удалось сохранить мероприятие.',
						errors: 'Ошибка сервера: ' + err.status
					});
				}
			});
			return false;
		});
		$('body').click(function(e){
			if ($(e.target).is('.ui-datepicker') || $(e.target).closest('.ui-datepicker').length) return;
			evtPopup.addClass('a-hidden').removeClass('edit-event').empty();
		});
		var events = [];
		$.each($('.evt-details'), function(){
			var evt = $(this);
			var event = {
				id: evt.data('id'),
				title: evt.data('title'),
				start: new Date(evt.data('start') * 1000),
				end: new Date(evt.data('end') * 1000),
				allDay: false,
				textColor: evt.data('foreground'),
				backgroundColor: evt.data('background')
			};
			events.push(event);
		});
		calendar = $('#evt-calendar').fullCalendar({
			firstDay: 1,
			timeFormat: 'H:mm',
			axisFormat: 'H:mm',
			allDayText: 'Весь день',
			allDaySlot: false,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			buttonText: {
				prev:     '&lsaquo;', // <
				next:     '&rsaquo;', // >
				prevYear: '&laquo;',  // <<
				nextYear: '&raquo;',  // >>
				today:    'Сегодня',
				month:    'Месяц',
				week:     'Неделя',
				day:      'День'
			},
			columnFormat: {
				month: 'ddd',    // Mon
				week: 'ddd d MMM', // Mon 9/7
				day: 'dddd d MMM'  // Monday 9/7
			},
			eventDrop: function( event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view ) { 
				if (confirm('Изменить время мероприятия?')){
					$.ajax({
						url: '/eventcal/updateDate/',
						data: {
							id: event.id,
							start: Math.floor(event.start.getTime()/1000),
							end: Math.floor(event.end.getTime()/1000)
						},
						type: 'post',
						dataType: 'json',
						success: function(data){
							console.log(data);
						},
						error: function(jqXHR, textStatus, errorThrown ){
							alert(textStatus);
							revertFunc();
						}
					});
				} else {
					revertFunc();
				}
			},
			eventResize: function( event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) {
				if (confirm('Изменить протяженность мероприятия?')){
					$.ajax({
						url: '/eventcal/updateDate/',
						data: {
							id: event.id,
							start: Math.floor(event.start.getTime()/1000),
							end: Math.floor(event.end.getTime()/1000)
						},
						type: 'post',
						dataType: 'json',
						success: function(data){
							console.log(data);
						},
						error: function(jqXHR, textStatus, errorThrown ){
							alert(textStatus);
							revertFunc();
						}
					});
				} else {
					revertFunc();
				}
			},
			eventClick: function(calEvent, jsEvent, view) {
				$.ajax({
					url: '/eventcal/viewEvent/',
					data: {
						id: calEvent.id
					},
					type: 'get',
					dataType: 'json',
					success: function(data){
						if (data.errors === null){
							evtPopup.html(data.content).removeClass('a-hidden');
							ui.initAll();
						} else {
							popupAlert.error({
								text: 'Произошла ошибка при загрузке мероприятия. Перезагрузите страницу и попробуйте еще раз.'
							});
						}
					},
					error: function(jqXHR, textStatus, errorThrown ){
						popupAlert.error({
							text: 'Произошла ошибка при загрузке мероприятия. Перезагрузите страницу и попробуйте еще раз.',
							errors: 'Ошибка сервера: ' + textStatus
						});
					}
				});
			},
			dayClick: function(date, allDay, jsEvent, view) {
				$.ajax({
					url: '/eventcal/edit/',
					type: 'get',
					dataType: 'json',
					success: function(data){
						if (data.errors === null){
							evtPopup.html(data.content);
							if (view.name === 'month') {
								date.setHours(12);
							}
							$('input[name=startDate]').val(makeDate(date));
							$('input[name=startTime]').val(makeTime(date));
							date.setHours(date.getHours() + 1);
							$('input[name=endDate]').val(makeDate(date));
							$('input[name=endTime]').val(makeTime(date));
							evtPopup.addClass('edit-event').removeClass('a-hidden');
							ui.initAll();
						} else {
							popupAlert.error({
								text: 'Произошла ошибка при создании мероприятия. Перезагрузите страницу и попробуйте еще раз.'
							});
						}
					},
					error: function(jqXHR, textStatus, errorThrown ){
						popupAlert.error({
							text: 'Произошла ошибка при создании мероприятия. Перезагрузите страницу и попробуйте еще раз.',
							errors: 'Ошибка сервера: ' + textStatus
						});
					}
				});
			},
			monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
			monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сент', 'Окт', 'Ноя', 'Дек'],
			dayNames: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
			dayNamesShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
			editable: true,
			events: events
		});

		function makeDate(jsDate){
			var date = jsDate.getDate() + '.';
			var month = jsDate.getMonth() + 1;
			if (month < 10){
				date = date + '0';
			}
			date = date + month + '.' + jsDate.getFullYear();
			return date;
		}
		function makeTime(jsDate){
			var time = jsDate.getHours() + ':';
			var minutes = jsDate.getMinutes();
			if (minutes < 10) {
				time = time + '0';
			}
			return time + minutes;
		}
	
	});
});