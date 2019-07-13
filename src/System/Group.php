<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Handle;
use Baiy\Cadmin\Model\AdminGroup;
use Baiy\Cadmin\Model\AdminMenu;
use Baiy\Cadmin\Model\AdminMenuGroup;
use Baiy\Cadmin\Model\AdminRequest;
use Baiy\Cadmin\Model\AdminRequestGroup;
use Baiy\Cadmin\Model\AdminUser;
use Baiy\Cadmin\Model\AdminUserGroup;
use Exception;

class Group extends Base
{
    public function lists($keyword = "")
    {
        $where = [];
        if (!empty($keyword)) {
            $where['name[~]'] = $keyword;
        }

        list($lists, $total) = $this->page(AdminGroup::table(), $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($group) {
                $group['request_length'] = $this->db->count(
                    AdminRequestGroup::table(),
                    ['admin_group_id' => $group['id']]
                );
                $group['menu_length'] = $this->db->count(
                    AdminMenuGroup::table(),
                    ['admin_group_id' => $group['id']]
                );
                $group['user_length'] = $this->db->count(
                    AdminUserGroup::table(),
                    ['admin_group_id' => $group['id']]
                );
                return $group;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $id = 0)
    {
        if (empty($name)) {
            throw new Exception("名称不能为空");
        }
        if (empty($id)) {
            return $this->db->insert(AdminGroup::table(), ['name' => $name]);
        }
        return $this->db->update(AdminGroup::table(), compact('name'), compact('id'));
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        AdminGroup::instance()->delete($id);
        return true;
    }
}
