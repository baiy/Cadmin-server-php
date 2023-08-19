<?php

namespace Baiy\Cadmin\Model;

abstract class Relate extends Base
{
    // 主体字段
    protected string $mainField = "";
    // 关联字段/表
    protected string $relateField = "";
    protected string $relateTable = "";

    public function getAssignInfo($id): array
    {
        return [
            'lists' => $this->db->select($this->relateTable, '*', ['ORDER' => ['id' => 'DESC']]),
            'selected' => $this->db->select($this->table, $this->relateField, [$this->mainField => $id]) ?: []
        ];
    }

    // 差异化更新
    public function assign($mainId, $relateIds = []): void
    {
        if (!$mainId) {
            throw new \Exception("输入异常");
        }

        if (!$this->mainField || !$this->relateField) {
            throw new \Exception("字段配置空位");
        }

        // 清空
        if (empty($relateIds)) {
            $this->db->delete($this->table, [$this->mainField => $mainId]);
            return;
        }

        if (!is_array($relateIds)) {
            throw new \Exception("数据格式异常");
        }

        $existIds = $this->db->select($this->table, $this->relateField, [$this->mainField => $mainId]) ?: [];

        // 删除
        $delIds = array_diff($existIds, $relateIds);
        if (!empty($delIds)) {
            $this->db->delete($this->table, [
                $this->mainField   => $mainId,
                $this->relateField => array_values($delIds),
            ]);
        }

        // 增加
        $addIds = array_diff($relateIds, $existIds);
        if (!empty($addIds)) {
            $this->db->insert($this->table, array_map(function ($itemId) use ($mainId) {
                return [
                    $this->relateField => $itemId,
                    $this->mainField   => $mainId
                ];
            }, array_values($addIds)));
        }
    }
}
