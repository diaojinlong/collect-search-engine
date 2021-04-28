<?php

namespace DiaoJinLong\CollectSearchEngine\Engine;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class So extends Base
{
    public $base_uri = 'https://www.so.com';

    public $path = '/s?q=%s&pn=%u';

    public $timeout = 30;

    public $snapShootPattern = '/www\.so\.com\/link\?m=/';

    public function getSearchResult($keyword, $page, $wantOriginalUrl = false)
    {
        $client = (new Client(['base_uri' => $this->base_uri, 'timeout' => $this->timeout]));
        $path = sprintf($this->path, $keyword, $this->getPn($page));
        $request = $client->request('GET', $path, [
            'headers' => [
                'Connection' => 'Keep-Alive',
                'Accept' => 'text/html, application/xhtml+xml, */*',
                'Accept-Language' => 'en-US,en;q=0.8,zh-Hans-CN;q=0.5,zh-Hans;q=0.3',
                'Accept-Encoding' => 'gzip, deflate',
                'User-Agent' => 'Mozilla/6.1 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko'
            ]
        ]);
        $body = (string)$request->getBody();
        preg_match_all('/<h3.*?res-title[\s\S]*?>[\s\S]*?<a[\s\S]*?href\s*?=\s*?"(.+?)"[\s\S]*?>([\s\S]*?)<\/a>[\s\S]*?<\/h3>/', $body, $match);
        $data = [];
        foreach ($match[2] as $key => $val) {
            $url = $this->completionUrl($match[1][$key]);
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
        return $data;
    }

    public function realHref($href)
    {
        $res = preg_match('/www\.so\.com\/link\?m=/', $href);
        if ($res) {
            $client = new Client();
            $res = $client->request('GET', $href, ['allow_redirects' => false]);
            $code = $res->getStatusCode();
            if ($code == 200) {
                $body = (string)$res->getBody();
                preg_match('/URL=\'([^\']+)\'/', $body, $match);
                if (isset($match[1])) {
                    return $match[1];
                } else {
                    return '';
                }
            } else {
                return '';
            }
        } else {
            return $href;
        }
    }

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
            preg_match('/URL=\'([^\']+)\'/', $body, $match);
            if (isset($match[1])) {
                return $match[1];
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    protected function getPn($page)
    {
        return $page;
    }
}
