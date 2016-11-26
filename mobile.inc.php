<?php
/**
*  类名: mobile
*  描述: 手机信息类
*/
class mobile
{
    /**
    *   函数名称:   getPhoneNumber
    *   函数功能:   取手机号
    *   输入参数:   none
    *   函数返回值: 成功返回号码，失败返回false
    *   其它说明:   说明
    */
    function getPhoneNumber()
    {
        if (isset($_SERVER['HTTP_X_NETWORK_INFO']))
        {
            $str1 = $_SERVER['HTTP_X_NETWORK_INFO'];
            $getstr1 = preg_replace('/(.*,)(\d+)(,.*)/i','\2',$str1);
            Return $getstr1;
			//Return $str1;
        }
        elseif (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID']))
        {
            $getstr2 = $_SERVER['HTTP_X_UP_CALLING_LINE_ID']; 
            Return $getstr2;
        }
        elseif (isset($_SERVER['HTTP_X_UP_SUBNO']))
        {
            $str3 = $_SERVER['HTTP_X_UP_SUBNO'];
            $getstr3 = preg_replace('/(.*)(\d+)(.*)/i','\2',$str3);
            Return $getstr3;
        }
        elseif (isset($_SERVER['DEVICEID']))
        {
			$str4 = $_SERVER['DEVICEID'];
            //$getstr4 = preg_replace('/(.*)(\d)(.*)/i','\2',$str4);
			Return $str4;
        }
        else
        {
            Return false;
        }
    }

    /**
    *   函数名称:   getHttpHeader
    *   函数功能:   取头信息
    *   输入参数:   none
    *   函数返回值: 成功返回号码，失败返回false
    *   其它说明:   说明
    */
    function getHttpHeader()
    {
        $str = '';
        foreach ($_SERVER as $key=>$val)
        {
            $gstr = str_replace("&","&",$val);
            $str.= "$key -> ".$gstr."\r\n";
        }
        Return $str;
    }

	/**
    *   函数名称:   getALL
    *   函数功能:   取HTTP所有信息
    *   输入参数:   none
    *   函数返回值: 成功返回信息，失败返回空
    *   其它说明:   说明
    */
    function getALL()
    {
        if (isset($_SERVER['ALL_HTTP']))
        {
            Return $_SERVER['ALL_HTTP'];
        }
        else
        {
            Return "";
        }
    }
	
    /**
    *   函数名称:   getUA
    *   函数功能:   取UA
    *   输入参数:   none
    *   函数返回值: 成功返回号码，失败返回false
    *   其它说明:   说明
    */
    function getUA()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            Return $_SERVER['HTTP_USER_AGENT'];
        }
        else
        {
            Return false;
        }
    }

    /**
    *   函数名称:   getPhoneType
    *   函数功能:   取得手机类型
    *   输入参数:   none
    *   函数返回值: 成功返回string，失败返回false
    *   其它说明:   说明
    */
    function getPhoneType()
    {
        $ua = $this->getUA();
        if($ua!=false)
        {
            $str = explode(' ',$ua);
            Return $str[0];
        }
        else
        {
            Return false;
        }
    }

	/**
    *   函数名称:   isQQ
    *   函数功能:   判断是否是QQ
    *   输入参数:   none
    *   函数返回值: 成功返回string，失败返回false
    *   其它说明:   说明
    */
    function isQQ()
    {
        $uainfo = $this->getALL();
        if ($uainfo!=''&&preg_match('/.*Q_UA.*/i',$uainfo))
        {
            Return true;
        }
        else
        {
            Return false;
        }
    }

    /**
    *   函数名称:   isOpera
    *   函数功能:   判断是否是opera
    *   输入参数:   none
    *   函数返回值: 成功返回string，失败返回false
    *   其它说明:   说明
    */
    function isOpera()
    {
        $uainfo = $this->getUA();
        if (preg_match('/.*Opera.*/i',$uainfo))
        {
            Return true;
        }
        else
        {
            Return false;
        }
    }

    /**
    *   函数名称:   isM3gate
    *   函数功能:   判断是否是m3gate
    *   输入参数:   none
    *   函数返回值: 成功返回string，失败返回false
    *   其它说明:   说明
    */
    function isM3gate()
    {
        $uainfo = $this->getUA();
        if (preg_match('/M3Gate/i',$uainfo))
        {
            Return true;
        }
        else
        {
            Return false;
        }
    }

    /**
    *   函数名称:   getHttpAccept
    *   函数功能:   取得HA
    *   输入参数:   none
    *   函数返回值: 成功返回string，失败返回false
    *   其它说明:   说明
    */
    function getHttpAccept()
    {
        if (isset($_SERVER['HTTP_ACCEPT']))
        {
            Return $_SERVER['HTTP_ACCEPT'];
        }
        else
        {
            Return false;
        }
    }

    /**
    *   函数名称:   getIP
    *   函数功能:   取得手机IP
    *   输入参数:   none
    *   函数返回值: 成功返回string
    *   其它说明:   说明
    */
    function getIP()
    {
        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
		   $ip = $_SERVER["HTTP_CLIENT_IP"];
		else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		   $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if(!empty($_SERVER["REMOTE_ADDR"]))
		   $ip = $_SERVER["REMOTE_ADDR"];
		else
		   $ip = "";

        return $ip;
    }
}
?>
