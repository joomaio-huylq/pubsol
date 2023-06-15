<?php


namespace App\plugins\milestone\controllers;

use SPT\Web\ControllerMVVM;
use SPT\Response;

class Document extends ControllerMVVM 
{
    public function detail()
    {
        $request_id = $this->validateRequestID();
        $request = $this->RequestEntity->findByPK($request_id);
        if (!$request)
        {
            $this->session->set('flashMsg', 'Invalid Request');
            return $this->app->redirect(
                $this->router->url('milestones')
            );
        }
        $this->app->set('layout', 'backend.document.form');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function save()
    {
        $request_id = $this->validateRequestID();
        $description = $this->request->post->get('description', '', 'string');

        $data = [
            'description' => $description, 
            'request_id' => $request_id,
        ];

        $check  = $this->DocumentModel->save($data);
       
        $msg = $try ? 'Update Document Successfully!' : 'Error: Update Document Failed!';
        $status = $try ? 'ok' : 'fail';

        $this->app->set('format', 'json');
        $this->set('result', $status);
        $this->set('message', $msg);
        return ;
    }

    public function validateRequestID()
    {
        
        $urlVars = $this->request->get('urlVars');
        $id = (int) $urlVars['request_id'];

        if(empty($id))
        {
            $this->session->set('flashMsg', 'Invalid Request');
            return $this->app->redirect(
                $this->router->url('milestones'),
            );
        }

        return $id;
    }

    public function getHistory()
    {
        $urlVars = $this->request->get('urlVars');
        $request_id = (int) $urlVars['request_id'];

        $list = $this->DocumentModel->getHistory($request_id);
        $list = $list ? $list : [];

        $this->app->set('format', 'json');
        $this->set('result', 'ok');
        $this->set('list', $list);
        return ;
    }

    public function getComment()
    {
        $urlVars = $this->request->get('urlVars');
        $request_id = (int) $urlVars['request_id'];

        $list = $this->DocumentModel->getComment($request_id);
        $list = $list ? $list : [];

        $this->app->set('format', 'json');
        $this->set('result', 'ok');
        $this->set('list', $list);
        return ;
    }
}