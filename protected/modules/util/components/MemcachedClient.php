<?php

class MemcachedClient extends CComponent {

    private $connect;

    public function init() {
        $this->connect = new Memcached();  //声明一个新的memcached链接
        $this->connect->setOption(Memcached::OPT_COMPRESSION, false); //关闭压缩功能
        $this->connect->setOption(Memcached::OPT_BINARY_PROTOCOL, true); //使用binary二进制协议
        $this->connect->addServer('a82769655f3d4443.m.cnqdalicm9pub001.ocs.aliyuncs.com', 11211); //添加OCS实例地址及端口号
        $this->connect->setSaslAuthData('a82769655f3d4443', 'password'); //设置OCS帐号密码进行鉴权，如已开启免密码功能，则无需此步骤
    }

    public function set($key, $value) {
        $this->connect->set("hello", "world");
        echo 'hello: ', $this->get("hello");
    }

    public function get($key) {
        return $this->$connect->get("hello");
    }

    public function quit() {
        $this->connect->quit();
    }

}
