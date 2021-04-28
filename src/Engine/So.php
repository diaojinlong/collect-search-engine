<?php

namespace DiaoJinLong\CollectSearchEngine\Engine;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class So extends Base
{

    protected $baseUri = 'https://www.so.com';

    protected $path = '/s?q=%s&pn=%u';

    protected $snapShootPattern = '/www\.so\.com\/link\?m=/';

    protected $searchResultPattern = '/<li\s+(?:data-kzx="1"\s+)?class="res-list(?:\s+spite)?"[\s\S.]*?>[\s\S.]*?<h3[\s\S.]+?<a[\s\S.]*?href\s?=\s*?"(?<href>.*?)"[\s\S.]+?>(?<title>[\s\S.]+?)<\/a>/';

    protected $realHrefPattern = '/URL=\'([^\']+)\'/';

    protected $headers = [
        'Connection' => 'keep-alive',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'Accept-Language' => 'zh-CN,zh;q=0.9',
        'Accept-Encoding' => 'gzip, deflate, br',
        'Cache-Control' => 'max-age=0',
        'Host' => 'www.so.com',
        'Referer' => 'https://www.so.com/',
        'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="90", "Google Chrome";v="90"',
        'sec-ch-ua-mobile' => '?0',
        'Sec-Fetch-Dest' => 'document',
        'Sec-Fetch-Mode' => 'navigate',
        'Sec-Fetch-Site' => 'same-origin',
        'Sec-Fetch-User' => '?1',
        'Upgrade-Insecure-Requests' => '1',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36'
    ];

    protected $cookieDomain = 'so.com';

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
        $client = (new Client(['base_uri' => $this->baseUri, 'timeout' => $this->timeout]));
        $path = sprintf($this->path, $keyword, $this->getPn($page));
        $cookieJar = CookieJar::fromArray($this->cookies, $this->cookieDomain);
        $request = $client->request('GET', $path, [
            'headers' => $this->headers,
            'cookies' => $cookieJar
        ]);
        $body = (string)$request->getBody();
        $this->html = $body;
        preg_match_all($this->searchResultPattern, $body, $match);
        $this->setCookies($cookieJar->toArray());
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
        return $page;
    }
}
