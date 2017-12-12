<header class="banner">
  <div class="container">
    <a class="brand" href="<?= esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    <nav class="nav-primary nav-full">
      <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(['theme_location' => 'primary_navigation','menu_class' => 'nav']);
      endif;
      ?>
    </nav>
  <nav class="navbar navbar-toggleable-md navbar-light bg-faded nav-min">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <?php
      wp_nav_menu(['theme_location' => 'primary_navigation','menu_class' => 'nav navbar-nav ']);
      ?>
    </div>
  </nav>
  </div>
  <div class="test"></div>
</header>


