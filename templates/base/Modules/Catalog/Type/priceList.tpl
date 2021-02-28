{?$pageTitle ='Прайс - листы — Управление сайтом | ТехноАльт'}
<h1>Прайс - листы</h1>
<div class="using-page">
	<ul class="using-types-list">
	{foreach from=$main_types item=t}
		<li class="category-block">
			<div class="category-title">{$t.title}</div>
			<ul>
			{?$price_lists = $t.price_list}
			{foreach from=$prices key=pr item=prop}
				<li class="using-type-item">
					<div class="item-title">
						{if (!empty($price_lists[$pr]))}
							<a href="{$price_lists[$pr]['download_path']}">{$prop.title}</a>
						{else}
							Нет файла
						{/if}
					</div>
				</li>
			{/foreach}
			</ul>
		</li>
	{/foreach}
	</ul>
</div>
