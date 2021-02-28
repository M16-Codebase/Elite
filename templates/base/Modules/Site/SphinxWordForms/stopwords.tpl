{?$pageTitle = 'Редактирование стоп-слов — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Редактирование стоп-слов</h1>	
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
		{if empty($error)}
			<form class="stopwords-form">
				<ul class="tags-cont syns-area">
					{foreach from=$stopwords item=stopword}
						<li>{$stopword}</li>
					{/foreach}	
				</ul>	
				<input type="submit" value="Сохранить" class="stopwords-submit a-button-blue">
			</form>
		{else}
			<div class="empty_content_message">Файл недоступен для редактирования.</div>
		{/if}
	</div>
</div>	
