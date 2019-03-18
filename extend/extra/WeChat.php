<?php

namespace extra;

class WeChat
{

    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret)
    {
        $this->appId        = $appId;
        $this->appSecret    = $appSecret;
    }

    //判断是否微信浏览器
    public function is_weChat()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    //获取用户openid
    public function getOpenid()
    {
        if (input('code')) {
            $aUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appId . "&secret=" . $this->appSecret . "&code=" . input('code') . "&grant_type=authorization_code";
            //获取网页授权access_token和openid等
            $data = httpsRequest($aUrl);
            $data = json_decode($data);
            return ['data'=>$data,'code'=>1];
        } else {
            //获取当前的url地址
            $rUrl = urlencode(request()->root(true).request()->url());
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appId . "&redirect_uri=" . $rUrl . "&response_type=code&scope=snsapi_userinfo&state=123456#wechat_redirect";
            return ['url'=>$url,'code'=>2];

        }
    }

    /**
     * @Title: getUserInfo
     * @Description: todo(获取用户详细信息)
     * @Author: liu tao
     * @Time: xxx
     * @param null $openid
     * @param null $access_token
     * @param array $weChatUser
     * @param bool $is_update
     * @return array
     */
    public function getUserInfo($openid = null, $access_token = null,$weChatUser=[],$is_update=false)
    {
        if (!input('code')) {
            //获取当前的url地址
            $rUrl = urlencode(request()->root(true).request()->url());
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appId . "&redirect_uri=" . $rUrl . "&response_type=code&scope=snsapi_userinfo&state=123456#wechat_redirect";
            //跳转页面
            redirect($url, 0);
        } else {
            if (!$access_token && !$openid) {
                $getOpenidUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appId . "&secret=" . $this->appSecret . "&code=" . input('code') . "&grant_type=authorization_code";
                //获取网页授权access_token和openid等 redirect($getOpenidUrl,0);
                $data = httpsRequest($getOpenidUrl);
                $access_token = $data->access_token;
                $openid = $data->openid;
            }
            $getUserInfoUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
            //获取用户数据
            $data = httpsRequest($getUserInfoUrl);
            $userInfo = json_decode($data, true);
            if ($userInfo['openid']) {
                // 将信息插入数据库
                $login_count = isset($weChatUser['login_count'])?$weChatUser['login_count']:0;
                $weChatUserInfo = [
                    'open_id' => $userInfo['openid'],
                    'nickname' => $userInfo['nickname'],
                    'sex' => $userInfo['sex'],
                    'city' => $userInfo['city'],
                    'province' => $userInfo['province'],
                    'country' => $userInfo['country'],
                    'header_img_url' => $userInfo['headimgurl'],
                    'privilege' => $userInfo['privilege'],
                    'union_id' => isset($userInfo['union_id'])?$userInfo['union_id']:'',
                    'type' => 1,
                    'login_count' => $login_count+1,
                    'create_ip' => request()->ip(),
                    'update_ip' => request()->ip(),
                    'user_agent' => request()->header('user-agent')

                ];

                if($is_update){
                    $weChatUserInfo['id']=$weChatUser['id'];
                    unset($weChatUserInfo['create_ip']);
                }else{
                    unset($weChatUserInfo['update_ip']);

                }

                try {
                    $id = model('wx_info')->addEdit($weChatUserInfo, 'id', $is_update);
                } catch (\Exception $e) {
                    return ['status'=>false,'msg'=>$e->getMessage()];
                }
                if ($id) {
                    if(!$is_update){
                        $weChatUserInfo['id'] = $id;
                    }
                    session('WeChatUser', $weChatUserInfo);
                    return ['status'=>true];

                } else {
                    return ['status'=>false,'msg'=>'新增/更新授权用户错误'];
                }
            }
        }
    }


    /**
     * @Title: getSignPackage
     * @Description: todo()
     * @Author: liu tao
     * @Time: 2019/2/18 上午10:58
     * @return array
     */
    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string,
            "jsapiTicket" => $jsapiTicket
        );
        return $signPackage;
    }

    /**
     * @Title: sendQueue
     * @Description: todo(发送模板消息)
     * @Author: liu tao
     * @Time: 2019/2/18 上午10:54
     * @return \think\response\Json
     */
    public function sendQueue($template_id,$_data,$open_id,$_url=''){
        $ret =  $this->sendTemplate($_data, $open_id, $_url, $template_id);
        return $ret;
    }


    /**
     * @Title: sendTemplate
     * @Description: todo(模板发送)
     * @Author: liu tao
     * @Time: 2019/2/18 上午10:54
     * @param $_data
     * @param $openId
     * @param $_url
     * @param $template_id
     * @param $access_token
     * @return mixed
     */
    private function sendTemplate($_data, $openId, $_url, $template_id,$color = '#FF0000')
    {
        $access_token = $this->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
        $template_msg = array(
            'touser' => $openId,
            'url' => $_url,
            'template_id' => $template_id,
            'topcolor' => $color,
            'data' => $_data
        );
        $curl = curl_init($url);
        $header = array();
        $header[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($template_msg));
        $response = curl_exec($curl);
        \Log::record('消息模板response:'.json_encode($response));
        curl_close($curl);
        return $response;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket()
    {
        $accessToken = $this->getToken();
        // 如果是企业号用以下 URL 获取 ticket
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $res = json_decode(httpsRequest($url));
        $ticket = $res->ticket;
        return $ticket;
    }

    private function getToken()
    { //获取access_token
        // 如果是企业号用以下URL获取access_token
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appSecret;
        $res = json_decode(httpsRequest($url));
        \Log::record('access_token_response:'.json_encode($res));
        $access_token = $res->access_token;
        return $access_token;
    }


}