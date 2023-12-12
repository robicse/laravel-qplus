<?php

namespace App\Http\Controllers\Common;

use DB;
use App\Helpers\ErrorTryCatch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use DataTables;

class UnitController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:units-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:units-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:units-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:units-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        try {
            if ($request->ajax()) {
                $unit = Unit::latest();
                return Datatables::of($unit)
                    ->addIndexColumn()
                    ->addColumn('action', function ($unit) {
                        $button = '<button type="button" id="EditBtn"  uid="' . $unit->id . '" class="btn btn-sm btn-info"';
                        $button .= $unit->name == "PCS" ? "disabled" : "";
                        $button .= '><i class="fas fa-edit"></i></button>';
                        //$button .= '&nbsp;&nbsp;';
                        //$button .= '<button type="button" id="DeleteBtn"  rid="' . $unit->id . '" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>';

                        return $button;
                    })
                    ->addColumn('status', function ($unit) {
                        if ($unit->status == 0) {
                            return '<span class="badge badge-danger"> <i class="fa fa-ban"></i> </span>';
                        } else {
                            return '<span class="badge badge-success"><i class="fa fa-check-square"></i></span>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            return view('backend.common.units.index');
        } catch (\Exception $e) {
            $response = ErrorTryCatch::createResponse(false, 500, 'Internal Server Error.', null);
            Toastr::error($response['message'], "Error");
            return back();
        }
    }

    public function create()
    {
        return view('backend.common.units.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:1|max:190|unique:units',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ]);
        } else {
            $unit = new Unit();
            $unit->name = $request->name;
            $unit->status = $request->status;
            $unit->created_by_user_id = Auth::User()->id;
            $unit->updated_by_user_id = Auth::User()->id;

            if ($unit->save()) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        }
    }

    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return view('backend.common.units.show', compact('unit'));
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return $unit;

    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:1|max:190|unique:units,name,' . $id,
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ]);
        } else {

            $unit = Unit::findOrFail($id);
            $unit->name = $request->name;
            $unit->status = $request->status;
            $unit->updated_by_user_id = Auth::User()->id;

            if ($unit->save()) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        }

    }

    public function destroy($id)
    {
        //
        $info = Unit::destroy($id);
        if ($info) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
