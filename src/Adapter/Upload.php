<?php

namespace Baiy\Cadmin\Adapter;

use Exception;

class Upload
{
    protected $clientFileName;
    protected $clientMediaType;
    protected $tmpFileName;
    protected $error;
    protected $size;

    // 上传文件后缀名
    protected $ext;
    protected $config = [
        'enabledExt' => 'jpg,jpeg,png,gif,pdf,zip,rar',// 可上传后缀名
        'pathPrefix' => "", // 文件存储目录前缀
        'urlPrefix'  => "",// 文件url前缀
        'maxSize'    => 5 * 1024 * 1024
    ];

    public function initialize($file, $config)
    {
        $this->clientFileName  = $file['name'];
        $this->clientMediaType = $file['type'];
        $this->tmpFileName     = $file['tmp_name'];
        $this->error           = $file['error'];
        $this->size            = $file['size'];
        $this->ext             = strtolower(substr(strrchr($this->clientFileName, '.'), 1));
        $this->config          = array_merge($this->config, $config);

        $this->check();

        return $this;
    }

    protected function check()
    {
        if (!empty($this->config['enabledExt'])
            && !in_array($this->ext, explode(',', $this->config['enabledExt']))
        ) {
            throw new Exception("文件类型不支持");
        }

        if (!empty($this->config['maxSize']) && $this->size > $this->config['maxSize']) {
            throw new Exception("上传文件过大 上传文件大小限制:".$this->config['maxSize'].'字节');
        }

        if (empty($this->config['pathPrefix'])) {
            throw new Exception("文件存储路径前缀配置错误");
        }

        if (empty($this->config['urlPrefix'])) {
            throw new Exception("文件URL路径前缀配置错误");
        }
    }

    public function moveTo($targetPath)
    {
        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new Exception("[{$dir}]创建文件存储目录失败");
            }
        }

        return move_uploaded_file($this->tmpFileName, $targetPath);
    }

    public function move()
    {
        $path = date('/Y/m/d/').md5(time().mt_rand(1, 99999)).'.'.$this->ext;
        if (!$this->moveTo(rtrim($this->config['pathPrefix'],'/').$path)) {
            throw new Exception("上传文件错误");
        }
        return [
            'path' => $path,
            'url'  => rtrim($this->config['urlPrefix'],'/').$path
        ];
    }
}