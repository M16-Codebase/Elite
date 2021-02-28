define([], function() {
	var defaultSelector = '.tiles';
	var contClass = 'tiles-cont';
	var innerClass = 'tiles-inner';
	var initedClass = 'tiles-active';
	var stateClass = 'tiles-state';
	var tileClass = 'tile';
	var activeTileClass = 'active-tile';
	var hiddenTileClass = 'hidden-tile';
	var attrStr = 'tiles-id';
	
	var defaultOpt = {
		cols: [1, 20],
		minRows: 1,
		size: ['20%', '50%'],
		fixedHeight: false,
		heightRatio: 1,
		space: [0, '10%'],
		edgeSpace: [0, 0, 0, 0],
		stateTitle: '',
		tiles: [],
		responsive: {},
		onInit: function() {},
		onChangeState: function() {}
	};
	
	var canBePercent = ['size', 'space', 'edgeSpace'];
	var maxResolution = '9999999';
	var currentSettings = {};
	var initedTiles = [];
	var idCounter = 0;
	
	var initTiles = function(els, userOpt) {
		if (els && (typeof els === 'string' || els.tagName || els.jquery)) {
			els = $(els);
		} else {
			if (typeof els === 'object') userOpt = els;
			els = $(defaultSelector);
			if (initedTiles.length) els = els.add(initedTiles);
		}
		if (!els.length) return;
		
		els.each(function() {
			var el = $(this);
			var create = false;
			var id = el.data(attrStr);
			if (!id) {
				create = true;
				id = ++idCounter;
				el.data(attrStr, id);
				el.addClass(contClass + ' ' + attrStr + id);
				if (!initedTiles.length) {
					initedTiles = el;
				} else {
					initedTiles = initedTiles.add(el);
				}
			}
			
			var inner = $('> .' + innerClass, el);
			if (!inner.length) {
				el.wrapInner('<div class="' + innerClass + '"></div>');
				inner = $('> .' + innerClass, el);
			}
			inner.css({position: 'relative'});
			
			var opt = create? {} : currentSettings[id];
			userOpt = (typeof userOpt === 'object')? userOpt : {};
			for (var o in defaultOpt) {
				if (o === 'responsive') continue;
				if (typeof userOpt[o] !== 'undefined') {
					opt[o] = userOpt[o];
				} else {
					opt[o] = create? defaultOpt[o] : currentSettings[id][o];
					if (typeof opt[o] === 'object') opt[o] = _.clone(opt[o]);
				}
			}
			validate(opt, el);
			
			if (!opt.responsive) opt.responsive = {};
			opt.responsive[maxResolution] = opt;
			if (userOpt.responsive) {
				for (var r in userOpt.responsive) {
					if (!opt.responsive[r]) opt.responsive[r] = {};
					for (var o in defaultOpt) {
						if (o === 'responsive') continue;
						if (typeof userOpt.responsive[r][o] !== 'undefined') {
							opt.responsive[r][o] = userOpt.responsive[r][o];
						} else if (o !== 'stateTitle') {
							opt.responsive[r][o] = opt[o];
							if (typeof opt[o] === 'object') opt.responsive[r][o] = _.clone(opt[o]);
							if (canBePercent.indexOf(o)) {
								for (var i in opt.responsive[r][o]) {
									if (typeof opt.responsive[r][o][i] === 'string') {
										opt.responsive[r][o][i] += '%';
									}
								}
							}
						}
					}
					validate(opt.responsive[r], el);
				}
			}
			
			currentSettings[id] = opt;
		});
		buildTiles(els);
	};
	
	initTiles.getTiles = function(el) {
		el = $(el || defaultSelector).first();
		var id = el.data(attrStr);
		if (!el.length || !id) return false;
		var opt = currentSettings[id];
		var tiles = [];
		for (var i in opt.tiles) {
			if (!tiles.length) tiles = opt.tiles[i].el;
			else tiles = tiles.add(opt.tiles[i].el);
		}
		return tiles;
	};
	
	initTiles.addTiles = function(el, tiles, callback) {
		if (el && (typeof el === 'string' || el.tagName || el.jquery)) {
			el = $(el);
		} else {
			if (typeof el === 'object') tiles = el;
			if (typeof el === 'function') callback = el;
			else if (typeof tiles === 'function') callback = tiles;
			el = $(defaultSelector).first();
		}
		var id = el.data(attrStr);
		if (!el.length || !id) return false;
		if (typeof tiles !== 'object') return false;
		if (!(tiles instanceof Array)) tiles = [tiles];
		var opt = currentSettings[id];
		for (var i in tiles) {
			opt.tiles.push(tiles[i]);
			for (var r in opt.responsive) {
				if (!tiles[i].responsive || !tiles[i].responsive[r]) {
					opt.responsive[r].tiles.push(tiles[i]);
				}
			}
			if (tiles[i].responsive) {
				for (var r in tiles[i].responsive) {
					if (!opt.responsive[r]) {
						opt.responsive[r] = opt;
					}
					var tileOpt = $.extend({}, tiles[i], tiles[i].responsive[r]);
					opt.responsive[r].tiles.push(tileOpt);
				}
			}
		}
		initTiles(el, opt);
		callback = callback || function() {};
		callback.call(el);
	};
	
	initTiles.removeTiles = function(els, tiles, callback) {
		if (els && (typeof els === 'string' || els.tagName || els.jquery)) {
			els = $(els);
		} else {
			if (typeof els === 'object') tiles = els;
			if (typeof els === 'function') callback = els;
			else if (typeof tiles === 'function') callback = tiles;
			els = $(defaultSelector);
		}
		var findAndRemoveTiles = function(optTiles, tiles) {
			for (var i in optTiles) {
				if (optTiles[i] && optTiles[i].el.is(tiles)) {
					optTiles[i].el.remove();
					optTiles[i] = null;
				}
			}
			return _.compact(optTiles);
		};
		els.each(function() {
			var el = $(this);
			var id = el.data(attrStr);
			if (!id) return;
			var opt = currentSettings[id];
			if (typeof tiles === 'string') {
				if (!el.find(tiles).length) return;
				tiles = el.find(tiles);
			} else if (tiles.tagName) {
				tiles = $(tiles);
			} else if (tiles instanceof Array) {
				var t = [];
				for (var i in tiles) {
					if (typeof tiles[i] === 'string' || tiles[i].tagName || tiles[i].jquery) {
						if (!t.length) t = $(tiles[i]);
						else t = t.add($(tiles[i]));
					}
				}
				tiles = t;
			} else if (!tiles.jquery) return;
			if (!tiles.length) return;
			opt.tiles = findAndRemoveTiles(opt.tiles, tiles);
			for (var r in opt.responsive) {
				opt.responsive[r].tiles = findAndRemoveTiles(opt.responsive[r].tiles, tiles);
			}
			initTiles(el, opt);
		});
		callback = callback || function() {};
		callback.call(els);
	};
	
	var buildTiles = function(els) {
		els = els || initedTiles;
		if (!els.length) return;
		var wWidth = $(window).width();
		els.each(function() {
			var el = $(this);
			var w = el.width();
			var id = el.data(attrStr);
			if (!id || !w) return;
			
			// берём настройки для текущего разрешения экрана
			var tilesOpt = currentSettings[id];
			var resolution = maxResolution;
			var oldResolution = el.data('resolution');
			var minR = parseInt(resolution);
			for (var r in tilesOpt.responsive) {
				r = parseInt(r);
				if (wWidth <= r && r < minR) {
					minR = r;
				}
			}
			var opt = tilesOpt.responsive[minR];
			resolution = minR;
			if (!oldResolution) {
				el.data('resolution', resolution);
				oldResolution = resolution;
			}
			if (resolution !== oldResolution) {
				el.data('resolution', resolution);
				opt.onChangeState.call(el, resolution, oldResolution);
			}
			var oldStateTitle = tilesOpt.responsive[oldResolution].stateTitle;
			el.removeClass(stateClass + '-' + (oldStateTitle || (String(oldResolution) === maxResolution? 'max' : oldResolution)))
				.addClass(stateClass + '-' + (opt.stateTitle || (String(resolution) === maxResolution? 'max' : resolution)));
			
			// преобразуем % в px
			var relativeOpt = {};
			for (var o in canBePercent) {
				relativeOpt[canBePercent[o]] = [];
				for (var i in opt[canBePercent[o]]) {
					if (typeof opt[canBePercent[o]][i] === 'string') {
						relativeOpt[canBePercent[o]][i] = w * parseInt(opt[canBePercent[o]][i]) / 100;
					} else {
						relativeOpt[canBePercent[o]][i] = opt[canBePercent[o]][i];
					}
				}
			}
			opt = $.extend({}, opt, relativeOpt);
			
			// считаем количество колонок
			var cols = ((w - opt.edgeSpace[1] - opt.edgeSpace[3] - opt.space[0]) / (opt.size[0] + opt.space[0])) >> 0;
			if (cols > opt.cols[1]) cols = opt.cols[1];
			else if (cols < opt.cols[0]) cols = opt.cols[0];
			if (cols > opt.tiles.length) cols = opt.tiles.length;
			
			// строим матрицу заполненных ячеек
			var matrix = [];
			var matrixLength = 0;
			var rows = opt.minRows;
			var rowStr = (new Array(cols + 1)).join('0');
			var checkRows = function() {
				matrix.length = rows;
				for (var y = 0; y < rows; y++) {
					if (!matrix[y]) matrix[y] = rowStr;
				}
			};
			var getFreeTile = function() {
				var pos = [];
				for (var y in matrix) {
					if (matrix[y].indexOf('0') >= 0) {
						pos = [matrix[y].indexOf('0') + 1, parseInt(y) + 1];
						break;
					}
				}
				if (!pos.length) {
					rows++;
					checkRows();
					pos = getFreeTile();
				}
				matrix[pos[1]-1] = matrix[pos[1]-1].substr(0, pos[0]-1) + '1' + matrix[pos[1]-1].substr(pos[0]);
				return pos;
			};
			checkRows();
			for (var i in opt.tiles) {
				var pos = opt.tiles[i].pos;
				if (!pos || !pos.length || pos.length < 2) continue;
				if (pos.length < 4) {
					pos = [pos[0], pos[1], pos[0], pos[1]];
				}
				if (rows < pos[3]) {
					rows = pos[3];
					checkRows();
				}
				if (pos[2] > matrixLength) {
					matrixLength = pos[2];
				}
				var length = pos[2] - pos[0] + 1;
				for (var y = pos[1]-1; y < pos[3]; y++) {
					matrix[y] = matrix[y].substr(0, pos[0]-1) + (new Array(length + 1)).join('1') + matrix[y].substr(pos[2]);
				}
			}
			if (matrixLength && cols !== matrixLength) {
				for (var y = 0; y < rows; y++) {
					if (cols > matrixLength) matrix[y] = matrix[y].substr(0, matrixLength);
					else matrix[y] += (new Array(matrixLength - matrix[y].length + 1)).join('0');
				}
				cols = matrixLength;
				if (cols > opt.cols[1]) cols = opt.cols[1];
				else if (cols < opt.cols[0]) cols = opt.cols[0];
			}
			
			// считаем размеры ячеек и отступов
			var inner = $('> .' + innerClass, el);
			$('> *', inner).removeClass(activeTileClass + ' ' + hiddenTileClass);
			var colSize = (w - opt.edgeSpace[1] - opt.edgeSpace[3] - opt.space[0] * (cols - 1)) / cols;
			if (colSize < opt.size[0]) colSize = opt.size[0];
			else if (colSize > opt.size[1]) colSize = opt.size[1];
			var spaceSize = (w - opt.edgeSpace[1] - opt.edgeSpace[3] - colSize * cols) / ((cols - 1) || 1);
			if (cols === 1) spaceSize = opt.space[0];
			else if (spaceSize < opt.space[0]) spaceSize = opt.space[0];
			else if (spaceSize > opt.space[1]) spaceSize = opt.space[1];
			var colHeight = opt.fixedHeight? opt.fixedHeight : colSize * opt.heightRatio;
			for (var i in opt.tiles) {
				var tile = opt.tiles[i].el;
				var pos = opt.tiles[i].pos || getFreeTile();
				var z = opt.tiles[i].z || (parseInt(i)+1);
				if (pos.length < 4) {
					pos = [pos[0], pos[1], pos[0], pos[1]];
				}
				var size = [pos[2] - pos[0] + 1, pos[3] - pos[1] + 1];
				tile.addClass(activeTileClass).css({
					left: opt.edgeSpace[1] + (colSize + spaceSize) * (pos[0] - 1),
					top: opt.edgeSpace[0] + (colHeight + spaceSize) * (pos[1] - 1),
					height: colHeight * size[1] + spaceSize * (size[1] - 1),
					width: colSize * size[0] + spaceSize * (size[0] - 1),
					display: 'block',
					zIndex: z
				});
			}
			$('> *:not(.' + activeTileClass + ')', inner).addClass(hiddenTileClass).css({display: 'none'});
			
			// задаём размеры контейнера
			var innerW = colSize*cols + spaceSize*(cols-1) + opt.edgeSpace[1] + opt.edgeSpace[3];
			inner.css({
				height: colHeight * rows + spaceSize * (rows-1) + opt.edgeSpace[0] + opt.edgeSpace[2]
			}).attr({
				'data-rows': rows,
				'data-cols': cols
			});
			if (w > innerW + 1) {
				inner.css({
					width: innerW,
					margin: '0 auto'
				});
			} else if (w < innerW - 1) {
				inner.css({
					width: innerW,
					margin: '0 -' + (innerW - w)/2 + 'px'
				});
			} else {
				inner.css({
					width: 'auto',
					margin: 0
				});
			}
			
			if (!el.data(initedClass)) {
				el.data(initedClass, true);
				el.addClass(initedClass);
				opt.onInit.call(el);
			}
		});
	};
	
	var validate = function(opt, el) {
		var inner = $('> .' + innerClass, el);
			
		if ('cols' in opt) {
			if (!(opt.cols instanceof Array)) {
				opt.cols = [opt.cols, opt.cols];
			}
			opt.cols = [parseInt(opt.cols[0]) || defaultOpt.cols[0], parseInt(opt.cols[1]) || defaultOpt.cols[1]];
		}

		if ('minRows' in opt) {
			opt.minRows = parseInt(opt.minRows) || defaultOpt.minRows;
		}

		if ('size' in opt) {
			if (!(opt.size instanceof Array)) {
				opt.size = [opt.size, opt.size];
			}
			for (var i = 0; i < 2; i++) {
				if (typeof opt.size[i] === 'string' && opt.size[i].match(/%$/i)) {
					opt.size[i] = opt.size[i].replace('%', '');
				} else {
					opt.size[i] = parseInt(opt.size[i]) || defaultOpt.size[i];
				}
			}
		}

		if ('space' in opt) {
			if (!(opt.space instanceof Array)) {
				opt.space = [opt.space, opt.space];
			}
			for (var i = 0; i < 2; i++) {
				if (typeof opt.space[i] === 'string' && opt.space[i].match(/%$/i)) {
					opt.space[i] = opt.space[i].replace('%', '');
				} else {
					opt.space[i] = parseInt(opt.space[i]) || defaultOpt.space[i];
				}
			}
		}
		
		if ('edgeSpace' in opt) {
			if (!(opt.edgeSpace instanceof Array)) {
				opt.edgeSpace = [opt.edgeSpace, opt.edgeSpace, opt.edgeSpace, opt.edgeSpace];
			} else if (opt.edgeSpace.length && opt.edgeSpace.length < 4) {
				if (opt.edgeSpace.length === 1) opt.edgeSpace = [opt.edgeSpace[0], opt.edgeSpace[0], opt.edgeSpace[0], opt.edgeSpace[0]];
				if (opt.edgeSpace.length === 2) opt.edgeSpace = [opt.edgeSpace[0], opt.edgeSpace[1], opt.edgeSpace[0], opt.edgeSpace[1]];
				if (opt.edgeSpace.length === 3) opt.edgeSpace = [opt.edgeSpace[0], opt.edgeSpace[1], opt.edgeSpace[2], opt.edgeSpace[1]];
			}
			for (var i = 0; i < 4; i++) {
				if (typeof opt.edgeSpace[i] === 'string' && opt.edgeSpace[i].match(/%$/i)) {
					opt.edgeSpace[i] = opt.edgeSpace[i].replace('%', '');
				} else {
					opt.edgeSpace[i] = parseInt(opt.edgeSpace[i]) || defaultOpt.edgeSpace[i];
				}
			}
		}
		
		if ('tiles' in opt) {
			if (!opt.tiles.length) {
				opt.tiles = [];
				$('> *', inner).each(function() {
					opt.tiles.push({el: $(this)});
				});
			}
			for (var i in opt.tiles) {
				var tile = opt.tiles[i];
				if (!tile.el) continue;
				if (typeof tile.el === 'string' || tile.el.tagName) {
					tile.el = $(tile.el);
				}
				if (!tile.el.length) {
					tile = null;
					continue;
				} else if (!el.find(tile.el).length) {
					inner.append(tile.el);
				}
				if (tile.pos) {
					var pos = tile.pos;
					if (!(pos instanceof Array)) {
						pos = [pos, pos];
					}
					for (var p in pos) {
						pos[p] = parseInt(pos[p]);
						if (!pos[p]) {
							pos = null;
							break;
						}
					}
					if (pos) {
						if (pos.length < 2) pos = null;
						else if (pos.length < 4) {
							pos = [pos[0], pos[1], pos[0], pos[1]];
						}
					}
				}
				tile.el.addClass(tileClass).css({
					position: 'absolute',
					display: 'block'
				});
			}
			opt.tiles = _.compact(opt.tiles);
		}
	};
	
	$(window).on('resize buildTiles', function () {
		buildTiles(null);
	});
	return initTiles;
});