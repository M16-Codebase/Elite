{if !empty($posts)}
{?$del_name = array(
	'blog' => 'записи',
	'news' => 'новости',
)}
	{foreach from=$posts item="post"}
		<div class="wblock white-block-row" data-id="{$post.id}" data-position="{$post.num}" data-post_type="{$post.type}">
			{if $post.type == 'article'}
			<div class="drag-drop w05"></div>
			{/if}
			<div class="w4">
				{if !empty($post.title)}{$post.title}{else}Без названия{/if}
				<br><span class="small-descr">{if !empty($post.author)}{$post.author}{/if}</span>
			</div>
			{if $post.type == 'article'}
			<div class="05"></div>
			<div class="w1"></div>
			{else}
			<div class="w2"></div>
			{/if}
			<div class="w2">{if $post.type == 'blog' || $post.type == 'news'}{if !empty($post.date)}{$post.date}{/if}{/if}</div>
			<div class="w1 action-button m-status-icon" title="{if $post.status=="close" || $post.status=="public"}Опубликован{elseif $post.status=="new"}Черновик{elseif $post.status=="hidden"}Скрыт{/if}">
				<i class="icon-{if $post.status=="close" || $post.status=="public"}show{elseif $post.status=="new"}draft{elseif $post.status=="hidden"}hide{/if}"></i>
			</div>
			<a href="{$post->getUrl($post.segment_id)}" class="action-button action-site w1 m-border" title="Смотреть на сайте"><i class="icon-site"></i></a>
			<a href="/{$post.type}{if $post.type != 'pages'}-admin{/if}/edit/?id={$post.id}" class="action-button action-edit w1 m-border" title="Редактировать">
				<i class="icon-edit"></i>
			</a>
			<div class="action-button action-delete w1 m-border" title="Удалить" data-delurl="{"/" . $moduleUrl . '/del/'}" data-delname="{if !empty($del_name[$post.type])}{$del_name[$post.type]}{/if}">
				<i class="icon-delete"></i>
			</div>
		</div>
	{/foreach}
{else}
	<div class="wblock white-block-row empty-result">
		<div class="w12">Нет записей</div>
	</div>
{/if}