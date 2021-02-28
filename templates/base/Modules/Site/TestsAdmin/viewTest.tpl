{?$pageTitle = 'Вопросы — ' . (!empty($confTitle) ? $confTitle : '')}

<div class="tabs-cont main-tabs">
	<div class="content-top">
		<h1>{$test_entity.title}</h1>
		<div class="content-options">
			{include file="Admin/components/actions_panel.tpl" 
				multiple = true
				buttons = array(
					'back' => '/site/',
				)
			)}	
			
		{?$req_tab = !empty($smarty.get.tab) ? $smarty.get.tab : 'questions'}
			{include file="Admin/components/tabs.tpl" 
				tabs = array(
					'questions' => array(
						'url' => '?id=' . $test_entity.id . '&tab=questions',
						'text' => 'Вопросы',
						'current' => ($req_tab == 'questions'),
					),
					'results' => array(
						'url' => '?id=' . $test_entity.id . '&tab=results',
						'text' => 'Результаты',
						'current' => ($req_tab == 'results'),
					),
				)}
		</div>
	</div>

	<div id="tabs-pages" class="content-scroll-cont">
		<div id="questions" class="tab-page actions-cont {if $req_tab == 'questions'} m-current{/if}" data-del-url='/tests-admin/deleteQuestion/' data-add-url="/tests-admin/questionFields/">
			<div class="content-scroll">
				<div class="aside-panel">
						{include file="Admin/components/actions_panel.tpl"
							buttons = array(
								'add' => array(
									'class' => 'show-create'
								)				
							)}
				</div>
				<div class="viewport" data-test-id="{$test_entity.id}">
					<div class="white-blocks sortable" data-url="/tests-admin/moveQuestion/" data-newpositionname="position" data-sendattrs="test_id;id" data-cont="#questions .white-blocks">
						{include file='Modules/Site/TestsAdmin/questionsList.tpl'}
					</div>
				</div>
			</div>
		</div>

		<div id="results" class="tab-page actions-cont{if $req_tab == 'results'} m-current{/if}" data-del-url='/tests-admin/deleteResult/' data-add-url="/tests-admin/resultFields/">
			<div class="content-scroll">
				<div class="aside-panel">
					{include file="Admin/components/actions_panel.tpl"
							buttons = array(
								'add' => array(
									'class' => 'show-create'
								)					
							)}
				</div>
				<div class="viewport" data-test-id="{$test_entity.id}">
					<div class="white-blocks">
					{include file='Modules/Site/TestsAdmin/resultsList.tpl'}
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
