<?php

namespace Baiy\Cadmin\Command;

use Baiy\Cadmin\Helper;
use Baiy\Cadmin\Model\AdminGroup;
use Baiy\Cadmin\Model\AdminMenu;
use Baiy\Cadmin\Model\AdminMenuGroup;
use Baiy\Cadmin\Model\AdminRequest;
use Baiy\Cadmin\Model\AdminRequestGroup;
use Baiy\Cadmin\Model\AdminToken;
use Baiy\Cadmin\Model\AdminUser;
use Baiy\Cadmin\Model\AdminUserGroup;
use Exception;
use Medoo\Medoo;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Base
{
    /** @var Medoo */
    private $db;
    private $dbConfig = [];
    private $table = [
        AdminGroup::class,
        AdminMenu::class,
        AdminMenuGroup::class,
        AdminRequest::class,
        AdminRequestGroup::class,
        AdminToken::class,
        AdminUser::class,
        AdminUserGroup::class,
    ];

    protected function configure()
    {
        $this->setName('cadmin:install')
            ->setDescription('安装Cadmin,初始化数据库表')
            ->addArgument(
                'dbConfig'
                , InputArgument::REQUIRED, '数据库配置信息 格式:mysql://root:1234@127.0.0.1:3306/database#utf8mb4'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln('=======数据库初始化开始=======');
            $output->write('解析数据库配置');
            $this->parseDsn($input->getArgument('dbConfig'));
            $this->db = new Medoo($this->dbConfig);
            $output->writeln('....完成');
            $output->write('检查数据库状态');
            $this->checkDb();
            $output->writeln('....完成');
            $output->write('创建数据表');
            $this->createTable();
            $output->writeln('....完成');
            $output->write('插入必要数据');
            $this->insertData();
            $output->writeln('....完成');
            $output->writeln('=======数据库初始化完成=======');
            $output->writeln('接下来请根据文档进行下一步的代码配置工作');
        } catch (Exception $e) {
            $output->writeln('<error>错误:</error>'.$e->getMessage());
        }
    }

    private function checkDb()
    {
        foreach ($this->table as $item) {
            if ($this->tableExists(Helper::parseTableName($item))) {
                throw new Exception(Helper::parseTableName($item)." 表已经存在");
            }
        }
    }

    private function parseDsn($dsnStr)
    {
        if (empty($dsnStr)) {
            throw new Exception("数据库配置信息错误");
        }
        $info = parse_url($dsnStr);
        if (!$info) {
            throw new Exception("数据库配置解析失败");
        }

        $this->dbConfig = [
            'database_type' => $info['scheme'],
            'username'      => isset($info['user']) ? $info['user'] : '',
            'password'      => isset($info['pass']) ? $info['pass'] : '',
            'server'        => isset($info['host']) ? $info['host'] : '',
            'port'          => isset($info['port']) ? $info['port'] : '',
            'database_name' => isset($info['path']) ? ltrim($info['path'], '/') : '',
            'charset'       => isset($info['fragment']) ? $info['fragment'] : 'utf8mb4',
        ];
    }

    private function tableExists($table)
    {
        try {
            if (!$table) {
                throw new Exception("表名不能为空");
            }
            $result = $this->db->pdo->query("SELECT 1 FROM $table LIMIT 1");
        } catch (Exception $e) {
            return false;
        }
        return $result !== false;
    }

    private function createTable()
    {
        if ($this->db->pdo->exec(file_get_contents(__DIR__.'/../database.sql')) === false) {
            throw new \Exception("创建相关数据表失败");
        }
    }

    private function insertData()
    {
        $datas = include __DIR__.'/../initializeData.php';
        foreach ($datas as $table => $data) {
            if (!$this->db->insert($table, $data)) {
                throw new \Exception("表{$table}数据插入失败");
            }
        }
    }
}