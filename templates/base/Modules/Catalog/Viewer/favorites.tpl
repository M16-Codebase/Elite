{?$currentCatalog = $current_type->getCatalog()}
<h1>Отобранные {$currentCatalog.word_cases['v']['2']['i']}</h1>			
<form method="POST" class="actions-list items_edit selection">
	{include file="Admin/components/actions_panel.tpl" 
		multiple = true
		buttons = array(
			'back' => '/site/',
			'no_selection' => array(
				'text' => 'Из выборки',
				'inactive' => 1
			),
			'delete' => array(
				'text' => 'Удалить все'
			),
			'pdf' => array('text' => 'Сделать PDF'),
			'contacts' => array(
				'linkattr' => 'target=_blank',
				'url' => '/catalog-view/printContacts/'
			),
			'print' => array(
				'linkattr' => 'target=_blank',
				'url' => '/catalog-view/printFavorites/'
			)
		)}
		
	{?$favorites = $account->getFavorites()}
	<div class="result-header header-objects">
		<div class="filter-check a-left">
			<input type="checkbox" value="" class="check-all" />
		</div>
		<div class="filter-result a-right">
			Найдено {$favorites.counts.variants|plural_form:$currentCatalog.word_cases['v']['2']['r']:$currentCatalog.word_cases['v']['1']['i']:$currentCatalog.word_cases['v']['2']['r']} в {$favorites.counts.items|plural_form:$currentCatalog.word_cases['i']['2']['p']:$currentCatalog.word_cases['i']['1']['p']:$currentCatalog.word_cases['i']['2']['p']}
		</div>
	</div>
	<div class="main-content-gray offer-body">
		{if !empty($favorites.items)}
			<div class="pdf-texts">
				<div class="input-item-cont">
					<div class="input-item">
						<div class="change-history"></div>
							<div class="input-title h4">Заголовок PDF-презентации</div>
						<div class="field justify">
							<input type="text" name="comments[title]" data-type="title" class="fav-comments" />
						</div>
					</div>
				</div>
				<div class="input-item-cont">
					<div class="input-item">
						<div class="change-history"></div>
							<div class="input-title h4">Вступительное слово для PDF-презентации</div>
						<div class="field justify">
							<textarea name="comments[text]" data-type="text" class="resizeable fav-comments" data-min-height="40"></textarea>
						</div>
					</div>
				</div>
			</div>
			{include file="Modules/Catalog/Viewer/favoritesList.tpl"}
		{else}
			<div class="empty-list">
				В выборке нет {$currentCatalog.word_cases['v']['2']['r']}
			</div>
		{/if}
	</div>
</form>