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
    public function approval_employee(Request $request,$employee_id)
    { 
        $date = date('Y-m-d h:i');
        $approval_employee = 
        DB::table('employee')
        ->where('employee.id', '=', $employee_id )
        ->update(['accepted_date' => $date,'is_active'=>1]);
        if(!empty($approval_employee) )
        return response()->json(['status'=>true,'code'=>200,'message'=>'Successfully accept employee'])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order accept'])->setStatusCode(400);
    }
    public function block_employee(Request $request,$employee_id)
    { 
        $date = date('Y-m-d h:i');
        $block_employee = 
        DB::table('employee')
        ->where('employee.id', '=', $employee_id )
        ->update(['updated_at' => $date,'is_blocked'=>1,'is_active'=>1]);
        if(!empty($block_employee) )
        return response()->json(['status'=>true,'code'=>200,'message'=>'Successfully accept employee'])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order accept'])->setStatusCode(400);
    }
}