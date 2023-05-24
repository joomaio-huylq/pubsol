<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a basic viewmodel
 * 
 */
namespace App\plugins\user\viewmodels; 

use SPT\View\Gui\Form;
use SPT\View\Gui\Listing;
use SPT\Web\MVVM\ViewModel;

class AdminGroup extends ViewModel
{
    public static function register()
    {
        return [
            'layouts.backend.usergroup.form'
        ];
    }

    public function form()
    {
        $request = $this->container->get('request');
        $GroupEntity = $this->container->get('GroupEntity');
        $router = $this->container->get('router');

        $urlVars = $request->get('urlVars');
        $id = (int) $urlVars['id'];

        $data = $id ? $GroupEntity->findByPK($id) : [];
        if (isset($data['access']) && $data['access'])
        {
            $data['access'] = (array) json_decode($data['access']);
        }
        $form = new Form($this->getFormFields($id), $data);

        return [
            'id' => $id,
            'form' => $form,
            'data' => $data,
            'title_page' => $data ? 'Update User Group' : 'New User Group',
            'url' => $router->url(),
            'link_list' => $router->url('user-groups'),
            'link_form' => $router->url('user-group'),
        ];
    }

    public function getFormFields($id)
    {
        $key_access = [];
        if ($this->container->exists('permission'))
        {
            $key_access = $this->container->get('permission')->getAccess();
        }
        $option = [];
        foreach ($key_access as $key)
        {
            $option[] = [
                'text' => $key,
                'value' => $key,
            ];
        }

        $fields = [
            'id' => ['hidden'],
            'name' => ['text',
                'showLabel' => false,
                'formClass' => 'form-control',
                'required' => 'required'
            ],
            'description' => ['textarea',
                'formClass' => 'form-control',
                'showLabel' => false,
                'placeholder' => ''
            ],
            'access' => ['option',
                'showLabel' => false,
                'placeholder' => 'Select Right Access',
                'type' => 'multiselect',
                'formClass' => 'form-select',
                'options' => $option
            ],
            'status' => ['option',
                'type' => 'radio',
                'showLabel' => false,
                'formClass' => '',
                'default' => 1,
                'options' => [
                    ['text'=>'Yes', 'value'=>1],
                    ['text'=>'No', 'value'=>0]
                ]
            ],
            'token' => ['hidden',
                'default' => $this->container->get('token')->getToken(),
            ],
        ];

        if($id)
        {
            $fields['modified_at'] = ['readonly'];
            $fields['modified_by'] = ['readonly'];
            $fields['created_at'] = ['readonly'];
            $fields['created_by'] = ['readonly'];
        }
        else
        {
            $fields['password']['required'] = 'required';
            $fields['confirm_password']['required'] = 'required';
            $fields['modified_at'] = ['hidden'];
            $fields['modified_by'] = ['hidden'];
            $fields['created_at'] = ['hidden'];
            $fields['created_by'] = ['hidden'];
        }

        return $fields;
    }
}
