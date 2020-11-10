<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Helper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromCollection, WithHeadings
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
            'Name',
            'Email',
            'Mobile',
            'Status',
            'Created At'
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $request = $this->request;
        $defaultCondition = 'uuid != ""';

        $name = $request['name'];
        if ($name != null) {
            $name = Helper::string_sanitize($name);
            $defaultCondition .= " AND `name` LIKE '%$name%' ";
        }

        $mobile = $request['mobile'];
        if ($mobile != null) {
            $defaultCondition .= " AND `mobile` LIKE '%$mobile%' ";
        }
        $email = $request['email'];
        if ($email != null) {
            $defaultCondition .= " AND `email` LIKE '%$email%' ";
        }

        $from_date = $request['from_date'];
        $to_date = $request['to_date'];

        $from = isset($from_date) ? (date('Y-m-d', strtotime($from_date))) : null;
        $to = isset($to_date) ? (date('Y-m-d', strtotime($to_date))) : null;

        if (empty($from) && !empty($to)) {
            $defaultCondition .= " AND DATE_FORMAT(`customer`.created_at, '%Y-%m-%d') <= '" . $to . "'";
        }
        if (!empty($from) && empty($to)) {
            $defaultCondition .= " AND DATE_FORMAT(`customer`.created_at, '%Y-%m-%d') >= '" . $from . "'";
        }
        if (!empty($from) && !empty($to)) {
            $defaultCondition .= " AND DATE_FORMAT(`customer`.created_at, '%Y-%m-%d') BETWEEN '" . $from . "' AND '" . $to . "'";
        }
        $status = $request['status'];
        if ($status != null) {
            $defaultCondition .= " AND status =" . $status;
        }
        $cusList = Customer::whereRaw($defaultCondition)
            ->select(
                'customer_id', 'name', 'email', 'mobile', 'status', 'created_at')
            ->get();

        foreach ($cusList as $key => $value) {
            $cusList[$key]->customer_id = $key + 1;
            $cusList[$key]->name = $value->name;
            $cusList[$key]->email = $value->email;
            $cusList[$key]->mobile = $value->mobile;
            if ($value->status) {
                $status = trans('backend/common.active');
            } else {
                $status = trans('backend/common.inactive');
            }
            $cusList[$key]->status = $status;

            $cusList[$key]->created_at = $value->created_at;
        }

        return $cusList;
    }
}
