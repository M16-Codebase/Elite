{?$pageTitle = 'Вопрос-ответ — Жилой комплекс Гранвиль'}
{?$admin_page = 1}
{?$site_link = '/questions/'}
<h1>Вопрос — ответ</h1>
{include file="Admin/components/actions_panel.tpl"
	buttons = array(
		'save' => '#',
		'|',
		'add' => '/questions-admin/addQuestion/'
	)}

<form class="edit-questions-form" action="/questions-admin/saveQuestions/">
	<ul class="question-list">
		{include file="Modules/Site/QuestionsAdmin/questionList.tpl"}
	</ul>
</form>
        
<div class="popup-window popup-add-question">
    <form action="/questions-admin/addQuestion/">
        <table class="ribbed">
            <tr>
                <td>
                    <input type="text" name="question" />
                </td>
            </tr>
        </table>
        <div class="buttons">
            <div class="submit a-button-blue">Создать</div>
        </div>
    </form>
</div>