<?php
namespace App\plugins\member\registers;

use SPT\Application\IApp;
use SPT\Support\Loader;

class Menu
{
    public static function registerItem(IApp $app)
    {
        $container = $app->getContainer();
        $router = $container->get('router');
        $path_current = $router->get('actualPath');

        $permission = $container->exists('PermissionModel') ? $container->get('PermissionModel') : null;
        $allow = $permission ? $permission->checkPermission(['member_manager', 'member_read']) : true;
        if (!$allow)
        {
            return false;
        }

        $active = strpos($path_current, 'member') !== false ? 'active' : '';
        $menu = [
            [
                'link' => $router->url('members'),
                'title' => 'Members', 
                'icon' => '<i class="fa-solid fa-user"></i>',
                'class' => $active,
            ]
        ];
        

        return [
            'menu' => $menu,
            'order' => 10,
        ];
    }
}