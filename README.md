# PHP搜索引擎爬虫

搜索关键词，抓取搜索引擎结果。

本功能主要使用Guzzle充当PHP客户端访问搜索引擎。

抓取正则表达式，最后一次更新为：2021年05月01日。

正则表达式推荐工具RegexBuddy。

# 安装方法

``` 
composer require diaojinlong/collect-search-engine
```

# 使用方法

```
use DiaoJinLong\CollectSearchEngine\EngineRequest;

$engine = 'baidu'; //搜索引擎:baidu=百度搜索,so=360搜索
$config = []; //搜索引擎配置，详情见下方细则
$keyword = 'PHP'; //搜索关键词
$page = 1; //搜索第几页
$wantOriginalUrl = true; //是否获取快照的真实地址
$engine = new EngineRequest('baidu', $config); //初始化搜索引擎
$res = $engine->getSearchResult($keyword, $page, $wantOriginalUrl); //进行搜索
var_dump($res->getData()); //打印搜索内容

```

###返回结果

```
Array
(
    [0] => Array
        (
            [title] => PHP:HypertextPreprocessor
            [url] => http://www.baidu.com/link?url=9p9X8nV1-1WiFuMJuxuPrIZbTHewjh04GJpOWMhUENq
            [original_url] => https://www.php.net/
        )

    [1] => Array
        (
            [title] => PHP(计算机编程语言)-百度百科
            [url] => http://www.baidu.com/link?url=wyp17FoB2lbo2hKrsYijkCjEFSGMH81h21U601eOnJki4OLvjj-ybKmf8_loZ8mgWfsAujWw1Cpol6NUOWetLK
            [original_url] => https://baike.baidu.com/item/PHP/9337?fr=aladdin
        )

    [2] => Array
        (
            [title] => 达内-PHP培训血拼120天=2年工作经验+高薪就业春季名额限时抢
            [url] => http://www.baidu.com/aladdin.php?url=060000aUsytRgffGrMzMJhXJ5VPm8WXp6cXpJ4ZM0f2ZNnIx8aHK8kHoJQZQrUj28koBhHA-82cOxrHpYY6huj4NlD91pwj6eUkF6H_utAw8sSCyID5K-PUC1JCMiahr5W-pF6RS6VQM4R_5amcIzIAauOXigwWH8lSS8BCcCdtTl6G1VxDTGhtDUB7CChw3yOpSxGpSrREVnmkW9J4rt_y3zmCc.7Y_a6heKzfupZwGCfprAj9Cfml_TZxqjvOlO5tEV4TVaAUvutVj5lOQ5SU3TO3OWMuCLuljl77rxF__3eq-xZr1w814QQPhHG____oo4xY5UOotOzSLJGyAp7W_LePLm.THdspZ00uhN-uA-b5HDkn1bsr0KGTdqLpgF-UAN1T1Ys0ANzTAPEuARqRjcLnHD0mLFW5HcdPWmk
            [original_url] => http://www.baidu.com/aladdin.php?url=060000aUsytRgffGrMzMJhXJ5VPm8WXp6cXpJ4ZM0f2ZNnIx8aHK8kHoJQZQrUj28koBhHA-82cOxrHpYY6huj4NlD91pwj6eUkF6H_utAw8sSCyID5K-PUC1JCMiahr5W-pF6RS6VQM4R_5amcIzIAauOXigwWH8lSS8BCcCdtTl6G1VxDTGhtDUB7CChw3yOpSxGpSrREVnmkW9J4rt_y3zmCc.7Y_a6heKzfupZwGCfprAj9Cfml_TZxqjvOlO5tEV4TVaAUvutVj5lOQ5SU3TO3OWMuCLuljl77rxF__3eq-xZr1w814QQPhHG____oo4xY5UOotOzSLJGyAp7W_LePLm.THdspZ00uhN-uA-b5HDkn1bsr0KGTdqLpgF-UAN1T1Ys0ANzTAPEuARqRjcLnHD0mLFW5HcdPWmk
        )

    [3] => Array
        (
            [title] => PHP简介|菜鸟教程
            [url] => http://www.baidu.com/link?url=BkRDW0md1bM_MRfJVykSTz4KkG8jGznz43NhrgdRxU3NLckJbhJqopU7vA1lYAm1NCzy0AFOaitPwFTnSNbHt_
            [original_url] => https://www.runoob.com/php/php-intro.html
        )

    [4] => Array
        (
            [title] => PHP中文网,PHP培训,PHP,HTML教程,PHP程序员,PHP安装,PHP手...
            [url] => http://www.baidu.com/link?url=DQI8ikJ4o-fegPVAZm3O0g7HbOMA81qGG7rPsITWmTtUZjKvtWmGNRTWHC5kCk2-xtLNVZ_1SB4wTeeChDZhWLHn1ItgG6Vc47bRGOpE5BG
            [original_url] => https://www.phpphp.com/phpcode?order=comment_count&cao_type=0
        )

    [5] => Array
        (
            [title] => PHP教程
            [url] => http://www.baidu.com/link?url=QAThMcoL5XX1fL_o78UqfbJ-I_GRmFV-UTUBmaymxRKc68dCIS2ChqB5D54uQh4r_W-gEfwo61h44b7vZ4fL8K
            [original_url] => https://www.w3school.com.cn/php/index.asp
        )

    [6] => Array
        (
            [title] => php中文网-教程_手册_视频-免费php在线学习平台
            [url] => http://www.baidu.com/link?url=ROVrM8gcBn5Lk3qepnO4V533d9GZFBQJDIz5u-RLUhC
            [original_url] => https://www.php.cn/
        )

    [7] => Array
        (
            [title] => PHPChina-最棒的PHP中文社区
            [url] => http://www.baidu.com/link?url=LKkWqd1dxQ2a50kBKFD385PKadTM7VWHu905dPVga_lMhiJeIvT5QGhs4rGlEMWm
            [original_url] => http://www.phpchina.com/
        )

    [8] => Array
        (
            [title] => php吧-百度贴吧
            [url] => http://www.baidu.com/link?url=6xU5i9-E8fMSAqjifaZH21waPLXDY4S8Lf8qmlFa_4_fQ8s4Anj0-7w871eCKytGIVsiZZpSEFf-6jFvLIKoBq
            [original_url] => http://tieba.baidu.com/f?kw=php&fr=ala0&tpl=5
        )

    [9] => Array
        (
            [title] => php开发环境集成_技术交流_源码时代官网
            [url] => http://www.baidu.com/link?url=OudD8FxSEnmvDZe_l-NYJDXgmp4aqMaiS24r51WBnirKxMyUh6NnqHQ3e2qamxi5TzzJmYAskI4s8KdfjlElbq
            [original_url] => https://www.itsource.cn/web/news/5/20170306/1144.html
        )

)
```

###请求使用Cookie

```
// 字符串用法
$cookies = 'PSTM=1619320035; BAIDUID=B19E0FB94756221AEA7BA8DF02654C5E:FG=1; BIDUPSID=B80B865D655AE3F8C0CA95275EA2A74E;'; //从浏览器Request Headers中复制的Cookie字符串

// 数组用法
$cookies = [
    [
        "Name"=>"PSTM",
        "Value"=>"1619320035"
    ],
    [
        "Name"=>"BAIDUID",
        "Value"=>"B19E0FB94756221AEA7BA8DF02654C5E:FG=1"
    ],
    [
        "Name"=>"BIDUPSID",
        "Value"=>"B80B865D655AE3F8C0CA95275EA2A74E"
    ]
];

$engine = new EngineRequest('baidu', $config); //初始化搜索引擎
$res = $engine->setCookies($cookies)->getSearchResult($keyword, $page, $wantOriginalUrl); //进行搜索
var_dump($res->getData()); //打印搜索内容
var_dump($res->getCookies()); //访问百度后更新的Cookie
var_dump($res->getHtml()); //访问百度的html


```

###Config配置

```
$config = [
    'baseUri'=>'', //搜索引擎网址
    'path'=>'', //搜索引擎路径参数
    'timeout'=>30, //超时时间单位（秒）
    'asyncGetRealHref'=>true, //是否启用异步获取原始链接
    'snapShootPattern'=>'', //快照链接正则校验
    'searchResultPattern'=>'', //搜索结果正则表达式，匹配标题和超链接
    'realHrefPattern'=>'', //搜索引擎快照链接跳转页面，匹配真实链接正则表达式
    'headers'=>[], //请求的headers参数，格式参考：['Connection' => 'keep-alive','Accept' => 'text/html']
    'cookies'=>[], //请求的Cookies参数，格式参考：[["Name"=>"PSTM","Value"=>"1619320035"],["Name"=>"BAIDUID","Value"=>"B19E0FB94756221AEA7BA8DF02654C5E:FG=1"]]
    'cookieDomain'=>'', //Cookie域名
];
// 一般都不需要初始化配置，在每个搜索引擎类里已定义，除非定义的内容已经过时无法使用。
```
