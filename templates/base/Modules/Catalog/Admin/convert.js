$(function(){
    $('.convert-form').each(function(){
        var form = $(this);
		var numbers = $('TEXTAREA[name="numbers"]', form);
		var newNumbers = $('TEXTAREA[name="ens_numbers"]', form);
		var convertFrom = $('SELECT[name="convertFrom"]', form);
		var convertTo = $('SELECT[name="convertTo"]', form);
		var checkNum = function() {
			var from = convertFrom.find(':selected').text() || '*';
			var to = convertTo.find(':selected').text() || '*';
			$('.convert-numbers .convertFrom', form).text(from);
			$('.convert-numbers .convertTo', form).text(to);
		};
		$('SELECT', form).change(checkNum);
		checkNum();
		form.submit(function() {
			$.getJSON('/catalog-admin/convertNum/', {
				numbers: numbers.val().replace(/\s/g,''), 
				convertFrom: convertFrom.val(), 
				convertTo: convertTo.val()
			}, function(result) {		
				if (result){
					if ('push' in result && !result.length) {
						newNumbers.val('—');
						return;
					}
					var str = '';
					var num = numbers.val().split(',');
					for (var i = 0; i < num.length; i++){
						var key = num[i];
						if (i) str += ', ';
						if (result[key]) {
							str += result[key]['code'];
						} else {
							str += '—';
						}
					}
					newNumbers.val(str);
				}
			});
			return false;
		});		
    });
});