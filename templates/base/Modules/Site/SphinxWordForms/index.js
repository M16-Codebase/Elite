$(function() {
	require(['ui', 'gatherSyns'], function(ui, gatherSyns) {

		//переменные
		var $addingPanel=$(".add-syn-form");
		var $addingPanelCreate=$(".action-button.action-save", $addingPanel);
		var $addingPanelCancel=$(".action-button.action-back", $addingPanel);
		var $searchBody=$(".syn-manipulations");
		var $synGroup=$(".syn-group");
		var $synSearch=$(".syn-search");
		var $resetSyns=$(".reset-syns");
		var $sortString=$(".sort-string");
		var $synBody=$(".syn-body");
		var tagitOptions={
			triggerKeys:['enter', 'space', 'tab'],
			seperatorKeys:['comma','semicolon', 'space']
		};
		var slideboxOptions={
			"beforeOpen":function() {
				$(this).find(".read-mode").hide();
				toggleInterface("off");
			},
			"close":function() {
				var $target=$(this);
				var $readMode=$target.find(".read-mode");
				var $editMode=$target.find(".edit-mode");
				var $tagsCont=$editMode.find(".tags-cont");
				var mainWordText=$readMode.find(".main-word").text();
				var $changeMainWord=$target.find(".change-main-word");
				var $errorsCont=$editMode.find(".errors");
				var allSyns=$readMode.find(".syn-list").text();
				allSyns=synsAsObjArray(allSyns);
				$tagsCont.tagit("fill", allSyns);
				$changeMainWord.val(mainWordText);
				$errorsCont.empty(mainWordText);
				$readMode.slideDown(function() {toggleInterface("on");});
			}
		};
			
		/*Функции общего назначения*/
		
		//Сброс отображается только в том случае, если параметр search что-то содержит (была произведена фильтрация)
		function toggleResetButton() {
			if(getQueryVariable("search")) {
				$resetSyns.css("visibility", "visible");
			} else {
				$resetSyns.css("visibility", "hidden");
			}			
		}
		
		toggleResetButton();
		
		//получаем переменные из url
		function getQueryVariable(variable)
		{
			   var query = window.location.search.substring(1);
			   var vars = query.split("&");
			   for (var i=0;i<vars.length;i++) {
					   var pair = vars[i].split("=");
					   if(pair[0] == variable){return pair[1];}
			   }
			   return(false);
		}
		
		//очищаем поля, убираем тэги, очищаем ошибки
		function cleanPanel(addingPanel) {
			addingPanel.find(".errors").empty();
			addingPanel.find(".change-main-word").val("");
			addingPanel.find(".tags-cont").tagit("reset");		
		}
		
		//блокировать/разблокировать интерфейс
		function toggleInterface(state) {
			switch(state) {
				case "on":
					$resetSyns.removeClass("off");
					$synSearch.removeAttr("disabled", "disabled");
					$sortString.removeClass("off");
					$(".action-add").removeClass("off");
					$(".single-syn").addClass("m-enabled");
				break;
				case "off":
					$resetSyns.addClass("off");
					$synSearch.attr("disabled", "disabled");
					$sortString.addClass("off");
					$(".action-add").addClass("off");
					$(".single-syn").removeClass("m-enabled");
				break;
			}
		}
			
		//преобразует строку синонимов, разделенных запятыми в массив объектов для реализации метода tagit(fill) - см. документацию плагина
		function synsAsObjArray(synString) {
			synString=synString.split(", ");
			var allSynsArray=[];
			for(var i=0; i<synString.length; i++) {
				var currentValue=synString[i];
				var synObj={label:currentValue, value: currentValue};
				allSynsArray.push(synObj);
			}
			return allSynsArray;
		}
		
		/*получает объект со строковыми ключами, разбивает их запятыми и возвращает единую строку*/
		function objToCommaList(object) {
			var commaList="";
			var listLength=0;
			var counter=0;
			var first=true;
			for(var item in object) {
				++listLength;
			}
			var onlyOne=(listLength === 1) ? true : false;
			if(onlyOne) {
				commaList="«"+object[item]+"»";
			} else {
				for(var item in object) {
					++counter;
					var prefix=(first) ? "«" : "";
					var tail=(counter === listLength) ? "»" : "», «";
					commaList+=prefix+object[item]+tail;
					first=false;
				}
			}
			var info={
				howMuch:onlyOne,
				string:commaList
			};
			return info;
		}
		
		//инициировать тэги
		$('.tags-cont').tagit(tagitOptions);
		
		//инициировать автокомплит
		$synSearch.autocomplete({
			appendTo: ".auto-menu",
			delay:50,
			source:"/sphinx-wordforms/autocomplete/",
			minLength:1,
			select:function(event, ui) {
				var synValue=ui.item.value;
				var order=($sortString.hasClass("m-backward")) ? 1 : 0;
				autocompleteSubmit(synValue, order);
			}
		});	
		
		//обработка ошибок
		function handleErrors(obj, cont) {
			var errors=obj.errors;
			cont.empty();
			for(var key in errors) {
				if(key == "dst_form" && errors[key] == "empty") {
					cont.append("<li>Введите основное слово</li>");
				}
				if(key == "dst_form" && errors[key] == "conflict") {
					cont.append("<li>Слово, которое вы хотите сделать основным уже используется где-то в качестве синонима</li>");
				}
				if(key == "src_form") {
					if(errors[key] == "empty") {
						cont.append("<li>Введите синонимы</li>");
					}else if(typeof(errors[key] == "object")) {
						var info=objToCommaList(errors[key]);
						var errorString=(info.howMuch) ? "Синоним "+info.string+", который вы пытаетесь использовать задействован в качестве основного слова" : "Синонимы "+info.string+", которые вы пытаетесь использовать задействованы в качестве основных слов";
						cont.append("<li>"+errorString+"</li>");
					}
				}
				if(key == "conflict") {
					var duplicates=errors[key];
					var info=objToCommaList(duplicates);
					var errorString=(info.howMuch) ? "Синоним "+info.string+" уже задействован" : "Синонимы "+info.string+" уже задействованы";
					cont.append("<li>"+errorString+"</li>");
				}
			}
		}
		
		function afterSorting(res) {
			$synGroup.empty().append(res);
			$('.tags-cont').tagit(tagitOptions);
			ui.slidebox(".single-syn-form", slideboxOptions);
		}
		
		function afterDel(res) {
			$synGroup.empty().append(res);
			$('.tags-cont').tagit(tagitOptions);
			ui.slidebox(".single-syn-form", slideboxOptions);
			var nothingExists=$synGroup.find(".nothing-exists").length;
			if(nothingExists) {//все синонимы были удалены
				$searchBody.addClass("a-hidden");				
			} 			
		}
		
		function doSorting(order, searchVal, callback) {
			$.ajax({
				type:"GET",
				url:"wordformList",
				data:{
					sort:order,
					search:searchVal
				},
				success:callback
			});
			if (window.history) {
				history.pushState({}, '', '?search='+searchVal+'&order='+order);
			}			
		}
		
		function autocompleteSubmit(synValue, order) {
			$.ajax({
				type:"GET",
				url:"wordformList",
				data:{
					sort:order,
					search:synValue					
				},
				success:function(res) {
					$synGroup.empty().append(res);
					$('.tags-cont').tagit(tagitOptions);
					ui.slidebox(".single-syn-form", slideboxOptions);
					//$resetSyns.css("visibility", "visible");
					if (window.history) {
						history.pushState({}, '', '?search='+synValue);
					}
					toggleResetButton();
					$synSearch.autocomplete("close");
				}
			});			
		}
		
		function resetSyns() {
			$synSearch.val("");
			if($sortString.hasClass("m-backward")) {
				$sortString.trigger("click");
			} else {
				$sortString.removeClass("m-backward");
				doSorting(0, "", afterSorting);				
			}
		}
		
		/*События*/
		
		//выезжающая панель
		$('.actions-panel .action-add').click(function(){
			if($(this).hasClass("off")) return;
			$('.edit-content .add-form').show().siblings().hide();
			$('.main-content-inner').addClass('m-edit');
			return false;
		});

		//закрытие панели создания синонима
		$addingPanelCancel.on("click", function() {
			cleanPanel($addingPanel);
		});
		
		//сортируем синонимы
		$sortString.click(function() {
			var $target=$(this);
			if($target.hasClass("off")) return;
			var sortValue=0;
			var defaultText="Сортировать А — Я";
			var newText="Сортировать Я — А";
			$target.toggleClass("m-backward");
			if($target.hasClass("m-backward")) {
				$target.text("");
				$target.text(newText);
				sortValue=1;
			} else {
				$target.text("");
				$target.text(defaultText);
				sortValue=0;
			}			
			var searchingValue=$synSearch.val();
			doSorting(sortValue, searchingValue, function(res) {afterSorting(res);});
		});
		
		//удаляем синоним
		$synBody.on("click", ".action-button.action-delete", function() {
			var userAnswer=confirm("Вы уверены, что хотите удалить группу синонимов?");
			if(!userAnswer) return;
			var $target=$(this);
			var $parent=$target.closest(".single-syn");
			var deleted=$(".main-word", $parent).text();
			var ordValue=($sortString.hasClass("m-backward")) ? 1 : 0;
			var searchValue="";
			$.ajax({
				type:"POST",
				url:"/sphinx-wordforms/delete/",
				data:{
					dst_form:deleted
				},
				success:function() {
					$parent.remove();
					var synsLength=$synGroup.find(".single-syn").length;
					if(!synsLength) {//если было стерто все - надо выяснить: все из текущей выборки или все вообще!
						doSorting(ordValue, searchValue, function(res) {afterDel(res);});						
					}				
				}
			});				
		});
		
		//добавляем новый синоним
		$addingPanel.submit(function(e) {
			e.preventDefault();
			var $form=$(this);
			var $errors=$(".errors", $form);
			var $mainWord=$(".change-main-word", $form);
			var mainWordValue=$mainWord.val();
			var $tagsWidget=$(".syns-area", $form);
			var synsArray=gatherSyns($tagsWidget);
			$.ajax({
				type:'POST',
				url:"/sphinx-wordforms/add/",
				data:{
					dst_form:mainWordValue,
					src_form:synsArray,
					sort:($sortString.hasClass("m-backward")) ? 1 : 0
				},
				beforeSend: function() {
					$errors.empty();
				},
				success:function(res) {
					try {
						var json = $.parseJSON(res);
						handleErrors(json, $errors);
					} catch(e) {
						$synGroup.empty().append(res);
						if($searchBody.hasClass("a-hidden")) {
							$searchBody.removeClass("a-hidden");
						}
						$('.tags-cont').tagit(tagitOptions);
						ui.slidebox(".single-syn-form", slideboxOptions);
						cleanPanel($addingPanel);
						$addingPanelCancel.trigger("click");						
						resetSyns();
					}
				}
			});
		});
		
		//редактируем синоним
		$synBody.on("submit", ".edit-syn-form", function(evt) {
			evt.preventDefault();
			var $form=$(this);
			var $errors=$(".errors", $form);
			var $tagsWidget=$(".syns-area", $form);
			var synsArray=gatherSyns($tagsWidget);
			$form.ajaxSubmit({
				data: {
					src_form: synsArray
				},
				success: function(data){
					try {
						var json = $.parseJSON(data);
						handleErrors(json, $errors);
					} catch(e) {
						var li = $form.parents('li');
						li.before(data);
						li.remove();
						toggleInterface("on");
						$('.tags-cont').tagit(tagitOptions);
						ui.slidebox(".single-syn-form", slideboxOptions);
					}
				}
			});
		});
		
		//раскрываем/закрываем синоним
		ui.slidebox(".single-syn-form", slideboxOptions);
		
		//отправляем синоним, набранный автокомплитом на сервер
		$(".syn-search-cont").on("submit", function(e) {
			e.preventDefault();
			var synValue=$synSearch.val();
			var order=($sortString.hasClass("m-backward")) ? 1 : 0;
			autocompleteSubmit(synValue, order);
		});
		
		//кнопка сброса
		$resetSyns.click(function() {
			if($(this).hasClass("off")) return;
			$resetSyns.css("visibility", "hidden");
			resetSyns();
		});
		
	});
});