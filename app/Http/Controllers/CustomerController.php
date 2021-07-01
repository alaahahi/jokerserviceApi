<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use PDF;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $clientId = DB::table('client')
        ->where('client.moblie', '=', $moblie )->select('id')->first();
        if(!empty($clientId))
        {
            $user_info = DB::table('client')
            ->where('client.id', '=', $clientId->id )
            ->update(['full_name' => $request->name]);
            return response()->json(['status'=>true,'code'=>200,'message'=>'Update client info'])->setStatusCode(201);
        }else
        DB::table('client')->insert(array('moblie' => $moblie,'full_name'=>$request->name));
        return response()->json(['status'=>true,'code'=>200,'message'=>'Added user info'])->setStatusCode(201);
        
    }
    public function add_employee_info(Request $request,$moblie)
    { 
        $employeeId = DB::table('employee')
        ->where('employee.phone', '=', $moblie )->select('id')->first();
        if(!empty($employeeId))
        {
            $user_info = DB::table('employee')
            ->where('employee.id', '=', $employeeId->id )
            ->update(['name' => $request->name,'birthdate'=>$request->birthdate,'sex'=>$request->sex,'experience'=>$request->experience]);
            return response()->json(['status'=>true,'code'=>200,'message'=>'Update employee info'])->setStatusCode(201);    
        }
        else
        DB::table('employee')
            ->where('employee.id', '=',DB::table('employee')->insertGetId(array('phone' => $moblie,'name'=>$request->name,'birthdate'=>$request->birthdate
            ,'sex'=>$request->sex,'years_experience'=>$request->years_experience,'languages'=>$request->languages)) )
            ->update(['experience'=>$request->experience]);
            return response()->json(['status'=>true,'code'=>200,'message'=>'Added employee info'])->setStatusCode(201);    
    }
    public function employee_info(Request $request,$moblie)
    { 
        $employeeId = DB::table('employee')
        ->where('employee.phone', '=', $moblie )->select('id')->first();
        $clientId = DB::table('client')
        ->where('client.moblie', '=', $moblie )->select('id')->first();
        if(!empty($clientId))
        {
            $user_info = DB::table('client')
            ->where('client.id', '=', $clientId->id )
            ->first();
        return response()->json(['status'=>true,'code'=>200,'message'=>'successfully client','data' => $user_info,])->setStatusCode(200);
        }
        if(!empty($employeeId))
        {
            $user_info = DB::table('employee')
            ->where('employee.id', '=', $employeeId->id )
            ->first();
            return response()->json(['status'=>true,'code'=>200,'message'=>'successfully employee','data' => $user_info,])->setStatusCode(200);
        }else
        return response()->json(['status'=>false,'code'=>400,'message'=>'User Not Found'])->setStatusCode(200);    
    }
}