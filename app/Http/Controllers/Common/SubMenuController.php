<?php

namespace App\Http\Controllers\Common;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Helpers\ErrorTryCatch;
use App\Http\Controllers\Controller;

class SubMenuController extends Controller
{
        public function subMenuList($id)
        {
                $Modules = Module::find($id)->name;
                return view('backend.common.sub_menu.index', compact('Modules'));
        }
}
