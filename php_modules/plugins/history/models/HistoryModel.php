<?php
/**
 * SPT software - Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic model
 * 
 */

namespace App\plugins\history\models;

use SPT\Container\Client as Base;

class HistoryModel extends Base
{ 
    use \SPT\Traits\ErrorString;

    public function validate($data)
    {
        if (!$data && !is_array($data))
        {
            $this->error = 'Invalid format data';
            return false;
        }

        if(!$data['object'] || !$data['object_id'])
        {
            $this->error = "Invalid object history";
            return false;
        }

        return true;
    }

    public function add($data)
    {
        if (!$this->validate($data))
        {
            return false;
        }

        $try = $this->HistoryEntity->add([
            'object' => $data['object'],
            'object_id' => $data['object_id'],
            'data' => $data['data'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $this->user->get('id'),
        ]);

        if (!$try)
        {
            $this->error = "Can't create history";
            return false;
        }

        return $try;
    }

    public function update($data)
    {
        if (!$this->validate($data) || !$data['id'])
        {
            return false;
        }

        $try = $this->HistoryEntity->update([
            'object' => $data['object'],
            'object_id' => $data['object_id'],
            'data' => $data['data'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $this->user->get('id'),
        ]);

        if (!$try)
        {
            $this->error = "Can't update history";
            return false;
        }

        return $try;
    }

    public function remove($id)
    {
        if (!$id)
        {
            $this->error = 'Invalid id';
            return false;
        }

        $try = $this->HistoryEntity->remove($id);

        if (!$try)
        {
            $this->error = "Can't remove history";
            return false;
        }

        return $try;
    }

    public function list($start, $limit, $where)
    {
        $list = $this->HistoryEntity->list($start, $limit, $where, 'created_at desc');
        $list = $list ? $list : [];
        
        foreach ($list as &$item)
        {
            $user_tmp = $this->UserEntity->findByPK($item['created_by']);
            $item['user'] = $user_tmp ? $user_tmp['name'] : '';
        }

        return $list;
    }

    public function detail($id)
    {
        if (!$id)
        {
            $this->error = 'Invalid id';
            return false;
        }

        $history = $this->HistoryEntity->findByPK($id);
        
        return $history;
    }
}
