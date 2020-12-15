<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Logs;
use App\Models\Permissions;
use App\Models\TerminalLog;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_logs');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.logs.index');
    }

    /**
     * Pagination for backend logs
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginate(Request $request)
    {
        $inputs = $request->all();
        //dd();
        $search = $inputs['search']['value'];
        $start = $inputs['start'];
        $limit = $inputs['length'];
        $logsList = [];
        $totallogs = 0;
        try {
            $where = 'type != ""';
            if ($search != '') {
                $search = Helper::string_sanitize($search);
                $where .= " AND (file_name LIKE '%$search%' OR function LIKE '%$search%' OR ip_address LIKE '%$search%')";
            }
            $totallogs = Logs::whereRaw($where)->count();
            $logsList = Logs::whereRaw($where)
                ->orderBy('log_id', 'DESC')
                ->limit($limit)->offset($start)
                ->get()->toArray();

            foreach ($logsList as $key => $value) {
                $logsList[$key]['index'] = ++$start;
            }
        } catch (\Exception $exception) {
            Helper::log('Logs pagination exception');
            Helper::log($exception);
            $logsList = [];
            $totallogs = 0;
        }
        $data = [
            "aaData" => $logsList,
            "iTotalDisplayRecords" => $totallogs,
            "iTotalRecords" => $totallogs,
            "sEcho" => $inputs['draw'],
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_logs');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $logsData = Logs::where('log_id', $id)->first();

        return view('backend.logs.view', compact('logsData'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function posLogsIndex()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_logs');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.pos-logs.index');
    }

    public function logPospaginate(Request $request)
    {
        $inputs = $request->all();
        //dd();
        $search = $inputs['search']['value'];
        $start = $inputs['start'];
        $limit = $inputs['length'];
        $logsList = [];
        $totallogs = 0;
        try {
            $where = 'terminal_log.uuid != ""';
            if ($search != '') {
                $search = Helper::string_sanitize($search);
                $where .= " AND (module_name LIKE '%$search%' OR description LIKE '%$search%' OR table_name LIKE '%$search%')";
            }
            $totallogs = TerminalLog::whereRaw($where)->count();
            $logsList = TerminalLog::leftjoin('terminal','terminal.terminal_id','terminal_log.terminal_id')
                ->leftjoin('branch','branch.branch_id','terminal_log.branch_id')
                ->whereRaw($where)
                ->select('terminal_log.*','terminal.terminal_name','branch.name AS branch_name')
                ->orderBy('id', 'DESC')
                ->limit($limit)->offset($start)
                ->get()->toArray();

            foreach ($logsList as $key => $value) {
                $logsList[$key]['index'] = ++$start;
            }
        } catch (\Exception $exception) {
            Helper::log('Logs pagination exception');
            Helper::log($exception);
            $logsList = [];
            $totallogs = 0;
        }
        $data = [
            "aaData" => $logsList,
            "iTotalDisplayRecords" => $totallogs,
            "iTotalRecords" => $totallogs,
            "sEcho" => $inputs['draw'],
        ];
        return response()->json($data);
    }
}
