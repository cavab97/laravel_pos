<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Terminal;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_attendance');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $userData = Auth::user();
        if ($userData->role == 1) {
            $branchList = Branch::where('status', 1)->get()->toArray();
            $terminalList = Terminal::where('terminal_type', 3)->get()->toArray();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();
            $branchList = Branch::where('status', 1)->whereIn('branch_id', $branchIds)->get();
            $terminalList = Terminal::where('terminal_type', 3)->whereIn('branch_id', $branchIds)->get()->toArray();
        }
        return view('backend.attendance.index', compact('branchList', 'terminalList'));
    }

    /**
     * Pagination for backend attendance
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginate(Request $request)
    {
        try {
            $search = $request['sSearch'];
            $start = $request['iDisplayStart'];
            $page_length = $request['iDisplayLength'];
            $iSortCol = $request['iSortCol_0'];
            $col = 'mDataProp_' . $iSortCol;
            $order_by_field = $request->$col;
            $order_by = $request['sSortDir_0'];

            $defaultCondition = 'attendance.id != ""';
            if (!empty($search)) {
                $defaultCondition .= " AND ( `users`.name LIKE '%$search%' OR `terminal`.terminal_name LIKE '%$search%' OR `branch`.name LIKE '%$search%' ) ";
            }

            $name = $request->input('name', null);
            if ($name != null) {
                $defaultCondition .= " AND `users`.name LIKE '%$name%' ";
            }
            $branch = $request->input('branch', null);
            if ($branch != null) {
                $defaultCondition .= " AND `branch`.branch_id = $branch ";
            }
            $terminal = $request->input('terminal', null);
            if ($terminal != null) {
                $defaultCondition .= " AND `terminal`.terminal_id = $terminal ";
            }
            $in_out = $request->input('in_out', null);
            if ($in_out != null) {
                $defaultCondition .= " AND `attendance`.in_out = $in_out ";
            }

            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            $from = isset($from_date) ? (date('Y-m-d', strtotime($from_date))) : null;
            $to = isset($to_date) ? (date('Y-m-d', strtotime($to_date))) : null;

            if (empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`attendance`.in_out_datetime, '%Y-%m-%d') <= '" . $to . "'";
            }
            if (!empty($from) && empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`attendance`.in_out_datetime, '%Y-%m-%d') >= '" . $from . "'";
            }
            if (!empty($from) && !empty($to)) {
                $defaultCondition .= " AND DATE_FORMAT(`attendance`.in_out_datetime, '%Y-%m-%d') BETWEEN '" . $from . "' AND '" . $to . "'";
            }
            $userData = Auth::user();
            if ($userData->role == 1) {
                $attendanceCount = Attendance::leftjoin('users', 'users.id', '=', 'attendance.employee_id')
                    ->leftjoin('branch', 'branch.branch_id', '=', 'attendance.branch_id')
                    ->leftjoin('terminal', 'terminal.terminal_id', '=', 'attendance.terminal_id')
                    ->whereRaw($defaultCondition)
                    ->count();
                $attendanceList = Attendance::leftjoin('users', 'users.id', '=', 'attendance.employee_id')
                    ->leftjoin('branch', 'branch.branch_id', '=', 'attendance.branch_id')
                    ->leftjoin('terminal', 'terminal.terminal_id', '=', 'attendance.terminal_id')
                    ->select('users.name as employee', 'branch.name as branch', 'terminal.terminal_name as terminal', 'attendance.*')
                    ->whereRaw($defaultCondition)
                    ->orderBy($order_by_field, $order_by)
                    ->limit($page_length)
                    ->offset($start)
                    ->get();
            } else {
                $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();

                $attendanceCount = Attendance::leftjoin('users', 'users.id', '=', 'attendance.employee_id')
                    ->leftjoin('branch', 'branch.branch_id', '=', 'attendance.branch_id')
                    ->leftjoin('terminal', 'terminal.terminal_id', '=', 'attendance.terminal_id')
                    ->whereRaw($defaultCondition)
                    ->whereIn('attendance.branch_id', $branchIds)
                    ->count();
                $attendanceList = Attendance::leftjoin('users', 'users.id', '=', 'attendance.employee_id')
                    ->leftjoin('branch', 'branch.branch_id', '=', 'attendance.branch_id')
                    ->leftjoin('terminal', 'terminal.terminal_id', '=', 'attendance.terminal_id')
                    ->select('users.name as employee', 'branch.name as branch', 'terminal.terminal_name as terminal', 'attendance.*')
                    ->whereRaw($defaultCondition)
                    ->whereIn('attendance.branch_id', $branchIds)
                    ->groupBy('attendance.id')
                    ->orderBy($order_by_field, $order_by)
                    ->limit($page_length)
                    ->offset($start)
                    ->get();
            }

            return response()->json([
                "aaData" => $attendanceList,
                "iTotalDisplayRecords" => $attendanceCount,
                "iTotalRecords" => $attendanceCount,
                "sColumns" => $request->sColumns,
                "sEcho" => $request->sEcho,
            ]);
        } catch (\Exception $exception) {
            Helper::log('Attendance pagination exception');
            Helper::log($exception);
        }
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
        //
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

    public function exportData(Request $request)
    {
        $fileName = 'Attandanace_' . time() . '.xlsx';
        return Excel::download(new AttendanceExport($request->all()), $fileName);
    }
}
