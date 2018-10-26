<?php
include "simple_html_dom.php";

$conn = new mysqli('localhost','root','server','student');

if($conn){
    echo "";
}
else{
    die("Connection Failed because ".$conn->connect_error);
}



//echo "Included";


$html = str_get_html(file_get_contents('https://cse.gndec.ac.in/sites/default/files/TT_17jul2018_data_and_timetable_data_and_timetable_teachers_days_horizontal_0.html'));

//echo "url fetched";

//***********Getting table NAmes****************

$tableName=array();
$name = "";
foreach($html->find('ul li') as $title){
    $name = $title->plaintext;
    
    $name = str_replace('-','_',$name);
    $name = str_replace('.','_',$name);
    $name = str_replace('/','_',$name);
    $name = str_replace('(','_',$name);
    $name = str_replace(')','_',$name);
    $name = str_replace(' ','_',$name);
    $name = trim($name,'_');
    
    
    array_push($tableName,$name);
//    echo $name .'<br>';
  
}

//********Table creation********


//foreach($tableName as $t){
//    $query = "CREATE TABLE $t (time varchar(5),monday varchar(30),tuesday varchar(30),wednesday varchar(30),thursday varchar(30),friday varchar(30)); ";
//    if($conn->query($query)){
//        echo "Table $t created <br>";
//    }
//    else{
//        echo "kuch gatlat hua<br>";
//    }
//
//
//}




$countTable=0; //indexing for tableName
//*****Getting table data*******


for($k=1; $k<=36; $k++){ //loops for all tables
    
$rows = array();
$data = "";

foreach($html->find('table#table_'.$k.' tbody tr td') as $row){
    
    // ******Getting table data**********
    $data = $row->plaintext;
    if($row->rowspan){
        $data = $data . '*';
    }
    array_push($rows,$data); 

}

array_shift($rows); //1st blank Spnace deleted.
    //**Two last elements deleted of array $rows*****
array_pop($rows); 
array_pop($rows);
    
    
//    ****Rowspan fixed*******
    
 for($i=0; $i<sizeof($rows); $i++){
    
    if(strpos($rows[$i],"*")){
        $rows[$i] = str_replace("*","",$rows[$i]);
        array_splice($rows,$i+5,0,$rows[$i]);
    }
}
    
    

  //  ******1d to 2 D conversion****
$datas = array();
$row = 0;
$col = 0;
for($i=0; $i<sizeof($rows); $i++){
    if($i%5==0 && $i>0){
        $row++;
    }
    $datas[$row][$col] = $rows[$i];
    $col++;
    if($col==5){
        $col=0;
    }
}
    
    $time = array('8','9','10','11','12','1','2','3','4','5','6');
    
   // ******Inserting in databse*******
    for($j=0; $j<11; $j++){
    
        $v1 = $datas[$j][0];
        $v2 = $datas[$j][1];
        $v3 = $datas[$j][2];
        $v4 = $datas[$j][3];
        $v5 = $datas[$j][4];
        $sql = "insert into $tableName[$countTable] values('$time[$j]','$v1','$v2','$v3','$v4','$v5')";
        
        if($conn->query($sql)){
            echo "Row inserted";
        }
        else{
            echo "something Error.";
        }
    
    
    }
    $countTable++;
}














$conn->close();





?>