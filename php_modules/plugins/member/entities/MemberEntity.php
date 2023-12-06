<?php
/**
 * SPT software - Entity
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic entity
 * 
 */

namespace App\plugins\member\entities;

use SPT\Storage\DB\Entity;

class MemberEntity extends Entity
{
    protected $table = '#__members';
    protected $pk = 'id';

    public function getFields()
    {
        return [
            'id' => [
                'type' => 'int',
                'pk' => 1,
                'option' => 'unsigned',
                'extra' => 'auto_increment',
            ],
            'name' => [
                'type' => 'varchar',
                'limit' => 255,
            ],
            'email' => [
                'type' => 'varchar',
                'limit' => 255,
            ],
            'email_verified_at' => [
                'type' => 'datetime',
                'default' => 'NOW()',
                'null' => 'YES',
            ],
            'password' => [
                'type' => 'varchar',
                'limit' => 255,
            ],
            'remember_token' => [
                'type' => 'varchar',
                'limit' => 100,
                'null' => 'YES',
            ],
            'created_at' => [
                'type' => 'datetime',
                'default' => 'NOW()',
            ],
            'updated_at' => [
                'type' => 'datetime',
                'default' => 'NOW()',
            ]
        ];
    }

    public function validate($data)
    {
        if (!$data || !is_array($data))
        {
            $this->error = "Data invalid format";
            return false;
        }

        if(empty($data['name'])) 
        {
            $this->error = "Name can't empty";
            return false;
        }

        if(empty($data['email'])) 
        {
            $this->error = "Email can't empty";
            return false;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error = "Invalid email format";
            return false;
        }

        if(empty($data['password'])) 
        {
            $this->error = "Email can't empty";
            return false;
        }

        unset($data['readyUpdate']);
        unset($data['readyNew']);
        return $data;
    }

    public function check_email_exist($email, $id=false)
    {
        $where = ['email' => $email];
        if ($id)
        {
            $where[] = '`id`<>'. $id;
        }
        $check = $this->findOne($where);
        return $check ? true : false;
    }

    public function bind($data = [], $returnObject = false)
    {
        $row = [];
        $data = (array) $data;
        $fields = $this->getFields();
        $skips = isset($data['id']) && $data['id'] ? ['created_at', 'email_verified_at', 'remember_token'] : ['id'];
        foreach ($fields as $key => $field)
        {
            if (!in_array($key, $skips))
            {
                $default = isset($field['default']) ? $field['default'] : '';
                $row[$key] = isset($data[$key]) ? $data[$key] : $default;
            }
        }

        if (isset($data['id']) && $data['id'])
        {
            $row['readyUpdate'] = true;
        }
        else{
            $row['readyNew'] = true;
        }

        return $returnObject ? (object)$row : $row;
    }
}