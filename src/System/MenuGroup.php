<?php

namespace Baiy\Cadmin\System;

use Exception;

class MenuGroup extends Base
{
    public function lists(): array
    {
        return $this->model->menuGroup()->all();
    }

    public function save($name, $template, $description = "", $id = 0): void
    {
        if (empty($name)) {
            throw new Exception("名称不能为空");
        }
        if (empty($template)) {
            throw new Exception("模板不能为空");
        }
        if ($id) {
            $this->db->update(
                $this->model->menuGroup()->table,
                compact('name', 'template', 'description'),
                compact('id')
            );
        } else {
            $this->db->insert($this->model->menuGroup()->table, compact('name', 'template', 'description'));
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
