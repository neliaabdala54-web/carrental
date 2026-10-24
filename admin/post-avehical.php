<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{ 

// Garantir colunas adicionais na base de dados
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

$vimage1=$_FILES["img1"]["name"];
$vimage2=$_FILES["img2"]["name"];
$vimage3=$_FILES["img3"]["name"];
$vimage4=$_FILES["img4"]["name"];
$vimage5=$_FILES["img5"]["name"];

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

move_uploaded_file($_FILES["img1"]["tmp_name"],"img/vehicleimages/".$_FILES["img1"]["name"]);
move_uploaded_file($_FILES["img2"]["tmp_name"],"img/vehicleimages/".$_FILES["img2"]["name"]);
move_uploaded_file($_FILES["img3"]["tmp_name"],"img/vehicleimages/".$_FILES["img3"]["name"]);
move_uploaded_file($_FILES["img4"]["tmp_name"],"img/vehicleimages/".$_FILES["img4"]["name"]);
move_uploaded_file($_FILES["img5"]["tmp_name"],"img/vehicleimages/".$_FILES["img5"]["name"]);

$sql="INSERT INTO tblvehicles(VehiclesTitle,VehiclesBrand,VehiclesOverview,PricePerDay,FuelType,ModelYear,SeatingCapacity,LicensePlate,SeriesVersion,Category,Transmission,StatusAvailability,Vimage1,Vimage2,Vimage3,Vimage4,Vimage5,AirConditioner,PowerDoorLocks,AntiLockBrakingSystem,BrakeAssist,PowerSteering,DriverAirbag,PassengerAirbag,PowerWindows,CDPlayer,CentralLocking,CrashSensor,LeatherSeats) VALUES(:vehicletitle,:brand,:vehicleoverview,:priceperday,:fueltype,:modelyear,:seatingcapacity,:licenseplate,:seriesversion,:category,:transmission,:statusavailability,:vimage1,:vimage2,:vimage3,:vimage4,:vimage5,:airconditioner,:powerdoorlocks,:antilockbrakingsys,:brakeassist,:powersteering,:driverairbag,:passengerairbag,:powerwindow,:cdplayer,:centrallocking,:crashcensor,:leatherseats)";
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

$query->bindParam(':vimage1',$vimage1,PDO::PARAM_STR);
$query->bindParam(':vimage2',$vimage2,PDO::PARAM_STR);
$query->bindParam(':vimage3',$vimage3,PDO::PARAM_STR);
$query->bindParam(':vimage4',$vimage4,PDO::PARAM_STR);
$query->bindParam(':vimage5',$vimage5,PDO::PARAM_STR);

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

$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg="Veículo cadastrado com sucesso";
}
else 
{
$error="Ocorreu um erro ao cadastrar o veículo. Tente novamente.";
}

}

	?>
<!doctype html>
<html lang="pt" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="Publicar Veículo - Painel Admin">
	<meta name="theme-color" content="#3e454c">
	
	<title>Portal de Aluguer de Carros | Publicar Veículo</title>

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
					
						<h2 class="page-title">Publicar Novo Veículo</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Informações Básicas do Veículo</div>
<?php if($error){?><div class="errorWrap"><strong>ERRO</strong>: <?php echo htmlentities($error); ?> </div><?php }
				else if($msg){?><div class="succWrap"><strong>SUCESSO</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">

<div class="form-group">
<label class="col-sm-2 control-label">Modelo do Veículo<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="vehicletitle" class="form-control" placeholder="Ex: Corolla, Hilux, Fortuner" required>
</div>

<label class="col-sm-2 control-label">Selecionar Marca<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="brandname" required>
<option value=""> Selecionar Marca </option>
<?php $ret="select id,BrandName from tblbrands";
$query= $dbh -> prepare($ret);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
foreach($results as $result)
{
?>
<option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?></option>
<?php }} ?>

</select>
</div>
</div>
											
<div class="form-group">
<label class="col-sm-2 control-label">Matrícula<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="licenseplate" class="form-control" placeholder="Ex: MM-123-MC" required>
</div>

<label class="col-sm-2 control-label">Série / Versão</label>
<div class="col-sm-4">
<input type="text" name="seriesversion" class="form-control" placeholder="Ex: 2.8 GD-6, 1.8 VVT-i, Executivo">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Categoria<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="category" required>
<option value=""> Selecionar Categoria </option>
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
<option value=""> Selecionar Combustível </option>
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
<option value=""> Selecionar Transmissão </option>
<option value="Manual">Manual</option>
<option value="Automática">Automática</option>
<option value="CVT">CVT</option>
</select>
</div>

<label class="col-sm-2 control-label">Disponibilidade<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="statusavailability" required>
<option value="Disponível">Disponível</option>
<option value="Não Disponível">Não Disponível</option>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Preço por Dia (MT)<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="priceperday" class="form-control" placeholder="Ex: 2500" required>
</div>

<label class="col-sm-2 control-label">Ano do Modelo<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="modelyear" class="form-control" placeholder="Ex: 2022" required>
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Capacidade de Lugares<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="seatingcapacity" class="form-control" placeholder="Ex: 5" required>
</div>
</div>

<div class="hr-dashed"></div>
<div class="form-group">
<label class="col-sm-2 control-label">Descrição Geral<span style="color:red">*</span></label>
<div class="col-sm-10">
<textarea class="form-control" name="vehicalorcview" rows="3" placeholder="Escreva uma breve descrição do carro..." required></textarea>
</div>
</div>

<div class="hr-dashed"></div>

<div class="form-group">
<div class="col-sm-12">
<h4><b>Carregar Imagens do Veículo</b></h4>
</div>
</div>

<div class="form-group">
<div class="col-sm-4">
Imagem 1 <span style="color:red">*</span><input type="file" name="img1" required>
</div>
<div class="col-sm-4">
Imagem 2 <span style="color:red">*</span><input type="file" name="img2" required>
</div>
<div class="col-sm-4">
Imagem 3 <span style="color:red">*</span><input type="file" name="img3" required>
</div>
</div>

<div class="form-group">
<div class="col-sm-4">
Imagem 4 <span style="color:red">*</span><input type="file" name="img4" required>
</div>
<div class="col-sm-4">
Imagem 5<input type="file" name="img5">
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
<input type="checkbox" id="airconditioner" name="airconditioner" value="1">
<label for="airconditioner"> Ar Condicionado </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="powerdoorlocks" name="powerdoorlocks" value="1">
<label for="powerdoorlocks"> Fecho Central das Portas </label>
</div></div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="antilockbrakingsys" name="antilockbrakingsys" value="1">
<label for="antilockbrakingsys"> Sistema de Travões ABS </label>
</div></div>
<div class="checkbox checkbox-inline">
<input type="checkbox" id="brakeassist" name="brakeassist" value="1">
<label for="brakeassist"> Assistente de Travagem </label>
</div>
</div>

<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="powersteering" name="powersteering" value="1">
<label for="powersteering"> Direção Assistida </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="driverairbag" name="driverairbag" value="1">
<label for="driverairbag">Airbag do Condutor</label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="passengerairbag" name="passengerairbag" value="1">
<label for="passengerairbag"> Airbag do Acompanhante </label>
</div></div>
<div class="checkbox checkbox-inline">
<input type="checkbox" id="powerwindow" name="powerwindow" value="1">
<label for="powerwindow"> Vidros Elétricos </label>
</div>
</div>

<div class="form-group">
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="cdplayer" name="cdplayer" value="1">
<label for="cdplayer"> Leitor de CD / Bluetooth </label>
</div>
</div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="centrallocking" name="centrallocking" value="1">
<label for="centrallocking">Trancamento Central</label>
</div></div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="crashcensor" name="crashcensor" value="1">
<label for="crashcensor"> Sensor de Colisão / Estacionamento </label>
</div></div>
<div class="col-sm-3">
<div class="checkbox checkbox-inline">
<input type="checkbox" id="leatherseats" name="leatherseats" value="1">
<label for="leatherseats"> Estofos em Pele </label>
</div>
</div>
</div>

											<div class="form-group">
												<div class="col-sm-8 col-sm-offset-2">
													<button class="btn btn-default" type="reset">Cancelar</button>
													<button class="btn btn-primary" name="submit" type="submit">Cadastrar Veículo</button>
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
