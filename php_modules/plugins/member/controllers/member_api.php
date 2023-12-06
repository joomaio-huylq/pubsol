<?php

namespace App\plugins\member\controllers;

use SPT\Web\ControllerMVVM;

class member_api extends ControllerMVVM
{
    public function list()
    {
        // get param page
        $page = $this->request->get->get('page');
        if ($page <= 0) $page = 1;

        // get param limit
        $limit = $this->request->get->get('limit');


        $where = [];
        $search = $this->request->get->get('search');
        // create search query
        if (!empty($search)) {
            $where[] = "(`name` LIKE '%" . $search . "%')";
            $where[] = "(`email` LIKE '%" . $search . "%')";
            $where = [implode(' OR ', $where)];
        }
        // get param sort
        $sort = $this->request->get->get('sort');
        $sort = $sort ? $sort : 'created_at desc';

        // get list of members
        $start = ($page - 1) * $limit;
        $result = $this->MemberEntity->list($start, $limit, $where, $sort);

        $this->app->set('format', 'json');
        $this->set('status', 'success');
        $this->set('data', $result);
        $this->set('message', 'Get Member List Successfully!');
        return;
    }

    public function detail()
    {
        // get member id
        $id = $this->validateID();

        $result = $this->MemberEntity->findByPK($id);

        if ($result)
        {
            $this->app->set('format', 'json');
            $this->set('status', 'success');
            $this->set('data', $result);
            $this->set('message', 'Get Member Successfully!');
        }
        else
        {
            $this->app->set('format', 'json');
            $this->set('status', 'failed');
            $this->set('data', '');
            $this->set('message', 'Member Not Found!');
        }

        return;
    }

    public function create()
    {
        // get input data
        $data = [
            'name' => $this->request->get('name', '', 'string'),
            'email' => $this->request->get('email', '', 'string'),
            'password' => $this->request->get('password', '', 'string')
        ];

        // validate input data
        $validated_data = $this->MemberEntity->validate($data);
        if (!$validated_data) {
            $this->app->set('format', 'json');
            $this->set('status', 'failed');
            $this->set('data', '');
            $this->set('message', $this->MemberEntity->getError());
            return;
        }
        // var_dump($validated_data);
        // create new member
        $member = $this->MemberModel->add($validated_data);

        if (!$member) {
            $this->app->set('format', 'json');
            $this->set('status', 'failed');
            $this->set('data', '');
            $this->set('message', $this->MemberModel->getError());
            return;
        } else {
            $this->app->set('format', 'json');
            $this->set('status', 'success');
            $this->set('data', $member);
            $this->set('message', 'Created Successfully!');
            return;
        }
    }

    public function update()
    {
        // get member id
        $id = $this->validateID();

        if (is_array($id) && $id != null) {
            $this->app->set('format', 'json');
            $this->set('status', 'failed');
            $this->set('data', '');
            $this->set('message', 'Invalid Member');
            return;
        }

        if (is_numeric($id) && $id) {
            // get input data
            parse_str(file_get_contents('php://input'), $_PUT);

            $data = [
                'name' => $_PUT['name'] ? $_PUT['name'] : '',
                'email' => $_PUT['email'] ? $_PUT['email'] : '',
                'password' => $_PUT['password'] ? $_PUT['password'] : '',
                'id' => $id,
            ];

            // validate input data
            $validated_data = $this->MemberEntity->validate($data);
            if (!$validated_data) {
                $this->app->set('format', 'json');
                $this->set('status', 'fail');
                $this->set('data', '');
                $this->set('message', $this->MemberEntity->getError());
                return;
            }

            // update member info
            $member = $this->MemberModel->update($validated_data);

            if ($member) {
                $this->app->set('format', 'json');
                $this->set('status', 'success');
                $this->set('data', $member);
                $this->set('message', 'Updated Successfully!');
                return;
            } else {
                $this->app->set('format', 'json');
                $this->set('status', 'failed');
                $this->set('data', '');
                $this->set('message', $this->MemberModel->getError());
                return;
            }
        }
    }

    public function delete()
    {
        // get member id
        $id = $this->validateID();

        if (is_array($id) && $id != null) {
            $this->app->set('format', 'json');
            $this->set('status', 'failed');
            $this->set('data', '');
            $this->set('message', 'Invalid Member');
            return;
        }

        if (is_numeric($id) && $id) {
            // get member
            $member = $this->MemberEntity->findByPK($id);

            if ($member) {
                // remove member
                $this->MemberModel->remove($id);

                $this->app->set('format', 'json');
                $this->set('status', 'success');
                $this->set('data', '');
                $this->set('message', 'Deleted Successfully!');
                return;
            } else {
                $this->app->set('format', 'json');
                $this->set('status', 'failed');
                $this->set('data', '');
                $this->set('message', 'Member Not Found!');
                return;
            }
        }
    }

    public function validateID()
    {

        $urlVars = $this->request->get('urlVars');
        $id = $urlVars ? (int) $urlVars['id'] : 0;

        return $id;
    }
}
