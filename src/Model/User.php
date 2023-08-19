<?php

namespace Baiy\Cadmin\Model;

class User extends Base
{
    const STATUS_LISTS = [
        'enable'   => ['v' => 1, 'n' => '启用'],
        'disabled' => ['v' => 2, 'n' => '禁用'],
    ];

    // 是否禁用
    public function isDisabled($user): bool
    {
        return !$user || $user['status'] == self::STATUS_LISTS['disabled']['v'];
    }

    public function getByUserName($username)
    {
        return $this->db->get($this->table, "*", ['username' => $username]);
    }

    public function loginUpdate(int $id, $ip): bool
    {
        return !!$this->db->update(
            $this->table,
            [
                'last_login_ip'   => $ip,
                'last_login_time' => date("Y-m-d H:i:s")
            ],
            ['id' => $id]
        );
    }

    public function getAllEnable(): array
    {
        return $this->db->select($this->table, '*', [
            'status' => self::STATUS_LISTS['enable']['v']
        ]);
    }

    public function getUserAuth($id): array
    {
        $groupIds = $this->model->userRelate()->groupIds($id);
        if (empty($groupIds)) {
            return [];
        }
        $authIds = $this->model->userGroupRelate()->authIds($groupIds);
        if (empty($authIds)) {
            return [];
        }
        return $this->model->auth()->getByIds($authIds);
    }

    public function getUserGroup($id): array
    {
        $groupIds = $this->model->userRelate()->groupIds($id);
        if (empty($groupIds)) {
            return [];
        }
        return $this->db->select($this->model->userGroup()->table, '*', ['id' => $groupIds]);
    }

    public function getUserMenu($id, $development = 0): array
    {
        $auths = $this->getUserAuth($id);
        if (empty($auths)) {
            return [];
        }
        $menuIds = $this->db->select(
            $this->model->menuRelate()->table,
            'admin_menu_id',
            ['admin_auth_id' => array_column($auths, 'id')]
        );
        if (empty($menuIds)) {
            return [];
        }
        $menuGroups = $this->db->select($this->model->menuGroup()->table, ['id' => ['template', 'template_development']]);

        // url 分组模版
        return array_map(function ($menu) use ($menuGroups, $development) {
            $url = $menu['url'];
            if (isset($menuGroups[$menu['admin_menu_group_id']])) {
                // url 模板
                $template = $menuGroups[$menu['admin_menu_group_id']]['template'] ?? '';

                // 默认模式
                if ($development && $menuGroups[$menu['admin_menu_group_id']]['template_development']) {
                    $template = $menuGroups[$menu['admin_menu_group_id']]['template_development'];
                }
                if ($template) {
                    $url = str_replace('{url}', $url, $template);
                }
            }

            $menu['url'] = $url;
            return $menu;
        }, $this->db->select($this->model->menu()->table, '*', ['id' => $menuIds]));
    }

    public function getUserRequest($id): array
    {
        $auths = $this->getUserAuth($id);
        if (empty($auths)) {
            return [];
        }

        $requestIds = $this->db->select(
            $this->model->requestRelate()->table, '
            admin_request_id',
            ['admin_auth_id' => array_column($auths, 'id')]
        );
        if (empty($requestIds)) {
            return [];
        }
        return $this->db->select($this->model->request()->table, '*', ['id' => $requestIds]);
    }
}
