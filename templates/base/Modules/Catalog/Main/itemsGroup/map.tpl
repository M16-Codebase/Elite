<div class="cluser-map{if $type_apartments || $type_investment} apt-map{/if}">
	<div class="map"></div>
	<div class="map-info a-hidden" data-count="{$count}" data-page-size="{$pageSize}">
		{include file="Modules/Catalog/Main/itemsForMap.tpl"}
	</div>
</div>