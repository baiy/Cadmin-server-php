<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Model\Auth;
use Baiy\Cadmin\Model\User;
use Baiy\Cadmin\Model\UserGroup as UserGroupModel;
use Baiy\Cadmin\Model\UserGroupRelate;
use Baiy\Cadmin\Model\UserRelate;
use Exception;
use PDO;

class UserGroup extends Base
{
    public function lists($keyword = "")
    {
        $where = [];
        if (!empty($keyword)) {
            $where['name[~]'] = $keyword;
        }

        list($lists, $total) = $this->page(UserGroupModel::table(), $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['auth'] = Auth::instance()->getByIds(
                    UserGroupRelate::instance()->authIds($item['id'])
                );
                $item['user'] = User::instance()->getByIds(
                    UserRelate::instance()->userIds($item['id'])
                );
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $description = "", $id = 0)
    {
        if (empty($name)) {
            throw new Exception("权限组名称不能为空");
        }
        if (empty($id)) {
            $this->db->insert(UserGroupModel::table(), compact('name', 'description'));
        } else {
            $this->db->update(UserGroupModel::table(), compact('name', 'description'), compact('id'));
        }
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        UserGroupModel::instance()->delete($id);
    }

    /**
     * 获取用户分组信息
     */
    public function getUser($id, $keyword = '')
    {
        $where = [];
        if (!empty($keyword)) {
            $where['username[~]'] = $keyword;
        }
        // 已分配权限
        $existIds = $this->db->select(UserRelate::table(), 'admin_user_id', ['admin_user_group_id' => $id]);
        if ($existIds) {
            $where['id[!]'] = $existIds;
        }

        list($noAssign, $total) = $this->page(User::table(), $where, ['id' => 'DESC']);
        $assign = $this->db->query(
            sprintf(
                "SELECT user.* FROM %s as user INNER JOIN %s as rel ON rel.admin_user_id = user.id ".
                "WHERE rel.admin_user_group_id = '%s' order by rel.id DESC",
                User::table(),
                UserRelate::table(),
                $id
            )
        )->fetchAll(PDO::FETCH_ASSOC);
        return [
            'lists' => ['assign' => $assign, 'noAssign' => $noAssign],
            'total' => $total
        ];
    }

    /**
     * 分配用户
     */
    public function assignUser($id, $userId)
    {
        $this->db->insert(UserRelate::table(), [
            'admin_user_group_id' => $id,
            'admin_user_id'       => $userId,
        ]);
    }

    /**
     * 移除用户分配
     */
    public function removeUser($id, $userId)
    {
        $this->db->delete(
            UserRelate::table(),
            ['admin_user_group_id' => $id, 'admin_user_id' => $userId]
        );
    }
}
