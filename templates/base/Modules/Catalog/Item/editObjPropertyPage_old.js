$(function(){
    $('.actions-panel .action-save A').click(function(evt){
        evt.preventDefault();
        $('#edit-prop-form').submit();
    });

    $('#edit-prop-form').submit(function(evt){
        evt.preventDefault();
        $(this).ajaxSubmit({
            dataType: 'json',
            success:function(res){
                if (res.errors === null){
                    $('#obj-edit-block').html(res.content);
                }
            }
        })
    });
});