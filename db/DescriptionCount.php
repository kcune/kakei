<?php

    class DescriptionCount extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        //摘要別の月別の金額のクロス集計
        public function selectDescriptionCrossMonthTotal(string $year)
        {

            $sql = 'select ';
            $sql = $sql . 'description,';

            for ($i=1;$i<=12;$i++){
                $sql = $sql . 'sum(case when month(spent_date)='.$i;
                $sql = $sql . ' then credit else 0 end) as sub' . $i . ',';
            }

            $sql = $sql . 'sum(credit) as sum_credit ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'year(spent_date)=:year and ';
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 and ';
            $sql = $sql . "(description <> '') ";
            $sql = $sql . 'group by description ';
            $sql = $sql . 'order by sum(credit) desc,description asc;';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //摘要別の月別の回数別のクロス集計
        public function selectDescriptionCrossMonthCount(string $year)
        {

            $sql = 'select ';
            $sql = $sql . 'description,';

            for ($i=1;$i<=12;$i++){
                $sql = $sql . 'sum(case when month(spent_date)='.$i;
                $sql = $sql . ' then 1 else 0 end) as sub' . $i . ',';
            }

            $sql = $sql . 'count(credit) as count_credit ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'year(spent_date)=:year and ';
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 and ';
            $sql = $sql . "(description <> '') ";
            $sql = $sql . 'group by description ';
            $sql = $sql . 'order by count(credit) desc,description asc;';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //摘要別の月別の金額別のクロス集計（スマホ用）
        public function selectDescriptionCrossMonthTotalPhone(string $year,string $month)
        {

            $sql = 'select ';
            $sql = $sql . 'description,sum(credit) as credit_sum ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'year(spent_date)=:year and month(spent_date)=:month and ';
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 and ';
            $sql = $sql . "description <> '' ";
            $sql = $sql . 'group by description ';
            $sql = $sql . 'order by sum(credit) desc,description;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->bindValue(':month',$month,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //摘要別の月別の回数別のクロス集計（スマホ用）
        public function selectDescriptionCrossMonthCountPhone(string $year,string $month)
        {

            $sql = 'select ';
            $sql = $sql . 'description,count(credit) as credit_count ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'year(spent_date)=:year and month(spent_date)=:month and ';
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 and ';
            $sql = $sql . "description <> '' ";
            $sql = $sql . 'group by description ';
            $sql = $sql . 'order by count(credit) desc,description;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->bindValue(':month',$month,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }        
    }

?>