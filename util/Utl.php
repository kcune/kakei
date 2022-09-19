<?php

    //ユーティリティクラス
    class Util
    {
        //セッション開始
        public static function sessionStart()
        {
            //セッション開始
            session_start();
            session_regenerate_id();
        }

        //トークン取得
        public static function getToken(string $tokenName = 'token')
        {

            //トークンを取得する
            $token = bin2hex(openssl_random_pseudo_bytes(32));
            //セッションに格納する
            $_SESSION[$tokenName] = $token;
            //取得したトークンを返す
            return $token;

        }

        //不正なトークンか判断
        // public static function isToken(string $token,string $tokenName = 'token')
        public static function isToken(string $token)
        {

            //tokeName
            $tokenName = 'token';

            //トークンがないか不一致を検出
            if(!isset($_SESSION[$tokenName])||$_SESSION[$tokenName] != $token){
                //不正なトークンを検出
                return false;
            }else{
                //正常なトークン
                return true;
            }

        }

        //入力したものをサニタイズ
        public static function sani(array $obj)
        {

            //配列の１次だけをサニタイズする
            foreach($obj as $k=>$val){
                $obj[$k] = htmlspecialchars($val);
            }

            //サニタイズしたものをもどす
            return $obj;

        }

        //ログイン画面から入ってきているか？
        public static function isLogin()
        {

            if($_SESSION['login'] == 'login'){
                return true;
            }else{
                return false;
            }
        }

    }

?>