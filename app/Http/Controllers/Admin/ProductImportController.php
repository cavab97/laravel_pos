<?php
namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Attributes;
use App\Models\Branch;
use App\Models\Category;
use App\Models\PriceType;
use App\Models\Printer;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\ProductCategory;
use App\Models\ProductAttribute;
use App\Models\ProductModifier;
use App\Models\Modifier;
use App\Models\UserBranch;
use App\Exports\ProductsExport;
use App\Models\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductImportController extends Controller
{

    /*
     * @method   : Appdata
     * @params   : datetime, branchId
     * @respose  : Json updated data secound time
     * $reference:  https://phpdox.net/demo/Symfony2/classes/Doctrine_DBAL_Schema_Column.xhtml
     *              https://phpspreadsheet.readthedocs.io/en/latest
     *              https://github.com/PHPOffice/PhpSpreadsheet/tree/master/src/PhpSpreadsheet
     * Doctrine\DBAL\Schema\Column
     */
    private $headerArray = [
        'Name',
        'Name_2',
        'Label',
        'Description',
        'SKU',
        'category',
        'status',
        'price_type',
        'price_type_value',
        'Price',
        'old_price',
        'has_inventory',
        'beer_management',
        'has_setmeal',
        'branch_1',
        'branch_2',
        'branch_3',
        'warning_stock_level',
        'display_order',
        'printer_id',
        'attribute_1',
        'attribute_2',
        'attribute_3',
        'attribute_4',
        'attribute_5',
        'attribute_6',
        'attribute_7',
        'attribute_8',
        'attribute_9',
        'attribute_10',
        'attribute_price_1',
        'attribute_price_2',
        'attribute_price_3',
        'attribute_price_4',
        'attribute_price_5',
        'attribute_price_6',
        'attribute_price_7',
        'attribute_price_8',
        'attribute_price_9',
        'attribute_price_10',
        'modifier_1',
        'modifier_2',
        'modifier_3',
        'modifier_4',
        'modifier_5',
        'modifier_price_1',
        'modifier_price_2',
        'modifier_price_3',
        'modifier_price_4',
        'modifier_price_5',
    ];
    public function getProductTemplate(Request $request)
    {
        $extension = $request->ext;
        $userData = Auth::user();
        if(Auth::user()->role == 1) {
            $branchIds = Branch::pluck('branch_id');
            $branchList =  Branch::select('branch_id', 'name')->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get();
        }
        $sheet = new Spreadsheet();
        $sheet->getProperties()
            ->setCreator('Marslab Solution')
            ->setLastModifiedBy('Marslab Solution')
            ->setTitle('MCNPOS Product Import')
            ->setSubject('MCNPOS Product Import')
            ->setDescription('mcnpos.com.my use only, Developer from Marslab Solution.')
            ->setKeywords('MCNPOS Product Import')
            ->setCategory('Product');
        $sheet->setActiveSheetIndex(0);
        $spreadsheet = $sheet->getActiveSheet();
        $indexOf = 0;
        $columnsArray = $this->headerArray;
        foreach($this->headerArray as $key=>$value) {
            $spreadsheet->setCellValueByColumnAndRow($key+1, 1, $value);
        }
        $endColumn = $spreadsheet->getHighestColumn();
        $maxRowCanEdit = 102;
        $validation = $spreadsheet->getCell('AZ1')->getDataValidation();

        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setPromptTitle('Info :');

        $validation->setPrompt("Second Name using for display on kitchen");
        for ($index=1; $index < 5; $index++) {
            $spreadsheet->getCell('B'.$index)->setDataValidation(clone $validation);
        }
        $validation->setPrompt("Item Description.\r\nIt only display on admin panel, from edit item use");
        for ($index=1; $index < 5; $index++) {
            $spreadsheet->getCell('D'.$index)->setDataValidation(clone $validation);
        }

        $validation->setPrompt("Item Code.\r\nIt must a unique code for each product");
        $indexOf = array_search('SKU', $columnsArray) + 1;
        for ($index=1; $index < 5; $index++) {
            $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
        }
        $validation->setPrompt("The history price for product.\r\nCan be leave blank");
        $indexOf = array_search('old_price', $columnsArray) + 1;
        for ($index=1; $index < 5; $index++) {
            $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
        }
        $validation->setPrompt("If has inventory, reach how much need info?");
        $indexOf = array_search('warning_stock_level', $columnsArray) + 1;
        for ($index=1; $index < 5; $index++) {
            $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
        }
        $validation->setPrompt("Sequence for product in the menu.\r\nThe lower the first.\r\nEx:\r\n1 is first,\r\n2 is second,\r\n3 is third.\r\nIf same value, follow product create time");
        $indexOf = array_search('display_order', $columnsArray) + 1;
        for ($index=1; $index < 5; $index++) {
            $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
        }



        $validation->setPrompt("The value of type for the product.");
        $validation->setType(DataValidation::OPERATOR_GREATERTHANOREQUAL);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
        $validation->setFormula1(0.01);
        $indexOf = array_search('price_type_value', $columnsArray) + 1;
        for ($index=2; $index < 5; $index++) {
            $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
        }

        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP );
        $validation->setFormula1('"0,1"');
        $validation->setPrompt("This product is store control product?");
        $indexOf = array_search('has_inventory', $columnsArray) + 1;
        for ($index=2; $index < $maxRowCanEdit; $index++) {
            $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
        }
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP );
        $validation->setFormula1('"0,1"');
        $validation->setPrompt("This product got beer management? Normal product choose 0.");
        $indexOf = array_search('beer_management', $columnsArray) + 1;
        for ($index=2; $index < $maxRowCanEdit; $index++) {
            $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
        }
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP );
        $validation->setFormula1('"0,1"');
        $validation->setPrompt("This got belong to some set meal?");
        $indexOf = array_search('has_setmeal', $columnsArray) + 1;
        for ($index=2; $index < $maxRowCanEdit; $index++) {
            $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
        }
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP );
        $validation->setFormula1('"0,1"');
        $validation->setPrompt("This product is ready to sell?\r\n ".str_pad(0, 2, " ", STR_PAD_LEFT)." = disabled.\r\n ".str_pad(1, 2, " ", STR_PAD_LEFT)." = ready.");

        $indexOf = array_search('status', $columnsArray) + 1;
        for ($index=2; $index < $maxRowCanEdit; $index++) {
            $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
        }
        for ($index=2; $index < $maxRowCanEdit; $index++) {
            $spreadsheet->getStyle("J".$index)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getStyle("K".$index)->getNumberFormat()->setFormatCode('0.00');
            for ($indexOfAttr=1; $indexOfAttr <= 10; $indexOfAttr++) {
                $indexOf = array_search('attribute_price_'.$indexOfAttr, $columnsArray) + 1;
                $spreadsheet->getCellByColumnAndRow($indexOf,$index)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            }
            for ($indexOfModi=1; $indexOfModi <= 5; $indexOfModi++) {
                $indexOf = array_search('modifier_price_'.$indexOfModi, $columnsArray) + 1;
                $spreadsheet->getCellByColumnAndRow($indexOf,$index)->getStyle()->getNumberFormat()->setFormatCode('0.00');
            }
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

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getColumnIterator() as $column) {
            if ($column->getColumnIndex() == "E") continue;
            $spreadsheet
                ->getColumnDimension($column->getColumnIndex())
                ->setAutoSize(true);
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
            $indexOf = array_search('price_type', $columnsArray) + 1;
            for ($index=2; $index < $maxRowCanEdit; $index++) {
                $spreadsheet->getCellByColumnAndRow($indexOf, $index)->setDataValidation(clone $validation);
            }
        }

        $inputValueArray = array();
        $stringPromptText = "";
        //Drop down list for branch
        if(count($branchList) > 0) {
            foreach ($branchList as $branch) {
                $stringPromptText .= str_pad($branch->branch_id, 3, " ", STR_PAD_LEFT)." = ".$branch->name."\r\n";
                array_push($inputValueArray, $branch->branch_id);
            }
            $validation->setPrompt($stringPromptText);
            $validation->setFormula1('"'.implode(",",$inputValueArray).'"');
            for ($arrayIndex = 1; $arrayIndex <= 10 ; $arrayIndex++) {
                $indexOf = array_search('branch_'.$arrayIndex, $columnsArray) + 1;
                for ($index=2; $index < $maxRowCanEdit; $index++) {
                    $spreadsheet->getCellByColumnAndRow($indexOf, $index)->setDataValidation(clone $validation);
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
            $indexOf = array_search('printer_id', $columnsArray) + 1;
            for ($index=2; $index < $maxRowCanEdit; $index++) {
                $spreadsheet->getCellByColumnAndRow($indexOf, $index)->setDataValidation(clone $validation);
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
            for ($arrayIndex = 1; $arrayIndex <= 10 ; $arrayIndex++) {
                $indexOf = array_search('attribute_'.$arrayIndex, $columnsArray) + 1;
                for ($index=2; $index < $maxRowCanEdit; $index++) {
                    $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
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

            for ($arrayIndex=1; $arrayIndex <= 5; $arrayIndex++) {
                $indexOf = array_search('modifier_'.$arrayIndex, $columnsArray) + 1;
                for ($index=2; $index < $maxRowCanEdit; $index++) {
                    $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
                }
            }
        }
        //Drop down list for category

        $inputValueArray = array();
        $stringPromptText = "";
        $categoryList = Category::select('category_id','name')->get();
        if(count($categoryList) > 0) {
            foreach ($categoryList as $category) {
                $stringPromptText .= str_pad($category->category_id, 3, " ", STR_PAD_LEFT)." = ".$category->name."\r\n";
                array_push($inputValueArray, $category->category_id);
            }
            $validation->setPrompt($stringPromptText);
            $validation->setFormula1('"'.implode(",",$inputValueArray).'"');
            $indexOf = array_search('category', $this->headerArray) + 1;
            for ($index=2; $index < $maxRowCanEdit; $index++) {
                $spreadsheet->getCellByColumnAndRow($indexOf,$index)->setDataValidation(clone $validation);
            }
        }
        /**
         * Format Sheet
         */


        $spreadsheet->getStyle('J2:'.$endColumn.$maxRowCanEdit)
        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getStyle('A2:'.$endColumn.$maxRowCanEdit)
        ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER );
        $spreadsheet->getSheetView()->setZoomScale(90);
        $spreadsheet->freezePane('F2');
        $spreadsheet->getColumnDimension('E')->setWidth(12);
        $spreadsheet->getStyle('A1:'.$endColumn.'1')->getFont()->setSize(16)->setBold(true);
        $spreadsheet->getProtection()->setSheet(true);
        $spreadsheet->getStyle('A2:'.$endColumn.$maxRowCanEdit)
        ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

        // Save
        // We'll be outputting an excel file
        //header('Content-type: application/vnd.ms-excel');
        if(isset($extension) && strtolower($extension) == "xlsx") {
            $fileName = 'Product_Template_' . time() . '.'.strtolower($extension);
            $writer = new Xlsx($sheet);
        } else {
            $fileName = 'Product_Template_' . time() . '.xls';
            $writer = new Xls($sheet);
        }
        header('Content-Disposition: attachment; filename='.$fileName);
        $writer->save('php://output');
    }

    public function templateToArray(Request $request) {
        if ($request->hasFile('file')) {

            $file = $request->file;
            $folder = $this->createDirectory('template-product');
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'.'.$extension;
            //move_uploaded_file($file, $fileName);
            $file->move($folder, $fileName);
            $inputFileName = $folder.$fileName;
            $inputfiletype = IOFactory::identify($inputFileName);
            $reader = IOFactory::createReader($inputfiletype);
            if (!$reader->canRead($inputFileName)) {
                return response()->json([
                    'status' => 200,
                    'message' => trans('api.file_content_invalid')
                ]);
            }
            try {
                /** Load $inputFileName to a Spreadsheet Object  **/
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($inputFileName);
                $spreadsheet = $spreadsheet->getSheet(0);
                $highestRow = $spreadsheet->getHighestDataRow();
                $highestColumn = $spreadsheet->getHighestDataColumn();
                $colNumber = Coordinate::columnIndexFromString($highestColumn);
                $dataArray = array();
                $keyList = array();
                $nameList = array();
                $errorMessage = "";
                $rowData = $spreadsheet->rangeToArray('A1'. ':' . $highestColumn .'1',NULL,TRUE,FALSE)[0];
                if(count($this->headerArray) != $colNumber) {
                    $addMessage = "";
                    if (count($this->headerArray) < $colNumber) {
                        $addMessage .= "Number of column greather than template";
                    } else {
                        $addMessage .= "Some column missing, no enough of column";
                    }
                    $errorMessage .= "Row 1, header no match. ".$addMessage." <br>";
                }
                else if(count(array_diff($this->headerArray, $rowData)) > 0) {
                    $errorMessage .= "Row 1, header no same, please download template again and check with template header.<br>";
                } else if($this->headerArray != $rowData) {
                    $errorMessage .= "Row 1, header sequence no correct, please check a try again.";
                } else {
                    for ($row = 2; $row <= $highestRow; $row++){
                        $rowData = $spreadsheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE)[0];

                        if($this->isEmptyRow($rowData)) { continue; } // skip empty row
                        else {
                            array_push($dataArray,$rowData);
                            $indexOf = array_search('SKU', $this->headerArray);
                            $duplicateSKUids = Product::where(['sku'=> $rowData[$indexOf]])->pluck('product_id');
                            $duplicateNameIDs = Product::where(['name'=> $rowData[0]])->pluck('product_id');
                            $indexOf = array_search('branch_1', $this->headerArray);

                            for ($i=0; $i < 3; $i++) {

                                if ($rowData[$indexOf+$i] == null) continue;
                                else if (Branch::where('branch_id', $rowData[$indexOf+$i])->doesntExist()) {
                                    $errorMessage .= "Row ".$row." has a branch ID no exist. ID : ".$rowData[$indexOf+$i].". Please check a remove it<br>";
                                } else {
                                    if (
                                        ProductBranch::whereIn('product_id', $duplicateSKUids)->where('branch_id', $rowData[$indexOf+$i])->exists()
                                    ) {
                                        $branch = Branch::where('branch_id', $rowData[$indexOf+$i])->first();
                                        if ($branch) {
                                            $errorMessage .= "Row ".$row." has a duplicate SKU key product existed on branch ".$branch->name.". Please check a remove it<br>";
                                        } else {
                                            $errorMessage .= "Row ".$row." has a duplicate SKU key product existed on branch ID : ".$rowData[$indexOf+$i].". Please check a remove it<br>";
                                        }
                                    }
                                    if(
                                        ProductBranch::whereIn('product_id', $duplicateNameIDs)->where('branch_id', $rowData[$indexOf+$i])->exists()
                                    ) {
                                        $branch = Branch::where('branch_id', $rowData[$indexOf+$i])->first();
                                        if ($branch) {
                                            $errorMessage .= "Row ".$row." has a duplicate product existed on branch ".$branch->name.". Please check a remove it<br>";
                                        } else {
                                            $errorMessage .= "Row ".$row." has a duplicate product existed on branch ID : ".$rowData[$indexOf+$i].". Please check a remove it<br>";
                                        }
                                    }
                                }
                            }
                            $indexOf = array_search('SKU', $this->headerArray);
                            if (in_array($rowData[$indexOf], $keyList)) {
                                $errorMessage .= "Row ".$row." has duplicate SKU key in excel. Please check a remove it<br>";
                            } else {
                                array_push($keyList,$rowData[$indexOf]);
                            }
                            if (in_array($rowData[0], $nameList)) {
                                $errorMessage .= "Row ".$row." has duplicate product name in excel. Please check a remove it<br>";
                            } else {
                                array_push($nameList,$rowData[0]);
                            }
                        }
                    }
                }

                if ($errorMessage) {
                    return response()->json([
                        'status' => 422,
                        'message' => trans('api.data_error'),
                        'err_message' => $errorMessage
                    ]);
                } else {
                    return response()->json([
                        'status' => 200,
                        'message' => trans('api.success'),
                        'data' => $dataArray
                    ]);
                }

            } catch(Exception $e) {
                Log::debug('Error loading file'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                return response()->json([
                    'status' => 200,
                    'message' => trans('api.error_loading_file')
                ]);
            }
        } else {
            return response()->json(['status' => 404, 'message' => trans('api.file_no_found')]);
        }
    }
    public function productImport(Request $request) {
        $productArray = $request->products;
        foreach ($productArray as $index => $product) {
            $price_type_value = 1;
            switch (gettype($product[5])) {
                case 'string':
                    $price_type_value = (double) $product[array_search('price_type_value', $this->headerArray)];
                    break;
                case 'double':
                    $price_type_value = $product[array_search('price_type_value', $this->headerArray)];
                    break;
                default:
                    $price_type_value = 1;
                    break;
            }
            $insertData = [
                'uuid' => Helper::getUuid(),
                'name' => trim($product[0]),
                'name_2' => trim($product[1]),
                'slug' => trim($product[2]),
                'description' => trim($product[3]),
                'sku' => trim($product[4]),
                'price_type_id' => (int) $product[array_search('price_type', $this->headerArray)],
                'price_type_value' => $price_type_value,
                'price' => (double) $product[7],
                'old_price' => (double) $product[array_search('old_price', $this->headerArray)],
                'has_inventory' => (int) $product[array_search('has_inventory', $this->headerArray)],
                'has_rac_managemant' => (int) $product[array_search('beer_management', $this->headerArray)],
                'has_setmeal' => (int) $product[array_search('has_setmeal', $this->headerArray)],
                'status' => (int) $product[array_search('status', $this->headerArray)],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
            ];
            $productID = Product::create($insertData)->product_id;
            Log::debug($productID);
            $branchIds = array();
            if($product[array_search('branch_1', $this->headerArray)])
                array_push($branchIds, (int) $product[array_search('branch_1', $this->headerArray)]);
            if($product[array_search('branch_2', $this->headerArray)])
                array_push($branchIds, (int) $product[array_search('branch_2', $this->headerArray)]);
            if($product[array_search('branch_3', $this->headerArray)])
                array_push($branchIds, (int) $product[array_search('branch_3', $this->headerArray)]);



            /* Assign Product Branch */
            foreach ($branchIds as $branchIndex => $branchId) {
                $insertProBranch = [
                    'uuid' => Helper::getUuid(),
                    'product_id' => $productID,
                    'branch_id' => $branchId,
                    'warningStockLevel' => (int)$product[array_search('warning_stock_level', $this->headerArray)] ?? 0,
                    'display_order' => (int)$product[array_search('display_order', $this->headerArray)],
                    'printer_id' => $product[array_search('printer_id', $this->headerArray)],
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                ProductBranch::create($insertProBranch);
            }
            /*insert Category Data*/
            foreach ( explode(",",$product[array_search('category', $this->headerArray)]) as $index => $value) {
                foreach ($branchIds as $branchIndex => $branchId) {
                    $insertCatData = [
                        'product_id' => $productID,
                        'category_id' => (int) trim($value),
                        'branch_id' => $branchId,
                        'display_order' => (int)$product[array_search('display_order', $this->headerArray)],
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->id,
                    ];
                    ProductCategory::create($insertCatData);
                }
            }

            /*insert attribute Data*/
            for ($index=1; $index <= 10; $index++) {
                $attrName = "attribute_".$index;
                $priceName = "attribute_price_".$index;
                $attributeID = $product[array_search($attrName, $this->headerArray)];
                $attrprice = (double) $product[array_search($priceName, $this->headerArray)] ?? 0;
                $attribute = Attributes::find($attributeID);
                if ($attribute) {
                    $ca_id = $attribute->ca_id;
                    $insertAttData = [
                        'uuid' => Helper::getUuid(),
                        'ca_id' => $ca_id,
                        'attribute_id' => $attributeID,
                        'product_id' => $productID,
                        'price' => $attrprice,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->id,
                    ];
                    ProductAttribute::create($insertAttData);
                }
            }

            /*insert modifier Data*/
            for ($index=1; $index <= 5; $index++) {
                $modiName = "modifier_".$index;
                $priceName = "modifier_price_".$index;
                $modifierID = (int) $product[array_search($modiName, $this->headerArray)];
                $modiprice = (double) $product[array_search($priceName, $this->headerArray)] ?? 0;
                if ($modifierID) {
                    $insertModData = [
                        'uuid' => Helper::getUuid(),
                        'modifier_id' => $modifierID,
                        'product_id' => $productID,
                        'price' => $modiprice,
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => Auth::user()->id,
                    ];
                    ProductModifier::create($insertModData);
                }
            }

        }

        //DB::commit();
        return response()->json(['status' => 200, 'message' => trans('api.success')]);
    }
    private function isEmptyRow($row) {
        foreach($row as $cell){
            if (null !== $cell) return false;
        }
        return true;
    }
}
?>
