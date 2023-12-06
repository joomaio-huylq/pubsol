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

class AdminMembers extends ViewModel
{
    public static function register()
    {
        return [
            'layout'=>[
                'backend.member.list',
                'backend.member.list.filter'
            ]
        ];
    }

    public function list()
    {
        $request = $this->container->get('request');
        $session = $this->container->get('session');
        $router = $this->container->get('router');
        $MemberEntity = $this->container->get('MemberEntity');
        $MemberModel = $this->container->get('MemberModel');

        // init filter
        $filter = $this->filter()['form'];
        // get filter data
        $limit  = $filter->getField('limit')->value;
        $sort   = $filter->getField('sort')->value;
        $search = trim($filter->getField('search')->value);
        
        $page = $this->state('page', 1, 'int', 'get', 'member.page');
        if ($page <= 0) $page = 1;
        $method = $this->request->getMethod();
        // filter result is always in page 1
        if ($method == 'POST')
        {
            $page = 1;
            $this->session->set('member.page', 1);
        }

        $where = [];
        // create search query
        if (!empty($search)) {
            $where[] = "(`name` LIKE '%" . $search . "%')";
            $where[] = "(`email` LIKE '%" . $search . "%')";
            $where = [implode(' OR ', $where)];
        }

        // get list of members
        $start = ($page - 1) * $limit;
        $sort = $sort ? $sort : 'created_at desc';
        $result = $MemberEntity->list($start, $limit, $where, $sort);
        $total = $MemberEntity->getListTotal();

        $get_log = [];
        $get_log = $MemberEntity->list(0, 0, $where, 0);

        if (!$result) {
            $result = [];
            $total = 0;
            $mgs = $search ? 'Member not found!' : '';
            $session->set('flashMsg', $mgs);
        }

        $limit = $limit == 0 ? $total : $limit;
        $list = new Listing($result, $total, $limit, $this->getColumns());
        $user = $this->container->get('user');
        return [
            'list' => $list,
            'page' => $page,
            'start' => $start,
            'url' => $router->url(),
            'link_list' => $router->url('members'),
            'title_page' => 'Member Manager',
            'link_form' => $router->url('member'),
            'token' => $this->container->get('token')->value(),
        ];
    }

    public function getColumns()
    {
        return [
            'num' => '#',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'created_at' => 'Created At',
            'col_last' => ' ',
        ];
    }

    protected $_filter;
    public function filter()
    {
        if (null === $this->_filter) :
            $data = [
                'search' => $this->state('search', '', '', 'post', 'member.search'),
                'limit' => $this->state('limit', 20, 'int', 'post', 'member.limit'),
                'sort' => $this->state('sort', '', '', 'post', 'member.sort')
            ];

            $filter = new Form($this->getFilterFields(), $data);
            $this->_filter = $filter;
        endif;

        return ['form' => $this->_filter];
    }

    public function getFilterFields()
    {
        return [
            'search' => [
                'text',
                'default' => '',
                'showLabel' => false,
                'formClass' => 'form-control h-full input_common w_full_475',
                'placeholder' => 'Search..'
            ],
            'limit' => [
                'option',
                'formClass' => 'form-select',
                'default' => 20,
                'options' => [
                    ['text' => '20', 'value' => 20],
                    ['text' => '50', 'value' => 50],
                    ['text' => 'All', 'value' => 0],
                ],
                'showLabel' => false
            ],
            'sort' => [
                'option',
                'formClass' => 'form-select',
                'default' => 'created_at desc',
                'options' => [
                    ['text' => 'Name ascending', 'value' => 'name asc'],
                    ['text' => 'Name descending', 'value' => 'name desc'],
                    ['text' => 'Email ascending', 'value' => 'email asc'],
                    ['text' => 'Email descending', 'value' => 'email desc'],
                    ['text' => 'Date ascending', 'value' => 'created_at asc'],
                    ['text' => 'Date descending', 'value' => 'created_at desc'],
                ],
                'showLabel' => false
            ]
        ];
    }
}
