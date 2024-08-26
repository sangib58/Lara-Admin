<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserHistory;
use App\Models\Setting;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponser;

    public function GetLoginInfo(Request $request)
    {
        try
        {
            $attr = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string|min:8'
            ]);
    
            if (!Auth::attempt($attr)) {
                return $this->error('Credentials not match', 202);
            }
            return $this->success([
                'token' => auth()->user()->createToken('lara-api-token')->plainTextToken,
                'obj'=>auth()->user()
            ]);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }        
    }

    public function CreateLoginHistory(Request $request)
    {
        try
        {
            $request->validate([
                'token'=>'required|string',
                'userId'=>'required'
            ]);
            
            $data=UserHistory::create([
                'log_code'=>$request->token,
                'user_id'=>$request->userId,
                'log_date'=>date('Y-m-d'),
                'log_in_time'=>now(),
                'browser'=>$request->browserName,
                'browser_version'=>$request->browserVersion,
                'platform'=>$request->platform,
                'user_ip'=>$request->userIp,
            ]);   
            return $this->success($data,'Saved successfully');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function UpdateLoginHistory(Request $request)
    {
        try
        {
            $request->validate([
                'token'=>'required|string'
            ]);
    
            $data=UserHistory::where('log_code',$request->token)
            ->update(['log_out_time'=>now()]);  
            return $this->success(null,'Updated successfully');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetLogInSummaryByDate($userId)
    {
        try
        {
            $roleId=User::findOrFail($userId);

            if($roleId->user_role_id==1)
            {
                $data=DB::table('log_histories')
                ->select(DB::raw('count(*) as count,log_date as date'))
                ->orderBy('log_date','desc')
                ->groupBy('log_date')
                ->take(10)
                ->get();
                return $this->success($data);
            }
            else
            {
                $data=DB::table('log_histories')
                ->select(DB::raw('count(*) as count,log_date as date'))
                ->where('user_id',$userId)
                ->orderBy('log_date','desc')
                ->groupBy('log_date')
                ->take(10)
                ->get();
                return $this->success($data);
            }
        }
        catch(ModelNotFoundException $ex)
        {
            return $this->error('User not exists',202,$ex->getMessage());
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetLogInSummaryByMonth($userId)
    {
        try
        {
            $roleId=User::findOrFail($userId);

            if($roleId->user_role_id==1)
            {
                $data=DB::table('log_histories')
                ->select(DB::raw('count(*) as count,MONTH(log_date) as month'))
                ->orderBy('log_date','desc')
                ->groupBy(DB::raw('MONTH(log_date)'))
                ->take(10)
                ->get();
                return $this->success($data);
            }
            else
            {
                $data=DB::table('log_histories')
                ->select(DB::raw('count(*) as count,MONTH(log_date) as month'))
                ->where('user_id',$userId)
                ->orderBy('log_date','desc')
                ->groupBy(DB::raw('MONTH(log_date)'))
                ->take(10)
                ->get();
                return $this->success($data);
            }
        }
        catch(ModelNotFoundException $ex)
        {
            return $this->error('User not exists',202,$ex->getMessage());
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetLogInSummaryByYear($userId)
    {
        try
        {
            $roleId=User::findOrFail($userId);
            
            if($roleId->user_role_id==1)
            {
                $data=DB::table('log_histories')
                ->select(DB::raw('count(*) as count,YEAR(log_date) as year'))
                ->orderBy('log_date','desc')
                ->groupBy(DB::raw('YEAR(log_date)'))
                ->take(10)
                ->get();
                return $this->success($data);
            }
            else
            {
                $data=DB::table('log_histories')
                ->select(DB::raw('count(*) as count,YEAR(log_date) as year'))
                ->where('user_id',$userId)
                ->orderBy('log_date','desc')
                ->groupBy(DB::raw('YEAR(log_date)'))
                ->take(10)
                ->get();
                return $this->success($data);
            }
        }
        catch(ModelNotFoundException $ex)
        {
            return $this->error('User not exists',202,$ex->getMessage());
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetSummaryByBrowser($userId)
    {
        try
        {
            $roleId=User::findOrFail($userId);
            
            if($roleId->user_role_id==1)
            {
                $data=DB::table('log_histories')
                ->select(DB::raw('count(*) as count,browser'))
                ->orderBy('log_date','desc')
                ->groupBy(DB::raw('browser'))
                ->take(10)
                ->get();
                return $this->success($data);
            }
            else
            {
                $data=DB::table('log_histories')
                ->select(DB::raw('count(*) as count,browser'))
                ->where('user_id',$userId)
                ->orderBy('log_date','desc')
                ->groupBy(DB::raw('browser'))
                ->take(10)
                ->get();
                return $this->success($data);
            }
        }
        catch(ModelNotFoundException $ex)
        {
            return $this->error('User not exists',202,$ex->getMessage());
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetRoleWiseUser()
    {
        try
        {
            $data=DB::table('users')
            ->join('user_roles','users.user_role_id','=','user_roles.user_role_id')
            ->select(DB::raw('count(*) as count,user_roles.role_name as roleName'))
            ->groupBy('user_roles.role_name')
            ->get();
            return $this->success($data);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetUserRoleList()
    {
        try 
        {
            $data=DB::table('user_roles')
            ->leftJoin('menu_groups','user_roles.menu_group_id','=','menu_groups.menu_group_id')
            ->select('user_role_id as userRoleId','role_name as roleName','role_desc as roleDesc','menu_groups.menu_group_id as menuGroupID','menu_group_name as menuGroupName')
            ->get();
            return $this->success($data);
        } 
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetSingleRole($roleId)
    {
        try 
        {
            $data=UserRole::findOrFail($roleId);
            return $this->success($data);
        } 
        catch(ModelNotFoundException $ex)
        {
            return $this->error('Role not exists',202,$ex->getMessage());
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function DeleteSingleRole($roleId)
    {        
        try 
        {
            UserRole::findOrFail($roleId);
            UserRole::destroy($roleId);
            return $this->success(null,'Deleted successfully');
        }
        catch(ModelNotFoundException $ex)
        {
            return $this->error('Role not exists',202,$ex->getMessage());
        } 
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function CreateUserRole(Request $request)
    {
        try 
        {
            $request->validate([
                'roleName'=>'required|string',
                'addedBy'=>'required|integer',
                'roleDesc'=>'string|nullable',
                'menuGrpId'=>'integer|nullable'
            ]);

            if(UserRole::where('role_name',$request->roleName)->get()->count()>0)
            {
                return $this->error('Duplicate Role Name',202);
            }
            else
            {
                $data=UserRole::create([
                    'role_name'=>$request->roleName,
                    'role_desc'=>$request->roleDesc,
                    'menu_group_id'=>$request->menuGrpId,
                    'added_by'=>$request->addedBy
                ]);   
                return $this->success($data,'Saved successfully');
            }
        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }      
    }

    public function UpdateUserRole(Request $request)   
    {
        try 
        {
            $request->validate([
                'roleId'=>'required|integer',
                'roleName'=>'required|string',
                'updatedBy'=>'required|integer',
                'roleDesc'=>'string|nullable',
                'menuGrpId'=>'integer|nullable'
            ]);

            if(UserRole::where([['role_name','=',$request->roleName],['user_role_id','!=',$request->roleId]])->get()->count()>0)
            {
                return $this->error('Duplicate Role Name',202);
            }
            else
            {
                UserRole::where('user_role_id',$request->roleId)
                ->update([
                    'role_name'=>$request->roleName,
                    'role_desc'=>$request->roleDesc,
                    'menu_group_id'=>$request->menuGrpId,
                    'last_updated_by'=>$request->updatedBy
                ]);               
                return $this->success(null,'Updated successfully');
            }
        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        } 
    }

    public function GetUserList()
    {
        try 
        {
            $data=DB::table('users')
            ->leftJoin('user_roles','users.user_role_id','=','user_roles.user_role_id')
            ->select('users.id as userId','users.user_role_id as userRoleId',
            'users.name as fullName','users.username as userName','password','email','mobile',
            'users.image_path as imagePath','users.date_of_birth as dateOfBirth',
            'user_roles.role_name as roleName')
            ->get();
            return $this->success($data);
        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetSingleUser($userId)
    {
        try 
        {
            $data=User::findOrFail($userId);
            return $this->success($data);
        } 
        catch(ModelNotFoundException $ex)
        {
            return $this->error('User not exists',202,$ex->getMessage());
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function DeleteSingleUser($userId)
    {        
        try 
        {
            User::findOrFail($userId);
            User::destroy($userId);
            return $this->success(null,'Deleted successfully');
        } 
        catch(ModelNotFoundException $ex)
        {
            return $this->error('User not exists',202,$ex->getMessage());
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function CreateUser(Request $request)
    {
        try 
        {
            $request->validate([
                'roleId'=>'required|integer',
                'name'=>'required|string',
                'username'=>'required|string',
                'password'=>'required|string',
                'mobile'=>'string|nullable',
                'email'=>'required|string',
                'dateOfBirth'=>'string|nullable',
                'imagePath'=>'string|nullable',
                'addedBy'=>'required|integer',
            ]);

            if(User::where('username',$request->username)->get()->count()>0)
            {
                return $this->error('Duplicate Username',202);
            }
            else if(User::where('email',$request->email)->get()->count()>0)
            {
                return $this->error('Duplicate Email',202);
            }
            else
            {
                $data=User::create([
                    'user_role_id'=>$request->roleId,
                    'name'=>$request->name,
                    'username'=>$request->username,
                    'password'=>bcrypt($request->password),
                    'mobile'=>$request->mobile,
                    'email'=>$request->email,
                    'date_of_birth'=>$request->dateOfBirth,
                    'image_path'=>$request->imagePath,
                    'added_by'=>$request->addedBy
                ]);   
                return $this->success($data,'Saved successfully');
            } 
        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }      
    }

    public function UpdateUser(Request $request)   
    {
        try 
        {
            $request->validate([
                'userId'=>'required|integer',
                'roleId'=>'required|integer',
                'name'=>'required|string',
                'username'=>'required|string',
                'mobile'=>'string|nullable',
                'email'=>'required|string',
                'dateOfBirth'=>'string|nullable',
                'imagePath'=>'string|nullable',
                'updatedBy'=>'required|integer',
            ]);

            if(User::where([['username','=',$request->username],['id','!=',$request->userId]])->get()->count()>0)
            {
                return $this->error('Duplicate Username',202);
            }
            else if(User::where([['email','=',$request->email],['id','!=',$request->userId]])->get()->count()>0)
            {
                return $this->error('Duplicate Email',202);
            }
            else
            {
                User::where('id',$request->userId)
                ->update([
                    'user_role_id'=>$request->roleId,
                    'name'=>$request->name,
                    'username'=>$request->username,
                    'mobile'=>$request->mobile,
                    'email'=>$request->email,
                    'date_of_birth'=>$request->dateOfBirth,
                    'image_path'=>$request->imagePath,               
                    'last_updated_by'=>$request->updatedBy
                ]);           
                return $this->success(null,'Updated successfully');
            }
        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        } 
    }

    public function UpdateUserProfile(Request $request)   
    {
        try 
        {
            $request->validate([
                'userId'=>'required|integer',
                'name'=>'required|string',
                'mobile'=>'string|nullable',
                'email'=>'required|string',
                'dateOfBirth'=>'string|nullable',
                'imagePath'=>'string|nullable',
                'updatedBy'=>'required|integer',
            ]);

            if(User::where([['email','=',$request->email],['id','!=',$request->userId]])->get()->count()>0)
            {
                return $this->error('Duplicate Email',202);
            }
            else
            {
                User::where('id',$request->userId)
                ->update([                  
                    'name'=>$request->name,
                    'mobile'=>$request->mobile,
                    'email'=>$request->email,
                    'date_of_birth'=>$request->dateOfBirth,
                    'image_path'=>$request->imagePath,               
                    'last_updated_by'=>$request->updatedBy
                ]);           
                return $this->success(null,'Updated successfully');
            }
        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        } 
    }

    public function ChangeUserPassword(Request $request)
    {
        try
        {
            User::where('id',$request->userId)
            ->update([
                'password'=>bcrypt($request->password)                          
            ]);           
            return $this->success(null,'Password changed successfully');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function Unlock(Request $request)
    {
        try
        {
            $request->validate([
                'password'=>'required|string',
                'hashedPassword'=>'required|string',
            ]);
            if(Hash::check($request->password, $request->hashedPassword))
            {
                return $this->success(null,'Password matched');
            }
            else
            {
                return $this->error('Password not matched',202);
            }
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function UserStatus()
    {
        try
        {
            $totalUser=User::count();
            $activeUser=User::where('is_active',true)->count();
            $inActiveUser=User::where('is_active',false)->count();
            $adminUser=User::where('user_role_id',1)->count();

            $data=['totalUser'=>$totalUser,'activeUser'=>$activeUser,
            'inActiveUser'=>$inActiveUser,'adminUser'=>$adminUser];
            return $this->success($data);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function Upload(Request $request) 
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,bmp,tif,tiff',
        ]);

        if(!$request->hasFile('image')) {
            return $this->error('upload_file_not_found',202);
        }
        $file = $request->file('image');
        if(!$file->isValid()) {
            return $this->error('invalid_file_upload',202);
        }
        $path=public_path().'/images';
        $fileName=time().$file->getClientOriginalName();
        $file->move($path, $fileName);
        $pathReturn='\\images\\'.$fileName;
        return $this->success($pathReturn);
    }

    public function GeneralSettings()
    {
        try 
        {
            $data=Setting::first();
            return $this->success($data);
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function UpdateSiteTitle(Request $request)
    {
        try
        {
            DB::table('settings')->update(['title'=>$request->title]);           
            return $this->success(null,'Title updated');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function UpdateDescription(Request $request)
    {
        try
        {
            DB::table('settings')->update(['description'=>$request->description]);           
            return $this->success(null,'Description updated');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function UpdateFooter(Request $request)
    {
        try
        {
            DB::table('settings')->update(['footer'=>$request->footer]);           
            return $this->success(null,'Footer updated');
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetBrowseList()
    {
        try
        {            
            $data=DB::select('select users.id as UserId, users.name as fullName, 
            users.username, log_in_time as logInTime, log_out_time as logOutTime, 
            user_ip as userIp, browser, browser_version as browserVersion,
            platform from log_histories left join 
            users on log_histories.user_id = users.id');
            return $this->success($data);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetNotificationList()
    {
        try
        {            
            $data=DB::select('select users.id as UserId, users.name as fullName, 
            users.username, log_in_time as logInTime, log_out_time as logOutTime, 
            user_ip as userIp, browser, browser_version as browserVersion,
            platform from log_histories left join 
            users on log_histories.user_id = users.id 
            where log_date >= DATE_ADD(CURDATE(), INTERVAL -2 DAY) order by log_date desc');
            return $this->success($data);
        }
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }
}
