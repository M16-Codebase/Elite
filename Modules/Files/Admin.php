<?php
namespace Modules\Files;
use Models\FilesManagement\File;
/**
 * Description of Admin
 *
 * @author olga
 */
class Admin extends \LPS\AdminModule{
    const FILE_COVER_MAX_WIDTH = 1000;
    const FILE_COVER_MAX_HEIGHT = 1000;
    const FILE_COVER_PATH = '/data/images/files/';
	const PAGE_SIZE = 20;
	const PAGE_LOGS_SIZE = 30;
	const FILE_XLS_LOGS_PATH = '/data/files/xls_tmp/';

    public function index() {
        $this->filesList(true);
    }
    /**
     * @ajax
     */
    public function addFile() {
        $ufile = $this->request->files->get('ufile');
        if (!empty($ufile)){
            $title = $this->request->request->get('title', '');
            $file_id = File::add($title, $ufile, $error);
            if (!empty($error)){
                $this->getAns()->add('error', $error);
			}else{
				$file = File::getById($file_id);
				$known_downloader = $this->request->request->get('known_downloader');
				$title = $this->request->request->get('title');
				$params = array();
				if (isset($known_downloader)){
					$params['known_downloader'] = $known_downloader;
				}
				if (!empty($title)){
					$params['title'] = $title;
				}
				$params['type'] = File::TYPE_ANALYTICS;
				$params['segment_id'] = $this->request->request->get('segment_id', 1);
				if (!empty($params)){
					$file->edit($params);
					$file->save();
				}
				$file->move(1);
			}
        }
        return $this->run('filesList');
    }
    /**
     * @ajax
     */
    public function renameFile() {
        $id = $this->request->request->get('id');
        $file = File::getById($id);
        if (!empty($file)){
            $title = $this->request->request->get('title');
            if (!empty($title)){
                $file->edit(array('title' => $title));
				$file->save();
            }else{
                $this->getAns()->add('error', 'Название файла обязательно для заполнения');
            }
        }else{
            $this->getAns()->add('error', 'Файл не найден');
        }
        return $this->run('filesList');
    }
    /**
     * @ajax
     */
    public function reloadFile(){
        $id = $this->request->request->get('id');
        $file = File::getById($id);
        if (!empty($file)){
            $rfile = $this->request->files->get('rfile');
            if (!empty($rfile)){
                $result = $file->reload($rfile);
                if ($result !== true)
                    $this->getAns()->add('error', $result);
            }
			if (!isset($result) || $result === TRUE){
				$file->edit(array('known_downloader' => $this->request->request->get('known_downloader'), 'title' => $this->request->request->get('title')));
				$file->save();
			}
        }else{
            $this->getAns()->add('error', 'Файл не найден');
        }
        return $this->run('filesList');
    }
    /**
     * @ajax
     */
    public function delFiles() {
        $cheked = $this->request->request->get('check');
        if (!empty($cheked)){
            $result = File::del(array_keys($cheked));
            if ($result !== true)
                $this->getAns()->add('error', $result);
        }
        return $this->run('filesList');
    }

    public function filesList($inner=false) {
        if(!$inner){
            $this->setAjaxResponse();
        }
		$page = $this->request->query->get('page', $this->request->request->get('page', 1));
		$files = File::search(
			array(
				'segment_id' => $this->request->query->get('s', $this->request->request->get('segment_id')),
				'type' => File::TYPE_ANALYTICS,
				'start' => ($page-1)*self::PAGE_SIZE, 
				'limit' => self::PAGE_SIZE)
			, $count);
        $this->getAns()->add('files', $files)
			->add('files_count', $count)
			->add('pageSize', self::PAGE_SIZE)
			->add('pageNum', $page);
    }

    /**
     * @ajax
     */
    public function addCover(){
        $file_id = $this->request->request->get('file_id');
        $cover = $this->request->files->get('cover');
        $file = File::getById($file_id);
        if (!empty($file)){
            $result = $file->uploadCover($cover, $error);
			$file->save();
            if (!is_null($result)){
                $this->getAns()->add('file_cover', $result);
            }
            if (!empty($error)){
                $this->getAns()->add('error', $error);
            }
        }
        return $this->run('filesList');
    }
    /**
     * @ajax
     * @return string
     */
    public function showIn(){
        $file_id = $this->request->request->get('file_id');
		$file = File::getById($file_id);
        if (!empty($file)){
            $show_in = $this->request->request->get('show_in');
            $file->setShowIn($show_in);
        }else{
            return 'Файл не найден';
        }
        return '';
    }
	
	public function security(){
		$file_id = $this->request->request->get('file_id');
		$file = File::getById($file_id);
        if (!empty($file)){
            $security = $this->request->request->get('value');
            $file->setKnownDownloader($security);
        }else{
            return 'Файл не найден';
        }
        return '';
	}
	
	/**
     * @ajax
     */
    public function editFields(){
        $this->setAjaxResponse();
        $id = $this->request->request->get('id');
        $file = File::getById($id);
        $this->getAns()->add('file', $file)
            ->add('types', array())
			->setFormData($file->asArray());
    }
	
	public function changePosition(){
        $file_id = $this->request->request->get('file_id');
        $file = File::getById($file_id);
        if (!empty($file)) {
            $position = $this->request->request->get('position', 0);
            $file->move($position);
			$file->save();
        }else{
            $this->getAns()->add('image_action_status', 'empty image');
        }
        return $this->run('filesList');
    }
	
	public function logs(&$logs = NULL){
		$params = $this->request->query->all();
		if (isset($params['subscribe']) && $params['subscribe'] == ""){
			unset($params['subscribe']);
		}
		if (!empty($params['time']['min']) && !empty($params['date_min'])){
			$params['date_min'] = $params['date_min'] . ' ' . $params['time']['min'] . ':00:00';
		}
		if (!empty($params['time']['max']) && !empty($params['date_max'])){
			$params['date_max'] = $params['date_max'] . ' ' . $params['time']['max'] . ':00:00';
		}
		if (empty($params['time']['min']) && empty($params['time']['max'])){
			if (!empty($params['date_max'])){
				$params['date_max'] = $params['date_max'] . ' ' . '23:59:59';
			}
		}
		if (empty($params['order'])){
			$params['order']['date'] = 1;
		}
		$page = $this->request->query->get('page', 1);
		$start = ($page-1)*self::PAGE_LOGS_SIZE;
		$limit = self::PAGE_LOGS_SIZE;
		$logs = File::getLog($params, $count, $start, $limit);
		$this->getAns()->add('pageNum', $page)
			->add('pageSize', self::PAGE_SIZE)
			->add('count', $count)
			->add('logs', $logs)
			->add('date_sort_order', !empty($params['order']['date']) ? 0 : 1);
	}
	
	public function getXLS(){
		$this->logs($logs);
		$tmp_file_name = time() . '.xls';
		$data = array(
			'Электронный адрес',
			'Название файла',
			'Дата',
			'Подписка'
		);
		$user = $this->account->getUser();
		$staff = $user->getStaff();
		// Create new PHPExcel object
		$objPHPExcel = new \PHPExcel();
		// Set properties
		$objPHPExcel->getProperties()->setCreator(!empty($staff) ? ($staff['surname'] . ' ' . $staff['name']) : $user['email']);
		$objPHPExcel->getProperties()->setLastModifiedBy(!empty($staff) ? ($staff['surname'] . ' ' . $staff['name']) : $user['email']);
		$objPHPExcel->getProperties()->setTitle("Список пользователей");
		$objPHPExcel->getProperties()->setSubject("Список пользователей");
		$objPHPExcel->getProperties()->setDescription("Список пользователей");
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0);
		$pColumn = 0;
		foreach ($data as $field_name){
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($pColumn, 1, $field_name);
			$pColumn++;
		}
		$logs_data = array();
		foreach ($logs as $l){
			$logs_data[] = array(
				$l['email'],
				$l['file_name'],
				$l['date'],
				$l['subscribe'] ? 'Да' : 'Нет'
			);
		}
		$objPHPExcel->getActiveSheet()->fromArray($logs_data, NULL, 'A2');
		$this->finalizeData($objPHPExcel, $tmp_file_name);
		\Models\FilesManagement\Download::existsFile(\LPS\Config::getRealDocumentRoot() . self::FILE_XLS_LOGS_PATH . $tmp_file_name, 'Список скачавших аналитику' . $tmp_file_name, TRUE);
	}
	
	private function finalizeData($data, $file_name){
		$objWriter = new \PHPExcel_Writer_Excel2007($data);
		$full_path = \LPS\Config::getRealDocumentRoot() . self::FILE_XLS_LOGS_PATH;
		if (!file_exists($full_path)){
			\LPS\Components\FS::makeDirs($full_path);
		}
		$objWriter->save($full_path . $file_name);
	}
}
?>
