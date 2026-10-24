<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{

if(isset($_REQUEST['del']))
	{
$delid=intval($_GET['del']);
$sql = "delete from tblvehicles WHERE id=:delid";
$query = $dbh->prepare($sql);
$query -> bindParam(':delid',$delid, PDO::PARAM_STR);
$query -> execute();
$msg="Veículo removido com sucesso";
}

 ?>

<!doctype html>
<html lang="pt" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="Gestão de Veículos - Painel Admin">
	<meta name="theme-color" content="#3e454c">
	
	<title>Portal de Aluguer de Carros | Gerir Veículos</title>

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

						<h2 class="page-title">Gerir Veículos</h2>

						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Lista de Veículos Cadastrados</div>
							<div class="panel-body">
							<?php if($error){?><div class="errorWrap"><strong>ERRO</strong>: <?php echo htmlentities($error); ?> </div><?php }
				else if($msg){?><div class="succWrap"><strong>SUCESSO</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Modelo</th>
											<th>Marca</th>
											<th>Matrícula</th>
											<th>Categoria</th>
											<th>Transmissão</th>
											<th>Combustível</th>
											<th>Preço/Dia</th>
											<th>Disponibilidade</th>
											<th>Ações</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>#</th>
											<th>Modelo</th>
											<th>Marca</th>
											<th>Matrícula</th>
											<th>Categoria</th>
											<th>Transmissão</th>
											<th>Combustível</th>
											<th>Preço/Dia</th>
											<th>Disponibilidade</th>
											<th>Ações</th>
										</tr>
									</tfoot>
									<tbody>

<?php $sql = "SELECT tblvehicles.*,tblbrands.BrandName from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{				?>	
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><strong><?php echo htmlentities($result->VehiclesTitle);?></strong><?php if($result->SeriesVersion) { echo " <small class='text-muted'>(".htmlentities($result->SeriesVersion).")</small>"; } ?></td>
											<td><?php echo htmlentities($result->BrandName);?></td>
											<td><code><?php echo htmlentities($result->LicensePlate ? $result->LicensePlate : 'N/D');?></code></td>
											<td><?php echo htmlentities($result->Category ? $result->Category : 'Geral');?></td>
											<td><?php echo htmlentities($result->Transmission ? $result->Transmission : 'Manual');?></td>
											<td><?php echo htmlentities($result->FuelType);?></td>
											<td><strong><?php echo htmlentities($result->PricePerDay);?> MT</strong></td>
											<td>
												<?php if($result->StatusAvailability == 'Não Disponível') { ?>
													<span class="label label-danger">Indisponível</span>
												<?php } else { ?>
													<span class="label label-success">Disponível</span>
												<?php } ?>
											</td>
											<td>
												<a href="edit-vehicle.php?id=<?php echo $result->id;?>" title="Editar" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Editar</a>&nbsp;
												<a href="manage-vehicles.php?del=<?php echo $result->id;?>" onclick="return confirm('Deseja realmente eliminar este veículo?');" title="Eliminar" class="btn btn-xs btn-danger"><i class="fa fa-close"></i></a>
											</td>
										</tr>
										<?php $cnt=$cnt+1; }} ?>
										
									</tbody>
								</table>

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
