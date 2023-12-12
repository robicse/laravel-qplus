<?php

namespace App\Http\Controllers\Common;

use File;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Helpers\ErrorTryCatch;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Image;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

class StoreController extends Controller
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

        $this->middleware('permission:stores-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:stores-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:stores-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:stores-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        try {
            $User = $this->User;
            if ($User->user_type == 'Admin') {
                $data = Store::wherecreated_by_user_id($User->id)->latest();
            } else {
                $data = Store::latest();
            }
            if ($request->ajax()) {
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function ($data) {
                        if ($data->status == 0) {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" onchange="updateStatus(this,\'stores\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        } else {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" checked="" onchange="updateStatus(this,\'stores\')" class="form-check-input"  value=' . $data->id . ' /></div>';
                        }
                    })
                    ->addColumn('action', function ($data) use ($User) {
                        $btn = '';
                        // if ($User->can('slides-edit')) {
                        $btn =
                            '<a href=' .
                            route(
                                request()->segment(1) . '.stores.edit',
                                $data->id
                            ) .
                            ' class="btn btn-info btn-sm waves-effect" style="margin-left: 5px"><i class="fa fa-edit"></i></a>';
                        // }
                        $btn .= '</span>';
                        return $btn;
                    })
                    ->addColumn('logo', function ($data) {
                        return '<img class="border-radius-lg shadow" src="' .
                            asset($data->logo) .
                            '" height="30px" width="30px"  />';
                    })
                    ->rawColumns(['logo','action', 'status'])
                    ->make(true);
            }

            return view('backend.common.store.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function create()
    {
        return view('backend.common.store.create');
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|min:1|max:290|unique:stores',
                'location' => 'required',
                'mobile' => 'required',
                'email' => 'required',
                'website' => 'required',
                'address' => 'required',
            ]
        );

         try {
            DB::beginTransaction();
            $store = new Store();
            $store->created_by_user_id = $this->User->id;
            $store->name = $request->name;
            $store->location = $request->location;
            $store->phone = $request->phone;
            $store->email = $request->email;
            $store->website = $request->website;
            $store->address = $request->address;
            $logo = $request->file('logo');
            if (isset($logo)) {
                $currentDate = Carbon::now()->toDateString();
                $logo_image_name = $currentDate . '-' . uniqid() . '.' . $logo->getClientOriginalExtension();
                // $logoImage = Image::make($logo)->resize(200, 200)->save($logo->getClientOriginalExtension());
                $logoImage = Image::make($logo)->save($logo->getClientOriginalExtension());
                Storage::disk('public')->put('uploads/store/' . $logo_image_name, $logoImage);
                $store->logo = 'uploads/store/' . $logo_image_name;
            }
            $store->save();
            DB::commit();
            Toastr::success('Store Update Successfully', 'Success');
            return redirect()->route(request()->segment(1) . '.stores.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function show(Store $store)
    {
        //
    }

    public function edit($id)
    {
        try {
            $User = $this->User;
            if ($User->user_type == 'Admin') {
                $data = Store::wherecreated_by_user_id($User->id)->findOrFail($id);
                // dd($data);
            } else {
                $data = Store::findOrFail($id);
            }
            return view('backend.common.store.edit')->with('store', $data);
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|min:1|max:290|unique:stores,' . $id,
                'location' => 'required',
                'mobile' => 'required',
                'email' => 'required',
                'website' => 'required',
                'address' => 'required',
            ]
        );

         try {
            DB::beginTransaction();
            $store = Store::find($id);
            $store->created_by_user_id = $this->User->id;
            $store->name = $request->name;
            $store->location = $request->location;
            $store->phone = $request->phone;
            $store->email = $request->email;
            $store->website = $request->website;
            $store->address = $request->address;
            $logo = $request->file('logo');
            if (isset($logo)) {
                $currentDate = Carbon::now()->toDateString();
                $logo_image_name = $currentDate . '-' . uniqid() . '.' . $logo->getClientOriginalExtension();
                // $logoImage = Image::make($logo)->resize(200, 200)->save($logo->getClientOriginalExtension());
                $logoImage = Image::make($logo)->save($logo->getClientOriginalExtension());
                Storage::disk('public')->put('uploads/store/' . $logo_image_name, $logoImage);
                $store->logo = 'uploads/store/' . $logo_image_name;
            }
            $store->save();
            DB::commit();
            Toastr::success('Store Update Successfully', 'Success');
            return redirect()->route(request()->segment(1) . '.stores.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ErrorTryCatch::createResponse(
                false,
                500,
                'Internal Server Error.',
                null
            );
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function destroy(Store $store)
    {
        //
    }
}
