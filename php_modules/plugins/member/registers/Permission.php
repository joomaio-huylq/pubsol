<?php

namespace App\plugins\member\registers;

use SPT\Application\IApp;

class Permission
{
    public static function registerAccess()
    {
        return [
            'member_manager', 'member_read', 'member_create', 'member_update', 'member_delete' 
        ];
    }
}
