{?$pageTitle = $post.title . ''}
{?$bc_location=array('url'=>('/article-admin/?theme=' . $post.theme_id), 'title' => 'Статьи')}
{include file="Modules/Posts/PagesTheme/edit.tpl"  action_rus='Статьи'}