$(function()
{
    if($(".filter").length)
    {
        var word = "фильтр";
        var lan = $("#mobile_header .segment span").text();

        if(lan == "РУ"){ word = "filter"; }

        // <span></span><span></span><span></span>
        $('<div class="menu_one" id="filtr_div"><div class="zag_filtr"><div class="burgerBtn" id="mob_filtr"></div><div class="zag">'+word+'</div></div></div>').insertBefore(".filter");

        $('.zag_filtr').on('click', function() {
            $('#mob_filtr').toggleClass("active");
            $('.filter').slideToggle();
        });
    }
    if($(".main-menu").length)
    {
        var word = "характеристики";
        var lan = $("#mobile_header .segment span").text();

        if(lan == "РУ"){ word = "features"; }

        $('<div class="menu_one" id="main_menu_div"><div class="zag_main_menu"><div class="burgerBtn" id="mob_main_menu"><span></span><span></span><span></span></div><div class="zag">'+word+'</div></div></div>').insertBefore(".main-menu");

        $('.zag_main_menu').on('click', function() {
            $('#mob_main_menu').toggleClass("active");
            $('.main-menu').slideToggle();
        });
    }
    $('.zag_one').on('click', function() {
        $('#mob_one').toggleClass("active");
        $('#menu_one').slideToggle();
    });

    $('.zag_two').on('click', function() {
        $('#mob_two').toggleClass("active");
        $('#menu_two').slideToggle();
    });

    $('#mob_all').on('click', function() {
        $('#mob_all').toggleClass("active");
        $('#mm_one').slideToggle();
        $('#mm_two').slideToggle();
    });

});