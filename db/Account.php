<?php

    class Account extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        //アカウント追加
        public function insertAccount(string $user_name,string $pass,int $admin_flg)
        {
            
            $pass = password_hash($pass,PASSWORD_DEFAULT);

            $sql = 'insert into accounts(user_name,pass,admin_flg) values(:user_name,:pass,:admin_flg); ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_name',$user_name,PDO::PARAM_STR);
            $stmt->bindValue(':pass',$pass,PDO::PARAM_STR);
            $stmt->bindValue(':admin_flg',$admin_flg,PDO::PARAM_INT);
            $stmt->execute();

        }

        //アカウントが存在しているか確認
        public function is_exist_account(string $user_name)
        {

            $sql = 'select * from accounts where is_deleted=0 and user_name=:user_name; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_name',$user_name,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //アカウント情報の取得
        public function selectAccount(int $user_id)
        {

            $sql = 'select * from accounts where is_deleted=0 and id=:id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //退会処理
        public function deleteAllTables(int $user_id)
        {
            //家計簿テーブルから削除
            $sql = 'update money set is_deleted=1 where user_id=:user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            //勘定科目テーブルから削除
            $sql = 'update subjects set is_deleted=1 where user_id=:user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            //口座テーブルから削除
            $sql = 'update medium set is_deleted=1 where user_id=:user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            //アカウントテーブルから削除
            $sql = 'update accounts set is_deleted=1 where id=:user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

        }

        public function admin_count()
        {
            $sql = 'select count(*) as cnt from accounts where is_deleted=0 and admin_flg=1; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function is_admin(int $id)
        {
            $sql = 'select admin_flg from accounts where is_deleted=0 and id=:id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result[0]['admin_flg']==1){
                return true;
            }else{
                return false;
            }

        }

    }

?>