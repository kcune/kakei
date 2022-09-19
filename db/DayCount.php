<?php

    class DayCount extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        //科目の上位10個の日ごとの合計
        public function selectAccountAll(string $year,string $month)
        {

            //科目を合計金額の上位順の表をつくる
            $sql = 'select subjects.account,subjects.id as id,sum(credit) as sum from money inner join subjects on ';
            $sql = $sql . 'money.account = subjects.id ';
            $sql = $sql . 'where calc_flg=0 and year(spent_date)=:year and month(spent_date)=:month ';
            $sql = $sql . 'and money.user_id=:user_id and subjects.user_id=:user_id and money.is_deleted=0 and subjects.is_deleted=0 ';
            $sql = $sql . 'group by subjects.account,subjects.id ';
            $sql = $sql . 'order by sum(credit) desc limit 10;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->bindValue(':month',$month,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //貸方の日ごとのクロス集計
        public function selectCreditCross(string $year,string $month,array $rec)
        {

            $cnt = 1;
            $sql = 'select ';
            $sql = $sql . 'day(spent_date) as date,sum(credit) as credit_sum,';

            foreach ($rec as $val){
                $sql = $sql . 'sum(case when subjects.id='.$val['id'];
                $sql = $sql . ' then credit else 0 end) as sub' . $cnt . ',';
                $cnt += 1;
            }

            $sql = substr($sql,0,strlen($sql)-1).' ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'year(spent_date)=:year and month(spent_date)=:month and ';
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 ';
            $sql = $sql . 'group by day(spent_date) ';
            $sql = $sql . 'order by day(spent_date);';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->bindValue(':month',$month,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //貸方の日ごとのクロス集計（スマホ用）
        public function selectCreditCrossPhone(string $year,string $month)
        {

            $sql = 'select ';
            $sql = $sql . 'day(spent_date) as date,sum(credit) as credit_sum,';
            $sql = $sql . 'subjects.account as ac ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'year(spent_date)=:year and month(spent_date)=:month and ';
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 ';
            $sql = $sql . 'group by day(spent_date),subjects.account ';
            $sql = $sql . 'order by day(spent_date),money.account;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->bindValue(':month',$month,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
      }

?>