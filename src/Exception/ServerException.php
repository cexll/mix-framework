<?php

namespace Mix\Framework\Exception;

class ServerException extends \Exception
{
    protected $message = '服务不存在';
    protected $code = 500;
}
