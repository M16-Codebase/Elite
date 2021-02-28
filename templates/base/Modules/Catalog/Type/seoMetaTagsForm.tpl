<form action="/catalog-type/saveSeoTags/">
    {if $tags_assigned}<input type="hidden" name="id" value="{$seo_tags_data.id}">{/if}
    <input type="hidden" name="action" value="{$tags_action_value}">
    <input type="hidden" name="page_uid" value="{$seo_page_uid}">
    title:
    <textarea rows="5" name="title">{if $tags_assigned}{$seo_tags_data.title}{/if}</textarea>
    description:
    <textarea rows="5" name="description">{if $tags_assigned}{$seo_tags_data.description}{/if}</textarea>
    <input type="submit" value="Сохранить">
</form>