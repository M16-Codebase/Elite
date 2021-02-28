var FILTER;

(function () {
    FILTER = {
        /* filter vars */
        /* чтобы не делать лишних http запросов лучше выполним все что нужно на месте */
        bedRooms : {
            1 : ['odnokomnatnye_kvartiry', 'odnokomnatnyye_kvartiry'],
            2 : ['dvuhkomnatnye_kvartiry', 'dvukhkomnatnyye_kvartiry', 'dvukhkomnatnye_kvartiry', 'dvuhkomnatnyye_kvartiry' ],
            3 : ['trehkomnatnye_kvartiry', 'trehkomnatnyye_kvartiry', 'trekhkomnatnyye_kvartiry', 'trekhkomnatnye_kvartiry'],
            4 : ['chetyrehkomnatnye_kvartiry', 'chetyrekhkomnatnyye_kvartiry', 'chetyrekhkomnatnyye_kvartiry', 'chetyrehkomnatnyye_kvartiry'],
            5 : ['pyatikomnatnye_kvartiry', 'pyatikomnatnyye_kvartiry'],
        },

        AREA : 'area_all',
        PRICE : 'close_price',
        BED_NUMBER : 'bed_number',

        intervalParams : {
            'price': 'close_price',
            'area': 'area_all'
        },

        isFilterRequest : false,

        parseFiterParams : function(filterGlobalParam) {
            var filterGlobalParam = filterGlobalParam || '',
                returnParams = [],
                bedNumVals = [];
            if (filterGlobalParam === '') {
                return false;
            }
            var countPositiveIterates = 0;

            var params = filterGlobalParam.split('__'),
                bedRoomsParamIsFinded = false;

            _.each(params, function(param){
                _.each(FILTER.bedRooms, function(variant, value) {
                    //if (!bedRoomsParamIsFinded) {
                        if (_.contains(variant, param))  {
                            bedNumVals.push(value);
                            bedRoomsParamIsFinded = true;
                            countPositiveIterates++;
                        }
                    //}
                });
                returnParams[FILTER.BED_NUMBER] = bedNumVals;


                _.each(FILTER.intervalParams, function(value, key) {
                    var ps = param.split('_');
                    if (ps[0] === key) {
                        var vs = ps[1].split('-');
                        if (vs.length == 2 && Number(vs[1]) > Number(vs[0])) {
                            returnParams[value] = {
                                'min' : vs[0],
                                'max' : vs[1]
                            };
                            countPositiveIterates ++;
                        }
                    }
                });
            });

            if (countPositiveIterates === 0) {
                return false;
            } else {
                FILTER.isFilterRequest = true;
            }
            return returnParams;
        },

        createUrlString: function(filterStr) {
            var filterStr = filterStr || '',
                MIN = 'min', MAX = 'max', url = [], newUrl = [],
                areaVals = [], priceVals = [], AREA = 'area',
                PRICE = 'price';

            if (filterStr === '') {
                return filterStr;
            }
            filterStr = decodeURI(filterStr);

            console.log(filterStr);
			//console.log(cityDistricts);
            var bn=0, dd=0;
            var filterPars = filterStr.replace(/\[|\]/g, ' ').split('&');
            _.each(filterPars, function (item) {
                var intVal = parseInt(item.replace(/\D+/g,""),10);
                if (/bed_number/.test(item)) {
                    url.push(FILTER.bedRooms[intVal][0]);bn++;
                } else if (/area_all/.test(item)) {
                    areaVals.push(intVal);
                    if (!_.contains(url, AREA)) {
                        url.push(AREA);
                    }
                } else if (/close_price/.test(item)) {
                    priceVals.push(intVal);
                    if (!_.contains(url, PRICE)) {
                        url.push(PRICE);
                    }
                } else if (/district/.test(item)) {
					//console.log(cityDistricts);
					//console.log(intVal);
					if(!!!cityDistricts[intVal]){
						console.log(cityDistricts);
						cityDistricts={34: "moskovskij-rajon", 35: "nevskij-rajon", 36: "petrogradskij-ra", 37: "petrodvortsovyj-", 38: "primorskij-rajon", 39: "pushkinskij-rajo", 40: "frunzenskij-rajo", 41: "tsentraljnyj-raj", 87: "admiraltejskij-r", 88: "vasileostrovskij", 89: "vyborgskij-rajon", 90: "kalininskij-rajo", 91: "kirovskij-rajon", 92: "kolpinskij-rajon", 93: "krasnogvardejski", 94: "krasnoseljskij-r", 95: "kronshtadtskij-r", 96: "kurortnyj-rajon", 103: "krestovskij-ostrov", 104: "zolotoj-treugolj"};
						console.log(cityDistricts);
					}
                    var d = cityDistricts[intVal].replace(/[\s]/g, '_');
                    url.push(FILTER.translate(d));dd++;
                }
            });

            _.each(url, function(item) {
                if (item === AREA) {
                    item = item + '_' + areaVals.join('-')
                } else if (item === PRICE) {
                    item = item + '_' + priceVals.join('-')
                }
                newUrl.push(item);
            });
            if (bn > 1 || dd > 1) {
                return false;
            }
            return newUrl.join('__');
        },

        /**
         * принимает uri и возвращает uri без ФИЛЬТР ПАРАМЕТРА
         * @param uri
         */
        cleanUri : function (uri) {
            var uri = uri || '',
                uriStr = uri;

            if (uri === '') {
                return uri;
            }
            uri = _.compact(uri.split('/'));

            var key = uri.length - 1,
                lastParam = uri[key],
                isFilter = false;

            _.each(FILTER.bedRooms, function(variant) {
                _.each(variant, function(value) {
                    var re = new RegExp(value);
                    if (re.test(lastParam)) {
                        isFilter = true;
                    }
                });
            });

            _.each(FILTER.intervalParams, function(item, value) {
                var re = new RegExp(value);
                if (re.test(lastParam)) {
                    isFilter = true;
                }
            });

            if (typeof cityDistricts !== 'undefined') {
				//console.log(cityDistricts);
                _.each(cityDistricts, function(item, value) {
                    var re = new RegExp(item);
                    if (re.test(lastParam)) {
                        isFilter = true;
                    }
                });
            }
/*
            if (/rayon/.test(lastParam)) {
                isFilter = true;
            }
*/
            if (isFilter) {
                uri.splice(key, 1);
                return '/' + uri.join('/') + '/';
            } else {
                return uriStr;
            }
        },

        translate: function (str){

            var arr={'а':'a', 'б':'b', 'в':'v', 'г':'g', 'д':'d', 'е':'e', 'ж':'g', 'з':'z', 'и':'i', 'й':'y', 'к':'k', 'л':'l', 'м':'m', 'н':'n', 'о':'o', 'п':'p', 'р':'r', 'с':'s', 'т':'t', 'у':'u', 'ф':'f', 'ы':'y', 'э':'e', 'А':'A', 'Б':'B', 'В':'V', 'Г':'G', 'Д':'D', 'Е':'E', 'Ж':'G', 'З':'Z', 'И':'I', 'Й':'Y', 'К':'K', 'Л':'L', 'М':'M', 'Н':'N', 'О':'O', 'П':'P', 'Р':'R', 'С':'S', 'Т':'T', 'У':'U', 'Ф':'F', 'Ы':'I', 'Э':'E', 'ё':'yo', 'х':'h', 'ц':'ts', 'ч':'ch', 'ш':'sh', 'щ':'shch', 'ъ':'', 'ь':'', 'ю':'yu', 'я':'ya', 'Ё':'YO', 'Х':'H', 'Ц':'C', 'Ч':'CH', 'Ш':'SH', 'Щ':'SHCH', 'Ъ':'', 'Ь':'',
                'Ю':'YU', 'Я':'YA', '\ ':'\_'};
            var replacer=function(a){return arr[a]||a};
            return str.replace('ь', '').replace(/[А-яёЁЬь]/g,replacer)
        }


};
})();

$(document).ready(function() {

    var $filter = $('.filter'),

        _RESALE = 'resale',
		_ARENDA = 'arenda',
        _RESIDENTIAL = 'residential',
        _REAL_ESTATE = 'real-estate',
        __APARTMENTS = 'apartments',

        curUrl = window.location.pathname,
        urlParams = _.compact(curUrl.split('/')),
        curSector = urlParams[0],
        lastParam = _.last(urlParams),

        filterGlobalParam = '',
        filterParams,

        M_CURRENT = 'm-current'

        ;
		console.log(curUrl);

    if (curSector == _RESALE || curSector == _RESIDENTIAL || curSector == _REAL_ESTATE || curSector == _ARENDA) {


        // район
        var filterData = $(document).find('#filter_data');
        if (filterData.length !== 0) {
            var fData = $(filterData).data();

            if ('district' in fData) {
                //console.dir(fData['district']);

                $filter.find("#district" + fData['district']).attr('checked', true);
            }
        }

        // проверяем предварительно последний параметр, не явл ли он
        // названием раздела и т.д.
        if (curSector == _REAL_ESTATE) {
            if (_.contains(urlParams, __APARTMENTS)) {
                if (lastParam != __APARTMENTS) {
                    filterGlobalParam = lastParam;
                }
            }
        }

        if (curSector != lastParam) {
            filterGlobalParam = lastParam;
        }

        // после пробуем распарсить его и разглядеть элементы фильтрации
        filterParams = FILTER.parseFiterParams(filterGlobalParam)
        if (!filterParams) {
            return;
        }

        var bedNumInputn = $filter.find('label.m-bedroom input[type=checkbox]');
        //console.log(filterParams);
        var districtSeoTextBlock = $('#district_seo_text');

        // кол-во комнат в фильтре
        _.each(bedNumInputn, function(input) {
            if (_.contains(filterParams[FILTER.BED_NUMBER], input.value)) {
                $(input).attr('checked', true);
            }
        });




        $filter.on('change', 'INPUT[type="checkbox"], INPUT[type="radio"]', function() {
            if (districtSeoTextBlock.is(':visible')) {
                //districtSeoTextBlock.hide();
            }
        });
    }



});

