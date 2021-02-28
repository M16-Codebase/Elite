var linkApi;

$(function () {
    require(['ui', 'editContent', 'editor', 'message'], function(ui, editContent, editor, message) {
    linkApi = {
        private : false,
        url: '/supportSeoLinks/',
        edit: function(params, callBack) {
            callBack = callBack || this.ef();
            params = params || {};
            this.private = true;
            var action = 'edit';
            this.execute(action, params, callBack);
        },
        add: function(params, callBack) {
            callBack = callBack || this.ef();
            params = params || {};
            this.private = true;
            var action = 'add';
            this.execute(action, params, callBack);
        },
        prepareUrl: function (method) {
            //var method = method || '';
            //return this.url + method;
            return this.url;
        },
        execute: function(method, params, callBack) {
            if (this.private === false) {
                return false;
            }
            callBack = callBack || this.ef();
            params = params || {};
            params = params || '';
            var data = {};
            $.extend( data, params );
            var url = this.prepareUrl(method);
            $.ajax({
                url: url,
                type: "GET",
                data: data,
                error: function(xhr, error){
                    console.dir(xhr); console.dir(error);
                },
                success: function(data) {
                    callBack(data);
                }
            });
        },
        ef: function () {}
    };



    var wblock = $("#wblock");
    var modalWindow = $('#add_link'),
        items = $('#items');

    var _modal = modal.init(modalWindow,
        {
            buttons: [
                {
                    text: "Ok",
                    icon: "ui-icon-heart",
                    click: saveLink
                }
            ]
        }
    );

    _modal.bind('close', clearModal);

    var bedNums = $('#bed_nums'),
        distList = $('#dist_list'),
        catList = $('#cat_list'),
        bedNum = '',
        selDist = '',
        catalog = '',
        href = $('#href');

    bedNums.on('change', function(){
        bedNum = $(this).val();
        createLink(catalog, bedNum, selDist);
    });
    distList.on('change', function(){
        selDist = $(this).val();
        createLink(catalog, bedNum, selDist);
    });
    catList.on('change', function(){
        catalog = $(this).val();
        createLink(catalog, bedNum, selDist);
    });

    function createLink(catalog, bedNum, selDist){
        bedNum = bedNum || '';
        selDist = selDist || '';
        catalog = catalog || '';
        var link = '/';

        if (catalog !== '') {
            link += catalog + '/';
        }
        if (selDist !== '') {
            link += selDist;
        }
        if (bedNum !== '') {
            var glue = '';
            if (selDist !== '') {
                glue = '__';
            }
            link += glue + bedNum + '/';
        }
        href.val(link);
    }

    $('#items').on('click', '.action-button.action-edit', function(e) {
        var parent = $(this).parent('.wblock');
        var id = parent.data('item_id');
        var href = parent.data('href');
        var text = parent.data('text');
        var work = parent.data('work');

        e.preventDefault();
        modalWindow.find('input[name=info]').attr('data-method', 'edit').attr('data-id', id)
            .attr('data-href', href).attr('data-text', text).attr('data-work', work);

        modalWindow.find("#href").val(href);
        modalWindow.find("#text").val(text);
        modalWindow.find("#work").prop("checked", work);

        editLink();
    });

        $('#items').on('click', '.action-button.action-add', function(e) {
        modalWindow.find('input[name=info]').attr('data-method', 'add');
        e.preventDefault();
        editLink();
    });

    function editLink()
    {
        _modal.open();
    }



    function saveLink()
    {
        var method = modalWindow.find('input[name=info]').attr('data-method');

        var href = modalWindow.find("#href").val();
        var text = modalWindow.find("#text").val();
        var work = modalWindow.find("#work").val();

        if (work === 'on') { work = 1; }
        else { work = 0; }

        if (method === 'edit') {
            var id = modalWindow.find('input[name=info]').attr('data-id');

            linkApi.edit({'id': id, 'href': href, 'text' :text, 'work' : work, 'method' :method}, function(result) {
                if (result !== '') {
                    result = JSON.parse(result);
                    items.empty().html(result.content);
                }
                console.log(result.content);
            });
        } else if (method === 'add') {
            linkApi.add({'href': href, 'text' :text, 'work' : work, 'method' :method}, function(result) {
                if (result !== '') {
                    result = JSON.parse(result);
                    items.empty().html(result.content);
                }
                console.log(result.content);
            });
        }
        _modal.close();
    }
    function clearModal() {
        modalWindow.find('input[name=info]').attr('data-method', '').attr('data-id', '')
            .attr('data-href', '').attr('data-text', '').attr('data-work', '');

        modalWindow.find("#href").val('');
        modalWindow.find("#text").val('');
        modalWindow.find("#work").prop("checked", "false");

        $('#dist_list option[value=""]').attr('selected','selected');
        $('#bed_nums option[value=""]').attr('selected','selected');
        $('#cat_list option[value=""]').attr('selected','selected');
    }
});
});
