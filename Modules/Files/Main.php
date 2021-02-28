<?php
/**
 * Description of Main
 *
 * @author olga
 */
namespace Modules\Files;
use Models\FilesManagement\File;
class Main extends \LPS\WebModule{
    public function index(){
		return $this->notFound();
	}
	/**
	 * Поддержка докачки
	 */
	public function download() {
		$this->isMyFileExists(TRUE, $error, $file);
		if (!empty($error)){
			$this->getAns()->add('error', $error);
		}else{
			$this->downloadFile($file->getUrl('absolute'), $file['full_name']);
			exit;
		}
	}
	
	protected function isMyFileExists($inner = FALSE, &$error = NULL, &$file = NULL){
		$filename = $this->request->query->get('file', $this->request->query->get('file_id'));
        if (empty($filename)){
            $error = 'Файл не задан';
			if (!$inner){
				$this->getAns()->addErrorByKey('file', $error);
			}
			return;
		}
		$file = File::getByName($filename, $error);
		if (empty($file)){
			$error = 'Файл не найден';
			if (!$inner){
				$this->getAns()->addErrorByKey('file', $error);
			}
			return;
		}
		return TRUE;
	}
    /**
     * кусочная азгрузка файла
     */
    public function upload(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $result = $this->uploadFile($filename);
        $ans->setStatus($result)
        ->addData('file_path', $filename);
    }
}
?>