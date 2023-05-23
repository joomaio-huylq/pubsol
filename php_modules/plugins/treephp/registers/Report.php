<?php
namespace App\plugins\treephp\registers;

use SPT\Application\IApp;
use SPT\Support\Loader;

class Report
{
    public static function registerType( IApp $app )
    {
        $container = $app->getContainer();
        $router = $container->get('router');
        $permission = $container->exists('permission') ? $container->get('permission') : null;
        $allow = $permission ? $permission->checkPermission(['treephp_manager', 'treephp_read']) : true;
        if (!$allow)
        {
            return [];
        }
        
        return [
            'tree_php' => [
                'title' => 'Tree Of Note',
                'new_link' => $router->url('tree-php/0'),
                'detail_link' => $router->url('tree-php'),
                'remove_object' => 'TreePhpModel',
            ],
        ];
    }
}