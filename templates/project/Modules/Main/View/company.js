$(function() {
	require(['ui', 'tiles', 'scrollEvent'], function(ui, tiles, scrollEvent) {


    (function(){

      var swiperEl = '.company-sc2-swiper';

      if( $(swiperEl).find('.swiper-slide').length > 1 ) {

        var swiper = new Swiper(swiperEl + ' .swiper-container', {
          pagination: swiperEl + ' .swiper-pagination',
          paginationClickable: swiperEl + ' .swiper-pagination',
          nextButton: swiperEl + ' .swiper-button-next',
          prevButton: swiperEl + ' .swiper-button-prev',
          speed: 500,
          onInit: function(){

            $(swiperEl)
              .find('.swiper-button-next, .swiper-button-prev')
            .show();

          },

          onSlideChangeStart: function(swiper){

            var prevIndex = swiper.previousIndex;
            var activeIndex = swiper.activeIndex;

            $(swiperEl + ' .swiper-slide')
              .eq(prevIndex).find('.company-sc2-quote')
                .stop(true,true).fadeOut(200);

            $(swiperEl + ' .swiper-slide')
              .eq(activeIndex).find('.company-sc2-quote')
                .stop(true,true).fadeIn(200);

          }

        });

      }

    })();


    (function(){

      var swiperEl = '.company-sc6-swiper';

      var $current = $(swiperEl + ' .swiper-counter-active');
      var $length = $(swiperEl + ' .swiper-counter-length');

      var swiper = new Swiper(swiperEl + ' .swiper-container', {
          pagination: swiperEl + ' .swiper-pagination',
          paginationClickable: swiperEl + ' .swiper-pagination',
          nextButton: swiperEl + ' .swiper-button-next',
          prevButton: swiperEl + ' .swiper-button-prev',
          speed: 600,
          effect:'coverflow',
          coverflow: {
            rotate: 60,
            stretch: 0,
            depth: 300,
            modifier: 2,
            slideShadows : false
          },
          onInit: function(){
            $current.html( 1 );
            $length.html( $(swiperEl + ' .swiper-slide').length );
            $(swiperEl + ' .swiper-counter-slash').show();
          },
          onSlideChangeStart: function(swiper){
            $current.html( swiper.activeIndex + 1 );
          }
        });

    })();



    (function(){

      $('.art-tiles').each(function() {
        var cont = $(this);

        tiles(cont, {
          tiles: [

            {
              el: '.art-1',
              pos: [1,1, 1,2]
            },
            {
              el: '.art-2',
              pos: [2,1, 2,2]
            },
            {
              el: '.art-3',
              pos: [3,1, 4,1]
            },
            {
              el: '.art-4',
              pos: [3,2, 4,2]
            },
            {
              el: '.art-5',
              pos: [1,3, 2,4]
            },
            {
              el: '.art-6',
              pos: [3,3, 3,4]
            },
            {
              el: '.art-7',
              pos: [4,3, 4,4]
            }
          ],
          size: [50, 900],
          space: 0,
          cols: 4
        });


      });

    })();


    (function(){

      scrollEvent({
        '.company-sc1': {
          start: 0,
          inActive: function() {
            appear(this);
          }
        },
        '.company-sc2, .company-sc3, .company-sc3 .company-article_group, .company-sc4, .company-sc5 .company-article_group, .company-sc6, .company-sc7, .company-sc8, .company-sc9, .company-sc10': {
          start: 150,
          inActive: function() {
            appear(this);
          }
        }
      });

      function appear(el){
        addClassToEls(el,'appeared');
        setTimeout(function(){addClassToEls(el,'no-delay'); },2000);
      }

      function addClassToEls(el, className){
        $(el).addClass(className);
        $(el).prevAll().addClass(className);
        $(el).closest('.company-sc').prevAll().addClass(className);
        $(el).prevAll().find('.company-article_group').addClass(className);
      }

    })();





	});
});