<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Permissions;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TerminalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('view_terminal');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $terminalList = Terminal::all();
        return view('backend.terminal.index', compact('terminalList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('add_terminal');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $branchList = Branch::where('status', 1)->get();
        return view('backend.terminal.create', compact('branchList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Terminal store : start');
        try {
            $name = $request->terminal_name;
            $terminal_device_id = $request->terminal_device_id;
            $terminal_branch_id = $request->branch_id;
            $terminal_key = $request->terminal_key;
            $terminal_type = $request->terminal_type;
            $loginId = Auth::user()->id;
            $checkExists = Terminal::where('terminal_name', $name)->count();
            if ($checkExists > 0) {
                Helper::log('Terminal store : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/terminal.terminal_exists')]);
            } else {
                $terminal_is_mother = 0;
                if ($request->terminal_is_mother) {
                    $terminal_is_mother = $request->terminal_is_mother;
                }
                $insertTerminal = [
                    'uuid' => Helper::getUuid(),
                    'terminal_device_id' => $terminal_device_id,
                    'branch_id' => $terminal_branch_id,
                    'terminal_name' => $name,
                    'terminal_key' => $terminal_key,
                    'terminal_type' => $terminal_type,
                    'terminal_is_mother' => $terminal_is_mother,
                    'status' => $request->status,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => $loginId,
                ];
                $terminalData = Terminal::create($insertTerminal);
            }
            DB::commit();
            Helper::saveLogAction('1', 'Terminal', 'Store', 'Add new Terminal ' . $terminalData->uuid, Auth::user()->id);
            Helper::log('Terminal store : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Terminal store : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Terminal', 'Store', 'Add new terminal Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_save_information')]);
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
    public function edit($id)
    {
        Languages::setBackLang();

        $checkPermission = Permissions::checkActionPermission('edit_terminal');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        $terminalData = Terminal::where('uuid', $id)->first();
        $branchList = Branch::where('status', 1)->get();
        if (empty($terminalData)) {
            Helper::log('Terminal edit : No record found');
            return redirect()->back()->with('error', trans('backend/common.oops'));
        }
        return view('backend.terminal.edit', compact('terminalData', 'branchList'));
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
        Languages::setBackLang();
        DB::beginTransaction();
        Helper::log('Terminal update : start');
        try {
            $name = $request->terminal_name;
            $terminal_id = $request->terminal_id;
            $terminal_device_id = $request->terminal_device_id;
            $terminal_branch_id = $request->branch_id;
            $terminal_key = $request->terminal_key;
            $terminal_type = $request->terminal_type;
            $loginId = Auth::user()->id;
            $checkExists = Terminal::where('terminal_name', $name)->where('terminal_id', '!=', $terminal_id)->count();
            if ($checkExists > 0) {
                Helper::log('Terminal update : is exists');
                return response()->json(['status' => 409, 'message' => trans('backend/terminal.terminal_exists')]);
            } else {
                $terminal_is_mother = 0;
                if ($request->terminal_is_mother) {
                    $terminal_is_mother = $request->terminal_is_mother;
                }
                $updateData = [
                    'terminal_device_id' => $terminal_device_id,
                    'branch_id' => $terminal_branch_id,
                    'terminal_name' => $name,
                    'terminal_key' => $terminal_key,
                    'terminal_type' => $terminal_type,
                    'terminal_is_mother' => $terminal_is_mother,
                    'status' => $request->status,
                    'updated_at' => config('constants.date_time'),
                    'updated_by' => $loginId,
                ];
                Terminal::where('uuid', $id)->update($updateData);
                DB::commit();
                Helper::saveLogAction('1', 'Terminal', 'Update', 'Update Terminal ' . $id, Auth::user()->id);

                Helper::log('Terminal update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Terminal update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Terminal', 'Update', 'Update terminal Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_update_information')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_terminal');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.terminal.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Languages::setBackLang();

        DB::beginTransaction();
        Helper::log('terminal delete : start');
        try {
            $terminalData = Terminal::where('uuid', $id)->first();
            $deleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2
            ];
            Terminal::where('uuid', $id)->update($deleteData);
            DB::commit();
            Helper::saveLogAction('1', 'Terminal', 'Destroy', 'Destroy Terminal ' . $id, Auth::user()->id);
            Helper::log('terminal delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('terminal delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Terminal', 'Destroy', 'Delete Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
