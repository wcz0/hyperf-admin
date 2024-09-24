<?php

namespace App\Support;

class Fluent
{
    /**
     * 存储属性
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * 动态获取属性值
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * 动态设置属性值
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * 检查属性是否存在
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * 删除属性
     *
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * 将对象转换为数组
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}