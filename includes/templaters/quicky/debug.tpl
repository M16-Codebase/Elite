{?$_debug_info = get_debug_info()}
{capture assign="debug_output"}
{if empty($_debug_charset)}{assign var="_debug_charset" value="utf-8"}{/if}

{?$script1='<style type="text/css">
		/* <![CDATA[ */
			#debug, #debug h1, #debug h2, #debug td, #debug th, #debug p {
				font-family: sans-serif;
				font-weight: normal;
				font-size: 0.9em;
				margin: 1px;
				padding: 0;
				vertical-align: top;
			}
			#debug h1 {
				margin: 0;
				text-align: left;
				padding: 2px;
				background-color: #6699ff;
				color:  black;
				font-weight: bold;
				font-size: 1.2em;
			 }
			#debug h2 {
				background-color: #006633;
				color: white;
				text-align: left;
				font-weight: bold;
				padding: 2px;
				border-top: 1px solid black;
			}
			#debug {
				background: #cccccc; 
			}
			#debug p, #debug table, #debug div {
				background: #f0ead8;
			} 
			#debug p {
				margin: 0;
				font-style: italic;
				text-align: center;
			}
			#debug table {
				width: 100%;
			}
			#debug th, #debug  td {
				font-family: monospace;
				vertical-align: top;
				text-align: left;
				width: 30%;
			}
			#debug td {
				color: green;
			}
			#debug .odd {
				background-color: #eeeeee;
			}
			#debug  .even {
				background-color: #fafafa;
			}
			#debug  .exectime {
				font-size: 0.8em;
				font-style: italic;
			}
			#debug  #table_assigned_vars th {
				color: blue;
			}
			#table_config_vars th {
				color: maroon;
			}
			.nobr{
				white-space: nowrap;
			}
			#tooltip {
				background:#ffffcc;border:1px solid #000000;
				font:normal 12px Tahoma, Verdana, Arial, sans-serif;
				color:#000000;margin:0px;padding:4px 5px;position:absolute;;
			}
			#footer{
				text-align:right;
			}
			.active{
				cursor:pointer;
			}
			/* ]]> */
		</style>
		<script>
			function toggle(obj) {
				var el = document.getElementById(obj);
				if ( el.style.display != "none" ) {
					el.style.display = "none";
				} else {
					el.style.display = "";
				}
			}

			function getElementsByClass(searchClass,node,tag) {
				var classElements = new Array();
				if ( node == null )
					node = document;
				if ( tag == null )
					tag = "*";
				var els = node.getElementsByTagName(tag);
				var elsLen = els.length;			
				var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
				for (i = 0, j = 0; i < elsLen; i++) {
					if ( pattern.test(els[i].className) ) {
						classElements[j] = els[i];
						j++;
					}
				}
				return classElements;
			}

			function addEvent(elm, evType, fn, useCapture) {
				if (typeof elm == "string")
					elm = document.getElementById(element);
				if (useCapture == null )
					useCapture = false;
				if (elm.addEventListener) {
					elm.addEventListener(evType, fn, useCapture);
				return true;
				}
				else if (elm.attachEvent) {
					var r = elm.attachEvent("on" + evType, fn);
					return r;
				}
				else {
					elm["on" + evType] = fn;
				}
			}

			function addLoadEvent(func) {
				var oldonload = window.onload;
				if (typeof window.onload != "function") {
					window.onload = func;
				}else{
					window.onload = function() {
						oldonload();
						func();
					}
				}
			}
		</script>
'}

{?$script2='
		<script type="text/javascript">/*
		originally written by paul sowden <paul@idontsmoke.co.uk> | http://idontsmoke.co.uk
		modified and localized by alexander shurkayev <alshur@narod.ru> | http://htmlcoder.visions.ru
		*/
		var tooltip = {
			options: {
				attr_name: "tooltip",
				blank_text: "(аОб�аКб�аОаЕб�б�б� аВ аНаОаВаОаМ аОаКаНаЕ)",
				newline_entity: "html",
				max_width: 0,
				delay: 100,
				skip_tags: ["link", "style"] 
			},
			/* а�а�а�а�аІ а�а�аЁаЂа а�а�а� */
			
			t: document.createElement("DIV"),
			c: null,
			g: false,
			canvas: null,

			m: function(e){
				if (tooltip.g){
					var x = window.event ? event.clientX + tooltip.canvas.scrollLeft : e.pageX;
					var y = window.event ? event.clientY + tooltip.canvas.scrollTop : e.pageY;
					tooltip.a(x, y);
				}
			},

			d: function(){
				tooltip.canvas = document.getElementsByTagName(document.compatMode && document.compatMode == "CSS1Compat" ? "HTML" : "BODY")[0];
				tooltip.t.setAttribute("id", "tooltip");
				document.body.appendChild(tooltip.t);
				if (tooltip.options.max_width) tooltip.t.style.maxWidth = tooltip.options.max_width + "px"; // all but ie
				var a = document.all && !window.opera ? document.all : document.getElementsByTagName("*"); // in opera 9 document.all produces type mismatch error
				var l = a.length;
				for (var i = 0; i < l; i++){

					if (!a[i] || tooltip.options.skip_tags.in_array(a[i].tagName.toLowerCase())) continue;

					var tooltip_title = a[i].getAttribute("title"); // returns form object if IE & name="title"; then IE crashes; so...
					if (tooltip_title && typeof tooltip_title != "string") tooltip_title = "";

					var tooltip_alt = a[i].getAttribute("alt");
					var tooltip_blank = a[i].getAttribute("target") && a[i].getAttribute("target") == "_blank" && tooltip.options.blank_text;
					if (tooltip_title || tooltip_blank){
						a[i].setAttribute(tooltip.options.attr_name, tooltip_blank ? (tooltip_title ? tooltip_title + " " + tooltip.options.blank_text : tooltip.options.blank_text) : tooltip_title);
						if (a[i].getAttribute(tooltip.options.attr_name)){
							a[i].removeAttribute("title");
							if (tooltip_alt && a[i].complete) a[i].removeAttribute("alt");
							tooltip.l(a[i], "mouseover", tooltip.s);
							tooltip.l(a[i], "mouseout", tooltip.h);
						}
					}else if (tooltip_alt && a[i].complete){
						a[i].setAttribute(tooltip.options.attr_name, tooltip_alt);
						if (a[i].getAttribute(tooltip.options.attr_name)){
							a[i].removeAttribute("alt");
							tooltip.l(a[i], "mouseover", tooltip.s);
							tooltip.l(a[i], "mouseout", tooltip.h);
						}
					}
					if (!a[i].getAttribute(tooltip.options.attr_name) && tooltip_blank){
						//
					}
				}
				document.onmousemove = tooltip.m;
				window.onscroll = tooltip.h;
				tooltip.a(-99, -99);
			},
			
			_: function(s){
				s = s.replace(/\&/g,"&amp;");
				s = s.replace(/\</g,"&lt;");
				s = s.replace(/\>/g,"&gt;");
				return s;
			},

			s: function(e){
				if (typeof tooltip == "undefined") return;
				var d = window.event ? window.event.srcElement : e.target;
				if (!d.getAttribute(tooltip.options.attr_name)) return;
				var s = d.getAttribute(tooltip.options.attr_name);
				if (tooltip.options.newline_entity!="html"){
					if (tooltip.options.newline_entity){
						var s = tooltip._(s);
						s = s.replace(eval("/" + tooltip._(tooltip.options.newline_entity) + "/g"), "<br />");
						tooltip.t.innerHTML = s;
					}else{
						tooltip.t.innerHTML = s;
			
						if (tooltip.t.firstChild) tooltip.t.removeChild(tooltip.t.firstChild);
						tooltip.t.appendChild(document.createTextNode(s));
					}
				}else{
					tooltip.t.innerHTML = s;
				}
				tooltip.c = setTimeout(function(){
					tooltip.t.style.visibility = "visible";
				}, tooltip.options.delay);
				tooltip.g = true;
			},

			h: function(e){
				if (typeof tooltip == "undefined") return;
				tooltip.t.style.visibility = "hidden";
				if (!tooltip.options.newline_entity && tooltip.t.firstChild) tooltip.t.removeChild(tooltip.t.firstChild);
				clearTimeout(tooltip.c);
				tooltip.g = false;
				tooltip.a(-99, -99);
			},

			l: function(o, e, a){
				if (o.addEventListener) o.addEventListener(e, a, false); // was true--Opera 7b workaround!
				else if (o.attachEvent) o.attachEvent("on" + e, a);
					else return null;
			},

			a: function(x, y){
				var w_width = tooltip.canvas.clientWidth ? tooltip.canvas.clientWidth + tooltip.canvas.scrollLeft : window.innerWidth + window.pageXOffset;
				var w_height = window.innerHeight ? window.innerHeight + window.pageYOffset : tooltip.canvas.clientHeight + tooltip.canvas.scrollTop; // should be vice verca since Opera 7 is crazy!

				if (document.all && document.all.item && !window.opera) tooltip.t.style.width = tooltip.options.max_width && tooltip.t.offsetWidth > tooltip.options.max_width ? tooltip.options.max_width + "px" : "auto";
				
				var t_width = tooltip.t.offsetWidth;
				var t_height = tooltip.t.offsetHeight;

				tooltip.t.style.left = x + 8 + "px";
				tooltip.t.style.top = y + 8 + "px";
				
				if (x + t_width > w_width) tooltip.t.style.left = w_width - t_width + "px";
				if (y + t_height > w_height) tooltip.t.style.top = w_height - t_height + "px";
			}
		}

		Array.prototype.in_array = function(value){
			var l = this.length;
			for (var i = 0; i < l; i++)
				if (this[i] === value) return true;
			return false;
		};

		var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
		if (root){
			if (root.addEventListener) root.addEventListener("load", tooltip.d, false);
			else if (root.attachEvent) root.attachEvent("onload", tooltip.d);
		}

		
		function collapse_all(){
			var elements = getElementsByClass("collapseble");
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = "none";
			}
			var elements = getElementsByClass("collapse_title");
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = "";
			}
		}
		function uncollapse_all(){
			var elements = getElementsByClass("collapseble");
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = "";
			}
			var elements = getElementsByClass("collapse_title");
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = "none";
			}
		}
		</script>
'}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>Quicky Debugger</title>
		{$script1|html}
	</head>
	<body>
		<div id="debug">
			<h1>Debug Console</h1>
			<h2>Global scope of assigned variables</h2>
			<div id="collapse_menu">
			</div>
			<table id="table_assigned_vars">
				{?$i=0;}
				{foreach key=$name from=$_debug_info.var item=$varinfo}
					{?$i++;}
					<tr class="{cycle values="odd,even"}">
						<th>{ldelim}${$name}{rdelim}</th>
						<td><span id="v_{$i}">{$varinfo.value|debug_print_var:$_debug_charset|html}<span></td>
						<td onclick="toggle('l_{$i}');toggle('l_{$i}_');" class="active">
							<span id="l_{$i}" class="collapseble" style="display:none;">
								{foreach from=$varinfo.trace item="traceitem" name="trace"}
									<p>{$traceitem.file}:{$traceitem.line}</p>
									{if last}
							</span>
							<span id="l_{$i}_" class="collapse_title">
								<p>{$traceitem.file|basename}:{$traceitem.line} ...</p>
							</span>
									{/if}
								{foreachelse}
							</span>
								{/foreach}
						</td>
					</tr>
				{foreachelse}
					<tr><td><p>no template variables assigned</p></td></tr>
				{/foreach}

{/capture}{*������� �� �� ���� � �����������*}
{capture assign="debug_output2"}
				<tr><td colspan="3"><h2>assigned GET &amp; POST &amp; SERVER vars</h2></td></tr>
				{?$i="get"}
				<tr class="{cycle values="odd,even"}">
					<th>{ldelim}$_GET{rdelim}</th>
					<td><span id="v_{$i}">{$quicky.get|debug_print_var:$_debug_charset|html}<span></td><td> </td>
					{?$i="post"}
				</tr><tr class="{cycle values="odd,even"}">
					<th>{ldelim}$_POST{rdelim}</th>
					<td><span id="v_{$i}">{$quicky.post|debug_print_var:$_debug_charset|html}<span></td><td> </td>
				</tr>
				{?$i="server"}
				</tr><tr class="{cycle values="odd,even"}">
					<th>{ldelim}$_SERVER{rdelim}</th>
					<td><span id="v_{$i}">{$quicky.server|debug_print_var:$_debug_charset|html}<span></td><td> </td>
				</tr>
			</table>
			<div id="collapse_menu">
				<span onclick="collapse_all();" class="active">collapse all</span> |
				<span onclick="uncollapse_all();" class="active">expand all</span>
			</div><hr>
			<div id = "footer">Quicky debugger. <a href="mailto:leadaxe@yandex.ru">Lex &amp; Quicky</a></div>
		</div>
		{$script2|html}
	</body>
</html>
{/capture}

{assign var="debug_output" value="`$debug_output``$debug_output2`"}{*������� �� �� ���� � �����������*}

{if isset($_quicky_debug_output) and $_quicky_debug_output eq "html"}
    {$debug_output|html}
{else}
<script type="text/javascript">
// <![CDATA[
    if ( self.name == "" ) {ldelim}
       var title = "Debug Console";
    {rdelim}
    else {ldelim}
       var title = "Debug Console_" + self.name;
    {rdelim}
    _quicky_console = window.open("", title.value, "width=880, height=600, resizable, scrollbars=yes");
    _quicky_console.document.write({$debug_output|native_json_encode|html});
    _quicky_console.document.close();
// ]]>
</script>
{/if}