{foreach from=$items item=$district name=district_n}
	{?$title = $delim|explode:$district.title}
   {* {if !empty($district.post) && !empty($district.post.text)}
        <li>
            <a href="{$district->getUrl($request_segment.id)}">{$district.title}</a>
        </li>
    {/if}*}
	<div class="flat-wrap">
		{if !empty($district.cover)}
			<a href="{$district->getUrl($request_segment.id)}" class='cover' style='background:url({$district.cover->getUrl(1900,950,true)}){if !empty($gravity)} {$gravity[$district.cover.gravity]}{/if}; background-size:cover;'></a>
		{/if}
		<div class='params'>
			{if !empty($title)}<div class="title">{if !empty($title[0])}<span>{$title[0]}</span>{/if} {if !empty($title[1])}{$title[1]}{/if}</div>{/if}
			{if !empty($district.post.annotation)}<div class="descr">{$district.post.annotation}</div>{/if}
			<div class="bottom"><a href="{$district->getUrl($request_segment.id)}" class="btn m-sand">{$lang->get('В деталях', 'In detail')}</a></div>
		</div>
	</div>
	{if $smarty.foreach.district_n.iteration == 3}
	<div class="bowtie-wrap row">
		<div class="w4">
			<div class="title">{$lang->get('Хотите <b>найти</b> себе<br>квартиру в желанном районе?', 'Trying <b>to find</b> yourself<br>an apartment in the desirable area?')|html}</div>
			<div class="descr">{$lang->get('Опытные специалисты в области недвижимости помогут Вам выбрать оптимальный вариант.', 'Qualified real estate professionals will help you to choose the best one.')}{*Наши специалисты свяжуться с вами, ответят на ваши вопросы, организуют просмотр квартиры*}</div>
			<a href="{$url_prefix}/selection/" class="btn m-light-magenta m-vw">{$lang->get('Персональный подбор', 'Personal selection')}</a>
		</div>
	</div>
	{/if}
{/foreach}