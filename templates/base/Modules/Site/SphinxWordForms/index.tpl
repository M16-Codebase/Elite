{?$page_title = "Синонимы Sphinx"}
<div class="content-top">
	<h1>Автоматическая замена синонимов</h1>
	<div class="content-options">
		{?$arr=array("back", "add")}
		{include file="Admin/components/actions_panel.tpl" buttons=array(
			'back' => (!empty($smarty.server["HTTP_REFERER"])) ? $smarty.server["HTTP_REFERER"] : "#",
			'add' => 1
		)}
	</div>
</div>
<div class="syn-body content-scroll">
	<div class="white-blocks viewport">
		<div class="wblock white-block-row syn-manipulations{if empty($wordforms) && empty($smarty.get.search)} a-hidden{/if}">
			<form class="syn-search-cont w7">
				<input class="syn-search" placeholder="Поиск" type="text"{if !empty($smarty.get.search)} value="{$smarty.get.search}"{/if}>
				<button class="magnif">
				</button>
				<div class="auto-menu">
				</div>
			</form>
			<div class="w1">
				<span class="reset-syns" title="Сбросить фильтр"></span>
			</div>
			<div class="w4 m-border">
				<span class="sort-string">Сортировать А — Я</span>
			</div>	
		</div>
		<ul class="syn-group slidebox-cont">
			{include file='Modules/Site/SphinxWordForms/wordformList.tpl'}
		</ul>		
	</div>
</div>	


{capture assign="editBlock" name="editBlock"}
	<form action="/sphinx-wordforms/add/" method="POST" class="add-syn-form add-form">
		<div class="content-top">
			<h1>Добавление синонима</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array('text' => 'Отмена'),
					'save' => array(
						'text' => 'Создать',
						'class' => 'submit'
					)
				)}
				{include file="Admin/components/actions_panel.tpl"
					assign = addFormButtons
					buttons = $buttons}
				{$addFormButtons|html}
				<input type="text" name="dst_form" class="change-main-word">
			</div>
		</div>
		<div class="content-scroll">
			<div class="white-blocks viewport">
				<div class="syn-editor wblock">
					<div class="editor-descr">
						Синонимы через запятую
					</div>
					<ul class="tags-cont syns-area">

					</ul>
					<ul class="errors">

					</ul>
				</div>	
			</div>
		</div>
	</form>
{/capture}