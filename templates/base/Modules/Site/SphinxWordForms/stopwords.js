
$(function() {
	require(['gatherSyns'], function(gatherSyns) {
		
		//variables
		var $stopArea=$(".stopwords-area");
		var tagitOptions={
			triggerKeys:['enter', 'space', 'tab'],
			seperatorKeys:['comma','semicolon', 'space']
		};
		
		//инициировать тэги
		$('.tags-cont').tagit(tagitOptions);
		
		//submit
		$(".stopwords-form").submit(function(e) {
			e.preventDefault();
			var $tagsWidget=$(".stopwords-form .syns-area");
			var allSyns=gatherSyns($tagsWidget);
			allSyns=allSyns.join(" ");
			console.log(allSyns);
			$.ajax({
				type:"POST",
				url:"/sphinx-wordforms/stopwords/",
				data:{
					text:allSyns
				},
				dataType:"json",
				success:function(res) {
					if(!res.errors) {
					} else {
						alert("Ошибка записи");
					}
				}
			});
		});
	})
});

