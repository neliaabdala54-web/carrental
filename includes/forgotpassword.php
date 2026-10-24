<?php
if(isset($_POST['update']))
{
$email=$_POST['email'];
$mobile=$_POST['mobile'];
$newpassword=md5($_POST['newpassword']);
$sql ="SELECT EmailId FROM tblusers WHERE EmailId=:email and ContactNo=:mobile";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':mobile', $mobile, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
$con="update tblusers set Password=:newpassword where EmailId=:email and ContactNo=:mobile";
$chngpwd1 = $dbh->prepare($con);
$chngpwd1-> bindParam(':email', $email, PDO::PARAM_STR);
$chngpwd1-> bindParam(':mobile', $mobile, PDO::PARAM_STR);
$chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
$chngpwd1->execute();
echo "<script>alert('Sua senha foi alterada com sucesso');</script>";
}
else {
echo "<script>alert('E-mail ou número de telefone registado inválido');</script>";
}
}
?>
<script type="text/javascript">
function validateForgotPasswordForm()
{
  var pass = document.getElementById('forgot_newpassword').value;
  var confirmPass = document.getElementById('forgot_confirmpassword').value;
  if(pass !== confirmPass)
  {
    alert("A nova senha e a confirmação de senha não coincidem!");
    document.getElementById('forgot_confirmpassword').focus();
    return false;
  }
  return true;
}
</script>
<div class="modal fade" id="forgotpassword">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Recuperar Senha</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="forgotpassword_wrap">
            <div class="col-md-12">
              <form name="chngpwd" method="post" onSubmit="return validateForgotPasswordForm();">

                <div class="form-group">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Endereço de E-mail <span style="color:red">*</span></label>
                  <input type="email" name="email" class="form-control" placeholder="Digite o seu e-mail registado" required="">
                </div>

                <div class="form-group">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Número de Celular <span style="color:red">*</span></label>
                  <input type="text" name="mobile" class="form-control" placeholder="Digite o seu número de celular registado" required="">
                </div>

                <div class="form-group" style="position: relative;">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Nova Senha <span style="color:red">*</span></label>
                  <input type="password" name="newpassword" id="forgot_newpassword" class="form-control" placeholder="Digite a nova senha" required="" style="padding-right: 40px;">
                  <span onclick="togglePasswordVisibility('forgot_newpassword', 'eye_icon_forgotnew')" style="position: absolute; right: 12px; bottom: 8px; cursor: pointer; color: #666; font-size: 16px; z-index: 10;" title="Mostrar/Ocultar Senha">
                    <i class="fa fa-eye" id="eye_icon_forgotnew"></i>
                  </span>
                </div>

                <div class="form-group" style="position: relative;">
                  <label style="font-weight: 600; margin-bottom: 5px; color: #333;">Confirmar Nova Senha <span style="color:red">*</span></label>
                  <input type="password" name="confirmpassword" id="forgot_confirmpassword" class="form-control" placeholder="Confirme a nova senha" required="" style="padding-right: 40px;">
                  <span onclick="togglePasswordVisibility('forgot_confirmpassword', 'eye_icon_forgotconfirm')" style="position: absolute; right: 12px; bottom: 8px; cursor: pointer; color: #666; font-size: 16px; z-index: 10;" title="Mostrar/Ocultar Senha">
                    <i class="fa fa-eye" id="eye_icon_forgotconfirm"></i>
                  </span>
                </div>

                <div class="form-group">
                  <input type="submit" value="Redefinir Senha" name="update" class="btn btn-block" style="background-color: #fa2823; border-color: #fa2823; color: #ffffff;">
                </div>

              </form>
              <div class="text-center">
                <p class="gray_text">Por motivos de segurança, a sua nova senha será atualizada diretamente no sistema.</p>
                <p><a href="#loginform" data-toggle="modal" data-dismiss="modal"><i class="fa fa-angle-double-left" aria-hidden="true"></i> Voltar ao Login</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
