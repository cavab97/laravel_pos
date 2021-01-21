<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assets;
use App\Models\Helper;
use App\Models\Languages;
use App\Models\Payment;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Languages::setBackLang();
        $paymentTypeList = Payment::whereNotIn('status',[2])->get();
        if(!empty($paymentTypeList)){
            foreach ($paymentTypeList as $key => $value)
            {
                $paymentBreadcrubs = Payment::getPaymentTreeIDsLibrary($value->payment_id);
                $html = "";
                for ($i = 0; $i < count($paymentBreadcrubs); $i++) {
                    if ($paymentBreadcrubs[$i]['payment_id'] != $value->payment_id) {
                        $html .= $paymentBreadcrubs[$i]['name']." > ";
                    } else {
                        $html .= $paymentBreadcrubs[$i]['name'];
                    }
                }
                $paymentTypeList[$key]['name'] = $html;

                $paymentImage = Assets::where('asset_type', 3)->where('asset_type_id', $value->payment_id)->orderBy('asset_id','DESC')->first();
                if(!empty($paymentImage)) {
                    $paymentTypeList[$key]['payment_icon'] = $paymentImage->asset_path;
                } else {
                    $paymentTypeList[$key]['payment_icon'] = config('constants.default_product');
                }
            }
        }
        return view('backend.payment.index', compact('paymentTypeList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Languages::setBackLang();
        $parentPaymentList = Payment::where(['is_parent' => 0, 'status' => Payment::ACTIVE])->get()->toArray();
        return view('backend.payment.create', compact('parentPaymentList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        Helper::log('Payment type create : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $is_parent = 0;
            if ($request->is_parent) {
                $is_parent = $request->is_parent;
            }
            $checkName = Payment::where('name', $name)->where('status',1)->count();
            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/payment.name_exists')]);
            } else {
                $paymentType = Payment::create([
                    'uuid' => Helper::getUuid(),
                    'slug' => Helper::slugify(trim($name)),
                    'name' => $name,
                    'is_parent' => $is_parent,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ]);
                $paymentId = $paymentType->payment_id;

                /*insert Image Data*/
                if ($file = $request->file('payment_icon')) {
                    $folder = $this->createDirectory('payment_icon');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move($folder, $fileName);
                    chmod($folder . $fileName, 0777);
                    $image = 'uploads/payment_icon/' . $fileName;
                    $imageData = [
                        'uuid' => Helper::getUuid(),
                        'asset_type' => 3,
                        'asset_type_id' => $paymentId,
                        'asset_path' => $image,
                        'updated_at' => config('constants.date_time'),
                        'updated_by' => Auth::user()->id
                    ];
                    Assets::create($imageData);

                }

                DB::commit();
                Helper::saveLogAction('1', 'Payment-type', 'Store', 'Add new Payment type ' . $paymentType->uuid, Auth::user()->id);

                Helper::log('Payment type create : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.save_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Payment type create : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Payment type', 'Store Payment type exception :' . $exception->getMessage(), Auth::user()->id);

            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        Languages::setBackLang();
        $paymentTypeData = Payment::where('uuid', $uuid)->first();
        $paymentId = $paymentTypeData->payment_id;
        $parentPaymentList = Payment::where(['is_parent' => 0, 'status' => Payment::ACTIVE])->where('uuid', '!=', $uuid)->get()->toArray();
        $paymentImage = Assets::where('asset_type', 3)->where('asset_type_id', $paymentId)->orderBy('asset_id','DESC')->first();
        if(!empty($paymentImage)) {
            $paymentTypeData->payment_icon = $paymentImage->asset_path;
        } else {
            $paymentTypeData->payment_icon = '';
        }
        return view('backend.payment.edit', compact('paymentTypeData','parentPaymentList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        DB::beginTransaction();
        Helper::log('Payment type update : start');
        try {
            $name = trim($request->name);
            $status = $request->status;
            $is_parent = 0;
            if ($request->is_parent) {
                $is_parent = $request->is_parent;
            }
            $checkName = Payment::where('name', $name)->where('uuid', '!=', $uuid)->where('status',1)->count();
            if ($checkName > 0) {
                return response()->json(['status' => 409, 'message' => trans('backend/payment.name_exists')]);
            } else {

                $paymentTypedata = Payment::where('uuid',$uuid)->first();
                $paymentId = $paymentTypedata->payment_id;

                Payment::where('uuid', $uuid)->update(
                    [
                        'slug' => Helper::slugify(trim($name)),
                        'name' => $name,
                        'is_parent' => $is_parent,
                        'status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                /*insert Image Data*/
                if ($file = $request->file('payment_icon')) {
                    $folder = $this->createDirectory('payment_icon');
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '.' . $extension;
                    $file->move($folder, $fileName);
                    chmod($folder . $fileName, 0777);
                    $image = 'uploads/payment_icon/' . $fileName;
                    $imageData = [
                        'uuid' => Helper::getUuid(),
                        'asset_type' => 3,
                        'asset_type_id' => $paymentId,
                        'asset_path' => $image,
                        'updated_at' => config('constants.date_time'),
                        'updated_by' => Auth::user()->id
                    ];
                    Assets::create($imageData);

                }

                DB::commit();
                Helper::saveLogAction('1', 'Payment-type', 'Update', 'Update Payment type ' . $uuid, Auth::user()->id);
                Helper::log('Payment type update : finish');
                return response()->json(['status' => 200, 'message' => trans('backend/common.update_information')]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('Payment type update : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'Payment type', 'Create Payment type exception :' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.oops')]);
        }
    }

    public function delete($uuid)
    {
        Languages::setBackLang();
        $checkPermission = Permissions::checkActionPermission('delete_payment_type');
        if ($checkPermission == false) {
            return view('backend.access-denied');
        }
        return view('backend.payment.delete', compact('uuid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Languages::setBackLang();

        DB::beginTransaction();
        Helper::log('PaymentType delete : start');
        try {
            Payment::where('uuid', $id)->first();
            $deleteData = [
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'status' => 2

            ];
            Payment::where('uuid', $id)->update($deleteData);
            DB::commit();
            Helper::saveLogAction('1', 'Payment-type', 'Destroy', 'Destroy Payment type' . $id, Auth::user()->id);
            Helper::log('PaymentType delete : finish');
            return response()->json(['status' => 200, 'message' => trans('backend/common.delete_information')]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Helper::log('PaymentType delete : exception');
            Helper::log($exception);
            Helper::saveLogAction('1', 'PaymentType', 'Destroy', 'Delete Exception ' . $exception->getMessage(), Auth::user()->id);
            return response()->json(['status' => 500, 'message' => trans('backend/common.not_delete_information')]);
        }
    }
}
