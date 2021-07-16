<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

use App\Models\Users;


class UsersController extends Controller
{
    private   $SERVER_API_KEY = 'AAAAt5GtBus:APA91bEO33tVbtZ5Ix30sC4vNpvdUn4E87i-aw-mLpfz5nAMxFMYOUuEEEkb5G1BVJceVkab3Zxmijoy3BFhMTen4yzCDlW-qpfmDQnp1pXCv-oWqYn7WCkTuKj0hL_D_TiGewRrqCwA';
    public function employees_payment(Request $request)
    { 
        $data = DB::table('employee')
        ->select("*")
        ->get();
        //return response()->json($data);
        if ($request->ajax()) 
        {
         return Datatables::of($data)->make(true);
        }
        return view('employees_payment',compact('data'));
    }
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

        $firebaseToken = Employee::whereNotNull('push_notification_token')->where('employee.id', '=', $employee_id )->pluck('push_notification_token')->all();
        return response()->json($firebaseToken);
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => "Employee Approval Acount",
                "body" => "Employee Approval Acount Successfully",  
            ]
        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $this->SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch); 
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
    public function un_block_employee(Request $request,$employee_id)
    { 
        $date = date('Y-m-d h:i');
        $un_block_employee = 
        DB::table('employee')
        ->where('employee.id', '=', $employee_id )
        ->update(['updated_at' => $date,'is_blocked'=>0,'is_active'=>1]);
        if(!empty($un_block_employee) )
        return response()->json(['status'=>true,'code'=>200,'message'=>'Successfully accept employee'])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order accept'])->setStatusCode(400);
    }
}