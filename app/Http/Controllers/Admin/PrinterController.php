<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrinterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_printer');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $printerList = Printer::leftjoin('branch', 'branch.branch_id', 'printer.branch_id')
            ->select('printer.*', 'branch.name as branch_name')
            ->get();
        return view('backend.printer.index', compact('printerList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('Add_printer');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $branchList = Branch::where('status', 1)->get();
        return view('backend.printer.create', compact('branchList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Helper::log('Printer create : start');
        try {
            $printer_name = trim($request->printer_name);
            $branch_id = $request->branch_id;
            $printer_ip = $request->printer_ip;
            $status = $request->status;
            $printer_is_cashier = 0;
            if ($request->printer_is_cashier != '') {
                $printer_is_cashier = 1;
            }
            $checkPrinterName = Printer::where('printer_name', $printer_name)->count();

            if ($checkPrinterName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/printer.printer_name_exists')]);
            } else {

                $printerData = [
                    'uuid' => Helper::getUuid(),
                    'branch_id' => $branch_id,
                    'printer_name' => $printer_name,
                    'printer_ip' => $printer_ip,
                    'printer_is_cashier' => $printer_is_cashier,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];
                $printer = Printer::create($printerData);
                Helper::saveLogAction('1', 'Printer', 'Store', 'Add new Printer ' . $printer->uuid, Auth::user()->id);

                Helper::log('Printer create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Printer create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Printer', 'Create printer exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
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
    public function edit($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('edit_printer');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        $printerData = Printer::where('uuid', $uuid)->first();
        $branchList = Branch::where('status', 1)->get();

        return view('backend.printer.edit', compact('printerData', 'branchList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        Helper::log('Printer update : start');
        try {
            $printer_name = trim($request->printer_name);
            $branch_id = $request->branch_id;
            $printer_ip = $request->printer_ip;
            $status = $request->status;
            $printer_is_cashier = 0;
            if ($request->printer_is_cashier != '') {
                $printer_is_cashier = 1;
            }
            $checkprinterName = Printer::where('printer_name', $printer_name)->where('uuid', '!=', $uuid)->count();

            if ($checkprinterName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/printer.printer_name_exists')]);
            } else {

                $printerData = [
                    'branch_id' => $branch_id,
                    'printer_name' => $printer_name,
                    'printer_ip' => $printer_ip,
                    'printer_is_cashier' => $printer_is_cashier,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ];

                Printer::where('uuid', $uuid)->update($printerData);
                Helper::saveLogAction('1', 'Printer', 'Update', 'Update Printer' . $uuid, Auth::user()->id);
                Helper::log('Printer update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            Helper::log('Printer update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Printer', 'Update printer exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('delete_printer');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }

        return view('backend.printer.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        Languages::setBackLang();
        Helper::log('Printer delete : start');
        try {
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Printer::where('uuid', $uuid)->update($deleteData);
            Helper::saveLogAction('1', 'Printer', 'Destroy', 'Destroy Printer' . $uuid, Auth::user()->id);

            Helper::log('Printer delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            Helper::log('Printer delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Printer', 'Destroy', 'Delete printer exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
