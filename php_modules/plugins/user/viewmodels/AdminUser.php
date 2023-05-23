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

class AdminUser extends ViewModel
{
    public static function register()
    {
        return [
            'layouts.backend.user.login',
            'layouts.backend.user.form',
            'layouts.backend.user.profile',
        ];
    }

    public function login($layoutData, &$viewData)
    {
        $app = $this->container->get('app');
        $GoogleModel = $this->container->get('GoogleModel');
        $link_google_auth = '';
        if (is_object($GoogleModel))
        {
            $link_google_auth = $GoogleModel->getUrlLogin();
        }

        return [
            'url' =>  $app->getRouter()->url(),
            'link_login' =>  $app->getRouter()->url('login'),
            'link_google_auth' =>  $link_google_auth,
        ];
    }

    public function form()
    {
        $request = $this->container->get('request');
        $UserEntity = $this->container->get('UserEntity');
        $router = $this->container->get('router');

        $urlVars = $request->get('urlVars');
        $id = (int) $urlVars['id'];

        $data = $id ? $UserEntity->findByPK($id) : [];
        if ($data)
        {
            $data['password'] = '';
            $groups = $UserEntity->getGroups($data['id']);
            foreach ($groups as $group)
            {
                $data['groups'][] = $group['group_id'];
            }
        }
        $form = new Form($this->getFormFields($id), $data);

        return [
           'id' => $id,
           'form' => $form,
           'data' => $data,
           'title_page' => $data ? 'Update User' : 'New User',
           'url' => $router->url(),
           'link_list' => $router->url('users'),
           'link_form' => $router->url('user'),
        ];
    }

    public function getFormFields($id)
    {
        $GroupEntity = $this->container->get('GroupEntity');
        $token = $this->container->get('token');
        
        $groups = $GroupEntity->list(0, 0, [], 'name asc');
        $options = [];
        foreach ($groups as $group)
        {
            $options[] = [
                'text' => $group['name'],
                'value' => $group['id'],
            ];
        }

        $fields = [
            'id' => ['hidden'],
            'name' => [
                'text',
                'placeholder' => 'Enter Name',
                'showLabel' => false,
                'formClass' => 'form-control',
                'required' => 'required'
            ],
            'username' => ['text',
                'placeholder' => 'Enter User Name',
                'showLabel' => false,
                'formClass' => 'form-control',
                'required' => 'required',
            ],
            'email' => ['email',
                'formClass' => 'form-control',
                'placeholder' => 'Enter Email',
                'showLabel' => false,
                // 'required' => 'required'
            ],
            'password' => ['password',
                'placeholder' => 'Enter Password',
                'showLabel' => false,
                'formClass' => 'form-control'
            ],
            'confirm_password' => ['password',
                'placeholder' => 'Enter Confirm Password',
                'showLabel' => false,
                'formClass' => 'form-control'
            ],
            'status' => ['option',
                'showLabel' => false,
                'type' => 'radio',
                'formClass' => '',
                'default' => 1,
                'options' => [
                    ['text'=>'Active', 'value'=>1],
                    ['text'=>'Inactive', 'value'=>0]
                ]
            ],
            'groups' => ['option',
                'options' => $options,
                'type' => 'multiselect',
                'showLabel' => false,
                'formClass' => 'form-select',
            ],
            'token' => ['hidden',
                'default' => $token->getToken(),
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

    public function profile()
    {
        $user = $this->container->get('user');
        $UserEntity = $this->container->get('UserEntity');
        $router = $this->container->get('router');
        $id = $user->get('id');
        $data = $id ? $UserEntity->findByPK($id) : [];
        if ($data)
        {
            $data['password'] = '';
            $data['groups'] = [];

            $groups = $UserEntity->getGroups($data['id']);
            foreach ($groups as $group)
            {
                $data['groups'][] = $group['group_name'];
            }
            $data['groups'] = implode(', ',  $data['groups']);
        }
        
        $form = new Form($this->getFormFieldsProfile(), $data);

        return [
            'form' => $form,
            'data' => $data,
            'title_page' => 'My Profile',
            'url' => $router->url(),
            'link_list' => $router->url('profile'),
            'link_form' => $router->url('profile'),
        ];
    }

    public function getFormFieldsProfile()
    {
        $fields = [
            'id' => ['hidden'],
            'name' => [
                'text',
                'placeholder' => 'Enter Name',
                'showLabel' => false,
                'formClass' => 'form-control',
                'required' => 'required'
            ],
            'username' => ['readonly',
                'placeholder' => 'Enter User Name',
                'showLabel' => false,
                'disabled' => 'disabled',
                'formClass' => 'form-control',
            ],
            'email' => ['email',
                'formClass' => 'form-control',
                'placeholder' => 'Enter Email',
                'showLabel' => false,
                // 'required' => 'required'
            ],
            'password' => ['password',
                'placeholder' => 'Enter Password',
                'showLabel' => false,
                'formClass' => 'form-control'
            ],
            'confirm_password' => ['password',
                'placeholder' => 'Enter Confirm Password',
                'showLabel' => false,
                'formClass' => 'form-control'
            ],
            'groups' => [
                'type' => 'readonly',
                'showLabel' => false,
            ],
            'token' => ['hidden',
                'default' => $this->container->get('token')->getToken(),
            ],
        ];

        return $fields;
    }
}
