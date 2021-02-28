<?php
/**
 * Экспорт данных в Excel с использованием шаблона
 * User: Charles Manson
 * Date: 16.09.14
 * Time: 17:01
 */

namespace Models;


class ExcelWriter {
    const IMAGES_TMP_DIR = '/data/excel_writer_tmp';

    const MAX_LIST_NAME = 31;
    const DATA_BLOCKS_BREAKER = '<br>';
    private $placeholders = array();
    private $templateColumnsWidth = array();
    private $excel_obj = NULL;
    private $object_key = NULL;
    private $templateSheets = array();
    private $activeTemplate = NULL;
    private $activeTemplateIndex = 1;
    private $countTemplateSheets = NULL;
    private $prevTemplate = NULL;
    private static $registry = array();
    private $sheet_next_line = array();

    private $tmp_images = array();

    const START_COL = 'start_col';
    const END_COL = 'end_col';
    const START_ROW = 'start_row';
    const START_ROW_OFFSET = 'start_row_offset';
    const END_ROW = 'end_row';
    const END_ROW_OFFSET = 'end_row_offset';
    const XF_INDEX = 'xfIndex';
    const FIELDS_XF_INDEXES = 'fields_xfIndexes';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const FIELD_VALUES = 'field_values';
    const PLACEHOLDER_VARIABLES = 'placeholder_variables';
    const PLACEHOLDER_LINKS = 'placeholder_links';
    const PLACEHOLDER_IMAGES = 'placeholder_images';
    const MERGE_RANGES = 'merge_ranges';
    const IMAGE = 'image';
    const IMAGES_LIST = 'images_list';

    private static $symbol_shift = array();

    /**
     *
     * @param string $template шаблон
     * @param array $data дополнительные данные
     * @return ExcelWriter
     */
    public static function factory($template = NULL, $data = NULL){
        $object_key = $template . serialize($data);
        if (!isset(self::$registry[$object_key])){
            $data['object_key'] = $object_key;
            self::$registry[$object_key] = new ExcelWriter($template, $data);
        }
        return self::$registry[$object_key];
    }

    private function __construct($template = NULL, $data = NULL) {
        if (empty($symbol_shift)){
            $this->makeSymbolShiftArray();
        }
        $this->object_key = $data['object_key'];
        $this->excel_obj = \PHPExcel_IOFactory::load($template);
        $this->excel_obj->setActiveSheetIndex();
        $this->countTemplateSheets = $this->excel_obj->getSheetCount();
        if (!empty($template)){
            for ($i = 1; $i <= $this->countTemplateSheets; $i++) {
                $this->templateSheets[$i] = $this->loadTemplate($i);//загружаем шаблон
                if ($i != $this->countTemplateSheets){
                    $this->excel_obj->setActiveSheetIndex($i);//т.к. листы с нуля, а это порядковый номер
                }
            }
        }
        $this->selectTemplate(!empty($data['template_index']) ? $data['template_index'] : 1);
        if (!empty($data)){
            $this->addSheet(!empty($data['list_name']) ? $data['list_name'] : NULL, !empty($data['list_header']) ? $data['list_header'] : NULL);//запомнили все шаблоны и создали следующий лист, где будем из этих шаблонов всё писать
        }
    }

    private function makeSymbolShiftArray(){
        $prev_char = NULL;
        foreach(range('A', 'Z') as $char){
            self::$symbol_shift[$char] = $prev_char;
            $prev_char = $char;
        }
    }

    /**
     * @return \PHPExcel_Worksheet
     * @throws \Exception
     */
    private function loadTemplate($iteration = 1){
        $current_block_index = NULL;               // Индекс блока данных, по нему будем обращаться
        $current_block_data = NULL;
        $sheet = $this->excel_obj->getActiveSheet();
        $merged_fields = $this->mergedFields($sheet);
        $images = $this->sheetImages($sheet);
        $links = $this->hyperLinks($sheet);
        foreach($sheet->getRowIterator() as $row){
            /* @var $row \PHPExcel_Worksheet_Row */
            $row_index = $row->getRowIndex();
            $new_block_index = $sheet->getCell('A' . $row_index)->getValue(); // Метка блока данных, объявленный в текущей строке
//            $is_array_block = substr($new_block_index, -2) == '[]';
//            if ($is_array_block){
//                $new_block_index = substr($new_block_index, 0, -2);
//            }
            if (empty($current_block_index) && empty($new_block_index)){
                // нет блока данных в обработке, новый блок данных также не объявлен
                continue;
            } elseif (!empty($new_block_index) && $new_block_index != $current_block_index){
                // новый блок
                if (!empty($current_block_index)){
                    // сохраняем старый если есть
                    $this->pushPlaceholderBlock($sheet, $iteration, $current_block_index, $current_block_data, $merged_fields, $images);
                    $current_block_data = NULL;
                    $current_block_index = NULL;
                }
                if ($new_block_index == self::DATA_BLOCKS_BREAKER){
                    continue;
                } else{
                    if (isset($this->placeholders[$new_block_index])){
                        throw new \Exception('Duplicate index "' . $new_block_index . '" in template');
                    }
                    $current_block_index = $new_block_index;
                    $current_block_data = array(
                        self::START_ROW => $row->getRowIndex(),
                        self::END_ROW => $row->getRowIndex(),
                        self::START_COL => 'B',
                        self::END_COL => 'B',
                        self::PLACEHOLDER_VARIABLES => array(),
                        self::PLACEHOLDER_LINKS => array(),
                        self::PLACEHOLDER_IMAGES => array()
                    );
                }
            } else {
                $current_block_data[self::END_ROW] = $row_index;
            }
            if (!empty($current_block_data)){
                $row_dimension = $sheet->getRowDimension($row_index);
                $current_block_data['rows_params'][$row_index] = array(
                    self::HEIGHT => $row_dimension->getRowHeight(),
                    self::XF_INDEX => $row_dimension->getXfIndex()
                );
            }
            $cell_iterator = $row->getCellIterator();
            foreach($cell_iterator as $cell){
                /* @var $cell \PHPExcel_Cell */
                $col_index = $cell->getColumn();
                // Первый столбец для меток, не просматриваем его
                if ($col_index == 'A') {
                    continue;
                }
                // Сохраняем ширину столбцов
                $result_col = self::$symbol_shift[$col_index]; // на создаваемых листах шаблоны смещаются на столбец влево
                if (empty($this->templateColumnsWidth[$iteration][$result_col])){
                    $src_dimension = $sheet->getColumnDimension($col_index);
                    $this->templateColumnsWidth[$iteration][$result_col] = array(
                        self::WIDTH => $src_dimension->getWidth(),
                        self::XF_INDEX => $src_dimension->getXfIndex()
                    );
                }
                if (preg_match_all('~\{([^}]+)\}~', $cell->getFormattedValue(), $matches)){
                    foreach($matches[1] as $var_name){
                        // Переменные указываются меткой {var_name}, ссылки {link|var_name}, изображения - {img|var_name}
                        $var_parts = explode('|', $var_name);
                        if (count($var_parts) == 1){
                            $current_block_data[self::PLACEHOLDER_VARIABLES][$var_name] = '{' . $var_name . '}';
                        } elseif (count($var_parts) == 2){
                            $row_offset = $row_index - $current_block_data[self::START_ROW];
                            if ($var_parts[0] == 'link'){
                                $current_block_data[self::PLACEHOLDER_LINKS][self::$symbol_shift[$col_index]][$row_offset] = $var_parts[1];
                            } elseif ($var_parts[0] == 'img'){
                                $current_block_data[self::PLACEHOLDER_IMAGES][self::$symbol_shift[$col_index]][$row_offset] = $var_parts[1];
                            }
                        }
                    }
                }
                if ($col_index > $current_block_data[self::END_COL]){
                    $current_block_data[self::END_COL] = $col_index;
                }
            }
        }
        if (!empty($current_block_index)){
            $this->pushPlaceholderBlock($sheet, $iteration, $current_block_index, $current_block_data, $merged_fields, $images);
        }
        return $sheet;
    }

    /**
     * Парсит диапазоны смерженных ячеек
     * @param \PHPExcel_Worksheet $worksheet
     * @return array Диапазоны объединения
     */
    private function mergedFields($worksheet){
        $merged_fields = $worksheet->getMergeCells();
        $result = array();
        foreach($merged_fields as $merge_range){
            if (preg_match('~^([^\d]+)(\d+):([^\d]+)(\d+)$~', $merge_range, $matches)){
                $result[] = array(
                    self::START_COL => $matches[1],
                    self::START_ROW => $matches[2],
                    self::END_COL => $matches[3],
                    self::END_ROW => $matches[4]
                );
            }
        }
        return $result;
    }

    /**
     * @param \PHPExcel_Worksheet $worksheet
     * @return array
     */
    private function sheetImages($worksheet){
        $images = $worksheet->getDrawingCollection();
        $result = array();
        $x_offset = $worksheet->getColumnDimension('A')->getWidth();
        foreach($images as $image){
            if (preg_match('~([^\d]+)(\d+)~', $image->getCoordinates(), $matches)){
                $result[] = array(
                    self::IMAGE => clone $image,
                    self::START_COL => self::$symbol_shift[$matches[1]],
                    self::START_ROW => $matches[2]
                );
            }
        }
        return $result;
    }

    /**
     * @param \PHPExcel_Worksheet $worksheet
     * @return array
     */
    private function hyperLinks($worksheet){
        $result = array();
        foreach($worksheet->getHyperlinkCollection() as $link){
        }

        return $result;
    }

    /**
     * Парсит и сохраняет блок данных шаблона
     * @param \PHPExcel_Worksheet $templateSheet
     * @param int $templateIndex
     * @param string $placeholder_key
     * @param array $placeholder_data
     * @param array $merged_fields
     * @param array $images
     */
    private function pushPlaceholderBlock($templateSheet, $templateIndex, $placeholder_key, $placeholder_data, &$merged_fields, &$images){
        $range = $placeholder_data[self::START_COL] . $placeholder_data[self::START_ROW] . ':' . $placeholder_data[self::END_COL] . $placeholder_data[self::END_ROW];
        $placeholder_data[self::FIELD_VALUES] = $templateSheet->rangeToArray($range);
        foreach(range($placeholder_data[self::START_COL], $placeholder_data[self::END_COL]) as $src_col){
            for($src_row = $placeholder_data[self::START_ROW]; $src_row <= $placeholder_data[self::END_ROW]; $src_row ++){
                $placeholder_data[self::FIELDS_XF_INDEXES][$src_col][$src_row] = $templateSheet->getCell($src_col.$src_row)->getXfIndex();
            }
        }
        // Сохраняем параметры объединения ячеек
        $placeholder_data[self::MERGE_RANGES] = array();
        foreach($merged_fields as $i => $merge_range){
            if($merge_range[self::START_ROW] >= $placeholder_data[self::START_ROW] && $merge_range[self::END_ROW] <= $placeholder_data[self::END_ROW]){
                $placeholder_data[self::MERGE_RANGES][] = array(
                    self::START_COL => self::$symbol_shift[$merge_range[self::START_COL]],
                    self::START_ROW_OFFSET => $merge_range[self::START_ROW] - $placeholder_data[self::START_ROW],
                    self::END_COL => self::$symbol_shift[$merge_range[self::END_COL]],
                    self::END_ROW_OFFSET => $merge_range[self::END_ROW] - $placeholder_data[self::START_ROW]
                );
                unset($merged_fields[$i]);
            }
        }
        // И картинки
        $placeholder_data[self::IMAGES_LIST] = array();
        foreach($images as $i => $image_data){
            if ($image_data[self::START_ROW] >= $placeholder_data[self::START_ROW] && $image_data[self::START_ROW] <= $placeholder_data[self::END_ROW]){
                $image_data[self::START_ROW_OFFSET] = $image_data[self::START_ROW] - $placeholder_data[self::START_ROW];
                unset($image_data[self::START_ROW]);
                $placeholder_data[self::IMAGES_LIST][] = $image_data;
                unset($images[$i]);
            }
        }
        $this->placeholders[$templateIndex][$placeholder_key] = $placeholder_data;
    }

    /**
     * Выбор шаблона
     * @param int $template_index
     * @return $this
     * @throws \Exception
     */
    public function selectTemplate($template_index){
        if (empty($this->templateSheets[$template_index])){
            throw new \Exception('Template #' . $template_index . ' not found');
        }
        $this->activeTemplateIndex = $template_index;
        return $this;
    }

    /**
     * Добавляем лист
     * @param string $name Название листа
     * @param array $header Параметры хедера (key, fixed и data)
     * @param bool $first в начало ли пихать лист
     * @return \Models\ExcelWriter
     */
    public function addSheet($name, $header = NULL, $first = FALSE){
        /* @var $new_sheet \PHPExcel_Worksheet */
        if (!empty($name)){
            $name = mb_substr($name, 0, self::MAX_LIST_NAME);
        }
        $addIndex = NULL;
        if ($first){
            $addIndex = $this->countTemplateSheets;
        }
        $new_sheet = $this->excel_obj->addSheet(new \PHPExcel_Worksheet(), $addIndex);
        // Показывать кнопку свертывания блоков сверху
        $new_sheet->setShowSummaryBelow(false);
        if (!empty($name)){
            $new_sheet->setTitle($name);
        }
        if (!empty($this->templateColumnsWidth[$this->activeTemplateIndex])){
            foreach($this->templateColumnsWidth[$this->activeTemplateIndex] as $col_index => $col_data){
                $new_sheet->getColumnDimension($col_index)->setWidth($col_data[self::WIDTH])->setXfIndex($col_data[self::XF_INDEX]);
            }
        }
        $new_index = $this->excel_obj->getIndex($new_sheet);
        $this->excel_obj->setActiveSheetIndex($new_index);//переключаемся на новый лист
//        $this->rowUpPlaceholders();
        if (!empty($header)){
            $this->writeBlock($header['key'], !empty($header['data']) ? $header['data'] : array());
            if (!empty($header['fixed'])){
                // замораживаем хедер
                $new_sheet->freezePane('A' . ($this->sheet_next_line[$new_index]));
            }
        }
        return $this;
    }

    /**
     * @param string $key
     * @param array $blocks_data
     * @param int $collaplseLevel
     * @return $this
     * @throws \Exception
     */
    public function writeBlocks($key, $blocks_data, $collaplseLevel = NULL){
        foreach($blocks_data as $data){
            $this->writeBlock($key, $data, $collaplseLevel);
        }
        return $this;
    }

    /**
     * Вставляет одиночный блок данных в текущий лист
     * @param string $key Метка шаблона данных
     * @param array $data переменные, параметры ссылок и изображений <ul>
     * <li> 'key' => 'value' - переменные
     * <li> 'key' => array('url' => '', 'text' => '', 'tooltip' => '') - ссылки
     * <li> 'key' => array('path' => '', 'width' => , 'height' => , 'offsetX' => , 'offsetY' => ) - изображения </ul>
     * @param int $collapseLevel
     * @return $this
     * @throws \Exception
     */
    public function writeBlock($key, $data = array(), $collapseLevel = NULL){
        if (empty($this->placeholders[$this->activeTemplateIndex][$key])){
            throw new \Exception('Placeholder "' . $key . '" doesn\'t exists');
        }
        $worksheet = $this->excel_obj->getActiveSheet();
        $sheet_index = $this->excel_obj->getActiveSheetIndex();
        $tmpl = $this->placeholders[$this->activeTemplateIndex][$key];
        $field_values = $tmpl[self::FIELD_VALUES];
        if (!empty($tmpl[self::PLACEHOLDER_VARIABLES])){
            $replacements = array();
            foreach($tmpl[self::PLACEHOLDER_VARIABLES] as $var_name => $var_plc){
                $replacements[$var_plc] = !empty($data[$var_name]) ? $data[$var_name] : '';
            }
            foreach($field_values as $i => $fv){
                foreach($fv as $j => $value){
                    $field_values[$i][$j] = str_replace($tmpl[self::PLACEHOLDER_VARIABLES], $replacements, $value);
                }
            }
        }
        $start_line = !empty($this->sheet_next_line[$sheet_index]) ? $this->sheet_next_line[$sheet_index] : 1;
        $end_line = $start_line + $tmpl[self::END_ROW] - $tmpl[self::START_ROW];

        $worksheet->fromArray($field_values, NULL, 'A'.$start_line);
        // clone styles
        for($src_row = $tmpl[self::START_ROW]; $src_row <= $tmpl[self::END_ROW]; $src_row ++){
            $dst_row = $start_line + $src_row - $tmpl[self::START_ROW];
            $worksheet->getRowDimension($dst_row)->setRowHeight($tmpl['rows_params'][$src_row][self::HEIGHT])->setXfIndex($tmpl['rows_params'][$src_row][self::XF_INDEX]);
            foreach(range($tmpl[self::START_COL], $tmpl[self::END_COL]) as $src_col){
                $dst_col = self::$symbol_shift[$src_col];
                $worksheet->getCell($dst_col.$dst_row)->setXfIndex($tmpl[self::FIELDS_XF_INDEXES][$src_col][$src_row]);
            }
        }
        if (!empty($tmpl[self::PLACEHOLDER_LINKS])){
            foreach($tmpl[self::PLACEHOLDER_LINKS] as $link_col => $plc_link_rows){
                foreach($plc_link_rows as $link_row_offset => $link_key){
                    if (!empty($data[$link_key])){
                        $worksheet->getCell($link_col . ($start_line + $link_row_offset))->setHyperlink($worksheet->getHyperlink()
                            ->setUrl(is_array($data[$link_key]) ? $data[$link_key]['url'] : $data[$link_key])
                            ->setTooltip(!empty($data[$link_key]['tooltip']) ? $data[$link_key]['tooltip'] : ''))
                            ->setValue($data[$link_key]['text']);

                    }
                }
            }
        }
        if (!empty($tmpl[self::PLACEHOLDER_IMAGES])){
            foreach($tmpl[self::PLACEHOLDER_IMAGES] as $img_col => $plc_img_rows) {
                foreach ($plc_img_rows as $img_row_offset => $img_key) {
                    if (!empty($data[$img_key])) {
                        $image = $this->createImage($data[$img_key]);
                        $image->setCoordinates($img_col . ($start_line + $img_row_offset))
                            ->setWorksheet($worksheet);
                    }
                }
            }
        }
        if (!empty($tmpl[self::MERGE_RANGES])){
            foreach($tmpl[self::MERGE_RANGES] as $merge_range_data){
                $merge_range = $merge_range_data[self::START_COL] . ($start_line + $merge_range_data[self::START_ROW_OFFSET]) . ':' . $merge_range_data[self::END_COL] . ($start_line + $merge_range_data[self::END_ROW_OFFSET]);
                $worksheet->mergeCells($merge_range);
            }
        }
        if (!empty($tmpl[self::IMAGES_LIST])){
            foreach($tmpl[self::IMAGES_LIST] as $image_data){
                /** @var \PHPExcel_Worksheet_Drawing $image */
                $image = $this->cloneImage($image_data[self::IMAGE]);
                $image->setCoordinates($image_data[self::START_COL] . ($start_line + $image_data[self::START_ROW_OFFSET]));
                $image->setWorksheet($worksheet);
            }
        }
        if (!is_null($collapseLevel)){
            foreach(range($start_line, $end_line) as $line){
                $worksheet->getRowDimension($line)->setOutlineLevel($collapseLevel)->setVisible(true);
            }
        }
        // Сохраняем индекс следующей строки
        $this->sheet_next_line[$sheet_index] = $end_line + 1;
        return $this;
    }

    /**
     * @param \PHPExcel_Worksheet_Drawing $image
     * @return  \PHPExcel_Worksheet_Drawing
     */
    private function cloneImage($image){
        $new_image = new  \PHPExcel_Worksheet_Drawing();
        // Вытаскиваем изображение на диск
        $path = $image->getPath();
        $imagesize = getimagesize($path);
        $save_image_path = \LPS\Config::getRealDocumentRoot() . self::IMAGES_TMP_DIR;
        if (!file_exists($save_image_path)){
            mkdir($save_image_path);
        }
        $tmp_file_name = $save_image_path . '/tmp' . md5($path . time());
        switch($imagesize['mime']){
            case image_type_to_mime_type(IMAGETYPE_GIF):
                $tmp_file_name .= '.gif';
                $tmp_image = imagecreatefromgif($path);
                imagegif($tmp_image, $tmp_file_name);
                break;
            case image_type_to_mime_type(IMAGETYPE_JPEG):
                $tmp_file_name .= '.jpeg';
                $tmp_image = imagecreatefromjpeg($path);
                imagejpeg($tmp_image, $tmp_file_name, 100);
                break;
            case image_type_to_mime_type(IMAGETYPE_PNG):
                $tmp_file_name .= '.png';
                $tmp_image = imagecreatefrompng($path);
                imagepng($tmp_image, $tmp_file_name, 9);
                break;
        }
        if(!empty($tmp_image)) {
            // если удалось сохранить изображение - скармливаем его новому объекту-картинке и копируем все параметры из исходного объекта
            $this->tmp_images[] = $tmp_file_name;
            $new_image->setPath($tmp_file_name)
                ->setOffsetX($image->getOffsetX())
                ->setOffsetY($image->getOffsetY())
                ->setHeight($image->getHeight())
                ->setWidth($image->getWidth())
                ->setDescription($image->getDescription())
                ->setName($image->getName())
                ->setRotation($image->getRotation())
                ->setShadow($image->getShadow());
        }
        return $new_image;
    }

    /**
     * Создание объекта картинки из файла с заданными параметрами
     * @param array $image_data keys('path', 'width', 'height', 'description', 'offsetX', 'offsetY')
     * @return \PHPExcel_Worksheet_Drawing
     */
    private function createImage($image_data){
        $image = new \PHPExcel_Worksheet_Drawing();
        $image->setPath($image_data['path']);
        if (!empty($image_data['width'])){
            $image->setWidth($image_data['width']);
        }
        if (!empty($image_data['height'])){
            $image->setHeight($image_data['height']);
        }
        if (!empty($image_data['description'])){
            $image->setDescription($image_data['description']);
        }
        if (!empty($image_data['offsetX'])){
            $image->setOffsetX($image_data['offsetX']);
        }
        if (!empty($image_data['offsetY'])){
            $image->setOffsetY($image_data['offsetY']);
        }
        return $image;
    }

    /**
     *
     * @param string $path_to_file
     * @param bool $new_format формат документа xls или xlsx
     * @return boolean
     */
    public function save($path_to_file, $new_format = TRUE){
        //удаляем листы с шаблонами
        foreach($this->templateSheets as $templateSheet){
            $this->excel_obj->removeSheetByIndex($this->excel_obj->getIndex($templateSheet));
        }
        if ($new_format){
            $writer = new \PHPExcel_Writer_Excel2007($this->excel_obj);
        }else{
            $writer = new \PHPExcel_Writer_Excel5($this->excel_obj);
        }
        try{
            $file_path = \LPS\Config::getRealDocumentRoot() . '/' . $path_to_file;
            $writer->save($file_path);
            foreach($this->tmp_images as $file){
                unlink($file);
            }
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }
} 