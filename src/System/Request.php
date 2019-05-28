<?php

namespace Baiy\Admin\System;

use Baiy\Admin\Model\AdminRequest;
use Baiy\Admin\Model\AdminRequestGroup;
use Baiy\Admin\Model\AdminUserGroup;
use Exception;

class Request extends Base
{
    public function lists($keyword = "", $type = "")
    {
        $where = [];
        $bindings = [];
        if (!empty($keyword)) {
            $where[] = "(`name` like ? or `action` like ? or `call` like ?)";
            array_push($bindings, "%{$keyword}%", "%{$keyword}%", "%{$keyword}%");

        }
        if (!empty($type)) {
            $where[] = "(`type` = ?)";
            array_push($bindings, $type);
        }

        list($lists, $total) = $this->page(AdminRequest::table(), $where, $bindings, 'id DESC');

        return [
            'lists' => array_map(function ($request) {
                $request['groups'] = array_map(function ($group) {
                    $group['uids'] = AdminUserGroup::instance()->getUids($group['id']);
                    return $group;
                }, AdminRequestGroup::instance()->getGroups($request['id']));
                return $request;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $action, $type, $call, $id = "")
    {
        if (empty($name) || empty($action) || empty($call)) {
            throw new Exception("参数错误");
        }
        if (!in_array($type, array_column(AdminRequest::TYPE_LISTS, 'v'))) {
            throw new Exception("类型错误");
        }

        if ($id) {
            $this->adapter->update(
                "update ".AdminRequest::table()." set `name` =?,`action` =?,`type` =?,`call`=? where id = ?",
                [$name, $action, $type, $call, $id]
            );
        } else {
            $id = $this->adapter->insert(AdminRequest::table(), compact('name', 'action', 'type', 'call'));
        }
        return $id;
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        AdminRequest::instance()->delete($id);
        return true;
    }
}
