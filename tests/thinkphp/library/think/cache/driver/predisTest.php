<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace tests\thinkphp\library\think\cache\driver;

/**
 * Redisd缓存驱动测试
 * @author 尘缘 <130775@qq.com>
 */
class predisTest extends redisTest
{
    private $_cacheInstance = null;

    protected function setUp()
    {
        if (!class_exists('\Predis\Client')) {
            $this->markTestSkipped("Predis没有安装，已跳过测试！");
        }
    }

    protected function getCacheInstance()
    {
        if (null === $this->_cacheInstance) {
            $this->_cacheInstance = new \think\cache\driver\Predis();
        }
        return $this->_cacheInstance;
    }
}
