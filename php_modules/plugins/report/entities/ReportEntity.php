<?php
/**
 * SPT software - Entity
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic entity
 * 
 */

namespace App\plugins\report\entities;

use SPT\Storage\DB\Entity;

class ReportEntity extends Entity
{
    protected $table = '#__reports';
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
            'title' => [
                'type' => 'varchar',
                'limit' => 255,
            ],
            'status' => [
                'type' => 'tinyint',
            ],
            'type' => [
                'type' => 'varchar',
                'limit' => 255,
            ],
            'data' => [
                'type' => 'text',
                'null' => 'YES',
            ],
            'assignment' => [
                'type' => 'text',
                'null' => 'YES',
            ],
            'created_at' => [
                'type' => 'datetime',
                'default' => 'NOW()',
            ],
            'created_by' => [
                'type' => 'int',
                'option' => 'unsigned',
            ],
            'modified_at' => [
                'type' => 'datetime',
                'default' => 'NOW()',
            ],
            'modified_by' => [
                'type' => 'int',
                'option' => 'unsigned',
            ],
        ];
    }
}