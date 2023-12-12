<?php

namespace App\Http\Controllers\Common;


use DataTables;
use App\Models\User;
use App\Models\Store;
use App\Models\PaymentReceipt;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Helpers\ErrorTryCatch;
use App\Imports\SupplierImport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Stevebauman\Location\Facades\Location;

class CustomerController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->User = Auth::user();
            if ($this->User->status == 0) {
                $request->session()->flush();
                return redirect('login');
            }
            return $next($request);
        });
        $this->middleware('permission:customers-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:customers-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:customers-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customers-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // $ip = '162.159.24.227'; /* Static IP address */
        // $currentUserInfo = Location::get($ip);
        // dd($currentUserInfo);

        $User=$this->User;
        try {
            if ($request->ajax()) {
                $customers = Customer::latest();
                return Datatables::of($customers)
                    ->addIndexColumn()
                    ->addColumn('status', function ($data) {
                        if ($data->status == 0) {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" onchange="updateStatus(this,\'customers\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        } else {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" checked="" onchange="updateStatus(this,\'customers\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        }
                    })
                    ->addColumn('action', function ($data) use($User) {
                        $btn='';
                        //$btn = '<span  class="d-inline-flex"><a href=' . route(\Request::segment(1) . '.customers.show', $data->id) . ' class="btn btn-warning btn-sm waves-effect"><i class="fa fa-eye"></i></a>';
                        if($User->can('customers-edit')){
                        $btn .= '<a href=' . route(\Request::segment(1) . '.customers.edit', $data->id) . ' class="btn btn-info waves-effect btn-sm float-left" style="margin-left: 5px"><i class="fa fa-edit"></i></a>';
                        }
                        $btn .= '</span>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }

            return view('backend.common.customers.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function create()
    {
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        return view('backend.common.customers.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required|unique:users',
            'address' => 'required'
        ]);
        // dd($request->all());

        // Operation Log initialize
        $activity_id = NULL;
        $status = 'Failed';
        $current_data = [];
        foreach($request->all() as $column => $value){
            $nested_data[$column] = $value;
        }
        array_push($current_data,$nested_data);
        $store_id = $request->store_id ? $request->store_id : 1;

        try {
            DB::beginTransaction();
            $customer = new Customer();
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->start_date = $request->start_date;
            $customer->opening_balance = $request->opening_balance;
            $customer->address = $request->address;
            $customer->created_by_user_id = $this->User->id;
            $customer->save();
            $insert_id = $customer->id;
            if($insert_id){
                if($request->opening_balance > 0){
                    $get_invoice_no = PaymentReceipt::orderBy('id', 'desc')->pluck('invoice_no')->first();
                    if ($get_invoice_no) {
                        $invoice_no = (int) $get_invoice_no + 1;
                    } else {
                        $invoice_no = 1;
                    }

                    $payment_receipt = new PaymentReceipt();
                    $payment_receipt->invoice_no = $invoice_no;
                    $payment_receipt->date = $request->start_date;
                    $payment_receipt->store_id = $request->store_id ? $request->store_id : 1;
                    $payment_receipt->order_type = 'Previous Due';
                    $payment_receipt->order_type_id = 2;
                    $payment_receipt->customer_id = $customer->id;
                    $payment_receipt->amount = $request->opening_balance;
                    $payment_receipt->comments = '';
                    $payment_receipt->receipt_time = 'Customer';
                    $payment_receipt->created_by_user_id = Auth::User()->id;
                    $payment_receipt->save();
                }
                // Operation Log Success
                $activity_id = $customer->id;
                $status = 'Success';
            }

            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Customer','Create',NULL,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success('Store Update Successfully', 'Success');
            return redirect()->route(request()->segment(1) . '.customers.index');
        }catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Error
            Helper::operationLog($store_id, 'Customer','Create',NULL,$current_data,'Error',NULL,$e->getMessage());
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        return view('backend.common.customers.edit', compact('customer','stores'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required|unique:users,phone,' . $id,
            'address' => 'required'
        ]);

        // Operation Log Initialize
        $activity_id = $id;
        $status = 'Failed';
        $previous_data = [];
        $data = Customer::findOrFail($id);
        $decode_data = json_decode($data->toJson());
        array_push($previous_data,$decode_data);

        $current_data = [];
        foreach($request->all() as $column => $value){
            $nested_data[$column] = $value;
        }
        array_push($current_data,$nested_data);
        $store_id = $request->store_id ? $request->store_id : 1;

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($id);
            // $exists_previous_due = $customer->opening_balance;
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->opening_balance = $request->opening_balance;
            $customer->start_date = $request->start_date;
            $customer->updated_by_user_id = Auth::User()->id;
            $update_customer=$customer->save();
            if($update_customer){
                $exists_previous_due = PaymentReceipt::wherecustomer_id($customer->id)->whereorder_type('Previous Due')->first();
                if(empty($exists_previous_due) && $request->opening_balance > 0){
                    $payment_receipt = new PaymentReceipt();
                    $payment_receipt->date = $request->start_date;
                    $payment_receipt->store_id = $request->store_id ? $request->store_id : 1;
                    $payment_receipt->order_type = 'Previous Due';
                    $payment_receipt->order_type_id = 2;
                    $payment_receipt->customer_id = $customer->id;
                    $payment_receipt->amount = $request->opening_balance;
                    $payment_receipt->comments = '';
                    $payment_receipt->receipt_time = 'Customer';
                    $payment_receipt->created_by_user_id = Auth::User()->id;
                    $payment_receipt->save();
                }elseif(!empty($exists_previous_due) && $request->opening_balance > 0){
                    $exists_previous_due->date = $request->start_date;
                    $exists_previous_due->store_id = $request->store_id ? $request->store_id : 1;
                    $exists_previous_due->amount = $request->opening_balance;
                    $exists_previous_due->updated_by_user_id = Auth::User()->id;
                    $exists_previous_due->save();
                }else{

                }
                // Operation Log Success
                $status = 'Success';
            }

            // Operation Log Success/Failed
            Helper::operationLog($store_id, 'Customer','Update',$previous_data,$current_data,$status,$activity_id,NULL);

            DB::commit();
            Toastr::success("Customer Updated Successfully", "Success");
            return redirect()->route(\Request::segment(1) . '.customers.index');
        } catch (\Exception $e) {
            DB::rollBack();

            // Operation Log Failed
            Helper::operationLog($store_id, 'Customer','Update',$previous_data,$current_data,'Error',NULL,$e->getMessage());

            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function destroy($id)
    {
        //
    }

    public function supplierExcelStore(Request $request){
        Excel::import(new SupplierImport, $request->file('customer'));

        Toastr::success("Customer Created", "Success");
        return redirect()->back();
    }
}
