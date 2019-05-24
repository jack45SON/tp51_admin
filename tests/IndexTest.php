<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace tests;

use PHPUnit\Framework\Constraint\IsFalse;

class IndexTest extends TestCase
{

    //针对Index控制器下的test方法
    public function testTest()
    {
        $this->visit('/index/index/index')->see('Hello world');
    }
}