<?php
/**
 * SPT software - Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic model
 * 
 */

namespace DTM\plugins\user\models;

use SPT\Container\Client as Base;

class UserGroupModel extends Base
{
    public function removeByGroup($group_id)
    {
        $user_group = $this->UserGroupEntity->list(0, 0, ['group_id' => $group_id]);
        $try = true;
        foreach($user_group as $value)
        {
            $try = $this->UserGroupEntity->remove($value['id']);
        }

        return $try;
    }

    public function removeByUser($user_id)
    {
        $try = true;
        $user_map = $this->UserGroupEntity->list(0, 0, ['user_id' => $user_id]);
        foreach($user_map as $value)
        {
            $try = $this->UserGroupEntity->remove($value['id']);
        }

        return $try;
    }

    public function updateUserMap($data)
    {
        $groups_update = $this->request->post->get('groups', [], 'array');

        $groups = $this->UserEntity->getGroups($data['id']);
        foreach($groups as $group)
        {
            if (!in_array($group['group_id'], $groups_update))
            {
                $user_map = $this->UserGroupEntity->findOne(['group_id' => $group['group_id'], 'user_id' => $data['id']]);
                $this->UserGroupEntity->remove($user_map['id']);
            }
            else
            {
                $key = array_search($group['group_id'], $groups_update);
                unset($groups_update[$key]);
            }
        }

        foreach($groups_update as $group)
        {
            $this->UserGroupEntity->add([
                'user_id' => $data['id'],
                'group_id' => $group,
            ]);
        }

        return true;
    }

    public function addUserMap($newid)
    {
        $groups = $this->request->post->get('groups', [], 'array');
        if ($groups)
        {
            foreach($groups as $group)
            {
                $this->UserGroupEntity->add([
                    'user_id' => $newid,
                    'group_id' => $group,
                ]);
            }
        }
        return true;
    }

    public function checkAccessGroup($id, $access = [])
    {
        $groups = $this->UserEntity->getGroups($this->user->get('id'));
        $check = false;
        $user_groups = [];

        foreach($groups as $group)
        {
            if ($group['group_id'] == $id)
            {
                $check = true;
            }
            else
            {
                $user_groups[] = $group['id'];
            }
        }

        if (!$check)
        {
            return true;
        }
        
        $user_access = $this->UserModel->getAccessByGroup($user_groups);
        $user_access = array_merge($user_access, $access);
        
        if (!in_array('user_manager', $user_access) || !in_array('usergroup_manager', $user_access))
        {
            // return false;
        }

        return true;
    }

    public function validate($id = 0)
    {
        $name = $this->request->post->get('name', '', 'string');

        if(!empty($name)) 
        {
            $find = $this->GroupEntity->findOne(['name' => $name]);
            if ($find && $find['id'] != $id)
            {
                $this->session->set('validate', "Error: Group Name already exists");
                return false;
            }
        } 
        else 
        {
            $this->session->set('validate', "Error: Group name can't empty");
            return false;
        }

        return true;
    }

    public function add($data)
    {
        if (!$data || !is_array($data) || !$data['name'])
        {
            return false;
        }

        $try = $this->GroupEntity->add($data);

        return $try;
    }

    public function update($data)
    {
        if (!$data || !is_array($data) || !$data['name'] || !$data['id'])
        {
            return false;
        }

        $try = $this->GroupEntity->update($data);

        return $try;
    }

    public function remove($id)
    {
        if (!$id) return false;
        $try = $this->GroupEntity->remove($id);
        if ($try)
        {
            $this->UserGroupModel->removeByGroup($id);
        }
        
        return $try;
    }
}
