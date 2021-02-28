$(function() {
	
	// выравнивание по высоте
	var heightItem = function() {
		var item = $('.viewed-result .catalog-item');
		var length = $('.viewed-result .catalog-item').length - 1;
		var maxHght = 0;
		var row = 0;
		var i = 0;
		item.each(function(n) {
			i++;
			if ($(this).innerHeight() > maxHght){
				maxHght = $(this).innerHeight();
			}
			if (row) row = row.add($(this));
			else row = $(this);
			if (i==5 || n==length) {
				$.each(row, function(){
					$(this).innerHeight(maxHght);
				});
				maxHght = 0;
				row = 0;
				i = 0;
			} 
		});			
	};
	heightItem();
		
});