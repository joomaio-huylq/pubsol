<?php
namespace App\plugins\user\models;

use SPT\Container\Client as Base;

class PermissionModel extends Base
{
    public function getAccess()
    {
        if (!isset($this->access)) 
        {
            $register_access = [];
            $this->app->plgLoad('permission', 'registerAccess', function($access) use (&$register_access){
                if (is_array($access) && $access)
                {
                    $register_access = array_merge($register_access, $access);
                }
            });
    
            $this->access = $register_access;
        }
        return $this->access;
    }

    public function checkPermission($access = null)
    {
        if (!$access)
        {
            $permission = $this->app->get('permission', []);
            $method = $this->request->header->getRequestMethod();

            if (!$permission)
            {
                return true;
            }

            if (isset($permission[$method]) && $permission[$method])
            {
                $access = $permission[$method];
            }
            else
            {
                if (!array_is_list($permission))
                {
                    return true;
                }

                $access = $permission;
            }
            
        }
        
        $user_access = $this->getAccessByUser();
        foreach($access as $item)
        {
            if (in_array($item, $user_access))
            {
                return true;
            }
        }

        return false;
    }

    public function getAccessByUser()
    {
        if (!$this->user->get('id'))
        {
            return [];
        }

        $groups = $this->UserEntity->getGroups($this->user->get('id'));
        $access = [];

        foreach($groups as $group)
        {
            $group_tmp = $this->GroupEntity->findByPK($group['group_id']);
            if ($group_tmp)
            {
                $access_tmp = $group_tmp['access'] ? json_decode($group_tmp['access'], true) : [];
                $access = array_merge($access, $access_tmp);
            }
        }

        return $access;
    }
}