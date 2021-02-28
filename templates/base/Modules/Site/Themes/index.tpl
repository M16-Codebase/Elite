{?$pageTitle = (!empty($action_rus) ? $action_rus : 'Темы') . ' — Управление сайтом | Сантехкомплект'}
{?$admin_page = 1}
<div class="content-top">
	<h1>{if !empty($action_rus)}{$action_rus}{else}Управление темами{/if}</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl"
			buttons = array(
				'add' => array(
					"class" => "to-form"
				),
		)}
	</div>
</div>
<div class="content-scroll">
	<div class="viewport white-blocks">
		<div class="wblock white-block-row white-header">
			<div class='w1'></div>
			<div class='w10'>Название</div>
			<div class='w1'></div>
		</div>
		<div class="white-body sortable-themes{*ui-sortable sortable-themes*}">
			{if !empty($themes) && !empty($themes[0])}
				{include file="Modules/Site/Themes/theme_list_element.tpl" themes_list= $themes[0]}
			{/if}
		</div>
	</div>
</div>
{capture assign=editBlock name=editBlock}
	<form class="create-theme" action="/site-themes/" method="post">
		<div class="content-top">
			<h1>Название новой темы</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array('text' => 'Отмена'),
					'save' => array(
						'text' => 'Создать',
						'class' => 'save-theme',
						'url' => '#'
					)
				)}
				{include file="Admin/components/actions_panel.tpl"
					assign = editHandlers
					buttons = $buttons}	
				{$editHandlers|html}
			</div>
		</div>
		<div class="content-scroll">
			<div class="viewport white-blocks">
				<div class="w12">
					<input type="text" name="add" class="bold" />
				</div>
			</div>
		</div>	
	</form>
{/capture}