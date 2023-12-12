<?php
namespace App\Http\Controllers\Common;
use DB;
use Hash;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Image;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ErrorTryCatch;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
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

        $this->middleware('permission:users-list', [
            'only' => ['index', 'show'],
        ]);
        $this->middleware('permission:users-create', [
            'only' => ['create', 'store'],
        ]);
        $this->middleware('permission:users-edit', [
            'only' => ['edit', 'update'],
        ]);
        $this->middleware('permission:users-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        try {
            $User = $this->User;
            // $data = User::get();
            if ($User->user_type == 'Super Admin') {
                $data = User::with('store')->whereNot(
                    'user_type',
                    '=',
                    'Super Admin'
                )->latest();
            } else if ($User->user_type == 'Admin') {
                $data = User::with('store')->whereNot(
                    'user_type',
                    '=',
                    'Admin'
                )->wherestore_id($User->store_id)->latest();
            }else{
                $data = User::with('store')->wherecreated_by_user_id($User->id)->latest();
            }
            if ($request->ajax()) {
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($data) use ($User) {
                        $btn = '';
                        // if($User->user_type != 'Admin'){
                            if($User->can('users-edit')) {
                                $btn =
                                    '<a href=' .
                                    route(
                                        request()->segment(1) . '.users.edit',
                                        $data->id
                                    ) .
                                    ' class="btn btn-info btn-sm waves-effect" style="margin-left: 5px"><i class="fa fa-edit"></i></a>';
                            }
                        // }
                        return $btn;
                    })

                    ->addColumn('status', function ($data) {
                        if ($data->status == 0) {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" onchange="updateStatus(this,\'users\')" class="form-check-input"  value=' .
                                $data->id .
                                ' /></div>';
                        } else {
                            return '<div class="form-check form-switch"><input type="checkbox" id="flexSwitchCheckDefault" checked="" onchange="updateStatus(this,\'users\')" class="form-check-input"  value=' .
                                $data->id .
                                ' /></div>';
                        }
                    })
                    ->addColumn('image', function ($data) {
                        return '<a title="Click for View" data-lightbox="roadtrip" href="' .
                            asset($data->image) .
                            '"><img id="demo-test-gallery" class="border-radius-lg shadow demo-gallery" src="' .
                            asset($data->image) .
                            '" height="40px" width="40px"  />';
                    })
                    ->rawColumns(['image', 'action', 'status'])
                    ->make(true);
            }

            return view('backend.common.users.index');
        } catch (\Exception $e) {
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

    public function create()
    {
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        $roles = Role::pluck('name', 'name')->all();
        return view('backend.common.users.create', compact('roles','stores'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'store_id' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users,email',
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
        ]);
        // try {
            DB::beginTransaction();
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $input['store_id'] = $input['store_id'];
            $input['user_type'] = $input['roles'];
            $input['status'] = 1;
            $input['last_login'] = date('Y-m-d H:i:s');
            $user = User::create($input);
            $user->assignRole($request->input('roles'));
            DB::commit();
            Toastr::success('User Created Successfully', 'Success');
            return redirect()->route(\Request::segment(1) . '.users.index');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
        //     Toastr::error($response['message'], 'Error');
        //     return back();
        // }
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $User=$this->User;
        if ($User->user_type == 'Super Admin') {
            $stores = Store::wherestatus(1)->get();
        }else{
            $stores = Store::where('id',$User->store_id)->wherestatus(1)->get();
        }
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view(
            'backend.common.users.edit',
            compact('user', 'stores', 'roles', 'userRole')
        );
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $this->validate($request, [
            // 'store_id' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'same:confirm-password',
            'roles' => 'required',
        ]);
        // try {
            DB::beginTransaction();
            $input = $request->all();
            if (!empty($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            } else {
                $input = Arr::except($input, ['password']);
            }
            $input['user_type'] = $input['roles'];
            $input['store_id'] = $input['store_id'];
            $image = $request->file('image');
            if (isset($image)) {
                $currentDate = Carbon::now()->toDateString();
                $image_name =
                    $currentDate .
                    '-' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();
                $userImage = Image::make($image)
                    ->resize(200, 200)
                    ->save($image->getClientOriginalExtension());
                Storage::disk('public')->put(
                    'uploads/user/' . $image_name,
                    $userImage
                );
                $input['image'] = 'uploads/user/' . $image_name;
            }
            $user = User::find($id);
            $user->update($input);
            DB::table('model_has_roles')
                ->where('model_id', $id)
                ->delete();
            $user->assignRole($request->input('roles'));
            DB::commit();
            Toastr::success('User Updated Successfully', 'Success');
            return redirect()->route(\Request::segment(1) . '.users.index');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
        //     Toastr::error($response['message'], 'Error');
        //     return back();
        // }
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    public function changepassword()
    {
        return view('backend.common.users.password');
    }

    public function updatepassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:6|max:30',
            'confirm-password' => 'required|same:password',
        ]);
        try {
            DB::beginTransaction();
            if (!Hash::check($request->oldpassword, Auth::user()->password)) {
                Toastr::error('Old Password wrong', 'Error');
                return back();
            } else {
                User::find(Auth::user()->id)->update(['remember_token' => null]);
                $userinfo = User::find(Auth::user()->id)->update([
                    'password' => Hash::make($request->confirm),
                ]);
            }
            if ($userinfo) {
                Auth::logout();
                $request->session()->invalidate();
                DB::commit();
                Toastr::success('Password Update Successfully', 'Success');
                return Redirect::to('login');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ErrorTryCatch::createResponse(false,500,'Internal Server Error.',null);
            Toastr::error($response['message'], 'Error');
            return back();
        }
    }

    public function ban($id){
        $user = User::findOrFail($id);

        if($user->banned == 1) {
            $user->banned = 0;
        } else {
            $user->banned = 1;
        }
        $user->save();
        return back();
    }
}
