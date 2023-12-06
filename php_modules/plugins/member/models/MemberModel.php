<?php

/**
 * SPT software - Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic model
 * 
 */

namespace App\plugins\member\models;

use SPT\Container\Client as Base;
use SPT\Traits\ErrorString;

class MemberModel extends Base
{
    use ErrorString;

    public function add($data)
    {
        $data = [
            'name' => $data['name'],
            'email' => $data['email'],
            'email_verified_at' => date('Y-m-d H:i:s'),
            'password' => $data['password'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $data = $this->MemberEntity->bind($data);
        if (!$data)
        {
            $this->error = $this->MemberEntity->getError();
            return false;
        }
        // check email exist
        $check_email = $this->MemberEntity->check_email_exist($data['email']);
        if ($check_email)
        {
            $this->error = 'Email is exist!';
            return false;
        }

        $newId = $this->MemberEntity->add($data);

        if (!$newId)
        {
            $this->error = $this->MemberEntity->getError();
        }

        return $newId;
    }

    public function update($data)
    {
        $data = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $data['id'],
        ];

        $data = $this->MemberEntity->bind($data);
        if (!$data)
        {
            $this->error = $this->MemberEntity->getError();
            return false;
        }

        // check email exist
        $check_email = $this->MemberEntity->check_email_exist($data['email'], $data['id']);
        if ($check_email)
        {
            $this->error = 'Email is exist!';
            return false;
        }

        $try = $this->MemberEntity->update($data);

        if (!$try)
        {
            $this->error = $this->MemberEntity->getError();
        }

        return $try;
    }

    public function remove($id)
    {
        if (!$id) return false;
        $try = $this->MemberEntity->remove($id);

        return $try;
    }
}