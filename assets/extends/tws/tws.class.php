<?php
    class TWS
    {


        static protected $flashKey = 'tws-flashdata'; // Key ufor flash data
        static protected $sessionKey = 'tws'; // key for userdata

        static function modx()
        {
            global $modx;
            $ref =& $modx;
            return $ref;

        }
        static function whoami()
        {
            $user = self::modx()->userLoggedIn();
            if($user['loggedIn'] && $user['id']){
                $tableA = self::modx()->getFullTableName('web_users');
                $tableB = self::modx()->getFullTableName('web_user_attributes');
                $tableC = self::modx()->getFullTableName('web_user_attributes_extended');


                $sql =  '   select a.username,b.*,c.*
                from '.$tableA .' a 
                LEFT JOIN ' . $tableB .' b ON a.`id`=b.`internalKey`  
                LEFT JOIN ' .$tableC .' c ON a.`id`=c.`internalKey` 
                WHERE a.`id` = '.$user['id'];
                return DBX::getRow($sql);
            } else {
                return false;
            }
        }

        static function pr($a, $print = 1)
        {
            $html='<pre class="prettyprint linenums" >';
            $html.=print_r($a, true);
            $html.='</pre>';

            if ($print)
            {
                echo $html;
            }
            else
            {
                return $html;
            }
        }

        static function flash($type, $data = '')
        {

            // if($type == 'formdata' && !is_array($data)){
            //     die('formdata must only be ste with array. data given was '.$data);
            // }
            if (is_array($data))
            {
                $_SESSION[self::$flashKey][$type]=$data;
            }
            else
            {
                $_SESSION[self::$flashKey][$type].=$data;
            }
        }

        static function getFlash($type = 'formdata', $key = false, $clear = false)
        {
            if (isSet($_SESSION[self::$flashKey][$type]))
            {
                if (!$key)
                {
                    $ret=$_SESSION[self::$flashKey][$type];

                    if ($clear)
                        $_SESSION[self::$flashKey][$type];

                    return $ret;
                }
                elseif (isSet($_SESSION[self::$flashKey][$type][$key]))
                {
                    return $_SESSION[self::$flashKey][$type][$key];
                }
            }
            else
            {
                return false;
            }
        }

        // called by twsunloader plugin on onWebPageComplete
        static function clearFlashData() { unset($_SESSION[self::$flashKey]); }
        static function clearFlashFormdata() {unset($_SESSION[self::$flashKey]['formdata']);}

        static function isIterable($e)
        {
            if (is_array($e) && count($e) > 0)
            {
                return count($e);
            }
            else
            {
                return false;
            }
        }

        static function redirect($url)
        {
            header('Location: '.$url);
            exit; 
        }
        static function requestVar($key,$method='post',$defaultValue=false){
            $method = strtolower($method);
            $allowedMethods = array('post','get','request','cookie');
            if(!in_array($method,$allowedMethods)){
                return false;
            }
            if($method == 'post'){
                return isSet($_POST[$key]) ? $_POST[$key] : $defaultValue;
            }
            if($method == 'get'){
                return isSet($_GET[$key]) ? $_GET[$key] : $defaultValue;
            }
            if($method == 'request'){
                return isSet($_REQUEST[$key]) ? $_REQUEST[$key] : $defaultValue;
            }
            if($method == 'cookie'){
                return isSet($_COOKIE[$key]) ? $_COOKIE[$key] : $defaultValue;
            }
        }

        // SESSION SET AND GET
        function sessionSet($key,$data=false){
            if(self::isIterable($key)){
                // key is a data array
                foreach ($key as $k=>$v){
                    self::sessionSet($k,$v);
                }
            } else {
                if($data){
                    $_SESSION[self::$sessionKey][$key]=$data;
                } else {
                    // unset if data is false
                   unset($_SESSION[self::$sessionKey][$key]); 
                }
                
            }
        }
        function sessionGet($key){
            return isSet($_SESSION[self::$sessionKey][$key]) ? $_SESSION[self::$sessionKey][$key] : false;
        }
        function sessionClear(){
            unset($_SESSION[self::$sessionKey]); 
        }
    } // end of class
?>