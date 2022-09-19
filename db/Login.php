<?php

    class Login extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        //パスワードがあっているか？
        public function is_match(string $user_name,string $pass)
        {

            $sql = 'select pass from accounts where user_name=:user_name and is_deleted=0; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_name',$user_name,PDO::PARAM_STR);
            $stmt->execute();

            $getPass = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($getPass){
                if(password_verify($pass,$getPass[0]['pass'])){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }

        }

        //ユーザーIDの取得
        public function getUserID(string $user_name)
        {
            
            $sql = 'select id from accounts where is_deleted=0 and user_name=:user_name; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_name',$user_name,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //管理者フラグ取得
        public function getAdminFlg(int $user_id)
        {
            $sql = 'select admin_flg from accounts where is_deleted=0 and id=:user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    }

?>