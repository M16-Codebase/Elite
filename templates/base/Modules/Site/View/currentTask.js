$(function(){
    var timerUserRedirect = setInterval(function() {
        $.post('/site/getCurrentTask/', {}, function (res){
            $('.inner').html(res);
        });
    }, 1000);
});