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
        $this->isLoggedIn();
        $request_id = $this->validateRequestID();

        $tmp_check = $this->checkVersion($request_id);
        if($tmp_check) {
            return $this->app->response([
                'result' => 'fail',
                'message' => 'Save Document Failed!'
            ],200);
        }

        $description = $this->request->post->get('description', '', 'string');

        $check  = $this->DocumentEntity->findOne(['request_id' => $request_id]);
        if ($check)
        {
            $try = $this->DocumentEntity->update([
                'description' => $description,
                'modified_by' => $this->user->get('id'),
                'modified_at' => date('Y-m-d H:i:s'),
                'id' => $check['id'],
            ]);
            $document_id = $check['id'];
        }
        else
        {
            $try =  $this->DocumentEntity->add([
                'request_id' => $request_id,
                'description' => $description,
                'created_by' => $this->user->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'modified_by' => $this->user->get('id'),
                'modified_at' => date('Y-m-d H:i:s')
            ]);

            $document_id = $try;
        }
       
        if( !$try )
        {
            $msg = 'Error: Update Document Failed!';

            return $this->app->response([
                'result' => 'fail',
                'message' => $msg,
            ], 200);
        }
        else
        {
            $try = $this->DocumentHistoryEntity->add([
                'document_id' => $document_id,
                'modified_by' => $this->user->get('id'),
                'modified_at' => date('Y-m-d H:i:s')
            ]);

            return $this->app->response([
                'result' => 'ok',
                'message' => 'Update Document Successfully!',
            ], 200);
        }
    }

    public function validateRequestID()
    {
        $this->isLoggedIn();

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
        $this->isLoggedIn();
        $urlVars = $this->request->get('urlVars');
        $request_id = (int) $urlVars['request_id'];

        $document = $this->DocumentEntity->findOne(['request_id' => $request_id]);
        $result = [];
        if ($document)
        {
            $list = $this->DocumentHistoryEntity->list(0 ,0 ,['document_id' => $document['id']]);
            $result = $list ? $list : [];
        }

        return $this->app->response(
            $result, 200);
    }

    public function getComment()
    {
        $this->isLoggedIn();
        $urlVars = $this->request->get('urlVars');
        $request_id = (int) $urlVars['request_id'];

        $document = $this->DocumentEntity->findOne(['request_id' => $request_id]);
        $result = [];
        if ($document)
        {
            $discussion = $this->DiscussionEntity->list(0, 0, ['document_id = '. $document['id']], 'sent_at asc');
            $discussion = $discussion ? $discussion : [];
            foreach ($discussion as &$item)
            {
                $user_tmp = $this->UserEntity->findByPK($item['user_id']);
                $item['user'] = $user_tmp ? $user_tmp['name'] : '';
            }

            $result = $discussion ? $discussion : [];
        }

        return $this->app->response(
            $result, 200);
    }

    public function checkVersion($request_id)
    {
        $version_lastest = $this->VersionEntity->list(0, 1, [], 'created_at desc');
        $version_lastest = $version_lastest ? $version_lastest[0]['version'] : '0.0.0';
        $tmp_request = $this->RequestEntity->list(0, 0, ['id = '.$request_id], 0);
        foreach($tmp_request as $item) {
        }
        if ($version_lastest > $item['version_id']) {
            return true;
        } else {
            return false;
        }
    }
}