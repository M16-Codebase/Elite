$(function() {

  require(['ui','scrollEvent'], function(ui, scrollEvent) {


    (function(){

      scrollEvent({
        '.service-sc': {
         start: 0,
         inActive: function() {
            appear(this);
         }
        },

        '.service-sc10, .service-sc11':{
          start: 100,
          inActive: function() {
            appear(this);
          }
        },

        '.service-sc5, .service-sc6, .service-sc11, .service-sc12':{
          start: 150,
          inActive: function() {
            appear(this);
          }
        },

        '.service-sc4, .service-sc7':{
          start: 200,
          inActive: function() {
            appear(this);
          }
        }

      });

      function appear(el){
        $(el).addClass('appeared');
        $(el).prevAll().addClass('appeared');
      }

    })();






	});

});