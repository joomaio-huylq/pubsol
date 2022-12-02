<?php
/**
 * SPT software - homeController
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic controller
 * 
 */

namespace App\plugins\milestone\controllers;

use SPT\MVC\JDIContainer\MVController;

class Document extends Admin 
{
    public function detail()
    {
        $this->isLoggedIn();
        
        $this->app->set('layout', 'backend.document.form');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function add()
    {
        $this->isLoggedIn();
        $request_id = $this->validateRequestID();
        //check title sprint
        $description = $this->request->post->get('description', '', 'string');

        // TODO: validate new add
        $newId =  $this->DocumentEntity->add([
            'request_id' => $request_id,
            'description' => $description,
            'created_by' => $this->user->get('id'),
            'created_at' => date('Y-m-d H:i:s'),
            'modified_by' => $this->user->get('id'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);

        if( !$newId )
        {
            $msg = 'Error: Update Failed!';
            $this->session->set('flashMsg', $msg);
            $this->app->redirect(
                $this->router->url('admin/document/'. $request_id)
            );
        }
        else
        {
            $try = $this->DocumentHistoryEntity->add([
                'document_id' => $newId,
                'modified_by' => $this->user->get('id'),
                'modified_at' => date('Y-m-d H:i:s')
            ]);
            $this->session->set('flashMsg', 'Update Success!');
            $this->app->redirect(
                $this->router->url('admin/document/'. $request_id)
            );
        }
    }

    public function update()
    {
        $request_id = $this->validateRequestID();
        // TODO valid the request input
        $find = $this->DocumentEntity->findOne(['request_id = '. $request_id]);
        if(!$find)
        {
            $this->session->set('flashMsg', 'Invalid Document');
            $this->app->redirect(
                $this->router->url('admin/document/'. $request_id)
            );
        }
        $description = $this->request->post->get('description', '', 'string');

        $try = $this->DocumentEntity->update([
            'description' => $description,
            'modified_by' => $this->user->get('id'),
            'modified_at' => date('Y-m-d H:i:s'),
            'id' => $find['id'],
        ]);
        
        if($try) 
        {
            $try = $this->DocumentHistoryEntity->add([
                'document_id' => $find['id'],
                'modified_by' => $this->user->get('id'),
                'modified_at' => date('Y-m-d H:i:s')
            ]);
            $this->session->set('flashMsg', 'Update Successfully');
            $this->app->redirect(
                $this->router->url('admin/document/'. $request_id)
            );
        }
        else
        {
            $msg = 'Error: Update Failed';
            $this->session->set('flashMsg', $msg);
            $this->app->redirect(
                $this->router->url('admin/document/'. $request_id)
            );
        }
    }

    public function delete()
    {
        // no delete
    }

    public function validateRequestID()
    {
        $this->isLoggedIn();

        $urlVars = $this->request->get('urlVars');
        $id = (int) $urlVars['request_id'];

        if(empty($id))
        {
            $this->session->set('flashMsg', 'Invalid Milestone');
            $this->app->redirect(
                $this->router->url('admin/milestones'),
            );
        }

        return $id;
    }
}