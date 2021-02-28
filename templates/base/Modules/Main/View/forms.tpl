{?$form_creator = $infoBlocks->get('formCreator')}
{?$form_data = array(
    'title' => 'Заголовок формы',
    'action' => '/action/link/',
    'method' => 'post',
    'class' => array('super-duper-form', 'test-form_constructor'),
    'antispam' => TRUE,
    'data' => array(
        'id' => 'kuischsche',
        'form-name' => 'ololo'
    ),
    'hidden_fields' => array(
        'fld_one' => 'fld_one_value',
        'fld_two' => 'fld_two_value',
        'fld_three' => array(
            'class' => 'hidden-field-class',
            'value' => 'fld_three_value'
        )
    ),
    'fields' => array(
        'email' => array(
            'type' => 'text',
            'title' => 'Электронная почта',
            'errors' => array(
                'empty' => 'Поле email обязательно для заполнения',
                'already_exists' => 'Пользователь с такой электронной почтой уже зарегистрирован'
            )
        ),
        array(
            'type' => 'group',
            'title' => 'Пароли джважды',
            'fields' => array(
                'pass' => array(
                    'type' => 'password',
                    'title' => 'Пароль'
                ),
                'pass2' => array(
                    'type' => 'password',
                    'title' => 'Повторите пароль',
                    'description' => 'Для защиты от опечаток введите желаемый пароль повторно'
                )
            )
        ),
        'cbx' => array(
            'type' => 'checkbox',
            'title' => 'Одиночный чекбокс',
            'label' => 'Можно клацнуть',
            'value' => '1',
            'default_value' => '0'
        ),
        'cbx2' => array(
            'type' => 'checkbox',
            'title' => 'Куча чекбоксов',
            'values' => array(
                'one' => array(
                    'label' => 'Первый',
                    'value' => 'one',
                    'class' => array('first-class', 'second-class', 'third_class')
                ),
                'two' => array(
                    'label' => 'Второй',
                    'value' => 'two',
                    'data' => array(
                        'id' => '113',
                        'another_shi_' => 'test'
                    ),
                    'class' => 'uck-that'
                ),
                'three' => array(
                    'label' => 'Третий',
                    'value' => 'three'
                )
            )
        ),
        'radio' => array(
            'type' => 'radio',
            'title' => 'Радиобатоны!!!!1111',
            'values' => array(
                array(
                    'label' => 'Первый',
                    'value' => 'one'
                ),
                array(
                    'label' => 'Второй',
                    'value' => 'two',
                    'data' => array(
                        'id' => '113',
                        'another_shi_' => 'test'
                    ),
                    'class' => 'uck-that'
                ),
                array(
                    'label' => 'Третий',
                    'value' => 'three'
                )
            )
        ),
        'select' => array(
            'type' => 'select',
            'title' => 'Селект',
            'options' => array(
                '' => 'Выберите',
                1 => 'Первый',
                2 => 'Второй'
            ),
            'field_class' => 'super-select-class',
            'field_data' => array(
                'id' => 14
            )
        )
    ),
    'buttons' => array(
        'submit' => 'Отправить',
        'clear' => 'Очистить'
    )
)}
{*{$form_creator->getForm($form_data)|html}*}

{$real_estate_form|html}
{$resale_form|html}
{$owner_form|html}
{$feedback_form|html}
{$callback_form|html}

{*<form class="user-form" data-checkstring="1437469284" data-hashstring="f9f91f306944f7cfc942c95e974b497a" action="/feedback/makeRequest/" method="POST" enctype="multipart/form-data">*}
    {*<div class="form-title">Откликнуться на вакансию</div>*}

    {*<input type="hidden" name="check_string">*}
    {*<input type="hidden" name="hash_string">*}


    {*<input type="hidden" name="feedbackType" value="vacancy">*}
    {*<input type="hidden" name="vacancy" value="14">*}


    {*<div class="field">*}
        {*<div class="f-title">Ф.И.О.</div>*}
        {*<div class="f-input">*}

            {*<input type="text" name="author" value="">*}
        {*</div>*}

        {*<ul class="f-errors a-hidden"><li class="error-empty a-hidden">Заполните поле</li><li class="error-incorrect_format a-hidden">Неверный формат</li></ul></div>*}
    {*<div class="field">*}
        {*<div class="f-title">Телефон</div>*}
        {*<div class="f-input">*}

            {*<input type="text" name="phone">*}
        {*</div>*}

        {*<ul class="f-errors a-hidden"><li class="error-empty a-hidden">Заполните поле</li><li class="error-incorrect_format a-hidden">Неверный формат</li></ul></div>*}
    {*<div class="field">*}
        {*<div class="f-title">Сопроводительное письмо</div>*}
        {*<div class="f-input">*}
            {*<textarea name="message"></textarea>*}
        {*</div>*}

        {*<ul class="f-errors a-hidden"><li class="error-empty a-hidden">Заполните поле</li><li class="error-incorrect_format a-hidden">Неверный формат</li></ul></div>*}
    {*<div class="field">*}
        {*<div class="f-title">Резюме</div>*}
        {*<div class="f-input">*}

            {*<input type="file" name="summary">*}
        {*</div>*}

        {*<ul class="f-errors a-hidden"><li class="error-empty a-hidden">Заполните поле</li><li class="error-incorrect_format a-hidden">Неверный формат</li></ul></div>*}
    {*<div class="field">*}
        {*<div class="f-title">Ссылка на резюме</div>*}
        {*<div class="f-input">*}

            {*<input type="text" name="summary_link">*}
        {*</div>*}

        {*<ul class="f-errors a-hidden"><li class="error-empty a-hidden">Заполните поле</li><li class="error-incorrect_format a-hidden">Неверный формат</li></ul></div>*}

    {*<ul class="f-errors a-hidden"><li class="error-check_sum a-hidden">Вы робот!</li><li class="error-403 a-hidden">Нет доступа</li><li class="error-500 a-hidden">Ошибка сервера</li></ul><div class="buttons">*}
        {*<button class="”submit-form”">Отправить</button>*}
        {*<span class="clear-form">Очистить</span>*}
    {*</div>*}
{*</form>*}


{*<form class="user-form" data-checkstring="1437469284" data-hashstring="f9f91f306944f7cfc942c95e974b497a" action="/feedback/makeRequest/" method="POST" enctype="multipart/form-data">*}
    {*<div class="form-title">Обратная связь</div>*}

    {*<input type="hidden" name="check_string">*}
    {*<input type="hidden" name="hash_string">*}


    {*<input type="hidden" name="feedbackType" value="feedback">*}


    {*<div class="field">*}
        {*<div class="f-title">Ф.И.О.</div>*}
        {*<div class="f-input">*}

            {*<input type="text" name="author" value="">*}
        {*</div>*}

        {*<ul class="f-errors a-hidden"><li class="error-empty a-hidden">Заполните поле</li><li class="error-incorrect_format a-hidden">Неверный формат</li></ul></div>*}
    {*<div class="field">*}
        {*<div class="f-title">Электронная почта</div>*}
        {*<div class="f-input">*}

            {*<input type="text" name="email">*}
        {*</div>*}

        {*<ul class="f-errors a-hidden"><li class="error-empty a-hidden">Заполните поле</li><li class="error-incorrect_format a-hidden">Неверный формат</li></ul></div>*}
    {*<div class="field">*}
        {*<div class="f-title">Сообщение</div>*}
        {*<div class="f-input">*}
            {*<textarea name="message"></textarea>*}
        {*</div>*}

        {*<ul class="f-errors a-hidden"><li class="error-empty a-hidden">Заполните поле</li><li class="error-incorrect_format a-hidden">Неверный формат</li></ul></div>*}

    {*<ul class="f-errors a-hidden"><li class="error-check_sum a-hidden">Вы робот!</li><li class="error-403 a-hidden">Нет доступа</li><li class="error-500 a-hidden">Ошибка сервера</li></ul><div class="buttons">*}
        {*<button class="”submit-form”">Отправить</button>*}
        {*<span class="clear-form">Очистить</span>*}
    {*</div>*}
{*</form>*}
{*<form class="user-form" data-checkstring="1437469284" data-hashstring="f9f91f306944f7cfc942c95e974b497a" action="/catalog/search/" method="GET">*}
    {*<div class="form-title">Логи поиска</div>*}

    {*<input type="hidden" name="check_string">*}
    {*<input type="hidden" name="hash_string">*}

	{*<div class="field">*}
		{*<input type="text" name="search" class="search-input" placeholder="Поиск" value="">*}
		{*<ul class="f-errors a-hidden"><li class="error-empty a-hidden">Заполните поле</li><li class="error-incorrect_format a-hidden">Неверный формат</li></ul>*}
	{*</div>*}

    {*<ul class="f-errors a-hidden"><li class="error-check_sum a-hidden">Вы робот!</li><li class="error-403 a-hidden">Нет доступа</li><li class="error-500 a-hidden">Ошибка сервера</li></ul><div class="buttons">*}
        {*<button class="”submit-form”">Отправить</button>*}
        {*<span class="clear-form">Очистить</span>*}
    {*</div>*}
{*</form>*}