<?php

namespace Baiy\Admin\System;

use Baiy\Admin\Model\AdminMenu;
use Exception;

class Menu extends Base
{
    public function lists()
    {
        return $this->adapter->select("select * from ".AdminMenu::table()." order by `sort` ASC,`id` ASC");
    }

    public function sort($menus)
    {
        foreach ($menus as $menu) {
            $this->adapter->delete("update ".AdminMenu::table()." set `sort`=? where id=?", [$menu['sort'], $menu['id']]);
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
            $this->adapter->update(
                "update ".AdminMenu::table()." set `name` =?,`parent_id,` =?,`url` =?,`icon`=?,`description`=? where id = ?",
                [$name, $parent_id, $url, $icon, $description, $id]
            );
        } else {
            // 计算排序值
            $temp = $this->adapter->selectOne(
                "select `sort` from ".AdminMenu::table()." where `parent_id`=? order `sort` DESC limit 1",
                [$parent_id]
            );
            $sort = empty($temp) ? 0 : $temp['sort'] + 1;
            $this->adapter->insert(AdminMenu::table(), compact('name', 'parent_id', 'url', 'icon', 'description', 'sort'));
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
