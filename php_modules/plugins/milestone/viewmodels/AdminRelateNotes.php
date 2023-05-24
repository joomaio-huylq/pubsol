<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a basic viewmodel
 * 
 */
namespace App\plugins\milestone\viewmodels; 

use SPT\View\Gui\Form;
use SPT\View\Gui\Listing;
use SPT\Web\MVVM\ViewModel;

class AdminRelateNotes extends ViewModel
{   
    public static function register()
    {
        return [
            'layouts.backend.relate_note.list',
            'layouts.backend.relate_note.list.filter',
            'layouts.backend.relate_note.list.javascript',
        ];
    
    }

    public function list()
    {
        $request =  $this->container->get('request');
        $NoteEntity =  $this->container->get('NoteEntity');
        $TagEntity =  $this->container->get('TagEntity');
        $session =  $this->container->get('session');
        $user =  $this->container->get('user');
        $token =  $this->container->get('token');
        $router =  $this->container->get('router');
        $RequestEntity =  $this->container->get('RequestEntity');
        $RelateNoteEntity =  $this->container->get('RelateNoteEntity');
        $MilestoneEntity =  $this->container->get('MilestoneEntity');
        $VersionEntity =  $this->container->get('VersionEntity');

        $filter = $this->filter()['form'];
        $urlVars = $request->get('urlVars');
        $request_id = (int) $urlVars['request_id'];

        $limit  = $filter->getField('limit')->value;
        $sort   = $filter->getField('sort')->value;
        $search = trim($filter->getField('search')->value);
        $page   = $request->get->get('page', 1);
        if ($page <= 0) $page = 1;

        $where = [];
        $where[] = ['request_id = '. $request_id];

        if( !empty($search) )
        {
            $where[] = "(`title` LIKE '%".$search."%')";
        }
        
        $start  = ($page-1) * $limit;
        $sort = $sort ? $sort : 'title asc';

        $result = $RelateNoteEntity->list( 0, 0, $where, 0);
        $total = $RelateNoteEntity->getListTotal();
        if (!$result)
        {
            $result = [];
            $total = 0;
        }
        $request = $RequestEntity->findByPK($request_id);
        $milestone = $request ? $MilestoneEntity->findByPK($request['milestone_id']) : ['title' => '', 'id' => 0];
        $title_page_relate_note = 'Related Notes';

        $note_exist = $this->container->exists('NoteEntity');

        $notes = [];
        foreach ($result as $index => &$item)
        {
            $note_tmp = false;
            if ($note_exist)
            {
                $note_tmp = $NoteEntity->findByPK($item['note_id']);
                if ($note_tmp)
                {
                    $item['title'] = $note_tmp['title'];
                    $item['type'] = $note_tmp['type'];
                    $item['description'] = strip_tags((string) $note_tmp['description']) ;
                    $item['tags'] = $note_tmp['tags'] ;
                }
                else
                {
                    unset($result[$index]);
                }

                if (!empty($item['tags'])){
                    $t1 = $where = [];
                    $where[] = "(`id` IN (".$item['tags'].") )";
                    $t2 = $TagEntity->list(0, 1000, $where,'','`name`');
    
                    foreach ($t2 as $i) $t1[] = $i['name'];
    
                    $item['tags'] = implode(', ', $t1);
                }
            }

            if (strlen($item['description']) > 100)
            {
                $item['description'] = substr($item['description'], 0, 100) .' ...';
            }

            if ($note_tmp)
            {
                $notes[] = $item;
            }
        }

        $result = $notes;
        $version_lastest = $VersionEntity->list(0, 1, [], 'created_at desc');
        $version_lastest = $version_lastest ? $version_lastest[0]['version'] : '0.0.0';
        $tmp_request = $RequestEntity->list(0, 0, ['id = '.$request_id], 0);
        foreach($tmp_request as $tmp_item) {
        }

        $status = false;

        $list   = new Listing($result, $total, $limit, $this->getColumns());
        return [
            'request_id' => $request_id,
            'list' => $list,
            'page' => $page,
            'start' => $start,
            'status' => $status,
            'sort' => $sort,
            'user_id' => $user->get('id'),
            'url' => $router->url(),
            'link_list' => $router->url('relate-notes/' . $request_id),
            'link_note' => $router->url('note'),
            'link_list_relate_note' => $router->url('relate-notes/' . $request_id),
            'title_page_relate_note' => $title_page_relate_note,
            'token' => $this->container->get('token')->getToken(),
        ];
    }

    public function javascript()
    {
        $request =  $this->container->get('request');
        $user =  $this->container->get('user');
        $token =  $this->container->get('token');
        $router =  $this->container->get('router');

        $filter = $this->filter()['form'];
        $urlVars = $request->get('urlVars');
        $request_id = (int) $urlVars['request_id'];

        return [
            'request_id' => $request_id,
            'link_list' => $router->url('relate-notes/' . $request_id),
            'link_form' => $router->url('relate-note/'. $request_id),
            'link_note' => $router->url('note'),
            'link_list_relate_note' => $router->url('relate-notes/' . $request_id),
            'token' => $this->container->get('token')->getToken(),
        ];
    }

    public function getColumns()
    {
        return [
            'num' => '#',
            'title' => 'Title',
            'status' => 'Status',
            'created_at' => 'Created at',
            'col_last' => ' ',
        ];
    }

    protected $_filter;
    public function filter()
    {
        if( null === $this->_filter):
            $data = [
                'search' => $this->state('search', '', '', 'post', 'relate_note.search'),
                'limit' => $this->state('limit', 10, 'int', 'post', 'relate_note.limit'),
                'sort' => $this->state('sort', '', '', 'post', 'relate_note.sort')
            ];

            $filter = new Form($this->getFilterFields(), $data);
            $this->_filter = $filter;
        endif;

        return ['form' => $this->_filter];
    }

    public function getFilterFields()
    {
        return [
            'search' => ['text',
                'default' => '',
                'showLabel' => false,
                'formClass' => 'form-control h-full input_common w_full_475',
                'placeholder' => 'Search..'
            ],
            'limit' => ['option',
                'formClass' => 'form-select',
                'default' => 10,
                'options' => [ 5, 10, 20, 50],
                'showLabel' => false
            ],
            'sort' => ['option',
                'formClass' => 'form-select',
                'default' => 'title asc',
                'options' => [
                    ['text' => 'Title ascending', 'value' => 'title asc'],
                    ['text' => 'Title descending', 'value' => 'title desc'],
                ],
                'showLabel' => false
            ]
        ];
    }

    public function state($key, $default='', $format='cmd', $request_type='post', $sessionName='')
    {
        if(empty($sessionName)) $sessionName = $key;
        $session = $this->container->get('session');
        $request = $this->container->get('request');

        $old = $session->get($sessionName, $default);

        if( !is_object( $request->{$request_type} ) )
        {
            $var = null;
        }
        else
        {
            $var = $request->{$request_type}->get($key, $old, $format);
            $session->set($sessionName, $var);
        }

        return $var;
    }
}
