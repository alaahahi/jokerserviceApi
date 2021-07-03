<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

use App\Models\Users;


class UsersController extends Controller
{
    public function employees_accept(Request $request)
    { 
        $data = DB::table('employee')
        ->select("*")
        ->get();
        //return response()->json($data);
        if ($request->ajax()) 
        {
         return Datatables::of($data)->make(true);
        }
        return view('employees_accept',compact('data'));
    }
}