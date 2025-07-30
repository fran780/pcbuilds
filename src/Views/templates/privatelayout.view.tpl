<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{SITE_TITLE}}</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{BASE_DIR}}/public/css/appstyle.css" />
  <link rel="stylesheet" href="{{BASE_DIR}}/public/css/paginas/footer.css" />
  <script src="https://kit.fontawesome.com/{{FONT_AWESOME_KIT}}.js" crossorigin="anonymous"></script>
  {{foreach SiteLinks}}
  <link rel="stylesheet" href="{{~BASE_DIR}}/{{this}}" />
  {{endfor SiteLinks}}
  {{foreach BeginScripts}}
  <script src="{{~BASE_DIR}}/{{this}}"></script>
  {{endfor BeginScripts}}
</head>

<body>
  <header>
    <input type="checkbox" class="menu_toggle" id="menu_toggle" />
    <label for="menu_toggle" class="menu_toggle_icon">
      <div class="hmb dgn pt-1"></div>
      <div class="hmb hrz"></div>
      <div class="hmb dgn pt-2"></div>
    </label>
    <h1>{{SITE_TITLE}}</h1>
    <nav id="menu">
      <ul>
        <li><a href="index.php?page={{PRIVATE_DEFAULT_CONTROLLER}}"><i class="fas fa-home"></i>&nbsp;Inicio</a></li>
        {{foreach NAVIGATION}}
        <li><a href="{{nav_url}}">{{nav_label}}</a></li>
        {{endfor NAVIGATION}}
      </ul>
    </nav>
    {{with login}}
    <a href="index.php?page=Carretilla" class="cart-link">
      <i class="fa-solid fa-cart-shopping">
        <span class="cart-items">{{if ~CART_ITEMS}}{{~CART_ITEMS}}{{endif ~CART_ITEMS}}</span>
      </i>
    </a>
    <span class="username">{{userName}} <a href="index.php?page=sec_logout"><i
          class="fas fa-sign-out-alt"></i></a></span>
    {{endwith login}}
  </header>
  <main>
    {{{page_content}}}
  </main>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-logo">
        <img src="public/imgs/hero/PC Builds Main.png" alt="PC Builds Logo">
      </div>

      <div class="footer-links">
        <h4>Páginas del sitio</h4>
        <ul>
          <li><a href="index.php">Inicio</a></li>
          <li><a href="index.php?page=Tienda">Tienda</a></li>
          <li><a href="index.php?page=Contactanos">Contáctanos</a></li>
        </ul>
      </div>

      <div class="footer-social">
        <h4>Síguenos en:</h4>
        <div class="social-icons">
          <a href="https://www.facebook.com/share/1F35m5WJ64/"><i class="fab fa-facebook-f"></i></a>
          <a href="https://x.com/pcbuildshn"><i class="fab fa-twitter"></i></a>
          <a href="https://www.instagram.com/pcbuildshonduras?igsh=MWx0OXZibWRoZGxnZQ=="><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </footer>
  {{foreach EndScripts}}
  <script src="{{~BASE_DIR}}/{{this}}"></script>
  {{endfor EndScripts}}
</body>

</html>