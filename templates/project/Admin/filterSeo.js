$(function() {
    require(['ui', 'editContent', 'editor', 'message'], function(ui, editContent, editor, message) {

    dialog = $( "#add_seo_item_type" ).dialog({
        autoOpen: false,
        height: 500,
        width: 400,
        modal: true,
        buttons: {
            "Добавить": function() {
                $( this ).dialog( "close" );
            },
            "Отмена": function() {
                $( this ).dialog( "close" );
            }
        }
    });

    form = dialog.find( "form" ).on( "submit", function( event ) {
        event.preventDefault();
        //addUser();
    });

    $( "#seo_item_types" ).find('a.action-add').button().on( "click", function() {
        dialog.dialog( "open" );
    });

    $('.actions-panel .action-add').click(function() {
        editContent.open({
            form: '.add-seo-item',
            clearform: true,
            success: function(res) {
                if (res.errors === null){
                    $('.white-body').html(res.content);
                    $(window).resize();
                    editContent.close();
                } else {
                    message.errors(res);
                }
            }
        });
    });

        $('.seo-filter-item-wrap').on('click','.action-edit', function() {
            var target = $(this);
            var parent = target.closest(".white-block-row");
            var itemInfo = parent.find('.item-info');
            var id = itemInfo.data('id');
            var sector = itemInfo.data('sitesector');
            editContent.open({
                getform: '/seo/editFilterSeoItem/',
                getformtype: 'json',
                getformdata: {
                    id: id,
                    sector: sector
                },
                getformmethod: 'post',
                success: function(res) {
                    console.dir($res);
                    /*if (res.errors === null){
                        $('.white-body').html(res.content);
                        $(window).resize();
                        editContent.close();
                    } else {
                        message.errors(res);
                    }*/
                }
            });
        });

    });});