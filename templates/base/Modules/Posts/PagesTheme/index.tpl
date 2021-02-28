{if !empty($current_theme) && $current_theme.count}
	{if !empty($action_rus)}{?$title = $action_rus}{else}{?$title = 'Темы'}{/if} 
{elseif !empty($themes)} 
	{if !empty($action_rus)}{?$title = $action_rus_theme}{else}{?$title = 'Темы'}{/if} 
{/if}
{?$pageTitle = (!empty($current_theme.title) ? $current_theme.title . ' — ': '') . (!empty($title) ? $title . ' — ' : '') . (!empty($confTitle) ? $confTitle : '')}
<div class="tabs-cont main-tabs">
	<div class="content-top">
		<h1>{if !empty($current_theme.title)}{$current_theme.title}{elseif !empty($title)}{$title}{/if}</h1>
		<div class="content-options">
			{include file="Admin/components/actions_panel.tpl"
				multiple = true
				buttons = array(
					'back' => (empty($current_theme) && empty($only_posts_create)) ? '/site/' : '/'.$moduleUrl.'/'.(!empty($current_theme.parent_id) ? '?theme='.$current_theme['parent_id'] : ''),
					'add-theme' => (empty($current_theme.count) && !$only_posts_create) ? array(
						'icon' => 'add',
						'class' => 'action-add',
						'text' => 'Добавить тему',
						'data' => array('url' => '/'.$moduleUrl.'/createThemeFields/' . (!empty($current_theme) ? '?theme='.$current_theme.id : ''))
					) : 0,
					'add-post' => (!empty($current_theme) && empty($current_theme.theme_count))? array(
						'icon' => 'add',
						'class' => 'action-add',
						'text' => 'Добавить статью',
						'data' => array('url' => '/'.$moduleUrl.'/createPostFields/' . (!empty($current_theme) ? '?theme='.$current_theme.id : ''))
					) : 0,
					'site' => ( $moduleUrl == 'news-admin'? '#' : '')
			)}
			{if $constants.segment_mode != 'none' && !empty($current_theme) && $current_theme.count}
				{?$req_tab = !empty($smarty.get.s) ? $smarty.get.s : 's' . $s.id}
				{?$tabs = Array()}
				{foreach from=$segments item=$s}
					{?$tabs['s'.$s.id]['url'] = '?theme=' . $current_theme.id . '&s=' . $s.id}
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
		{if !empty($current_theme) && $current_theme.count}
			{if $constants.segment_mode != 'none'}
				{foreach from=$segments item=$s}
					<div id="s{$s.id}" class="tab-page actions-cont {if $req_tab == $s.id} m-current{/if}">
						<div class="content-scroll">
							<div class="posts-list white-blocks viewport">
								<div class="wblock white-block-row white-header">
									<div class="w5">Название</div>
									<div class="w1"></div>
									<div class="w3">Дата</div>
									<div class="w3"></div>
								</div>
								<div class="white-body">
								</div>
							</div>
						</div>
					</div>
				{/foreach}
			{else}
				<div class="content-scroll">
					<div class="white-blocks posts-list viewport sortable" data-url="/{$moduleUrl}/movePost/" data-newpositionname="position" data-cont=".view-content">
						{include file="Modules/Posts/Pages/postsList.tpl"}
					</div>
				</div>
			{/if}
		{elseif !empty($themes)}
			<div class="content-scroll">
				<div class="white-blocks themes-list viewport">
					<div class="wblock white-block-row white-header">
						<div class="w05"></div>
						<div class="w5">Название темы</div>
						<div class="w2"></div>
						<div class="w2">Кол-во статей</div>
						<div class="w05"></div>
						<div class="w2"></div>
					</div>
					<div class="white-body sortable" data-url="/{$moduleUrl}/moveTheme/" data-newpositionname="position" data-cont=".view-content">
					{include file="Modules/Posts/PagesTheme/theme_list_element.tpl" themes_list= $themes}
					</div>
				</div>
			</div>
		{else}
			<div class="content-scroll">
				<div class="white-blocks viewport">
					<div class="wblock white-block-row">
						<div class="w12">Темы или статьи еще не созданы.</div>
					</div>
				</div>
			</div>
		{/if}
	</div>
</div>