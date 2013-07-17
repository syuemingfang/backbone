<?php
$mysqlHost='127.0.0.1';
$mysqlUser='root';
$mysqlPass='';
$mysqlDB='school';
if(isset($_GET['zone'])){
	$conID=mysql_pconnect($mysqlHost, $mysqlUser, $mysqlPass)or die('Error: '.mysql_error().'<br />');
	@mysql_select_db($mysqlDB, $conID);
	if($_GET['zone'] == 'createDatabase'){
		@mysql_query('Set names=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set character_set_client=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set character_set_results=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set character_set_connection=utf8')or die('Error: '.mysql_error().'<br />');
		@mysql_query('Set collation_connection=utf8_general_ci')or die('Error: '.mysql_error().'<br />');
		echo '<h2>Database</h2>';
		@mysql_query('Create  Database '.$_POST['mysqlDB'], $conID)or die('Error: '.mysql_error().'<br />');
		echo 'Okay: '.$_POST['mysqlDB'].'<br />';
		echo '<h2>Table</h2>';
		$query='Create Table student(id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), nickname VARCHAR(255), age INT(11))';
		$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
		echo 'Okay: student<br />';
		echo '<h2>Write</h2>';
		$data=array(
			array('id'=>'1', 'nickname'=>'Tom', 'age'=>'20'),
			array('id'=>'2', 'nickname'=>'Jack', 'age'=>'18'),
			array('id'=>'3', 'nickname'=>'Sam', 'age'=>'25')
		);
		for($i=0; $i < count($data); $i++){
			$query='Insert Into student(id, nickname, age) Values ('.$data[$i]['id'].', "'.$data[$i]['nickname'].'", '.$data[$i]['age'].');';
			$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
			echo 'Okay: '.$query.'<br />';
		}
		echo 'MySQL quite ready now.<br />';
	} else if($_GET['zone'] == 'view'){
		$result=mysql_query("Select * From student", $conID)or die('Error: '.mysql_error().'<br />');
		while(list($id, $nickname, $age)=@mysql_fetch_row($result)){
			echo $nickname.'<br />';
		}
	} else if($_GET['zone'] == 'read'){
		if(isset($_GET['id'])){
			$result=mysql_query("Select * From student Where id=".$_GET['id'], $conID)or die('Error: '.mysql_error().'<br />');			
		}
		else{
			$result=mysql_query("Select * From student", $conID)or die('Error: '.mysql_error().'<br />');
		}
		while($row=@mysql_fetch_assoc($result)){
			$rows[]=$row;
		}
		header('Content-Type: application/json; charset=utf-8'); 
		echo json_encode($rows);
	} else if($_GET['zone'] == 'create'){
		$data=json_decode(file_get_contents('php://input'));
		$query="Select Max(id) as newId From student";
		$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
		$rows=@mysql_fetch_row($result);
		$newId=$rows[0]+1;
		$query='Insert Into student(id, nickname, age) Values ('.$newId.', "'.$data->{'nickname'}.'", '.$data->{'age'}.');';
		$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
	} else if($_GET['zone'] == 'update'){
		$data=json_decode(file_get_contents('php://input'));
		$query='Update student SET nickname="'.$data->{'nickname'}.'", age="'.$data->{'age'}.'" Where id='.$data->{'id'};
		$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
	} else if($_GET['zone'] == 'delete'){
		$data=json_decode(file_get_contents('php://input'));
		$query='Delete From student Where id='.$data->{'id'};
		$result=@mysql_query($query, $conID)or die('Error: '.$query.'<br />');
	}
} else{
	header('Content-Type:text/html; charset=utf-8');
	echo'
	<ol>
		<li><a href="mysql.php?zone=createDatabase">Create Database</a></li>
		<li><a href="mysql.php?zone=view">View Data</a></li></li>
		<li><a href="mysql.php?zone=json">View JSON</a></li>
	</ol>
	';
}
?>