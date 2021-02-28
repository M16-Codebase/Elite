{?$pageTitle = (!empty($post.title) ? '«' . $post.title . '» — ' : '') . (!empty($action_rus) ? $action_rus . ' — ' : '') . (!empty($confTitle) ? $confTitle : '')}
{?$bc_location = empty($bc_location)? array('url'=>'/pages/', 'title' => 'Тексты к страницам') : $bc_location}
{? $del_name = array(
		'article' => 'статьи',
		'areaguide' => 'статьи',
		'blog' => 'записи',
		'news' => 'новости',
		'pages' => 'текста',
		'test_result' => 'текста',
)}
{? $save_name = array(
		'article' => 'Статья',
		'areaguide' => 'Статья',
		'blog' => 'Запись',
		'news' => 'Новость',
		'pages' => 'Текст',
		'test_result' => 'текста',
)}
{?$delete_params = empty($delete_params) ? array('data' => array('id' => $post.id, 'delurl' => '/' . $moduleUrl . '/del/', 'delname' => $del_name[$post.type])) : $delete_params}
{if $constants.segment_mode != 'none'}
    {?$bc_location.url .= (!empty($post.segment_id) ? ($post.type == 'pages' ? '&s=' : '?s=') . $post.segment_id : '')}
{/if}

<div class="content-top">
	<h1>Редактирование {if !empty($del_name[$post.type])}{$del_name[$post.type] . ' '}{/if}{if !empty($post.title)}«{$post.title}»{/if}</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl"
			buttons = array(
				'back' => ($bc_location.url),
				'save' => array('data' => array('savename' => $save_name[$post.type])),
				'delete' => $delete_params
			)
			site_url = (!empty($site_url) ? $site_url : ($moduleUrl != 'pages'? $post->getUrl($post.segment_id) : ''))}
	</div>
</div>

<div class="content-scroll">
	<div class="viewport">
		<div class='white-blocks list'>
		{include file='Modules/Posts/Pages/editPost.tpl'}
		</div>
	</div>
</div>
