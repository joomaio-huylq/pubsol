<?php
/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a basic viewmodel
 * 
 */
namespace App\plugins\report\viewmodels; 

use SPT\View\Gui\Form;
use SPT\View\Gui\Listing;
use SPT\Web\ViewModel;

class AdminDiagrams extends ViewModel
{
    public static function register()
    {
        return [
            'layouts.backend.diagram.list',
            'layouts.backend.diagram.list.row',
            'layouts.backend.diagram.list.filter',
        ];
    }
    
    public function list()
    {
        $request = $this->container->get('request');
        $UserEntity = $this->container->get('UserEntity');
        $ReportModel = $this->container->get('ReportModel');
        $session = $this->container->get('session');
        $user = $this->container->get('user');
        $router = $this->container->get('router');
        $DiagramEntity = $this->container->get('DiagramEntity');

        $filter = $this->filter()['form'];

        $limit  = $filter->getField('limit')->value;
        $sort   = $filter->getField('sort')->value;
        $search = trim($filter->getField('search')->value);
        $status = $filter->getField('status')->value;
        $page   = $request->get->get('page', 1);
        if ($page <= 0) $page = 1;

        $where = [];
        

        if( !empty($search) )
        {
            $where[] = "(`title` LIKE '%". $search ."%') ";
        }
        
        if(is_numeric($status))
        {
            $where[] = '`status`='. $status;
        }

        $start  = ($page-1) * $limit;
        $sort = $sort ? $sort : 'title asc';

        $result = $DiagramEntity->list( $start, $limit, $where, $sort);
        $total = $DiagramEntity->getListTotal();
        if (!$result)
        {
            $result = [];
            $total = 0;
            if( !empty($search) )
            {
                $session->set('flashMsg', 'Not Found Report');
            }
        }

        $types = $ReportModel->getTypes();

        foreach($result as &$item)
        {
            $item['report_type'] = isset($types[$item['report_type']]) ? $types[$item['report_type']] : $item['report_type'];
            $user_tmp = $UserEntity->findByPK($item['created_by']);
            $item['auth'] = $user_tmp ? $user_tmp['name'] : '';
            $item['created_at'] = $item['created_at'] && $item['created_at'] != '0000-00-00 00:00:00' ? date('d-m-Y', strtotime($item['created_at'])) : '';
            
            $assigns = $item['assignment'] ? json_decode($item['assignment']) : [];
            $assign_tmp = [];
            $selected_tmp = [];
            foreach($assigns as $assign)
            {
                $user_tmp = $UserEntity->findByPK($assign);
                if ($user_tmp)
                {
                    $assign_tmp[] = $user_tmp['name'];
                    $selected_tmp[] = [
                        'id' => $assign,
                        'name' => $user_tmp['name'],
                    ];
                }
            }
            $item['assign'] = implode(', ', $assign_tmp);
            $item['assignment'] = json_encode($selected_tmp);
        }

        $limit = $limit == 0 ? $total : $limit;
        $list   = new Listing($result, $total, $limit, $this->getColumns() );
        return [
            'list' => $list,
            'page' => $page,
            'start' => $start,
            'types' => $types,
            'sort' => $sort,
            'user_id' => $user->get('id'),
            'url' => $router->url(),
            'link_list' => $router->url('reports'),
            'title_page' => 'Report',
            'token' => $this->container->get('token')->value(),
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
                'search' => $this->state('search', '', '', 'post', 'report.search'),
                'status' => $this->state('status', '','', 'post', 'report.status'),
                'limit' => $this->state('limit', 20, 'int', 'post', 'report.limit'),
                'sort' => $this->state('sort', '', '', 'post', 'report.sort')
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
            'status' => ['option',
                'default' => '1',
                'formClass' => 'form-select',
                'options' => [
                    ['text' => '--', 'value' => ''],
                    ['text' => 'Show', 'value' => '1'],
                    ['text' => 'Hide', 'value' => '0'],
                ],
                'showLabel' => false
            ],
            'limit' => ['option',
                'formClass' => 'form-select',
                'default' => 20,
                'options' => [
                    ['text' => '20', 'value' => 20],
                    ['text' => '50', 'value' => 50],
                    ['text' => 'All', 'value' => 0],
                ],
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

    public function row($layoutData, $viewData)
    {
        $row = $viewData['list']->getRow();
        return [
            'item' => $row,
            'index' => $viewData['list']->getIndex(),
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
