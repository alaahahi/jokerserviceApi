<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use PDF;

class CustomerController extends Controller
{

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
    public function sub_categories_employee(Request $request ,$subCategoriesId)
    { 
        $sub_categories_employee = 
        DB::table('employee')
        ->orWhere('experience', 'like', '%' ."'".$subCategoriesId."'". '%')
        ->select('*')
        ->get();
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
            DB::table('client')
            ->where('client.id', '=', $clientId->id )
            ->update(['name' =>  $name,'updated_at'=> $date]);
            return response()->json(['status'=>true,'code'=>200,'message'=>'Update client info'])->setStatusCode(201);
        }else
        DB::table('client')->insert(array('phone' => $moblie,'name'=>$request->name,'created_at'=> $date));
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
            //return response()->json( $name );
            $user_info = DB::table('employee')
            ->where('employee.id', '=', $employeeId->id )
            ->update(['name' => $name,'birthdate'=>$birthdate,'sex'=>$sex,'experience'=>$experience 
            ,'photo'=>'employee/'.$monthName.$year.'/'.$imageName,'years_experience'=>$years_experience,'languages'=>$languages ,'id_number'=>$id_number
        ]);
            return response()->json(['status'=>true,'code'=>200,'message'=>'Update employee info'])->setStatusCode(201);    
        }
        else
            DB::table('employee')->insertGetId(array('phone' => $moblie,'name'=>$request->name,'birthdate'=>$request->birthdate
            ,'sex'=>$request->sex,'years_experience'=>$request->years_experience,'languages'=>$request->languages
            ,'photo'=>'employee/'.$monthName.$year.'/'.$imageName,'experience'=>$request->experience,'id_number'=>$request->id_number
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
        $imageName ="default.png";
        //$date = date('Y-m-d h:i');
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
        
            DB::table('order')->insertGetId(array('client_id' =>$clientId,'employee_id'=>  $employeeId,'subcategory_id'=>$sub_categories_id
            ,'image'=>'order/'.$monthName.$year.'/'.$imageName,'location_lng'=>$request->location_lng,'location_lat'=>$request->location_lat
            ,'date'=>$request->date,'details'=>$request->details
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
    public function get_order_employee(Request $request ,$employeeId ,$lang)
    { 
        $order_employee = 
        DB::table('order')
        ->join('sub_category', 'sub_category.id', '=', 'order.subcategory_id')
        ->join('client', 'client.id', '=', 'order.client_id')
        ->join('employee', 'employee.id', '=', 'order.employee_id')
        ->join('sub_category_translation', 'sub_category_translation.sub_category_Id', '=', 'sub_category.id')
        ->where('order.employee_id', '=', $employeeId )
        ->where('sub_category_translation.lang', '=', $lang )
        ->select('*','order.id')
        ->get();
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully','data' => $order_employee,])->setStatusCode(200);
    }
    public function employee_order_accept(Request $request ,$orderId)
    { 
        $date = date('Y-m-d h:i');
        $employee_order_accept = 
        DB::table('order')
        ->where('order.id', '=', $orderId )
        ->update(['status' => 1,'accepted_date' => $date,'note'=> $request->note]);
        if(!empty($employee_order_accept) )
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully accept order'])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order accept'])->setStatusCode(400);
    }
    public function client_order_finish(Request $request ,$orderId)
    { 
        $date = date('Y-m-d h:i');
        $client_order_accept = 
        DB::table('order')
        ->where('order.id', '=', $orderId )
        ->update(['status' => 3,'finish_date' => $date,'rate'=> $request->rate]);
        if(!empty($client_order_accept) )
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully finish order'])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order finish'])->setStatusCode(400);
    }
    public function employee_order_reject(Request $request ,$orderId)
    { 
        $date = date('Y-m-d h:i');
        $employee_order_reject = 
        DB::table('order')
        ->where('order.id', '=', $orderId )
        ->update(['status' => 2,'reject_date' => $date,'note'=> $request->note]);
        //return response()->json( $request->reject_note);
        if(!empty($employee_order_reject) )
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully reject order'])->setStatusCode(200);
        else
        return response()->json(['status'=>false,'code'=>400,'message'=>'No order reject'])->setStatusCode(400);
    }
}