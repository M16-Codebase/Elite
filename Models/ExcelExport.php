<?php
/**
 * Экспорт данных в Excel с использованием шаблона
 *
 * @author Charles Manson
 */
namespace Models;
class ExcelExport {
    const MAX_LIST_NAME = 31;
    private $placeholders = array();
    private $excel_obj = NULL;
    private $object_key = NULL;
    private $template = NULL;
    private $firstSheet = NULL;
    private static $registry = array();
    /**
     * 
     * @param string $template шаблон
     * @param array $data дополнительные данные
     * @return ExcelExport
     */
    public static function factory($template = NULL, $data = NULL){
        $object_key = $template . serialize($data);
        if (!isset(self::$registry[$object_key])){
            $data['object_key'] = $object_key;
            self::$registry[$object_key] = new ExcelExport($template, $data);
        }
        return self::$registry[$object_key];
    }

    private function __construct($template = NULL, $data = NULL) {
        $this->template = $template;
        $this->object_key = $data['object_key'];
        if (!empty($template)){
            $this->loadTemplate($template, $data);
        }
    }
    
    private function loadTemplate($template, $data = NULL){
       /* $type = \PHPExcel_IOFactory::identify($template);
        $this->excel_obj = \PHPExcel_IOFactory::createReader($type);
        $this->excel_obj->load($template);*/
        $this->excel_obj = \PHPExcel_IOFactory::load($template);
        $this->excel_obj->setActiveSheetIndex();
        $sheet = $this->excel_obj->getActiveSheet();
        foreach($sheet->getRowIterator() as $row){
            /* @var $row \PHPExcel_Worksheet_RowIterator */
            $cell_iterator = $row->getCellIterator();
            foreach($cell_iterator as $cell){
                /* @var $cell \PHPExcel_Worksheet_CellIterator */
                $value = $cell->getValue();
                $row_index = $cell->getRow();
                $col_index = $cell->getColumn();
                if (preg_match('~^\{([^[]+\[\])?\.?([^}]+)\}$~', $value, $match)){
                    $count = count($match);
                    if ($count == 2 || $count == 3 && $match[1] == '') {
                        // Одиночная ячейка
                        $cell_name = $count == 2 ? $match[1] : $match[2];
                        if (isset($this->placeholders[$cell_name])){
                            throw new \Exception('Duplicate cell "'.$cell_name.'"');
                        }
                        $this->placeholders[$cell_name] = array(
                            'name' => $cell_name,               // Имя ячейки
                            'type' => 'cell',                   // Тип плейсхолдера ячейка
							'init_row_index' => $row_index,		// Самый первый начальный индекс в шаблоне
                            'row' => $row_index,                // Индекс строки
                            'col' => $col_index                 // Индекс колонки
                        );
                    } elseif ($count == 3){
                        // строка массива
                        $row_name = substr($match[1], 0, -2);
                        $cell_name = $match[2];
                        if (!isset($this->placeholders[$row_name])){
                            // инициализация плейсхолдера
                            $this->placeholders[$row_name] = array(
                                'name' => $row_name,            // Имя массива
                                'type' => 'row',                // Тип плейсхолдера - строка массива
								'init_row_index' => $row_index, // Самый первый начальный индекс в шаблоне
                                'row' => $row_index,            // Индекс первой строки
                                'end_row' => $row_index,        // Индекс последней строки
                                'start_col' => $col_index,      // Первый столбец
                                'end_col' => $col_index,        // Последний столбец
                                'row_count' => 1,               // Количество строк
                                'cells' => array(),             // Массив полей строки
                                'merge' => array()              // Массив объединенных ячеек
                            );
                        }
                        if (isset($this->placeholders[$row_name]['cells'][$cell_name])){
                            
                            // Exception duplicate cell
                        }
                        $offset = $row_index - $this->placeholders[$row_name]['row'];
                        $this->placeholders[$row_name]['cells'][$cell_name] = array(
                            'name' => $cell_name,
                            'index' => $col_index,
                            'offset' => $row_index - $this->placeholders[$row_name]['row'],
                            'xf_index' => $sheet->getCell($col_index.$row_index)->getXfIndex()
                        );
                        
                        if ($row_index > $this->placeholders[$row_name]['end_row']){
                            $this->placeholders[$row_name]['end_row'] = $row_index;
                            $this->placeholders[$row_name]['row_count'] = $offset + 1;
                        }
                        if ($col_index > $this->placeholders[$row_name]['end_col']){
                            $this->placeholders[$row_name]['end_col'] = $col_index;
                        } elseif ($col_index < $this->placeholders[$row_name]['start_col']){
                            $this->placeholders[$row_name]['start_col'] = $col_index;
                        }
                    }
                }elseif(preg_match('~^\~([^\~]+)\~$~', $value, $match)){//заголовок
                    $cell_name = $match[1];
                    if (isset($this->placeholders[$cell_name])){
                        throw new \Exception('Duplicate cell "'.$cell_name.'"');
                    }
                    $this->placeholders[$cell_name] = array(
                        'name' => $cell_name,           // Имя ячейки
                        'type' => 'title',              // Тип плейсхолдера заголовок
						'init_row_index' => $row_index, // Самый первый начальный индекс в шаблоне
                        'row' => $row_index,            // Индекс строки
                        'end_row' => $row_index,        // Индекс последней строки
                        'col' => $col_index,            // Индекс колонки
                        'start_col' => $col_index,      // Первый столбец
                        'end_col' => $col_index,        // Последний столбец
                        'row_count' => 1,               // Количество строк
                        'merge' => array()              // Массив объединенных ячеек
                    );
                }
            }
        }
        /**
         * Поиск объединенных ячеек
         * Те что попадают в плейсхолдеры строк массива записываем в свойства плейсхолдера
         * start_col и end_col - абсолютные индексы столбцов объединенной области
         * start_offset и end_offset - смещения строк объединенной области относительно первой строки плейсхолдера
         */
        foreach($sheet->getMergeCells() as $mrg_range){
            if (preg_match('~^([^\d]+)(\d+):([^\d]+)(\d+)$~', $mrg_range, $match)){
                foreach($this->placeholders as $key => $plc){
                    if ($plc['type'] == 'cell'){
                        continue;
                    }
                    $s_row = $match[2]; $f_row = $match[4];
                    if ($s_row >= $plc['row'] && $s_row <= $plc['end_row'] && $f_row >= $plc['row'] && $f_row <= $plc['end_row']){
                        $this->placeholders[$key]['merge'][] = array(
                            'start_col' => $match[1],
                            'end_col' => $match[3],
                            'start_offset' => $match[2] - $plc['row'],
                            'end_offset' => $match[4] - $plc['row']
                        );
                        break;
                    }
                }
            }
        }
        /**
         * Загрузка стилей плейсхолдеров строк массива
         * обходим все ячейки плейсхолдера и сохраняем стили в массив array[row_offset][column_index] = StyleArray
         */
        foreach($this->placeholders as $key => $plc){
            if ($plc['type'] == 'cell'){
                continue;
            }
            $plc_styles = array();
            $initial_row = $plc['row'];
            for($row_offset = 0; $row_offset < $plc['row_count']; $row_offset++){
                $plc_styles[$row_offset] = array();
                for($column = $plc['start_col']; $column <= $plc['end_col']; $column++){
                    $cell_coords = $column . ($initial_row + $row_offset);
                    $cell_style = $sheet->getStyle($cell_coords);
                    $src_style=$this->makeStyleArray($cell_style);
                    $plc_styles[$row_offset][$column] = $src_style;
                }
            }
            $this->placeholders[$key]['styles'] = $plc_styles;
            $row_height = $sheet->getRowDimension($initial_row)->getRowHeight();
            $this->placeholders[$key]['row_height'] = $row_height;
        }
        $this->firstSheet = $sheet;//первый лист всегда шаблон
        $this->addSheet(!empty($data['list_name']) ? $data['list_name'] : NULL);
    }
    
    /**
     * Возвращает массив стилей ячейки
     * @param \PHPExcel_style $cell_style адрес ячейки, например A13
     * @return array 
     */
    private function makeStyleArray($cell_style){
        $font = $cell_style->getFont();
        $fill = $cell_style->getFill();
        $borders = $cell_style->getBorders();
        $align = $cell_style->getAlignment();
        $numberFormat = $cell_style->getNumberFormat();
        $result = array(
            'font' => array(
                'name' => $font->getName(),
                'bold' => $font->getBold(), 
                'italic' => $font->getItalic(),
               // 'superScript' => $font->getSuperScript(),
               // 'subScript' => $font->getSubScript(),
                'underline' => $font->getUnderline(),
                'strike' => $font->getStrikethrough(), 
                'color' => array(
                    'argb' => $font->getColor()->getARGB()
                ),
                'size' => $font->getSize()
            ),
            'fill' => array(
                'type' => $fill->getFillType(), 
                'rotation' => $fill->getRotation(), 
                'startcolor' => array(
                    'argb' => $fill->getStartColor()->getARGB()
                ),
                'endcolor' => array(
                    'argb' => $fill->getEndColor()->getARGB()
                ),
                'color' => array(
                    'argb'
                )
            ),
            'alignment' => array(
                'horizontal' => $align->getHorizontal(),
                'vertical' => $align->getVertical(),
                'rotation' => $align->getTextRotation(),
                'wrap' => $align->getWrapText(),
                'shrinkToFit' => $align->getShrinkToFit(),
                'indent' => $align->getIndent()
            ),
            'borders' => array(
                'top' => $this->getBorderStyle($borders->getTop()),
                'left' => $this->getBorderStyle($borders->getLeft()),
                'right' => $this->getBorderStyle($borders->getRight()),
                'bottom' => $this->getBorderStyle($borders->getBottom()),
            ),
            'numberformat' => array(
                'code' => $numberFormat->getFormatCode()
            )
        );
        return $result;
    }
    /**
     * Возвращает массив стилей границы ячейки
     * @param \PHPExcel_Style_Border $border
     * @return array
     */
    private function getBorderStyle($border){
        $result = array(
            'style' => $border->getBorderStyle(),
            'color' => array(
                'rgb' => $border->getColor()->getRGB()
            )
        );
        return $result;
    }
    
    public function showPlaceholders(){
        var_dump($this->placeholders);
    }
    
    public function loadData($data = array()){
        $sheet = $this->excel_obj->getActiveSheet();
        foreach ($data as $key=>$data_entity){
            if (!isset($this->placeholders[$key])){
                continue;
                //Exception
            }
            $plc = $this->placeholders[$key];
            $row_index = $plc['row'];
            $sheet->getDefaultRowDimension()->setRowHeight(20);
            switch ($plc['type']) {
                case 'title':
                    if (is_array($data_entity)){
                        throw new Exception('Can\'t print array in single cell, variable expected');
                    }
                    $sheet->insertNewRowBefore($row_index, 1);
                    $sheet->setCellValue($plc['col'].$plc['row'], $data_entity);
                    $sheet->getStyle($plc['start_col'].$row_index)->applyFromArray($plc['styles'][0][$plc['start_col']]);
                    foreach($plc['merge'] as $mrg){
                        $sheet->mergeCells($mrg['start_col'].($row_index + $mrg['start_offset']).':'.$mrg['end_col'].($row_index + $mrg['end_offset']));
                    }
                    //высота строки
                    if (!empty($plc['row_height'])){
                        $sheet->getRowDimension($row_index)->setRowHeight($plc['row_height']);
                    }
                    $this->shiftPlaceholders($plc['row'], 1);//сдвигаем всех на одну строку, т.к. записали заголовок
                    break;
                case 'cell':
                    if (is_array($data_entity)){
                        throw new Exception('Can\'t print array in single cell, variable expected');
                    }
                    $sheet->setCellValue($plc['col'].$plc['row'], $data_entity);
                    break;
                
                case 'row':
                   
                    if (!is_array($data_entity)){
                        // Exception
                        break;
                    }
                    $row_index = $plc['row'];
                    $inserted_rows_count = count($data_entity)*$plc['row_count'];
                    $sheet->insertNewRowBefore($row_index, 1);//внимание, магия! все последующие строчки создаются с высотой предыдущей
                    if (!empty($plc['row_height'])){
                        $sheet->getRowDimension($row_index)->setRowHeight($plc['row_height']);
                    }
                    if ($inserted_rows_count > 1){
                        $sheet->insertNewRowBefore($row_index + 1, $inserted_rows_count - 1);
                    }
                    //высота строки //каждой строке менять высоту не получилось, выжирает память
//                    if (!empty($plc['row_height'])){
//                        for($i=$row_index;$i++;$i<=$row_index+$inserted_rows_count){
//                            $sheet->getRowDimension($i)->setRowHeight($plc['row_height']);
//                        }
//                    }
                    foreach($data_entity as $row_entity){
                        foreach($plc['cells'] as $cell_name=>$cell){
                            if (empty($row_entity[$cell_name])){
                                // Exception
                            }
                            $cell_index = $cell['index'].($row_index + $cell['offset']);
                            $sheet->setCellValue($cell_index, $row_entity[$cell_name]);
                        }
                        for($row_offset = 0; $row_offset < $plc['row_count']; $row_offset++){
                            for($column_addr = $plc['start_col']; $column_addr <= $plc['end_col']; $column_addr++){
                                $dest_row = $row_index + $row_offset;
                                $sheet->getStyle($column_addr.$dest_row)->applyFromArray($plc['styles'][$row_offset][$column_addr]);
                            }
                        }
                        foreach($plc['merge'] as $mrg){
                            $sheet->mergeCells($mrg['start_col'].($row_index + $mrg['start_offset']).':'.$mrg['end_col'].($row_index + $mrg['end_offset']));
                        }
                        $row_index += $plc['row_count'];
                    }
                    $this->shiftPlaceholders($plc['row'], count($data_entity) * $plc['row_count']);
                    
                    break;
                    
                default:
                    break;
            }
        }
        return $this;
    }
    /**
     * Добавляем лист
     * @param string $name Название листа
     * @return \Models\ExcelExport
     */
    public function addSheet($name){
        /* @var $new_sheet \PHPExcel_Worksheet */
        if (!empty($name)){
            $name = mb_substr($name, 0, 31);
        }
        //клонируем первый лист с шаблоном
        if (!empty($this->firstSheet)){
            $tmp_new_sheet = clone $this->firstSheet;
            if (!empty($name)){
                $tmp_new_sheet->setTitle($name);//пишем название
            }
            $new_sheet = $this->excel_obj->addSheet($tmp_new_sheet);
			$active_index = $this->excel_obj->getActiveSheetIndex();
			if ($active_index != 0){//удаляем данные шаблона (это нельзя делать у самого шаблона)
				$this->clearTemplateData();
			}
        }else{//, либо просто создаем новый
            $new_sheet = $this->excel_obj->createSheet();
            if (!empty($name)){
                $new_sheet->setTitle($name);//пишем название
            }
        }
        $new_index = $this->excel_obj->getIndex($new_sheet);
        $this->excel_obj->setActiveSheetIndex($new_index);//переключаемся на новый лист
        $this->rowUpPlaceholders();
        return $this;
    }
	public function getActive(){
		return $this->excel_obj->getActiveSheetIndex();
	}
    /**
     * 
     * @param string $path_to_file
     * @param bool $new_format формат документа xls или xlsx
     * @return boolean
     */
    public function save($path_to_file, $new_format = FALSE){
        //удаляем первый лист с шаблоном
        $active_index = $this->excel_obj->getActiveSheetIndex();
        if (!empty($this->firstSheet)){
            if ($active_index == 0){
                return;
            }
            $this->clearTemplateData();//с последнего листа удаляем лишнее
            $this->excel_obj->removeSheetByIndex(0);//удаляем первый с шаблоном, т.к. он теперь не нужен
            //в PHPExcel косяк с переприсвоением другого текущего индекса
            if ($active_index > 0){
//                $this->excel_obj->setActiveSheetIndex(--$active_index);
                $this->excel_obj->setActiveSheetIndex(0);
            }
        }
        if ($new_format){
            $writer = new \PHPExcel_Writer_Excel2007($this->excel_obj);
        }else{
            $writer = new \PHPExcel_Writer_Excel5($this->excel_obj);
        }
        try{
            $file_path = \LPS\Config::getRealDocumentRoot() . '/' . $path_to_file;
            $writer->save($file_path);
        } catch (Exception $ex) {
            return false;
        }
        return true;
    }
    /**
     * Сбрасываем индексы строк у плейсхолдеров
     */
    private function rowUpPlaceholders(){
		$min_index = 0;
        foreach($this->placeholders as $key=>$plc){
			//надо сбросить на минимальный индекс из существующих, чтобы весь шаблон с переменными смещался вниз
			if ($min_index == 0 || $min_index > $this->placeholders[$key]['init_row_index']){
				$min_index = $this->placeholders[$key]['init_row_index'];
			}
        }
		foreach ($this->placeholders as $key => $plc){
			$this->placeholders[$key]['row'] = $min_index;
		}
    }
    
    private function shiftPlaceholders($row, $count){
        foreach($this->placeholders as $key=>$plc){
            if ($plc['row'] >= $row){
                $this->placeholders[$key]['row'] += $count;
            }
        }
    }
    /**
     * Чистим активный лист от данных шаблона
     * @return boolean
     */
    private function clearTemplateData(){
        $sheet = $this->excel_obj->getActiveSheet();
        $row_index = 1;
        $last_row_count = array();
        foreach($this->placeholders as $key=>$plc){
            if ($plc['type'] == 'cell'){
                continue;
            }
            if ($this->placeholders[$key]['row'] > $row_index){
                $row_index = $this->placeholders[$key]['row'];
            }
            $last_row_count[$plc['type']] = $plc['row_count'];
            foreach ($plc['merge'] as $mrg){
                foreach($sheet->getMergeCells() as $mrg_range){
                    $range = $mrg['start_col'] . ($row_index + $mrg['start_offset']) . ':' . $mrg['end_col'] . ($row_index + $mrg['end_offset']);
                    if ($mrg_range == $range){
                        $sheet->unmergeCells($range);
                    }
                }
            }
        }
        $count = array_sum($last_row_count);
        $sheet->removeRow($row_index, $count);
        return TRUE;
    }
}