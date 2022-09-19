<?php

    require_once('../const/Const.php');

    class Db
    {

        protected $dbh;

        public function __construct()
        {
            //データベース接続
            $dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset=utf8';
            $this->dbh = new PDO($dsn,DB_USER,DB_PASS);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        }

    }

?>