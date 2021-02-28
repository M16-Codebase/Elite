{?$admin_page = 1}
{?$pageTitle = 'Логи поисковых запросов — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Логи поисковых запросов</h1>
	{*<div class="filter">
		<form method="GET" class="a-inline-cont">
			<div class="field">
				<div class="title">Сортировать по</div>
				<div class="chosen-cont">
					<select name="order" class="chosen">
						<option value="count">количеству запросов</option>
						<option value="phrase">фразам</option>
						<option value="date">по дате</option>
					</select>
				</div>
			</div>
			<div class="search-button">
				<button class="submit"></button>
			</div>
		</form>
	</div>*}
</div>
{*<div class="select-variant choose-url tab-top">
	<div class="title">Сортировать по</div>
	<select name="url" selected="selected">
		<option value="count">количеству запросов</option>
		<option value="phrase">фразам</option>
		<option value="date">по дате</option>
	</select>
</div>*}
<div class="content-scroll">
	<div class="viewport">
		<div class="white-blocks list">
			{include file="Modules/Site/Search/logsList.tpl"}
		</div>
		<form class="order-logs user-form" action="/site-search/logsList/" method="GET"><input type="hidden" name="sort" class="input-sort" /></form>
	</div>
</div>