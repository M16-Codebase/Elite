<?php
/**
 * Description of Index
 *
 * @author pochka
 */
namespace Modules\Exchange;
use Models\CatalogManagement\Export\CommerceML as ExportCommerceML;
use Models\CatalogManagement\Export\CSV as ExportCSV;
use Models\CatalogManagement\Import\CSV as ImportCSV;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use App\Configs\CatalogConfig;
class Index extends \LPS\WebModule{
	public function index(){
        return $this->notFound();
	}
    /**
     * @ajax
     */
	public function CSVImport(){
        $this->setJsonAns()->setEmptyContent();
        $segment = \App\Segment::getInstance()->getDefault();
		$importManager = ImportCSV::factory(!empty($segment) ? $segment['id'] : NULL);
        $FILE = $this->request->files->get('file');
        $type_id = $this->request->request->get('type_id');
        if (!empty($FILE)){
            $folder = \LPS\Config::getRealDocumentRoot() . \Models\CatalogManagement\Exchange\Import\CSV::FILE_PATH . $this->segment_id . '/';
            if (!file_exists($folder)){
                \LPS\Components\FS::makeDirs($folder);
            }
            if (empty($type_id)){
                $this->getAns()->addErrorByKey('type_id', \Models\Validator::ERR_MSG_EMPTY);
                return;
            }
            $params['type_id'] = $type_id;
            $type = Type::getById($type_id);
            if (empty($type)){
                $this->getAns()->addErrorByKey('type_id', \Models\Validator::ERR_MSG_NOT_FOUND);
                return;
            }
            $params['catalog_id'] = $type->getCatalog()->getId();
            $params['type_title']  = $type['title'];
            foreach (array(
                'images',
                'enum',
                'visible',
                'empty_values'
            ) as $field){
                $params[$field] = $this->request->request->get($field);
            }
            $task_id = \Models\CronTask::add(array(
                    'type' => static::CRON_TASK,
                    'segment_id' => $this->segment_id,
                    'status' => \Models\CronTask::STATUS_NEW,
                    'file_name' => $FILE->getClientOriginalName(),
                    'time_create' => date('Y-m-d H:i:s'),
                    'data' => $params,
                    'user_id' => !empty($params['user_id']) ? $params['user_id'] : NULL
                )
            );
            $new_file_name = $folder . $task_id . '.' . self::FILE_EXT;
            move_uploaded_file($FILE->getRealPath(), $new_file_name);
            chmod($new_file_name, 0666);
            if ($encoding == 'cp1251'){
                $file_content = file_get_contents($new_file_name);
                file_put_contents($new_file_name, mb_convert_encoding($file_content, 'utf-8', 'Windows-1251'));
            }
            $importManager->copyFile($FILE, $type_id, array(
                'images' => $this->request->request->get('images'),
                'enum' => $this->request->request->get('enum'),
                'visible' => $this->request->request->get('visible')),
                    $this->request->request->get('encoding'));
            return json_encode(array('status' => 'ok'));
        }
        $this->getAns()->addErrorByKey('file', \Models\Validator::ERR_MSG_EMPTY);
	}    
    public function downloadExample(){
        $type_id = $this->request->query->get('type_id');
        if (!empty($type_id) && Type::getById($type_id)){
            $properties = PropertyFactory::search($type_id, PropertyFactory::P_EXPORT, 'key');
            $import_keys = array(ImportCSV::FIELD_NAME_VARIANT_ID, ImportCSV::FIELD_NAME_ITEM_ID, ImportCSV::FIELD_NAME_IMAGES);
            $import_titles = array('ID варианта', 'Карточка товара', 'Изображения');
            foreach ($properties as $prop){
                $import_keys[] = $prop['key'];
                $import_titles[] = $prop['title'];
            }
            $str = implode(ImportCSV::SEPARATOR_CELL, $import_keys) . ImportCSV::SEPARATOR_ROW . implode(ImportCSV::SEPARATOR_CELL, $import_titles);
            header('Content-type: application/octetstream');
            header('Content-Disposition: attachment; filename="file.csv"');
            return $str;
        }else{
            return 'Ошибка. Не указан тип.';
        }
    }
}
