<?php
namespace Home\Controller;
use Think\Controller;
class CronController extends Controller {

	function index(){
	}

    function import(){
    	$file = ROOT_PATH."Public/abc.xlsx";

		$exceArray = $this->readExcel($file);

		print_r($exceArray);	 
    }

	function readExcel($path){

        import("Common.Util.PHPExcel");        
        import("Common.Util.PHPExcel.IOFactory");

	    $xlsReader = \PHPExcel_IOFactory::createReader('Excel2007');  
	    $xlsReader->setReadDataOnly(true);
	    $xlsReader->setLoadSheetsOnly(true);
	    $Sheets = $xlsReader->load($path);

		foreach ($Sheets->getWorksheetIterator() as $worksheet) {     //遍历工作表
		       echo 'Worksheet - ' , $worksheet->getTitle() , PHP_EOL;
		       foreach ($worksheet->getRowIterator() as $row) {       //遍历行
		             echo '    Row number - ' , $row->getRowIndex() , PHP_EOL;
		            $cellIterator = $row->getCellIterator();   //得到所有列
		            $cellIterator->setIterateOnlyExistingCells( false); // Loop all cells, even if it is not set
		             foreach ($cellIterator as $cell) {  //遍历列
		                   if (!is_null($cell)) {  //如果列不给空就得到它的坐标和计算的值
		                         echo '        Cell - ' , $cell->getCoordinate() , ' - ' , $cell->getCalculatedValue() , PHP_EOL;
		                  }
		            }
		      }
		}

	    //$dataArray = $Sheets->getSheet(0)->toArray();
	    //return $dataArray;
	}
}