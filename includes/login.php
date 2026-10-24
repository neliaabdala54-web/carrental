<?php
if(isset($_POST['login']))
{
$email=$_POST['email'];
$password=md5($_POST['password']);
$sql ="SELECT EmailId,Password,FullName FROM tblusers WHERE EmailId=:email and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
$_SESSION['login']=$_POST['email'];
$_SESSION['fname']=$results->FullName;
$currentpage=$_SERVER['REQUEST_URI'];
echo "<script type='text/javascript'> document.location = '$currentpage'; </script>";
} else{
  echo "<script>alert('E-mail ou Senha incorretos');</script>";
}
}
?>

<div class="modal fade" id="loginform">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Entrar na Conta</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="login_wrap">
            <div class="col-md-12 col-sm-6">
              <form method="post">

                <div class="form-group">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Endereço de E-mail <span style="color:red">*</span></label>
                  <input type="email" class="form-control" name="email" placeholder="Digite o seu e-mail" required>
                </div>

                <div class="form-group" style="position: relative;">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Senha <span style="color:red">*</span></label>
                  <input type="password" class="form-control" name="password" id="login_password" placeholder="Digite a sua senha" required style="padding-right: 40px;">
                  <span onclick="togglePasswordVisibility('login_password', 'eye_icon_loginpass')" style="position: absolute; right: 12px; bottom: 8px; cursor: pointer; color: #666; font-size: 16px; z-index: 10;" title="Mostrar/Ocultar Senha">
                    <i class="fa fa-eye" id="eye_icon_loginpass"></i>
                  </span>
                </div>

                <div class="form-group">
                  <input type="submit" name="login" value="Entrar" class="btn btn-block" style="background-color: #fa2823; border-color: #fa2823; color: #ffffff;">
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <p>Ainda não tem uma conta? <a href="#signupform" data-toggle="modal" data-dismiss="modal">Registe-se aqui</a></p>
        <p><a href="#forgotpassword" data-toggle="modal" data-dismiss="modal">Esqueceu a senha?</a></p>
      </div>
    </div>
  </div>
</div>
