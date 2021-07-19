<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use PDF;

class CustomerController extends Controller
{
    private   $SERVER_API_KEY = 'AAAAt5GtBus:APA91bEO33tVbtZ5Ix30sC4vNpvdUn4E87i-aw-mLpfz5nAMxFMYOUuEEEkb5G1BVJceVkab3Zxmijoy3BFhMTen4yzCDlW-qpfmDQnp1pXCv-oWqYn7WCkTuKj0hL_D_TiGewRrqCwA';
    public function categories(Request $request ,$lang)
    { 
        $category = 
        DB::table('category')
        ->join('category_translation', 'category_translation.categoryId', '=', 'category.id')
        ->where('category.visible', '=', '1' )
        ->where('category_translation.lang', '=', $lang )
        ->select('*')
        ->get();
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => $category,])->setStatusCode(200);
        
    }
    public function categories_sub_categories(Request $request ,$categoryId ,$lang)
    { 
        $categories_sub_categories = 
        DB::table('category')
        ->join('sub_category', 'sub_category.category_id', '=', 'category.id')
        ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
        ->where('sub_category.visible', '=', '1' )
        ->where('category.id', '=', $categoryId )
        ->where('sub_category_translation.lang', '=', $lang )
        ->select('*')
        ->get();
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => $categories_sub_categories,])->setStatusCode(200);
        
    }
    public function sub_categories_employee(Request $request ,$subCategoriesId,$lang='en')
    { 
        $user_info = DB::table('sub_category')
        ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
        ->where('sub_category.id', '=',$subCategoriesId )
        ->where('sub_category_translation.lang', '=',$lang)
        ->select('*','sub_category.id')
        ->get();
        $categories = DB::table('sub_category')
        ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
        ->where('sub_category.id', '=', $subCategoriesId )
        ->where('sub_category_translation.lang', '=',$lang)
        ->select('sub_category.category_id')->groupBy('category_id')
        ->get();
        $sub_categories_employee = Employee::Where('experience', 'like', '%' ."'".$subCategoriesId."'". '%')->get();
            foreach ($sub_categories_employee as $employee ){
            $employee->setAttribute('experience',$user_info );
            $employee->setAttribute('categories',$categories );
            }
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => $sub_categories_employee,])->setStatusCode(200);
    }
    public function user_info($moblie)
    { 
        $userId = DB::table('users')
        ->where('users.phone', '=', $moblie )->select('id')->first();
        if(!empty($userId))
        {
            $user_info = DB::table('users')
            ->where('users.id', '=', $userId->id )
            ->select('*')->first();
            return response()->json($user_info);
        }
        
    }
    public function edit_user_info(Request $request ,$moblie)
    { 
        $name=$request->name;
        $userId = DB::table('users')
        ->where('users.phone', '=', $moblie )->select('id')->first();
        if(!empty($userId))
        {
            DB::table('users')->where('id',$userId->id)->update(['name' => $name]);
            return response()->json('Update user info');
        }
        
    }
    public function add_client_info(Request $request,$moblie)
    { 
        $date = date('Y-m-d h:i');
        $clientId = DB::table('client')
        ->where('client.phone', '=', $moblie )->select('id')->first();
        $employeeId = DB::table('employee')
        ->where('employee.phone', '=', $moblie )->select('id')->first();
        if(!empty($employeeId))
        {
            return response()->json(['status'=>false,'code'=>400,'message'=>'This Mobile Number is Used'])->setStatusCode(400);
        }
        if(!empty($clientId))
        {
            $client = DB::table('client')
            ->where('client.id', '=', $clientId->id )->first();
            (!empty($request->name)) ?  $name = $request->name : $name = $client->name;
            (!empty($request->push_notification_token)) ?  $push_notification_token = $request->push_notification_token : $push_notification_token = $client->push_notification_token;
            DB::table('client')
            ->where('client.id', '=', $clientId->id )
            ->update(['name' =>  $name,'updated_at'=> $date,'push_notification_token'=>$push_notification_token]);
            return response()->json(['status'=>true,'code'=>200,'message'=>'Update client info'])->setStatusCode(201);
        }else
        DB::table('client')->insert(array('phone' => $moblie,'name'=>$request->name,'created_at'=> $date,'push_notification_token'=>$request->push_notification_token));
        return response()->json(['status'=>true,'code'=>200,'message'=>'Added user info'])->setStatusCode(201);
        
    }
    public function add_employee_info(Request $request,$moblie)
    { 
        $clientId = DB::table('client')
        ->where('client.phone', '=', $moblie )->select('id')->first();
        if(!empty($clientId))
        {
            return response()->json(['status'=>false,'code'=>400,'message'=>'This Mobile Number is Used'])->setStatusCode(201);
        }
        $imageName ="default.png";
        $date = date('Y-m-d h:i');
        $monthName = date('F');
        $year = date('Y');
        $employeeId = DB::table('employee')
        ->where('employee.phone', '=', $moblie )->select('id')->first();
        $request->validate([
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);
          if ($request->file('file')) {
            $imagePath = $request->file('file');
            $imageName = time().'.'.$request->file->extension();
            //$imageName = $imagePath->getClientOriginalName();
            $path = $request->file('file')->storeAs('employee/'.$monthName.$year, $imageName, 'public');
        }
        if(!empty($employeeId))
        {
            $employee = DB::table('employee')
            ->where('employee.id', '=', $employeeId->id )->first();
            (!empty($request->name)) ?  $name = $request->name : $name = $employee->name;
            (!empty($request->birthdate)) ?  $birthdate = $request->birthdate : $birthdate = $employee->birthdate;
            (!empty($request->sex)) ?  $sex = $request->sex : $sex = $employee->sex;
            (!empty($request->experience)) ?  $experience = $request->experience : $experience = $employee->experience;
            (!empty($request->years_experience)) ?  $years_experience = $request->years_experience : $years_experience = $employee->years_experience;
            (!empty($request->languages)) ?  $languages = $request->languages : $languages = $employee->languages;
            (!empty($request->id_number)) ?  $id_number = $request->id_number : $id_number = $employee->id_number;
            (!empty($request->name_ar)) ?  $name_ar = $request->name_ar : $name_ar = $employee->name_ar;
            (!empty($request->push_notification_token)) ?  $push_notification_token = $request->push_notification_token : $push_notification_token = $employee->push_notification_token;
            //return response()->json( $name );
            $user_info = DB::table('employee')
            ->where('employee.id', '=', $employeeId->id )
            ->update(['name' => $name,'birthdate'=>$birthdate,'sex'=>$sex,'experience'=>$experience 
            ,'photo'=>'employee/'.$monthName.$year.'/'.$imageName,'years_experience'=>$years_experience,'languages'=>$languages ,'id_number'=>$id_number
            ,'name_ar'=>$name_ar,'push_notification_token'=>$push_notification_token
        ]);
            return response()->json(['status'=>true,'code'=>200,'message'=>'Update employee info'])->setStatusCode(201);    
        }
        else
            DB::table('employee')->insertGetId(array('phone' => $moblie,'name'=>$request->name,'birthdate'=>$request->birthdate
            ,'sex'=>$request->sex,'years_experience'=>$request->years_experience,'languages'=>$request->languages
            ,'photo'=>'employee/'.$monthName.$year.'/'.$imageName,'experience'=>$request->experience,'id_number'=>$request->id_number,
            'name_ar'=>$request->name_ar,'push_notification_token'=>$request->push_notification_token
            ));
            return response()->json(['status'=>true,'code'=>200,'message'=>'Added employee info'])->setStatusCode(201);    
    }

    public function employee_info(Request $request,$moblie,$lang='en')
    { 
        $employeeId = DB::table('employee')
        ->where('employee.phone', '=', $moblie )->select('id')->first();
        $clientId = DB::table('client')
        ->where('client.phone', '=', $moblie )->select('id')->first();
        if(!empty($clientId))
        {
            $user_info = DB::table('client')
            ->where('client.id', '=', $clientId->id )
            ->first();
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully client','data' => $user_info,])->setStatusCode(200);
        }
        if(!empty($employeeId))
        {
            $user_exper = DB::table('employee')
            ->where('employee.id', '=', $employeeId->id )
            ->first()->experience;
            $array = array_filter( explode(",", str_replace("'", "", $user_exper) ));
            $user_info = DB::table('sub_category')
            ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
            ->whereIn('sub_category.id', $array )
            ->where('sub_category_translation.lang', '=',$lang)
            ->get();
            $categories = DB::table('sub_category')
            ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
            ->whereIn('sub_category.id', $array )
            ->where('sub_category_translation.lang', '=',$lang)
            ->select('sub_category.category_id')->groupBy('category_id')
            ->get();
            //return response()->json(json_decode(json_encode($categories), true));
            $employees = Employee::Where('id',  $employeeId->id)->get();
            foreach ($employees as $employee ){
            $employee->setAttribute('experience',$user_info );
            $employee->setAttribute('categories',$categories );
            }

            if($employees[0]->is_active == 0){
                return response()->json(['status'=>true,'code'=>200,'message'=>'successfully employee is pending','data' => $employees->first(),])->setStatusCode(200);
            }
            if($employees[0]->is_active == 1)
            return response()->json(['status'=>true,'code'=>200,'message'=>'successfully employee  is actiive','data' => $employees->first(),])->setStatusCode(200);
        }else
        return response()->json(['status'=>false,'code'=>400,'message'=>'User Not Found'])->setStatusCode(200);    
    }
    public function add_order(Request $request,$clientId,$sub_categories_id,$employeeId)
    { 
        $title="Order Added";
        $body="The Order Are In Status Pendding Successfully";
        $imageName ="default.png";
        $date = date('Y-m-d h:i');
        $monthName = date('F');
        $year = date('Y');
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);

          if ($request->file('image')) {
            $imagePath = $request->file('image');
            $imageName = time().'.'.$request->image->extension();
            //$imageName = $imagePath->getClientOriginalName();
            $path = $request->file('image')->storeAs('order/'.$monthName.$year, $imageName, 'public');
        }
        if(!empty($employeeId) && !empty($clientId))
        {
            $firebaseToken = Employee::whereNotNull('push_notification_token')->where('employee.id', '=', $employeeId )->pluck('push_notification_token')->all();
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

            DB::table('notification')->insert(array('employee_id'=>$employeeId,'title' =>  $title,'body'=>$body,'created_at'=> $date,'time'=>$date));

            DB::table('order')->insertGetId(array('client_id' =>$clientId,'employee_id'=>  $employeeId,'subcategory_id'=>$sub_categories_id
            ,'image'=>'order/'.$monthName.$year.'/'.$imageName,'location_lng'=>$request->location_lng,'location_lat'=>$request->location_lat
            ,'date'=>$request->date,'details'=>$request->details,'created_at'=> $date
            ));
            return response()->json(['status'=>true,'code'=>200,'message'=>'Added order info'])->setStatusCode(201);        
        }
            return response()->json(['status'=>false,'code'=>400,'message'=>'Not Found'])->setStatusCode(400);    
    }
    public function get_order_client(Request $request ,$clientId ,$lang)
    { 
        $order_client = 
        DB::table('order')
        ->join('sub_category', 'sub_category.id', '=', 'order.subcategory_id')
        ->join('client', 'client.id', '=', 'order.client_id')
        ->join('employee', 'employee.id', '=', 'order.employee_id')
        ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
        ->where('order.client_id', '=', $clientId )
        ->where('sub_category_translation.lang', '=', $lang )
        ->select('*','order.id')
        ->get();
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => $order_client,])->setStatusCode(200);
    }
    public function get_order_employee(Request $request ,$employeeId,$status ,$lang)
    { 
        $order_employee = 
        DB::table('order')
        ->join('sub_category', 'sub_category.id', '=', 'order.subcategory_id')
        ->join('client', 'client.id', '=', 'order.client_id')
        ->join('employee', 'employee.id', '=', 'order.employee_id')
        ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
        ->where('order.employee_id', '=', $employeeId )
        ->where('order.status', '=', $status)
        ->where('sub_category_translation.lang', '=', $lang )
        ->select('*','order.id')
        ->get();
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => $order_employee,])->setStatusCode(200);
    }
    public function employee_order_accept(Request $request ,$orderId)
    { 
        $title="Order Accept";
        $body="The Order Are In Status Accept Successfully";
        
        $date = date('Y-m-d h:i');
        $employee_order_accept = 
        DB::table('order')
        ->where('order.id', '=', $orderId )
        ->update(['status' => 1,'accepted_date' => $date,'note'=> $request->note]);
        $client_id=DB::table('order')
        ->where('order.id', '=', $orderId )->first()->client_id;
        if(!empty($employee_order_accept) ){
        $firebaseToken = Client::whereNotNull('push_notification_token')->where('client.id', '=', $client_id )->pluck('push_notification_token')->all();
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
        DB::table('notification')->insert(array('client_id'=>$client_id,'title' =>  $title,'body'=>$body,'created_at'=> $date,'time'=>$date));

        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully accept order'])->setStatusCode(200);
        }
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order accept'])->setStatusCode(400);
    }
    public function client_order_finish(Request $request ,$orderId)
    { 
        $title="Order Finish";
        $body="The Order Are In Status Finish Successfully";
        $date = date('Y-m-d h:i');
        $client_order_accept = 
        DB::table('order')
        ->where('order.id', '=', $orderId )
        ->update(['status' => 3,'finish_date' => $date,'rate'=> $request->rate]);
        $employee_id=DB::table('order')
        ->where('order.id', '=', $orderId )->first()->employee_id;
        if(!empty($client_order_accept) )
        {
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

            DB::table('notification')->insert(array('employee_id'=>$employee_id,'title' =>  $title,'body'=>$body,'created_at'=> $date,'time'=>$date));
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully finish order'])->setStatusCode(200);
        }
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order finish'])->setStatusCode(400);
    }
    public function employee_order_reject(Request $request ,$orderId)
    { 
        $title="Order Reject";
        $body="Sorrt,The Order Are In Status Reject";
        $date = date('Y-m-d h:i');
        $employee_order_reject = 
        DB::table('order')
        ->where('order.id', '=', $orderId )
        ->update(['status' => 2,'reject_date' => $date,'note'=> $request->note]);
        //return response()->json( $request->reject_note);
        $client_id=DB::table('order')
        ->where('order.id', '=', $orderId )->first()->client_id;
        if(!empty($employee_order_reject) )
        {
            $firebaseToken = Client::whereNotNull('push_notification_token')->where('client.id', '=', $client_id )->pluck('push_notification_token')->all();
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
            DB::table('notification')->insert(array('client_id'=>$client_id,'title' =>  $title,'body'=>$body,'created_at'=> $date,'time'=>$date));
    
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully reject order'])->setStatusCode(200);
        }
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order reject'])->setStatusCode(400);
    }
    public function client_order_remove(Request $request ,$orderId)
    { 
        $date = date('Y-m-d h:i');
        $client_order_remove = 
        DB::table('order')
        ->where('order.id', '=', $orderId )
        ->delete();
        //return response()->json( $request->reject_note);
        if(!empty($client_order_remove) )
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully delete order'])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order deleted'])->setStatusCode(400);
    }
    public function search(Request $request ,$q ,$lang='en')
    { 
        $category = DB::table('category')
        ->join('category_translation', 'category_translation.categoryId', '=', 'category.id')
        ->where('category_translation.title', 'like', "%$q%")
        ->where('category_translation.lang', '=',$lang)
        ->where('category.visible', '=', '1' )
        ->select('*','category.id')
        ->get();
        $sub_category = DB::table('sub_category')
        ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
        ->where('sub_category_translation.title', 'like', "%$q%")
        ->where('sub_category_translation.lang', '=',$lang)
        ->where('sub_category.visible', '=', '1' )
        ->select('*','sub_category.id')
        ->get();

        $employees = Employee::Where('name', 'like', "%$q%")->get();
        foreach ($employees as $employee ){
            $employee->setAttribute('experience',
             DB::table('sub_category')
            ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
            ->whereIn('sub_category.id',array_filter( explode(",", str_replace("'", "", $employee->experience) )) )
            ->where('sub_category_translation.lang', '=',$lang)
            ->get()
         );
            }
        if($sub_category || $employee)
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => ['category'=>$category ,'sub_category'=>$sub_category,'employee'=>$employees],])->setStatusCode(200);
        else
        return response()->json(['status'=>true,'code'=>400,'message'=>'not found sub category or employee',])->setStatusCode(400);
    }
    public function app_page(Request $request ,$title,$lang)
    { 
        $app_page = 
        DB::table('app_page')
        ->join('app_page_translation', 'app_page_translation.app_page_id', '=', 'app_page.id')
        ->where('app_page.title', '=', $title)
        ->where('app_page_translation.lang', '=', $lang )
        ->select('*')
        ->get();
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => $app_page,])->setStatusCode(200);
        
    }
    public function update_location(Request $request ,$moblie)
    { 
        $employee = DB::table('employee')
        ->where('employee.phone', '=', $moblie )->first();
        (!empty($request->location_lng)) ?  $location_lng = $request->location_lng : $location_lng = $employee->location_lng;
        (!empty($request->location_lat)) ?  $location_lat = $request->location_lat : $location_lat = $employee->location_lat;
        //return response()->json( $name );
        $user_info = DB::table('employee')
        ->where('employee.id', '=', $employee->id )
        ->update(['location_lat' => $location_lat,'location_lng'=>$location_lng,]);
        if($user_info)
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully User location is updated',])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'User Not found ',])->setStatusCode(400);
    }
    public function update_token(Request $request ,$moblie)
    { 
        $user_client="";
        $user_employee="";
        $employee = DB::table('employee')
        ->where('employee.phone', '=', $moblie )->first();
        (!empty($request->push_notification_token)) ?  $push_notification_token_employee = $request->push_notification_token : $push_notification_token_employee = $employee->push_notification_token;
        //return response()->json( $name );
        if(!empty($employee))
        {
            $user_employee = DB::table('employee')
            ->where('employee.id', '=', $employee->id )
            ->update(['push_notification_token' => $push_notification_token_employee,]);
        }
        $client = DB::table('client')
        ->where('client.phone', '=', $moblie  )->first();
        (!empty($request->push_notification_token)) ?  $push_notification_token_client = $request->push_notification_token : $push_notification_token_client = $client->push_notification_token;
        if(!empty($client))
        {
            $user_client=DB::table('client')
            ->where('client.id', '=', $client->id )
            ->update(['push_notification_token'=>$push_notification_token_client]);
        }
        if($user_employee!="" || $user_client!="")
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully User Token is updated',])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'User Not found ',])->setStatusCode(400);
    }
    public function fcm(Request $request )
    {

        return view('fcm');
    }
    public function sendPushNotification(Request $request)
    {
        $firebaseToken = Employee::whereNotNull('push_notification_token')->pluck('push_notification_token')->all();
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
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
  
        dd($response);
    }
    public function notification(Request $request ,$moblie)
    { 
        $notification="";
        $employee = DB::table('employee')
        ->where('employee.phone', '=', $moblie )->first();
        //return response()->json(  $employee  );
        if(!empty($employee))
        {
            $notification= DB::table('notification')
            ->where('notification.employee_id', '=', $employee->id )->get();
        }
        $client = DB::table('client')
        ->where('client.phone', '=', $moblie  )->first();
        if(!empty($client))
        {
            $notification= DB::table('notification')
            ->where('notification.client_id', '=', $client->id )->get();
        }
        if($employee!="" || $client!="")
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => $notification])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'User Not found ',])->setStatusCode(400);
    }
    public function slider(Request $request )
    { 
        $slider = DB::table('slider')->get();
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => $slider])->setStatusCode(200);
    }
}