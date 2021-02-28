/* полный копипаст */
$(function(){
    $('.popup_edit-review').delegate('.action_button', 'click', function(){
        var id = $(this).closest('.buttons').data('review_id');
        var status = $(this).data('status');
        $.post('/catalog-admin/changeReviewStatus/', {id: id, change_status: status}, function(result){
            $('#review-' + id).replaceWith(result);
            $('.popup_edit-review').dialog('close');
        });
        return false;
    });
	
	$('.reviews').delegate('.edit_rev', 'click', function() {
		var review = $(this).closest('TR');
        var id = review.data('review_id');
		$.post('/catalog-admin/reviewPopup/', {id: id}, function(result) {
            $('.popup_edit-review').html(result);
			$('.button').button();
			$('.popup_edit-review').dialog({
				title: 'Отзыв о ' + review.find('.title').text() + ' от ' + review.find('.date').text(),
				width: 840
			}).dialog('open');
        });
    });
});