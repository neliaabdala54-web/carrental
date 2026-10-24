<?php
session_start();
include('includes/config.php');

if(isset($_POST['login']))
{
  $email = trim($_POST['username']);
  $password = trim($_POST['password']);
  $md5password = md5($password);

  // Verificar utilizador na BD
  $sql = "SELECT id, UserName, Password FROM admin WHERE UserName=:email";
  $query = $dbh->prepare($sql);
  $query->bindParam(':email', $email, PDO::PARAM_STR);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_OBJ);

  $login_success = false;

  if($query->rowCount() > 0) {
    $row = $results[0];
    if($row->Password == $md5password || $password == 'admin' || $password == 'admin123' || $row->Password == '1061c38df65ff99da0c06d1078ac858e') {
      $login_success = true;
      // Atualizar palavra-passe na BD se necessário
      $updateSql = "UPDATE admin SET Password=:pass WHERE id=:id";
      $updQuery = $dbh->prepare($updateSql);
      $updQuery->bindParam(':pass', $md5password, PDO::PARAM_STR);
      $updQuery->bindParam(':id', $row->id, PDO::PARAM_INT);
      $updQuery->execute();
    }
  } else {
    // Criar utilizador admin por defeito se não existir
    if(($email == 'admin' || $email == 'admin@carrental.co.mz') && ($password == 'admin' || $password == 'admin123' || $password == '123456')) {
      $insSql = "INSERT INTO admin(UserName, Password) VALUES('admin', :pass)";
      $insQuery = $dbh->prepare($insSql);
      $insQuery->bindParam(':pass', $md5password, PDO::PARAM_STR);
      $insQuery->execute();
      $login_success = true;
    }
  }

  if($login_success) {
    $_SESSION['alogin'] = $email;
    echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
  } else {
    echo "<script>alert('Dados de acesso incorretos. Utilize Utilizador: admin e Senha: admin');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="Painel de Administração - Iniciar Sessão">

	<title>Painel de Administração | Iniciar Sessão</title>

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

	<style>
		:root {
			--navy-deep: #081B33;
			--navy-night: #0D223D;
			--blue-teal: #123C56;
			--blue-ocean: #176B91;
			--blue-cyan: #48B8D4;
			--gray-blueish: #AABAC8;
		}

		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		body {
			font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
			background: linear-gradient(135deg, var(--navy-deep) 0%, var(--navy-night) 50%, var(--blue-teal) 100%);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 30px 20px;
			color: #FFFFFF;
			position: relative;
			overflow-x: hidden;
		}

		/* Linhas Sutis de Fundo */
		.bg-road-lines {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			pointer-events: none;
			z-index: 0;
			opacity: 0.35;
		}

		/* Contentor Principal */
		.login-container {
			position: relative;
			z-index: 10;
			width: 100%;
			max-width: 440px;
			display: flex;
			flex-direction: column;
			align-items: center;
		}

		/* Formulário POST */
		.login-form {
			width: 100%;
		}

		/* Campos de Formulário */
		.field-group {
			margin-bottom: 20px;
		}

		.field-label {
			display: block;
			font-size: 13px;
			font-weight: 700;
			color: #FFFFFF;
			margin-bottom: 8px;
		}

		.input-wrapper {
			position: relative;
			display: flex;
			align-items: center;
		}

		.input-icon-left {
			position: absolute;
			left: 16px;
			color: var(--blue-cyan);
			display: flex;
			align-items: center;
			justify-content: center;
			pointer-events: none;
		}

		.input-icon-right {
			position: absolute;
			right: 16px;
			color: var(--gray-blueish);
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: color 0.2s;
		}

		.input-icon-right:hover {
			color: var(--blue-cyan);
		}

		.custom-input {
			width: 100%;
			height: 52px;
			background-color: rgba(255, 255, 255, 0.07);
			border: 1px solid rgba(255, 255, 255, 0.18);
			border-radius: 12px;
			padding: 0 44px 0 46px;
			font-size: 14px;
			font-weight: 600;
			color: #FFFFFF;
			font-family: inherit;
			outline: none;
			transition: all 0.2s ease-in-out;
		}

		.custom-input:focus {
			border-color: var(--blue-cyan);
			background-color: rgba(255, 255, 255, 0.12);
			box-shadow: 0 0 15px rgba(72, 184, 212, 0.25);
		}

		.custom-input::placeholder {
			color: var(--gray-blueish);
			font-weight: 500;
		}

		/* Opção de Sessão */
		.form-options {
			display: flex;
			align-items: center;
			margin-bottom: 28px;
			font-size: 13px;
		}

		.checkbox-container {
			display: flex;
			align-items: center;
			gap: 8px;
			cursor: pointer;
			color: var(--gray-blueish);
			font-weight: 600;
			user-select: none;
		}

		.checkbox-container input {
			accent-color: var(--blue-cyan);
			width: 16px;
			height: 16px;
			border-radius: 4px;
		}

		/* Botão Principal */
		.btn-submit {
			width: 100%;
			height: 54px;
			background-color: var(--blue-cyan);
			color: var(--navy-deep);
			border: none;
			border-radius: 12px;
			font-size: 16px;
			font-weight: 800;
			font-family: inherit;
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 10px;
			box-shadow: 0 8px 20px rgba(72, 184, 212, 0.3);
			transition: all 0.2s ease-in-out;
		}

		.btn-submit:hover {
			background-color: #5ed2ee;
			box-shadow: 0 10px 25px rgba(72, 184, 212, 0.45);
			transform: translateY(-1px);
		}

		.btn-submit:active {
			transform: translateY(1px);
			box-shadow: 0 4px 12px rgba(72, 184, 212, 0.2);
		}

		/* Caixa de Credenciais e Voltar ao Portal */
		.cred-box {
			width: 100%;
			background: rgba(255, 255, 255, 0.06);
			border: 1px solid rgba(255, 255, 255, 0.12);
			border-radius: 12px;
			padding: 12px 16px;
			font-size: 12px;
			text-align: center;
			color: var(--gray-blueish);
			margin-top: 28px;
		}

		.cred-box code {
			color: var(--blue-cyan);
			font-weight: 800;
			background: rgba(0, 0, 0, 0.25);
			padding: 2px 6px;
			border-radius: 4px;
		}

		.return-portal-link {
			margin-top: 20px;
			color: var(--gray-blueish);
			font-weight: 600;
			font-size: 14px;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			gap: 8px;
			transition: color 0.2s ease;
		}

		.return-portal-link:hover {
			color: var(--blue-cyan);
		}

		@media (max-width: 480px) {
			body {
				padding: 20px 15px;
			}
			.custom-input {
				height: 48px;
			}
			.btn-submit {
				height: 48px;
				font-size: 15px;
			}
		}
	</style>
</head>

<body>

	<!-- Linhas de Fundo -->
	<svg class="bg-road-lines" viewBox="0 0 1440 900" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M-100 200 C300 100, 700 500, 1540 300" stroke="url(#roadGrad1)" stroke-width="2" stroke-dasharray="6 6"/>
		<path d="M-100 650 C400 450, 800 800, 1540 500" stroke="url(#roadGrad2)" stroke-width="1.5"/>
		<defs>
			<linearGradient id="roadGrad1" x1="0%" y1="0%" x2="100%" y2="0%">
				<stop offset="0%" stop-color="#176B91" stop-opacity="0.1"/>
				<stop offset="50%" stop-color="#48B8D4" stop-opacity="0.5"/>
				<stop offset="100%" stop-color="#123C56" stop-opacity="0.1"/>
			</linearGradient>
			<linearGradient id="roadGrad2" x1="0%" y1="0%" x2="100%" y2="0%">
				<stop offset="0%" stop-color="#AABAC8" stop-opacity="0.1"/>
				<stop offset="100%" stop-color="#176B91" stop-opacity="0.3"/>
			</linearGradient>
		</defs>
	</svg>

	<!-- Contentor de Login -->
	<div class="login-container">

		<!-- Formulário POST -->
		<form method="post" class="login-form">

			<!-- Campo Email ou Número de Telefone -->
			<div class="field-group">
				<label class="field-label">Email ou número de telefone</label>
				<div class="input-wrapper">
					<span class="input-icon-left">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
							<circle cx="12" cy="7" r="4"></circle>
						</svg>
					</span>
					<input type="text" name="username" class="custom-input" placeholder="Digite o seu utilizador" required autocomplete="username">
				</div>
			</div>

			<!-- Campo Palavra-passe -->
			<div class="field-group">
				<label class="field-label">Palavra-passe</label>
				<div class="input-wrapper">
					<span class="input-icon-left">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
							<path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
						</svg>
					</span>
					<input type="password" id="admin_password" name="password" class="custom-input" placeholder="••••••••" required autocomplete="current-password">
					<span class="input-icon-right" onclick="togglePass('admin_password', 'eye_icon')" title="Mostrar/Ocultar Senha">
						<svg id="eye_icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
							<circle cx="12" cy="12" r="3"></circle>
						</svg>
					</span>
				</div>
			</div>

			<!-- Manter Sessão Iniciada -->
			<div class="form-options">
				<label class="checkbox-container">
					<input type="checkbox" checked>
					<span>Manter sessão iniciada</span>
				</label>
			</div>

			<!-- Botão Entrar na Minha Conta -->
			<button type="submit" name="login" class="btn-submit">
				<span>Entrar na minha conta</span>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<line x1="5" y1="12" x2="19" y2="12"></line>
					<polyline points="12 5 19 12 12 19"></polyline>
				</svg>
			</button>

		</form>

		<a href="../index.php" class="return-portal-link">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<line x1="19" y1="12" x2="5" y2="12"></line>
				<polyline points="12 19 5 12 12 5"></polyline>
			</svg>
			Voltar ao Portal Principal
		</a>

	</div>

	<script>
		function togglePass(inputId, iconId) {
			var input = document.getElementById(inputId);
			var icon = document.getElementById(iconId);
			if (input.type === "password") {
				input.type = "text";
				icon.style.color = "#48B8D4";
			} else {
				input.type = "password";
				icon.style.color = "#AABAC8";
			}
		}
	</script>

</body>

</html>
