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

class AdminUsersVM extends ViewModel
{
    public static function register()
    {
        return [
            'layouts.backend.user' => [
                'list',
                'list.row',
                'list.filter'
            ]
        ];
    }

    public function list()
    {
        $filter = $this->filter();
        $request = $this->container->get('request');
        $user = $this->container->get('user');
        $UserEntity = $this->container->get('UserEntity');
        $UserGroupEntity = $this->container->get('UserGroupEntity');
        $session = $this->container->get('session');
        $router = $this->container->get('router');

        $limit  = $filter->getField('limit')->value;
        $sort   = $filter->getField('sort')->value;
        $search = $filter->getField('search')->value;
        $status = $filter->getField('status')->value;
        $filter_group = $filter->getField('group')->value;
        $page   = $request->get->get('page', 1, 'int');
        if ($page <= 0) $page = 1;

        $where = [];
        

        if( !empty($search) )
        {
            $where[] = "(`username` LIKE '%".$search."%' ".
                "OR `name` LIKE '%".$search."%' ".
                "OR `email` LIKE '%".$search."%' )";
        }
        if(is_numeric($status))
        {
            $where[] = '`status`='. $status;
        }

        $start  = ($page-1) * $limit;
        $sort = $sort ? $sort : 'name ASC';
        if ($filter_group)
        {
            $user_map = $UserGroupEntity->list(0, 0, ['group_id' => $filter_group]);
            $where_group[] = 0;
            foreach($user_map as $map)
            {
                $where_group[] = $map['user_id'];
            }
        
            $where[] = 'id IN ('. implode(',', $where_group) . ')';
        }

        $result = $UserEntity->list( $start, $limit, $where, $sort);
        $total = $UserEntity->getListTotal();

        if (!$result)
        {
            $result = [];
            $total = 0;
            if ($where)
            {
                $session->set('flashMsg', 'User note found');
            }
        }

        foreach( $result as $key => &$value )
        {
            $result[$key]['groups'] = $UserEntity->getGroups($value['id']);
        }

        $list   = new Listing($result, $total, $limit, $this->getColumns() );
        return [
            'list' => $list,
            'page' => $page,
            'start' => $start,
            'sort' => $sort,
            'user_id' => $user->get('id'),
            'link_list' => $router->url('users'), true,
            'link_form' => $router->url('user'), true,
            'title_page' => 'User Manager',
            'token' => $this->container->get('token'),
        ];
    }

    public function getColumns()
    {
        return [
            'num' => '#',
            'name' => 'Name',
            'username' => 'User name',
            'emal' => 'Email',
            'block' => 'Is block',
            'created_at' => 'Created at',
            'col_last' => ' ',
        ];
    }

    protected $_filter;
    public function filter()
    {
        if( null === $this->_filter):
            $data = [
                'search' => $this->state('search', '', '', 'post', 'users.search'),
                'status' => $this->state('status', '','', 'post', 'users.status'),
                'group' => $this->state('group', '','', 'post', 'users.group'),
                'limit' => $this->state('limit', 10, 'int', 'post', 'users.limit'),
                'sort' => $this->state('sort', '', '', 'post', 'users.sort')
            ];

            $filter = new Form($this->getFilterFields(), $data);
            $this->set('form', ['filter' => $filter], true);
            $this->set('dataform', $data, true);

            foreach($data as $k=>$v) $this->set($k, $v);
            $this->_filter = $filter;
        endif;

        return $this->_filter;
    }

    public function getFilterFields()
    {
        $groups = $this->container->get('GroupEntity')->list(0, 0, [], 'name asc');
        $options = [
            ['text' => 'Select Group', 'value' => ''],
        ];
        foreach ($groups as $group)
        {
            $options[] = [
                'text' => $group['name'],
                'value' => $group['id'],
            ];
        }

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
                    ['text' => 'Inactive', 'value' => '0'],
                    ['text' => 'Active', 'value' => '1']
                ],
                'showLabel' => false
            ],
            'limit' => ['option',
                'formClass' => 'form-select',
                'default' => 10,
                'options' => [ 5, 10, 20, 50],
                'showLabel' => false
            ],
            'group' => ['option',
                'formClass' => 'form-select',
                'options' => $options,
                'showLabel' => false
            ],
            'sort' => ['option',
                'formClass' => 'form-select',
                'default' => 'name asc',
                'options' => [
                    ['text' => 'Name ascending', 'value' => 'name asc'],
                    ['text' => 'Name descending', 'value' => 'name desc'],
                    ['text' => 'Email ascending', 'value' => 'email asc'],
                    ['text' => 'Email descending', 'value' => 'email desc'],
                    ['text' => 'Username ascending', 'value' => 'username asc'],
                    ['text' => 'Username descending', 'value' => 'username desc'],
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
}
