<?php

namespace Baiy\Cadmin;

class Response
{
    private $status;
    private $info;
    private $data;

    public function __construct($status, $info, $data)
    {
        $this->status = $status == "success" ? "success" : "error";
        $this->info   = $info;
        $this->data   = $data;
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
    
    public function force()
    {
        return null;    
    }

    public function toArray()
    {
        return [
            'status' => $this->status,
            'info'   => $this->info,
            'data'   => $this->data,
        ];
    }
}
