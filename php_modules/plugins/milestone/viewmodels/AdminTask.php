<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a basic viewmodel
 * 
 */
namespace DTM\plugins\milestone\viewmodels; 

use SPT\View\Gui\Form;
use SPT\View\Gui\Listing;
use SPT\Web\ViewModel;

class AdminTask extends ViewModel
{
    public static function register()
    {
        return [
            'layouts.backend.task.form'
        ];
    }

    public function form()
    {
        $request = $this->container->get('request');
        $RequestEntity = $this->container->get('RequestEntity');
        $MilestoneEntity = $this->container->get('MilestoneEntity');
        $router = $this->container->get('router');

        $urlVars = $request->get('urlVars');
        $request_id = (int) $urlVars['request_id'];

        $form = new Form($this->getFormFields(), []);
        $request = $RequestEntity->findByPK($request_id);
        $milestone = $request ? $MilestoneEntity->findByPK($request['milestone_id']) : ['title' => '', 'id' => 0];

        return [
            'form' => $form,
            'url' => $router->url(),
            'link_list' => $router->url('tasks/'. $request_id),
            'link_form' => $router->url('task/'. $request_id),
        ];
    }

    public function getFormFields()
    {
        $fields = [
            'id' => ['hidden'],
            'title' => [
                'text',
                'placeholder' => 'New Task',
                'showLabel' => false,
                'formClass' => 'form-control h-50-px fw-bold rounded-0 fs-3',
                'required' => 'required'
            ],
            'url' => ['text',
                'placeholder' => 'Enter Url',
                'showLabel' => false,
                'formClass' => 'form-control rounded-0 border border-1 py-1 fs-4-5',
            ],
            'token' => ['hidden',
                'default' => $this->container->get('token')->value(),
            ],
        ];

        return $fields;
    }
}
