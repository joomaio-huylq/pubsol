<?php
/**
 * SPT software - homeController
 *
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic controller
 *
 */

namespace App\plugins\report_timeline\controllers;

use DTM\report\libraries\ReportController;
use SPT\Web\ControllerMVVM;

class report extends ReportController 
{
    public function detail()
    {
        $this->app->set('layout', 'backend.report.form');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function preview()
    {
        $this->app->set('layout', 'backend.report.preview');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function add()
    {
        //check title sprint
        $save_close = $this->request->post->get('save_close', '', 'string');
        if (!$title)
        {
            $this->session->set('flashMsg', 'Error: Title is required! ');
            return $this->app->redirect(
                $this->router->url('new-report/timeline')
            );
        }
        
        $try = $this->TimelineModel->add([
            'title' => $title,
            'status' => 1,
            'milestones' => implode(',', $milestone),
            'tags' => implode(',', $tags),
        ]);
        
        if( !$try )
        {
            $this->session->set('flashMsg', $this->TimelineModel->getError());
            return $this->app->redirect(
                $this->router->url('new-report/timeline')
            );
        }
        else
        {
            // save struct
            $this->session->set('flashMsg', 'Created Successfully!');
            $link = $save_close ? 'reports' : 'report/detail/'. $newId;
            return $this->app->redirect(
                $this->router->url($link)
            );
        }
    }

    public function update()
    {
        $id = $this->validateID();

        // TODO valid the request input
        $save_close = $this->request->post->get('save_close', '', 'string');

        $try = $this->TimelineModel->update([
            'id' => $id,
            'title' => $title,
            'status' => 1,
            'milestones' => implode(',', $milestone),
            'tags' => implode(',', $tags),
        ]);
        
        if($try)
        {
            $this->session->set('flashMsg', 'Updated successfully');
            $link = $save_close ? 'reports' : 'report/detail/'. $id;
            return $this->app->redirect(
                $this->router->url($link)
            );
        }
        else
        {
            $this->session->set('flashMsg', $this->TimelineModel->getError());
            return $this->app->redirect(
                $this->router->url('report/detail/'. $ids)
            );
        }
    }

    public function delete()
    {
        $ids = $this->validateID();

        $count = 0;
        if( is_array($ids))
        {
            foreach($ids as $id)
            {
                //Delete file in source
                if( $this->TimelineModel->remove($id) )
                {
                    $count++;
                }
            }
        }
        elseif( is_numeric($ids) )
        {
            if( $this->TimelineModel->remove($ids) )
            {
                $count++;
            }
        }


        $this->session->set('flashMsg', $count.' deleted record(s)');
        return $this->app->redirect(
            $this->router->url('reports'),
        );
    }

    public function validateID()
    {
        $urlVars = $this->request->get('urlVars');
        $id = (int) $urlVars['id'];

        if(empty($id))
        {
            $ids = $this->request->post->get('ids', [], 'array');
            if(count($ids)) return $ids;

            $this->session->set('flashMsg', 'Invalid timeline diagram');
            return $this->app->redirect(
                $this->router->url('reports'),
            );
        }

        return $id;
    }
}
