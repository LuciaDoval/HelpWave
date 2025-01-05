<?php
define('ERR_NO_IDENTIF',3); 

session_start(); 

if(!isset($_SESSION['helpwave_db'])){
   	header('Location: login.php?error='.ERR_NO_IDENTIF); 
}
?>