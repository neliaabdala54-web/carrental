<?php 
session_start();
include('includes/config.php');
error_reporting(0);
if(isset($_POST['submit']))
{
$fromdate=$_POST['fromdate'];
$todate=$_POST['todate']; 
$message=$_POST['message'];
$useremail=$_SESSION['login'];
$status=0;
$vhid=$_GET['vhid'];

$today = date('Y-m-d');
if($fromdate < $today || $todate < $today)
{
    echo "<script>alert('A data da reserva não pode ser anterior à data atual.');</script>";
    echo "<script type='text/javascript'> document.location = 'vehical-details.php?vhid=" . intval($vhid) . "'; </script>";
    exit;
}
if($todate < $fromdate)
{
    echo "<script>alert('A data de fim não pode ser anterior à data de início.');</script>";
    echo "<script type='text/javascript'> document.location = 'vehical-details.php?vhid=" . intval($vhid) . "'; </script>";
    exit;
}

$bookingno=mt_rand(100000000, 999999999);
$ret="SELECT * FROM tblbooking where (:fromdate BETWEEN date(FromDate) and date(ToDate) || :todate BETWEEN date(FromDate) and date(ToDate) || date(FromDate) BETWEEN :fromdate and :todate) and VehicleId=:vhid";
$query1 = $dbh -> prepare($ret);
$query1->bindParam(':vhid',$vhid, PDO::PARAM_STR);
$query1->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
$query1->bindParam(':todate',$todate,PDO::PARAM_STR);
$query1->execute();
$results1=$query1->fetchAll(PDO::FETCH_OBJ);

if($query1->rowCount()==0)
{

$sql="INSERT INTO tblbooking(BookingNumber,userEmail,VehicleId,FromDate,ToDate,message,Status) VALUES(:bookingno,:useremail,:vhid,:fromdate,:todate,:message,:status)";
$query = $dbh->prepare($sql);
$query->bindParam(':bookingno',$bookingno,PDO::PARAM_STR);
$query->bindParam(':useremail',$useremail,PDO::PARAM_STR);
$query->bindParam(':vhid',$vhid,PDO::PARAM_STR);
$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
$query->bindParam(':todate',$todate,PDO::PARAM_STR);
$query->bindParam(':message',$message,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
echo "<script>alert('Reserva efetuada com sucesso.');</script>";
echo "<script type='text/javascript'> document.location = 'my-booking.php'; </script>";
}
else 
{
echo "<script>alert('Ocorreu um erro. Por favor tente novamente');</script>";
 echo "<script type='text/javascript'> document.location = 'car-listing.php'; </script>";
} }  else{
 echo "<script>alert('O carro já se encontra reservado para este período');</script>";
 echo "<script type='text/javascript'> document.location = 'car-listing.php'; </script>";
}

}

?>


<!DOCTYPE HTML>
<html lang="pt">
<head>

<title>Portal de Aluguer de Carros | Detalhes do Veículo</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custome Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--OWL Carousel slider-->
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<!--slick-slider -->
<link href="assets/css/slick.css" rel="stylesheet">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">

<!-- SWITCHER -->
		<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>
<body>

<!-- Start Switcher -->
<?php include('includes/colorswitcher.php');?>
<!-- /Switcher -->  

<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header --> 

<!--Listing-Image-Slider-->

<?php 
$vhid=intval($_GET['vhid']);
$sql = "SELECT tblvehicles.*,tblbrands.BrandName,tblbrands.id as bid  from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.id=:vhid";
$query = $dbh -> prepare($sql);
$query->bindParam(':vhid',$vhid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
{  
$_SESSION['brndid']=$result->bid;  
?>  

<section id="listing_img_slider">
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage2);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage3);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage4);?>" class="img-responsive"  alt="image" width="900" height="560"></div>
  <?php if($result->Vimage5=="")
{

} else {
  ?>
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage5);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <?php } ?>
</section>
<!--/Listing-Image-Slider-->


<!--Listing-detail-->
<section class="listing-detail">
  <div class="container">
    <div class="listing_detail_head row">
      <div class="col-md-9">
        <h2><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?><?php if($result->SeriesVersion){ echo " <small>(" . htmlentities($result->SeriesVersion) . ")</small>"; } ?></h2>
      </div>
      <div class="col-md-3">
        <div class="price_info">
          <p><?php echo htmlentities($result->PricePerDay);?> MT</p>Por Dia
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-9">
        <div class="main_features">
          <ul>
            <li> <i class="fa fa-calendar" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->ModelYear);?></h5>
            </li>
            <li> <i class="fa fa-cogs" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->FuelType);?></h5>
            </li>
            <li> <i class="fa fa-user-plus" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->SeatingCapacity);?></h5>
            </li>
<?php if($result->Category){ ?>
            <li> <i class="fa fa-tags" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->Category);?></h5>
            </li>
<?php } ?>
<?php if($result->Transmission){ ?>
            <li> <i class="fa fa-tachometer" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->Transmission);?></h5>
            </li>
<?php } ?>
<?php if($result->LicensePlate){ ?>
            <li> <i class="fa fa-id-card-o" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->LicensePlate);?></h5>
            </li>
<?php } ?>
<?php if($result->StatusAvailability){ ?>
            <li> <i class="fa fa-check-circle-o" aria-hidden="true" style="color: #888;"></i>
              <h5 style="color: #666;"><?php echo htmlentities($result->StatusAvailability);?></h5>
            </li>
<?php } ?>
          </ul>
        </div>
        <div class="listing_more_info">
          <div class="listing_detail_wrap"> 
            <!-- Nav tabs -->
            <ul class="nav nav-tabs gray-bg" role="tablist">
              <li role="presentation" class="active"><a href="#vehicle-overview" aria-controls="vehicle-overview" role="tab" data-toggle="tab">Visão Geral do Veículo</a></li>
              <li role="presentation"><a href="#accessories" aria-controls="accessories" role="tab" data-toggle="tab">Acessórios</a></li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content"> 
              <!-- vehicle-overview -->
              <div role="tabpanel" class="tab-pane active" id="vehicle-overview">
                <p><?php echo htmlentities($result->VehiclesOverview);?></p>
              </div>

              <!-- Accessories -->
              <div role="tabpanel" class="tab-pane" id="accessories"> 
                <!--Accessories-->
                <table>
                  <thead>
                    <tr>
                      <th colspan="2">Acessórios & Equipamentos</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Ar Condicionado</td>
<?php if($result->AirConditioner==1) { ?>
                      <td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?> 
                      <td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
                    </tr>

<tr>
<td>Sistema de Travões ABS</td>
<?php if($result->AntiLockBrakingSystem==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else {?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Direção Assistida</td>
<?php if($result->PowerSteering==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Vidros Elétricos</td>
<?php if($result->PowerWindows==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>
                   
<tr>
<td>Leitor de CD / Audio</td>
<?php if($result->CDPlayer==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Estofos em Couro</td>
<?php if($result->LeatherSeats==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Fecho Centralizado</td>
<?php if($result->CentralLocking==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Bloqueio de Portas Elétrico</td>
<?php if($result->PowerDoorLocks==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Assistência de Travagem</td>
<?php if($result->BrakeAssist==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Airbag do Condutor</td>
<?php if($result->DriverAirbag==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>
 
<tr>
<td>Airbag do Passageiro</td>
<?php if($result->PassengerAirbag==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else {?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Sensor de Impacto / Colisão</td>
<?php if($result->CrashSensor==1) { ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
        </div>
<?php }} ?>
   
      </div>
      
      <!--Side-Bar-->
      <aside class="col-md-3">
      
        <div class="share_vehicle">
          <p>Partilhar:
            <a href="https://www.facebook.com/Ariel%20major" target="_blank" title="Facebook: Ariel major"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
            <a href="https://x.com/Niavike" target="_blank" title="X (Twitter): @Niavike" style="display: inline-flex; align-items: center;"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
            <a href="https://www.instagram.com/alien34586" target="_blank" title="Instagram: alien34586"><i class="fa fa-instagram" aria-hidden="true"></i></a>
            <a href="https://wa.me/258870880696" target="_blank" title="WhatsApp" style="color: #25D366;"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
          </p>
        </div>
        <div class="sidebar_widget">
          <div class="widget_heading">
            <h5><i class="fa fa-envelope" aria-hidden="true"></i>Reservar Agora</h5>
          </div>
          <form method="post">
            <div class="form-group">
              <label>Data de Início:</label>
              <input type="date" class="form-control" name="fromdate" id="fromdate" min="<?php echo date('Y-m-d'); ?>" onchange="document.getElementById('todate').min=this.value;" required>
            </div>
            <div class="form-group">
              <label>Data de Fim:</label>
              <input type="date" class="form-control" name="todate" id="todate" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
              <textarea rows="4" class="form-control" name="message" placeholder="Mensagem ou observação..." required></textarea>
            </div>
          <?php if($_SESSION['login'])
              {?>
              <div class="form-group">
                <input type="submit" class="btn" name="submit" value="Confirmar Reserva" style="background-color: #fa2823; border-color: #fa2823; color: #ffffff;">
              </div>
              <?php } else { ?>
<a href="#loginform" class="btn btn-xs uppercase" data-toggle="modal" data-dismiss="modal" style="background-color: #fa2823; border-color: #fa2823; color: #ffffff;">Entrar para Reservar</a>

              <?php } ?>
          </form>
        </div>
      </aside>
      <!--/Side-Bar--> 
    </div>
    
    <div class="space-20"></div>
    <div class="divider"></div>
    
    <!--Similar-Cars-->
    <div class="similar_cars">
      <h3>Carros Semelhantes</h3>
      <div class="row">
<?php 
$bid=$_SESSION['brndid'];
$sql="SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id,tblvehicles.SeatingCapacity,tblvehicles.VehiclesOverview,tblvehicles.Vimage1 from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.VehiclesBrand=:bid";
$query = $dbh -> prepare($sql);
$query->bindParam(':bid',$bid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>      
        <div class="col-md-3 grid_listing">
          <div class="product-listing-m gray-bg">
            <div class="product-listing-img"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image" /> </a>
            </div>
            <div class="product-listing-content">
              <h5><a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a></h5>
              <p class="list-price"><?php echo htmlentities($result->PricePerDay);?> MT / Dia</p>
          
              <ul class="features_list">
                <li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity);?></li>
                <li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear);?></li>
                <li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->FuelType);?></li>
              </ul>
            </div>
          </div>
        </div>
 <?php }} ?>       

      </div>
    </div>
    <!--/Similar-Cars--> 
    
  </div>
</section>
<!--/Listing-detail--> 

<!--Footer -->
<?php include('includes/footer.php');?>
<!-- /Footer--> 

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top--> 

<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form --> 

<!--Register-Form -->
<?php include('includes/registration.php');?>
<!--/Register-Form -->

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<script src="assets/switcher/js/switcher.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>

</body>
</html>