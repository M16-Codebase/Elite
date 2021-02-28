<div class="popup-window popup-edit-type">
    <form action="/catalog-type/{if !empty($editCatalog)}updateCatalog{else}update{/if}/" id="update_type_title">
        <input type="hidden" name="id" value="" />
        <input type="hidden" name="parent_id" value="{$current_type.id}" />
        <div class="inner">
             {include file="Modules/Catalog/Type/editPopup.tpl"}
        </div>
        <div class="buttons clearbox">
            <div class="submit a-button-green">Сохранить</div>
        </div>
    </form>
</div>