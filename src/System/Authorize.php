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

class Authorize extends Base
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

    public function getRequestAssign($id, $keyword = '')
    {
        $where = [];
        if (!empty($keyword)) {
            $where['OR'] = [
                'name[~]'   => $keyword,
                'action[~]' => $keyword,
            ];
        }
        $where['id[!]'] = array_merge(
            $this->db->select(AdminRequestGroup::table(), 'admin_request_id', ['admin_group_id' => $id]),
            Handle::instance()->getOnlyLoginRequestIds(), // 过滤无需分配的请求
            Handle::instance()->getNoCheckLoginRequestIds() // 过滤无需分配的请求
        );

        list($noAssignRequest, $total) = $this->page(AdminRequest::table(), $where, ['id' => 'DESC']);
        $assignRequest = $this->db->select(
            AdminRequest::table(),
            ['[><]'.AdminRequestGroup::table() => ['id' => 'admin_request_id']],
            [AdminRequest::table().'*'],
            [
                'AND'   => [
                    AdminRequestGroup::table().'.admin_group_id' => $id
                ],
                'ORDER' => [
                    AdminRequestGroup::table().'.id' => 'DESC'
                ]
            ]
        );
        return [
            'lists' => [$noAssignRequest, $assignRequest],
            'total' => $total
        ];
    }

    public function assignRequest($groupId, $requestId)
    {
        return $this->db->insert(AdminRequestGroup::table(), [
            'admin_group_id'   => $groupId,
            'admin_request_id' => $requestId,
        ]);
    }

    public function removeRequest($groupId, $requestId)
    {
        return $this->db->delete(
            AdminRequestGroup::table(),
            ['admin_group_id' => $groupId, 'admin_request_id' => $requestId]
        );
    }

    public function getMenuAssign($id)
    {
        $existIds = $this->db->select(AdminMenuGroup::table(), 'admin_menu_id', ['admin_group_id' => $id]);
        return array_map(function ($item) use ($existIds) {
            $item['checked'] = in_array($item['id'], $existIds);
            return $item;
        }, AdminMenu::instance()->getAllSorted());
    }

    public function assignMenu($groupId, $ids = [])
    {
        if (empty($ids)) {
            // 清空
            return $this->db->delete(AdminMenuGroup::table(), ['admin_group_id' => $groupId]);
        }
        $existIds = $this->db->select(AdminMenuGroup::table(), 'admin_menu_id', ['admin_group_id' => $groupId]);
        // 删除
        $delIds = array_diff($existIds, $ids);
        if (!empty($delIds)) {
            $this->db->delete(AdminMenuGroup::table(), [
                'admin_group_id' => $groupId,
                'admin_menu_id'  => $delIds,
            ]);
        }
        // 增加
        $addIds = array_diff($ids, $existIds);
        if (!empty($addIds)) {
            foreach ($addIds as $id) {
                $this->db->insert(AdminMenuGroup::table(), [
                    'admin_menu_id'  => $id,
                    'admin_group_id' => $groupId
                ]);
            }
        }
        return true;
    }

    public function getUserAssign($id)
    {
        $existIds = $this->db->select(AdminUserGroup::table(), 'admin_user_id', ['admin_group_id' => $id]);
        return array_map(function ($item) use ($existIds) {
            unset($item['password']);
            $item['checked'] = in_array($item['id'], $existIds);
            return $item;
        }, $this->db->select(AdminUser::table(), '*', ['ORDER' => ['id' => 'DESC']]));
    }

    public function assignUser($groupId, $ids = [])
    {
        if (empty($ids)) {
            // 清空
            return $this->db->delete(
                AdminUserGroup::table(),
                ['admin_group_id' => $groupId]
            );
        }
        $existIds = $this->db->select(AdminUserGroup::table(), 'admin_user_id', ['admin_group_id' => $groupId]);

        // 删除
        $delIds = array_diff($existIds, $ids);
        if (!empty($delIds)) {
            $this->db->delete(
                AdminUserGroup::table(),
                ['admin_group_id' => $groupId, 'admin_user_id' => $delIds]
            );
        }
        // 增加
        $addIds = array_diff($ids, $existIds);
        if (!empty($addIds)) {
            foreach ($addIds as $id) {
                $this->db->insert(AdminUserGroup::table(), [
                    'admin_user_id'  => $id,
                    'admin_group_id' => $groupId
                ]);
            }
        }
        return true;
    }
}
