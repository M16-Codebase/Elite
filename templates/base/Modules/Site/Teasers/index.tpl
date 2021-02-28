{?$pageTitle = 'Тизеры — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Тизеры</h1>
	<div class="content-options" id="teasers">
		{include file="Admin/components/actions_panel.tpl" 
			multiple = true
			buttons = array(
				'back' => '/site/',
				'add' => array(
					'class' => 'show-create'
				)
			)
		)}	
	</div>
</div>
<div class="content-scroll">
	<div class="viewport">
		{include file="Modules/Site/Teasers/teasers.tpl"}
	</div>
</div>