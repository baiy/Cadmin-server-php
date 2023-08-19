<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Model\UserGroup;
use Exception;

class Auth extends Base
{
    public function lists($keyword = ""): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['name[~]'] = $keyword;
        }

        list($lists, $total) = $this->page($this->model->auth()->table, $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['request']   = count($this->model->requestRelate()->requestIds($item['id']));
                $item['menu']      = count($this->model->menuRelate()->menuIds($item['id']));
                $item['userGroup'] = count($this->model->userGroupRelate()->userGroupIds($item['id']));
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $description = "", $id = 0): void
    {
        if (empty($name)) {
            throw new Exception("权限名称不能为空");
        }
        if (empty($id)) {
            $this->db->insert($this->model->auth()->table, compact('name', 'description'));
        } else {
            $this->db->update($this->model->auth()->table, compact('name', 'description'), compact('id'));
        }
    }

    public function remove($id): void
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        $this->model->auth()->delete($id);
    }

    /**
     * 获取请求权限分配情况
     */
    public function getRequest($id): array
    {
        return $this->model->requestRelate()->getAssignInfo($id);
    }

    /**
     * 请求分配
     */
    public function assignRequest($id, $assignIds = []): void
    {
        $this->model->requestRelate()->assign($id, $assignIds);
    }

    /**
     * 获取用户组权限分配情况
     */
    public function getUserGroup($id): array
    {
        $result = $this->model->userGroupRelate()->getAssignInfo($id);
        // 过滤超级管理员
        $result['lists'] = array_filter($result['lists'], function ($item) {
            return $item['id'] != UserGroup::USER_GROUP_ADMINISTRATOR_ID;
        });
        return $result;
    }

    /**
     * 用户组分配
     */
    public function assignUserGroup($id, $assignIds = []): void
    {
        $this->model->userGroupRelate()->assign($id, $assignIds);
    }

    /**
     * 获取菜单权限分配情况
     */
    public function getMenu($id): array
    {
        return $this->model->menuRelate()->getAssignInfo($id);
    }

    /**
     * 分配菜单
     */
    public function assignMenu($id, $assignIds = []): void
    {
        $this->model->menuRelate()->assign($id, $assignIds);
    }
}
