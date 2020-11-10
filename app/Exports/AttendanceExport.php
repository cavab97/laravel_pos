<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\UserBranch;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $id;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'SN',
            'Date',
            'Employee',
            'Branch',
            'In Out',
            'Terminal'
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $request = $this->request;
        $name = $request['name'];

        $defaultCondition = 'attendance.id != ""';
        if ($name != null) {
            $defaultCondition .= " AND `users`.name LIKE '%$name%' ";
        }
        $branch = $request['branch'];

        if ($branch != null) {
            $defaultCondition .= " AND `branch`.branch_id = $branch ";
        }
        $terminal = $request['terminal'];
        if ($terminal != null) {
            $defaultCondition .= " AND `terminal`.terminal_id = $terminal ";
        }
        $in_out = $request['in_out'];
        if ($in_out != null) {
            $defaultCondition .= " AND `attendance`.in_out = $in_out ";
        }

        $from_date = $request['from_date'];
        $to_date = $request['to_date'];

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
            $attendanceList = Attendance::leftjoin('users', 'users.id', '=', 'attendance.employee_id')
                ->leftjoin('branch', 'branch.branch_id', '=', 'attendance.branch_id')
                ->leftjoin('terminal', 'terminal.terminal_id', '=', 'attendance.terminal_id')
                ->select('attendance.id', 'attendance.in_out_datetime', 'users.name as employee', 'branch.name as branch', 'attendance.in_out', 'terminal.terminal_name as terminal')
                ->whereRaw($defaultCondition)
                ->get();
        } else {
            $branchIds = UserBranch::where('user_id', $userData->id)->select("branch_id")->get();

            $attendanceList = Attendance::leftjoin('users', 'users.id', '=', 'attendance.employee_id')
                ->leftjoin('branch', 'branch.branch_id', '=', 'attendance.branch_id')
                ->leftjoin('terminal', 'terminal.terminal_id', '=', 'attendance.terminal_id')
                ->select('attendance.id', 'attendance.in_out_datetime', 'users.name as employee', 'branch.name as branch', 'attendance.in_out', 'terminal.terminal_name as terminal')
                ->whereRaw($defaultCondition)
                ->whereIn('attendance.branch_id', $branchIds)
                ->groupBy('attendance.id')
                ->get();
        }
        foreach ($attendanceList as $key => $value) {
            $attendanceList[$key]->id = $key + 1;
            $attendanceList[$key]->in_out_datetime = $value->in_out_datetime;
            $attendanceList[$key]->employee = $value->employee;
            $attendanceList[$key]->branch = $value->branch;
            if ($value->in_out) {
                $in_out = trans('backend/attendance.in');
            } else {
                $in_out = trans('backend/attendance.out');
            }
            $attendanceList[$key]->in_out = $in_out;
            $attendanceList[$key]->terminal = $value->terminal;
        }

        return $attendanceList;
    }
}
