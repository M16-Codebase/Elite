{if !empty($attached_items)}
	{foreach from=$attached_items item=item}
            {if !empty($item)}
		<div class="uploaded-item uploaded-image" data-id="{$item.id}">
			<div class="gallery-image">
				<div class="uploaded-img-options">
					<div class="option paste-button paste-left" title="Вставить слева"></div>
					<div class="option paste-button paste-right" title="Вставить справа"></div>
					<div class="option delete" title="Удалить из списка"></div>
				</div>
				{if !empty($item.gallery)}
					{?$gallery = $item.gallery}
					{?$cover = $gallery->getCover()}
					{if empty($cover)}
						{?$cover = $gallery->getDefault()}
					{/if}
					{if !empty($cover)}
						<div class="cover-cont">
							<img src="{$cover->getUrl(116, 106, true)}" alt="{if !empty($item.title)}{$item.title}{else}No title{/if}" class="cover" />
						</div>
					{/if}
				{/if}
			</div>
			<div class="item-title"><a href="{$item->getUrl()}" target="_blank">{$item.title}</a></div>
		</div>
            {/if}
	{/foreach}
{/if}
<div class="add-to-gallery add-new-item">
	Добавить<br>товар
</div>