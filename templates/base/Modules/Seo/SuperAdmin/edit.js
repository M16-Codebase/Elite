$(function() {
	require(['picUploader', 'editor', 'editContent'], function(editContent) {
		
		/*variables*/
		
//		var saveMeta=$(".view-content .action-save");
//		var metaForm=$(".meta-form");
//		
//		/*events*/
//		saveMeta.click(function() {
//			metaForm.submit();
//		});
		
		$('.actions-panel .action-save').click(function() {
			$(this).closest('FORM').submit();
		});
		
//		tinyMCE.init({
//			// General options
//			mode : "specific_textareas",
//			editor_selector : /(mceEditor)/,
//			plugins : "spellchecker,safari,pagebreak,style,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",
//			document_base_url : (window.location+'').replace(/(^[^\/]*:\/\/[^\/]*)\/.*/i, function(b, a){
//				return a;
//			}),
//			convert_urls : false,
//			theme : "advanced",
//			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,|,code,fullscreen",
//			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,insertdate,inserttime,preview,|,forecolor,backcolor",
//			theme_advanced_buttons3 : "tablecontrols,|,hr,|,sub,sup,|,charmap,nonbreaking,emotions,|,styleprops,attribs,pagebreak,|,cleanup,removeformat,spellchecker",
//			theme_advanced_buttons4 : "",
//			theme_advanced_toolbar_location : "top",
//			theme_advanced_toolbar_align : "left",
//			theme_advanced_statusbar_location : "bottom",
//			theme_advanced_resizing : true,
//			valid_elements : "@[id|class|style|title],"
//			+ "a[rel|rev|charset|hreflang|tabindex|accesskey|type|name|href|target|title|class],"
//			+ "#p,br,-strong/b,-em/i,-strike,-u,-sub,-sup,-blockquote,"
//			+ "-ol[type|compact],-ul[type|compact],-li,"
//			+ "img[longdesc|usemap|src|border|alt=|title|hspace|vspace|width|height|align],"
//			+ "-table[border=0|cellspacing|cellpadding|width|rules|height|align|summary|bgcolor|background|bordercolor],"
//			+ "-tr[rowspan|width|height|align|valign|bgcolor|background|bordercolor],"
//			+ "tbody,thead,tfoot,"
//			+ "#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor|scope],"
//			+ "#th[colspan|rowspan|width|height|align|valign|scope],"
//			+ "-div[videourl|align]],-span,-code,-pre,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],"
//			+ "-font[face|size|color],del[datetime|cite],ins[datetime|cite],",
//			// Example content CSS (should be your site CSS)
//			content_css : "/templates/MCE_editor.css",
//			cleanup_on_startup : true,
//			width : "99%",
//			area_width :"99%",
//			// Spellchecker
//			spellchecker_languages : "+Russian=ru,Ukrainian=uk,English=en",
//			spellchecker_rpc_url : "/yandex_spellchecker_rpc_proxy.php",
//			spellchecker_word_separator_chars : '\\s!"#$%&()*+,./:;<=>?@[\]^_{|}\xa7 \xa9\xab\xae\xb1\xb6\xb7\xb8\xbb\xbc\xbd\xbe\u00bf\xd7\xf7\xa4\u201d\u201c'
//
//		});		
	});
});