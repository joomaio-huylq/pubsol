<?php namespace App\plugins\member\controllers;

use SPT\Web\ControllerMVVM;
use SPT\Response;

class member extends ControllerMVVM 
{
    public function list()
    {
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
        $this->app->set('layout', 'backend.member.list');
    }

    public function add()
    {
        // get input data
        $data = [
            'name' => $this->request->post->get('name', '', 'string'),
            'email' => $this->request->post->get('email', '', 'string'),
            'password' => $this->request->post->get('password', '', 'string')
        ];

        // check email exist
        $check_email = $this->MemberEntity->check_email_exist($data['email']);
        if ($check_email)
        {
            $this->session->setform('member', $data);
            $this->session->set('flashMsg', 'Error: Email is exist! Please enter another email!');
            return $this->app->redirect(
                $this->router->url('members')
            );
        }

        // validate input data
        $validated_data = $this->MemberEntity->validate($data);
        if (!$validated_data)
        {
            $this->session->setform('member', $data);
            $this->session->set('flashMsg', 'Error: '. $this->MemberEntity->getError());
            return $this->app->redirect(
                $this->router->url('members')
            );
        }
        
        // create new member
        $newId = $this->MemberModel->add($validated_data);
        
        if(!$newId)
        {
            $this->session->setform('member', $data);
            $this->session->set('flashMsg', 'Error: '. $this->MemberModel->getError());
            return $this->app->redirect(
                $this->router->url('members')
            );
        }
        else
        {
            $this->session->set('flashMsg', 'Created Successfully!');
            return $this->app->redirect(
                $this->router->url('members')
            );
        }
    }

    public function update()
    {
        // validate member id
        $ids = $this->validateID(); 

        if(is_array($ids) && $ids != null)
        {
            $this->session->set('flashMsg', 'Invalid Members');
            return $this->app->redirect(
                $this->router->url('members')
            );
        }
        if(is_numeric($ids) && $ids)
        {
            // get input data
            $data = [
                'name' => $this->request->post->get('name', '', 'string'),
                'email' => $this->request->post->get('email', '', 'string'),
                'password' => $this->request->post->get('password', '', 'string'),
                'id' => $ids,
            ];

            // check email exist
            $check_email = $this->MemberEntity->check_email_exist($data['email'], $data['id']);
            if ($check_email)
            {
                $this->session->setform('member', $data);
                $this->session->set('flashMsg', 'Error: Email is exist! Please enter another email!');
                return $this->app->redirect(
                    $this->router->url('members')
                );
            }

            // validate input data
            $validated_data = $this->MemberEntity->validate($data);
            if (!$validated_data)
            {
                $this->session->setform('member', $data);
                $this->session->set('flashMsg', 'Error: '. $this->MemberEntity->getError());
                return $this->app->redirect(
                    $this->router->url('members')
                );
            }
    
            // update member info
            $try = $this->MemberModel->update($validated_data);
            
            if($try) 
            {
                $this->session->set('flashMsg', 'Updated Successfully');
                return $this->app->redirect(
                    $this->router->url('members')
                );
            }
            else
            {
                $this->session->set('flashMsg', 'Error: '. $this->MemberModel->getError());
                return $this->app->redirect(
                    $this->router->url('members')
                );
            }
        }
    }

    public function delete()
    {
        // validate member id
        $ids = $this->validateID();
        
        $count = 0;
        if(is_array($ids))
        {
            // delete list of members by id
            foreach($ids as $id)
            {
                if($this->MemberModel->remove($id))
                {
                    // count number of deleted members
                    $count++;
                }
            }
        }
        elseif(is_numeric($ids))
        {
            if( $this->MemberModel->remove($ids))
            {
                $count++;
            }
        }  

        $this->session->set('flashMsg', $count.' deleted record(s)');
        return $this->app->redirect(
            $this->router->url('members'), 
        );
    }

    public function validateID()
    {
        
        $urlVars = $this->request->get('urlVars');
        $id = $urlVars ? (int) $urlVars['id'] : 0;

        if(empty($id))
        {
            $ids = $this->request->post->get('ids', [], 'array');
            if(count($ids)) return $ids;

            $this->session->set('flashMsg', 'Invalid Member');
            return $this->app->redirect(
                $this->router->url('members'),
            );
        }

        return $id;
    }

}