{if !isset($types_rus_include)}
{?$types_rus = array(
    'pages' => array('региональная статья', 'региональной статьи', 'региональных статей'),
    'texts' => array('текст к странице', 'текста к странице', 'текстов к страницам'),
    'property_value' => array('статья', 'статьи', 'статей'),
    'types' => array('статья категории', 'статьи категории', 'статей категорий'),
    'news' => array('новость', 'новости', 'новостей'),
    'article' => array('статья', 'статьи', 'статей'),
    'blog' => array('блог', 'блога', 'блогов')
)}
{?$types_rus_include = 1}
{/if}