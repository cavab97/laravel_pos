<?php
namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Attributes;
use App\Models\Branch;
use App\Models\Product;
use App\Models\PriceType;
use App\Models\Printer;
use App\Models\Modifier;
use App\Models\UserBranch;
use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ProductImportController extends Controller
{

    /*
     * @method   : Appdata
     * @params   : datetime, branchId
     * @respose  : Json updated data secound time
     * $reference:  https://phpdox.net/demo/Symfony2/classes/Doctrine_DBAL_Schema_Column.xhtml
     *              https://phpspreadsheet.readthedocs.io/en/latest/search.html
     *              https://github.com/PHPOffice/PHPExcel/blob/1.8/Classes/PHPExcel/Cell/DataValidation.php
     * Doctrine\DBAL\Schema\Column
     */

    public function getXlsFile(Request $request)
    {
        $extension = $request->extension;
        $userData = Auth::user();
        if(Auth::user()->role == 1) {
            $branchIds = Branch::pluck('branch_id');
            $branchList =  Branch::select('branch_id', 'name')->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get();
        }
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Marslab Solution')
            ->setLastModifiedBy('Marslab Solution')
            ->setTitle('MCNPOS Product Import')
            ->setSubject('MCNPOS Product Import')
            ->setDescription('mcnpos.com.my use only, Developer from Marslab Solution.')
            ->setKeywords('MCNPOS Product Import')
            ->setCategory('Product');
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()
            ->setCellValue('A1', 'Name')
            ->setCellValue('B1', 'Name_2')
            ->setCellValue('C1', 'Label')
            ->setCellValue('D1', 'Description')
            ->setCellValue('E1', 'SKU')
            ->setCellValue('F1', 'price_type')
            ->setCellValue('G1', 'price_type_value')
            ->setCellValue('H1', 'Price')
            ->setCellValue('I1', 'old_price')
            ->setCellValue('J1', 'has_inventory')
            ->setCellValue('K1', 'beer_management')
            ->setCellValue('L1', 'has_setmeal')
            ->setCellValue('M1', 'branch_1')
            ->setCellValue('N1', 'branch_2')
            ->setCellValue('O1', 'branch_3')
            ->setCellValue('P1', 'warning_stock_level')
            ->setCellValue('Q1', 'display_order')
            ->setCellValue('R1', 'printer_id')
            ->setCellValue('S1', 'attribute_1')
            ->setCellValue('T1', 'attribute_2')
            ->setCellValue('U1', 'attribute_3')
            ->setCellValue('V1', 'attribute_4')
            ->setCellValue('W1', 'attribute_5')
            ->setCellValue('X1', 'attribute_6')
            ->setCellValue('Y1', 'attribute_7')
            ->setCellValue('Z1', 'attribute_8')
            ->setCellValue('AA1', 'attribute_9')
            ->setCellValue('AB1', 'attribute_10')
            ->setCellValue('AC1', 'modifier_1')
            ->setCellValue('AD1', 'modifier_2')
            ->setCellValue('AE1', 'modifier_3')
            ->setCellValue('AF1', 'modifier_4')
            ->setCellValue('AG1', 'modifier_5')
            ->setCellValue('AH1', 'status')
            ;
        $validation = $spreadsheet->getActiveSheet()->getCell('H1')->getDataValidation();

        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setPromptTitle('Info :');

        $validation->setPrompt("Second Name using for display on kitchen");
        for ($index=2; $index < 5; $index++) {
            $spreadsheet->getActiveSheet()->getCell('B'.$index)->setDataValidation(clone $validation);
        }
        $validation->setPrompt("Item Description.\r\nIt only display on admin panel, from edit item use");
        for ($index=2; $index < 5; $index++) {
            $spreadsheet->getActiveSheet()->getCell('D'.$index)->setDataValidation(clone $validation);
        }

        $validation->setPrompt("Item Code.\r\nIt must a unique code for each product");
        for ($index=2; $index < 5; $index++) {
            $spreadsheet->getActiveSheet()->getCell('E'.$index)->setDataValidation(clone $validation);
        }
        $validation->setPrompt("The history price for product.\r\nCan be leave blank");
        for ($index=2; $index < 5; $index++) {
            $spreadsheet->getActiveSheet()->getCell('I'.$index)->setDataValidation(clone $validation);
        }
        $validation->setPrompt("If has inventory, reach how much need info?");
        for ($index=2; $index < 5; $index++) {
            $spreadsheet->getActiveSheet()->getCell('P'.$index)->setDataValidation(clone $validation);
        }
        $validation->setPrompt("Sequence for product in the menu.\r\nThe lower the first.\r\nEx:\r\n1 is first,\r\n2 is second,\r\n3 is third.\r\nIf same value, follow product create time");
        for ($index=2; $index < 5; $index++) {
            $spreadsheet->getActiveSheet()->getCell('Q'.$index)->setDataValidation(clone $validation);
        }



        $validation->setPrompt("The value of type for the product.");
        $validation->setType(DataValidation::OPERATOR_GREATERTHANOREQUAL);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
        $validation->setFormula1(0.01);
        for ($index=2; $index < 5; $index++) {
            $spreadsheet->getActiveSheet()->getCell('G'.$index)->setDataValidation(clone $validation);
        }

        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP );
        $validation->setFormula1('"0,1"');
        $validation->setPrompt("This product is store control product?");
        for ($index=2; $index < 102; $index++) {
            $spreadsheet->getActiveSheet()->getCell('J'.$index)->setDataValidation(clone $validation);
        }
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP );
        $validation->setFormula1('"0,1"');
        $validation->setPrompt("This product got beer management? Normal product choose 0.");
        for ($index=2; $index < 102; $index++) {
            $spreadsheet->getActiveSheet()->getCell('K'.$index)->setDataValidation(clone $validation);
        }
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP );
        $validation->setFormula1('"0,1"');
        $validation->setPrompt("This got belong to some set meal?");
        for ($index=2; $index < 102; $index++) {
            $spreadsheet->getActiveSheet()->getCell('L'.$index)->setDataValidation(clone $validation);
        }
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP );
        $validation->setFormula1('"0,1"');
        $validation->setPrompt("This product is ready to sell?\r\n ".str_pad(0, 2, " ", STR_PAD_LEFT)." = disabled.\r\n ".str_pad(1, 2, " ", STR_PAD_LEFT)." = ready.");
        for ($index=2; $index < 102; $index++) {
            $spreadsheet->getActiveSheet()->getCell('AH'.$index)->setDataValidation(clone $validation);
        }
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP );
        //$validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
        //$validation->setAllowBlank(false);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list. It will cause import error.');
        $validation->setPromptTitle('Pick from list');
        $inputValueArray = array();
        $stringPromptText = "";
        $columnsArray = array();

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            foreach ($spreadsheet->getActiveSheet()->getColumnIterator() as $column) {
                array_push($columnsArray, $column->getColumnIndex());
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }


        //Drop down list for price type
        $inputValueArray = array();
        $stringPromptText = "";
        $priceTypeList = PriceType::select('pt_id','name')->get();
        if(count($priceTypeList) > 0) {
            foreach ($priceTypeList as $priceType) {
                $stringPromptText .= str_pad($priceType->pt_id, 3, " ", STR_PAD_LEFT)." = ".$priceType->name."\r\n";
                array_push($inputValueArray, $priceType->pt_id);
            }
            $validation->setPrompt($stringPromptText);
            $validation->setFormula1('"'.implode(",",$inputValueArray).'"');
            for ($index=2; $index < 102; $index++) {
                $spreadsheet->getActiveSheet()->getCell('F'.$index)->setDataValidation(clone $validation);
            }
        }

        //Drop down list for branch
        if(count($branchList) > 0) {
            foreach ($branchList as $branch) {
                $stringPromptText .= str_pad($branch->branch_id, 3, " ", STR_PAD_LEFT)." = ".$branch->name."\r\n";
                array_push($inputValueArray, $branch->branch_id);
            }
            $validation->setPrompt($stringPromptText);
            $validation->setFormula1('"'.implode(",",$inputValueArray).'"');
            foreach(range('M','O') as $columnID) {
                for ($index=2; $index < 102; $index++) {
                    $spreadsheet->getActiveSheet()->getCell($columnID.$index)->setDataValidation(clone $validation);
                }
            }
        }


        //Drop down list fro printer
        $inputValueArray = array();
        $stringPromptText = "";
        $printerList = Printer::whereIn('branch_id', $branchIds)->select('printer_id','printer_name')->get();
        if(count($printerList) > 0) {
            foreach ($printerList as $printer) {
                $stringPromptText .= str_pad($printer->printer_id, 3, " ", STR_PAD_LEFT)." = ".$printer->printer_name."\r\n";
                array_push($inputValueArray, $printer->printer_id);
            }
            $validation->setPrompt($stringPromptText);
            $validation->setFormula1('"'.implode(",",$inputValueArray).'"');
            for ($index=2; $index < 102; $index++) {
                $spreadsheet->getActiveSheet()->getCell('R'.$index)->setDataValidation(clone $validation);
            }
        }


        //Drop down list for attributes
        $inputValueArray = array();
        $stringPromptText = "";
        $attributesList = Attributes::select('attribute_id','name')->get();
        if(count($attributesList) > 0) {
            foreach ($attributesList as $attribute) {
                $stringPromptText .= str_pad($attribute->attribute_id, 3, " ", STR_PAD_LEFT)." = ".$attribute->name."\r\n";
                array_push($inputValueArray, $attribute->attribute_id);
            }
            $validation->setPrompt($stringPromptText);
            $validation->setFormula1('"'.implode(",",$inputValueArray).'"');
            for ($arrayIndex=18; $arrayIndex < 28 && $arrayIndex < count($columnsArray); $arrayIndex++) {
                for ($index=2; $index < 102; $index++) {
                    $spreadsheet->getActiveSheet()->getCell($columnsArray[$arrayIndex].$index)->setDataValidation(clone $validation);
                }
            }
        }

        //Drop down list for modifier
        $inputValueArray = array();
        $stringPromptText = "";
        $modifiersList = Modifier::select('modifier_id','name')->get();
        if(count($modifiersList) > 0) {
            foreach ($modifiersList as $modifier) {
                $stringPromptText .= str_pad($modifier->modifier_id, 3, " ", STR_PAD_LEFT)." = ".$modifier->name."\r\n";
                array_push($inputValueArray, $modifier->modifier_id);
            }
            $validation->setPrompt($stringPromptText);
            $validation->setFormula1('"'.implode(",",$inputValueArray).'"');

            for ($arrayIndex=30; $arrayIndex < count($columnsArray); $arrayIndex++) {
                for ($index=2; $index < 102; $index++) {
                    $spreadsheet->getActiveSheet()->getCell($columnsArray[$arrayIndex].$index)->setDataValidation(clone $validation);
                }
            }
        }
        /**
         * Format Sheet
         */

        $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(90);
        // Save
        if(isset($extension) && strtolower($extension) == "xlsx") {
            $fileName = 'Product_Template_' . time() . '.'.strtolower($extension);
            $writer = new Xlsx($spreadsheet);
        } else {
            $fileName = 'Product_Template_' . time() . '.xls';
            $writer = new Xls($spreadsheet);
        }
        $writer->save($fileName);
    }
}
?>
