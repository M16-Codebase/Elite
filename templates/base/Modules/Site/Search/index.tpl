{?$admin_page = 1}
{?$pageTitle = 'Поисковые фразы — ' . (!empty($confTitle) ? $confTitle : '')}

<div class="content-top">
{*	{?$show_filter=true}*}
	{*{capture assign="phraseContent" name="phraseContent"}
		{if !empty($phrases)}
				<div class="wblock white-header white-block-row">
					<div class="w4">Фраза</div>
					<div class="w7">Url</div>
					<div class="w1"></div>
				</div>
				<div class="white-body">
					{include file="Modules\Site\Search\phrasesList.tpl"}
				</div>
			{else}
			<div class="white-body">
				{?$show_filter=false}
				<div class="wblock white-block-row">
					<div class="w12">
						Фразы не созданы
					</div>
				</div>
			</div>	
		{/if}		
	{/capture}*}
	
	<h1>Поисковые фразы</h1>
	<div class="content-options">
		{?$buttons = array(
			'back' => array(
				'text' => 'Назад',
				'url'=> '/site/'
			),
			'add' => array(
				'text' => 'Добавить',
				'class' => 'submit'
			)
		)}
		{include file="Admin/components/actions_panel.tpl"
			assign = searchPhrase
			buttons = $buttons}
		{$searchPhrase|html}
	</div>
	{*{if !empty($show_filter)}
		<div class="filter">
			<form method="GET" class="filter-phrase a-inline-cont">
				<div class="field">
					<div class="title">Сортировать по</div>
					<div class="chosen-cont">
						<select name="order" class="w7 chosen sortPhrase">
							<option value="phrase">фразам</option>
							<option value="url">url</option>
						</select>
					</div>
				</div>
				<div class="search-button">
					<button class="submit"></button>
				</div>
			</form>
		</div>
	{/if}*}
</div>

<div class="content-scroll">
	<div class="viewport">
		<div class="white-blocks list">
			{include file="Modules/Site/Search/phrasesList.tpl"}
		</div>
		<form class="order-logs user-form" action="/site-search/phrasesList/" method="GET"><input type="hidden" name="sort" class="input-sort" /></form>
	</div>
</div>
{include file="/Modules/Site/Search/phraseFields.tpl" assign=add_phrase}
{capture assign=editBlock name=editBlock}
	{$add_phrase|html}
{/capture}