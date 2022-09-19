<?php

    class Subject extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        //勘定科目表示
        public function selectSubjectAll(int $user_id)
        {
            $sql = 'select * from subjects where is_deleted=0 and user_id=:user_id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //勘定科目追加
        public function insertSubject(int $user_id,string $account,int $calc_flg)
        {
            $sql = 'insert into subjects(user_id,account,calc_flg) values(:user_id,:account,:calc_flg); ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':account',$account,PDO::PARAM_STR);
            $stmt->bindValue(':calc_flg',$calc_flg,PDO::PARAM_INT);
            $stmt->execute();

        }

        //勘定科目更新
        public function updateSubject(int $id,string $account,int $calc_flg)
        {
            $sql = 'update subjects set account=:account,calc_flg=:calc_flg where id=:id ; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->bindValue(':account',$account,PDO::PARAM_STR);
            $stmt->bindValue(':calc_flg',$calc_flg,PDO::PARAM_INT);
            $stmt->execute();

        }

        //勘定科目削除
        public function deleteSubject(int $id)
        {
            $sql = 'delete from subjects where id=:id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->execute();

        }


    }

?>