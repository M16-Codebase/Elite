{?$form_creator = $infoBlocks->get('formCreator')}
{?$fields = array(
'title' => 'Заголовок формы',
'fields' => $user_form_fields
)}
{$form_creator->getForm($fields)|html}