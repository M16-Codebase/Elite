{?$pageTitle = (!empty($action_rus) ? $action_rus . ' — ' : '') . (!empty($confTitle) ? $confTitle : '')}
{?$tab_url = !empty($section_url) ? $section_url : '/'.$moduleUrl.'/?'}
{?$back_url = !empty($back_url) ? $back_url : '/site/'}
<div class="tabs-cont main-tabs">
	<div class="content-top">
		<h1>{!empty($action_rus)? $action_rus : "Тексты к страницам"}</h1>
		<div class="content-options">
			{include file="Admin/components/actions_panel.tpl"
				multiple = true
				buttons = array(
					'back' => $back_url,
					'add' => '/'.$moduleUrl.'/createPostFields/' . (!empty($current_theme) ? '?theme='.$current_theme.id : ''),
			)}
			{if $constants.segment_mode != 'none'}
				{?$req_tab = !empty($smarty.get.s) ? $smarty.get.s : 's' . $s.id}
				{?$tabs = Array()}
				{foreach from=$segments item=$s}
					{?$tabs['s'.$s.id]['url'] = '?s=' . $s.id}
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
				<div class="posts-list white-blocks viewport">
					<div class="wblock white-block-row white-header">
						<div class="w5">Название</div>
						<div class="w1"></div>
						<div class="w3">Дата</div>
						<div class="w3"></div>
					</div>
					<div class="white-body">
						{include file="Modules/Posts/Pages/postsList.tpl"}
					</div>
				</div>
			</div>
		{/if}
	</div>
</div>
