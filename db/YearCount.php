<?php

    class YearCount extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function selectCreditCrossYear()
        {

            //今年
            $now_year = date('Y');

            //基準年
            $year = date('Y', strtotime('-4 year'));

            $sql = 'select ';
            $sql = $sql . 'subjects.account as ac,subjects.id as id,';

            for ($i=$year;$i<=$now_year;$i++){
                $sql = $sql . 'sum(case when year(spent_date)='.$i;
                $sql = $sql . ' then credit else 0 end) as sub' . $i . ',';
            }

            $sql = substr($sql,0,strlen($sql)-1).' ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 ';
            $sql = $sql . 'group by subjects.account,subjects.id ';
            $sql = $sql . 'order by subjects.id;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        public function selectCreditCrossYearPhone(string $year)
        {

            $sql = 'select ';
            $sql = $sql . 'subjects.account as ac,sum(credit) as credit_sum ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'year(spent_date)=:year and ';
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 ';
            $sql = $sql . 'group by subjects.account ';
            $sql = $sql . 'order by subjects.id;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

    }

?>