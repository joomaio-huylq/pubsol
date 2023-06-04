<?php
namespace App\plugins\note\registers;

use SPT\Application\IApp; 
use SPT\File;

class Dispatcher
{
    public static function dispatch(IApp $app)
    {
        $app->plgLoad('permission', 'CheckSession'); 

        // prepare note
        $container = $app->getContainer();
        if (!$container->exists('file'))
        {
            $container->set('file', new File());
        }
        
        $cName = $app->get('controller');
        $fName = $app->get('function');
        
        $cName = ucfirst($cName);

        $controller = 'App\plugins\note\controllers\\'. $cName;
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