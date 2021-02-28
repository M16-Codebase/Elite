<div class="popup-window popup-create-post">
    <form action="/{$moduleUrl}/create/">
        <table class="ribbed">
            <tr>
                <td>
                    <input type="text" name="title" />
                </td>
                {if (!empty($current_theme_id))}
                    <input type="hidden" name="theme_id" value="{$current_theme_id}">
                {/if}
                {if !empty($smarty.get.s)}
                    <input type="hidden" name="s" value="{$smarty.get.s}">
                {/if}
            </tr>
        </table>
        <div class="buttons">
            <div class="submit a-button-green">Создать</div>
        </div>
    </form>
</div>