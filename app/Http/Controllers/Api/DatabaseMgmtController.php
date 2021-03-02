<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\UserBranch;
use App\Models\Terminal;
use App\User;

class DatabaseMgmtController extends Controller
{
    /*
     * @method   : Appdata
     * @params   : datetime, branchId
     * @respose  : Json updated data secound time
     * $reference: https://phpdox.net/demo/Symfony2/classes/Doctrine_DBAL_Schema_Column.xhtml
     * Doctrine\DBAL\Schema\Column
     *      */
  /*   public function usingAsset() {

        $data = file_get_contents(public_path('storage\json\appVersion.json'), true);
        //$data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/storage/json/appVersion.json', true);
        return $data;
    }
    public function test() {
        $data = File::get('storage/app/public/json/appVersion.json', true);
        $appVersionList = json_decode($data, true);
        dd($appVersionList);
    }*/
    private function getTablesArray($appVersion) {
        $arrayTableDetail = array();
        $tableDetail;

        //dd($arrayTableDetail);
        $filterArray = array();

        //$data = file_get_contents('storage/json/appVersion.json', true);
        //$data = asset('resources/json/appVersion.json');
        $appVersionList = //json_decode($data, true);
        [
            "1.0.0+2020" => "2021/02/01",
        ];
        $getDateFromVersion = $appVersionList[array_key_last($appVersionList)];
        if (array_key_exists($appVersion, $appVersionList)) {
            $getDateFromVersion = $appVersionList[$appVersion];
        } else {
            $getDateFromVersion = "2100/01/01";
        }
        $db_database = 'staging';//env('DB_DATABASE', 'mcnpos');
        //Log::debug($db_database);
        $query =
            DB::raw(
                "SELECT `TABLE_NAME`, `CREATE_TIME`, `UPDATE_TIME` FROM information_schema.tables "
                ."WHERE `TABLE_SCHEMA` = '".$db_database."' "
                ."AND (`CREATE_TIME` >= '".$getDateFromVersion."' OR `UPDATE_TIME` >= '".$getDateFromVersion."') ORDER BY `CREATE_TIME` DESC"
            );
        //Log::debug($query);
        $arrayTableDetail = DB::select($query);
        $updateDate = date('Y-m-d', strtotime($getDateFromVersion));
        foreach ($arrayTableDetail as $key => $value) {
            $tableLastUpdateDate;
            if($value->UPDATE_TIME != null) {
                $tableLastUpdateDate = date('Y-m-d', strtotime($value->UPDATE_TIME));
            } else {
                $tableLastUpdateDate = date('Y-m-d', strtotime($value->CREATE_TIME));
            }
            Log::debug($tableLastUpdateDate.' '.$value->TABLE_NAME);
            if ($tableLastUpdateDate >= $updateDate) {
                array_push($filterArray, $value->TABLE_NAME);
            }
        }
        return $filterArray;
    }
    public function getTablesUpdate(Request $request, $locale) {
        $appVersion = $request->version;
        $excludeTable = ['migrations', 'log', 'language', 'password_reset', 'password_resets'];
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('point', 'string');
        $tableQueryArray = array();
        foreach($this->getTablesArray($appVersion) as $table) {
            if (in_array($table, $excludeTable)) {} else {
                $query = [
                    "table_name"=>$table,
                    "create" =>$this->getCreateTableQuery($table),
                    "delete" =>$this->getDeleteTableQuery($table),
                ];
                array_push($tableQueryArray, $query);
            }
        }
        //dd(response()->json($tableQueryArray));
        return response()->json(['status' => 200, 'show' => false, 'message' => trans('api.success'), 'data'=> $tableQueryArray ]);
    }
    private function getCreateTableQuery($tableName) {
        $query = 'CREATE TABLE `'.$tableName.'` (';

        $columns = DB::connection()->getDoctrineSchemaManager()->listTableColumns($tableName);
        $index = 1;
        $max = count($columns);
        //dd($columns);
        foreach($columns as $column) {
            $dataType = 'TEXT';
            switch ($column->getType()->getName()) {
                case 'string':
                case 'datetime':
                case 'text':
                    $dataType = 'TEXT';
                    break;
                case 'bigint':
                case 'integer':
                    $dataType = 'INTEGER';
                    break;
                case 'float':
                    $dataType = 'REAL';
                    break;
                case 'decimal':
                case 'boolean':
                    $dataType = 'NUMERIC';
                    break;
                default:
                    break;

            }
            $query .= ' '.$column->getName().' '.$dataType;
            if (
                $column->getUnsigned() &&
                !empty($column->getAutoincrement()) &&
                $dataType == 'INTEGER'
            ) {
                $query .= ' PRIMARY KEY AUTOINCREMENT';
            } else if ($index == 1) {
                $query .= ' PRIMARY KEY';
            }
            if(!is_null($column->getDefault()) && $dataType == 'INTEGER') {
                $query .= ' DEFAULT '. ($column->getDefault() ?? 1) ;
            }
            if ($max != $index) {
                $query .=', ';
                $index++;
            }
        }
        $query .= ')';
        return $query;
    }
    private function getDeleteTableQuery($tableName) {
        return 'DROP TABLE IF EXISTS `'.$tableName.'`';
    }
    public function getTableInsert(Request $request, $locale){
        $tableName = $request->table;
        if (empty($tableName))
        return response()->json(['status' => 200, 'show' => false, 'message' => trans('api.success'), 'data'=> [] ]);
        $terminalID = $request->terminal_id;
        $is_init = $request->is_init;
        $userPIN = $request->pin;
        $userUUID = $request->user_uuid;
        $terminal = Terminal::find($terminalID);
        $branchID = $terminal->branch_id ?? 0;
        $userIds = UserBranch::where('branch_id', $branchID)->pluck('user_id');
        $user = User::where(['user_pin'=>$userPIN, 'uuid'=> $userUUID])->exists();
        $data = DB::table($tableName);

        if (Schema::hasColumn($tableName, 'branch_id')) {
            $data = $data->where('branch_id', $branchID);
        } else if (Schema::hasColumn($tableName, 'terminal_id') && $terminal != null) {
            $data = $data->where('terminal_id', $terminalID);
        }
        switch (strtolower($tableName)) {
            case 'users':
                if ($is_init) {
                    $data = $data->get();
                } else {
                    $data = $user ? $data->whereIn('id', $userIds)->get() : [];
                }
                break;
            default:
            if (Schema::hasColumn($tableName, 'updated_at')) {
                $data = $data->orderBy('updated_at', 'desc')->get();//->limit(30)
            } else if(Schema::hasColumn($tableName, 'order_date')) {
                $data = $data->orderBy('order_date', 'desc')->get();//->limit(30)
            } else if(Schema::hasColumn($tableName, 'created_at')) {
                $data = $data->orderBy('created_at', 'desc')->get();//->limit(30)
            } else if(Schema::hasColumn($tableName, 'id')) {
                $data = $data->orderBy('id', 'desc')->get();//->limit(30)
            } else {
                $data = $data->get();
            }
                break;
        }
        return response()->json(['status' => 200, 'show' => false, 'message' => trans('api.success'), 'data'=> $data ]);

    }
}
?>
