{if !empty($post->getUrl($post.segment_id))}{?$site_link = $post->getUrl($post.segment_id)}{/if}
{?$bc_location = empty($bc_location)? array('url'=>'/pages/', 'title' => 'Тексты к страницам') : $bc_location}
{include file="Modules/Posts/Pages/edit.tpl" size_description="200×120"}