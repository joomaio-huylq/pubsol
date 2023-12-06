<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a basic viewmodel
 * 
 */
namespace App\plugins\member\viewmodels; 

use SPT\Web\Gui\Form;
use SPT\Web\Gui\Listing;
use SPT\Web\ViewModel;

class AdminMember extends ViewModel
{
    public static function register()
    {
        return [
            'layout'=>'backend.member.form'
        ];
    }

    public function form()
    {
        $form = new Form($this->getFormFields(), []);
        $router = $this->container->get('router');
        return [
            'form' => $form,
            'url' => $router->url(),
            'link_list' => $router->url('members'),
            'link_form' => $router->url('member'),
        ];
    }

    public function getFormFields()
    {
        $fields = [
            'id' => ['hidden'],
            'name' => [
                'text',
                'placeholder' => 'Enter Your Name',
                'showLabel' => false,
                'formClass' => 'form-control rounded-0 border border-1 py-1 fs-4-5',
                'required' => 'required'
            ],
            'email' => [
                'text',
                'placeholder' => 'Enter Your Email',
                'showLabel' => false,
                'formClass' => 'form-control rounded-0 border border-1 py-1 fs-4-5',
                'required' => 'required'
            ],
            'password' => [
                'text',
                'placeholder' => 'Enter Tour Password',
                'showLabel' => false,
                'formClass' => 'form-control rounded-0 border border-1 py-1 fs-4-5',
                'required' => 'required'
            ],
            'token' => ['hidden',
                'default' => $this->token->value(),
            ],
        ];

        return $fields;
    }
}
