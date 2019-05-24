<?php
namespace app\common\lib\exception;

use think\exception\Handle;

class ApiHandleException extends Handle
{
    public $httpCode =  500;
    public $code     =  -1;
    public function render(\Exception $e)
    {
        if(config('app_debug')){
            return parent::render($e);
        }
        if ($e instanceof ApiException) {
            $this->httpCode = $e->httpCode;
            $this->code     = $e->code;
        }
      return  showApi($this->code,$e->getMessage(),[],$this->httpCode);
    }
}