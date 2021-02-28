{?$pageTitle = 'Редактирование статьи — ' . $post.title}
{?$bc_location=array('url'=>'/segment-text/urlSection/?id=' . $url_data.id, 'title' => 'Тексты к страницам')}
{include file="Modules/Posts/Pages/edit.tpl" action_rus="Редактирование статьи «" . $post.title . "»"}
{include file="/Modules/Segment/Text/collection_links.tpl"}
{*{?$site_link= $collection_links[$post_type]}*}