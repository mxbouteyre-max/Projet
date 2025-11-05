<?php  
function getBD(){ 
$bd = new PDO('mysql:host=localhost;dbname=orscp;charset=utf8', 'root', 'root');
$bd -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
return $bd; 
}
?> 