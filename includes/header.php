<style>
/* Remove uppercase forced by theme and use exact first letter capital */
.custom-nav-link {
  color: #1a202c !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  line-height: 58px !important;
  padding: 0 16px !important;
  display: block !important;
  text-decoration: none !important;
  text-transform: none !important; /* Primeira letra maiúscula, resto minúscula */
  transition: color 0.2s ease-in-out !important;
}
.custom-nav-link:hover, .custom-nav-link:focus {
  color: #fa2823 !important;
  background-color: transparent !important;
}
.navbar-nav {
  margin: 0 !important;
}
.navbar-nav > li > a {
  text-transform: none !important;
}
.dropdown-menu {
  border-radius: 0 !important;
}
/* Estilo uniforme para botões de 36px de altura */
.header-btn-uniform {
  height: 36px !important;
  line-height: 36px !important;
  padding: 0 16px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  border-radius: 4px !important;
  text-transform: none !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-sizing: border-box !important;
}
/* Custom placeholder color for search box */
#header-search-form input::placeholder {
  color: #64748b !important;
  opacity: 0.8;
}

/* Tom de vermelho unificado (#fa2823) para todos os botões e formulários do site */
.btn,
.btn-primary,
.btn-danger,
input[type="submit"].btn,
button.btn,
.header-btn-uniform,
#submit {
  background-color: #fa2823 !important;
  border-color: #fa2823 !important;
  color: #ffffff !important;
}

.btn:hover,
.btn:focus,
.btn:active,
input[type="submit"].btn:hover,
input[type="submit"].btn:focus,
button.btn:hover,
button.btn:focus,
.header-btn-uniform:hover,
.header-btn-uniform:focus,
#submit:hover,
#submit:focus {
  background-color: #d91e19 !important;
  border-color: #d91e19 !important;
  color: #ffffff !important;
}

.modal-footer a {
  color: #fa2823;
}
.modal-footer a:hover {
  color: #d91e19;
}
</style>

<header style="background-color: #ffffff; border-bottom: 2px solid #e2e8f0; height: 58px; min-height: 58px; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
  <!-- Navigation -->
  <nav id="navigation_bar" class="navbar navbar-default" style="background-color: #ffffff; margin: 0; border: none; height: 58px; min-height: 58px;">
    <div class="container" style="height: 58px; display: flex; align-items: center; justify-content: space-between; width: 100%; padding-left: 15px; padding-right: 15px;">

      <!-- Esquerda: Logo -->
      <div class="navbar-header" style="display: flex; align-items: center; height: 58px;">
        <button id="menu_slide" data-target="#navigation" aria-expanded="false" data-toggle="collapse" class="navbar-toggle collapsed" type="button" style="background-color: #111; border-color: #111; margin-top: 10px; margin-bottom: 10px; border-radius: 0;">
          <span class="sr-only">Navegação</span>
          <span class="icon-bar" style="background-color: #fff;"></span>
          <span class="icon-bar" style="background-color: #fff;"></span>
          <span class="icon-bar" style="background-color: #fff;"></span>
        </button>
        <div class="logo" style="display: flex; align-items: center; height: 58px; padding: 0;">
          <a href="index.php" style="display: flex; align-items: center; height: 100%;">
            <img src="assets/images/logo.png" alt="Logo" style="max-height: 38px; width: auto; object-fit: contain;"/>
          </a>
        </div>
      </div>

      <!-- Centro/Direita: Links Principais (Primeira letra maiúscula e resto minúscula, alinhados mais à direita) -->
      <div class="collapse navbar-collapse" id="navigation" style="height: 58px !important; padding: 0; margin-left: auto; margin-right: 20px;">
        <ul class="nav navbar-nav" style="margin: 0; display: flex; align-items: center; height: 58px;">
          <li style="height: 58px;"><a href="index.php" class="custom-nav-link">Início</a></li>
          <li style="height: 58px;"><a href="page.php?type=aboutus" class="custom-nav-link">Sobre nós</a></li>
          <li style="height: 58px;"><a href="car-listing.php" class="custom-nav-link">Explorar</a></li>
          <li style="height: 58px;"><a href="contact-us.php" class="custom-nav-link">Contacte-nos</a></li>
        </ul>
      </div>

      <!-- Canto Direito Extremo: Pesquisa e Entrar/Cadastrar de Mesmo Tamanho (36px de Altura) -->
      <div class="header_wrap" style="display: flex; align-items: center; gap: 12px; height: 58px; padding: 0;">

        <!-- Barra de Pesquisa (36px de altura, tom elegante perfeitamente integrado) -->
        <div class="header_search_box" style="display: flex; align-items: center; height: 36px;">
          <form action="search.php" method="post" id="header-search-form" style="margin: 0; display: flex; align-items: center; background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; padding: 0 8px; height: 36px; box-sizing: border-box;">
            <input type="text" placeholder="Pesquisar..." name="searchdata" class="form-control" required="true" style="background: transparent; border: none; color: #1a202c; height: 36px; padding: 0 6px; font-size: 13px; outline: none; width: 140px; border-radius: 0; line-height: 36px; box-shadow: none;">
            <button type="submit" style="background: transparent; border: none; color: #fa2823; font-size: 14px; cursor: pointer; padding: 0 4px; height: 36px; display: flex; align-items: center;"><i class="fa fa-search" aria-hidden="true"></i></button>
          </form>
        </div>

        <!-- Botão Entrar / Cadastrar do Mesmo Tamanho (36px de altura) -->
        <div class="user_login" style="display: flex; align-items: center; height: 36px;">
          <?php if(isset($_SESSION['login']) && strlen($_SESSION['login']) > 0){
            $email = $_SESSION['login'];
            $sql = "SELECT FullName FROM tblusers WHERE EmailId=:email ";
            $query = $dbh->prepare($sql);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);
            $fullName = "A minha conta";
            if($query->rowCount() > 0) {
              foreach($results as $result) {
                $fullName = $result->FullName;
              }
            }
          ?>
            <div class="dropdown" style="height: 36px; display: flex; align-items: center;">
              <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #0f172a; font-weight: 800; font-size: 13.5px; display: flex; align-items: center; gap: 6px; text-decoration: none; height: 36px; padding: 0 14px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 6px; text-transform: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <i class="fa fa-user-circle" aria-hidden="true" style="font-size: 17px; color: #fa2823;"></i>
                <span style="color: #0f172a; font-weight: 800;"><?php echo htmlentities($fullName); ?></span>
                <i class="fa fa-angle-down" aria-hidden="true" style="color: #334155;"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-right" style="margin-top: 5px; border-radius: 6px; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px rgba(0,0,0,0.12); padding: 8px 0;">
                <li><a href="profile.php" style="text-transform: none; color: #0f172a; font-weight: 700; padding: 8px 18px;"><i class="fa fa-user" style="color: #fa2823; margin-right: 8px;"></i> Definições do Perfil</a></li>
                <li><a href="update-password.php" style="text-transform: none; color: #0f172a; font-weight: 700; padding: 8px 18px;"><i class="fa fa-key" style="color: #fa2823; margin-right: 8px;"></i> Atualizar Senha</a></li>
                <li><a href="my-booking.php" style="text-transform: none; color: #0f172a; font-weight: 700; padding: 8px 18px;"><i class="fa fa-car" style="color: #fa2823; margin-right: 8px;"></i> Minhas Reservas</a></li>
                <li><a href="post-testimonial.php" style="text-transform: none; color: #0f172a; font-weight: 700; padding: 8px 18px;"><i class="fa fa-pencil" style="color: #fa2823; margin-right: 8px;"></i> Publicar Depoimento</a></li>
                <li><a href="my-testimonials.php" style="text-transform: none; color: #0f172a; font-weight: 700; padding: 8px 18px;"><i class="fa fa-comments" style="color: #fa2823; margin-right: 8px;"></i> Meus Depoimentos</a></li>
                <li class="divider"></li>
                <li><a href="logout.php" style="color: #dc2626; font-weight: 800; text-transform: none; padding: 8px 18px;"><i class="fa fa-power-off" style="margin-right: 8px;"></i> Sair</a></li>
              </ul>
            </div>
          <?php } else { ?>
            <a href="#loginform" class="btn header-btn-uniform" data-toggle="modal" data-dismiss="modal" style="background-color: #fa2823; color: #ffffff; border: none; text-decoration: none;">Entrar / Cadastrar</a>
          <?php } ?>
        </div>

      </div>

    </div>
  </nav>
  <!-- Navigation end --> 
</header>
