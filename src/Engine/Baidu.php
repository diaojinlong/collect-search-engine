<?php

namespace DiaoJinLong\CollectSearchEngine\Engine;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class Baidu extends Base
{

    protected $baseUri = 'https://www.baidu.com';

    protected $path = '/s?wd=%s&pn=%u&oq=%s&ie=utf-8&usm=0';

    protected $snapShootPattern = '/www\.baidu\.com\/link\?url=/';

    protected $searchResultPattern = '/<div\s+class="(?:(?:(?:result|result-op)\s+c-container\s+new-pmd)|(?:c-container\s+result))\s?[\s\S.]+?<a\s+(data-click="[\s\S.]+?")?[\s\S.]*?href\s?=\s*?"(?<href>http.*?)"[\s\S.]+?>(?<title>[\s\S.]+?)<\/a>/';

    protected $realHrefPattern = '/URL=\'([^\']+)\'/';

    protected $headers = [
        'Connection' => 'keep-alive',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'Accept-Language' => 'en-US,en;q=0.8,zh-Hans-CN;q=0.5,zh-Hans;q=0.3',
        'Accept-Encoding' => 'gzip, deflate',
        'Cache-Control' => 'max-age=0',
        'Host' => 'www.baidu.com',
        'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="90", "Google Chrome";v="90"',
        'sec-ch-ua-mobile' => '?0',
        'Sec-Fetch-Dest' => 'document',
        'Sec-Fetch-Mode' => 'navigate',
        'Sec-Fetch-Site' => 'none',
        'Sec-Fetch-User' => '?1',
        'Upgrade-Insecure-Requests' => '1',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36'
    ];

    protected $cookieDomain = 'baidu.com';

    /**
     * 构造方法
     * Baidu constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * 获取搜索结果
     * @param $keyword
     * @param $page
     * @param bool $wantOriginalUrl
     * @return $this|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSearchResult($keyword, $page, $wantOriginalUrl = false)
    {
        $url = $this->baseUri . sprintf($this->path, $keyword, $this->getPn($page), $keyword);
        $this->response = $this->sendRequest($url);
        $body = (string)$this->response->getBody();
        preg_match_all($this->searchResultPattern, $body, $match);
        $data = [];
        foreach ($match['title'] as $key => $val) {
            $url = $this->completionUrl($match['href'][$key]);
            $originalUrl = ($wantOriginalUrl && $this->asyncGetRealHref == false) ? $this->realHref($url) : '';
            $data[] = [
                'title' => $this->cutstrHtml($val),
                'url' => $url,
                'original_url' => $originalUrl
            ];
        }
        if ($wantOriginalUrl && $this->asyncGetRealHref) {
            $data = $this->asyncRealHref($data);
        }
        $this->data = $data;
        return $this;
    }

    /**
     * 计算分页
     * @param $page
     * @return float|int
     */
    protected function getPn($page)
    {
        return ($page - 1) * 10;
    }
}
