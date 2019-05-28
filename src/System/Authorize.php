<?php

namespace Baiy\Admin\System;

use Baiy\Admin\Handle;
use Baiy\Admin\Model\AdminGroup;
use Baiy\Admin\Model\AdminMenu;
use Baiy\Admin\Model\AdminMenuGroup;
use Baiy\Admin\Model\AdminRequest;
use Baiy\Admin\Model\AdminRequestGroup;
use Baiy\Admin\Model\AdminUser;
use Baiy\Admin\Model\AdminUserGroup;
use Exception;

class Authorize extends Base
{
    public function lists($keyword = "")
    {
        $where = [];
        $bindings = [];
        if (!empty($keyword)) {
            $where[] = " `name` like ? ";
            $bindings[] = "%{$keyword}%";
        }

        list($lists, $total) = $this->page(AdminGroup::table(), $where, $bindings, 'id DESC');

        return [
            'lists' => array_map(function ($group) {
                $group['request_length'] = $this->adapter->count(
                    "select count(*) as `total` from ".AdminRequestGroup::table()." where admin_group_id =?",
                    [$group['id']]
                );
                $group['menu_length'] = $this->adapter->count(
                    "select count(*) as `total` from ".AdminMenuGroup::table()." where admin_group_id =?",
                    [$group['id']]
                );
                $group['user_length'] = $this->adapter->count(
                    "select count(*) as `total` from ".AdminUserGroup::table()." where admin_group_id =?",
                    [$group['id']]
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
            return $this->adapter->insert(AdminGroup::table(), ['name' => $name]);
        }
        return $this->adapter->update("update ".AdminGroup::table()." set `name` = ? where id = ?", [$name, $id]);
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        AdminGroup::instance()->delete($id);
        return true;
    }

    public function getRequestAssign($id, $keyword = '')
    {
        $where = [];
        $bindings = [];
        if (!empty($keyword)) {
            $where[] = "(`name` like ? or `action` like ?)";
            array_push($bindings, "%{$keyword}%", "%{$keyword}%");
        }
        $where[] = "`id` not in (select admin_request_id from ".AdminRequestGroup::table()." where admin_group_id = ?)";
        array_push($bindings, $id);
        // 过滤无需分配的请求
        $where[] = "`id` not in (?)";
        array_push(
            $bindings,
            implode(',',array_merge(
                Handle::instance()->getOnlyLoginRequestIds(),
                Handle::instance()->getNoCheckLoginRequestIds()
            ))
        );

        list($noAssignRequest, $total) = $this->page(AdminRequest::table(), $where, $bindings, 'id DESC');
        $assignRequest = $this->adapter->select(
            "SELECT r.* FROM ".AdminRequest::table()." as r INNER JOIN ".AdminRequestGroup::table()." as g ON g.admin_request_id = r.id WHERE g.admin_group_id = ? order by g.id DESC",
            [$id]
        );
        return [
            'lists'  => [$noAssignRequest, $assignRequest],
            'total' => $total
        ];
    }

    public function assignRequest($groupId, $requestId)
    {
        return $this->adapter->insert(AdminRequestGroup::table(), [
            'admin_group_id'   => $groupId,
            'admin_request_id' => $requestId,
        ]);
    }

    public function removeRequest($groupId, $requestId)
    {
        return $this->adapter->delete(
            "delete from ".AdminRequestGroup::table()." where admin_group_id = ? and admin_request_id = ?",
            [$groupId, $requestId]
        );
    }

    public function getMenuAssign($id)
    {
        $existIds = array_column(
            $this->adapter->select(
                "select admin_menu_id from ".AdminMenuGroup::table()." where admin_group_id =?",
                [$id]
            ),
            'admin_menu_id'
        );
        return array_map(function ($item) use ($existIds) {
            $item['checked'] = in_array($item['id'], $existIds);
            return $item;
        }, $this->adapter->select("select * from ".AdminMenu::table()." order by `sort` ASC,`id` ASC"));
    }

    public function assignMenu($groupId, $ids = [])
    {
        if (empty($ids)) {
            // 清空
            return $this->adapter->delete(
                "delete from ".AdminMenuGroup::table()." where admin_group_id = ?",
                [$groupId]
            );
        }
        $existIds = array_column(
            $this->adapter->select(
                "select admin_menu_id from ".AdminMenuGroup::table()." where admin_group_id =?",
                [$groupId]
            ),
            'admin_menu_id'
        );
        // 删除
        $delIds = array_diff($existIds, $ids);
        if (!empty($delIds)) {
            $this->adapter->delete(
                "delete from ".AdminMenuGroup::table()." where admin_group_id = ? and admin_menu_id in (?)",
                [$groupId, implode(',', $delIds)]
            );
        }
        // 增加
        $addIds = array_diff($ids, $existIds);
        if (!empty($addIds)) {
            foreach ($addIds as $id) {
                $this->adapter->insert(AdminMenuGroup::table(), [
                    'admin_menu_id'  => $id,
                    'admin_group_id' => $groupId
                ]);
            }
        }
        return true;
    }

    public function getUserAssign($id)
    {
        $existIds = array_column(
            $this->adapter->select(
                "select admin_user_id from ".AdminUserGroup::table()." where admin_group_id =?",
                [$id]
            ),
            'admin_user_id'
        );
        return array_map(function ($item) use ($existIds) {
            unset($item['password']);
            $item['checked'] = in_array($item['id'], $existIds);
            return $item;
        }, $this->adapter->select("select * from ".AdminUser::table()." order by `id` DESC"));
    }

    public function assignUser($groupId, $ids = [])
    {
        if (empty($ids)) {
            // 清空
            return $this->adapter->delete(
                "delete from ".AdminUserGroup::table()." where admin_group_id = ?",
                [$groupId]
            );
        }
        $existIds = array_column(
            $this->adapter->select(
                "select admin_user_id from ".AdminUserGroup::table()." where admin_group_id =?",
                [$groupId]
            ),
            'admin_user_id'
        );
        // 删除
        $delIds = array_diff($existIds, $ids);
        if (!empty($delIds)) {
            $this->adapter->delete(
                "delete from ".AdminUserGroup::table()." where admin_group_id = ? and admin_user_id in (?)",
                [$groupId, implode(',', $delIds)]
            );
        }
        // 增加
        $addIds = array_diff($ids, $existIds);
        if (!empty($addIds)) {
            foreach ($addIds as $id) {
                $this->adapter->insert(AdminUserGroup::table(), [
                    'admin_user_id'  => $id,
                    'admin_group_id' => $groupId
                ]);
            }
        }
        return true;
    }
}
