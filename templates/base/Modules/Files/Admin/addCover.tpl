{if !empty($cover_status)}
    <div class="status">
    {if $cover_status=='new'}Изображение загруженно{/if}
    {if $cover_status=='delete'}Изображение удалено{/if}
    </div>
{/if}
{if !empty($file_cover)}
    <a href="{$file_cover['url']}" rel="gallery">
        <img src="{$file_cover->getUrl(130, 155, 90)}" />
    </a>&nbsp;
{/if}