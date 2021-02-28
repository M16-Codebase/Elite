$('#expanded-panel').accordion({
    collapsible: true,
    autoHeight: false,
    navigation: true
});
$('#tabs').tabs();

var modal;

$(function() {

    modal = {
        dialog: '',
        isOpen: false,
        options: {
            height: 400,
            width: 400,
            modal: true,
        },
        init: function(elem, options) {
            this.dialog = $(elem);
            $.extend( this.options, options );
            return this;
        },
        open: function() {
            this.dialog.dialog(this.options);
            //if (typeof this.dialog === 'object' && this.dialog.dialog('isOpen') === false) {}
            return true;
        },
        close: function() {
            this.dialog.dialog("close");
            //if (this.dialog.dialog('isOpen') === true) {}
        },
        bind: function(eventName, callback) {
            callback = callback || ef();
            this.dialog.bind('dialog' + eventName,callback)
        },
        ef: function () {}
    }

});
