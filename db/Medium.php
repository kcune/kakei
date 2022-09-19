<?php

    class Medium extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function selectMediumAll(int $user_id)
        {
            $sql = 'select * from medium where is_deleted=0 and user_id=:user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        public function insertMedium(int $user_id,string $medium)
        {
            //最初の入力かどうか？
            if ($this->is_first_medium($user_id)){

                if ($this->is_first_subjects($user_id)){
                 
                    //勘定科目に前月繰越を追加
                    $sql = "insert into subjects(user_id,account,calc_flg) values(:user_id,'前月繰越',1); ";
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
                    $stmt->execute();

                }
            }            
            
            //口座を追加
            $sql = 'insert into medium(user_id,medium) values(:user_id,:medium); ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':medium',$medium,PDO::PARAM_STR);
            $stmt->execute();

        }

        function is_first_medium(int $user_id)
        {
            $sql = 'select * from medium where is_deleted=0 and user_id=:user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (is_null($rec[0]['id'])){
                return true;
            }else {
                return false;
            }

        }

        function is_first_subjects(int $user_id)
        {
            $sql = "select * from subjects where is_deleted=0 and user_id=:user_id and account='前月繰越'; ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (is_null($rec[0]['id'])){
                return true;
            }else {
                return false;
            }

        }

        public function updateMedium(int $id,string $medium)
        {
            $sql = 'update medium set medium=:medium where id=:id ; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->bindValue(':medium',$medium,PDO::PARAM_STR);
            $stmt->execute();

        }

        public function deleteMedium(int $id)
        {
            $sql = 'update medium set is_deleted=1 where id=:id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->execute();

        }

        public function minMedium(int $user_id)
        {
            $sql = 'select min(id) as min_id from medium where is_deleted=0 and user_id=:user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }


    }

?>