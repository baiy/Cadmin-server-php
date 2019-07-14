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
use PDO;

class Auth extends Base
{
    public function getRequest($id, $keyword = '')
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
        $assignRequest = $this->db->query(
            sprintf(
                "SELECT r.* FROM %s as r INNER JOIN %s as g ON g.admin_request_id = r.id ".
                "WHERE g.admin_group_id = %s order by g.id DESC",
                AdminRequest::table(),
                AdminRequestGroup::table(),
                $id
            )
        )->fetchAll(PDO::FETCH_ASSOC);
        return [
            'lists' => [$noAssignRequest, $assignRequest?:[]],
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

    public function getMenu($id)
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

    public function getUserByGroup($groupId)
    {
        $existIds = $this->db->select(AdminUserGroup::table(), 'admin_user_id', ['admin_group_id' => $groupId]);
        if (empty($existIds)) {
            return [];
        }
        return $this->db->select(
            AdminUser::table(),
            ['id', 'username'],
            ['AND'=>['id' => $existIds], 'ORDER' => ['id' => 'DESC']]
        );
    }

    public function getGroupByUser($userid)
    {
        $existIds = $this->db->select(AdminUserGroup::table(), 'admin_group_id', ['admin_user_id' => $userid]);
        return array_map(function ($item) use ($existIds) {
            $item['checked'] = in_array($item['id'], $existIds);
            return $item;
        }, $this->db->select(AdminGroup::table(), '*', ['ORDER' => ['id' => 'ASC']]));
    }

    public function relateGroupToUser($userid, $ids = [])
    {
        if (empty($ids)) {
            // 清空
            return $this->db->delete(
                AdminUserGroup::table(),
                ['admin_user_id' => $userid]
            );
        }
        $existIds = $this->db->select(AdminUserGroup::table(), 'admin_group_id', ['admin_user_id' => $userid]);

        // 删除
        $delIds = array_diff($existIds, $ids);
        if (!empty($delIds)) {
            $this->db->delete(
                AdminUserGroup::table(),
                ['admin_group_id' => $delIds, 'admin_user_id' => $userid]
            );
        }
        // 增加
        $addIds = array_diff($ids, $existIds);
        if (!empty($addIds)) {
            foreach ($addIds as $id) {
                $this->db->insert(AdminUserGroup::table(), [
                    'admin_group_id' => $id,
                    'admin_user_id'  => $userid
                ]);
            }
        }
        return true;
    }
}
