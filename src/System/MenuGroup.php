<?php

namespace Baiy\Cadmin\System;

use Exception;

class MenuGroup extends Base
{
    public function lists($keyword = ""): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['name[~]'] = $keyword;
        }

        list($lists, $total) = $this->page($this->model->menuGroup()->table, $where, ['id' => 'DESC']);
        return [
            'lists' => array_map(function ($item) {
                $item['menu'] = $this->db->count($this->model->menu()->table, ['admin_menu_group_id' => $item['id']]);
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $template = "", $template_development = "", $description = "", $id = 0): void
    {
        if (empty($name)) {
            throw new Exception("名称不能为空");
        }
        if ($id) {
            $this->db->update(
                $this->model->menuGroup()->table,
                compact('name', 'template', 'description', 'template_development'),
                compact('id')
            );
        } else {
            $this->db->insert($this->model->menuGroup()->table, compact('name', 'template', 'template_development', 'description'));
        }
    }

    public function remove($id): void
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        $this->model->menuGroup()->delete($id);
    }
}
