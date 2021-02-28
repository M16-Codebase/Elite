$(function() {

    //variables
    var editTheme=$("#edit-theme-form");
    var saveEditTheme=$(".edit-theme");

    //events
    saveEditTheme.click(function() {
        editTheme.ajaxSubmit({
            dataType: 'json',
            success:function(res) {
                if (!res.errors) {
                    window.location = res.data.url;
                }
            }
        });
    });
});