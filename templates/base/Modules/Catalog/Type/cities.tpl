{?$pageTitle = 'Настройки — Города'}
<h1>Города</h1>
<table class="ribbed">
    <tr>
        <th class="city-th">Название</th>
        <th class="city-th">Страна</th>
        {foreach from=$post_types item=$type_title key=$type_key}
            <th>{$type_title}</th>
        {/foreach}
    </tr>
    {if !empty($cities)}
        {foreach from=$cities item=city key=city_id}
            <tr data-city_id="{$city_id}" data-country_id="{$city.country_id}">
                <td>{$city.name}</td>
                <td>
					{if $account->isPermission('catalog-type', 'saveCityCountry')}
						<a href="#" class="changeCountry">{if !empty($city.country_id)}{$countries[$city.country_id]['value']}{else}Не назначена{/if}</a>
					{else}
						{if !empty($city.country_id)}{$countries[$city.country_id]['value']}{else}Не назначена{/if}
					{/if}
				</td>
                {foreach from=$post_types item=$type_title key=$type_key}
                    <td>
                        {foreach from=$segments item=$segm name=posts_by_segments_list}
							{if $account->isPermission('catalog-type', 'addPost')}
								{if !empty($city.posts[$segm['id']][$type_key])}
									<a href="/city-post/edit/?id={$city.posts[$segm['id']][$type_key]['id']}" title="Редактировать">{$segm.title}</a>
								{else}
									<a href="/catalog-type/addPost/?id={$city_id}&segment_id={$segm.id}&type={$type_key}" title="Редактировать">{$segm.title}</a>
								{/if}
							{else}
								{$segm.title}
								{if !empty($city.posts[$segm['id']][$type_key])} (+)
								{else} (-){/if}
							{/if}
							{if !last}<br />{/if}
                        {/foreach}
                    </td>
                {/foreach}
            </tr>
        {/foreach}
    {/if}
</table>

<div class="popup-window popup-change_country" title="Прикрепление страны к городу">
    <form action="/catalog-type/saveCityCountry/">
        <input type="hidden" name="city_id" />
		<table class="ribbed">
			<tr>
				<td>
					<select name="country_id" class="chosen fullwidth">
						<option value="">Выберите...</option>
						{foreach from=$countries item=country}
							<option value="{$country.id}">{$country.value}</option>
						{/foreach}
					</select>
				</td>
			</tr>
		</table>
        <div class="buttons">
			<button class="a-button-green">Сохранить</button>
		</div>
    </form>
</div>