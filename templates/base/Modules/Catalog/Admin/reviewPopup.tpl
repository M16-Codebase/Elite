{if !empty($review)}
    {?$marks_text = array('Очень плохо','Плохо','Неплохо','Хорошо','Отлично')}
    <div class="to-site">
        <a href="{$review.item->getUrl()}reviews/">Посмотреть на сайте</a>
    </div>
	
	<div class="review-item clearbox">
		<div class="review-info a-left">
			<div class="author">{$review.name}</div>
			<div class="date">{$review.timestamp|date_format_lang:'%d %B %Y':'ru'}</div>
			<div class="mark">
				<div class="num">{$review.mark}</div>
				<div class="rating-stars m-{$review.mark}"></div>
			</div>
			<div class="mark-text">{$marks_text[$review.mark]}</div>
		</div>
		<div class="review-content a-right">
			{if !empty($review.text_worth)}
				<div class="review-section">
					<div class="review-section-title">Достоинства</div>
					<p>{$review.text_worth|html}</p>
				</div>
			{/if}
			{if !empty($review.text_fault)}
				<div class="review-section">
					<div class="review-section-title">Недостатки</div>
					<p>{$review.text_fault|html}</p>
				</div>
			{/if}
			<div class="review-section">
				<div class="review-section-title">Отзыв</div>
				<p>{$review.text|html}</p>
			</div>
		</div>
	</div>	
	
    <div class="buttons clearbox" data-review_id="{$review.id}">
        {if $review.status != 'approved'}
            <div class="action_button a-button-blue" data-status="approved">Разместить</div>
        {/if}
        {if $review.status != 'decline'}
            <div class="action_button a-button-blue" data-status="decline">Отклонить</div>
        {/if}
    </div>
{/if}