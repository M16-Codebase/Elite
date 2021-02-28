<div class="popup-window popup-create-type">
    <form action="/catalog-type/{if !empty($createCatalog)}createCatalog{else}create{/if}/">
		<input type="hidden" name="parent_id" value="{$current_type.id}" />
        <div class="inner">
            {include file="Modules/Catalog/Type/editPopup.tpl"}
        </div>
        <div class="buttons clearbox">
            <div class="submit a-button-green">Создать</div>
        </div>
    </form>
</div>