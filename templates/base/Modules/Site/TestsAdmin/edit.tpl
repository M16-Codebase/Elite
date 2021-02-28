{?$pageTitle = 'Редактирование текста результатов теста «' . $test_entity.title . '»'}
{?$bc_location=array('url'=>'/tests-admin/viewTest/?id=' . $test_entity.id . '&tab=results', 'title' => 'Тест «' . $test_entity.title . '»')}
{include file="Modules/Posts/Pages/edit.tpl" action_rus='Редактирование текста результатов теста «' . $test_entity.title . '» от ' . $post_score_range.min_score . ' до ' . $post_score_range.max_score}
{include file="/Modules/Segment/Text/collection_links.tpl"}
{*{?$site_link= $collection_links[$post_type]}*}