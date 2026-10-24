<?php 
session_start();
include('includes/config.php');
error_reporting(0);

?>

<!DOCTYPE HTML>
<html lang="pt">
<head>

<title>Portal de Aluguer de Carros</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<link href="assets/css/slick.css" rel="stylesheet">
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
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
<style>
.carousel-fade .carousel-inner .item {
  opacity: 0;
  transition-property: opacity;
  transition-duration: 0.8s;
  transition-timing-function: ease-in-out;
}
.carousel-fade .carousel-inner .active {
  opacity: 1;
}
.carousel-fade .carousel-inner .active.left,
.carousel-fade .carousel-inner .active.right {
  left: 0;
  opacity: 0;
  z-index: 1;
}
.carousel-fade .carousel-inner .next.left,
.carousel-fade .carousel-inner .prev.right {
  opacity: 1;
}
.carousel-fade .carousel-control {
  z-index: 2;
}
</style>
</head>
<body>

<!-- Start Switcher -->
<?php include('includes/colorswitcher.php');?>
<!-- /Switcher -->  
        
<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header --> 

<!-- Banners Slider - 4 Imagens em Rotação Automática Sem Texto -->
<section id="banner-slider" class="carousel slide carousel-fade" data-ride="carousel" data-interval="3000" data-pause="false" style="position: relative; overflow: hidden; background: #000;">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#banner-slider" data-slide-to="0" class="active"></li>
    <li data-target="#banner-slider" data-slide-to="1"></li>
    <li data-target="#banner-slider" data-slide-to="2"></li>
    <li data-target="#banner-slider" data-slide-to="3"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <!-- Imagem 1 -->
    <div class="item active" style="height: 480px; background: url('assets/images/banner-image.jpg') center center / cover no-repeat;"></div>

    <!-- Imagem 2 -->
    <div class="item" style="height: 480px; background: url('assets/images/banner-image-1.jpg') center center / cover no-repeat;"></div>

    <!-- Imagem 3 -->
    <div class="item" style="height: 480px; background: url('assets/images/banner-image-2.jpg') center center / cover no-repeat;"></div>

    <!-- Imagem 4 -->
    <div class="item" style="height: 480px; background: url('assets/images/aboutus-page-header-img.jpg') center center / cover no-repeat;"></div>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#banner-slider" role="button" data-slide="prev" style="background: none; width: 5%;">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true" style="font-size: 30px;"></span>
    <span class="sr-only">Anterior</span>
  </a>
  <a class="right carousel-control" href="#banner-slider" role="button" data-slide="next" style="background: none; width: 5%;">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true" style="font-size: 30px;"></span>
    <span class="sr-only">Próximo</span>
  </a>
</section>
<!-- /Banners --> 


<!-- Resent Cat-->
<section class="section-padding gray-bg">
  <div class="container">
    <div class="section-header text-center">
      <h2>Encontre o Melhor <span>Carro Para Si</span></h2>
      <p>Oferecemos uma vasta gama de veículos modernos, confortáveis e seguros para atender a todas as suas necessidades de transporte e viagens com as melhores tarifas do mercado.</p>
    </div>
    <div class="row"> 
      
      <!-- Nav tabs -->
      <div class="recent-tab">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#resentnewcar" role="tab" data-toggle="tab">Veículos Recentes</a></li>
        </ul>
      </div>
      <!-- Recently Listed New Cars -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="resentnewcar">

<?php $sql = "SELECT tblvehicles.*,tblbrands.BrandName from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand order by tblvehicles.id desc limit 9";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
{  
?>  

<div class="col-list-3">
<div class="recent-car-list" style="border-radius: 0;">
<div class="car-info-box"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image"></a>
<ul>
<li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->FuelType);?></li>
<li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear);?></li>
<li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity);?></li>
</ul>
</div>
<div class="car-title-m">
<h6><a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"> <?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?><?php if($result->SeriesVersion){ echo " <small class='text-muted'>(" . htmlentities($result->SeriesVersion) . ")</small>"; } ?></a></h6>
<span class="price"><?php echo htmlentities($result->PricePerDay);?> MT / Dia</span>
</div>
<div class="inventory_info_m">
<p style="margin-bottom: 5px;">
  <?php if($result->Category){ ?><span class="label label-default" style="margin-right:3px;"><?php echo htmlentities($result->Category);?></span><?php } ?>
  <?php if($result->Transmission){ ?><span class="label label-info" style="margin-right:3px;"><?php echo htmlentities($result->Transmission);?></span><?php } ?>
  <?php if($result->StatusAvailability == 'Não Disponível'){ ?><span class="label label-default" style="background-color: #888888; color: #ffffff; font-weight: normal;"><i class="fa fa-times-circle" style="font-size:10px; margin-right:3px;"></i>Indisponível</span><?php } else if($result->StatusAvailability){ ?><span class="label label-default" style="background-color: #888888; color: #ffffff; font-weight: normal;"><i class="fa fa-check-circle-o" style="font-size:10px; margin-right:3px;"></i>Disponível</span><?php } ?>
</p>
<p><?php echo substr($result->VehiclesOverview,0,70);?></p>
</div>
</div>
</div>
<?php }}?>
       
      </div>
    </div>
  </div>
</section>
<!-- /Resent Cat --> 

<!-- Fun Facts-->
<section class="fun-facts-section">
  <div class="container div_zindex">
    <div class="row">
      <div class="col-lg-3 col-xs-6 col-sm-3">
        <div class="fun-facts-m">
          <div class="cell">
            <h2><i class="fa fa-calendar" aria-hidden="true"></i>10+</h2>
            <p>Anos no Mercado</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6 col-sm-3">
        <div class="fun-facts-m">
          <div class="cell">
            <h2><i class="fa fa-car" aria-hidden="true"></i>500+</h2>
            <p>Veículos Disponíveis</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6 col-sm-3">
        <div class="fun-facts-m">
          <div class="cell">
            <h2><i class="fa fa-car" aria-hidden="true"></i>1000+</h2>
            <p>Alugueres Realizados</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6 col-sm-3">
        <div class="fun-facts-m">
          <div class="cell">
            <h2><i class="fa fa-user-circle-o" aria-hidden="true"></i>2000+</h2>
            <p>Clientes Satisfeitos</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Dark Overlay-->
  <div class="dark-overlay"></div>
</section>
<!-- /Fun Facts--> 


<!--Testimonial -->
<section class="section-padding testimonial-section parallex-bg">
  <div class="container div_zindex">
    <div class="section-header white-text text-center">
      <h2>Depoimentos de <span>Clientes</span></h2>
    </div>
    <div class="row">
      <div id="testimonial-slider">
<?php 
$tid=1;
$sql = "SELECT tbltestimonial.Testimonial,tblusers.FullName from tbltestimonial join tblusers on tbltestimonial.UserEmail=tblusers.EmailId where tbltestimonial.status=:tid limit 4";
$query = $dbh -> prepare($sql);
$query->bindParam(':tid',$tid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
{  ?>


        <div class="testimonial-m">
 
          <div class="testimonial-content">
            <div class="testimonial-heading">
              <h5><?php echo htmlentities($result->FullName);?></h5>
            <p><?php echo htmlentities($result->Testimonial);?></p>
          </div>
        </div>
        </div>
        <?php }} ?>
        
       
  
      </div>
    </div>
  </div>
  <!-- Dark Overlay-->
  <div class="dark-overlay"></div>
</section>
<!-- /Testimonial--> 


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
<!--/Forgot-password-Form --> 

<!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->
<script src="assets/switcher/js/switcher.js"></script>
<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 
<!--Slider-JS--> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>

<script>
$(document).ready(function(){
  $('#banner-slider').carousel({
    interval: 3000,
    pause: false,
    wrap: true
  });
});
</script>

</body>
</html>