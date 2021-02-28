<h1>Отзывы</h1>

<div class="reviews-filter">
	<form method="GET" class="clear_form">
		<div class="field a-left">
			<div class="title">Показать отзывы только со статусом</div>
			<select class="selectmenu small-select" name="status">
				<option value="">Все</option>
				<option value="new">Новые</option>
				<option value="approved">Подтвержденные</option>
				<option value="decline">Отклоненные</option>
			</select>
		</div>
		<div class="search-button a-left">
			<div class="button submit">Показать</div>
			<a href="/product/reviews/" class="clear">Сбросить</a>
		</div>		
	</form>	
</div>

<div class="reviews">
    {include file="Modules/Catalog/Admin/reviews_list.tpl"}
</div>