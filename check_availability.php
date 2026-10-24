<?php 
require_once("includes/config.php");
// code user email availablity
if(!empty($_POST["emailid"])) {
	$email= $_POST["emailid"];
	if (filter_var($email, FILTER_VALIDATE_EMAIL)===false) {
		echo "Erro: Por favor insira um e-mail válido.";
	}
	else {
		$sql ="SELECT EmailId FROM tblusers WHERE EmailId=:email";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
echo "<span style='color:red'> Este e-mail já está registado.</span>";
 echo "<script>$('#submit').prop('disabled',true);</script>";
} else{
	echo "<span style='color:green'> E-mail disponível para registo.</span>";
 echo "<script>$('#submit').prop('disabled',false);</script>";
}
}
}
?>
