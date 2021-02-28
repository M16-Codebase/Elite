define(['raphael'], function() {
	
	var generalOptions = {
		polyCont: '.poly-cont',
		paperCont: '.poly',
		svgClass: 'svg'
	};
	var polyOptions = {
		animation: 0,
		attr: {
			href: '',
			cursor: 'pointer',
			fill: '#fff',
			'fill-opacity': 1,
			stroke: '#000',
			'stroke-width': 1,
			'stroke-opacity': 1,
			'stroke-linejoin': 'round',
			opacity: 1
		},
		// hover: {attr},
		// active: {attr},
		events: {
			mouseover: function() {},
			mouseout: function() {},
			click: function() {},
			ready: function() {}
		}
	};
	var editOptions = {
		coordsInPercent: true,
		dotAnimation: 200,
		dotAttr: {
			'stroke-width': 0,
			cursor: 'pointer',
			fill: '#fff',
			r: 4
		},
		dotHover: {
			fill: '#fc0',
			r: 6
		},
		dotActive: {
			fill: '#f30',
			r: 6
		},
		borderAnimation: 200,
		borderAttr: {
			'stroke-width': 1,
			stroke: '#fc0',
			opacity: 1
		},
		borderHover: {
			'stroke-width': 3,
			opacity: 1
		},
		borderActive: {
			'stroke-width': 3,
			stroke: '#f30',
			opacity: 1
		},
		borderActiveDot: {
			'stroke-width': 4,
			stroke: '#f30',
			fill: '#111',
			r: 4
		}
	};
	
	var Poly = function(img, userOptions, callback) {
		if (!img || !img.length) return false;
		userOptions = userOptions || {};
		callback = callback || function() {};
		var poly = this;		
		
		// применяем пользовательские опции или подставляем дефолтные
		var options = {};
		for (var opt in generalOptions) {
			options[opt] = (opt in userOptions)? userOptions[opt] : generalOptions[opt];
		}
		
		// запоминаем полигоны, которые пытались добавить до завершения инициализации
		var tmp = [];
		poly.add = function(polys) {
			if (!polys.push) polys = [polys];
			for (var i in polys) {
				if (!polys[i].coords) continue;
				tmp.push(polys[i]);
			}
		};
		
		// инициализация
		var init = function() {
			if (img.polyInit) return;
			img.polyInit = true;
			poly.img = img;
			// строим общий контейнер
			if (!img.closest(options.polyCont).length) {
				img.wrap(function() {
					var type = 'class';
					if (options.polyCont.substr(0, 1) === '#') type = 'id';
					return '<div ' + type + '="' + options.polyCont.replace(/[.#]/gi, '') +'"></div>';
				});
			}			
			poly.cont = img.closest(options.polyCont).css({
				position: 'relative',
				width: img.width()
			});
			// строим контейнер для полигонов
			if (!$(options.paperCont, poly.cont).length) {
				poly.cont.prepend(function() {
					var type = 'class';
					if (options.paperCont.substr(0, 1) === '#') type = 'id';
					return '<div ' + type + '="' + options.paperCont.replace(/[.#]/gi, '') +'"></div>';
				});
			}
			poly.paperCont = $(options.paperCont, poly.cont).css({
				zIndex: parseInt(img.css('z-index'))? (parseInt(img.css('z-index')) + 1) : 2,
				position: 'absolute',
				left: 0,
				top: 0
			});
			poly.paper = Raphael(poly.paperCont[0], img.width(), img.height());
			poly.cont.find('svg:first').attr('class', options.svgClass);
						
			// добавление новых полигонов
			poly.polys = [];
			poly.add = function(polys) {
				if (!polys.push) polys = [polys];
				for (var i in polys) {
					if (!polys[i].coords) continue;					
					var p = {coords: polys[i].coords};
					// применяем пользовательские опции или подставляем дефолтные
					for (var type in polyOptions) {
						if ((polyOptions[type] instanceof Object) && !polyOptions[type].push) {
							if (!p[type]) p[type] = {};
							for (var opt in polyOptions[type]) {
								p[type][opt] = (opt in polys[i])? polys[i][opt] : polyOptions[type][opt];
							}
						} else {
							p[type] = (type in polys[i])? polys[i][type] : polyOptions[type];
						}
					}
					if (polys[i].hover) {
						p.hover = {};
						for (var a in p.attr) {
							p.hover[a] = (a in polys[i].hover)? polys[i].hover[a] : p.attr[a];
						}
					}
					if (polys[i].active) {
						p.active = {};
						for (var a in p.hover) {
							p.active[a] = (a in polys[i].active)? polys[i].active[a] : p.hover[a];
						}
					}
					
					// строим фигуру
					var path = 'M';
					var coords = p.coords.split(',');
					for (var i = 1; i <= coords.length; i++) {						
						if (i%2 === 0) path += parseFloat(coords[i-1]) * poly.img.height() / 100;
						else if (i < coords.length) {
							if (i > 1) path += 'L';
							path += (parseFloat(coords[i-1]) * poly.img.width() / 100) + ',';
						}
					}
					path += 'Z';
					p.path = poly.paper.path(path).attr(p.attr);
					
					// навешиваем события
					p.path[0].onmouseover = function(e) {
						if (!p.isActive && p.hover) {
							if (p.animation) p.path.animate(p.hover, p.animation);
							else p.path.attr(p.hover);
						}
						p.events.mouseover.call(p.path, e);
					};
					p.path[0].onmouseout = function(e) {
						if (!p.isActive && p.hover) {
							if (p.animation) p.path.animate(p.attr, p.animation);
							else p.path.attr(p.attr);
						}
						poly.paper.safari();
						p.events.mouseout.call(p.path, e);
					};
					p.path[0].onclick = function(e) {
						p.events.click.call(p.path, e);
						if (!p.attr.href) return false;
					};
					p.events.ready.call(p.path);
					poly.polys.push(p);
				}
				return poly;
			};
			
			
			
			// редактирование полигонов
			poly.edit = function(userEditOptions, change) {
				userEditOptions = userEditOptions || {};
				change = change || function() {};
				var activeBorderDot = null;
				var activeBorder = null;
				var activePath = null;
				var activeDot = null;
				var dragging = false;
				var activeI;
				
				// утилита для конвертирования формата кривых
				var mapPathStraight = function (path, matrix) {
					if (!matrix) return path;
					var x, y, i, j, ii, jj, pathi;
					path = Raphael.parsePathString(path);
					for (i = 0, ii = path.length; i < ii; i++) {
						pathi = path[i];
						for (j = 1, jj = pathi.length; j < jj; j += 2) {
							x = matrix.x(pathi[j], pathi[j + 1]);
							y = matrix.y(pathi[j], pathi[j + 1]);
							pathi[j] = x;
							pathi[j + 1] = y;
						}
					}
					return path;
				};

				// применяем пользовательские опции или подставляем дефолтные
				var edOptions = {};
				for (var opt in editOptions) {
					edOptions[opt] = (opt in userEditOptions)? userEditOptions[opt] : editOptions[opt];
				}
				
				// получаем строку с координатами
				var getPath = function(p) {
					var path = '';
					var coords = p.path.attr('path');
					var imgW = img.width();
					var imgH = img.height();
					for (var i = 0; i < coords.length; i++) {
						if (coords[i].length > 2) {
							if (path) path += ' , ';
							if (edOptions.coordsInPercent) {
								var x = (coords[i][1] / imgW * 100).toFixed(4);
								var y = (coords[i][2] / imgH * 100).toFixed(4);
								path += x + ',' + y;
							} else {
								path += coords[i][1].toFixed(4) + ',' + coords[i][2].toFixed(4);
							}
						}
					}
					return path;
				};
				
				// снимаем выделение с активной точки и фигуры
				var unsetActiveDot = function() {
					if (activeDot) {
						if (!activeBorderDot || activeBorderDot.id !== activeDot.id) {
							if (edOptions.dotAnimation) activeDot.animate(edOptions.dotAttr, edOptions.dotAnimation);
							else activeDot.attr(edOptions.dotAttr);
							activeDot.isActive = false;
						}
						activeDot = null;
					}
				};				
				var unsetActiveBorder = function() {
					if (activeBorder) {
						if (edOptions.borderAnimation) activeBorder.animate(edOptions.borderAttr, edOptions.borderAnimation);
						else activeBorder.attr(edOptions.borderAttr);
						if (activeBorderDot) {
							if (!activeDot || activeDot.id !== activeBorderDot.id) {
								if (edOptions.dotAnimation) activeBorderDot.animate(edOptions.dotAttr, edOptions.dotAnimation);
								else activeBorderDot.attr(edOptions.dotAttr);
								activeBorderDot.isActive = false;
							}
							activeBorderDot = null;
						}
						activeBorder.isActive = false;
						activeBorder = null;
						activeI = undefined;
					}
				};	
				var unsetActivePath = function() {
					if (activePath) {
						if (activePath.animation) activePath.path.animate(activePath.attr, activePath.animation);
						else activePath.path.attr(activePath.attr);
						activePath.isActive = false;
						activePath = null;
					}
				};
				
				// делаем элементы перетаскиваемыми
				var makeDraggable = function(p, events) {
					events.start = events.start || function() {};
					events.move = events.move || function() {};
					events.end = events.end || function() {};
					p.drag(function(dx, dy) {
						if (this.type === 'path') {
							this.translate(dx - this.odx, dy - this.ody);
							this.odx = dx;
							this.ody = dy;
							var newPath = mapPathStraight(this.attr('path'), Raphael.toMatrix(this.attr('path'), this.attr('transform')));
							this.attr({
								path: newPath.toString(),
								transform: ''
							});
						} else if (this.type === 'circle') {
							this.attr('cx', this.data('ox') + dx);
							this.attr('cy', this.data('oy') + dy);
						}
						events.move.apply(this, arguments);
					}, function() {
						if (this.type === 'path') {
							this.odx = 0;
							this.ody = 0;
						} else if (this.type === 'circle') {
							this.data('ox', this.attr('cx'));
							this.data('oy', this.attr('cy'));
						}
						events.start.apply(this, arguments);
					}, events.end);
				};
								
				// рисуем точки и границы
				var drawControls = function(p, coords, dotsDrag, borderDrag) {
					if (!p) {
						for (var i in poly.polys) {
							drawControls(poly.polys[i]);
						}
						return false;
					}
					coords = coords || p.path.attr('path');
					
					//рисуем границы
					p.borders = p.borders || [];
					for (var b in p.borders) p.borders[b].remove();
					p.borders = [];
					if (borderDrag === undefined) {
						p.bordersBig = p.bordersBig || [];
						for (var bb in p.bordersBig) p.bordersBig[bb].remove();
						p.bordersBig = [];
					}
					for (var c = 0; c < coords.length; c++) {
						(function() {
							var i = c;
							if (coords[i].length > 2) {
								var borders = [];
								borders[0] = [coords[i][1], coords[i][2]];
								if (coords[i + 1] && coords[i + 1].length > 2) {
									borders[1] = [coords[i + 1][1], coords[i + 1][2]];
								} else {
									borders[1] = [coords[0][1], coords[0][2]];
								}
								var border = poly.paper.path('M' + borders[0][0] + ',' + borders[0][1] + 'L' + borders[1][0] + ',' + borders[1][1]).toFront();
								if (activeI === undefined || activeI !== i) {
									border.attr(edOptions.borderAttr);
								} else {
									border.attr(edOptions.borderActive);
									border.isActive = true;
									activeBorder = border;
								}
								border.poly = p;
								p.borders.push(border);
								if (borderDrag === undefined) {
									var borderBig = poly.paper.path('M' + borders[0][0] + ',' + borders[0][1] + 'L' + borders[1][0] + ',' + borders[1][1]).attr({
										'stroke-width': edOptions.dotHover.r * 2 + 4,
										opacity: 0
									}).toFront();
									borderBig.poly = p;
									borderBig.border = border;
									border.borderBig = borderBig;
									var newDot = null;
									borderBig[0].onmouseover = function() {
										if (!border.isActive) {
											if (edOptions.borderAnimation) border.animate(edOptions.borderHover, edOptions.borderAnimation);
											else border.attr(edOptions.borderHover);
										}
									};
									var x, y, time;
									borderBig[0].onmousemove = function(e) {
										if (dragging) return false;
										var y = (e.offsetY === undefined)? e.layerY : e.offsetY;
										var x = (e.offsetX === undefined)? e.layerX : e.offsetX;
										var ax = borders[0][0];
										var ay = borders[0][1];
										var bx = borders[1][0];
										var by = borders[1][1];
										if (ax === bx) x = ax;
										else if (ay === by) y = ay;
										else {
											if (Math.abs(bx - ax) > Math.abs(by - ay)) {
												y = (x - ax)*(by - ay)/(bx - ax) + ay;
											} else {
												x = (y - ay)*(bx - ax)/(by - ay) + ax;
											}
										}
										if (!newDot) {
											newDot = poly.paper.circle(x, y).attr(edOptions.dotAttr).attr({opacity: 0.5}).toBack();
										}
										newDot.attr({cx: x,	cy: y});
									};
									borderBig[0].onmousedown = function(e) {
										x = e.pageX;
										y = e.pageY;
										time = e.timeStamp;
									};
									borderBig[0].onmouseup = function(e) {
										if (Math.abs(e.pageX - x) > 5 || Math.abs(e.pageY - y) > 5 || (e.timeStamp - time > 500)) return false;
										x = y = time = 0;
										if (e.button === 2) {
											if (border.isActive) {
												unsetActiveBorder();
												return false;
											}
											if (activeBorder) unsetActiveBorder();
											if (edOptions.borderAnimation) border.animate(edOptions.borderActive, edOptions.borderAnimation);
											else border.attr(edOptions.borderActive);
											border.isActive = true;
											activeBorder = border;
											p.dots[i].attr(edOptions.borderActiveDot);
											p.dots[i].isActive = true;
											activeBorderDot = p.dots[i];
											activeI = i;
											unsetActiveDot();
											unsetActivePath();
											return false;
										} else {
											if (!newDot) return;
											var path = 'M';
											for (var j = 0; j < p.dots.length; j++) {
												if (path !== 'M') path += 'L';
												path += p.dots[j].attr('cx') + ',' + p.dots[j].attr('cy');
												if (j === i) path += 'L' + newDot.attr('cx') + ',' + newDot.attr('cy');
											}
											path += 'Z';
											p.path.attr('path', path);
											drawControls(p, p.path.attr('path'));
											if (newDot) newDot.remove();
											newDot = null;
											if (activeI === undefined || activeI !== i) unsetActiveBorder();
											return false;
										}
									};
									borderBig[0].oncontextmenu = function() {return false;};
									borderBig[0].onmouseout = function() {
										if (!border.isActive) {
											if (edOptions.borderAnimation) border.animate(edOptions.borderAttr, edOptions.borderAnimation);
											else border.attr(edOptions.borderAttr);
										}
										if (newDot) newDot.remove();
										newDot = null;
									};
									var dot1, dot2, moved = false;
									makeDraggable(borderBig, {
										start: function() {
											dot1 = p.dots[i];
											dot2 = p.dots[i+1]? p.dots[i+1] : p.dots[0];
											borderBig.ox1 = dot1.attr('cx');
											borderBig.oy1 = dot1.attr('cy');
											borderBig.ox2 = dot2.attr('cx');
											borderBig.oy2 = dot2.attr('cy');
											dragging = true;
											moved = false;
										},
										move: function(dx, dy) {
											if (Math.abs(dx) > 2 || Math.abs(dy) > 2) moved = true;
											if (newDot) newDot.remove();
											newDot = null;
											dot1 = p.dots[i];
											dot2 = p.dots[i+1]? p.dots[i+1] : p.dots[0];
											dot1.attr({cx: dx + borderBig.ox1, cy: dy + borderBig.oy1});
											dot2.attr({cx: dx + borderBig.ox2, cy: dy + borderBig.oy2});
											var path = 'M';
											for (var dt in p.dots) {
												if (path !== 'M') path += 'L';
												if (p.dots[dt].id === dot1.id) path += (dx + borderBig.ox1) + ',' + (dy + borderBig.oy1);
												else if (p.dots[dt].id === dot2.id) path += (dx + borderBig.ox2) + ',' + (dy + borderBig.oy2);
												else path += p.dots[dt].attr('cx') + ',' + p.dots[dt].attr('cy');
											}
											path += 'Z';
											p.path.attr('path', path);
											drawControls(p, p.path.attr('path'), false, i);
										},
										end: function() {
											borderBig.odx = 0;
											borderBig.ody = 0;
											if (moved) drawControls(p);
											dragging = false;
											moved = false;
										}
									});
									p.bordersBig.push(borderBig);
								}
							}
						})();
					}
					
					// рисуем точки
					if (dotsDrag) {
						for (var d in p.dots) p.dots[d].toFront();
					} else {
						p.dots = p.dots || [];
						for (var d in p.dots) p.dots[d].remove();
						p.dots = [];
						for (var c = 0; c < coords.length; c++) {
							(function() {
								var i = c;
								if (coords[i].length > 2) {
									var dot = poly.paper.circle(coords[i][1], coords[i][2]).toFront();
									if (activeI === undefined || activeI !== i) {
										dot.attr(edOptions.dotAttr);
									} else {
										dot.attr(edOptions.borderActiveDot);
										dot.isActive = true;
										activeBorderDot = dot;
									}
									dot.poly = p;
									var x, y, time;
									dot[0].onmouseover = function() {
										if (!dot.isActive) {
											if (edOptions.dotAnimation) dot.animate(edOptions.dotHover, edOptions.dotAnimation);
											else dot.attr(edOptions.dotHover);
										}
									};
									dot[0].onmousedown = function(e) {
										x = e.pageX;
										y = e.pageY;
										time = e.timeStamp;
									};
									dot[0].onmouseup = function(e) {
										if (Math.abs(e.pageX - x) > 5 || Math.abs(e.pageY - y) > 5 || (e.timeStamp - time > 500)) return false;
										x = y = time = 0;
										if (dot.isActive && !activeBorderDot) {
											unsetActiveBorder();
											unsetActiveDot();
											return false;
										}
										if (activeDot) unsetActiveDot();
										if (edOptions.dotAnimation) dot.animate(edOptions.dotActive, edOptions.dotAnimation);
										else dot.attr(edOptions.dotActive);
										dot.isActive = true;
										activeDot = dot;
										dot.toFront();
										unsetActiveBorder();
										return false;
									};
									dot[0].oncontextmenu = function() {return false;};
									dot[0].onmouseout = function() {
										if (!dot.isActive) {
											if (edOptions.dotAnimation) dot.animate(edOptions.dotAttr, edOptions.dotAnimation);
											else dot.attr(edOptions.dotAttr);
										}
									};
									makeDraggable(dot, {
										start: function() {
											dragging = true;
										},
										move: function() {
											var path = 'M';
											for (var dt in p.dots) {
												if (path !== 'M') path += 'L';
												path += p.dots[dt].attr('cx') + ',' + p.dots[dt].attr('cy');
											}
											path += 'Z';
											p.path.attr('path', path);
											drawControls(p, p.path.attr('path'), true);
										},
										end: function() {
											dragging = false;
										}
									});
									p.dots.push(dot);
								}
							})();
						}
					}
					
					change.call(poly, getPath(p));
				};
				
				// действия с активной точкой и фигурой
				var moveTimer = 0;
				var moveTime = 400;
				var setDotsTranslate = function(d, trX, trY) {
					var p = d.poly;
					d.attr({
						cx: d.attr('cx') + trX,
						cy: d.attr('cy') + trY
					});
					var path = 'M';
					for (var j = 0; j < p.dots.length; j++) {
						if (path !== 'M') path += 'L';
						path += p.dots[j].attr('cx') + ',' + p.dots[j].attr('cy');
					}
					path += 'Z';
					p.path.attr('path', path);
					drawControls(p, p.path.attr('path'), true);
					clearTimeout(moveTimer);
					moveTimer = setTimeout(function() {
						setDotsTranslate(d, trX, trY);
					}, moveTime);
				};
				var setPathTranslate = function(p, trX, trY) {
					activePath.path.translate(trX, trY);
					var newPath = mapPathStraight(activePath.path.attr('path'), Raphael.toMatrix(activePath.path.attr('path'), activePath.path.attr('transform')));
					activePath.path.attr({
						path: newPath.toString(),
						transform: ''
					});
					drawControls(activePath);
					clearTimeout(moveTimer);
					moveTimer = setTimeout(function() {
						setPathTranslate(p, trX, trY);
					}, moveTime);
				};
				var setBorderTranslate = function(b, trX, trY) {
					var p = b.poly;
					if (!p.dots) return;
					var dot1 = p.dots[activeI];
					var dot2 = p.dots[activeI+1]? p.dots[activeI+1] : p.dots[0];
					var path = 'M';
					for (var dt in p.dots) {
						if (path !== 'M') path += 'L';
						if (p.dots[dt].id === dot1.id) path += (dot1.attr('cx') + trX) + ',' + (dot1.attr('cy') + trY);
						else if (p.dots[dt].id === dot2.id) path += (dot2.attr('cx') + trX) + ',' + (dot2.attr('cy') + trY);
						else path += p.dots[dt].attr('cx') + ',' + p.dots[dt].attr('cy');
					}
					path += 'Z';
					p.path.attr('path', path);
					drawControls(p);
					clearTimeout(moveTimer);
					moveTimer = setTimeout(function() {
						setBorderTranslate(b, trX, trY);
					}, moveTime);
				};
				var setOneLine = function(b, vert) {
					var p = b.poly;
					if (!p.dots) return;
					var dot1 = p.dots[activeI];
					var dot2 = p.dots[activeI+1]? p.dots[activeI+1] : p.dots[0];
					if (vert && dot1.attr('cy') === dot2.attr('cy')) return;
					else if (!vert && dot1.attr('cx') === dot2.attr('cx')) return;
					var path = 'M';
					for (var dt in p.dots) {
						if (path !== 'M') path += 'L';
						if (p.dots[dt].id === dot1.id) path += dot1.attr('cx') + ',' + dot1.attr('cy');
						else if (p.dots[dt].id === dot2.id && vert) path += dot1.attr('cx') + ',' + dot2.attr('cy');
						else if (p.dots[dt].id === dot2.id && !vert) path += dot2.attr('cx') + ',' + dot1.attr('cy');
						else path += p.dots[dt].attr('cx') + ',' + p.dots[dt].attr('cy');
					}
					path += 'Z';
					p.path.attr('path', path);
					drawControls(p);
				};
				$(document).on('keyup', function() {
					clearTimeout(moveTimer);
				}).on('keydown', function(e) {
					if (activeDot) {
						switch (e.keyCode) {
							case 37:
								setDotsTranslate(activeDot, -1, 0);
								return false;
								break;
							case 38:
								setDotsTranslate(activeDot, 0, -1);
								return false;
								break;
							case 39:
								setDotsTranslate(activeDot, 1, 0);
								return false;
								break;
							case 40:
								setDotsTranslate(activeDot, 0, 1);
								return false;
								break;
							case 8:
							case 46:
								var p = activeDot.poly;
								if (p.dots.length === 3) {
									if (confirm('Удалить фигуру?')) {
										poly.remove(p);
										change.call(poly, '');
									}
									return;
								} else {
									if (!confirm('Удалить точку?')) return;
								}
								var path = 'M';
								for (var j = 0; j < p.dots.length; j++) {
									if (p.dots[j].isActive) continue;
									if (path !== 'M') path += 'L';
									path += p.dots[j].attr('cx') + ',' + p.dots[j].attr('cy');
								}
								path += 'Z';
								p.path.attr('path', path);
								drawControls(p, p.path.attr('path'));
								return false;
								break;
						}
					} else if (activeBorder) {
						switch (e.keyCode) {
							case 37:
								if (e.ctrlKey) setOneLine(activeBorder, 0);
								else setBorderTranslate(activeBorder, -1, 0);
								return false;
								break;
							case 38:
								if (e.ctrlKey) setOneLine(activeBorder, 1);
								else setBorderTranslate(activeBorder, 0, -1);
								return false;
								break;
							case 39:
								if (e.ctrlKey) setOneLine(activeBorder, 0);
								else setBorderTranslate(activeBorder, 1, 0);
								return false;
								break;
							case 40:
								if (e.ctrlKey) setOneLine(activeBorder, 1);
								else setBorderTranslate(activeBorder, 0, 1);
								return false;
								break;
							case 72:
							case 85:
								setOneLine(activeBorder, 0);
								return false;
								break;
							case 68:
							case 86:
								setOneLine(activeBorder, 1);
								return false;
								break;
						}
					} else if (activePath) {
						switch (e.keyCode) {
							case 37:
								setPathTranslate(activePath, -1, 0);
								return false;
								break;
							case 38:
								setPathTranslate(activePath, 0, -1);
								return false;
								break;
							case 39:
								setPathTranslate(activePath, 1, 0);
								return false;
								break;
							case 40:
								setPathTranslate(activePath, 0, 1);
								return false;
								break;
							case 8:
							case 46:
								if (confirm('Удалить фигуру?')) {
									poly.remove(activePath);
									change.call(poly, '');
								}
								return false;
								break;
						}
					}
				});
				
				// снимаем активность по клику вне
				poly.cont.on('mouseup', function(e) {
					if (activeDot && e.target.localName !== 'circle') {
						unsetActiveDot();
					}
					if (activeBorder && e.target.localName !== 'path') {
						unsetActiveBorder();
					}
					if (activePath && e.target.localName !== 'path') {
						unsetActivePath();
					}
				});
				$('BODY').on('mouseup', function(e) {
					if (activeDot || activeBorder || activePath) {
						var target = $(e.target);
						if (!target.is(poly.cont) && !target.closest(poly.cont).length) {
							unsetActiveDot();
							unsetActivePath();
							unsetActiveBorder();
						}
					}
				});
				
				// инициацализация всех полигонов
				for (var i in poly.polys) {
					(function() {
						var p = poly.polys[i];
						drawControls(p, p.path.attr('path'));
						makeDraggable(p.path, {
							start: function() {
								dragging = true;
							},
							move: function() {
								drawControls(p, this.attr('path'));
							},
							end: function() {
								dragging = false;
							}
						});
						var x, y, time;
						p.path[0].onmousedown = function(e) {
							x = e.pageX;
							y = e.pageY;
							time = e.timeStamp;
						};
						p.path[0].oncontextmenu = function() {return false;};
						poly.cont[0].oncontextmenu = function() {return false;};
						p.path[0].onmouseup = function(e) {
							if (Math.abs(e.pageX - x) > 5 || Math.abs(e.pageY - y) > 5 || (e.timeStamp - time > 500)) {
								x = y = time = 0;
								return;
							}
							x = y = time = 0;
							unsetActiveBorder();
							if (p.isActive) {
								unsetActivePath();
								return false;
							}
							if (activePath) unsetActivePath();
							if (p.animation) p.path.animate(p.active, p.animation);
							else p.path.attr(p.active);
							p.isActive = true;
							activePath = p;
							return false;
						};
					})();
				}
				return poly;
			};
			
			// удаление полигона
			poly.remove = function(p) {
				if (!p) {
					for (var i in poly.polys) {
						poly.remove(poly.polys[i]);
					}
					return poly;
				}
				var newPolys = [];
				for (var i in poly.polys) {
					if (poly.polys[i].path.id !== p.path.id) newPolys.push(poly.polys[i]);
				}
				poly.polys = newPolys;
				for (var bb in p.bordersBig) p.bordersBig[bb].remove();
				for (var b in p.borders) p.borders[b].remove();
				for (var d in p.dots) p.dots[d].remove();
				p.path.remove();
				return poly;
			};
			
			if (tmp.length) poly.add(tmp);
			poly.ready = true;
			callback.call(poly);
		};
		
		// инициализируем после завершения загрузки картинки
		/*if ((img[0].complete || img[0].readyState === 4) && img[0].width) init();
		else */img[0].onload = init;
		if (!img.polyInit) img.attr('src', img.attr('src') + '?' + Date.now());
		
		return poly;
	};
	
	return Poly;
});