<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Model\AdminMenu;
use Exception;

class Menu extends Base
{
    public function lists()
    {
        return AdminMenu::instance()->getAllSorted();
    }

    public function sort($menus)
    {
        foreach ($menus as $menu) {
            $this->db->update(
                AdminMenu::table(),
                ['sort' => $menu['sort']],
                ['id' => $menu['id']]
            );
        }
        return true;
    }

    public function save($parent_id, $name, $url, $icon = "", $description = "", $id = 0)
    {

        if (empty($name)) {
            throw new Exception("菜单名称不能为空");
        }
        if (!empty($parent_id)) {
            $parentMenu = AdminMenu::instance()->getById($parent_id);
            if (empty($parentMenu)) {
                throw new Exception("父菜单不存在");
            }
            if (!empty($parentMenu['url'])) {
                throw new Exception("父菜单不是目录类型菜单");
            }
        }
        if ($id) {
            $this->db->update(
                AdminMenu::table(),
                compact('name', 'parent_id', 'url', 'icon', 'description'),
                compact('id')
            );
        } else {
            // 计算排序值
            $sort = $this->db->get(
                AdminMenu::table(), 'sort', ['AND' => compact('parent_id'), 'ORDER' => ['sort' => 'DESC']]
            );
            $sort = $sort ? 0 : $sort + 1;
            $this->db->insert(AdminMenu::table(), compact('name', 'parent_id', 'url', 'icon', 'description', 'sort'));
        }
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        AdminMenu::instance()->delete($id);
        return true;
    }
}
