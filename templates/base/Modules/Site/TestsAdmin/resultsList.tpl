{?$score_from = $test_entity.min_score}
{?$score_to = $test_entity.max_score}

{*<ul>
    {foreach from=$test_entity.results item=res}
        <li>От {$score_from} до {$res.max_score} - <a href="/tests-admin/edit/?id={$res.post_id}">Редактировать текст</a></li>
        {?$score_from = $res.max_score}
    {/foreach}
    <li>От {$score_from} до {$score_to} - <a href="/tests-admin/edit/?id={$test_entity.post_id}">Редактировать текст</a></li>
</ul>*}
	{?$last_score = $score_from}
	{if !empty($test_entity)}
	{foreach from=$test_entity.results item=res}
		<div class="wblock white-block-row" data-id="{$res.id}">
			<div class="w10">От {$last_score} до {$res.max_score}</div>
			<a href="/tests-admin/edit/?id={$res.post_id}" class="w1 action-button action-edit" title="Редактировать"><i class="icon-edit"></i></a>
			<div class="w1 action-button action-delete m-border" title="Удалить"><i class="icon-delete"></i></div>
		</div>
		{?$last_score = $res.max_score}
	{/foreach}
	{/if}
		<div class="wblock white-block-row">
			<div class="w10">От {$last_score} до {$score_to}</div>
			<a href="/tests-admin/edit/?id={$test_entity.post_id}" class="w1 action-button action-edit" title="Редактировать"><i class="icon-edit"></i></a>
			<div class="w1"></div>
		</div>

{*{include file='Modules/Site/TestsAdmin/resultFields.tpl'}*}