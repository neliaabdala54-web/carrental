<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(isset($_POST['send']))
{
$name=$_POST['fullname'];
$email=$_POST['email'];
$contactno=$_POST['contactno'];
$message=$_POST['message'];

// Guardar na base de dados
$sql="INSERT INTO tblcontactusquery(name,EmailId,ContactNumber,Message) VALUES(:name,:email,:contactno,:message)";
$query = $dbh->prepare($sql);
$query->bindParam(':name',$name,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':contactno',$contactno,PDO::PARAM_STR);
$query->bindParam(':message',$message,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();

if($lastInsertId)
{
  // Obter o e-mail de destino das configurações
  $adminEmail = "neliaabdala54@gmail.com";
  $sqlContact = "SELECT EmailId FROM tblcontactusinfo LIMIT 1";
  $queryContact = $dbh->query($sqlContact);
  if($queryContact && $rowContact = $queryContact->fetch(PDO::FETCH_OBJ)) {
    if(!empty($rowContact->EmailId)) {
      $adminEmail = trim($rowContact->EmailId);
    }
  }

  // Enviar e-mail de notificação para o suporte
  $to = $adminEmail;
  $subject = "Novo Contacto Recebido - Portal de Aluguer de Carros";
  $body = "Recebeu uma nova mensagem do formulário de contacto do site:\n\n";
  $body .= "Nome do Cliente: " . $name . "\n";
  $body .= "E-mail do Cliente: " . $email . "\n";
  $body .= "Contacto Telefónico: " . $contactno . "\n\n";
  $body .= "Mensagem:\n" . $message . "\n\n";
  $body .= "----------------------------------------\n";
  $body .= "Enviado automaticamente a partir do formulário do Portal de Aluguer de Carros.";

  $headers = "From: " . $email . "\r\n";
  $headers .= "Reply-To: " . $email . "\r\n";
  $headers .= "X-Mailer: PHP/" . phpversion();

  @mail($to, $subject, $body, $headers);

  $msg = "Mensagem enviada com sucesso! Entraremos em contacto brevemente.";
}
else 
{
  $error = "Ocorreu um erro ao enviar a mensagem. Por favor tente novamente.";
}

}
?>
<!DOCTYPE HTML>
<html lang="pt">
<head>

<title>Portal de Aluguer de Carros | Contacte-nos</title>
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

<!-- Start Switcher -->
<?php include('includes/colorswitcher.php');?>
<!-- /Switcher -->  
        
<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header --> 

<!--Page Header-->
<section class="page-header contactus_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1>Contacte-nos</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="index.php">Início</a></li>
        <li>Contacte-nos</li>
      </ul>
    </div>
  </div>
  <!-- Dark Overlay-->
  <div class="dark-overlay"></div>
</section>
<!-- /Page Header--> 

<!--Contact-us-->
<section class="contact_us section-padding">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <h3>Entre em contacto connosco pelo formulário</h3>
          <?php if($error){?><div class="errorWrap"><strong>ERRO</strong>: <?php echo htmlentities($error); ?> </div><?php }
        else if($msg){?><div class="succWrap"><strong>SUCESSO</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>
        <div class="contact_form gray-bg">
          <form method="post">
            <div class="form-group">
              <label class="control-label">Nome Completo <span>*</span></label>
              <input type="text" name="fullname" class="form-control white_bg" id="fullname" required>
            </div>
            <div class="form-group">
              <label class="control-label">Endereço de E-mail <span>*</span></label>
              <input type="email" name="email" class="form-control white_bg" id="emailaddress" required>
            </div>
            <div class="form-group">
              <label class="control-label">Número de Celular <span>*</span></label>
              <input type="text" name="contactno" class="form-control white_bg" id="phonenumber" required maxlength="15">
            </div>
            <div class="form-group">
              <label class="control-label">Mensagem <span>*</span></label>
              <textarea class="form-control white_bg" name="message" rows="4" required></textarea>
            </div>
            <div class="form-group">
              <button class="btn" type="submit" name="send">Enviar Mensagem <span class="angle_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span></button>
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <h3>Informações de Contacto</h3>
        <div class="contact_detail">
<?php
$sql = "SELECT * from tblcontactusinfo";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
{
?>
          <ul>
            <?php if(!empty($result->Address)){ ?>
            <li>
              <div class="icon_wrap"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
              <div class="contact_info_m"><strong>Localização:</strong> <?php echo htmlentities($result->Address); ?></div>
            </li>
            <?php } ?>
            <?php if(!empty($result->EmailId)){ ?>
            <li>
              <div class="icon_wrap"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
              <div class="contact_info_m"><a href="mailto:<?php echo htmlentities($result->EmailId); ?>"><?php echo htmlentities($result->EmailId); ?></a></div>
            </li>
            <?php } ?>
            <?php if(!empty($result->ContactNo)){ ?>
            <li>
              <div class="icon_wrap"><i class="fa fa-phone" aria-hidden="true"></i></div>
              <div class="contact_info_m"><a href="tel:<?php echo htmlentities($result->ContactNo); ?>"><?php echo htmlentities($result->ContactNo); ?></a></div>
            </li>
            <?php } ?>
          </ul>
<?php }} ?>

          <h4 style="margin-top: 25px; margin-bottom: 12px; font-weight: 700;">Redes Sociais & Mensagens</h4>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; gap: 6px; align-items: center;">
            <li style="margin: 0; padding: 0;">
              <a href="https://www.facebook.com/Ariel%20major" target="_blank" title="Facebook: Ariel major" style="color: #3b5998; font-size: 22px; padding: 3px; display: inline-flex; align-items: center;"><i class="fa fa-facebook-square"></i></a>
            </li>
            <li style="margin: 0; padding: 0;">
              <a href="https://x.com/Niavike" target="_blank" title="X (Twitter): @Niavike" style="color: #111; font-size: 18px; padding: 3px; display: inline-flex; align-items: center;"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
            </li>
            <li style="margin: 0; padding: 0;">
              <a href="https://www.instagram.com/alien34586" target="_blank" title="Instagram: alien34586" style="color: #e1306c; font-size: 22px; padding: 3px; display: inline-flex; align-items: center;"><i class="fa fa-instagram"></i></a>
            </li>
            <li style="margin: 0; padding: 0;">
              <a href="https://wa.me/258870880696" target="_blank" title="WhatsApp" style="color: #25D366; font-size: 22px; padding: 3px; display: inline-flex; align-items: center;"><i class="fa fa-whatsapp"></i></a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /Contact-us--> 


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