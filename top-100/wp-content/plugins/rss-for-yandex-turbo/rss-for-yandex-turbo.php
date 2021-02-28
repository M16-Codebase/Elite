<?php
/*
Plugin Name: RSS for Yandex Turbo
Plugin URI: https://wordpress.org/plugins/rss-for-yandex-turbo/
Description: Создание RSS-ленты для сервиса Яндекс.Турбо.
Version: 1.14
Author: Flector
Author URI: https://profiles.wordpress.org/flector#content-plugins
Text Domain: rss-for-yandex-turbo
*/ 

//функция установки значений по умолчанию при активации плагина begin
function yturbo_init() {
    $yturbo_options = array(); 
    $yturbo_options['ytrssname'] = "turbo";
    $yturbo_options['yttitle'] = get_bloginfo_rss('title');
    $yturbo_options['ytlink'] = get_bloginfo_rss('url');
    $yturbo_options['ytdescription'] = get_bloginfo_rss('description');
    $yturbo_options['ytlanguage'] = "ru";
    $yturbo_options['ytnumber'] = "120";
    $yturbo_options['yttype'] = "post";
    $yturbo_options['ytrazb'] = "enabled";
    $yturbo_options['ytrazbnumber'] = "40";
    $yturbo_options['ytfigcaption'] = "Отключить описания";
    $yturbo_options['ytimgauthorselect'] = "Отключить указание автора";
    $yturbo_options['ytimgauthor'] = "";
    $yturbo_options['ytauthorselect'] = "Отключить указание автора";
    $yturbo_options['ytauthor'] = "";
    $yturbo_options['ytthumbnail'] = "enabled";
    $yturbo_options['ytselectthumb'] = "large";
    $yturbo_options['ytexcludetags'] = "disabled";
    $yturbo_options['ytexcludetagslist'] = "<div>";
    $yturbo_options['ytexcludetags2'] = "enabled";
    $yturbo_options['ytexcludetagslist2'] = "<ins>,<style>,<object>";
    $yturbo_options['ytexcludecontent'] = "disabled";
    $yturbo_options['ytexcludecontentlist'] = esc_textarea("<!--more-->\n<p><\/p>\n<p>&nbsp;<\/p>");

    $yturbo_options['ytad1'] = "disabled";
    $yturbo_options['ytad1set'] = "РСЯ";
    $yturbo_options['ytad1rsa'] = "";
    $yturbo_options['ytadfox1'] = "";
    
    $yturbo_options['ytad2'] = "disabled";
    $yturbo_options['ytad2set'] = "РСЯ";
    $yturbo_options['ytad2rsa'] = "";   
    $yturbo_options['ytadfox2'] = "";
    
    $yturbo_options['ytad3'] = "disabled";
    $yturbo_options['ytad3set'] = "РСЯ";
    $yturbo_options['ytad3rsa'] = "";
    $yturbo_options['ytadfox3'] = "";
    
    $yturbo_options['ytad4'] = "disabled";
    $yturbo_options['ytad4set'] = "РСЯ";
    $yturbo_options['ytad4rsa'] = "";
    $yturbo_options['ytadfox4'] = "";
    
    $yturbo_options['ytad5'] = "disabled";
    $yturbo_options['ytad5set'] = "РСЯ";
    $yturbo_options['ytad5rsa'] = "";
    $yturbo_options['ytadfox5'] = "";
  
    
    $yturbo_options['ytrelated'] = "enabled";
    $yturbo_options['ytrelatednumber'] = "5";
    $yturbo_options['ytrelatedselectthumb'] = "thumbnail";
    $yturbo_options['ytrelatedcache'] = "enabled";
    $yturbo_options['ytrelatedcachetime'] = "72";
    $yturbo_options['ytrelatedinfinity'] = "disabled";
    
    $yturbo_options['ytrazmer'] = "500";
    $yturbo_options['ytremoveturbo'] = "disabled";
    
    $yturbo_options['ytcounterselect'] = "Яндекс.Метрика";
    $yturbo_options['ytmetrika'] = "";
    $yturbo_options['ytliveinternet'] = "";
    $yturbo_options['ytgoogle'] = "";
    $yturbo_options['ytmailru'] = "";
    $yturbo_options['ytrambler'] = "";
    $yturbo_options['ytmediascope'] = "";
    
    $yturbo_options['ytqueryselect'] = "Все таксономии, кроме исключенных";
    $yturbo_options['yttaxlist'] = "";
    $yturbo_options['ytaddtaxlist'] = "";
    
    $yturbo_options['ytselectmenu'] = "Не использовать";
    $yturbo_options['ytshare'] = "disabled";
    $yturbo_options['ytnetw'] = "vkontakte,facebook,twitter,google,odnoklassniki,telegram,";
    $yturbo_options['ytgallery'] = "disabled";
    $yturbo_options['ytcomments'] = "disabled";
    $yturbo_options['ytcommentsavatar'] = "disabled";
    $yturbo_options['ytcommentsnumber'] = "40";
    $yturbo_options['ytcommentsorder'] = "В начале старые комментарии";
    $yturbo_options['ytcommentsdate'] = "enabled";
    $yturbo_options['ytcommentsdrevo'] = "enabled";
    $yturbo_options['ytpostdate'] = "enabled";
    $yturbo_options['ytexcerpt'] = "disabled";
    
    $yturbo_options['ytfeedback'] = "disabled";
    $yturbo_options['ytfeedbackselect'] = "right";
    $yturbo_options['ytfeedbackselectmesto'] = "В конце записи";
    $yturbo_options['ytfeedbacktitle'] = "Обратная связь";
    $yturbo_options['ytfeedbacknetw'] = "call,mail,vkontakte,";
    
    $yturbo_options['ytfeedbackcall'] = "";
    $yturbo_options['ytfeedbackcallback'] = "";
    $yturbo_options['ytfeedbackcallback2'] = "";
    $yturbo_options['ytfeedbackcallback3'] = "";
    $yturbo_options['ytfeedbackmail'] = "";
    $yturbo_options['ytfeedbackvkontakte'] = "";
    $yturbo_options['ytfeedbackodnoklassniki'] = "";
    $yturbo_options['ytfeedbacktwitter'] = "";
    $yturbo_options['ytfeedbackfacebook'] = "";
    $yturbo_options['ytfeedbackgoogle'] = "";
    $yturbo_options['ytfeedbackviber'] = "";
    $yturbo_options['ytfeedbackwhatsapp'] = "";
    $yturbo_options['ytfeedbacktelegram'] = "";
    
    $yturbo_options['ytseotitle'] = "disabled";
    $yturbo_options['ytseoplugin'] = "Yoast SEO";
    
    $yturbo_options['ytexcludeshortcodes'] = "disabled";
    $yturbo_options['ytexcludeshortcodeslist'] = "contact-form-7";
    

    add_option('yturbo_options', $yturbo_options);
    
    yturbo_add_feed();
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
add_action('activate_rss-for-yandex-turbo/rss-for-yandex-turbo.php', 'yturbo_init');
//функция установки значений по умолчанию при активации плагина end

//функция при деактивации плагина begin
function yturbo_on_deactivation() {
	if ( ! current_user_can('activate_plugins') ) return;
    
    //удаляем ленту плагина при деактивации плагина и обновляем пермалинки begin
    $yturbo_options = get_option('yturbo_options'); 
    if (!isset($yturbo_options['ytrssname'])) {$yturbo_options['ytrssname']="turbo";}
    global $wp_rewrite;
    if ( in_array( $yturbo_options['ytrssname'], $wp_rewrite->feeds ) ) {
       unset($wp_rewrite->feeds[array_search($yturbo_options['ytrssname'], $wp_rewrite->feeds)]);
    }
    $wp_rewrite->flush_rules();
    //удаляем ленту плагина при деактивации плагина и обновляем пермалинки end
}
register_deactivation_hook( __FILE__, 'yturbo_on_deactivation' );
//функция при деактивации плагина end

//функция при удалении плагина begin
function yturbo_on_uninstall() {
	if ( ! current_user_can('activate_plugins') ) return;
    delete_option('yturbo_options');
}
register_uninstall_hook( __FILE__, 'yturbo_on_uninstall' );
//функция при удалении плагина end

//загрузка файла локализации плагина begin
function yturbo_setup(){
    load_plugin_textdomain('rss-for-yandex-turbo');
}
add_action('init', 'yturbo_setup');
//загрузка файла локализации плагина end

//добавление ссылки "Настройки" на странице со списком плагинов begin
function yturbo_actions($links) {
	return array_merge(array('settings' => '<a href="options-general.php?page=rss-for-yandex-turbo.php">' . __('Настройки', 'rss-for-yandex-turbo') . '</a>'), $links);
}
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ),'yturbo_actions');
//добавление ссылки "Настройки" на странице со списком плагинов end

//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина begin
function yturbo_files_admin($hook_suffix) {
	$purl = plugins_url('', __FILE__);

    if ( is_admin() && $hook_suffix == 'settings_page_rss-for-yandex-turbo' ) {
    
    wp_register_script('yturbo-lettering', $purl . '/inc/jquery.lettering.js');  
    wp_register_script('yturbo-textillate', $purl . '/inc/jquery.textillate.js');  
	wp_register_style('yturbo-animate', $purl . '/inc/animate.min.css');
    wp_register_script('yturbo-script', $purl . '/inc/yturbo-script.js', array(), '1.14');  
	
	if(!wp_script_is('jquery')) {wp_enqueue_script('jquery');}
    wp_enqueue_script('yturbo-lettering');
    wp_enqueue_script('yturbo-textillate');
    wp_enqueue_style('yturbo-animate');
    wp_enqueue_script('yturbo-script');
    
    }
}
add_action('admin_enqueue_scripts', 'yturbo_files_admin');
//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина end

//функция вывода страницы настроек плагина begin
function yturbo_options_page() {
$purl = plugins_url('', __FILE__);

if (isset($_POST['submit'])) {
     
//проверка безопасности при сохранении настроек плагина begin       
if ( ! wp_verify_nonce( $_POST['yturbo_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
   wp_die(__( 'Cheatin&#8217; uh?' ));
}
//проверка безопасности при сохранении настроек плагина end
        
    //проверяем и сохраняем введенные пользователем данные begin    
    $yturbo_options = get_option('yturbo_options');
    
    if (!preg_match('/[^A-Za-z0-9]/', $_POST['ytrssname']))  {
        $yturbo_options['ytrssname'] = $_POST['ytrssname'];
        update_option('yturbo_options', $yturbo_options);
        yturbo_add_feed();
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
    
    $yturbo_options['yttitle'] = sanitize_text_field($_POST['yttitle']);
    $yturbo_options['ytlink'] = esc_url_raw($_POST['ytlink']);
    $yturbo_options['ytdescription'] = sanitize_text_field($_POST['ytdescription']);
    $yturbo_options['ytlanguage'] = sanitize_text_field($_POST['ytlanguage']);
    
    $ytnumber = sanitize_text_field($_POST['ytnumber']); 
    if (is_numeric($ytnumber)) {
        $yturbo_options['ytnumber'] = sanitize_text_field($_POST['ytnumber']);
    }
    
    if(isset($_POST['ytrazb'])){$yturbo_options['ytrazb'] = sanitize_text_field($_POST['ytrazb']);}else{$yturbo_options['ytrazb'] = 'disabled';}
    $ytrazbnumber = sanitize_text_field($_POST['ytrazbnumber']); 
    if (is_numeric($ytrazbnumber)) {
        $yturbo_options['ytrazbnumber'] = sanitize_text_field($_POST['ytrazbnumber']);
    }
    
    $yturbo_options['yttype'] = sanitize_text_field($_POST['yttype']);
    $yturbo_options['ytfigcaption'] = sanitize_text_field($_POST['ytfigcaption']);
    $yturbo_options['ytimgauthorselect'] = sanitize_text_field($_POST['ytimgauthorselect']);
    $yturbo_options['ytimgauthor'] = sanitize_text_field($_POST['ytimgauthor']);
    $yturbo_options['ytauthorselect'] = sanitize_text_field($_POST['ytauthorselect']);
    $yturbo_options['ytauthor'] = sanitize_text_field($_POST['ytauthor']);
    
    if(isset($_POST['ytthumbnail'])){$yturbo_options['ytthumbnail'] = sanitize_text_field($_POST['ytthumbnail']);}else{$yturbo_options['ytthumbnail'] = 'disabled';}
    $yturbo_options['ytselectthumb'] = sanitize_text_field($_POST['ytselectthumb']);
    
    if(isset($_POST['ytexcludetags'])){$yturbo_options['ytexcludetags'] = sanitize_text_field($_POST['ytexcludetags']);}else{$yturbo_options['ytexcludetags'] = 'disabled';}
    $yturbo_options['ytexcludetagslist'] = esc_textarea($_POST['ytexcludetagslist']);
    
    if(isset($_POST['ytexcludetags2'])){$yturbo_options['ytexcludetags2'] = sanitize_text_field($_POST['ytexcludetags2']);}else{$yturbo_options['ytexcludetags2'] = 'disabled';}
    $yturbo_options['ytexcludetagslist2'] = esc_textarea($_POST['ytexcludetagslist2']);
    
    if(isset($_POST['ytexcludecontent'])){$yturbo_options['ytexcludecontent'] = sanitize_text_field($_POST['ytexcludecontent']);}else{$yturbo_options['ytexcludecontent'] = 'disabled';}
    $yturbo_options['ytexcludecontentlist'] = addcslashes(esc_textarea($_POST['ytexcludecontentlist']), '/');
    
    if(isset($_POST['ytad1'])){$yturbo_options['ytad1'] = sanitize_text_field($_POST['ytad1']);}else{$yturbo_options['ytad1'] = 'disabled';}    
    $yturbo_options['ytad1set'] = sanitize_text_field($_POST['ytad1set']);
    $yturbo_options['ytad1rsa'] = sanitize_text_field($_POST['ytad1rsa']);
    $yturbo_options['ytadfox1'] = esc_html($_POST['ytadfox1']);

    if(isset($_POST['ytad1'])) {
        if($yturbo_options['ytad1set'] == "РСЯ" && !$yturbo_options['ytad1rsa']) {$yturbo_options['ytad1'] = 'disabled';}
        if($yturbo_options['ytad1set'] == "ADFOX" && !$yturbo_options['ytadfox1']) {$yturbo_options['ytad1'] = 'disabled';}
    }
    
    if(isset($_POST['ytad2'])){$yturbo_options['ytad2'] = sanitize_text_field($_POST['ytad2']);}else{$yturbo_options['ytad2'] = 'disabled';}
    $yturbo_options['ytad2set'] = sanitize_text_field($_POST['ytad2set']);
    $yturbo_options['ytad2rsa'] = sanitize_text_field($_POST['ytad2rsa']);
    $yturbo_options['ytadfox2'] = esc_html($_POST['ytadfox2']);

    if(isset($_POST['ytad2'])) {
        if($yturbo_options['ytad2set'] == "РСЯ" && !$yturbo_options['ytad2rsa']) {$yturbo_options['ytad2'] = 'disabled';}
        if($yturbo_options['ytad2set'] == "ADFOX" && !$yturbo_options['ytadfox2']) {$yturbo_options['ytad2'] = 'disabled';}
    }
    
    if(isset($_POST['ytad3'])){$yturbo_options['ytad3'] = sanitize_text_field($_POST['ytad3']);}else{$yturbo_options['ytad3'] = 'disabled';}
    $yturbo_options['ytad3set'] = sanitize_text_field($_POST['ytad3set']);
    $yturbo_options['ytad3rsa'] = sanitize_text_field($_POST['ytad3rsa']);
    $yturbo_options['ytadfox3'] = esc_html($_POST['ytadfox3']);

    if(isset($_POST['ytad3'])) {
        if($yturbo_options['ytad3set'] == "РСЯ" && !$yturbo_options['ytad3rsa']) {$yturbo_options['ytad3'] = 'disabled';}
        if($yturbo_options['ytad3set'] == "ADFOX" && !$yturbo_options['ytadfox3']) {$yturbo_options['ytad3'] = 'disabled';}
    }
    
    if(isset($_POST['ytad4'])){$yturbo_options['ytad4'] = sanitize_text_field($_POST['ytad4']);}else{$yturbo_options['ytad4'] = 'disabled';}
    $yturbo_options['ytad4set'] = sanitize_text_field($_POST['ytad4set']);
    $yturbo_options['ytad4rsa'] = sanitize_text_field($_POST['ytad4rsa']);
    $yturbo_options['ytadfox4'] = esc_html($_POST['ytadfox4']);

    if(isset($_POST['ytad4'])) {
        if($yturbo_options['ytad4set'] == "РСЯ" && !$yturbo_options['ytad4rsa']) {$yturbo_options['ytad4'] = 'disabled';}
        if($yturbo_options['ytad4set'] == "ADFOX" && !$yturbo_options['ytadfox4']) {$yturbo_options['ytad4'] = 'disabled';}
    }
    
    if(isset($_POST['ytad5'])){$yturbo_options['ytad5'] = sanitize_text_field($_POST['ytad5']);}else{$yturbo_options['ytad5'] = 'disabled';}
    $yturbo_options['ytad5set'] = sanitize_text_field($_POST['ytad5set']);
    $yturbo_options['ytad5rsa'] = sanitize_text_field($_POST['ytad5rsa']);
    $yturbo_options['ytadfox5'] = esc_html($_POST['ytadfox5']);

    if(isset($_POST['ytad5'])) {
        if($yturbo_options['ytad5set'] == "РСЯ" && !$yturbo_options['ytad5rsa']) {$yturbo_options['ytad5'] = 'disabled';}
        if($yturbo_options['ytad5set'] == "ADFOX" && !$yturbo_options['ytadfox5']) {$yturbo_options['ytad5'] = 'disabled';}
    }
    
    if(isset($_POST['ytrelated'])){$yturbo_options['ytrelated'] = sanitize_text_field($_POST['ytrelated']);}else{$yturbo_options['ytrelated'] = 'disabled';}
    $ytrelatednumber = sanitize_text_field($_POST['ytrelatednumber']); 
    if (is_numeric($ytrelatednumber) && (int)$ytrelatednumber<=30) {
        $yturbo_options['ytrelatednumber'] = sanitize_text_field($_POST['ytrelatednumber']);
    }
    $yturbo_options['ytrelatedselectthumb'] = sanitize_text_field($_POST['ytrelatedselectthumb']);
    if(isset($_POST['ytrelatedcache'])){$yturbo_options['ytrelatedcache'] = sanitize_text_field($_POST['ytrelatedcache']);}else{$yturbo_options['ytrelatedcache'] = 'disabled';}
    $ytrelatedcachetime = sanitize_text_field($_POST['ytrelatedcachetime']); 
    if (is_numeric($ytrelatedcachetime)) {
        $yturbo_options['ytrelatedcachetime'] = sanitize_text_field($_POST['ytrelatedcachetime']);
    }
    if(isset($_POST['ytrelatedinfinity'])){$yturbo_options['ytrelatedinfinity'] = sanitize_text_field($_POST['ytrelatedinfinity']);}else{$yturbo_options['ytrelatedinfinity'] = 'disabled';}
    if($yturbo_options['ytrelatedinfinity']=='enabled'){$yturbo_options['ytrelatedselectthumb']='Не использовать';}
    
    $ytrazmer = sanitize_text_field($_POST['ytrazmer']); 
    if (is_numeric($ytrazmer)) {
        $yturbo_options['ytrazmer'] = sanitize_text_field($_POST['ytrazmer']);
    }
    
    if(isset($_POST['ytremoveturbo'])){$yturbo_options['ytremoveturbo'] = sanitize_text_field($_POST['ytremoveturbo']);}else{$yturbo_options['ytremoveturbo'] = 'disabled';}
    
    $yturbo_options['ytcounterselect'] = sanitize_text_field($_POST['ytcounterselect']);
    $yturbo_options['ytmetrika'] = sanitize_text_field($_POST['ytmetrika']);
    $yturbo_options['ytliveinternet'] = sanitize_text_field($_POST['ytliveinternet']);
    $yturbo_options['ytgoogle'] = sanitize_text_field($_POST['ytgoogle']);
    $yturbo_options['ytmailru'] = sanitize_text_field($_POST['ytmailru']);
    $yturbo_options['ytrambler'] = sanitize_text_field($_POST['ytrambler']);
    $yturbo_options['ytmediascope'] = sanitize_text_field($_POST['ytmediascope']);
    
    $yturbo_options['ytqueryselect'] = sanitize_text_field($_POST['ytqueryselect']);
    $yturbo_options['yttaxlist'] = esc_textarea($_POST['yttaxlist']);
    $yturbo_options['ytaddtaxlist'] = esc_textarea($_POST['ytaddtaxlist']);
    
    $yturbo_options['ytselectmenu'] = sanitize_text_field($_POST['ytselectmenu']);
    if(isset($_POST['ytshare'])){$yturbo_options['ytshare'] = sanitize_text_field($_POST['ytshare']);}else{$yturbo_options['ytshare'] = 'disabled';}
    $yturbo_options['ytnetw'] = sanitize_text_field($_POST['ytnetwspan']);
    if(isset($_POST['ytgallery'])){$yturbo_options['ytgallery'] = sanitize_text_field($_POST['ytgallery']);}else{$yturbo_options['ytgallery'] = 'disabled';}
    if(isset($_POST['ytcomments'])){$yturbo_options['ytcomments'] = sanitize_text_field($_POST['ytcomments']);}else{$yturbo_options['ytcomments'] = 'disabled';}
    if(isset($_POST['ytcommentsavatar'])){$yturbo_options['ytcommentsavatar'] = sanitize_text_field($_POST['ytcommentsavatar']);}else{$yturbo_options['ytcommentsavatar'] = 'disabled';}
    $ytcommentsnumber = sanitize_text_field($_POST['ytcommentsnumber']); 
    if (is_numeric($ytcommentsnumber) && (int)$ytcommentsnumber<=40) {
        $yturbo_options['ytcommentsnumber'] = sanitize_text_field($_POST['ytcommentsnumber']);
    }
    $yturbo_options['ytcommentsorder'] = sanitize_text_field($_POST['ytcommentsorder']);
    if(isset($_POST['ytcommentsdate'])){$yturbo_options['ytcommentsdate'] = sanitize_text_field($_POST['ytcommentsdate']);}else{$yturbo_options['ytcommentsdate'] = 'disabled';}
    if(isset($_POST['ytcommentsdrevo'])){$yturbo_options['ytcommentsdrevo'] = sanitize_text_field($_POST['ytcommentsdrevo']);}else{$yturbo_options['ytcommentsdrevo'] = 'disabled';}
    if(isset($_POST['ytpostdate'])){$yturbo_options['ytpostdate'] = sanitize_text_field($_POST['ytpostdate']);}else{$yturbo_options['ytpostdate'] = 'disabled';}
    if(isset($_POST['ytexcerpt'])){$yturbo_options['ytexcerpt'] = sanitize_text_field($_POST['ytexcerpt']);}else{$yturbo_options['ytexcerpt'] = 'disabled';}
    
    if(isset($_POST['ytfeedback'])){$yturbo_options['ytfeedback'] = sanitize_text_field($_POST['ytfeedback']);}else{$yturbo_options['ytfeedback'] = 'disabled';}
    $yturbo_options['ytfeedbackselect'] = sanitize_text_field($_POST['ytfeedbackselect']);
    $yturbo_options['ytfeedbackselectmesto'] = sanitize_text_field($_POST['ytfeedbackselectmesto']);
    $yturbo_options['ytfeedbacktitle'] = sanitize_text_field($_POST['ytfeedbacktitle']);
    $yturbo_options['ytfeedbacknetw'] = sanitize_text_field($_POST['ytfeedbacknetwspan']);
    
    $yturbo_options['ytfeedbackcall'] = sanitize_text_field($_POST['ytfeedbackcall']);
    $yturbo_options['ytfeedbackcallback'] = sanitize_text_field($_POST['ytfeedbackcallback']);
    $yturbo_options['ytfeedbackcallback2'] = sanitize_text_field(htmlspecialchars($_POST['ytfeedbackcallback2']));
    $yturbo_options['ytfeedbackcallback3'] = sanitize_text_field($_POST['ytfeedbackcallback3']);
    $yturbo_options['ytfeedbackmail'] = sanitize_text_field($_POST['ytfeedbackmail']);
    $yturbo_options['ytfeedbackvkontakte'] = sanitize_text_field($_POST['ytfeedbackvkontakte']);
    $yturbo_options['ytfeedbackodnoklassniki'] = sanitize_text_field($_POST['ytfeedbackodnoklassniki']);
    $yturbo_options['ytfeedbacktwitter'] = sanitize_text_field($_POST['ytfeedbacktwitter']);
    $yturbo_options['ytfeedbackfacebook'] = sanitize_text_field($_POST['ytfeedbackfacebook']);
    $yturbo_options['ytfeedbackgoogle'] = sanitize_text_field($_POST['ytfeedbackgoogle']);
    $yturbo_options['ytfeedbackviber'] = sanitize_text_field($_POST['ytfeedbackviber']);
    $yturbo_options['ytfeedbackwhatsapp'] = sanitize_text_field($_POST['ytfeedbackwhatsapp']);
    $yturbo_options['ytfeedbacktelegram'] = sanitize_text_field($_POST['ytfeedbacktelegram']);
    
    if(isset($_POST['ytseotitle'])){$yturbo_options['ytseotitle'] = sanitize_text_field($_POST['ytseotitle']);}else{$yturbo_options['ytseotitle'] = 'disabled';}
    $yturbo_options['ytseoplugin'] = sanitize_text_field($_POST['ytseoplugin']);
    
    if(isset($_POST['ytexcludeshortcodes'])){$yturbo_options['ytexcludeshortcodes'] = sanitize_text_field($_POST['ytexcludeshortcodes']);}else{$yturbo_options['ytexcludeshortcodes'] = 'disabled';}
    $yturbo_options['ytexcludeshortcodeslist'] = esc_textarea($_POST['ytexcludeshortcodeslist']);

    update_option('yturbo_options', $yturbo_options);
    
    yturbo_clear_transients();
    //проверяем и сохраняем введенные пользователем данные end
}
yturbo_set_new_options();
$yturbo_options = get_option('yturbo_options');
?>
<?php   if (!empty($_POST) ) :
if ( ! wp_verify_nonce( $_POST['yturbo_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
   wp_die(__( 'Cheatin&#8217; uh?' ));
}
?>
<div id="message" class="updated fade"><p><strong><?php _e('Настройки сохранены.', 'rss-for-yandex-turbo') ?></strong></p></div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Настройки плагина &#171;Яндекс.Турбо&#187;', 'rss-for-yandex-turbo'); ?></h2>

<div class="metabox-holder" id="poststuff">
<div class="meta-box-sortables">

<div class="postbox">

    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e("Вам нравится этот плагин ?", 'rss-for-yandex-turbo'); ?></span></h3>
    <div class="inside" style="display: block;margin-right: 12px;">
        <img src="<?php echo $purl . '/img/icon_coffee.png'; ?>" title="<?php _e("Купить мне чашку кофе :)", 'rss-for-yandex-turbo'); ?>" style=" margin: 5px; float:left;" />
		
        <p><?php _e("Привет, меня зовут <strong>Flector</strong>.", 'rss-for-yandex-turbo'); ?></p>
        <p><?php _e("Я потратил много времени на разработку этого плагина.", 'rss-for-yandex-turbo'); ?> <br />
		<?php _e("Поэтому не откажусь от небольшого пожертвования :)", 'rss-for-yandex-turbo'); ?></p>
      <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/donate.xml?account=41001443750704&quickpay=donate&payment-type-choice=on&mobile-payment-type-choice=on&default-sum=200&targets=%D0%9D%D0%B0+%D1%80%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D1%83+WordPress-%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD%D0%BE%D0%B2.&project-name=&project-site=&button-text=05&successURL=" width="508" height="64"></iframe>
      
      <p><?php _e("Или вы можете заказать у меня услуги по WordPress, от мелких правок до создания полноценного сайта.", 'rss-for-yandex-turbo'); ?><br />
        <?php _e("Быстро, качественно и дешево. Прайс-лист смотрите по адресу <a target='new' href='https://www.wpuslugi.ru/?from=yturbo-plugin'>https://www.wpuslugi.ru/</a>.", 'rss-for-yandex-turbo'); ?></p>
        <div style="clear:both;"></div>
    </div>
</div>

<form action="" method="post">

<div class="postbox">

    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e("Настройки", 'rss-for-yandex-turbo'); ?></span></h3>
    <div class="inside" style="display: block;">

        <table class="form-table">
        
        <?php yturbo_count_feeds();  ?>
        
          <?php if ( get_option('permalink_structure') ) {
            $kor = get_bloginfo("url") .'/feed/' . '<strong>' . $yturbo_options['ytrssname'] . '</strong>/';
         } else {
            $kor = get_bloginfo("url") .'/?feed=' . '<strong>' . $yturbo_options['ytrssname']. '</strong>';
         } ?>
         
            <tr>
                <th><?php _e("Имя RSS-ленты:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytrssname" size="40" value="<?php echo $yturbo_options['ytrssname']; ?>" />
                    <br /><small><?php _e("Текущий URL RSS-ленты:", "rss-for-yandex-turbo"); ?> <tt><?php echo $kor; ?></tt><br />
                    <?php _e("Только буквы и цифры, не меняйте без необходимости.", "rss-for-yandex-turbo"); ?>
                    </small><div style="margin-bottom:20px;"></div>
                </td>
            </tr>
            <tr>
                <th><?php _e("Заголовок:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="yttitle" size="40" value="<?php echo stripslashes($yturbo_options['yttitle']); ?>" />
                    <br /><small><?php _e("Название издания.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr>
                <th><?php _e("Ссылка:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytlink" size="40" value="<?php echo stripslashes($yturbo_options['ytlink']); ?>" />
                    <br /><small><?php _e("Адрес сайта издания.", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr>
                <th><?php _e("Описание:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytdescription" size="40" value="<?php echo stripslashes($yturbo_options['ytdescription']); ?>" />
                    <br /><small><?php _e("Описание издания.", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr>
                <th><?php _e("Язык:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytlanguage" size="2" value="<?php echo stripslashes($yturbo_options['ytlanguage']); ?>" />
                    <br /><small><?php _e("Язык статей издания в стандарте <a target='new' href='https://ru.wikipedia.org/wiki/%D0%9A%D0%BE%D0%B4%D1%8B_%D1%8F%D0%B7%D1%8B%D0%BA%D0%BE%D0%B2'>ISO 639-1</a> (Россия - <strong>ru</strong>, Украина - <strong>uk</strong> и т.д.)", "rss-for-yandex-turbo"); ?> </small>
                    <div  style="margin-bottom:30px;"></div>
               </td>
            </tr>
           <tr>
                <th><?php _e("Количество записей:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytnumber" size="2" value="<?php echo stripslashes($yturbo_options['ytnumber']); ?>" />
                    <br /><small><?php _e("Общее количество записей в RSS (обязательно прочтите про <a target='new' href='https://yandex.ru/support/webmaster/turbo/feed.html#quota'>ограничения</a> Яндекса)", "rss-for-yandex-turbo"); ?> <br />
                    </small>
               </td>
            </tr>
            <tr class="razb">
                <th><?php _e("Разбитие RSS-ленты:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytrazb"><input type="checkbox" value="enabled" name="ytrazb" id="ytrazb" <?php if ($yturbo_options['ytrazb'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Включить разбитие RSS-ленты", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Плагин будет генерировать несколько RSS-лент с указанным числом записей в каждой.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Включите эту опцию, если RSS-лента слишком долго генерируется или если она превышает <a target='new' href='https://yandex.ru/support/webmaster/turbo/feed.html#quota'>ограничения</a>, установленные Яндексом.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Внимание! Не обязательно держать в Яндекс.Вебмастере максимальное количество лент (одновременно там может присутствовать <strong>10</strong> лент).", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Достаточно единоразово \"скормить\" Яндексу максимальное количество лент, а потом их можно безбоязненно удалить (турбо-страницы при этом удалены не будут).", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Таким образом можно обойти ограничение Яндекса на 5000 турбо-страниц (10 RSS-лент по 500 записей в каждой).", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("В идеале у вас должна остаться в Яндекс.Вебмастере только одна RSS-лента с 20-30 последними записями сайта и все.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Однако помните, что при добавлении новых \"турбо-фич\" вам надо будет заново \"скормить\" Яндексу максимальное количество RSS-лент.", "rss-for-yandex-turbo"); ?> <br />
                    
                    </small>
                </td>
            </tr>
            <tr class="ytrazbnumbertr" style="display:none;">
                <th><?php _e("Делить RSS-ленту по:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytrazbnumber" size="22" value="<?php echo stripslashes($yturbo_options['ytrazbnumber']); ?>" />
                    <br /><small><?php _e("Укажите число записей, по которому лента будет делиться.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Для получения ссылок на ваши RSS-ленты сохраните настройки плагина.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Важно: разбитие не будет работать, если на вашем сайте нет необходимого числа записей.", "rss-for-yandex-turbo"); ?> <br />
               </td>
            </tr>
            <tr>
                <th style="padding-top: 40px;"><?php _e("Дата записей:", 'rss-for-yandex-turbo') ?></th>
                <td style="padding-top: 40px;">
                    <label for="ytpostdate"><input type="checkbox" value="enabled" name="ytpostdate" id="ytpostdate" <?php if ($yturbo_options['ytpostdate'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Указать дату публикации записей", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Выводить или не выводить дату публикации записей в ленте.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
                </td>
            </tr>
            <tr>
                <th><?php _e("Отрывок записей:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytexcerpt"><input type="checkbox" value="enabled" name="ytexcerpt" id="ytexcerpt" <?php if ($yturbo_options['ytexcerpt'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Добавить в начало записей \"отрывок\"", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Используйте эту опцию только в случае необходимости.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Например, когда \"отрывок\" (цитата) записи содержит контент, которого нет в самой записи.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
                </td>
            </tr>
            <tr class="ytseotitletr">
                <th><?php _e("Заголовок записей:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytseotitle"><input type="checkbox" value="enabled" name="ytseotitle" id="ytseotitle" <?php if ($yturbo_options['ytseotitle'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Использовать данные из SEO-плагинов", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("В качестве заголовка записи (тег <tt>&lt;h1&gt;</tt>) будет использован заголовок записи из выбранного SEO-плагина.", "rss-for-yandex-turbo"); ?><br /> 
                    <?php _e("Эта опция меняет только видимый пользователями тег <tt>&lt;h1&gt;</tt> и не затрагивает RSS-теги <tt>&lt;title&gt;</tt> и <tt>&lt;turbo:topic&gt;</tt>.", "rss-for-yandex-turbo"); ?><br /> 
                    </small>
                </td>
            </tr>
            <tr class="ytseoplugintr" style="display:none;">
                <th><?php _e("SEO-плагин:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <select name="ytseoplugin" style="width: 250px;">
                        <option value="Yoast SEO" <?php if ($yturbo_options['ytseoplugin'] == 'Yoast SEO') echo "selected='selected'" ?>><?php _e("Yoast SEO", "rss-for-yandex-turbo"); ?></option>
                        <option value="All in One SEO Pack" <?php if ($yturbo_options['ytseoplugin'] == 'All in One SEO Pack') echo "selected='selected'" ?>><?php _e("All in One SEO Pack", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Выберите используемый вами SEO-плагин. <br /> Если заголовок записи в SEO-плагине не установлен, то будет использован стандартный заголовок.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="ytthumbnailtr">
                <th><?php _e("Миниатюра в RSS:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytthumbnail"><input type="checkbox" value="enabled" name="ytthumbnail" id="ytthumbnail" <?php if ($yturbo_options['ytthumbnail'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Добавить миниатюру к заголовку записи", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("В заголовок записи (RSS-тег <tt>&lt;header></tt>) будет добавлена миниатюра записи (изображение записи).", "rss-for-yandex-turbo"); ?> 
                    </small>
                </td>
            </tr>
            <tr class="ytselectthumbtr" style="display:none;">
                <th><?php _e("Размер миниатюры в RSS:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <select name="ytselectthumb" style="width: 250px;">
                        <?php $image_sizes = get_intermediate_image_sizes(); ?>
                        <?php foreach ($image_sizes as $size_name): ?>
                            <option value="<?php echo $size_name ?>" <?php if ($yturbo_options['ytselectthumb'] == $size_name) echo "selected='selected'" ?>><?php echo $size_name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br /><small><?php _e("Выберите нужный размер миниатюры (в списке находятся все зарегистрированные на сайте размеры миниатюр). ", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr>
                <th><?php _e("Автор записей:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <select name="ytauthorselect" id="ytauthorselect" style="width: 250px;">
                        <option value="Автор записи" <?php if ($yturbo_options['ytauthorselect'] == 'Автор записи') echo "selected='selected'" ?>><?php _e("Автор записи", "rss-for-yandex-turbo"); ?></option>
                        <option value="Указать автора" <?php if ($yturbo_options['ytauthorselect'] == 'Указать автора') echo "selected='selected'" ?>><?php _e("Указать автора", "rss-for-yandex-turbo"); ?></option>
                        <option value="Отключить указание автора" <?php if ($yturbo_options['ytauthorselect'] == 'Отключить указание автора') echo "selected='selected'" ?>><?php _e("Отключить указание автора", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Автор записей (RSS-тег <tt>&lt;author></tt> - для сервиса Яндекс.Турбо данный тег не является обязательным). ", "rss-for-yandex-turbo"); ?> <br />
                    </small>
               </td>
            </tr>
            <tr id="ownname2" style="display:none;">
                <th><?php _e("Автор записей:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytauthor" size="20" value="<?php echo stripslashes($yturbo_options['ytauthor']); ?>" />
                    <br /><small><?php _e("Произвольное имя автора записей (если не заполнено, то будет использовано имя автора записи).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr>
                <th><?php _e("Описания изображений:", 'rss-for-yandex-turbo') ?></th>
                <td>
                     <select name="ytfigcaption" id="capalt" style="width: 250px;">
                        <option value="Использовать alt по возможности" <?php if ($yturbo_options['ytfigcaption'] == 'Использовать alt по возможности') echo "selected='selected'" ?>><?php _e("Использовать alt по возможности", "rss-for-yandex-turbo"); ?></option>
                        <option value="Использовать название записи" <?php if ($yturbo_options['ytfigcaption'] == 'Использовать название записи') echo "selected='selected'" ?>><?php _e("Использовать название записи", "rss-for-yandex-turbo"); ?></option>
                        <option value="Отключить описания" <?php if ($yturbo_options['ytfigcaption'] == 'Отключить описания') echo "selected='selected'" ?>><?php _e("Отключить описания", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Разметка \"описания\" для изображений (<tt>&lt;figcaption>Описание&lt;/figcaption></tt>).", "rss-for-yandex-turbo"); ?> <br />
                    <span id="altimg"><?php _e("В случае отсутствия у изображения alt-атрибута для описания изображения будет использовано название записи.", "rss-for-yandex-turbo"); ?> </span></small>
                </td>
            </tr>
            <tr>
                <th><?php _e("Автор изображений:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <select name="ytimgauthorselect" id="imgselect" style="width: 250px;">
                        <option value="Автор записи" <?php if ($yturbo_options['ytimgauthorselect'] == 'Автор записи') echo "selected='selected'" ?>><?php _e("Автор записи", "rss-for-yandex-turbo"); ?></option>
                        <option value="Указать автора" <?php if ($yturbo_options['ytimgauthorselect'] == 'Указать автора') echo "selected='selected'" ?>><?php _e("Указать автора", "rss-for-yandex-turbo"); ?></option>
                        <option value="Отключить указание автора" <?php if ($yturbo_options['ytimgauthorselect'] == 'Отключить указание автора') echo "selected='selected'" ?>><?php _e("Отключить указание автора", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Разметка \"автора\" для изображений (<tt>&lt;span class=\"copyright\">Автор&lt;/span></tt>).", "rss-for-yandex-turbo"); ?> <br />
                    </small>
               </td>
            </tr>
            <tr id="ownname" style="display:none;">
                <th><?php _e("Автор изображений:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytimgauthor" size="20" value="<?php echo stripslashes($yturbo_options['ytimgauthor']); ?>" />
                    <br /><small><?php _e("Автор изображений (если не заполнено, то будет использовано имя автора записи).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Сохранить настройки &raquo;', 'rss-for-yandex-turbo'); ?>" />
                </td>
            </tr> 
        </table>
    </div>
</div>

<div class="postbox">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('Блоки Яндекс.Турбо', 'rss-for-yandex-turbo'); ?></span></h3>
	  <div class="inside" style="padding-bottom:15px;display: block;">
     
        <table class="form-table">
        
            <tr class="ytselectmenutr">
                <th><?php _e("Меню:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <select name="ytselectmenu" style="width: 250px;">
                        <?php $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );  ?>  
                        <?php foreach ($menus as $menu): ?>
                            <option value="<?php echo $menu->name; ?>" <?php if ($yturbo_options['ytselectmenu'] == $menu->name) echo "selected='selected'" ?>><?php echo $menu->name; ?></option>
                        <?php endforeach; ?>
                        <option value="Не использовать" <?php if ($yturbo_options['ytselectmenu'] == 'Не использовать') echo "selected='selected'"; ?>><?php echo 'Не использовать'; ?></option>
                    </select>
                    <?php $menulink = get_bloginfo("url") .'/wp-admin/nav-menus.php'; ?> 
                    <br /><small><?php _e("Выберите меню для использования на турбо-страницах (создать меню можно на вкладке ", "rss-for-yandex-turbo"); ?> "<a target='new' href="<?php echo $menulink; ?>"><?php _e("Внешний вид \ Меню", "rss-for-yandex-turbo"); ?></a>").


                   <br />
                    <?php _e("Меню должно быть ограничено <strong>10</strong> ссылками без иерархии (пример смотреть <a target='new' href='https://yandex.ru/support/webmaster/turbo/rss-elements.html#turbo-content-details__menu'>здесь</a>).", "rss-for-yandex-turbo"); ?>
                    </small>
                </td>
            </tr>
            <tr class="ytsharetr trbordertop">
                <th><?php _e("Блок \"Поделиться\":", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytshare"><input type="checkbox" value="enabled" name="ytshare" id="ytshare" <?php if ($yturbo_options['ytshare'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Добавить блок \"Поделиться\" на турбо-страницы", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Этот блок будет добавлен в конце записи (пример смотреть <a target='new' href='https://yandex.ru/support/webmaster/turbo/rss-elements.html#turbo-content-details__share'>здесь</a>).", "rss-for-yandex-turbo"); ?> 
                    </small>
                </td>
            </tr>
            <tr class="ytsharechildtr" style="display:none;">
                <th><?php _e("Социальные сети:", 'rss-for-yandex-turbo') ?></th>
                <td style="padding:0;">
                   
                   <table>
                   <tr style="margin-left:-5px;">
                   
                   <td>
                    <label for="facebook"><img title="Facebook" src="<?php echo $purl . '/img/facebook.png'; ?>" style="margin-bottom: 5px;width:48px;height:48px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks[]" id="facebook" style="margin-left:16px;" />
                   </td>
                   
                   <td>
                    <label for="twitter"><img title="Twitter" src="<?php echo $purl . '/img/twitter.png'; ?>" style="margin-bottom: 5px;width:48px;height:48px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks[]" id="twitter" style="margin-left:16px;" />
                   </td>
                   
                   <td>
                    <label for="google"><img title="Google Plus" src="<?php echo $purl . '/img/google.png'; ?>" style="margin-bottom: 5px;width:48px;height:48px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks[]" id="google" style="margin-left:16px;" />
                   </td>

                   <td>
                    <label for="odnoklassniki"><img title="Odnoklassniki" src="<?php echo $purl . '/img/odnoklassniki.png'; ?>" style="margin-bottom: 5px;width:48px;height:48px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks[]" id="odnoklassniki" style="margin-left:16px;">
                   </td>
                   
                   <td>
                    <label for="vkontakte"><img title="VKontakte" src="<?php echo $purl . '/img/vk.png'; ?>" style="margin-bottom: 5px;width:48px;height:48px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks[]" id="vkontakte" style="margin-left:16px;" />
                   </td>
                   
                   <td>
                    <label for="telegram"><img title="Telegram" src="<?php echo $purl . '/img/telegram.png'; ?>" style="margin-bottom: 5px;width:48px;height:48px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks[]" id="telegram" style="margin-left:16px;" />
                   </td>
                   
     
                  </tr>
                  </table>
                </td>
                
            </tr>
            <tr class="ytsharechildtr" style="display:none;">
                <th><?php _e("Порядок:", "rss-for-yandex-turbo") ?></th>
                <td>
                   <input style="" type="text" name="ytnetw" id="ytnetw" size="62" value="<?php echo $yturbo_options['ytnetw']; ?>" disabled="disabled" />
                   <input type="text" style="display:none;"  name="ytnetwspan" id="ytnetwspan" value="<?php echo $yturbo_options['ytnetw']; ?>"/>
                    <br /><small style=""><?php _e("Для сортировки иконок сначала снимите все чекбоксы, а потом снова их выберите в нужном вам порядке.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="ytfeedbacktr trbordertop">
                <th><?php _e("Блок обратной связи:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytfeedback"><input type="checkbox" value="enabled" name="ytfeedback" id="ytfeedback" <?php if ($yturbo_options['ytfeedback'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Добавить блок обратной связи на турбо-страницы", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("На турбо-страницы будет добавлен блок обратной связи в выбранном вами месте (пример смотреть <a target='new' href='https://yandex.ru/support/webmaster/turbo/rss-elements.html#turbo-content-details__feedback'>здесь</a>).", "rss-for-yandex-turbo"); ?> 
                    </small>
                </td>
            </tr>
            <tr class="ytfeedbackchildtr" style="display:none;">
                <th><?php _e("Выравнивание блока:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <select name="ytfeedbackselect" id="ytfeedbackselect" style="width: 250px;">
                        <option value="left" <?php if ($yturbo_options['ytfeedbackselect'] == 'left') echo "selected='selected'" ?>><?php _e("Слева", "rss-for-yandex-turbo"); ?></option>
                        <option value="right" <?php if ($yturbo_options['ytfeedbackselect'] == 'right') echo "selected='selected'" ?>><?php _e("Справа", "rss-for-yandex-turbo"); ?></option>
                        <option value="false" <?php if ($yturbo_options['ytfeedbackselect'] == 'false') echo "selected='selected'" ?>><?php _e("В указанном месте", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Укажите где именно на турбо-страницах должен выводиться блок обратной связи.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("При выравнивании по левому или правому краю страницы можно разместить лишь <strong>4</strong> кнопки связи.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
               </td>
            </tr>
            <tr class="ytfeedbackselectmestotr" style="display:none;">
                <th><?php _e("Расположить блок:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <select name="ytfeedbackselectmesto" id="ytfeedbackselectmesto" style="width: 250px;">
                        <option value="В начале записи" <?php if ($yturbo_options['ytfeedbackselectmesto'] == 'В начале записи') echo "selected='selected'" ?>><?php _e("В начале записи", "rss-for-yandex-turbo"); ?></option>
                        <option value="В конце записи" <?php if ($yturbo_options['ytfeedbackselectmesto'] == 'В конце записи') echo "selected='selected'" ?>><?php _e("В конце записи", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("В начале записи блок будет расположен после заголовка, а в конце записи после блока \"Поделиться\".", "rss-for-yandex-turbo"); ?> <br />
                    </small>
               </td>
            </tr>
            <tr class="ytfeedbackselectmestotr" style="display:none;">
                <th><?php _e("Заголовок блока:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbacktitle" size="30" value="<?php echo stripslashes($yturbo_options['ytfeedbacktitle']); ?>" />
                    <br /><small><?php _e("Укажите заголовок блока (используется только при выводе блока в указанном месте).", "rss-for-yandex-turbo"); ?><br />
                    </small>
               </td>
            </tr>
            <tr class="ytfeedbackchildtr" style="display:none;">
                <th><?php _e("Кнопки связи:", 'rss-for-yandex-turbo') ?></th>
                <td style="padding:0;">
                   
                   <table>
                   <tr style="margin-left:-5px;">
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackcall"><img title="Звонок" src="<?php echo $purl . '/img/feedback/call.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackcall" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackcallback"><img title="Контактная форма" src="<?php echo $purl . '/img/feedback/callback.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackcallback" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackchat"><img title="Чат" src="<?php echo $purl . '/img/feedback/chat.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackchat" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackmail"><img title="E-mail" src="<?php echo $purl . '/img/feedback/mail.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackmail" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackvkontakte"><img title="VKontakte" src="<?php echo $purl . '/img/feedback/vkontakte.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackvkontakte" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackodnoklassniki"><img title="Odnoklassniki" src="<?php echo $purl . '/img/feedback/odnoklassniki.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackodnoklassniki" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbacktwitter"><img title="Twitter" src="<?php echo $purl . '/img/feedback/twitter.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbacktwitter" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackfacebook"><img title="Facebook" src="<?php echo $purl . '/img/feedback/facebook.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackfacebook" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackgoogle"><img title="Google Plus" src="<?php echo $purl . '/img/feedback/google.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackgoogle" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackviber"><img title="Viber" src="<?php echo $purl . '/img/feedback/viber.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackviber" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbackwhatsapp"><img title="Whatsapp" src="<?php echo $purl . '/img/feedback/whatsapp.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbackwhatsapp" style="margin-left:16px;" />
                   </td>
                   
                   <td style="padding: 15px 3px;">
                    <label for="feedbacktelegram"><img title="Telegram" src="<?php echo $purl . '/img/feedback/telegram.png'; ?>" style="margin-bottom: 5px;width:52px;height:52px; vertical-align: middle; " /><br /></label>
                    <input type="checkbox" name="networks2[]" id="feedbacktelegram" style="margin-left:16px;" />
                   </td>
                   
                  </tr>
                  </table>
                </td> 
            </tr>
            <tr class="ytfeedbackchildtr" style="display:none;">
                <th><?php _e("Порядок кнопок:", "rss-for-yandex-turbo") ?></th>
                <td>
                   <input style="" type="text" name="ytfeedbacknetw" id="ytfeedbacknetw" size="62" value="<?php echo $yturbo_options['ytfeedbacknetw']; ?>" disabled="disabled" />
                   <input type="text" style="display:none;"  name="ytfeedbacknetwspan" id="ytfeedbacknetwspan" value="<?php echo $yturbo_options['ytfeedbacknetw']; ?>"/>
                    <br /><small style=""><?php _e("Для сортировки иконок сначала снимите все чекбоксы, а потом снова их выберите в нужном вам порядке.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="ytfeedbackchildtr ytfeedbackcontactstr">
                <th><?php _e("Контакты для кнопок:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <select name="ytfeedbackcontacts" id="ytfeedbackcontacts" style="width: 250px;">
                        <option value="myselect" selected='true'><?php _e("- Выбрать -", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackcall"><?php _e("Звонок", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackcallback"><?php _e("Контактная форма", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackchat"><?php _e("Чат", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackmail"><?php _e("E-mail", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackvkontakte"><?php _e("VKontakte", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackodnoklassniki"><?php _e("Odnoklassniki", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbacktwitter"><?php _e("Twitter", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackfacebook"><?php _e("Facebook", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackgoogle"><?php _e("Google Plus", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackviber"><?php _e("Viber", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbackwhatsapp"><?php _e("Whatsapp", "rss-for-yandex-turbo"); ?></option>
                        <option disabled="disabled" value="feedbacktelegram"><?php _e("Telegram", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Для установки контактов выберите нужную кнопку из списка (доступны только отмеченные кнопки связи).", "rss-for-yandex-turbo"); ?> <br />
                    </small>
               </td>
            </tr>
            <tr class="ytfeedbackcalltr" style="display:none;">
                <th><?php _e("Звонок:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackcall" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackcall']); ?>" />
                    <br /><small><?php _e("Укажите телефонный номер в международном формате (пример: <tt>+74951234567</tt>).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytfeedbackcallbacktr" style="display:none;">
                <th><?php _e("Email для контактной формы:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackcallback" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackcallback']); ?>" />
                    <br /><small><?php _e("Укажите адрес e-mail (пример: <tt>mail@test.ru</tt>).", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Разрешено указывать только e-mail, принадлежащий вашему домену.", "rss-for-yandex-turbo"); ?><br />
                    </small>
               </td>
            </tr>
            <tr class="ytfeedbackcallbacktr" style="display:none;">
                <th><?php _e("Название организации:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackcallback2" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackcallback2']); ?>" />
                    <br /><small><?php _e("Укажите юридическое название вашей организации (пример: <tt>ООО «Ромашка»</tt>).", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("* При заполнении требуется указать ссылку на пользовательское соглашении.", "rss-for-yandex-turbo"); ?><br />
                    </small>
               </td>
            </tr>
            <tr class="ytfeedbackcallbacktr" style="display:none;">
                <th><?php _e("Пользовательское соглашение:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackcallback3" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackcallback3']); ?>" />
                    <br /><small><?php _e("Укажите ссылку на пользовательское соглашение о предоставлении обратной связи.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("* При заполнении требуется указать юридическое название вашей организации.", "rss-for-yandex-turbo"); ?><br />
                    </small>
               </td>
            </tr>
            <tr class="ytfeedbackchattr" style="display:none;">
                <th><?php _e("Чат:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input disabled="disabled" type="text" name="ytfeedbackchat" size="40" value="" />
                    <br /><small><?php _e("Указывать ничего не надо, если вы создали чат для своего сайта.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Справку о том, как создать \"Чат для бизнеса\" читайте <a target='new' href='https://yandex.ru/support/webmaster/turbo/rss-elements.html#turbo-content-details__chat'>здесь</a>.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
               </td>
            </tr>
            <tr class="ytfeedbackmailtr" style="display:none;">
                <th><?php _e("E-mail:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackmail" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackmail']); ?>" />
                    <br /><small><?php _e("Укажите адрес e-mail (пример: <tt>mail@test.ru</tt>).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytfeedbackvkontaktetr" style="display:none;">
                <th><?php _e("VKontakte:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackvkontakte" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackvkontakte']); ?>" />
                    <br /><small><?php _e("Укажите урл (профиль, группа или чат) ВКонтакте (пример для чата: <tt>https://vk.me/123456</tt>, где <tt>123456</tt> это ваш аккаунт).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytfeedbackodnoklassnikitr" style="display:none;">
                <th><?php _e("Odnoklassniki:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackodnoklassniki" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackodnoklassniki']); ?>" />
                    <br /><small><?php _e("Укажите урл (профиль или группа) Одноклассники (пример для профиля: <tt>https://www.ok.ru/profile/123456</tt>, где <tt>123456</tt> это ваш аккаунт).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytfeedbacktwittertr" style="display:none;">
                <th><?php _e("Twitter:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbacktwitter" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbacktwitter']); ?>" />
                    <br /><small><?php _e("Укажите урл профиля Twitter (пример: <tt>https://twitter.com/yandex</tt>, где <tt>yandex</tt> это ваш аккаунт).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytfeedbackfacebooktr" style="display:none;">
                <th><?php _e("Facebook:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackfacebook" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackfacebook']); ?>" />
                    <br /><small><?php _e("Укажите урл (профиль, группа или чат) Facebook (пример для профиля: <tt>https://www.facebook.com/yandex</tt>, где <tt>yandex</tt> это ваш аккаунт).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytfeedbackgoogletr" style="display:none;">
                <th><?php _e("Google Plus:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackgoogle" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackgoogle']); ?>" />
                    <br /><small><?php _e("Укажите урл профиля Google Plus (пример: <tt>https://plus.google.com/123456</tt>, где <tt>123456</tt> это ваш аккаунт).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytfeedbackvibertr" style="display:none;">
                <th><?php _e("Viber:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackviber" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackviber']); ?>" />
                    <br /><small><?php _e("Укажите урл связи для Viber (пример для чата: <tt>viber://chat?number=+74951234567</tt>, где <tt>+74991234567</tt> это ваш номер телефона).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytfeedbackwhatsapptr" style="display:none;">
                <th><?php _e("Whatsapp:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbackwhatsapp" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbackwhatsapp']); ?>" />
                    <br /><small><?php _e("Укажите урл связи для Whatsapp (пример: <tt>whatsapp://send?phone=74951234567</tt>, где <tt>74951234567</tt> это ваш номер телефона).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytfeedbacktelegramtr" style="display:none;">
                <th><?php _e("Telegram:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytfeedbacktelegram" size="40" value="<?php echo stripslashes($yturbo_options['ytfeedbacktelegram']); ?>" />
                    <br /><small><?php _e("Укажите урл связи для Telegram (пример: <tt>https://t.me/123456</tt>, где <tt>123456</tt> это ваш аккаунт).", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytgallerytr trbordertop">
                <th><?php _e("Галереи:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytgallery"><input type="checkbox" value="enabled" name="ytgallery" id="ytgallery" <?php if ($yturbo_options['ytgallery'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Использовать галереи", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Данная опция преобразует стандартные галереи WordPress в турбо-галереи (пример смотреть <a target='new' href='https://yandex.ru/support/webmaster/turbo/rss-elements.html#turbo-content-details__gallery'>здесь</a>).", "rss-for-yandex-turbo"); ?> 
                    </small>
                </td>
            </tr>
            <tr class="ytcommentstr trbordertop">
                <th><?php _e("Комментарии:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytcomments"><input type="checkbox" value="enabled" name="ytcomments" id="ytcomments" <?php if ($yturbo_options['ytcomments'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Добавить комментарии к турбо-страницам", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("К записям на турбо-страницах будут добавлены комментарии (пример смотреть <a target='new' href='https://yandex.ru/support/webmaster/turbo/rss-elements.html#turbo-content-details__comments'>здесь</a>).", "rss-for-yandex-turbo"); ?> 
                    </small>
                </td>
            </tr>
            <tr class="ytcommentschildtr" style="display:none;">
                <th><?php _e("Аватары:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytcommentsavatar"><input type="checkbox" value="enabled" name="ytcommentsavatar" id="ytcommentsavatar" <?php if ($yturbo_options['ytcommentsavatar'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Добавить аватары к комментариям", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Выводить или нет аватары (граватары) к комментариям.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Внимание! Картинки аватаров могут не уложиться в лимит изображений на одну запись (не более <strong>30</strong> штук).", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("В случае отключения вывода аватаров Яндекс выведет на месте аватаров картинку-заглушку.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
                </td>
            </tr>
            <tr class="ytcommentschildtr" style="display:none;">
                <th><?php _e("Число комментариев:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytcommentsnumber" size="2" value="<?php echo stripslashes($yturbo_options['ytcommentsnumber']); ?>" />
                    <br /><small><?php _e("Укажите число выводимых комментариев (максимально можно выводить только <strong>40</strong> комментариев).", "rss-for-yandex-turbo"); ?> <br/>
                    </small>
               </td>
            </tr>
            <tr class="ytcommentschildtr" style="display:none;">
                <th><?php _e("Сортировка:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <select name="ytcommentsorder" id="ytcommentsorder" style="width: 250px;">
                        <option value="В начале новые комментарии" <?php if ($yturbo_options['ytcommentsorder'] == 'В начале новые комментарии') echo "selected='selected'" ?>><?php _e("В начале новые комментарии", "rss-for-yandex-turbo"); ?></option>
                        <option value="В начале старые комментарии" <?php if ($yturbo_options['ytcommentsorder'] == 'В начале старые комментарии') echo "selected='selected'" ?>><?php _e("В начале старые комментарии", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Сортировка комментариев по дате добавления.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Учтите, что при использовании древовидных комментариев сортировка визуально может быть нарушена.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
               </td>
            </tr>
            <tr class="ytcommentschildtr" style="display:none;">
                <th><?php _e("Дата комментариев:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytcommentsdate"><input type="checkbox" value="enabled" name="ytcommentsdate" id="ytcommentsdate" <?php if ($yturbo_options['ytcommentsdate'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Добавить дату к комментариям", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Указывать дату для комментариев по <a target='new' href='https://yandex.ru/support/webmaster/turbo/rss-elements.html#turbo-content-details__comments'>спецификации</a> Яндекса необязательно.", "rss-for-yandex-turbo"); ?> 
                    </small>
                </td>
            </tr>
            <tr class="ytcommentschildtr" style="display:none;">
                <th><?php _e("Древовидность:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytcommentsdrevo"><input type="checkbox" value="enabled" name="ytcommentsdrevo" id="ytcommentsdrevo" <?php if ($yturbo_options['ytcommentsdrevo'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Использовать древовидность", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Выводить или не выводить комментарии в древовидном виде.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Поддерживается древовидность только для 2 уровней глубины.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Учтите, что отключение древовидности не повлияет на сортировку комментариев.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
                </td>
            </tr>
            <tr class="ytrelatedtr trbordertop">
                <th><?php _e("Похожие записи:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytrelated"><input type="checkbox" value="enabled" name="ytrelated" id="ytrelated" <?php if ($yturbo_options['ytrelated'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Добавить блок похожих записей на турбо-страницы", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("На турбо-страницы будет добавлен блок похожих записей (RSS-тег <tt>&lt;yandex:related></tt>).", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="ytrelatedchildtr" style="display:none;">
                <th><?php _e("Количество похожих записей:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytrelatednumber" size="2" value="<?php echo stripslashes($yturbo_options['ytrelatednumber']); ?>" />
                    <br /><small><?php _e("Укажите число записей в блоке похожих записей.", "rss-for-yandex-turbo"); ?> <br >
                    <?php _e("Список похожих записей будет формироваться случайным образом из записей рубрики текущей записи.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Внимание! Не устанавливайте слишком большое число похожих записей, если вы используете вместе с ними вывод миниатюр.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Лимит Яндекса на общее количество изображений одной страницы - <strong>30</strong> (миниатюры похожих записей тоже учитываются).", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Больше <strong>30</strong> похожих записей установить нельзя (тоже лимит Яндекса на количество ссылок в блоке похожих записей).", "rss-for-yandex-turbo"); ?><br />
                    </small>
               </td>
            </tr>
            <tr class="ytrelatedchildtr" style="display:none;">
                <th><?php _e("Миниатюра для похожих записей:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <select name="ytrelatedselectthumb" style="width: 250px;">
                        <?php $image_sizes = get_intermediate_image_sizes(); ?>
                        <?php foreach ($image_sizes as $size_name): ?>
                            <option value="<?php echo $size_name ?>" <?php if ($yturbo_options['ytrelatedselectthumb'] == $size_name) echo "selected='selected'"; ?>><?php echo $size_name ?></option>
                        <?php endforeach; ?>
                            <option value="Не использовать" <?php if ($yturbo_options['ytrelatedselectthumb'] == 'Не использовать') echo "selected='selected'"; ?>><?php echo 'Не использовать'; ?></option>
                    </select>
                    <br /><small><?php _e("Выберите нужный размер миниатюры (в списке находятся все зарегистрированные на сайте размеры миниатюр). ", "rss-for-yandex-turbo"); ?> <br /><?php _e("Вывод миниатюр для похожих записей можно отключить.", "rss-for-yandex-turbo"); ?><br />
                    
                    </small>
                </td>
            </tr>
            <tr class="ytrelatedchildtr" style="display:none;">
                <th><?php _e("Непрерывная лента статей:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytrelatedinfinity"><input type="checkbox" value="enabled" name="ytrelatedinfinity" id="ytrelatedinfinity" <?php if ($yturbo_options['ytrelatedinfinity'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Включить непрерывную ленту статей", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Вместо обычного короткого списка похожих статей будет выводиться непрерывная лента из полных записей (пример смотреть <a target='new' href='https://yandex.ru/support/webmaster/turbo/rss-elements.html#item__infinity'>здесь</a>).", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("При включении непрерывной ленты статей вывод миниатюр для похожих записей будет отключен.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
                </td>
            </tr>
            <tr class="ytrelatedchildtr" style="display:none;">
                <th><?php _e("Кэширование:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytrelatedcache"><input type="checkbox" value="enabled" name="ytrelatedcache" id="ytrelatedcache" <?php if ($yturbo_options['ytrelatedcache'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Кэшировать список похожих записей", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Для ускорения генерирования RSS-ленты вы можете включить кэширование списка похожих записей.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="ytrelatedchildtr ytcachetime" style="display:none;">
                <th><?php _e("Время жизни кэша:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytrelatedcachetime" size="22" value="<?php echo stripslashes($yturbo_options['ytrelatedcachetime']); ?>" />
                    <br /><small><?php _e("Укажите время жизни кэша (в часах).", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Внимание! Любое изменение настроек плагина скинет кэш похожих записей.", "rss-for-yandex-turbo"); ?><br />
               </td>
            </tr>

            <tr class="trbordertop">
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Сохранить настройки &raquo;', 'rss-for-yandex-turbo'); ?>" />
                </td>
            </tr>
            
        </table>
    </div>
</div>

<div class="postbox">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('Реклама и счетчики', 'rss-for-yandex-turbo'); ?></span></h3>
	  <div class="inside" style="padding-bottom:15px;display: block;">
     
        <table class="form-table">
        
            <tr class="counters">
                <th><?php _e("Счетчики:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <select name="ytcounterselect" id="ytcounterselect" style="width: 250px;">
                        <option value="Яндекс.Метрика" <?php if ($yturbo_options['ytcounterselect'] == 'Яндекс.Метрика') echo "selected='selected'" ?>><?php _e("Яндекс.Метрика", "rss-for-yandex-turbo"); ?></option>
                        <option value="LiveInternet" <?php if ($yturbo_options['ytcounterselect'] == 'LiveInternet') echo "selected='selected'" ?>><?php _e("LiveInternet", "rss-for-yandex-turbo"); ?></option>
                        <option value="Google Analytics" <?php if ($yturbo_options['ytcounterselect'] == 'Google Analytics') echo "selected='selected'" ?>><?php _e("Google Analytics", "rss-for-yandex-turbo"); ?></option>
                        <option value="Рейтинг Mail.RU" <?php if ($yturbo_options['ytcounterselect'] == 'Рейтинг Mail.RU') echo "selected='selected'" ?>><?php _e("Рейтинг Mail.RU", "rss-for-yandex-turbo"); ?></option>
                        <option value="Rambler Топ-100" <?php if ($yturbo_options['ytcounterselect'] == 'Rambler Топ-100') echo "selected='selected'" ?>><?php _e("Rambler Топ-100", "rss-for-yandex-turbo"); ?></option>
                        <option value="Mediascope (TNS)" <?php if ($yturbo_options['ytcounterselect'] == 'Mediascope (TNS)') echo "selected='selected'" ?>><?php _e("Mediascope (TNS)", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Выберите нужный счетчик и укажите его идентификатор. <br /> В ленте будут использованы все указанные вами счетчики.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
               </td>
            </tr>
            <tr class="metrika" style="display:none;">
                <th><?php _e("Яндекс.Метрика:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytmetrika" size="22" value="<?php echo stripslashes($yturbo_options['ytmetrika']); ?>" />
                    <br /><small><?php _e("Укажите <strong>ID</strong> счетчика Яндекс.Метрики (например: <tt>33382498</tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-counter-id.html'>как узнать ID счетчика</a>).", "rss-for-yandex-turbo"); ?> </small>
                    <?php if ($yturbo_options['ytad1'] == 'enabled') echo '<div style="margin-top:30px"></div>'; ?>
               </td>
            </tr>
            <tr class="liveinternet" style="display:none;">
                <th><?php _e("LiveInternet:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytliveinternet" size="22" value="<?php echo stripslashes($yturbo_options['ytliveinternet']); ?>" />
                    <br /><small><?php _e("Укажите <strong>ID</strong> счетчика LiveInternet (например: <tt>site.ru</tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-counter-id.html'>как узнать ID счетчика</a>).", "rss-for-yandex-turbo"); ?> </small>
                    <?php if ($yturbo_options['ytad1'] == 'enabled') echo '<div style="margin-top:30px"></div>'; ?>
               </td>
            </tr>
            <tr class="google" style="display:none;">
                <th><?php _e("Google Analytics:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytgoogle" size="22" value="<?php echo stripslashes($yturbo_options['ytgoogle']); ?>" />
                    <br /><small><?php _e("Укажите <strong>ID</strong> счетчика Google Analytics (например: <tt>UA-12340005-6</tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-counter-id.html'>как узнать ID счетчика</a>).", "rss-for-yandex-turbo"); ?> </small>
                    <?php if ($yturbo_options['ytad1'] == 'enabled') echo '<div style="margin-top:30px"></div>'; ?>
               </td>
            </tr>
            <tr class="mailru" style="display:none;">
                <th><?php _e("Рейтинг Mail.RU:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytmailru" size="22" value="<?php echo stripslashes($yturbo_options['ytmailru']); ?>" />
                    <br /><small><?php _e("Укажите <strong>ID</strong> счетчика Рейтинг Mail.RU (например: <tt>123456</tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-counter-id.html'>как узнать ID счетчика</a>).", "rss-for-yandex-turbo"); ?> </small>
                    <?php if ($yturbo_options['ytad1'] == 'enabled') echo '<div style="margin-top:30px"></div>'; ?>
               </td>
            </tr>
            <tr class="rambler" style="display:none;">
                <th><?php _e("Rambler Топ-100:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytrambler" size="22" value="<?php echo stripslashes($yturbo_options['ytrambler']); ?>" />
                    <br /><small><?php _e("Укажите <strong>ID</strong> счетчика Rambler Топ-100 (например: <tt>4505046</tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-counter-id.html'>как узнать ID счетчика</a>).", "rss-for-yandex-turbo"); ?> </small>
                    <?php if ($yturbo_options['ytad1'] == 'enabled') echo '<div style="margin-top:30px"></div>'; ?>
               </td>
            </tr>
            <tr class="mediascope" style="display:none;">
                <th><?php _e("Mediascope (TNS):", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytmediascope" size="22" value="<?php echo stripslashes($yturbo_options['ytmediascope']); ?>" />
                    <br /><small><?php _e("Укажите идентификатор <strong>tmsec</strong> счетчика Mediascope (TNS) (<a target='new' href='https://yandex.ru/support/webmaster/turbo/find-counter-id.html'>как узнать ID счетчика</a>).", "rss-for-yandex-turbo"); ?> </small>
                    <?php if ($yturbo_options['ytad1'] == 'enabled') echo '<div style="margin-top:30px"></div>'; ?>
               </td>
            </tr>
            <tr class="myturbo">
                <th><?php _e("Блок рекламы #1:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytad1"><input type="checkbox" value="enabled" name="ytad1" id="ytad1" <?php if ($yturbo_options['ytad1'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Включить первый блок рекламы (<span style='color:green;'>после заголовка записи</span>)", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Будет включен блок рекламы на турбо-страницах в выбранном вами месте.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="myturbo block1" style="display:none;">
                <th><?php _e("Рекламная сеть блока рекламы #1:", 'rss-for-yandex-turbo') ?></th>
                <td>
                     <select name="ytad1set" id="ytad1set" style="width: 200px;">
                        <option value="РСЯ" <?php if ($yturbo_options['ytad1set'] == 'РСЯ') echo "selected='selected'" ?>><?php _e("РСЯ", "rss-for-yandex-turbo"); ?></option>
                        <option value="ADFOX" <?php if ($yturbo_options['ytad1set'] == 'ADFOX') echo "selected='selected'" ?>><?php _e("ADFOX", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Рекламная сеть блока рекламы #1.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="myturbo trrsa block1" style="display:none;">
                <th><?php _e("РСЯ идентификатор:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytad1rsa" size="22" value="<?php echo stripslashes($yturbo_options['ytad1rsa']); ?>" />
                    <br /><small><?php _e("Укажите идентификатор блока РСЯ (например, <strong>RA-123456-7</strong>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>)</small>.", "rss-for-yandex-turbo"); ?>
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>
            <tr class="myturbo trfox1 block1" style="display:none;">
                <th><?php _e("Код ADFOX:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <textarea rows="12" cols="60" name="ytadfox1" id="ytadfox1"><?php echo stripcslashes($yturbo_options['ytadfox1']); ?></textarea>
                    <br /><small><?php _e("Код рекламной сети ADFOX (начиная с <tt>&lt;div></tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>).", "rss-for-yandex-turbo"); ?> <br />
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>

            <tr class="myturbo">
                <th><?php _e("Блок рекламы #2:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytad2"><input type="checkbox" value="enabled" name="ytad2" id="ytad2" <?php if ($yturbo_options['ytad2'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Включить второй блок рекламы (<span style='color:green;'>в середине записи</span>)", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Будет включен блок рекламы на турбо-страницах в выбранном вами месте.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="myturbo block2" style="display:none;">
                <th><?php _e("Рекламная сеть блока рекламы #2:", 'rss-for-yandex-turbo') ?></th>
                <td>
                     <select name="ytad2set" id="ytad2set" style="width: 200px;">
                        <option value="РСЯ" <?php if ($yturbo_options['ytad2set'] == 'РСЯ') echo "selected='selected'" ?>><?php _e("РСЯ", "rss-for-yandex-turbo"); ?></option>
                        <option value="ADFOX" <?php if ($yturbo_options['ytad2set'] == 'ADFOX') echo "selected='selected'" ?>><?php _e("ADFOX", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Рекламная сеть блока рекламы #2.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="myturbo trrsa2 block2" style="display:none;">
                <th><?php _e("РСЯ идентификатор:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytad2rsa" size="22" value="<?php echo stripslashes($yturbo_options['ytad2rsa']); ?>" />
                    <br /><small><?php _e("Укажите идентификатор блока РСЯ (например, <strong>RA-123456-7</strong>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>)</small>.", "rss-for-yandex-turbo"); ?>
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>
            <tr class="myturbo trfox2 block2" style="display:none;">
                <th><?php _e("Код ADFOX:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <textarea rows="12" cols="60" name="ytadfox2" id="ytadfox2"><?php echo stripcslashes($yturbo_options['ytadfox2']); ?></textarea>
                    <br /><small><?php _e("Код рекламной сети ADFOX (начиная с <tt>&lt;div></tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>).", "rss-for-yandex-turbo"); ?> <br />
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>
            <tr class="myturbo">
                <th><?php _e("Блок рекламы #3:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytad3"><input type="checkbox" value="enabled" name="ytad3" id="ytad3" <?php if ($yturbo_options['ytad3'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Включить третий блок рекламы (<span style='color:green;'>в конце записи</span>)", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Будет включен блок рекламы на турбо-страницах в выбранном вами месте.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="myturbo block3" style="display:none;">
                <th><?php _e("Рекламная сеть блока рекламы #3:", 'rss-for-yandex-turbo') ?></th>
                <td>
                     <select name="ytad3set" id="ytad3set" style="width: 200px;">
                        <option value="РСЯ" <?php if ($yturbo_options['ytad3set'] == 'РСЯ') echo "selected='selected'" ?>><?php _e("РСЯ", "rss-for-yandex-turbo"); ?></option>
                        <option value="ADFOX" <?php if ($yturbo_options['ytad3set'] == 'ADFOX') echo "selected='selected'" ?>><?php _e("ADFOX", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Рекламная сеть блока рекламы #3.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="myturbo trrsa3 block3" style="display:none;">
                <th><?php _e("РСЯ идентификатор:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytad3rsa" size="22" value="<?php echo stripslashes($yturbo_options['ytad3rsa']); ?>" />
                    <br /><small><?php _e("Укажите идентификатор блока РСЯ (например, <strong>RA-123456-7</strong>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>)</small>.", "rss-for-yandex-turbo"); ?>
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>
            <tr class="myturbo trfox3 block3" style="display:none;">
                <th><?php _e("Код ADFOX:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <textarea rows="12" cols="60" name="ytadfox3" id="ytadfox3"><?php echo stripcslashes($yturbo_options['ytadfox3']); ?></textarea>
                    <br /><small><?php _e("Код рекламной сети ADFOX (начиная с <tt>&lt;div></tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>).", "rss-for-yandex-turbo"); ?> <br />
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>
            <tr class="myturbo">
                <th><?php _e("Блок рекламы #4:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytad4"><input type="checkbox" value="enabled" name="ytad4" id="ytad4" <?php if ($yturbo_options['ytad4'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Включить четвертый блок рекламы (<span style='color:green;'>после блока \"Поделиться\"</span>)", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Будет включен блок рекламы на турбо-страницах в выбранном вами месте.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Рекламный блок будет выведен только, если включена опция вывода блока \"Поделиться\".", "rss-for-yandex-turbo"); ?></small>
                </td>
            </tr>
            <tr class="myturbo block4" style="display:none;">
                <th><?php _e("Рекламная сеть блока рекламы #4:", 'rss-for-yandex-turbo') ?></th>
                <td>
                     <select name="ytad4set" id="ytad4set" style="width: 200px;">
                        <option value="РСЯ" <?php if ($yturbo_options['ytad4set'] == 'РСЯ') echo "selected='selected'" ?>><?php _e("РСЯ", "rss-for-yandex-turbo"); ?></option>
                        <option value="ADFOX" <?php if ($yturbo_options['ytad4set'] == 'ADFOX') echo "selected='selected'" ?>><?php _e("ADFOX", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Рекламная сеть блока рекламы #4.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="myturbo trrsa4 block4" style="display:none;">
                <th><?php _e("РСЯ идентификатор:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytad4rsa" size="22" value="<?php echo stripslashes($yturbo_options['ytad4rsa']); ?>" />
                    <br /><small><?php _e("Укажите идентификатор блока РСЯ (например, <strong>RA-123456-7</strong>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>)</small>.", "rss-for-yandex-turbo"); ?>
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>
            <tr class="myturbo trfox4 block4" style="display:none;">
                <th><?php _e("Код ADFOX:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <textarea rows="12" cols="60" name="ytadfox4" id="ytadfox4"><?php echo stripcslashes($yturbo_options['ytadfox4']); ?></textarea>
                    <br /><small><?php _e("Код рекламной сети ADFOX (начиная с <tt>&lt;div></tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>).", "rss-for-yandex-turbo"); ?> <br />
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>
            <tr class="myturbo">
                <th><?php _e("Блок рекламы #5:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytad5"><input type="checkbox" value="enabled" name="ytad5" id="ytad5" <?php if ($yturbo_options['ytad5'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Включить пятый блок рекламы (<span style='color:green;'>после комментариев</span>)", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Будет включен блок рекламы на турбо-страницах в выбранном вами месте.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Рекламный блок будет выведен только, если к записи есть хотя бы один комментарий (и включен вывод комментариев).", "rss-for-yandex-turbo"); ?></small>
                </td>
            </tr>
            <tr class="myturbo block5" style="display:none;">
                <th><?php _e("Рекламная сеть блока рекламы #5:", 'rss-for-yandex-turbo') ?></th>
                <td>
                     <select name="ytad5set" id="ytad5set" style="width: 200px;">
                        <option value="РСЯ" <?php if ($yturbo_options['ytad5set'] == 'РСЯ') echo "selected='selected'" ?>><?php _e("РСЯ", "rss-for-yandex-turbo"); ?></option>
                        <option value="ADFOX" <?php if ($yturbo_options['ytad5set'] == 'ADFOX') echo "selected='selected'" ?>><?php _e("ADFOX", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Рекламная сеть блока рекламы #5.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="myturbo trrsa5 block5" style="display:none;">
                <th><?php _e("РСЯ идентификатор:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytad5rsa" size="22" value="<?php echo stripslashes($yturbo_options['ytad5rsa']); ?>" />
                    <br /><small><?php _e("Укажите идентификатор блока РСЯ (например, <strong>RA-123456-7</strong>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>)</small>.", "rss-for-yandex-turbo"); ?>
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>
            <tr class="myturbo trfox5 block5" style="display:none;">
                <th><?php _e("Код ADFOX:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <textarea rows="12" cols="60" name="ytadfox5" id="ytadfox5"><?php echo stripcslashes($yturbo_options['ytadfox5']); ?></textarea>
                    <br /><small><?php _e("Код рекламной сети ADFOX (начиная с <tt>&lt;div></tt>, <a target='new' href='https://yandex.ru/support/webmaster/turbo/find-ad-block.html'>как его узнать</a>).", "rss-for-yandex-turbo"); ?> <br />
                    <div style="margin-top:30px;"></div>
               </td>
            </tr>
            <tr class="myturbo">
                <th><?php _e("Минимальный размер записи:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="ytrazmer" size="2" value="<?php echo stripslashes($yturbo_options['ytrazmer']); ?>" />
                    <br /><small><?php _e("Укажите минимальное количество символов записи для добавления рекламы.", "rss-for-yandex-turbo"); ?> <br/>
                    <?php _e("Данная опция используется только при вставке рекламы в <strong>середину</strong> записи.", "rss-for-yandex-turbo"); ?><br/>
                    <?php _e("Учитывается только текст контента записи (html-разметка не считается).", "rss-for-yandex-turbo"); ?>
                    </small>
               </td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Сохранить настройки &raquo;', 'rss-for-yandex-turbo'); ?>" />
                </td>
            </tr>
            
        </table>
    </div>
</div>

<div class="postbox">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('Продвинутые настройки', 'rss-for-yandex-turbo'); ?></span></h3>
	  <div class="inside" style="padding-bottom:15px;display: block;">
     
        <table class="form-table">
        
        <p><?php _e("В данной секции находятся продвинутые настройки. <br />Пожалуйста, будьте внимательны в этом разделе!", "rss-for-yandex-turbo"); ?> </p>
        
            <tr class="ytqueryselect">
                <th><?php _e("Включить в RSS:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <select name="ytqueryselect" id="ytqueryselect" style="width: 280px;">
                        <option value="Все таксономии, кроме исключенных" <?php if ($yturbo_options['ytqueryselect'] == 'Все таксономии, кроме исключенных') echo "selected='selected'" ?>><?php _e("Все таксономии, кроме исключенных", "rss-for-yandex-turbo"); ?></option>
                        <option value="Только указанные таксономии" <?php if ($yturbo_options['ytqueryselect'] == 'Только указанные таксономии') echo "selected='selected'" ?>><?php _e("Только указанные таксономии", "rss-for-yandex-turbo"); ?></option>
                    </select>
                    <br /><small><?php _e("Внимание! Будьте осторожны с этой настройкой!", "rss-for-yandex-turbo"); ?> <br />
                    <span id="includespan"><?php _e("Обязательно установите ниже таксономии для включения в ленту - иначе лента будет пустая.", "rss-for-yandex-turbo"); ?> <br /></span>
                    <span id="excludespan"><?php _e("По умолчанию в ленту попадают записи всех таксономий, кроме указанных ниже.", "rss-for-yandex-turbo"); ?> <br /></span>
                    </small>
               </td>
            </tr> 
            <tr class="yttaxlisttr">
                <th><?php _e("Таксономии для исключения:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <textarea rows="3" cols="60" name="yttaxlist" id="yttaxlist"><?php echo stripslashes($yturbo_options['yttaxlist']); ?></textarea>
                    <br /><small><?php _e("Используемый формат: <strong>taxonomy_name:id1,id2,id3</strong>", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Пример: <code>category:1,2,4</code> - записи рубрик с ID равным 1, 2 и 4 будут <strong style='color:red;'>исключены</strong> из RSS-ленты.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Каждая новая таксономия должна начинаться с новой строки.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Стандартные таксономии WordPress: рубрика: <code>category</code>, метка: <code>post_tag</code>.", "rss-for-yandex-turbo"); ?>
                    </small>
                </td>
            </tr>
            <tr class="ytaddtaxlisttr">
                <th><?php _e("Таксономии для добавления:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <textarea rows="3" cols="60" name="ytaddtaxlist" id="ytaddtaxlist"><?php echo stripslashes($yturbo_options['ytaddtaxlist']); ?></textarea>
                    <br /><small><?php _e("Используемый формат: <strong>taxonomy_name:id1,id2,id3</strong>", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Пример: <code>category:1,2,4</code> - записи рубрик с ID равным 1, 2 и 4 будут <strong style='color:red;'>добавлены</strong> в RSS-ленту.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Каждая новая таксономия должна начинаться с новой строки.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Стандартные таксономии WordPress: рубрика: <code>category</code>, метка: <code>post_tag</code>.", "rss-for-yandex-turbo"); ?>
                    </small>
                </td>
            </tr> 
            <tr>
                <th><?php _e("Типы записей:", "rss-for-yandex-turbo") ?></th>
                <td>
                    <input type="text" name="yttype" size="20" value="<?php echo stripslashes($yturbo_options['yttype']); ?>" />
                    <br /><small><?php _e("Типы записей в ленте через запятую (<strong>post</strong> - записи, <strong>page</strong> - страницы и т.д.).<br />У произвольных типов записей должно быть поле <strong>post_content</strong>!", "rss-for-yandex-turbo"); ?> </small>
               </td>
            </tr>
            <tr class="ytexcludeshortcodestr">
                <th><?php _e("Фильтр шорткодов:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytexcludeshortcodes"><input type="checkbox" value="enabled" name="ytexcludeshortcodes" id="ytexcludeshortcodes" <?php if ($yturbo_options['ytexcludeshortcodes'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Удалить указанные шорткоды", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Из контента записей будут удалены все указанные шорткоды (вместе с их контентом).", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="ytexcludeshortcodeslisttr">
                <th><?php _e("Шорткоды для удаления:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <textarea rows="3" cols="60" name="ytexcludeshortcodeslist" id="ytexcludeshortcodeslist"><?php echo stripslashes($yturbo_options['ytexcludeshortcodeslist']); ?></textarea>
                    <br /><small><?php _e("Список удаляемых шорткодов через запятую (пример: <code>spoiler,contact-form-7</code>).", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Указывать параметры шорткодов (включая квадратные скобки) не требуется.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
                </td>
            </tr>
            <tr class="ytexcludetagstr">
                <th><?php _e("Фильтр тегов (без контента):", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytexcludetags"><input type="checkbox" value="enabled" name="ytexcludetags" id="ytexcludetags" <?php if ($yturbo_options['ytexcludetags'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Удалить указанные html-теги", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Из контента записей будут удалены все указанные html-теги (<strong>сам контент этих тегов останется</strong>).", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="ytexcludetagslisttr">
                <th><?php _e("Теги для удаления:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <textarea rows="3" cols="60" name="ytexcludetagslist" id="ytexcludetagslist"><?php echo stripslashes($yturbo_options['ytexcludetagslist']); ?></textarea>
                    <br /><small><?php _e("Список удаляемых html-тегов через запятую.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Указывать классы, идентификаторы и прочее не требуется.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Самозакрывающиеся теги вроде <tt>&lt;img src=\"...\" /></tt> и <tt>&lt;br /></tt> удалить нельзя.", "rss-for-yandex-turbo"); ?><br />
                    </small>
                </td>
            </tr>
            <tr class="ytexcludetags2tr">
                <th><?php _e("Фильтр тегов (с контентом):", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytexcludetags2"><input type="checkbox" value="enabled" name="ytexcludetags2" id="ytexcludetags2" <?php if ($yturbo_options['ytexcludetags2'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Удалить указанные html-теги", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Из контента записей будут удалены все указанные html-теги (<strong>включая сам контент этих тегов</strong>).", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="ytexcludetagslist2tr">
                <th><?php _e("Теги для удаления:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <textarea rows="3" cols="60" name="ytexcludetagslist2" id="ytexcludetagslist2"><?php echo stripslashes($yturbo_options['ytexcludetagslist2']); ?></textarea>
                    <br /><small><?php _e("Список удаляемых html-тегов через запятую.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Указывать классы, идентификаторы и прочее не требуется.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Самозакрывающиеся теги вроде <tt>&lt;img src=\"...\" /></tt> и <tt>&lt;br /></tt> удалить нельзя.", "rss-for-yandex-turbo"); ?><br />
                    </small>
                </td>
            </tr>
            <tr class="ytexcludecontenttr">
                <th><?php _e("Контент для удаления:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytexcludecontent"><input type="checkbox" value="enabled" name="ytexcludecontent" id="ytexcludecontent" <?php if ($yturbo_options['ytexcludecontent'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Удалить указанный контент из RSS", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Точные вхождения указанного контента будут удалены из записей в RSS-ленте.", "rss-for-yandex-turbo"); ?> </small>
                </td>
            </tr>
            <tr class="ytexcludecontentlisttr">
                <th><?php _e("Список удаляемого контента:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <textarea rows="5" cols="60" name="ytexcludecontentlist" id="ytexcludecontentlist"><?php echo stripcslashes($yturbo_options['ytexcludecontentlist']); ?></textarea>
                    <br /><small><?php _e("Каждый новый шаблон для удаления должен начинаться с новой строки.", "rss-for-yandex-turbo"); ?> <br />
                    </small>
                </td>
            </tr>
            <tr>
                <th><?php _e("Отключение Турбо:", 'rss-for-yandex-turbo') ?></th>
                <td>
                    <label for="ytremoveturbo"><input type="checkbox" value="enabled" name="ytremoveturbo" id="ytremoveturbo" <?php if ($yturbo_options['ytremoveturbo'] == 'enabled') echo "checked='checked'"; ?> /><?php _e("Отключить турбо-страницы", "rss-for-yandex-turbo"); ?></label>
                    <br /><small><?php _e("Эта опция добавит в RSS-ленту атрибут <tt>turbo=\"false\"</tt> к тегу <tt>&lt;item></tt>.", "rss-for-yandex-turbo"); ?> <br />
                    <?php _e("Это единственный способ заставить Яндекс отключить турбо-страницы для вашего сайта.", "rss-for-yandex-turbo"); ?><br />
                    <?php _e("Простое удаление плагина не поможет - необходимо, чтобы бот Яндекса \"съел\" ленту с <tt>turbo=\"false\"</tt>.", "rss-for-yandex-turbo"); ?>
                    </small>
                </td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Сохранить настройки &raquo;', 'rss-for-yandex-turbo'); ?>" />
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="postbox">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('О плагине', 'rss-for-yandex-turbo'); ?></span></h3>
	  <div class="inside" style="padding-bottom:15px;display: block;">
     
      <p><?php _e('Если вам нравится мой плагин, то, пожалуйста, поставьте ему <a target="new" href="https://wordpress.org/plugins/rss-for-yandex-turbo/"><strong>5 звезд</strong></a> в репозитории.', 'rss-for-yandex-turbo'); ?></p>
      <p style="margin-top:20px;margin-bottom:10px;"><?php _e('Возможно, что вам также будут интересны другие мои плагины:', 'rss-for-yandex-turbo'); ?></p>
      
      <div class="about">
        <ul>
            <li style="list-style-type: square;margin: 0px 0px 3px 35px;"><a class="yt" target="new" href="https://ru.wordpress.org/plugins/rss-for-yandex-zen/">RSS for Yandex Zen</a> - <?php _e('cоздание RSS-ленты для сервиса Яндекс.Дзен.', 'rss-for-yandex-turbo'); ?></li>
            <li style="list-style-type: square;margin: 0px 0px 3px 35px;"><a class="yt" target="new" href="https://ru.wordpress.org/plugins/bbspoiler/">BBSpoiler</a> - <?php _e('плагин позволит вам спрятать текст под тегами [spoiler]текст[/spoiler].', 'rss-for-yandex-turbo'); ?></li>
            <li style="list-style-type: square;margin: 0px 0px 3px 35px;"><a class="yt" target="new" href="https://ru.wordpress.org/plugins/easy-textillate/">Easy Textillate</a> - <?php _e('плагин очень красиво анимирует текст (шорткодами в записях и виджетах или PHP-кодом в файлах темы).', 'rss-for-yandex-turbo'); ?> </li>
            <li style="list-style-type: square;margin: 0px 0px 3px 35px;"><a class="yt" target="new" href="https://ru.wordpress.org/plugins/cool-image-share/">Cool Image Share</a> - <?php _e('плагин добавляет иконки социальных сетей на каждое изображение в ваших записях.', 'rss-for-yandex-turbo'); ?> </li>
            <li style="list-style-type: square;margin: 0px 0px 3px 35px;"><a class="yt" target="new" href="https://ru.wordpress.org/plugins/today-yesterday-dates/">Today-Yesterday Dates</a> - <?php _e('относительные даты для записей за сегодня и вчера.', 'rss-for-yandex-turbo'); ?> </li>
            </ul>
      </div>     
    </div>
</div>
<?php wp_nonce_field( plugin_basename(__FILE__), 'yturbo_nonce'); ?>
</form>
</div>
</div>
<?php 
}
//функция вывода страницы настроек плагина end

//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" begin
function yturbo_menu() {
	add_options_page('Яндекс.Турбо', 'Яндекс.Турбо', 'manage_options', 'rss-for-yandex-turbo.php', 'yturbo_options_page');
}
add_action('admin_menu', 'yturbo_menu');
//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" end

//подключение стилей на странице настроек плагина begin
function yturbo_admin_print_scripts() {
    $post_permalink = $_SERVER["REQUEST_URI"];
    if(strpos($post_permalink, 'rss-for-yandex-turbo.php') == true) : ?>
        <style>
        tt {padding: 1px 5px 1px;margin: 0 1px;background: #eaeaea;background: rgba(0,0,0,.07);font-size: 13px;font-family: Consolas,Monaco,monospace;unicode-bidi: embed;}
        #ytfeedbackcontacts option {color: #00a0d2;font-weight: normal;}
        #ytfeedbackcontacts :disabled{color: graytext;font-weight: normal;}
        #ytfeedbackcontacts option:first-child{color: #32373c;font-weight: normal;}
        .trbordertop {border-top-width: 2px;border-top-style: solid;border-top-color: #e7e2e2;}
        </style>
    <?php endif; ?>
<?php }    
add_action('admin_head', 'yturbo_admin_print_scripts');
//подключение стилей на странице настроек плагина end

//создаем метабокс begin
function yturbo_meta_box(){
    $yturbo_options = get_option('yturbo_options');  
    $yttype = $yturbo_options['yttype']; 
    $yttype = explode(",", $yttype);
    add_meta_box('yturbo_meta_box', 'Яндекс.Турбо', 'yturbo_callback', $yttype, 'normal' , 'high');
}
add_action( 'add_meta_boxes', 'yturbo_meta_box' );
//создаем метабокс end

//сохраняем метабокс begin
function yturbo_save_metabox($post_id){ 
    global $post;
    
    if ( ! isset( $_POST['yturbo_meta_nonce'] ) ) 
        return $post_id;
 
    if ( ! wp_verify_nonce($_POST['yturbo_meta_nonce'], plugin_basename(__FILE__) ) )
		return $post_id;
    
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
    
       
    if(isset($_POST["ytrssenabled"])){
        $ytrssenabled = 'yes';
        update_post_meta($post->ID, 'ytrssenabled_meta_value', $ytrssenabled);
    } else {
        $ytrssenabled = 'no';
        update_post_meta($post->ID, 'ytrssenabled_meta_value', $ytrssenabled);
    }   

    if(isset($_POST["ytad1meta"])){
        $ytad1meta = 'disabled';
        update_post_meta($post->ID, 'ytad1meta', $ytad1meta);
    } else {
        $ytad1meta = 'enabled';
        update_post_meta($post->ID, 'ytad1meta', $ytad1meta);
    }    
    if(isset($_POST["ytad2meta"])){
        $ytad2meta = 'disabled';
        update_post_meta($post->ID, 'ytad2meta', $ytad2meta);
    } else {
        $ytad2meta = 'enabled';
        update_post_meta($post->ID, 'ytad2meta', $ytad2meta);
    } 
    if(isset($_POST["ytad3meta"])){
        $ytad3meta = 'disabled';
        update_post_meta($post->ID, 'ytad3meta', $ytad3meta);
    } else {
        $ytad3meta = 'enabled';
        update_post_meta($post->ID, 'ytad3meta', $ytad3meta);
    } 
    if(isset($_POST["ytad4meta"])){
        $ytad4meta = 'disabled';
        update_post_meta($post->ID, 'ytad4meta', $ytad4meta);
    } else {
        $ytad4meta = 'enabled';
        update_post_meta($post->ID, 'ytad4meta', $ytad4meta);
    } 
    if(isset($_POST["ytad5meta"])){
        $ytad5meta = 'disabled';
        update_post_meta($post->ID, 'ytad5meta', $ytad5meta);
    } else {
        $ytad5meta = 'enabled';
        update_post_meta($post->ID, 'ytad5meta', $ytad5meta);
    } 
}
add_action('save_post', 'yturbo_save_metabox');
//сохраняем метабокс end

//выводим метабокс begin
function yturbo_callback(){
    global $post;
	wp_nonce_field( plugin_basename(__FILE__), 'yturbo_meta_nonce' );
    
    $yturbo_options = get_option('yturbo_options');
    
    $ytad1meta = get_post_meta($post->ID, 'ytad1meta', true); 
    if (!$ytad1meta) {$ytad1meta = $yturbo_options['ytad1'];} 
    
    $ytad2meta = get_post_meta($post->ID, 'ytad2meta', true); 
    if (!$ytad2meta) {$ytad2meta = $yturbo_options['ytad2'];} 
    
    $ytad3meta = get_post_meta($post->ID, 'ytad3meta', true); 
    if (!$ytad3meta) {$ytad3meta = $yturbo_options['ytad3'];} 
    
    $ytad4meta = get_post_meta($post->ID, 'ytad4meta', true); 
    if (!$ytad4meta) {$ytad4meta = $yturbo_options['ytad4'];} 
    
    $ytad5meta = get_post_meta($post->ID, 'ytad5meta', true); 
    if (!$ytad5meta) {$ytad5meta = $yturbo_options['ytad5'];} 

    $ytrssenabled = get_post_meta($post->ID, 'ytrssenabled_meta_value', true); 
    if (!$ytrssenabled) {$ytrssenabled = "no";}   
    ?>   
    
    <p style="margin: 10px 0px 0px 5px;!important;">
    
    <label for="ytrssenabled"><input type="checkbox" value="enabled" name="ytrssenabled" id="ytrssenabled" <?php if ($ytrssenabled == 'yes') echo "checked='checked'"; ?> /><?php _e("Исключить эту запись из RSS", "rss-for-yandex-turbo"); ?></label>
    </p>
    
    <p style="margin:5px!important;margin-top:10px!important;">
    
    <?php if ($yturbo_options['ytad1'] == 'enabled') { ?>
        <label for="ytad1meta"><input type="checkbox" value="disabled" name="ytad1meta" id="ytad1meta" <?php if ($ytad1meta == 'disabled') echo "checked='checked'"; ?> /><?php _e("Отключить блок рекламы #1 для этой записи (в начале записи)", "rss-for-yandex-turbo"); ?></label><br />
    <?php } ?>    
    <?php if ($yturbo_options['ytad2'] == 'enabled') { ?>
        <label for="ytad2meta"><input type="checkbox" value="disabled" name="ytad2meta" id="ytad2meta" <?php if ($ytad2meta == 'disabled') echo "checked='checked'"; ?> /><?php _e("Отключить блок рекламы #2 для этой записи (в середине записи)", "rss-for-yandex-turbo"); ?></label><br />
    <?php } ?> 
    <?php if ($yturbo_options['ytad3'] == 'enabled') { ?>
        <label for="ytad3meta"><input type="checkbox" value="disabled" name="ytad3meta" id="ytad3meta" <?php if ($ytad3meta == 'disabled') echo "checked='checked'"; ?> /><?php _e("Отключить блок рекламы #3 для этой записи (в конце записи)", "rss-for-yandex-turbo"); ?></label><br />
    <?php } ?> 
    <?php if ($yturbo_options['ytad4'] == 'enabled') { ?>
        <label for="ytad4meta"><input type="checkbox" value="disabled" name="ytad4meta" id="ytad4meta" <?php if ($ytad4meta == 'disabled') echo "checked='checked'"; ?> /><?php _e("Отключить блок рекламы #4 для этой записи (после блока \"Поделиться\")", "rss-for-yandex-turbo"); ?></label><br />
    <?php } ?> 
    <?php if ($yturbo_options['ytad5'] == 'enabled') { ?>
        <label for="ytad5meta"><input type="checkbox" value="disabled" name="ytad5meta" id="ytad5meta" <?php if ($ytad5meta == 'disabled') echo "checked='checked'"; ?> /><?php _e("Отключить блок рекламы #5 для этой записи (после комментариев)", "rss-for-yandex-turbo"); ?></label><br />
    <?php } ?> 
    </p>
    
<?php }
//выводим метабокс end

//добавляем новую rss-ленту begin
function yturbo_add_feed(){
    $yturbo_options = get_option('yturbo_options'); 
    if (!isset($yturbo_options['ytrssname'])) {$yturbo_options['ytrssname']="turbo";update_option('yturbo_options', $yturbo_options);}
    add_feed($yturbo_options['ytrssname'], 'yturbo_feed_template');
}
add_action('init', 'yturbo_add_feed');
//добавляем новую rss-ленту end

//шаблон для RSS-ленты Яндекс.Турбо begin
function yturbo_feed_template(){
yturbo_set_new_options();
$yturbo_options = get_option('yturbo_options');  

$yttitle = $yturbo_options['yttitle'];
$ytlink = $yturbo_options['ytlink'];
$ytdescription = $yturbo_options['ytdescription'];
$ytlanguage = $yturbo_options['ytlanguage']; 
$ytnumber = $yturbo_options['ytnumber']; 
$ytrazb = $yturbo_options['ytrazb'];
$ytrazbnumber = $yturbo_options['ytrazbnumber'];
$yttype = $yturbo_options['yttype']; 
$yttype = explode(",", $yttype);
$ytfigcaption = $yturbo_options['ytfigcaption']; 
$ytimgauthorselect = $yturbo_options['ytimgauthorselect']; 
$ytimgauthor = $yturbo_options['ytimgauthor']; 
$ytauthorselect = $yturbo_options['ytauthorselect']; 
$ytauthor = $yturbo_options['ytauthor'];
$ytthumbnail = $yturbo_options['ytthumbnail']; 
$ytselectthumb = $yturbo_options['ytselectthumb']; 

$ytad1 = $yturbo_options['ytad1'];
$ytad1set = $yturbo_options['ytad1set'];
$ytad1rsa = $yturbo_options['ytad1rsa'];
$ytadfox1 = html_entity_decode(stripcslashes($yturbo_options['ytadfox1']),ENT_QUOTES);
$ytad2 = $yturbo_options['ytad2'];
$ytad2set = $yturbo_options['ytad2set'];
$ytad2rsa = $yturbo_options['ytad2rsa'];
$ytadfox2 = html_entity_decode(stripcslashes($yturbo_options['ytadfox2']),ENT_QUOTES);
$ytad3 = $yturbo_options['ytad3'];
$ytad3set = $yturbo_options['ytad3set'];
$ytad3rsa = $yturbo_options['ytad3rsa'];
$ytadfox3 = html_entity_decode(stripcslashes($yturbo_options['ytadfox3']),ENT_QUOTES);
$ytad4 = $yturbo_options['ytad4'];
$ytad4set = $yturbo_options['ytad4set'];
$ytad4rsa = $yturbo_options['ytad4rsa'];
$ytadfox4 = html_entity_decode(stripcslashes($yturbo_options['ytadfox4']),ENT_QUOTES);
$ytad5 = $yturbo_options['ytad5'];
$ytad5set = $yturbo_options['ytad5set'];
$ytad5rsa = $yturbo_options['ytad5rsa'];
$ytadfox5 = html_entity_decode(stripcslashes($yturbo_options['ytadfox5']),ENT_QUOTES);

$ytexcludetags = $yturbo_options['ytexcludetags']; 
$ytexcludetagslist = html_entity_decode($yturbo_options['ytexcludetagslist']); 
$ytexcludetags2 = $yturbo_options['ytexcludetags2']; 
$ytexcludetagslist2 = html_entity_decode($yturbo_options['ytexcludetagslist2']); 
$ytexcludecontent = $yturbo_options['ytexcludecontent']; 
$ytexcludecontentlist = html_entity_decode($yturbo_options['ytexcludecontentlist']);
$tax_query = array();
  
$ytrelated = $yturbo_options['ytrelated'];
$ytrelatednumber = $yturbo_options['ytrelatednumber'];
$ytrelatedselectthumb = $yturbo_options['ytrelatedselectthumb'];  
$ytrelatedcachetime = $yturbo_options['ytrelatedcachetime'];
$ytremoveturbo = $yturbo_options['ytremoveturbo'];  
$ytrelatedinfinity = $yturbo_options['ytrelatedinfinity']; 

$ytmetrika = $yturbo_options['ytmetrika'];
$ytliveinternet = $yturbo_options['ytliveinternet'];
$ytgoogle = $yturbo_options['ytgoogle'];
$ytmailru = $yturbo_options['ytmailru'];
$ytrambler = $yturbo_options['ytrambler'];
$ytmediascope = $yturbo_options['ytmediascope'];

$ytqueryselect = $yturbo_options['ytqueryselect'];
$yttaxlist = $yturbo_options['yttaxlist']; 
$ytaddtaxlist = $yturbo_options['ytaddtaxlist']; 

$ytselectmenu = $yturbo_options['ytselectmenu']; 
$ytgallery = $yturbo_options['ytgallery']; 
$ytcomments = $yturbo_options['ytcomments']; 
$ytcommentsnumber = $yturbo_options['ytcommentsnumber']; 
$ytcommentsorder = $yturbo_options['ytcommentsorder']; 
if ($ytcommentsorder=='В начале новые комментарии'){
    $reverse_top_level=false;
    $reverse_children=false;
} else {
    $reverse_top_level=true;
    $reverse_children=true;    
}    
$ytcommentsdate = $yturbo_options['ytcommentsdate'];
$ytcommentsdrevo = $yturbo_options['ytcommentsdrevo'];
if ($ytcommentsdrevo=='enabled') {
    $ytcommentsdrevo = 2;
} else {
    $ytcommentsdrevo = 1;
}   
$ytpostdate = $yturbo_options['ytpostdate']; 
$ytseotitle = $yturbo_options['ytseotitle']; 
$ytseoplugin = $yturbo_options['ytseoplugin'];

if ($ytqueryselect=='Все таксономии, кроме исключенных' && $yttaxlist) {
    $textAr = explode("\n", trim($yttaxlist));
    $textAr = array_filter($textAr, 'trim');
    $tax_query = array( 'relation' => 'AND' );
    foreach ($textAr as $line) {
        $tax = explode(":", $line);
        $taxterm = explode(",", $tax[1]);
        $tax_query[] = array(
            'taxonomy' => $tax[0],
            'field'    => 'id',
            'terms'    => $taxterm,
            'operator' => 'NOT IN',
        );
    } 
}    
if (!$ytaddtaxlist) {$ytaddtaxlist = 'category:10000000';}
if ($ytqueryselect=='Только указанные таксономии' && $ytaddtaxlist) {
    $textAr = explode("\n", trim($ytaddtaxlist));
    $textAr = array_filter($textAr, 'trim');
    $tax_query = array( 'relation' => 'OR' );
    foreach ($textAr as $line) {
        $tax = explode(":", $line);
        $taxterm = explode(",", $tax[1]);
        $tax_query[] = array(
            'taxonomy' => $tax[0],
            'field'    => 'id',
            'terms'    => $taxterm,
            'operator' => 'IN',
        );
    } 
}    

if ($ytrazb == 'enabled' && $ytrazbnumber) {
    if (isset($_GET['paged'])) {
        $paged = $_GET['paged'];
    } else {
        $paged = 0;
    }    
    $offset = $ytrazbnumber * ($paged - 1);
    if ($paged == 0) { 
        $paged = 1;
        $offset = 0;
    }    
    $temp = ceil($ytnumber / $ytrazbnumber);
    if ($paged > $temp) {echo 'Не хватает записей для этой ленты, измените настройки плагина.'; return;}
    $perpage = $ytrazbnumber * $paged;
} else {
    $offset = 0;
    $ytrazbnumber = $ytnumber;
}    

$args = array('offset'=> $offset,'ignore_sticky_posts' => 1, 'post_type' => $yttype, 'post_status' => 'publish', 'posts_per_page' => $ytrazbnumber,'tax_query' => $tax_query,
'meta_query' => array('relation' => 'OR', array('key' => 'ytrssenabled_meta_value', 'compare' => 'NOT EXISTS',),
array('key' => 'ytrssenabled_meta_value', 'value' => 'yes', 'compare' => '!=',),));
$query = new WP_Query( $args );

header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'.PHP_EOL;
?>
<rss
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
    version="2.0">
<channel>
    <title><?php echo $yttitle; ?></title>
    <link><?php echo $ytlink; ?></link>
    <description><?php echo $ytdescription; ?></description>
    <?php if ($ytmetrika) { ?><turbo:analytics id="<?php echo $ytmetrika; ?>" type="Yandex"></turbo:analytics><?php echo PHP_EOL; ?><?php } ?>
    <?php if ($ytliveinternet) { ?><turbo:analytics type="LiveInternet"></turbo:analytics><?php echo PHP_EOL; ?><?php } ?>
    <?php if ($ytgoogle) { ?><turbo:analytics id="<?php echo $ytgoogle; ?>" type="Google"></turbo:analytics><?php echo PHP_EOL; ?><?php } ?>
    <?php if ($ytmailru) { ?><turbo:analytics id="<?php echo $ytmailru; ?>" type="MailRu"></turbo:analytics><?php echo PHP_EOL; ?><?php } ?>
    <?php if ($ytrambler) { ?><turbo:analytics id="<?php echo $ytrambler; ?>" type="Rambler"></turbo:analytics><?php echo PHP_EOL; ?><?php } ?>
    <?php if ($ytmediascope) { ?><turbo:analytics id="<?php echo $ytmediascope; ?>" type="Mediascope"></turbo:analytics><?php echo PHP_EOL; ?><?php } ?>
    <?php do_action( 'yturbo_ads_header' ); echo yturbo_turbo_ads(); ?>
    <language><?php echo $ytlanguage; ?></language>
    <generator>RSS for Yandex Turbo v1.14 (https://wordpress.org/plugins/rss-for-yandex-turbo/)</generator>
    <?php while($query->have_posts()) : $query->the_post(); ?>
    <?php if ($ytremoveturbo != 'enabled') { ?>
    <item turbo="true">
    <?php } else { ?>
    <item turbo="false">
    <?php } ?>
        <title><?php the_title_rss(); ?></title>
        <link><?php the_permalink_rss(); ?></link>
        <turbo:topic><?php the_title_rss(); ?></turbo:topic>
        <turbo:source><?php the_permalink_rss(); ?></turbo:source>
        <?php if ($ytpostdate == 'enabled') { ?> 
        <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
        <?php } ?> 
        <?php if ($ytauthorselect != "Отключить указание автора") { ?> 
        <?php if ($ytauthor && $ytauthorselect != "Автор записи") { 
            echo '<author>'.$ytauthor.'</author>'.PHP_EOL;
        } else {
            echo '<author>'.get_the_author().'</author>'.PHP_EOL;
        } } ?>
        <?php if($ytimgauthorselect == 'Указать автора' && !$ytimgauthor){$ytimgauthor = get_the_author();} ?>
        <?php if($ytimgauthorselect == 'Автор записи'){$ytimgauthor = get_the_author();} ?>
        <turbo:content><![CDATA[
       	<?php 
        global $post;
        $tt = $post;
		$content = yturbo_the_content_feed();
        $post = $tt;
        setup_postdata( $post );
        
        if ($ytexcludetags != 'disabled' && $ytexcludetagslist) {
            $content = yturbo_strip_tags_without_content($content, $ytexcludetagslist);
        }
        if ($ytexcludetags2 != 'disabled' && $ytexcludetagslist2) {
            $content = yturbo_strip_tags_with_content($content, $ytexcludetagslist2, true);
        }      
        
        //удаляем все unicode-символы (как невалидные в rss)
        $content = preg_replace('/[\x00-\x1F\x7F]/u', '', $content); 
        
        //удаляем все атрибуты тега img кроме alt и src
        $content = yturbo_strip_attributes($content,array('alt','src'));
        
        $content = wpautop($content);
        
        //удаляем разметку движка при использовании шорткода с подписью [caption] (в html4 темах)
        //и заменяем alt у тега img на текст подписи, установленной в редакторе
        $pattern = "/<div id=\"attachment(.*?)>(.*?)<img(.*?)alt=\"(.*?)\"(.*?) \/>(.*?)<\/p>\n<p class=\"wp-caption-text\">(.*?)<\/p>\n<\/div>/i";
        $replacement = '$2<img$3alt="$7"$5 />$6';
        $content = preg_replace($pattern, $replacement, $content);
        //удаляем ошметки шорткода [caption], если тег <div> удаляется в настройках плагина
        $pattern = "/<p class=\"wp-caption-text\">(.*?)<\/p>/i";
        $replacement = '';
        $content = preg_replace($pattern, $replacement, $content);
        
        //удаляем разметку движка при использовании шорткода с подписью [caption] (в html5 темах)
        //и заменяем alt у тега img на текст подписи, установленной в редакторе
        $pattern = "/<figure id=\"attachment(.*?)>(.*?)<img(.*?)alt=\"(.*?)\"(.*?) \/>(.*?)<figcaption class=\"wp-caption-text\">(.*?)<\/figcaption><\/figure>/i";
        $replacement = '$2<img$3alt="$7"$5 />$6';
        $content = preg_replace($pattern, $replacement, $content);
        
        //удаляем <figure>, если они изначально присутствуют в контенте записи
        $pattern = "/<figure(.*?)>(.*?)<img(.*?)>(.*?)<\/figure>/i";
        $replacement = '<img$3>';
        $content = preg_replace($pattern, $replacement, $content);
        
        //удаление тегов <p> у отдельно стоящих изображений
        $pattern = "/<p><img(.*?)\" \/><\/p>/i";
        $replacement = '<img$1" />';
        $content = preg_replace($pattern, $replacement, $content);
        
        //удаление тегов <p> у отдельно стоящих изображений, обернутых ссылкой
        $pattern = "/<p><a(.*?)><img(.*?)\" \/><\/a><\/p>/i";
        $replacement = '<a$1><img$2" /></a>';
        $content = preg_replace($pattern, $replacement, $content);
        
        //добавляем alt если его вообще нет в теге img
        $pattern = "/<img(?!([^>]*\b)alt=)([^>]*?)>/i";
        $replacement = '<img alt="'. get_the_title_rss() .'"$1$2>';
        $content = preg_replace( $pattern, $replacement, $content );
        
        //устанавливаем alt равным названию записи, если он пустой
        $pattern = "/<img alt=\"\" (.*?) \/>/i";
        $replacement = '<img alt="'.get_the_title_rss().'" $1 />';
        $content = preg_replace($pattern, $replacement, $content);
             
        $copyrighttext = ' <span class="copyright">'. $ytimgauthor .'</span>';
        if ($ytimgauthorselect == 'Отключить указание автора') {$copyrighttext = '';}
        $figcaptionopen = '<figcaption>'; $figcaptionclose = '</figcaption>'; 
        if ($ytfigcaption == "Отключить описания" && $ytimgauthorselect == 'Отключить указание автора') {
            $figcaptionopen = '';  $figcaptionclose = ''; 
        }    
       
        //обрабатываем img теги и оборачиваем их тегами figure 
       
        if ($ytfigcaption == "Использовать alt по возможности") {
             //оборачиваем img тегом figure и прописываем ему описание и авторство
             $pattern = "/<img alt=\"(.*?)\" src=\"(.*?)\" \/>/i";
             $replacement = '<figure><img src="$2" />'.$figcaptionopen.'$1'.$copyrighttext.$figcaptionclose.'</figure>';
             $content = preg_replace($pattern, $replacement, $content); 
        } 
        if ($ytfigcaption == "Использовать название записи") {
             //оборачиваем img тегом figure и прописываем ему описание и авторство 
             $pattern = "/<img alt=\"(.*?)\" src=\"(.*?)\" \/>/i";
             $replacement = '<figure><img src="$2" />'.$figcaptionopen.get_the_title_rss() .$copyrighttext.$figcaptionclose.'</figure>';
             $content = preg_replace($pattern, $replacement, $content);
        }  
        if ($ytfigcaption == "Отключить описания") {
             //оборачиваем img тегом figure и прописываем ему описание и авторство 
             $pattern = "/<img alt=\"(.*?)\" src=\"(.*?)\" \/>/i";
             $replacement = '<figure><img src="$2" />'.$figcaptionopen.$copyrighttext.$figcaptionclose.'</figure>';
             $content = preg_replace($pattern, $replacement, $content);
        } 
        
        //удаляем картинки из контента, если их больше 30 уникальных (ограничение яндекс.турбо)
        if (preg_match_all("/<figure><img(.*?)>(.*?)<\/figure>/i", $content, $res)) {
            $i = 0; 
            if ($ytrelated=="enabled" && $ytrelatednumber) $i = $ytrelatednumber;
            if ($ytthumbnail=="enabled" && has_post_thumbnail(get_the_ID())) $i++;    
            $final = array();
            foreach ($res[0] as $r) {
                if (! in_array($r, $final)) {$i++;}
                if ($i > 30 && ! in_array($r, $final)) {
                    $content = str_replace($r, '', $content);
                }
                if (! in_array($r, $final)) {$final[] = $r;} 
            }    
        }
        
        if ($ytexcludecontent != 'disabled' && $ytexcludecontentlist) {
            $textAr = explode("\n", trim($ytexcludecontentlist));
            $textAr = array_filter($textAr, 'trim');
            foreach ($textAr as $line) {
                $line = trim($line);
                $content = preg_replace('/'.$line.'/i','', $content);
            }    
        }   
        
        if ( $ytgallery != 'disabled' ) { 
            add_shortcode('gallery', 'gallery_shortcode');
            add_filter( 'post_gallery', 'yturbo_gallery', 10, 2 );
            $content = do_shortcode($content);
        }
        
        $ytad4meta = get_post_meta($post->ID, 'ytad4meta', true);  
        $ytad5meta = get_post_meta($post->ID, 'ytad5meta', true);  
               
		?>
        <header>
            <?php if ( $ytthumbnail=="enabled" && has_post_thumbnail(get_the_ID()) ) {   
            echo '<figure><img src="'. strtok(get_the_post_thumbnail_url(get_the_ID(),$ytselectthumb), '?') .'" /></figure>'.PHP_EOL;} ?>
            <?php 
            if ($ytseotitle != 'disabled') { 
                if ($ytseoplugin == 'Yoast SEO') {
                    $temp = get_post_meta(get_the_ID(), "_yoast_wpseo_title", true);
                    $temp = apply_filters( 'convert_chars', $temp );
                    $temp = apply_filters( 'ent2ncr', $temp, 8 );
                    if (!$temp) {$temp = get_the_title_rss();}
                    echo "<h1>{$temp}</h1>".PHP_EOL;
                }    
                if ($ytseoplugin == 'All in One SEO Pack') {
                    $temp = get_post_meta(get_the_ID(), "_aioseop_title", true);
                    $temp = apply_filters( 'convert_chars', $temp );
                    $temp = apply_filters( 'ent2ncr', $temp, 8 );
                    if (!$temp) {$temp = get_the_title_rss();}
                    echo "<h1>{$temp}</h1>".PHP_EOL;
                }  
            } else { ?>
            <h1><?php the_title_rss(); ?></h1>
            <?php } ?>
            <?php if ($ytselectmenu!="Не использовать") {   
            echo '<menu>'.PHP_EOL; 
                    $menu = wp_get_nav_menu_object( $ytselectmenu );
                    $menu_items = wp_get_nav_menu_items($menu->term_id);
 
                    foreach ( (array) $menu_items as $key => $menu_item ) {
                        $title = $menu_item->title;
                        $url = $menu_item->url;
                        echo '<a href="' . $url . '">' . $title . '</a>'.PHP_EOL;
                    }
                
            echo '</menu>'.PHP_EOL;} ?>
        </header>
        <?php if ($yturbo_options['ytfeedback'] != 'disabled' && $yturbo_options['ytfeedbackselect'] == 'false' && $yturbo_options['ytfeedbackselectmesto'] == 'В начале записи') {echo yturbo_widget_feedback();} ?>
        <?php 
        $temp = apply_filters( 'yturbo_add_custom_ads', $content );
        if ( $temp != $content ) {
            echo $temp;
        } else {
            echo yturbo_add_advert($content);
        }
        ?>
        <?php if ($yturbo_options['ytshare'] == 'enabled') {
            echo PHP_EOL.'<div data-block="share" data-network="'.$yturbo_options['ytnetw'].'"></div>';
            if ($ytad4 == 'enabled' && $ytad4meta != 'disabled') { echo PHP_EOL.'<figure data-turbo-ad-id="fourth_ad_place"></figure>'.PHP_EOL; }
            do_action( 'yturbo_after_share' );
        } ?>
        <?php if ($yturbo_options['ytfeedback'] != 'disabled' && $yturbo_options['ytfeedbackselect'] == 'false' && $yturbo_options['ytfeedbackselectmesto'] == 'В конце записи') {echo yturbo_widget_feedback();} ?>
        <?php if ($yturbo_options['ytfeedback'] != 'disabled' && $yturbo_options['ytfeedbackselect'] != 'false') {echo yturbo_widget_feedback();} ?>
        <?php if ($ytcomments == 'enabled') {
           $comments = get_comments(array(
			'post_id' => get_the_ID(),
			'status' => 'approve',
		));
        if ($comments) {echo PHP_EOL.'<div data-block="comments" data-url="'.get_permalink().'#respond">';}
        wp_list_comments(array(
            'type' => 'comment',
			'per_page' => $ytcommentsnumber, 
			'callback' => 'yturbo_comments',
            'end-callback' => 'yturbo_comments_end',
            'title_li' => null,
            'max_depth' => $ytcommentsdrevo,
            'reverse_top_level' => $reverse_top_level,
            'reverse_children' => $reverse_children,
            'style' => 'div',
		), $comments);
        if ($comments) {echo '</div>';}
        if ($comments && $ytad5 == 'enabled' && $ytad5meta != 'disabled') { echo PHP_EOL.'<figure data-turbo-ad-id="fifth_ad_place"></figure>'.PHP_EOL; }
        do_action( 'yturbo_after_comments' );
       } ?>   
		]]></turbo:content>
        <?php 
        if ( $ytrelated=="enabled"  ) {   
        
            $tempID = get_the_ID();
            $rcontent = get_transient('related-' . $tempID);
            if(!$rcontent) {
                $cats = array();
                foreach (get_the_category(get_the_ID()) as $c) {
                    $cat = get_category($c);
                    array_push($cats, $cat->cat_ID);
                }
                $cur_post_id = array();
                array_push($cur_post_id, get_the_ID());

                $args = array('post__not_in' => $cur_post_id, 'cat' => $cats,'orderby' => 'rand','ignore_sticky_posts' => 1, 'post_type' => $yttype, 'post_status' => 'publish', 'posts_per_page' => $ytrelatednumber,'tax_query' => $tax_query,'meta_query' => array('relation' => 'OR', array('key' => 'ytrssenabled_meta_value', 'compare' => 'NOT EXISTS',),array('key' => 'ytrssenabled_meta_value', 'value' => 'yes', 'compare' => '!=',),));
                $related = new WP_Query( $args );
            
                if (!$related->have_posts()) {
                    $args = array('post__not_in' => $cur_post_id, 'orderby' => 'rand','ignore_sticky_posts' => 1, 'post_type' => $yttype, 'post_status' => 'publish', 'posts_per_page' => $ytrelatednumber,'tax_query' => $tax_query,'meta_query' => array('relation' => 'OR', array('key' => 'ytrssenabled_meta_value', 'compare' => 'NOT EXISTS',),array('key' => 'ytrssenabled_meta_value', 'value' => 'yes', 'compare' => '!=',),));
                    $related = new WP_Query( $args );
                }    
            
                if ($related->have_posts()) {
                    if ( $ytrelatedinfinity == 'disabled') { 
                        $rcontent .= '<yandex:related>'.PHP_EOL;
                    } else {
                        $rcontent .= '<yandex:related type="infinity">'.PHP_EOL;
                    }    
                }
                while ($related->have_posts()) : $related->the_post();
                    $thumburl = '';
                    if ($ytrelatedselectthumb != "Не использовать" && has_post_thumbnail(get_the_ID() && $ytrelatedinfinity != "enabled") ) {
                        $thumburl = ' img="' . strtok(get_the_post_thumbnail_url(get_the_ID(),$ytrelatedselectthumb), '?') . '"';
                    }    
                    $rlink = htmlspecialchars(get_the_permalink());
                    $rtitle = get_the_title_rss();
                    if ($ytrelatedselectthumb != "Не использовать" && $ytrelatedinfinity != "enabled") {
                        $rcontent .=  '<link url="'.$rlink.'"'.$thumburl.'>'.$rtitle.'</link>'.PHP_EOL;
                    } else {
                        $rcontent .=  '<link url="'.$rlink.'">'.$rtitle.'</link>'.PHP_EOL;
                    }
            
                endwhile;
                if ($related->have_posts()) {$rcontent .=  '</yandex:related>'.PHP_EOL;}
                if ($related->have_posts()) {echo $rcontent;}
                wp_reset_query($related);
                
                set_transient('related-' . $tempID, $rcontent, $ytrelatedcachetime * HOUR_IN_SECONDS); 
            } else {
                echo $rcontent;
            }    
        } ?>
    </item>
<?php endwhile; ?>
<?php wp_reset_postdata(); ?>
<?php wp_reset_query(); ?>
</channel>
</rss>
<?php }
//шаблон для RSS-ленты Яндекс.Турбо end

//установка правильного content type для ленты плагина begin
function yturbo_feed_content_type( $content_type, $type ) {
    $yturbo_options = get_option('yturbo_options'); 
    if (!isset($yturbo_options['ytrssname'])) {$yturbo_options['ytrssname']="turbo";update_option('yturbo_options', $yturbo_options);}
    if( $yturbo_options['ytrssname'] == $type ) {
        $content_type = 'application/rss+xml';
    }
    return $content_type;
}
add_filter( 'feed_content_type', 'yturbo_feed_content_type', 10, 2 );
//установка правильного content type для ленты плагина end

//функция формирования content в rss begin
function yturbo_the_content_feed() {
    $yturbo_options = get_option('yturbo_options');  
    if ( $yturbo_options['ytgallery'] != 'disabled' ) { remove_shortcode("gallery"); }
    if ($yturbo_options['ytexcerpt'] == 'enabled') {
        $content = '';
        if ( has_excerpt( get_the_ID() ) ) {
            $content = '<p>' . get_the_excerpt( get_the_ID() ) . '</p>';
        }
        $content .= apply_filters('the_content', yturbo_strip_shortcodes(get_post_field('post_content', get_the_ID())));
    } else {
        $content = apply_filters('the_content', yturbo_strip_shortcodes(get_post_field('post_content', get_the_ID())));
    }    
    $content = apply_filters('yturbo_the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
    $content = apply_filters('wp_staticize_emoji', $content);
    $content = apply_filters('_oembed_filter_feed_content', $content);
    return $content;
}
//функция формирования content в rss end

//функция удаления тегов вместе с их контентом begin 
function yturbo_strip_tags_with_content($text, $tags = '', $invert = FALSE) {
    preg_match_all( '/<(.+?)[\s]*\/?[\s]*>/si', trim( $tags ), $tags_array );
	$tags_array = array_unique( $tags_array[1] );

	$regex = '';

	if ( count( $tags_array ) > 0 ) {
		if ( ! $invert ) {
			$regex = '@<(?!(?:' . implode( '|', $tags_array ) . ')\b)(\w+)\b[^>]*?(>((?!<\1\b).)*?<\/\1|\/)>@si';
			$text  = preg_replace( $regex, '', $text );
		} else {
			$regex = '@<(' . implode( '|', $tags_array ) . ')\b[^>]*?(>((?!<\1\b).)*?<\/\1|\/)>@si';
			$text  = preg_replace( $regex, '', $text );
		}
	} elseif ( ! $invert ) {
		$regex = '@<(\w+)\b[^>]*?(>((?!<\1\b).)*?<\/\1|\/)>@si';
		$text  = preg_replace( $regex, '', $text );
	}

	if ( $regex && preg_match( $regex, $text ) ) {
		$text = yturbo_strip_tags_with_content( $text, $tags, $invert );
	}

	return $text;
} 
//функция удаления тегов вместе с их контентом end

//функция удаления тегов без их контента begin 
function yturbo_strip_tags_without_content($text, $tags = '') {

    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
    $tags = array_unique($tags[1]);
   
    if(is_array($tags) AND count($tags) > 0) {
        foreach($tags as $tag)  {
            $text = preg_replace("/<\\/?" . $tag . "(.|\\s)*?>/", '', $text);
        }
    }
    return $text;
} 
//функция удаления тегов без их контента end

//функция принудительной установки header-тега X-Robots-Tag (решение проблемы с SEO-плагинами) begin
function yturbo_index_follow_rss() {
    $yturbo_options = get_option('yturbo_options'); 
    if (!isset($yturbo_options['ytrssname'])) {$yturbo_options['ytrssname']="turbo";update_option('yturbo_options', $yturbo_options);}
    if ( is_feed( $yturbo_options['ytrssname'] ) ) {
        header( 'X-Robots-Tag: index, follow', true );
        header( 'HTTP/1.1 200 OK', true );
    }
}
add_action( 'template_redirect', 'yturbo_index_follow_rss', 999999 );
//функция принудительной установки header-тега X-Robots-Tag (решение проблемы с SEO-плагинами) end

//функция подсчета количества rss-лент и их вывод на странице настроек плагина begin
function yturbo_count_feeds() {
$yturbo_options = get_option('yturbo_options');  

$ytnumber = $yturbo_options['ytnumber']; 
$ytrazb = $yturbo_options['ytrazb'];
$ytrazbnumber = $yturbo_options['ytrazbnumber'];
$yttype = $yturbo_options['yttype']; 
$yttype = explode(",", $yttype);

$tax_query = array();

$ytqueryselect = $yturbo_options['ytqueryselect'];
$yttaxlist = $yturbo_options['yttaxlist']; 
$ytaddtaxlist = $yturbo_options['ytaddtaxlist']; 

if ($ytqueryselect=='Все таксономии, кроме исключенных' && $yttaxlist) {
    $textAr = explode("\n", trim($yttaxlist));
    $textAr = array_filter($textAr, 'trim');
    $tax_query = array( 'relation' => 'AND' );
    foreach ($textAr as $line) {
        $tax = explode(":", $line);
        $taxterm = explode(",", $tax[1]);
        $tax_query[] = array(
            'taxonomy' => $tax[0],
            'field'    => 'id',
            'terms'    => $taxterm,
            'operator' => 'NOT IN',
        );
    } 
}    
if (!$ytaddtaxlist) {$ytaddtaxlist = 'category:10000000';}
if ($ytqueryselect=='Только указанные таксономии' && $ytaddtaxlist) {
    $textAr = explode("\n", trim($ytaddtaxlist));
    $textAr = array_filter($textAr, 'trim');
    $tax_query = array( 'relation' => 'OR' );
    foreach ($textAr as $line) {
        $tax = explode(":", $line);
        $taxterm = explode(",", $tax[1]);
        $tax_query[] = array(
            'taxonomy' => $tax[0],
            'field'    => 'id',
            'terms'    => $taxterm,
            'operator' => 'IN',
        );
    } 
}    

if ($ytnumber > 1000) :
if ($ytrazb == 'enabled') {
$paged = 2;
echo '<p>Вы установили слишком большое общее количество записей в RSS (больше 1000 записей),<br /> 
поэтому чтобы не нагружать базу данных фактическая проверка существования разбитых RSS-лент <br />
не осуществлялась. Проверяйте наличие записей в RSS-лентах самостоятельно.</p>
<p>Всего у вас ' . yturbo_russian_number(ceil($ytnumber / $ytrazbnumber), array(' RSS-лента', ' RSS-ленты', ' RSS-лент')) . ' (в каждой по '.yturbo_russian_number($ytrazbnumber, array(' запись', ' записи', ' записей')). '):</p>';   
echo '<ul>';
if ( get_option('permalink_structure') ) {
    echo '<li>1. <a target="new" href="'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/">'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/</a></li>';
} else {
    echo '<li>1. <a target="new" href="'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'">'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'</a></li>'; 
}
while ($paged <= ceil($ytnumber / $ytrazbnumber) ) {

    if ( get_option('permalink_structure') ) {
            echo '<li>'.$paged.'. <a target="new" href="'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/?paged='.$paged.'">'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/?paged='.$paged.'</a></li>';
        } else {
            echo '<li>'.$paged.'. <a target="new" href="'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'&paged='.$x.'">'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'&paged='.$paged.'</a></li>'; 
        }
    $paged++;
    
    if ($paged == 13) {
        echo '<li>....</li>';
        echo '<p>Слишком много RSS-лент, остальные ленты были скрыты.</p>';
        break; 
    }  
}
echo '</ul>';
} else {
    echo '<p>Всего у вас 1 RSS-лента '  . ' (в ней '.yturbo_russian_number($ytnumber, array(' запись', ' записи', ' записей')). '):</p>';   
echo '<ul>';
if ( get_option('permalink_structure') ) {
    echo '<li>1. <a target="new" href="'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/">'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/</a></li>';
} else {
    echo '<li>1. <a target="new" href="'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'">'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'</a></li>'; 
}
}    
else :

$args = array('ignore_sticky_posts' => 1, 'post_type' => $yttype, 'post_status' => 'publish', 'posts_per_page' => $ytnumber,'tax_query' => $tax_query,
'meta_query' => array('relation' => 'OR', array('key' => 'ytrssenabled_meta_value', 'compare' => 'NOT EXISTS',),
array('key' => 'ytrssenabled_meta_value', 'value' => 'yes', 'compare' => '!=',),));
$query = new WP_Query( $args );

if ($query->post_count < $ytnumber) $ytnumber = $query->post_count;
 
if ($ytrazb == 'enabled' && (ceil($query->post_count / $ytrazbnumber) > 1)) {
    echo '<p>Всего у вас ' . yturbo_russian_number(ceil($query->post_count / $ytrazbnumber), array(' RSS-лента', ' RSS-ленты', ' RSS-лент')) . ' (в каждой по '.yturbo_russian_number($ytrazbnumber, array(' запись', ' записи', ' записей')). '):</p>';   
} else {
    echo '<p>Всего у вас 1 RSS-лента '. ' (в ней '.yturbo_russian_number($ytnumber, array(' запись', ' записи', ' записей')). '):</p>';   
}    

echo '<ul style="margin-bottom: 20px;">';
if ( get_option('permalink_structure') ) {
    echo '<li>1. <a target="new" href="'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/">'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/</a></li>';
} else {
    echo '<li>1. <a target="new" href="'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'">'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'</a></li>'; 
}

if ($ytrazb == 'enabled' && (ceil($query->post_count / $ytrazbnumber) > 1)) {
    for ($x=1; $x++<ceil($query->post_count / $ytrazbnumber);) {
        if ( get_option('permalink_structure') ) {
            echo '<li>'.$x.'. <a target="new" href="'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/?paged='.$x.'">'.get_bloginfo("url").'/feed/'.$yturbo_options['ytrssname'].'/?paged='.$x.'</a></li>';
        } else {
            echo '<li>'.$x.'. <a target="new" href="'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'&paged='.$x.'">'.get_bloginfo("url").'/?feed='.$yturbo_options['ytrssname'].'&paged='.$x.'</a></li>'; 
        }
    if ($x == 12) {
        echo '<li>....</li>';
        echo '<p>Слишком много RSS-лент, остальные ленты были скрыты.</p>';
        break; 
    }    
    }
}

echo '</ul>';

endif;
}    
//функция подсчета количества rss-лент и их вывод на странице настроек плагина end

//функция склонения слов после числа begin
function yturbo_russian_number($number, $titles) {  
    $cases = array (2, 0, 1, 1, 1, 2);  
    return $number . " " . $titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[min($number%10, 5)] ];  
}  
//функция склонения слов после числа end

//функция добавления рекламы в запись begin
function yturbo_add_advert( $content ){
    
    $yturbo_options = get_option('yturbo_options');  
    $ytrazmer = $yturbo_options['ytrazmer'];
    $ytad1 = $yturbo_options['ytad1'];
    $ytad2 = $yturbo_options['ytad2'];
    $ytad3 = $yturbo_options['ytad3'];
    
    $tempcontent = $content;
    $tempcontent = strip_tags($tempcontent);
    $tempcontent = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $tempcontent);

    $num = ceil(mb_strlen($tempcontent) / 2);
    $temp = ceil(mb_strlen($tempcontent) / 10);
    $num = $num - $temp;
    
    global $post;
    $ytad1meta = get_post_meta($post->ID, 'ytad1meta', true);  
    $ytad2meta = get_post_meta($post->ID, 'ytad2meta', true);       
    $ytad3meta = get_post_meta($post->ID, 'ytad3meta', true);  
    
    if ($ytad2 != 'enabled' or $ytad2meta == 'disabled') {$ads ='';}
    
    if ($ytad2 == 'enabled' && $ytad2meta != 'disabled') {
        $ads = PHP_EOL.'<figure data-turbo-ad-id="second_ad_place"></figure>';
    }
    
    if (mb_strlen($tempcontent) > (int)$ytrazmer) {
        $content = preg_replace('~[^^]{'. $num .'}.*?(?:\r?\n\r?\n|</p>|</figure>|</ul>|</pre>|</table>)~su', "\${0}$ads", trim( $content ), 1);
    }    
    
    if ($ytad1 == 'enabled' && $ytad1meta != 'disabled') { $content = '<figure data-turbo-ad-id="first_ad_place"></figure>'.PHP_EOL . $content;} 
    if ($ytad3 == 'enabled' && $ytad3meta != 'disabled') { $content = PHP_EOL . $content . PHP_EOL.'<figure data-turbo-ad-id="third_ad_place"></figure>';} 
    
	return $content;
}
//функция добавления рекламы в запись end

//функция удаления всех атрибутов тега img кроме указанных begin
function yturbo_strip_attributes($s, $allowedattr = array()) {
  if (preg_match_all("/<img[^>]*\\s([^>]*)\\/*>/msiU", $s, $res, PREG_SET_ORDER)) {
   foreach ($res as $r) {
     $tag = $r[0];
     $attrs = array();
     preg_match_all("/\\s.*=(['\"]).*\\1/msiU", " " . $r[1], $split, PREG_SET_ORDER);
     foreach ($split as $spl) {
      $attrs[] = $spl[0];
     }
     $newattrs = array();
     foreach ($attrs as $a) {
      $tmp = explode("=", $a);
      if (trim($a) != "" && (!isset($tmp[1]) || (trim($tmp[0]) != "" && !in_array(strtolower(trim($tmp[0])), $allowedattr)))) {

      } else {
          $newattrs[] = $a;
      }
     }
    
     //сортировка чтобы alt был раньше src   
     sort($newattrs);
     reset($newattrs);
     
     $attrs = implode(" ", $newattrs);
     $rpl = str_replace($r[1], $attrs, $tag);
     //заменяем одинарные кавычки на двойные
     $rpl = str_replace("'", "\"", $rpl);   
     //добавляем закрывающий символ / если он отсутствует
     $rpl = str_replace("\">", "\" />", $rpl);
     //добавляем пробел перед закрывающим символом /
     $rpl = str_replace("\"/>", "\" />", $rpl);
     //удаляем двойные пробелы
     $rpl = str_replace("  ", " ", $rpl);
     
     $s = str_replace($tag, $rpl, $s);
   }
  } 

  return $s;
}
//функция удаления всех атрибутов тега img кроме указанных end

//функция удаления транзитного кэша для похожих записей begin
function yturbo_clear_transients() {
    global $wpdb;

    $sql = "
            DELETE 
            FROM {$wpdb->options}
            WHERE option_name like '\_transient\_related-%'
            OR option_name like '\_transient\_timeout\_related-%'
    ";

    $wpdb->query($sql);
}
//функция удаления транзитного кэша для похожих записей end

//функция преобразования стандартных галерей движка в турбо-галереи begin
function yturbo_gallery( $output, $attr) {
    
    $yturbo_options = get_option('yturbo_options'); 
    if ( !is_feed( $yturbo_options['ytrssname'] ) ) {return;}
    
    $post = get_post();

	static $instance = 0;
	$instance++;

    if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) ) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}
    
    $html5 = current_theme_supports( 'html5', 'gallery' );
	$atts = shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => $html5 ? 'figure'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery' );

	$id = intval( $atts['id'] );

    $atts['include'] = str_replace(array("&#187;","&#8243;"), "", $atts['include']);
    $atts['orderby'] = str_replace(array("&#187;","&#8243;"), "", $atts['orderby']);
    $atts['order'] = str_replace(array("&#187;","&#8243;"), "", $atts['order']);
    $atts['exclude'] = str_replace(array("&#187;","&#8243;"), "", $atts['exclude']);

	if ( ! empty( $atts['include'] ) ) {
		$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
        

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
       
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
	} else {
		$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
        
	}

	if ( empty( $attachments ) ) {
		return '';
	}

    $output = PHP_EOL."<div data-block=\"gallery\">".PHP_EOL;

        foreach ( $attachments as $id => $attachment ) {
            $output .= '<img src="'.wp_get_attachment_url($id) . '" />'.PHP_EOL;
        }
       $output .= "</div>".PHP_EOL;
            
    return $output;
}
//функция преобразования стандартных галерей движка в турбо-галереи end

//функции открытия и закрытия комментариев begin
function yturbo_comments($comment, $args, $depth) { 
    $yturbo_options = get_option('yturbo_options');  
    $ytcommentsdate = $yturbo_options['ytcommentsdate'];
    $ytcommentsdrevo = $yturbo_options['ytcommentsdrevo'];
    $ytcommentsavatar = $yturbo_options['ytcommentsavatar'];
    echo PHP_EOL; 
    ?>
    <div data-block="comment"
         data-author="<?php comment_author(); ?>" 
         <?php if ($ytcommentsavatar=='enabled') { ?>
         data-avatar-url="<?php echo esc_url( get_avatar_url( $comment, 100 ) ); ?>" 
         <?php } ?>
         <?php if ($ytcommentsdate=='enabled') { ?>
         data-subtitle="<?php echo get_comment_date(); ?> в <?php echo get_comment_time(); ?>"
         <?php } ?>
    >
        <div data-block="content">
        <?php comment_text(); ?>
        </div>
        <?php if ($args['has_children'] && $ytcommentsdrevo=='enabled') { ?><?php echo '<div data-block="comments">'; ?><?php } 
}

function yturbo_comments_end($comment, $args, $depth) { 
$yturbo_options = get_option('yturbo_options');  
$ytcommentsdrevo = $yturbo_options['ytcommentsdrevo'];
?>
    </div>
    <?php if ($depth==1 && $ytcommentsdrevo=='enabled') { ?><?php echo '</div>'; ?><?php } ?>
<?php }
//функции открытия и закрытия комментариев end

//функция установки новых опций при обновлении плагина у пользователей begin
function yturbo_set_new_options() { 
$yturbo_options = get_option('yturbo_options');
if (!isset($yturbo_options['ytrelated'])) {$yturbo_options['ytrelated']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytrelatednumber'])) {$yturbo_options['ytrelatednumber']="5";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytrelatedselectthumb'])) {$yturbo_options['ytrelatedselectthumb']="medium";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytrelatedcache'])) {$yturbo_options['ytrelatedcache']="enabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytrelatedcachetime'])) {$yturbo_options['ytrelatedcachetime']="72";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytrelatedinfinity'])) {$yturbo_options['ytrelatedinfinity']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytad3'])) {$yturbo_options['ytad3']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytad3set'])) {$yturbo_options['ytad3set']="РСЯ";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytad3rsa'])) {$yturbo_options['ytad3rsa']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytadfox1'])) {$yturbo_options['ytadfox1']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytadfox2'])) {$yturbo_options['ytadfox2']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytadfox3'])) {$yturbo_options['ytadfox3']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytrazmer'])) {$yturbo_options['ytrazmer']="500";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytremoveturbo'])) {$yturbo_options['ytremoveturbo']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytauthorselect'])) {$yturbo_options['ytauthorselect']="Указать автора";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytcounterselect'])) {$yturbo_options['ytcounterselect']="Яндекс.Метрика";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytliveinternet'])) {$yturbo_options['ytliveinternet']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytgoogle'])) {$yturbo_options['ytgoogle']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytmailru'])) {$yturbo_options['ytmailru']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytrambler'])) {$yturbo_options['ytrambler']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytmediascope'])) {$yturbo_options['ytmediascope']="";update_option('yturbo_options', $yturbo_options);}  
if (!isset($yturbo_options['ytqueryselect'])) {$yturbo_options['ytqueryselect']="Все таксономии, кроме исключенных";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['yttaxlist'])) {$yturbo_options['yttaxlist']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytaddtaxlist'])) {$yturbo_options['ytaddtaxlist']="";update_option('yturbo_options', $yturbo_options);}    
if (!isset($yturbo_options['ytselectmenu'])) {$yturbo_options['ytselectmenu']="Не использовать";update_option('yturbo_options', $yturbo_options);}  
if (!isset($yturbo_options['ytshare'])) {$yturbo_options['ytshare']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytnetw'])) {$yturbo_options['ytnetw']="vkontakte,facebook,twitter,google,odnoklassniki,telegram,";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytgallery'])) {$yturbo_options['ytgallery']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytcomments'])) {$yturbo_options['ytcomments']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytcommentsavatar'])) {$yturbo_options['ytcommentsavatar']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytcommentsnumber'])) {$yturbo_options['ytcommentsnumber']="40";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytcommentsorder'])) {$yturbo_options['ytcommentsorder']="В начале старые комментарии";update_option('yturbo_options', $yturbo_options);}  
if (!isset($yturbo_options['ytcommentsdate'])) {$yturbo_options['ytcommentsdate']="enabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytcommentsdrevo'])) {$yturbo_options['ytcommentsdrevo']="enabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytpostdate'])) {$yturbo_options['ytpostdate']="enabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytexcerpt'])) {$yturbo_options['ytexcerpt']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytad4'])) {$yturbo_options['ytad4']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytad4set'])) {$yturbo_options['ytad4set']="РСЯ";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytad4rsa'])) {$yturbo_options['ytad4rsa']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytadfox4'])) {$yturbo_options['ytadfox4']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytad5'])) {$yturbo_options['ytad5']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytad5set'])) {$yturbo_options['ytad5set']="РСЯ";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytad5rsa'])) {$yturbo_options['ytad5rsa']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytadfox5'])) {$yturbo_options['ytadfox5']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytfeedback'])) {$yturbo_options['ytfeedback']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytfeedbackselect'])) {$yturbo_options['ytfeedbackselect']="right";update_option('yturbo_options', $yturbo_options);}  
if (!isset($yturbo_options['ytfeedbackselectmesto'])) {$yturbo_options['ytfeedbackselectmesto']="В конце записи";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbacktitle'])) {$yturbo_options['ytfeedbacktitle']="Обратная связь";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbacknetw'])) {$yturbo_options['ytfeedbacknetw']="call,mail,vkontakte,";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytfeedbackcall'])) {$yturbo_options['ytfeedbackcall']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbackcallback'])) {$yturbo_options['ytfeedbackcallback']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytfeedbackcallback2'])) {$yturbo_options['ytfeedbackcallback2']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytfeedbackcallback3'])) {$yturbo_options['ytfeedbackcallback3']="";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytfeedbackmail'])) {$yturbo_options['ytfeedbackmail']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbackvkontakte'])) {$yturbo_options['ytfeedbackvkontakte']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbackodnoklassniki'])) {$yturbo_options['ytfeedbackodnoklassniki']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbacktwitter'])) {$yturbo_options['ytfeedbacktwitter']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbackfacebook'])) {$yturbo_options['ytfeedbackfacebook']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbackgoogle'])) {$yturbo_options['ytfeedbackgoogle']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbackviber'])) {$yturbo_options['ytfeedbackviber']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbackwhatsapp'])) {$yturbo_options['ytfeedbackwhatsapp']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytfeedbacktelegram'])) {$yturbo_options['ytfeedbacktelegram']="";update_option('yturbo_options', $yturbo_options);} 
if (!isset($yturbo_options['ytseotitle'])) {$yturbo_options['ytseotitle']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytseoplugin'])) {$yturbo_options['ytseoplugin']="Yoast SEO";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytexcludeshortcodes'])) {$yturbo_options['ytexcludeshortcodes']="disabled";update_option('yturbo_options', $yturbo_options);}
if (!isset($yturbo_options['ytexcludeshortcodeslist'])) {$yturbo_options['ytexcludeshortcodeslist']="contact-form-7";update_option('yturbo_options', $yturbo_options);}

}
//функция установки новых опций при обновлении плагина у пользователей end

//функция формирования объявлений рекламной сети begin
function yturbo_turbo_ads() {
    $yturbo_options = get_option('yturbo_options');  
    
    $ytcomments = $yturbo_options['ytcomments']; 
    $ytshare = $yturbo_options['ytshare']; 
    
    $ytad1 = $yturbo_options['ytad1'];
    $ytad1set = $yturbo_options['ytad1set'];
    $ytad1rsa = $yturbo_options['ytad1rsa'];
    $ytadfox1 = html_entity_decode(stripcslashes($yturbo_options['ytadfox1']),ENT_QUOTES);
    $ytad2 = $yturbo_options['ytad2'];
    $ytad2set = $yturbo_options['ytad2set'];
    $ytad2rsa = $yturbo_options['ytad2rsa'];
    $ytadfox2 = html_entity_decode(stripcslashes($yturbo_options['ytadfox2']),ENT_QUOTES);
    $ytad3 = $yturbo_options['ytad3'];
    $ytad3set = $yturbo_options['ytad3set'];
    $ytad3rsa = $yturbo_options['ytad3rsa'];
    $ytadfox3 = html_entity_decode(stripcslashes($yturbo_options['ytadfox3']),ENT_QUOTES);
    $ytad4 = $yturbo_options['ytad4'];
    $ytad4set = $yturbo_options['ytad4set'];
    $ytad4rsa = $yturbo_options['ytad4rsa'];
    $ytadfox4 = html_entity_decode(stripcslashes($yturbo_options['ytadfox4']),ENT_QUOTES);
    $ytad5 = $yturbo_options['ytad5'];
    $ytad5set = $yturbo_options['ytad5set'];
    $ytad5rsa = $yturbo_options['ytad5rsa'];
    $ytadfox5 = html_entity_decode(stripcslashes($yturbo_options['ytadfox5']),ENT_QUOTES);
    
    $yturboads = '';
    if ($ytad1 == 'enabled') { 
        if ($ytad1set == 'РСЯ') { 
            $yturboads .= '<turbo:adNetwork type="Yandex" id="'.$ytad1rsa.'" turbo-ad-id="first_ad_place"></turbo:adNetwork>'.PHP_EOL;
        } 
        if ($ytad1set == 'ADFOX') { 
            $yturboads .= '<turbo:adNetwork type="AdFox" turbo-ad-id="first_ad_place">
                <![CDATA[
                    '.$ytadfox1.' 
                ]]>
            </turbo:adNetwork>'.PHP_EOL;
        } 
    } 
    if ($ytad2 == 'enabled') { 
        if ($ytad2set == 'РСЯ') { 
            $yturboads .= '<turbo:adNetwork type="Yandex" id="'.$ytad2rsa.'" turbo-ad-id="second_ad_place"></turbo:adNetwork>'.PHP_EOL;
        } 
        if ($ytad2set == 'ADFOX') { 
            $yturboads .= '<turbo:adNetwork type="AdFox" turbo-ad-id="second_ad_place">
                <![CDATA[
                    '.$ytadfox2.' 
                ]]>
            </turbo:adNetwork>'.PHP_EOL;
        } 
    }
    if ($ytad3 == 'enabled') { 
        if ($ytad3set == 'РСЯ') { 
            $yturboads .= '<turbo:adNetwork type="Yandex" id="'.$ytad3rsa.'" turbo-ad-id="third_ad_place"></turbo:adNetwork>'.PHP_EOL;
        } 
        if ($ytad3set == 'ADFOX') { 
            $yturboads .= '<turbo:adNetwork type="AdFox" turbo-ad-id="third_ad_place">
                <![CDATA[
                    '.$ytadfox3.' 
                ]]>
            </turbo:adNetwork>'.PHP_EOL;
        } 
    }
    if ($ytad4 == 'enabled' && $ytshare == 'enabled') { 
        if ($ytad4set == 'РСЯ') { 
            $yturboads .= '<turbo:adNetwork type="Yandex" id="'.$ytad4rsa.'" turbo-ad-id="fourth_ad_place"></turbo:adNetwork>'.PHP_EOL;
        } 
        if ($ytad4set == 'ADFOX') { 
            $yturboads .= '<turbo:adNetwork type="AdFox" turbo-ad-id="fourth_ad_place">
                <![CDATA[
                    '.$ytadfox4.' 
                ]]>
            </turbo:adNetwork>'.PHP_EOL;
        } 
    }
    if ($ytad5 == 'enabled' && $ytcomments == 'enabled') { 
        if ($ytad5set == 'РСЯ') { 
            $yturboads .= '<turbo:adNetwork type="Yandex" id="'.$ytad5rsa.'" turbo-ad-id="fifth_ad_place"></turbo:adNetwork>'.PHP_EOL;
        } 
        if ($ytad5set == 'ADFOX') { 
            $yturboads .= '<turbo:adNetwork type="AdFox" turbo-ad-id="fifth_ad_place">
                <![CDATA[
                    '.$ytadfox5.' 
                ]]>
            </turbo:adNetwork>'.PHP_EOL;
        } 
    }
    
    return $yturboads;
}
//функция формирования объявлений рекламной сети end

//функция вывода виджета обратной связи start
function yturbo_widget_feedback() {
    $yturbo_options = get_option('yturbo_options');  
    
    if ($yturbo_options['ytfeedback'] == "disabled") return;
    
    
    $content = PHP_EOL.PHP_EOL.'<div data-block="widget-feedback" data-title="'.$yturbo_options['ytfeedbacktitle'].'" data-stick="'.$yturbo_options['ytfeedbackselect'].'">'.PHP_EOL;
     
    $ytfeedbacknetw = explode(',', $yturbo_options['ytfeedbacknetw']);
    foreach ($ytfeedbacknetw as $network) {
        switch ($network) { 
        case "call":
            if ($yturbo_options['ytfeedbackcall']) {
                $content .= '<div data-type="call" data-url="'.$yturbo_options['ytfeedbackcall'].'"></div>'.PHP_EOL;
            }
            break;  
        case "callback":
            if ($yturbo_options['ytfeedbackcallback']) {
                $content .= '<div data-type="callback" data-send-to="'.$yturbo_options['ytfeedbackcallback'].'"';
                if ($yturbo_options['ytfeedbackcallback2'] && $yturbo_options['ytfeedbackcallback3']) {
                    $content .= ' data-agreement-company="'.stripslashes($yturbo_options['ytfeedbackcallback2']).'" data-agreement-link="'.$yturbo_options['ytfeedbackcallback3'].'"';
                } 
            }
            $content .= '></div>'.PHP_EOL;
            break;     
        case "chat":
            $content .= '<div data-type="chat"></div>'.PHP_EOL;
            break;
        case "mail":
            if ($yturbo_options['ytfeedbackmail']) {
                $content .= '<div data-type="mail" data-url="'.$yturbo_options['ytfeedbackmail'].'"></div>'.PHP_EOL;
            }
            break;
        case "vkontakte":
            if ($yturbo_options['ytfeedbackvkontakte']) {
                $content .= '<div data-type="vkontakte" data-url="'.$yturbo_options['ytfeedbackvkontakte'].'"></div>'.PHP_EOL;
            }    
            break;
        case "odnoklassniki":
            if ($yturbo_options['ytfeedbackodnoklassniki']) {
                $content .= '<div data-type="odnoklassniki" data-url="'.$yturbo_options['ytfeedbackodnoklassniki'].'"></div>'.PHP_EOL;
            }
            break;
        case "twitter":
            if ($yturbo_options['ytfeedbacktwitter']) {
                $content .= '<div data-type="twitter" data-url="'.$yturbo_options['ytfeedbacktwitter'].'"></div>'.PHP_EOL;
            }
            break;
        case "facebook":
            if ($yturbo_options['ytfeedbackfacebook']) {
                $content .= '<div data-type="facebook" data-url="'.$yturbo_options['ytfeedbackfacebook'].'"></div>'.PHP_EOL;
            }
            break;
        case "google":
            if ($yturbo_options['ytfeedbackgoogle']) {
                $content .= '<div data-type="google" data-url="'.$yturbo_options['ytfeedbackgoogle'].'"></div>'.PHP_EOL;
            }
            break;
        case "viber":
            if ($yturbo_options['ytfeedbackviber']) {
                $content .= '<div data-type="viber" data-url="'.$yturbo_options['ytfeedbackviber'].'"></div>'.PHP_EOL;
            }
            break;
        case "whatsapp":
            if ($yturbo_options['ytfeedbackwhatsapp']) {
                $content .= '<div data-type="whatsapp" data-url="'.$yturbo_options['ytfeedbackwhatsapp'].'"></div>'.PHP_EOL;
            }
            break;
        case "telegram":
            if ($yturbo_options['ytfeedbacktelegram']) {
                $content .= '<div data-type="telegram" data-url="'.$yturbo_options['ytfeedbacktelegram'].'"></div>'.PHP_EOL;
            }
            break;
        }    
    }
    unset($network);
    
    $content .= '</div>'.PHP_EOL;
    return $content;

}    
//функция вывода виджета обратной связи end

//функция удаления указанных шорткодов start
function yturbo_strip_shortcodes( $content ) {
    $yturbo_options = get_option('yturbo_options');  
    
    if ($yturbo_options['ytexcludeshortcodes'] == 'disabled' or !$yturbo_options['ytexcludeshortcodeslist']) return $content;
    
    global $shortcode_tags;
    $stack = $shortcode_tags;
    
    $code = array_map('trim', explode(',', $yturbo_options['ytexcludeshortcodeslist']));
    
    $how_many = count($code);
    for($i = 0; $i < $how_many; $i++){
        $arr[$code[$i]] = 1;
    }

    $shortcode_tags = $arr;
    $content = strip_shortcodes($content);
    $shortcode_tags = $stack;
    
    return $content;
}    
//функция удаления указанных шорткодов end