<?php
/**
 * Description of CSV
 *
 * @author pochka
 */
namespace Models\CatalogManagement\Exchange\Import;

use Models\CatalogManagement\Type;
use LPS\Components\CsvData;
use Models\CronTasks\Task;

class CSV extends ImportCatalogEntities{
	const FILE_EXT = 'csv';
    const SEPARATOR_SET_VALUES = '|';
	
	const STACK_SIZE = 100;
    /**
     * Создание задачи на основе параметров и/или файла
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     */
    public static function createTask($params = array(), $FILE = NULL, &$error = NULL){
        if (!empty($params['type_id'])){
            $type = Type::getById($params['type_id']);
            $params['type_title']  = $type['title'];
        }
        $file_extension = $FILE instanceof UploadedFile ? $FILE->getClientOriginalExtension() : pathinfo($FILE)['extension'];
        if ($file_extension != static::FILE_EXT){
            throw new \Exception('Передано неверное расширение файла в задачу импорта csv: ' . $file_extension);
        }
        return Task::add($params, $FILE);
    }
    /**
     * Основной метод, который запускает импорт
     * @throws \Exception
     */
    public function getData(){
        if (empty($this->catalog)){
            $this->task->setCancel(array('errors' => 'Неверно задан ключ каталога'));
            return;
        }
        $catalogPositionsClass = \App\Configs\CatalogConfig::getEntityClass($this->catalog['key'], \Models\CatalogManagement\Item::CATALOG_IDENTITY_KEY);
        if (empty($catalogPositionsClass) || !class_exists($catalogPositionsClass)){
            $this->task->setCancel(array('errors' => 'Неверно задан ключ каталога'));
            return;
        }
        if (isset($this->task['percent']) && $this->task['percent'] == 100){
            $this->task->setComplete();
            return;
        }
        if ($this->task['status'] != Task::STATUS_PROCESS){
            $this->task->setStart();
        }
        $file = $this->task->getFile('absolute');
        if (!file_exists($file)){
            $this->task->setCancel(array('errors' => 'Файл не найден'));
            return;
        }
        $line_count = \LPS\Components\FS::getFileLineCount($file);
		if (!is_readable($file)){
            $this->task->setCancel(array('errors' => 'На файл стоит защита от чтения'));
			return;
		}
        $handle = fopen($file, "r");
        $separator = \LPS\Config::CSV_SEPARATOR_CELL;
        $properties_keys = CsvData::fileGet($handle, $separator);//первая строка - ключи свойств
        if (count($properties_keys) < 2){
            rewind($handle);
            $separator = \LPS\Config::CSV_SEPARATOR_CELL_ALTERNATE;
            $properties_keys = CsvData::fileGet($handle, $separator);
        }
        if (count($properties_keys) < 2){
            rewind($handle);
            $separator = \LPS\Config::CSV_SEPARATOR_CELL_ALTERNATE_ELSE;
            $properties_keys = CsvData::fileGet($handle, $separator);
        }
        if (count($properties_keys) < 2){
            $this->task->setCancel(array('errors' => 'Не найден разделитель полей в файле'));
        }
        $empty_cols = array();
        foreach ($properties_keys as $col_num => &$prop_key){
            //защита от пустых столбцов
            $prop_key = preg_replace('/[^0-9a-zA-Z_\-]/u', '', $prop_key);
            if (empty($prop_key)){
                $empty_cols[$col_num] = $col_num;
                unset($properties_keys[$col_num]);
                continue;
            }
        }
        if (count($properties_keys) < 2){
            $this->task->setCancel(array('errors' => 'Не удается прочитать файл, возможно он пустой'));
			return;
		}
        $row_num = 2;
        $file_row = TRUE;
        //переставляем указатель файла на нужную позицию не считывая данные
		if ($this->task['status'] == Task::STATUS_PROCESS && !empty($this->task['percent']) && $this->task['percent'] < 100){
			$percent = $this->task['percent'];
            $current_percent = 0;
            while ($current_percent < $percent){
                $div_row_num = $row_num%self::STACK_SIZE;
                if (empty($div_row_num)){
                    $current_percent = round(100*$row_num/$line_count, 2);
                    if ($current_percent >= $percent){
                        break;
                    }
                }
                $file_row = fgets($handle);
                $row_num++;
            }
		}
        $data = array();
        while(!feof($handle)){//пробегаем по всем строкам
			$div_row_num = $row_num%self::STACK_SIZE;
            if (empty($div_row_num) && !empty($data)){
                $this->dataProcessing($data);
                $data = array();
                if (!$this->task->iterationComplete(round(100*$row_num/$line_count, 2))){
                    return;
                }
            }
            $file_row = CsvData::fileGet($handle, $separator);
            if ($file_row === FALSE){
                break;
            }
            $row_num++;
            if (empty($file_row)){
                continue;
            }
            if (!empty($empty_cols)){
                foreach ($empty_cols as $col_num){
                    unset($file_row[$col_num]);
                }
            }
            //совмещаем первую строку (с ключами) со значениями. получаем массив array('ключ' => 'значение')
			if (count($properties_keys) < count($file_row)){
                $this->task->addError($row_num, 'Количество столбцов должно быть одинаковым в каждой строке');
				continue;
			}elseif(count($properties_keys) > count($file_row)){
				$file_row = array_pad($file_row, count($properties_keys), '');
			}
            $data[$row_num] = array_combine($properties_keys, $file_row);
            foreach ($data[$row_num] as $p_k => &$p_v){
                $p_v = trim($p_v);
            }
            $data[$row_num]['num'] = $row_num-1;
            if (!empty($this->task['data']['type_id'])){
                $data[$row_num][static::FIELD_NAME_TYPE_ID] = $this->task['data']['type_id']; 
            }
        }
        //обрабатываем остатки
        if (!empty($data)){
            $this->dataProcessing($data);
        }
        fclose($handle);
        $this->task->setComplete(array('data' => array('items_count' => $this->items_count, 'variants_count' => $this->variants_count) + $this->task['data']));
    }
}
