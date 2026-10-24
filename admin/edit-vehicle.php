<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{ 

// Garantir colunas na base de dados
try {
  $dbh->exec("ALTER TABLE tblvehicles ADD COLUMN LicensePlate varchar(50) DEFAULT NULL");
  $dbh->exec("ALTER TABLE tblvehicles ADD COLUMN Transmission varchar(50) DEFAULT NULL");
  $dbh->exec("ALTER TABLE tblvehicles ADD COLUMN SeriesVersion varchar(100) DEFAULT NULL");
  $dbh->exec("ALTER TABLE tblvehicles ADD COLUMN Category varchar(100) DEFAULT NULL");
  $dbh->exec("ALTER TABLE tblvehicles ADD COLUMN StatusAvailability varchar(50) DEFAULT 'Disponível'");
} catch(PDOException $e) {}

if(isset($_POST['submit']))
  {
$vehicletitle=$_POST['vehicletitle'];
$brand=$_POST['brandname'];
$vehicleoverview=$_POST['vehicalorcview'];
$priceperday=$_POST['priceperday'];
$fueltype=$_POST['fueltype'];
$modelyear=$_POST['modelyear'];
$seatingcapacity=$_POST['seatingcapacity'];
$licenseplate=$_POST['licenseplate'];
$seriesversion=$_POST['seriesversion'];
$category=$_POST['category'];
$transmission=$_POST['transmission'];
$statusavailability=$_POST['statusavailability'];

$airconditioner=$_POST['airconditioner'];
$powerdoorlocks=$_POST['powerdoorlocks'];
$antilockbrakingsys=$_POST['antilockbrakingsys'];
$brakeassist=$_POST['brakeassist'];
$powersteering=$_POST['powersteering'];
$driverairbag=$_POST['driverairbag'];
$passengerairbag=$_POST['passengerairbag'];
$powerwindow=$_POST['powerwindow'];
$cdplayer=$_POST['cdplayer'];
$centrallocking=$_POST['centrallocking'];
$crashcensor=$_POST['crashcensor'];
$leatherseats=$_POST['leatherseats'];
$id=intval($_GET['id']);

$sql="UPDATE tblvehicles SET VehiclesTitle=:vehicletitle,VehiclesBrand=:brand,VehiclesOverview=:vehicleoverview,PricePerDay=:priceperday,FuelType=:fueltype,ModelYear=:modelyear,SeatingCapacity=:seatingcapacity,LicensePlate=:licenseplate,SeriesVersion=:seriesversion,Category=:category,Transmission=:transmission,StatusAvailability=:statusavailability,AirConditioner=:airconditioner,PowerDoorLocks=:powerdoorlocks,AntiLockBrakingSystem=:antilockbrakingsys,BrakeAssist=:brakeassist,PowerSteering=:powersteering,DriverAirbag=:driverairbag,PassengerAirbag=:passengerairbag,PowerWindows=:powerwindow,CDPlayer=:cdplayer,CentralLocking=:centrallocking,CrashSensor=:crashcensor,LeatherSeats=:leatherseats WHERE id=:id";
$query = $dbh->prepare($sql);
$query->bindParam(':vehicletitle',$vehicletitle,PDO::PARAM_STR);
$query->bindParam(':brand',$brand,PDO::PARAM_STR);
$query->bindParam(':vehicleoverview',$vehicleoverview,PDO::PARAM_STR);
$query->bindParam(':priceperday',$priceperday,PDO::PARAM_STR);
$query->bindParam(':fueltype',$fueltype,PDO::PARAM_STR);
$query->bindParam(':modelyear',$modelyear,PDO::PARAM_STR);
$query->bindParam(':seatingcapacity',$seatingcapacity,PDO::PARAM_STR);
$query->bindParam(':licenseplate',$licenseplate,PDO::PARAM_STR);
$query->bindParam(':seriesversion',$seriesversion,PDO::PARAM_STR);
$query->bindParam(':category',$category,PDO::PARAM_STR);
$query->bindParam(':transmission',$transmission,PDO::PARAM_STR);
$query->bindParam(':statusavailability',$statusavailability,PDO::PARAM_STR);

$query->bindParam(':airconditioner',$airconditioner,PDO::PARAM_STR);
$query->bindParam(':powerdoorlocks',$powerdoorlocks,PDO::PARAM_STR);
$query->bindParam(':antilockbrakingsys',$antilockbrakingsys,PDO::PARAM_STR);
$query->bindParam(':brakeassist',$brakeassist,PDO::PARAM_STR);
$query->bindParam(':powersteering',$powersteering,PDO::PARAM_STR);
$query->bindParam(':driverairbag',$driverairbag,PDO::PARAM_STR);
$query->bindParam(':passengerairbag',$passengerairbag,PDO::PARAM_STR);
$query->bindParam(':powerwindow',$powerwindow,PDO::PARAM_STR);
$query->bindParam(':cdplayer',$cdplayer,PDO::PARAM_STR);
$query->bindParam(':centrallocking',$centrallocking,PDO::PARAM_STR);
$query->bindParam(':crashcensor',$crashcensor,PDO::PARAM_STR);
$query->bindParam(':leatherseats',$leatherseats,PDO::PARAM_STR);
$query->bindParam(':id',$id,PDO::PARAM_STR);
$query->execute();

$msg="Dados do veículo atualizados com sucesso";

}

	?>
<!doctype html>
<html lang="pt" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="Editar Veículo - Painel Admin">
	<meta name="theme-color" content="#3e454c">
	
	<title>Portal de Aluguer de Carros | Editar Veículo</title>

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
					
						<h2 class="page-title">Editar Veículo</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Informações do Veículo</div>
									<div class="panel-body">
<?php if($msg){?><div class="succWrap"><strong>SUCESSO</strong>: <?php echo htmlentities($msg); ?> </div><?php } ?>
<?php 
$id=intval($_GET['id']);
$sql ="SELECT tblvehicles.*,tblbrands.BrandName,tblbrands.id as bid from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.id=:id";
$query = $dbh -> prepare($sql);
$query-> bindParam(':id', $id, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
{	?>

<form method="post" class="form-horizontal" enctype="multipart/form-data">

<div class="form-group">
<label class="col-sm-2 control-label">Modelo do Veículo<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="vehicletitle" class="form-control" value="<?php echo htmlentities($result->VehiclesTitle)?>" required>
</div>

<label class="col-sm-2 control-label">Marca<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="brandname" required>
<option value="<?php echo htmlentities($result->bid);?>"><?php echo htmlentities($bdname=$result->BrandName); ?> </option>
<?php $ret="select id,BrandName from tblbrands";
$query= $dbh -> prepare($ret);
$query-> execute();
$resultss = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
foreach($resultss as $results)
{
if($results->BrandName==$bdname)
{
continue;
} else{
?>
<option value="<?php echo htmlentities($results->id);?>"><?php echo htmlentities($results->BrandName);?></option>
<?php }}} ?>

</select>
</div>
</div>
											
<div class="form-group">
<label class="col-sm-2 control-label">Matrícula<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="licenseplate" class="form-control" value="<?php echo htmlentities($result->LicensePlate);?>" required>
</div>

<label class="col-sm-2 control-label">Série / Versão</label>
<div class="col-sm-4">
<input type="text" name="seriesversion" class="form-control" value="<?php echo htmlentities($result->SeriesVersion);?>">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Categoria<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="category" required>
<option value="<?php echo htmlentities($result->Category);?>"><?php echo htmlentities($result->Category ? $result->Category : 'Selecionar');?></option>
<option value="Sedan">Sedan</option>
<option value="SUV">SUV</option>
<option value="Picape">Picape</option>
<option value="Hatchback">Hatchback</option>
<option value="Van / Minibus">Van / Minibus</option>
<option value="Comercial / Carga">Comercial / Carga</option>
</select>
</div>

<label class="col-sm-2 control-label">Tipo de Combustível<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="fueltype" required>
<option value="<?php echo htmlentities($result->FuelType);?>"> <?php echo htmlentities($result->FuelType);?> </option>
<option value="Gasolina">Gasolina</option>
<option value="Diesel">Diesel</option>
<option value="Híbrido">Híbrido</option>
<option value="Elétrico">Elétrico</option>
<option value="Gás">Gás</option>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Caixa de Velocidade<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="transmission" required>
<option value="<?php echo htmlentities($result->Transmission);?>"><?php echo htmlentities($result->Transmission ? $result->Transmission : 'Selecionar');?></option>
<option value="Manual">Manual</option>
<option value="Automática">Automática</option>
<option value="CVT">CVT</option>
</select>
</div>

<label class="col-sm-2 control-label">Disponibilidade<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="statusavailability" required>
<option value="<?php echo htmlentities($result->StatusAvailability ? $result->StatusAvailability : 'Disponível');?>"><?php echo htmlentities($result->StatusAvailability ? $result->StatusAvailability : 'Disponível');?></option>
<option value="Disponível">Disponível</option>
<option value="Não Disponível">Não Disponível</option>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Preço por Dia (MT)<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="priceperday" class="form-control" value="<?php echo htmlentities($result->PricePerDay);?>" required>
</div>

<label class="col-sm-2 control-label">Ano do Modelo<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="modelyear" class="form-control" value="<?php echo htmlentities($result->ModelYear);?>" required>
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Capacidade de Lugares<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="seatingcapacity" class="form-control" value="<?php echo htmlentities($result->SeatingCapacity);?>" required>
</div>
</div>

<div class="hr-dashed"></div>
<div class="form-group">
<label class="col-sm-2 control-label">Descrição Geral<span style="color:red">*</span></label>
<div class="col-sm-10">
<textarea class="form-control" name="vehicalorcview" rows="3" required><?php echo htmlentities($result->VehiclesOverview);?></textarea>
</div>
</div>

<div class="hr-dashed"></div>
<div class="form-group">
<div class="col-sm-12">
<h4><b>Imagens do Veículo</b></h4>
</div>
</div>

<div class="form-group">
<div class="col-sm-4">
Imagem 1 <img src="img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" width="300" height="200" style="border:solid 1px #000; display:block; margin-bottom:5px;">
<a href="changeimage1.php?imgid=<?php echo htmlentities($result->id)?>" class="btn btn-xs btn-default">Alterar Imagem 1</a>
</div>
<div class="col-sm-4">
Imagem 2 <img src="img/vehicleimages/<?php echo htmlentities($result->Vimage2);?>" width="300" height="200" style="border:solid 1px #000; display:block; margin-bottom:5px;">
<a href="changeimage2.php?imgid=<?php echo htmlentities($result->id)?>" class="btn btn-xs btn-default">Alterar Imagem 2</a>
</div>
<div class="col-sm-4">
Imagem 3 <img src="img/vehicleimages/<?php echo htmlentities($result->Vimage3);?>" width="300" height="200" style="border:solid 1px #000; display:block; margin-bottom:5px;">
<a href="changeimage3.php?imgid=<?php echo htmlentities($result->id)?>" class="btn btn-xs btn-default">Alterar Imagem 3</a>
</div>
</div>

<div class="form-group">
<div class="col-sm-4">
Imagem 4 <img src="img/vehicleimages/<?php echo htmlentities($result->Vimage4);?>" width="300" height="200" style="border:solid 1px #000; display:block; margin-bottom:5px;">
<a href="changeimage4.php?imgid=<?php echo htmlentities($result->id)?>" class="btn btn-xs btn-default">Alterar Imagem 4</a>
</div>
<div class="col-sm-4">
Imagem 5
<?php if($result->Vimage5=="")
{
echo htmlentities("Sem imagem");
} else {?>
<img src="img/vehicleimages/<?php echo htmlentities($result->Vimage5);?>" width="300" height="200" style="border:solid 1px #000; display:block; margin-bottom:5px;">
<a href="changeimage5.php?imgid=<?php echo htmlentities($result->id)?>" class="btn btn-xs btn-default">Alterar Imagem 5</a>
<?php } ?>
</div>
</div>
<div class="hr-dashed"></div>									
</div>
</div>
</div>
</div>

<div class="row">
<div class="col-md-12">
<div class="panel panel-default">
<div class="panel-heading">Acessórios e Recursos</div>
<div class="panel-body">

<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" name="airconditioner" <?php if($result->AirConditioner==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox1"> Ar Condicionado </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox2" name="powerdoorlocks" <?php if($result->PowerDoorLocks==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox2"> Fecho Central das Portas </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox3" name="antilockbrakingsys" <?php if($result->AntiLockBrakingSystem==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox3"> Sistema ABS </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox4" name="brakeassist" <?php if($result->BrakeAssist==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox4"> Assistente de Travagem </label>
</div>
</div>
</div>

<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox5" name="powersteering" <?php if($result->PowerSteering==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox5"> Direção Assistida </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox6" name="driverairbag" <?php if($result->DriverAirbag==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox6">Airbag Condutor</label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox7" name="passengerairbag" <?php if($result->PassengerAirbag==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox7"> Airbag Acompanhante </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox8" name="powerwindow" <?php if($result->PowerWindows==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox8"> Vidros Elétricos </label>
</div>
</div>
</div>

<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox9" name="cdplayer" <?php if($result->CDPlayer==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox9"> Leitor de CD/Bluetooth </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox10" name="centrallocking" <?php if($result->CentralLocking==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox10">Trancamento Central</label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox11" name="crashcensor" <?php if($result->CrashSensor==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox11"> Sensor de Colisão </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="inlineCheckbox12" name="leatherseats" <?php if($result->LeatherSeats==1) echo 'checked'; ?> value="1">
<label for="inlineCheckbox12"> Estofos em Pele </label>
</div>
</div>
</div>

<?php }} ?>

											<div class="form-group">
												<div class="col-sm-8 col-sm-offset-2">
													<button class="btn btn-primary" name="submit" type="submit" style="margin-top:20px;">Guardar Alterações</button>
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
