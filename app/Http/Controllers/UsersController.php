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
        ->join('order', 'order.employee_id', '=', 'employee.id')
        ->select("*")
        ->get();
        //return response()->json($data);
        if ($request->ajax()) 
        {
         return Datatables::of($data)->addColumn('action', function ($data) {
            return '<a  href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" class="btn btn-sm btn-primary pull-right edit"><i class="voyager-wallet"></i>Pay</a>';
    })
        ->rawColumns(['action'])->make(true);
        }
        return view('employees_payment',compact('data'));
    }
    public function employees_accept(Request $request)
    { 
        $data = Employee::all();
        foreach ($data as $employee ){
            $employee->setAttribute('experience',
             DB::table('sub_category')
            ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
            ->whereIn('sub_category.id',array_filter( explode(",", str_replace("'", "", $employee->experience) )) )
            ->where('sub_category_translation.lang', '=','en')
            ->get()
         );}
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
        $title="Employee Approval Account";
        $body="Employee Approval Account Successfully";
        $approval_employee = 
        DB::table('employee')
        ->where('employee.id', '=', $employee_id )
        ->update(['accepted_date' => $date,'is_active'=>1]);
        $firebaseToken = Employee::whereNotNull('push_notification_token')->where('employee.id', '=', $employee_id )->pluck('push_notification_token')->all();
        //return response()->json($firebaseToken);
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "body" => $body,  
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
       
        curl_exec($ch); 
        if(!empty($approval_employee) )
        {
        DB::table('notification')->insert(array('employee_id'=>$employee_id,'title' =>  $title,'body'=>$body,'created_at'=> $date,'time'=>$date));
        return response()->json(['status'=>true,'code'=>200,'message'=>'Successfully accept employee'])->setStatusCode(200);
        }
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