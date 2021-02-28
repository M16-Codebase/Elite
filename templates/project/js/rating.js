/*$(document).ready(function() {
    var rating = $(".complex-rating");
    var objectId = rating.data('object');

    rating.stars({
        color: '#b59974',
        click: function(i) {
            $.get(
                "/ajaxtest.php",
                {
                    param1: "param1",
                    param2: 2
                },
                onAjaxSuccess
            );
            //alert("Star " + i + " selected.");
        },
        value:4
    });
});
*/

$(function() {
    require(['ui'], function (ui) {
        var rating = $(".rating");
        var url= '/ratings/set'; var method= 'GET';
        var cnt = rating.attr('data-markscount');
        var max = rating.attr('data-maxmark');
        var rat = rating.attr('data-rating');
        var rw = $('.complex-rating-wrapper');
        var ri = rw.find('#rating-info');
        var readOnly = rating.attr('data-voted');
        //var rating = ui.ratings.el;
        rating.raty({//
            score: function() {
                return $(this).attr('data-rating');
            },
            starType : 'i',
            half     : false,
            readOnly: !!(Number(readOnly)),
            hints: false,
            click: function(score, evt) {
                score = Math.round(score * 100) / 100;
                $.get(
                    url,
                    {
                        index: score,
                        objId: $(this).attr('data-object')
                    },
                    function (res) {
                        console.dir(res);
                        $(this).attr('data-rating', res['data']);
                        cnt = Number(cnt) +1;
                        $(this).attr('data-markscount', cnt);
                        ri.empty().html( cnt + ' оценок, средняя ' + $(this).attr('data-rating') + ' из ' + max
                            + ', вы уже поставили оценку');
                    }
                );
            }
        });
    });

});
