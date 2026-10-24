<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
if($_POST['submit']=="Update")
{
	$pagetype=$_GET['type'];
	$pagedetails=$_POST['pgedetails'];
$sql = "UPDATE tblpages SET detail=:pagedetails WHERE type=:pagetype";
$query = $dbh->prepare($sql);
$query -> bindParam(':pagetype',$pagetype, PDO::PARAM_STR);
$query-> bindParam(':pagedetails',$pagedetails, PDO::PARAM_STR);
$query -> execute();
$msg="Página atualizada com sucesso";

}

?>

<!doctype html>
<html lang="pt" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="Gerir Páginas - Painel Admin">
	<meta name="theme-color" content="#3e454c">
	
	<title>Portal de Aluguer de Carros | Gerir Páginas</title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
	<script type="text/JavaScript">
function MM_jumpMenu(targ,selObj,restore){
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>
<script type="text/javascript" src="nicEdit.js"></script>
<script type="text/javascript">
	bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>
  <style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>

</head>

<body>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">
					
						<h2 class="page-title">Gerir Páginas Institucionais</h2>

						<div class="row">
							<div class="col-md-10">
								<div class="panel panel-default">
									<div class="panel-heading">Editar Conteúdo de Páginas</div>
									<div class="panel-body">
										<form method="post" name="chngpwd" class="form-horizontal">
										
  	        	  <?php if($error){?><div class="errorWrap"><strong>ERRO</strong>: <?php echo htmlentities($error); ?> </div><?php }
				else if($msg){?><div class="succWrap"><strong>SUCESSO</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>
											<div class="form-group">
												<label class="col-sm-4 control-label">Selecionar Página</label>
												<div class="col-sm-8">
													<select name="menu1" onChange="MM_jumpMenu('parent',this,0)" class="form-control">
														<option value="" selected="selected">*** Selecione uma Página ***</option>
														<option value="manage-pages.php?type=terms">Termos e Condições</option>
														<option value="manage-pages.php?type=privacy">Política de Privacidade</option>
														<option value="manage-pages.php?type=aboutus">Sobre Nós</option>
														<option value="manage-pages.php?type=faqs">Perguntas Frequentes (FAQs)</option>
													</select>
												</div>
											</div>
											<div class="hr-dashed"></div>
											
											<div class="form-group">
												<label class="col-sm-4 control-label">Página Selecionada</label>
												<div class="col-sm-8">
						<?php
			switch($_GET['type'])
			{
				case "terms" :
					echo "Termos e Condições";
					break;
				case "privacy" :
					echo "Política de Privacidade";
					break;
				case "aboutus" :
					echo "Sobre Nós";
					break;
				case "faqs" :
					echo "Perguntas Frequentes (FAQs)";
					break;
				default :
					echo "Nenhuma";
					break;
			}
			?>
												</div>
											</div>
								
									<div class="form-group">
												<label class="col-sm-4 control-label">Detalhes da Página</label>
												<div class="col-sm-8">
			<textarea class="form-control" rows="8" cols="50" name="pgedetails" id="pgedetails" required>
										<?php 
$pagetype=$_GET['type'];
$sql = "SELECT detail from tblpages where type=:pagetype";
$query = $dbh -> prepare($sql);
$query->bindParam(':pagetype',$pagetype,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
{		
echo htmlentities($result->detail);
}}
?>
										</textarea>
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-sm-8 col-sm-offset-4">
													<button type="submit" name="submit" value="Update" id="submit" class="btn-primary btn">Guardar Alterações</button>
												</div>
											</div>

										</form>

									</div>
								</div>
							</div>
							
						</div>

					</div>
				</div>
				
			</div>
		</div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>

</body>

</html>
<?php } ?>
