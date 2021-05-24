<?php

namespace DiaoJinLong\CollectSearchEngine\Engine;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

abstract class Base
{

    /**
     * 搜索引擎网址
     * @var string
     */
    protected $baseUri = '';

    /**
     * 搜索引擎路径参数
     * @var string
     */
    protected $path = '';

    /**
     * 超时时间单位（秒）
     * @var int
     */
    protected $timeout = 30;

    /**
     * 是否启用异步获取原始链接
     * @var bool
     */
    protected $asyncGetRealHref = true;

    /**
     * 快照链接正则校验
     * @var string
     */
    protected $snapShootPattern = '';

    /**
     * 搜索结果正则表达式，匹配标题和超链接
     * @var string
     */
    protected $searchResultPattern = '';

    /**
     * 搜索引擎快照链接跳转页面，匹配真实链接正则表达式
     * @var string
     */
    protected $realHrefPattern = '';

    /**
     * 请求的headers参数
     * @var array
     */
    protected $headers = [];

    /**
     * 请求的Cookies和响应后的Cookie
     * @var array
     */
    protected $cookies = [];

    /**
     * Cookie域名
     * @var string
     */
    protected $cookieDomain = '';

    /**
     * 请求结束是否更新cookie
     * @var bool
     */
    protected $updateCookies = true;

    /**
     * 请求的响应对象
     * @var object Psr\Http\Message\ResponseInterface
     */
    protected $response = null;

    /**
     * 请求结果数据
     * @var array
     */
    protected $data = [];


    /**
     * 获取关键词列表标题及链接
     * @param $keyword
     * @param $page
     * @return array
     */
    abstract public function getSearchResult($keyword, $page, $ordUrl = true);


    /**
     * 计算分页
     * @param $page
     * @return integer
     */
    abstract protected function getPn($page);

    /**
     * 构造方法
     * Base constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
    }

    /**
     * 设置配置属性
     * @param array $config
     */
    public function setConfig($config = [])
    {
        foreach ($config as $key => $val) {
            $this->{$key} = $val;
        }
    }

    /**
     * 获取配置属性
     * @param $name
     * @return mixed
     */
    public function getConfig($name)
    {
        return $this->{$name};
    }

    /**
     * 获取请求的响应结果, 参考地址：https://guzzle-cn.readthedocs.io/zh_CN/latest/quickstart.html#id7
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 获取请求后的结果
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取Cookie
     * @return array
     */
    public function getCookies()
    {
        $cookies = [];
        foreach ($this->cookies as $key => $val) {
            $cookies[] = [
                'Name' => $key,
                'Value' => $val
            ];
        }
        return $cookies;
    }

    /**
     * 设置Cookie
     * @param $cookies
     * @return $this
     */
    public function setCookies($cookies)
    {
        if (is_array($cookies)) {
            foreach ($cookies as $cookie) {
                $this->cookies[$cookie['Name']] = $cookie['Value'];
            }
        } else if (is_string($cookies)) {
            $cookieArr = explode(';', trim($cookies, ';'));
            foreach ($cookieArr as $cookie) {
                $cookie = array_map('trim', explode('=', $cookie, 2));
                $this->cookies[$cookie[0]] = $cookie[1];
            }
        }
        return $this;
    }

    /**
     * 删除Cookie
     * @param string $key
     * @return $this
     */
    public function delCookies($key = '')
    {
        if ($key) {
            unset($this->cookies[$key]);
        } else {
            $this->cookies = [];
        }
        return $this;
    }

    /**
     * 发送请求
     * @param $url
     * @param array $data
     * @param string $method
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest($url, $data = [], $method = "GET")
    {
        $method = strtoupper($method);
        $client = (new Client(['timeout' => $this->timeout]));
        $cookieJar = CookieJar::fromArray($this->cookies, $this->cookieDomain);
        $queryOption = [
            'headers' => $this->headers,
            'cookies' => $cookieJar
        ];
        if ($method == 'GET' && $data) {
            $queryOption['query'] = $data;
        }
        if ($method == 'POST' && $data) {
            $queryOption['body'] = $data;
        }
        $response = $client->request($method, $url, $queryOption);
        if ($this->updateCookies) {
            $this->setCookies($cookieJar->toArray());
        }
        return $response;
    }

    /**
     * 提取原始链接
     * @param string $href 需要查询原始链接的请求地址
     * @return string
     */
    protected function realHref(String $href)
    {
        $res = preg_match($this->snapShootPattern, $href); //正则是否是快照地址
        if ($res) {
            $client = new Client();
            $response = $client->request('GET', $href, ['allow_redirects' => false]);
            return $this->extractRealHref($response);
        } else {
            return $href;
        }
    }

    /**
     * 请求快照地址，正则或302获取原始链接
     * @param ResponseInterface $response
     * @return mixed|string
     */
    protected function extractRealHref(ResponseInterface $response)
    {
        $code = $response->getStatusCode();
        if ($code == 302) {
            $location = $response->getHeader('Location');
            if (isset($location[0])) {
                return $location[0];
            } else {
                return '';
            }
        } else if ($code == 200) {
            $body = (string)$response->getBody();
            preg_match($this->realHrefPattern, $body, $match);
            if (isset($match[1])) {
                return $match[1];
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * 异步批量提取原始链接
     * @param array $data 请求数据数组
     * @param string $urlField 需要访问的链接地址键名
     * @param string $originalUrlField 原始链接地址键名
     * @return array
     */
    protected function asyncRealHref(Array $data, $urlField = 'url', $originalUrlField = 'original_url')
    {
        $urlData = [];
        $urlIndex = [];
        foreach ($data as $index => $item) {
            $res = preg_match($this->snapShootPattern, $item[$urlField]);
            if ($res) {
                $urlData[] = $item[$urlField];
                $urlIndex[] = $index;
            } else {
                $item[$originalUrlField] = $item[$urlField];
            }
        }
        if ($urlData) {
            $client = new Client();
            $requests = function ($urlArr) {
                foreach ($urlArr as $url) {
                    yield new Request('GET', $url);
                }
            };
            $pool = new Pool($client, $requests($urlData), [
                'concurrency' => count($urlData),
                'options' => [
                    'allow_redirects' => false
                ],
                'fulfilled' => function ($response, $index) use (&$data, $urlIndex, $originalUrlField) {
                    $dataIndex = $urlIndex[$index];
                    $data[$dataIndex][$originalUrlField] = $this->extractRealHref($response);
                },
                'rejected' => function ($reason, $index) use (&$data, $urlIndex, $originalUrlField) {
                    $dataIndex = $urlIndex[$index];
                    $data[$dataIndex][$originalUrlField] = '';
                },
            ]);
            $promise = $pool->promise();
            $promise->wait();
        }
        return (array)$data;
    }

    /**
     * 去掉多余的html及换行空格等
     * @param $string
     * @return string
     */
    protected function cutstrHtml($string)
    {
        $string = strip_tags($string);
        $string = str_replace(["\t", "\r\n", "\r", "\n", " "], [], $string);
        return trim($string);
    }

    /**
     * 检测超链接是否标准，不标准的增加域名
     * @param $url
     * @return string
     */
    protected function completionUrl($url)
    {
        if (preg_match('/^((http:\/\/)|(https:\/\/)).+$/', $url)) {
            return $url;
        } else {
            return $this->baseUri . $url;
        }
    }
}

