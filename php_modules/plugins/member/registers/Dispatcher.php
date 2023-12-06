<?php
namespace App\plugins\member\registers;

use SPT\Application\IApp;
use SPT\Response;

class Dispatcher
{
    public static function dispatch(IApp $app)
    {   
        // Check Permission
        $app->plgLoad('permission', 'CheckSession');

        $cName = $app->get('controller');
        $fName = $app->get('function');

        $app->set('theme', $app->cf('adminTheme'));

        $controller = 'App\plugins\member\controllers\\'. $cName;
        if(!class_exists($controller))
        {
            $app->raiseError('Invalid controller '. $cName);
        }

        $controller = new $controller($app->getContainer());
        $controller->{$fName}();

        $fName = 'to'. ucfirst($app->get('format', 'html'));

        $app->finalize(
            $controller->{$fName}()
        );
    }
}