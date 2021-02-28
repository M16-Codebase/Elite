{?$admin_page = 1}
<h1>Подписчики</h1>
{if !empty($subscribers)}
    <table class="ribbed">
    {foreach from=$subscribers item=sub}
        <tr>
            <td>{$sub.email}</td>
            <td>{$sub.name}</td>
        </tr>
    {/foreach}
    </table>
{/if}