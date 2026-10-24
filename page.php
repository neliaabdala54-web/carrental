<?php
session_start();
error_reporting(0);
include('includes/config.php');

$pagetype = $_GET['type'];
if(empty($pagetype)) {
  $pagetype = 'aboutus';
}

// Tentar atualizar BD para Português
try {
  $dbh->exec("UPDATE tblpages SET PageName='Sobre Nós', detail='Somos uma empresa líder no segmento de aluguer de automóveis em Moçambique, comprometida em oferecer aos nossos clientes soluções de mobilidade modernas, seguras e acessíveis.<br><br>A nossa frota conta com veículos de alta qualidade, inspecionados e prontos para atender tanto a viagens de negócios como a passeios em família. Garantimos um atendimento personalizado, transparência total e as melhores tarifas do mercado.' WHERE type='aboutus'");
  $dbh->exec("UPDATE tblpages SET PageName='Política de Privacidade', detail='A sua privacidade é fundamental para nós. Esta política descreve como recolhemos, usamos e protegemos as suas informações pessoais quando utiliza os nossos serviços de aluguer de carros.<br><br>Todos os dados fornecidos são armazenados com total segurança e utilizados exclusivamente para o processamento de reservas e melhoria contínua dos nossos serviços.' WHERE type='privacy'");
  $dbh->exec("UPDATE tblpages SET PageName='Termos e Condições', detail='Ao utilizar o nosso portal e efetuar uma reserva de veículo, concorda com os nossos termos e condições de serviço.<br><br>O condutor deve possuir uma licença de condução válida e idade mínima legal. Os pagamentos e cauções são informados com total transparência antes da confirmação final da reserva.' WHERE type='terms'");
} catch(PDOException $e) {
  // Ignore DB update errors
}

// Consultar dados
$sql = "SELECT type,detail,PageName from tblpages where type=:pagetype";
$query = $dbh -> prepare($sql);
$query->bindParam(':pagetype',$pagetype,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

$pageTitle = "Sobre Nós";
$detailContent = "";

if($pagetype == 'aboutus') {
  $pageTitle = "Sobre Nós";
  $addressInfo = "";
  $sqlContact = "SELECT Address FROM tblcontactusinfo LIMIT 1";
  $queryContact = $dbh->query($sqlContact);
  if($queryContact && $rowContact = $queryContact->fetch(PDO::FETCH_OBJ)) {
    if(!empty($rowContact->Address)) {
      $addressInfo = "<br><br><strong>Localização da Empresa:</strong> " . htmlentities($rowContact->Address) . ".";
    }
  }
  $detailContent = "Somos uma empresa líder no segmento de aluguer de automóveis em Moçambique, comprometida em oferecer aos nossos clientes soluções de mobilidade modernas, seguras e acessíveis.<br><br>A nossa frota conta com veículos de alta qualidade, inspecionados e prontos para atender tanto a viagens de negócios como a passeios em família. Garantimos um atendimento personalizado, transparência total e as melhores tarifas do mercado." . $addressInfo;
} else if($pagetype == 'privacy') {
  $pageTitle = "Política de Privacidade";
  $detailContent = "A sua privacidade é fundamental para nós. Esta política descreve como recolhemos, usamos e protegemos as suas informações pessoais quando utiliza os nossos serviços de aluguer de carros.<br><br>Todos os dados fornecidos são armazenados com total segurança e utilizados exclusivamente para o processamento de reservas e melhoria contínua dos nossos serviços.";
} else if($pagetype == 'terms') {
  $pageTitle = "Termos e Condições";
  $detailContent = "Ao utilizar o nosso portal e efetuar uma reserva de veículo, concorda com os nossos termos e condições de serviço.<br><br>O condutor deve possuir uma licença de condução válida e idade mínima legal. Os pagamentos e cauções são informados com total transparência antes da confirmação final da reserva.";
} else if($query->rowCount() > 0) {
  foreach($results as $result) {
    $pageTitle = $result->PageName;
    $detailContent = $result->detail;
  }
}
?>

<!DOCTYPE HTML>
<html lang="pt">
<head>

<title>Portal de Aluguer de Carros | <?php echo htmlentities($pageTitle); ?></title>
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
        
<!-- Fav and touch icons -->
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

<section class="page-header aboutus_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1><?php echo htmlentities($pageTitle); ?></h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="index.php">Início</a></li>
        <li><?php echo htmlentities($pageTitle); ?></li>
      </ul>
    </div>
  </div>
  <!-- Dark Overlay-->
  <div class="dark-overlay"></div>
</section>

<section class="about_us section-padding">
  <div class="container">
    <div class="section-header text-center">
      <h2><?php echo htmlentities($pageTitle); ?></h2>
      <div style="font-size: 16px; line-height: 1.8; color: #4a5568; margin-top: 20px; text-align: justify; max-width: 900px; margin-left: auto; margin-right: auto;">
        <?php echo $detailContent; ?>
      </div>
    </div>
  </div>
</section>
<!-- /About-us--> 

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

</body>
</html>
