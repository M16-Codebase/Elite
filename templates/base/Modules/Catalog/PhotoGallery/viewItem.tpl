<h1>{$gallery.title}</h1>

{?$images = $gallery.images->getImages()}

<ul>
    {foreach from=$images item=i}
        <li><img src="{$i->getUrl(100,100)}" /></li>
    {/foreach}
</ul>