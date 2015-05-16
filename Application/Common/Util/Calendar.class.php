<?php 
//日历类
namespace Common\Util;
class Calendar
{
    private $year;
    private $month;
    private $day_week;
    private $url;
    private $stu_id;
    
    function __construct(){
        $this->year=isset($_GET['year']) ? $_GET['year'] : date("Y");
        $this->month=isset($_GET['month']) ? $_GET['month'] : date("m");
        $this->day_week=date("w", mktime(0, 0, 0, $this->month, 1, $this->year));
        $this->stu_id = isset($_GET['stuid']) ? $_GET['stuid']:0;
        $this->url = '/index.php?m=Home&c=Schedule&a=index';
    }
     
    private function xianDate(){
        echo "<tr>";
        echo "<td class='td1'><a href='".$this->nextmonth($this->month, $this->year,$this->url)."'>"."<<"."</td>";
        echo "<td colspan='5' class='td1'>".$this->year."年".$this->month."月</td>";
        echo "<td class='td1'><a href='".$this->aftermonth($this->month, $this->year,$this->url)."'>".">>"."</td>";
        echo "</tr>";
    }
    
    
    private function weeks(){
        $weeks=array("日", "一", "二", "三", "四", "五", "六");
        echo "<tr >";
        foreach($weeks as $value){
            echo "<th class='th1'>".$value."</th>";
        }
        echo "</tr>";
    }
    
    private function days(){
        echo "<tr>";
        for($i=0; $i<$this->day_week; $i++){
            echo "<td class='td1'>&nbsp;</td>";
        }
        for($j=1; $j <= date("t", mktime(0, 0, 0, $this->month, 1, $this->year)); $j++){
            $i++;
            if($j == date("d")){
                echo "<td class='td1 fontb'>".$this->day($this->year, $this->month, $j, $this->url)."</td>";
            }else{
                echo "<td class='td1'>".$this->day($this->year, $this->month, $j, $this->url)."</td>";                
            }
            if($i%7 == 0){
                echo "</tr>";
            }
        }
        while($i%7 != 0){
            echo "<td class='td1'>&nbsp;</td>";
            $i++;
        }
    }
    
    private function nextyear($year, $month){
        if($year == 1970){
            $year=1970;
        }else{
            $year--;
        }
        return "&year=".$year."&month=".$month;
    }
    
    private function afteryear($year, $month){
        if($year == 2038){
            $year=2038;
        }else{
            $year++;
        }
        return "&year=".$year."&month=".$month;
    }
    
    private function nextmonth($month, $year,$url){
       
        if($month == 01){
            $year--;
            $month1=12;
        }else{
            $month--;
            if($month<10)
            {
                $month1 = '0'.$month;
            }
            else 
            {
                $month1 = $month;
            }
        }
        
        return $url."&year=".$year."&month=".$month1;
    }
    
    private function aftermonth($month, $year,$url){
        if($month == 12){
            $year++;
            $month1 = '01';
        }else{
            $month++;
            if($month<10)
            {
                $month1 = '0'.$month;
            }else{
                $month1 = $month;
            }
            
        }
        
        return $url."&year=".$year."&month=".$month1;
    }
    
    private function day($year,$month,$day,$url)
    {
        $html ='';
        $date = $year."/".$month."/".$day;
        $where = array('date_value'=>$date,'member_id'=>session('member_id'));
        if($this->stu_id>0)
        {
            $where['stu_id'] = $this->stu_id;
        }
        $count=D('Schedule')->get_count_num($where);
        if($count!=0)
        {
            $url1= $url.'&date='.$date;
            $html="<a href='".$url1."' style='text-decoration:underline;font-weight:bold;'>".$day."</a>";
        }
        else 
        {
            $html=$day;
        }
        
        return $html;
    }
    
    
    public function out(){
        echo "<table style='width:100%'>";
        $this->xianDate();
        $this->weeks();
        $this->days();
        echo "</table>";
    } 
}



?>