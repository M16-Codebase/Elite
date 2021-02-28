define(function() {
	var gatherSyns=function($widget) {
		var arr=[];
		var $labels=$widget.find(".tagit-label");
		$labels.each(function() {
			var syn=$(this).text();
			arr.push(syn);
		});
		var possibleTag=$widget.find("tester").text();
		if(possibleTag !== arr[0] && possibleTag !== "") {
			arr.push(possibleTag);
		}
		return arr;
	};
	
	return gatherSyns;
});

