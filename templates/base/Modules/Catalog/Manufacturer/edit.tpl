{include file="Modules/Posts/Pages/edit.tpl" 
    action_rus=('Добавить статью' . (!empty($manuf) ? (' к производителю ' . $manuf.title) : ''))
    site_url = ('/catalog/brand/' . $manuf.key . '/')
    bc_location = array('url'=>'/manuf/', 'title' => 'Производители')
    delete_params = array('data' => array('id' => $post.id, 'redirect' => '/manuf/'))
}