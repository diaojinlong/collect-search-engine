<?php
namespace DiaoJinLong\CollectSearchEngine;

class EngineRequest
{
    /**
     * 搜索引擎
     * @var null
     */
    protected $engine = null;

    /**
     * EngineRequest constructor.
     * @param string $engine baidu=百度搜索 so=360搜索
     * @param array $config []
     */
    public function __construct($engine = 'baidu', $config=[])
    {
        $object = __NAMESPACE__ . '\\Engine\\' . ucfirst($engine);
        $this->engine = new $object($config);
    }

    public function __call($name, $arguments)
    {
        return $this->engine->{$name}(...$arguments);
    }

}
