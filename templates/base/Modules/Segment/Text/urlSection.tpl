{?$pageTitle = (!empty($url_data.title) ? $url_data.title . ' — ' : '') . 'Тексты к страницам — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="tabs-cont main-tabs">
	<div class="content-top">
		<h1>{$url_data.title}</h1>
		<div class="content-options">
			{include file='Admin/components/actions_panel.tpl'
				buttons = array(
					'back' => ('/segment-text/'),
					'add' => 1)
				)
			}
			{if $constants.segment_mode != 'none'}
				{?$req_tab = !empty($smarty.get.s) ? $smarty.get.s : $request_segment.id}
				{?$tabs = Array()}
				{foreach from=$segments item=$s}
					{?$tabs['s'.$s.id]['url'] = '?id=' . $url_data.id . '&s=' . $s.id}
					{?$tabs['s'.$s.id]['text'] = $s.title}
					{?$tabs['s'.$s.id]['data'] = array('postUrl' => "/" . $moduleUrl . "/postsList/", 'segment' => $s.id)}
					{?$tabs['s'.$s.id]['current'] = ($req_tab == $s.id)}
				{/foreach}
				{?$count=0}
				{include file="Admin/components/tabs.tpl" tabs = $tabs}
			{/if}		
		</div>
	</div>
	<div id="tabs-pages" class="content-scroll-cont">
		{if $constants.segment_mode != 'none'}
			{foreach from=$segments item=$s}
				<div id="s{$s.id}" class="tab-page actions-cont {if $req_tab == $s.id} m-current{/if}">
					<div class="content-scroll">
						<div class="posts-list white-blocks viewport">
							<div class="wblock white-block-row white-header">
								<div class="w12">Текст</div>
							</div>
							<div class="white-body">
								{foreach from=$posts key=post_key item=post_data}
										{if !empty($post_data.posts[$s.id])}
											<div class="wblock white-block-row" data-id="{$post_data.posts[$s.id].id}">
												<div class="w9">
													{$post_data.posts[$s.id].title}
												</div>
												<div class="w1 action-button m-status-icon" title="{if $post_data.posts[$s.id].status=="close" || $post_data.posts[$s.id].status=="public"}Опубликован{elseif $post_data.posts[$s.id].status=="new"}Черновик{elseif $post_data.posts[$s.id].status=="hidden"}Скрыт{/if}">
													<i class="icon-{if $post_data.posts[$s.id].status=="close" || $post_data.posts[$s.id].status=="public"}show{elseif $post_data.posts[$s.id].status=="new"}draft{elseif $post_data.posts[$s.id].status=="hidden"}hide{/if}"></i>
												</div>
												<a href="/segment-text/edit/?id={$post_data.posts[$s.id].id}" class="action-button action-edit w1 m-border" title="Редактировать">
													<i class="icon-edit"></i>
												</a>
												<div class="action-button action-delete w1 m-border" title="Удалить"  data-delname="текста">
													<i class="icon-delete"></i>
												</div>
											</div>
										{/if}
								{/foreach}
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		{else}
			<div class="content-scroll">
				<div class="posts-list white-blocks viewport">
					<div class="wblock white-block-row white-header">
						<div class="w12">Текст</div>
					</div>
					<div class="white-body">
						{if !empty($posts)}
							{foreach from=$posts key=post_key item=post_data}
								{if !empty($post_data.posts[0])}
									<div class="wblock white-block-row" data-id="{$post_data.posts[0].id}">
										<div class="w9">
											{$post_data.posts[0].title}
										</div>
										<div class="w1 action-button m-status-icon" title="{if $post_data.posts[0].status=="close" || $post_data.posts[0].status=="public"}Опубликован{elseif $post_data.posts[0].status=="new"}Черновик{elseif $post_data.posts[0].status=="hidden"}Скрыт{/if}">
											<i class="icon-{if $post_data.posts[0].status=="close" || $post_data.posts[0].status=="public"}show{elseif $post_data.posts[0].status=="new"}draft{elseif $post_data.posts[0].status=="hidden"}hide{/if}"></i>
										</div>
										<a href="/segment-text/edit/?id={$post_data.posts[0].id}" class="action-button action-edit w1 m-border" title="Редактировать">
											<i class="icon-edit"></i>
										</a>
										<div class="action-button action-delete w1 m-border" title="Удалить"  data-delname="текста">
											<i class="icon-delete"></i>
										</div>
									</div>
								{/if}
							{/foreach}
						{else}
							<div class="wblock white-block-row empty-result">
								<div class="w12">Нет записей</div>
							</div>
						{/if}
					</div>
				</div>
			</div>
		{/if}
	</div>
</div>
{include file="/Modules/Segment/Text/addUrlSection.tpl" assign=add_url}
{capture assign=editBlock name=editBlock}
	{$add_url|html}
{/capture}