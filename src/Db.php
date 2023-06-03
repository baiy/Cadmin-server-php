<?php

namespace Baiy\Cadmin;

class Db extends Library\Medoo\Medoo
{
    private Container $container;

    // 重写执行方法 方便记录sql
    public function exec(string $statement, array $map = [], callable $callback = null): ?\PDOStatement
    {
        $startTime = microtime(true);
        $result    = parent::exec($statement, $map, $callback);
        $this->container->context->addListenSql(
            $this->generate($statement, $map),
            number_format((microtime(true) - $startTime), 6)
        );
        return $result;
    }

    public function setContainer(Container $container): static
    {
        $this->container = $container;
        return $this;
    }

    public function select(string $table, $join, $columns = null, $where = null): array
    {
        return parent::select($table, $join, $columns, $where) ?: [];
    }
}
