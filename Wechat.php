<?php
/**
 * @project wechatPublicPlatform
 * @date 2015-4-3
 * @author xialei <xialeistudio@gmail.com>
 * @link http://www.ddhigh.com
 */

/**
 * 使用本程序最低PHP版本为5.4
 * 如果想在低版本PHP使用，请更改以下选项
 * 更改数组形式为array
 * 更改json_encode的第二个参数，请使用其他方式实现JSON编码不转成UTF-8
 *
 * 微信公众平台开发
 * Class Wechat
 * @link http://mp.weixin.qq.com/wiki/home/index.html
 */
class Wechat
{
    /**
     * @var string 微信公众号APPID
     */
    private $appId;
    /**
     * @var string 微信公众号APPKEY
     */
    private $appKey;

    /**
     * @var string 通信TOKEN
     */
    private $token;
    /**
     * @var string 请求微信接口必须参数
     */
    private $access_token;

    /**
     * @var array 微信公众平台交互数据
     */
    private $pushData;

    /**
     * 实例化
     * @param string $appId AppID
     * @param string $appKey AppKey
     * @param string $token 通信密钥
     * @param string $accessToken AccessToken
     * @throws Exception 消息签名失败
     */
    function __construct($appId, $appKey, $token, $accessToken = '')
    {
        $this->appId = $appId;
        $this->appKey = $appKey;
        $this->token = $token;
        $this->checkSign();
        if (!empty($accessToken)) {
            $this->access_token = $accessToken;
        } else {
            $this->getAccessTokenFromRemote();
        }
    }

    /**
     * 获取AppID
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * 获取AppKey
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * 获取AccessToken
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * 设置AccessToken
     * @param string $access_token AccessToken
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 执行POST请求
     * @param string $url 请求地址
     * @param array $params GET参数，会拼接到URL中
     * @param array $data POST参数
     * @param bool $return 是否返回执行结果
     * @return array|null
     * @throws Exception 请求出错
     */
    private function post($url, $params = [], $data = [], $return = true)
    {
        $ch = curl_init();
        //GET参数处理
        $GetParams = urldecode(http_build_query($params));
        //URL处理
        if (strpos($url, '?')) {
            $url .= '&' . $GetParams;
        } else {
            $url .= '?' . $GetParams;
        }
        $data = is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
        //通用参数设置
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($return) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        }
        //关闭SSL验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if ($return) {
            $resp = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($resp, true);
            if (isset($json['errcode']) && $json['errcode'] != 0) {
                throw new Exception($json['errmsg'], $json['errcode']);
            }
            return $json;
        } else {
            curl_exec($ch);
            curl_close($ch);
            return null;
        }
    }

    /**
     * 执行GET请求
     * @param string $url 请求地址
     * @param array $params GET参数，会拼接到URL中
     * @param bool $return 是否返回执行结果
     * @return mixed|null
     * @throws Exception 请求出错
     */
    private function get($url, $params = [], $return = true)
    {
        $ch = curl_init();
        //GET参数处理
        $GetParams = urldecode(http_build_query($params));
        //URL处理
        if (strpos($url, '?')) {
            $url .= '&' . $GetParams;
        } else {
            $url .= '?' . $GetParams;
        }
        //通用参数设置
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($return) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        }
        //关闭SSL验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if ($return) {
            $resp = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($resp, true);
            if (isset($json['errcode']) && $json['errcode'] != 0) {
                throw new Exception($json['errmsg'], $json['errcode']);
            }
            return $json;
        } else {
            curl_exec($ch);
            curl_close($ch);
            return null;
        }
    }

    /**
     * curl上传文件
     * @param string $url 上传地址
     * @param string $path 本地文件路径
     * @param array $params GET 参数
     * @param string $field 字段名，接收使用$_FILES[$field]接收
     * @param bool $return 是否返回
     * @return mixed|null 上传结果
     * @throws Exception
     */
    private function upload($url, $path, $params = [], $field = 'media', $return = true)
    {
        $ch = curl_init();
        //GET参数处理
        $GetParams = urldecode(http_build_query($params));
        //URL处理
        if (strpos($url, '?')) {
            $url .= '&' . $GetParams;
        } else {
            $url .= '?' . $GetParams;
        }
        //通用参数设置
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($return) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        }
        //关闭SSL验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //上传兼容处理
        curl_setopt($ch, CURLOPT_POST, 1);
        if (class_exists('\CURLFile')) {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                $field => new CURLFile(realpath($path)),
            ]);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                $field => '@' . realpath($path)
            ]);
        }
        if ($return) {
            $resp = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($resp, true);
            if (isset($json['errcode']) && $json['errcode'] != 0) {
                throw new Exception($json['errmsg'], $json['errcode']);
            }
            return $json;
        } else {
            curl_exec($ch);
            curl_close($ch);
            return null;
        }
    }

    /**
     * 从微信服务器读取AccessToken
     * @param bool $setToSelf 是否设置到本实例
     * @return mixed|null
     * [
     *  'access_token'=>'AccessToken',  微信AccessToken
     *  'expires_in'=>'过期时间'    该AccessToken在多久以后失效，单位（秒）
     * ]
     */
    public function getAccessTokenFromRemote($setToSelf = true)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token';
        $params = [
            'grant_type' => 'client_credential',
            'appid' => $this->appId,
            'secret' => $this->appKey
        ];
        $data = $this->get($url, $params);
        if ($setToSelf) {
            $this->setAccessToken($data['access_token']);
        }
        return $data;
    }

    /**
     * 获取微信服务器IP地址
     * @return array ip列表 ['127.0.0.1','127.0.0.1']
     * @throws Exception
     */
    public function getWechatServerAddress()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = $this->get($url, $params);
        return $data['ip_list'];
    }

    /**
     * 检测消息签名
     * @throws Exception
     */
    private function checkSign()
    {
        //如果是首次接入，直接通过
        if (isset($_GET['echostr'])) {
            echo $_GET['echostr'];
            exit;
        }
        //参数检测
        if (!isset($_GET['signature'])) {
            throw new Exception('缺少签名参数');
        }
        if (!isset($_GET['timestamp'])) {
            throw new Exception('缺少时间戳');
        }
        if (!isset($_GET['nonce'])) {
            throw new Exception('缺少随机数');
        }
        //签名验证
        $params = [
            $this->token,
            $_GET['timestamp'],
            $_GET['nonce']
        ];
        sort($params, SORT_STRING);
        $str = implode($params);
        $str = sha1($str);
        if ($str != $_GET['signature']) {
            throw new Exception('消息签名失败');
        }
    }

    /**
     * 获取微信推送的数据
     * @return array
     */
    public function requestWechatPush()
    {
        $raw = file_get_contents('php://input');
        $xml = new SimpleXMLElement($raw);
        foreach ($xml as $key => $value) {
            $this->pushData[$key] = $value;
        }
        return $this->pushData;
    }

    /**
     * * 响应微信发送的信息（自动回复）
     * @param  array $content 回复信息，文本信息为string类型
     * @param  string $type 消息类型
     * @param int|string $flag 是否新标刚接受到的信息
     * @return string XML字符串
     */
    public function response($content, $type = 'text', $flag = 0)
    {
        /* 基础数据 */
        $this->pushData = array(
            'ToUserName' => $this->pushData['FromUserName'],
            'FromUserName' => $this->pushData['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => $type,
        );
        /* 添加类型数据 */
        if ($type != 'transfer_customer_service') {
            $this->$type($content);
        }
        /* 添加状态 */
        $this->pushData['FuncFlag'] = $flag;
        /* 转换数据为XML */
        $xml = new SimpleXMLElement('<xml></xml>');
        $this->data2xml($xml, $this->pushData);
        return $xml->asXML();
    }

    /**
     * 回复文本信息
     * @param  string $content 要回复的信息
     */
    private function text($content)
    {
        $this->pushData['Content'] = $content;
    }

    /**
     * 回复音乐信息
     * @param  string $content 要回复的音乐
     */
    private function music($music)
    {
        list(
            $music['Title'],
            $music['Description'],
            $music['MusicUrl'],
            $music['HQMusicUrl']
            ) = $music;
        $this->pushData['Music'] = $music;
    }

    /**
     * 回复图文信息
     * @param  array $news 要回复的图文内容
     */
    private function news($news)
    {
        $articles = array();
        foreach ($news as $key => $value) {
            list(
                $articles[$key]['Title'],
                $articles[$key]['Description'],
                $articles[$key]['PicUrl'],
                $articles[$key]['Url']
                ) = $value;
            if ($key >= 9) {
                break;
            } //最多只允许10调新闻
        }
        $this->pushData['ArticleCount'] = count($articles);
        $this->pushData['Articles'] = $articles;
    }

    /**
     * 数据XML编码
     * @param  object $xml XML对象
     * @param  mixed $data 数据
     * @param  string $item 数字索引时的节点名称
     * @return string
     */
    private function data2xml($xml, $data, $item = 'item')
    {
        foreach ($data as $key => $value) {
            /* 指定默认的数字key */
            is_numeric($key) && $key = $item;
            /* 添加子元素 */
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                $this->data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }


    /**
     * 添加客服账号
     * @param array $data 客服账号数据
     * [
     *  'kf_account'=>'test@wechat',wechat为公众平台账号名称,test为客服账号前缀
     *  'nickname'=>'客服昵称',
     *  'password'=>'password' password为32位MD5加密结果
     * ]
     * @return bool 添加结果 成功|失败
     * @throws Exception
     */
    public function addCustomer($data)
    {
        $url = 'https://api.weixin.qq.com/customservice/kfaccount/add';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        return $this->post($url, $params, $data);
    }

    /**
     * 修改客服账号
     * @param array $data 客服账号数据
     * [
     *  'kf_account'=>'test@wechat',wechat为公众平台账号名称,test为客服账号前缀
     *  'nickname'=>'客服昵称',
     *  'password'=>'password' password为32位MD5加密结果
     * ]
     * @return bool 修改结果 成功|失败
     * @throws Exception
     */
    public function updateCustomer($data)
    {
        $url = 'https://api.weixin.qq.com/customservice/kfaccount/update';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        return $this->post($url, $params, $data);
    }

    /**
     * 删除客服账号
     * @param array $data 客服账号数据
     * [
     *  'kf_account'=>'test@wechat',wechat为公众平台账号名称,test为客服账号前缀
     *  'nickname'=>'客服昵称',
     *  'password'=>'password' password为32位MD5加密结果
     * ]
     * @return bool 删除结果 成功|失败
     * @throws Exception
     */
    public function deleteCustomer($data)
    {
        $url = 'https://api.weixin.qq.com/customservice/kfaccount/del';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        return $this->post($url, $params, $data);
    }

    /**
     * 设置客服头像
     * @param string $account 客服账号 test@wechat
     * @param string $path 图片地址，支持远程图片
     * @return bool
     * @throws Exception
     */
    public function setCustomerAvatar($account, $path)
    {
        $url = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg';
        $params = [
            'access_token' => $this->getAccessToken(),
            'kf_account' => $account
        ];
        //处理远程图片
        $file_path = '';//文件路径
        if (strpos($path, 'http') !== false) {
            $local_handler = fopen($file_path, 'wb');
            if (!$local_handler) {
                throw new Exception('临时文件创建失败');
            }
            $remote_handler = fopen($path, 'rb');
            while (!feof($remote_handler)) {
                //8K缓冲区
                fwrite($local_handler, fread($remote_handler, 8192));
            }
            //关闭句柄
            fclose($local_handler);
            fclose($remote_handler);
        } else {
            $file_path = $path;
        }
        //准备上传
        $resp = $this->upload($url, $file_path, $params);
        //删除文件
        if (strpos($path, 'http') !== false) {
            unlink($file_path);
        }
        return $resp;
    }

    /**
     * 获取客服列表
     * @return array
     * [
     *      [
     *      "kf_account"=>"test1@test",
     *       "kf_nick"=>"ntest1",
     *       "kf_id"=>"1001"
     *       kf_headimgurl"=>"http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw/0""
     *      ]
     * ]
     * @throws Exception
     */
    public function getCustomers()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = $this->get($url, $params);
        return $data['kf_list'];
    }


    /**
     * 发送客服消息
     * @param string $openid 用户ID
     * @param array|string $sendData 要发送的数据
     * @param string $type 消息类型
     * @param string $customer 是否指定客服账号发送
     * @return array|null
     * @throws Exception
     * @link http://mp.weixin.qq.com/wiki/1/70a29afed17f56d537c833f89be979c9.html#.E5.AE.A2.E6.9C.8D.E6.8E.A5.E5.8F.A3-.E5.8F.91.E6.B6.88.E6.81.AF 参考文档链接
     */
    public function sendCustomerMessage($openid, $sendData, $type = 'text', $customer = '')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = [
            'touser' => $openid,
            'msgtype' => $type
        ];
        if (!empty($customer)) {
            $data['customservice'] = [
                'kf_account' => $customer
            ];
        }
        switch ($type) {
            case 'text':
                $data['text'] = [
                    'content' => $sendData
                ];
                break;
            case 'image':
            case 'voice':
                $data[$type] = [
                    'media_id' => $sendData
                ];
                break;
            case 'video':
            case 'music':
                $data[$type] = $sendData;
                break;
            case 'news':
                $data['news'] = [
                    'articles' => $sendData
                ];
                break;
            default:
                throw new Exception('消息类型不存在');
                break;
        }

        return $this->post($url, $params, $data);
    }

    /**
     * 上传图文素材
     * @param array $articles
     * @return array|null
     * @throws Exception
     * @link http://mp.weixin.qq.com/wiki/15/5380a4e6f02f2ffdc7981a8ed7a40753.html#.E4.B8.8A.E4.BC.A0.E5.9B.BE.E6.96.87.E6.B6.88.E6.81.AF.E7.B4.A0.E6.9D.90.E3.80.90.E8.AE.A2.E9.98.85.E5.8F.B7.E4.B8.8E.E6.9C.8D.E5.8A.A1.E5.8F.B7.E8.AE.A4.E8.AF.81.E5.90.8E.E5.9D.87.E5.8F.AF.E7.94.A8.E3.80.91
     */
    public function uploadNews(array $articles)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = [
            'articles' => $articles
        ];
        return $this->post($url, $params, $data);
    }

    /**
     * 根据分组进行群发
     * @param string|array $data 数据
     * @param int $group_id 分组ID
     * @param string $type 消息类型
     * @return array|null
     * @throws Exception
     * @link http://mp.weixin.qq.com/wiki/15/5380a4e6f02f2ffdc7981a8ed7a40753.html#.E6.A0.B9.E6.8D.AE.E5.88.86.E7.BB.84.E8.BF.9B.E8.A1.8C.E7.BE.A4.E5.8F.91.E3.80.90.E8.AE.A2.E9.98.85.E5.8F.B7.E4.B8.8E.E6.9C.8D.E5.8A.A1.E5.8F.B7.E8.AE.A4.E8.AF.81.E5.90.8E.E5.9D.87.E5.8F.AF.E7.94.A8.E3.80.91
     */
    public function sendMassToGroup($data, $group_id, $type = 'text')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $send = [
            'filter' => [
                'is_to_all' => false,
                'group_id' => $group_id
            ],
            'msgtype' => $type
        ];

        switch ($type) {
            case 'text':
                $send['text'] = [
                    'content' => $data
                ];
                break;
            case 'mpnews':
            case 'voice':
            case 'image':
            case 'mpvideo':
                $send[$type] = [
                    'media_id' => $data
                ];
                break;
        }
        return $this->post($url, $params, $send);
    }

    /**
     * 上传视频
     * @param string $media_id 视频ID
     * @param string $title 视频标题
     * @param string $description 视频简介
     * @return array|null
     * @throws Exception
     */
    public function uploadVideo($media_id, $title, $description)
    {
        $url = 'https://file.api.weixin.qq.com/cgi-bin/media/uploadvideo';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = [
            'media_id' => $media_id,
            'title' => $title,
            'description' => $description
        ];
        return $this->post($url, $params, $data);
    }

    /**
     * 根据Openid列表进行群发
     * @param array $openids OPENID列表
     * @param array|string $data 数据
     * @param string $type 消息类型
     * @return array|null
     * @throws Exception
     */
    public function sendMassToOpenids(array $openids, $data, $type = 'text')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send';
        $params = [
            'access_token' => $this->getAccessToken()
        ];

        $send = [
            'touser' => $openids,
            'msgtype' => $type
        ];

        switch ($type) {
            case 'text':
                $send['text'] = [
                    'content' => $data
                ];
                break;
            case 'mpnews':
            case 'voice':
            case 'image':
            case 'mpvideo':
                $send[$type] = [
                    'media_id' => $data
                ];
                break;
        }
        return $this->post($url, $params, $send);
    }

    /**
     * 删除群发
     * @param int $msg_id 消息ID
     * @return array|null
     * @throws Exception
     */
    public function deleteMass($msg_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = [
            'msg_id' => $msg_id
        ];
        return $this->post($url, $params, $data);
    }

    /**
     * 预览群发消息
     * @param string $openid 预览者ID
     * @param array|string $data 数据
     * @param string $type 消息类型
     * @return array|null
     * @throws Exception
     */
    public function previewMass($openid, $data, $type = 'text')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview';
        $params = [
            'access_token' => $this->getAccessToken()
        ];

        $send = [
            'touser' => $openid,
            'msgtype' => $type
        ];

        switch ($type) {
            case 'text':
                $send['text'] = [
                    'content' => $data
                ];
                break;
            case 'mpnews':
            case 'voice':
            case 'image':
            case 'mpvideo':
                $send[$type] = [
                    'media_id' => $data
                ];
                break;
        }
        return $this->post($url, $params, $send);
    }

    /**
     * 获取群发消息状态
     * @param int $msg_id 消息ID
     * @return array|null
     * @throws Exception
     */
    public function getMassStatus($msg_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/get';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = [
            'msg_id' => $msg_id
        ];
        return $this->post($url, $params, $data);
    }

    /**
     * 设置模板消息所属行业
     * @param int $industry_id1 行业一ID
     * @param int $industry_id2 行业二ID
     * @return array|null
     * @throws Exception
     * @link http://mp.weixin.qq.com/wiki/17/304c1885ea66dbedf7dc170d84999a9d.html#.E8.AE.BE.E7.BD.AE.E6.89.80.E5.B1.9E.E8.A1.8C.E4.B8.9A
     */
    public function setTemplateIndustry($industry_id1, $industry_id2)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = [
            'industry_id1' => $industry_id1,
            'industry_id2' => $industry_id2
        ];
        return $this->post($url, $params, $data);
    }

    /**
     * 获得模板ID
     * @param string $template_id_short 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * @return array|null
     * @throws Exception
     */
    public function getTemplateId($template_id_short)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = [
            'template_id_short' => $template_id_short
        ];
        return $this->post($url, $params, $data);
    }

    /**
     * 发送模板消息
     * @param string $openid 用户ID
     * @param string $template_id 模板ID
     * @param string $link 点击模板消息跳转链接
     * @param array $data 模板数据
     * @param string $topcolor
     * @return array|null
     * @throws Exception
     * @link http://mp.weixin.qq.com/wiki/17/304c1885ea66dbedf7dc170d84999a9d.html#.E5.8F.91.E9.80.81.E6.A8.A1.E6.9D.BF.E6.B6.88.E6.81.AF
     */
    public function sendTemplate($openid, $template_id, $link, $data, $topcolor = '#FF0000')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send';
        $params = [
            'access_token' => $this->getAccessToken()
        ];
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $link,
            'topcolor' => $topcolor,
            'data' => $data
        ];
        return $this->post($url, $params, $data);
    }
}