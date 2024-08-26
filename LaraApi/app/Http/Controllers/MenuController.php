<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuGroup;
use App\Models\MenuMapping;
use App\Models\UserRole;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuController extends Controller
{
    use ApiResponser;

    public function GetSidebarMenu($roleId)
    {
        try
        {
            $parentMenus=Menu::where([
                ['parent_id','0'],
                ['is_sub_menu','1']
            ])
            ->select('menu_id','parent_id','menu_title','sort_order','icon_class')
            ->orderBy('sort_order')
            ->get();

            $sidebar=array();
            foreach($parentMenus as $itemKey=>$itemVal)
            {                           
                $menuGroupId=UserRole::find($roleId); 
                $childMenus=DB::select('SELECT m.menu_id as id,m.menu_title as title,m.url as route FROM `menus` m where m.parent_id=:menuId
                and m.menu_id=(select menu_id from menu_mappings where menu_group_id=:menuGroupId
                and menu_id=m.menu_id)',array('menuId'=>$itemVal->menu_id,'menuGroupId'=>$menuGroupId->menu_group_id));

                if(count($childMenus)>0)
                {
                    $sidebar[]=['id'=>$itemVal->menu_id,'title'=>$itemVal->menu_title,
                    'icon'=>$itemVal->icon_class,'childItems'=>$childMenus];
                }
            }

            return $this->success($sidebar);
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetAllMenu($menuGrpId)
    {
        try
        {
            $parentMenus=Menu::where([
                ['parent_id','0'],
                ['is_sub_menu','1']
            ])
            ->select('menu_id','parent_id','menu_title','sort_order','icon_class')
            ->orderBy('sort_order')
            ->get();

            $sidebar=array();
            foreach($parentMenus as $itemKey=>$itemVal)
            {                            
                $childMenus=DB::select('SELECT m.menu_id as id,m.menu_title as title,'.$menuGrpId.' as groupId,
                if(m.menu_id=(select menu_id from menu_mappings where menu_group_id=:menuGroupId and menu_id=m.menu_id),true,false) as isSelected FROM `menus` m 
                where m.parent_id=:menuId',array('menuId'=>$itemVal->menu_id,'menuGroupId'=>$menuGrpId));

                if(count($childMenus)>0)
                {
                    $sidebar[]=['id'=>$itemVal->menu_id,'title'=>$itemVal->menu_title,'children'=>$childMenus];
                }
            }
            return $this->success($sidebar);
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function MenuAssign(Request $request)
    {
        try 
        {
            $request->validate([
                'menuId'=>'required|integer',
                'menuGroupId'=>'required|integer',
                'isSelected'=>'required|boolean',
                'addedBy'=>'required|integer'
            ]);

            $objCheck=MenuMapping::where([
                ['menu_group_id',$request->menuGroupId],
                ['menu_id',$request->menuId]
            ])->first();

            if($request->isSelected==false)
            {
                if($objCheck==null)
                {
                    MenuMapping::create([
                        'menu_group_id'=>$request->menuGroupId,
                        'menu_id'=>$request->menuId,
                        'added_by'=>$request->addedBy
                    ]);
                    return $this->success(null,'assigned');
                }
            }
            else if($request->isSelected==true)
            {
                if($objCheck!=null)
                {
                    MenuMapping::destroy($objCheck->menu_mapping_id);
                    return $this->success(null,'un-assigned');
                }
            }
            return $this->error('Something unexpected',202);
        } 
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetMenuList()
    {
        try 
        {
            $data=DB::select('SELECT m.menu_id as menuID,m.menu_title as menuTitle,m.url,m.is_sub_menu as isSubMenu,m.sort_order as sortOrder,m.icon_class as iconClass,m.parent_id as parentID,(select menu_title from menus where menu_id=m.parent_id) as parentMenuName FROM `menus` m');
            return $this->success($data);
        } 
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetParentMenuList()
    {
        try 
        {
            $initial=array(['id'=>0,'text'=>'Root']);
            $data=Menu::where([
                ['parent_id',0],
                ['is_sub_menu',1]
            ])
            ->select('menu_id as id','menu_title as text')
            ->get()
            ->toArray();

            $meergedData=array_merge($initial,$data);

            return $this->success($meergedData);
        } 
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetSingleMenu($menuId)
    {
        try 
        {
            $data=Menu::findOrFail($menuId);
            return $this->success($data);
        } 
        catch(ModelNotFoundException $ex)
        {
            return $this->error('Menu not exists',202,$ex->getMessage());
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function DeleteSingleMenu($menuId)
    {        
        try 
        {
            Menu::findOrFail($menuId);
            Menu::destroy($menuId);
            return $this->success(null,'Deleted successfully');
        }
        catch(ModelNotFoundException $ex)
        {
            return $this->error('Menu not exists',202,$ex->getMessage());
        } 
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function CreateMenu(Request $request)
    {
        try
        {
            $request->validate([
                'parentId'=>'required|integer',
                'menuTitle'=>'required|string',
                'url'=>'nullable|string',
                'isSubMenu'=>'required|integer',
                'sortOrder'=>'required|integer',
                'iconClass'=>'nullable|string',
                'addedBy'=>'required|integer',
            ]);

            if(Menu::where('menu_title',$request->menuTitle)->get()->count()>0)
            {
                return $this->error('Duplicate Menu Title',202);
            }
            else if(Menu::where([['sort_order','=',$request->sortOrder],['parent_id','=',0],['sort_order','>',0]])->get()->count()>0)
            {
                return $this->error('Duplicate Order No.',202);
            }
            else if($request->parentId==0 && $request->sortOrder<=0)
            {
                return $this->error('Order No. must greater than 0.',202);
            }
            else if($request->parentId!=0 && $request->sortOrder<>0)
            {
                return $this->error('Child menu Order No. must equal to 0.',202);
            }
            else
            {
                $data=Menu::create([
                    'parent_id'=>$request->parentId,
                    'menu_title'=>$request->menuTitle,
                    'url'=>$request->url,
                    'is_sub_menu'=>$request->isSubMenu,
                    'sort_order'=>$request->sortOrder,
                    'icon_class'=>$request->iconClass,
                    'added_by'=>$request->addedBy
                ]);
                return $this->success($data,'Saved successfully');
            }
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function UpdateMenu(Request $request)
    {
        try
        {
            $request->validate([
                'menuId'=>'required|integer',
                'parentId'=>'required|integer',
                'menuTitle'=>'required|string',
                'url'=>'nullable|string',
                'isSubMenu'=>'required|integer',
                'sortOrder'=>'required|integer',
                'iconClass'=>'nullable|string',
                'updatedBy'=>'required|integer',
            ]);

            if(Menu::where([['menu_title','=',$request->menuTitle],['menu_id','!=',$request->menuId]])->get()->count()>0)
            {
                return $this->error('Duplicate Menu Title',202);
            }
            else if(Menu::where([['sort_order','=',$request->sortOrder],['parent_id','=',0],['menu_id','!=',$request->menuId]])->get()->count()>0)
            {
                return $this->error('Duplicate Order No.',202);
            }
            else if($request->parentId==0 && $request->sortOrder<=0)
            {
                return $this->error('Order No. must greater than 0.',202);
            }
            else if($request->parentId!=0 && $request->sortOrder<>0)
            {
                return $this->error('Child menu Order No. must equal to 0.',202);
            }
            else
            {
                Menu::where('menu_id',$request->menuId)
                ->update([
                    'parent_id'=>$request->parentId,
                    'menu_title'=>$request->menuTitle,
                    'url'=>$request->url,
                    'is_sub_menu'=>$request->isSubMenu,
                    'sort_order'=>$request->sortOrder,
                    'icon_class'=>$request->iconClass,
                    'last_updated_by'=>$request->updatedBy
                ]);
                return $this->success(null,'Updated successfully');
            }
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetMenuGroupList()
    {
        try 
        {         
            $data=MenuGroup::select('menu_group_id as menuGroupID','menu_group_name as menuGroupName')->orderBy('menu_group_id')->get();
            return $this->success($data);     
        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function GetSingleMenuGroup($menuGrpId)
    {
        try 
        {
            $data=MenuGroup::findOrFail($menuGrpId);
            return $this->success($data);
        } 
        catch(ModelNotFoundException $ex)
        {
            return $this->error('Menu Group not exists',202,$ex->getMessage());
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function DeleteSingleMenuGroup($menuGrpId)
    {        
        try 
        {
            MenuGroup::findOrFail($menuGrpId);
            MenuGroup::destroy($menuGrpId);
            return $this->success(null,'Deleted successfully');
        } 
        catch(ModelNotFoundException $ex)
        {
            return $this->error('Menu Group not exists',202,$ex->getMessage());
        }
        catch (\Exception $ex) 
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }

    public function CreateMenuGroup(Request $request)
    {
        try 
        {
            $request->validate([
                'menuGrpName'=>'required|string',
                'addedBy'=>'required|integer'
            ]);

            if(MenuGroup::where('menu_group_name',$request->menuGrpName)->get()->count()>0)
            {
                return $this->error('Duplicate Menu Group Name',202);
            }
            else
            {
                $data=MenuGroup::create([
                    'menu_group_name'=>$request->menuGrpName,
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

    public function UpdateMenuGroup(Request $request)
    {
        try 
        {
            $request->validate([
                'menuGrpId'=>'required|integer',
                'menuGrpName'=>'required|string',
                'updatedBy'=>'required|integer'
            ]);

            if(MenuGroup::where([['menu_group_name','=',$request->menuGrpName],['menu_group_id','!=',$request->menuGrpId]])->get()->count()>0)
            {
                return $this->error('Duplicate Menu Group Name',202);
            }
            else
            {
                MenuGroup::where('menu_group_id',$request->menuGrpId)
                ->update([
                    'menu_group_name'=>$request->menuGrpName,
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

    public function GetMenuGroupWiseMenuMappingList()
    {
        try 
        {
            $data=MenuMapping::all();
            return $this->success($data);
        } 
        catch(\Exception $ex)
        {
            return $this->error($ex->getMessage(),400,$ex->getMessage());
        }
    }
}
