<div class="popup-window files-popup popup-add-file-cover">
    <form id="upload_file_cover" action="/files-admin/addCover/">
        <input type="hidden" name="file_id" value="">
		<div class="clearbox">
			<div class="cover-image">
				<div class="cover a-hidden">
					<div class="delete-cover"></div>
					<img src="" alt="cover" />
				</div>
				<span class="no-cover">Обложка не загружена</span>
			</div>
			<div class="cover-upload">
				<input type="file" name="cover" />
			</div>	
		</div>

		<div class="buttons">
			<div class="submit a-button-green">Загрузить</div>
		</div>
    </form>
</div>