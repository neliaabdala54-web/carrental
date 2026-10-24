<?php
if(isset($_POST['signup']))
{
$fname=$_POST['fullname'];
$email=$_POST['emailid']; 
$mobile=$_POST['mobileno'];
$password=md5($_POST['password']); 
$sql="INSERT INTO tblusers(FullName,EmailId,ContactNo,Password) VALUES(:fname,:email,:mobile,:password)";
$query = $dbh->prepare($sql);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':mobile',$mobile,PDO::PARAM_STR);
$query->bindParam(':password',$password,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
echo "<script>alert('Cadastro realizado com sucesso. Agora pode iniciar sessão');</script>";
}
else 
{
echo "<script>alert('Ocorreu um erro. Por favor tente novamente');</script>";
}
}

?>

<script>
function checkAvailability() {
  $("#loaderIcon").show();
  jQuery.ajax({
    url: "check_availability.php",
    data:'emailid='+$("#emailid").val(),
    type: "POST",
    success:function(data){
      $("#user-availability-status").html(data);
      $("#loaderIcon").hide();
    },
    error:function (){}
  });
}

function togglePasswordVisibility(inputId, iconId) {
  var input = document.getElementById(inputId);
  var icon = document.getElementById(iconId);
  if (input.type === "password") {
    input.type = "text";
    icon.className = "fa fa-eye-slash";
  } else {
    input.type = "password";
    icon.className = "fa fa-eye";
  }
}

function validateSignupForm()
{
  var pass = document.getElementById('signup_password').value;
  var confirmPass = document.getElementById('signup_confirmpassword').value;
  if(pass !== confirmPass)
  {
    alert("A senha e a confirmação de senha não coincidem!");
    document.getElementById('signup_confirmpassword').focus();
    return false;
  }
  return true;
}
</script>

<div class="modal fade" id="signupform" tabindex="-1" role="dialog" style="overflow-x: hidden !important; overflow-y: auto !important; touch-action: pan-y;">
  <div class="modal-dialog" role="document" style="overflow-x: hidden !important; touch-action: pan-y;">
    <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column; overflow-x: hidden !important; touch-action: pan-y;">
      <div class="modal-header" style="flex-shrink: 0;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Criar Conta</h3>
      </div>
      <div class="modal-body" style="overflow-x: hidden !important; overflow-y: auto; max-height: calc(90vh - 120px); -webkit-overflow-scrolling: touch; flex-grow: 1; touch-action: pan-y;">
        <div class="row" style="margin-left: 0; margin-right: 0;">
          <div class="signup_wrap">
            <div class="col-md-12 col-sm-12" style="padding-left: 0; padding-right: 0;">
              <form method="post" name="signup" onSubmit="return validateSignupForm();">

                <div class="form-group">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Nome Completo <span style="color:red">*</span></label>
                  <input type="text" class="form-control" name="fullname" placeholder="Digite o seu nome completo" required="required">
                </div>

                <div class="form-group">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Número de Celular <span style="color:red">*</span></label>
                  <input type="text" class="form-control" name="mobileno" placeholder="Digite o seu número de celular (Ex: 870880696)" maxlength="15" required="required">
                </div>

                <div class="form-group">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Endereço de E-mail <span style="color:red">*</span></label>
                  <input type="email" class="form-control" name="emailid" id="emailid" onBlur="checkAvailability()" placeholder="Digite o seu endereço de e-mail" required="required">
                  <span id="user-availability-status" style="font-size:12px;"></span>
                </div>

                <div class="form-group" style="position: relative;">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Senha <span style="color:red">*</span></label>
                  <input type="password" class="form-control" name="password" id="signup_password" placeholder="Digite a sua senha" required="required" style="padding-right: 40px;">
                  <span onclick="togglePasswordVisibility('signup_password', 'eye_icon_pass')" style="position: absolute; right: 12px; bottom: 8px; cursor: pointer; color: #666; font-size: 16px; z-index: 10;" title="Mostrar/Ocultar Senha">
                    <i class="fa fa-eye" id="eye_icon_pass"></i>
                  </span>
                </div>

                <div class="form-group" style="position: relative;">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Confirmar Senha <span style="color:red">*</span></label>
                  <input type="password" class="form-control" name="confirmpassword" id="signup_confirmpassword" placeholder="Confirme a sua senha" required="required" style="padding-right: 40px;">
                  <span onclick="togglePasswordVisibility('signup_confirmpassword', 'eye_icon_confirmpass')" style="position: absolute; right: 12px; bottom: 8px; cursor: pointer; color: #666; font-size: 16px; z-index: 10;" title="Mostrar/Ocultar Senha">
                    <i class="fa fa-eye" id="eye_icon_confirmpass"></i>
                  </span>
                </div>

                <div class="form-group checkbox">
                  <input type="checkbox" id="terms_agree" required="required" checked="">
                  <label for="terms_agree">Concordo com os <a href="page.php?type=terms">Termos e Condições</a></label>
                </div>

                <div class="form-group">
                  <input type="submit" value="Criar Conta" name="signup" id="submit" class="btn btn-block" style="background-color: #fa2823; border-color: #fa2823; color: #ffffff;">
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <p>Já possui uma conta? <a href="#loginform" data-toggle="modal" data-dismiss="modal">Entrar aqui</a></p>
      </div>
    </div>
  </div>
</div>
