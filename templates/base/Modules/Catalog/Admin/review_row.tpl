<tr id="review-{$review.id}" class="{if $review.status=='new'}new-rev{elseif $review.status=='decline'}dec-rev{elseif $review.status=='approved'}apr-rev{/if}" data-review_id="{$review.id}">
    <td class="td-title">
		<a href="{$review.item->getUrl()}" class="title">{!empty($review.item.title) ? $review.item.title : 'No title'}</a>
	</td>
    <td>
		<strong>{!empty($review.name) ? $review.name : 'Аноним'}</strong>
	</td>
    <td>
		<div class="date">
			{$review.timestamp|date_format:'%d.%m.%Y'}<br />
			{$review.timestamp|date_format:'%H:%M:%S'}
		</div>
	</td>
    <td>
		{if $review.status=='new'}Новый{elseif $review.status=='decline'}Отклонён{elseif $review.status=='approved'}Размещён{/if}
	</td>
    <td>
		<div class="td-edit edit_rev a-link">Ред.</div>
	</td>
</tr>