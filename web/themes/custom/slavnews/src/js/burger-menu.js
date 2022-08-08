(function ($, Drupal, once) {
  Drupal.behaviors.my_burger_menu = {

    attach: function (context, settings) {

      once("my_burger_menu", "body", context).forEach(function () {
        // Apply the myCustomBehaviour effect to the elements only once.
        //opening menu
        $(".my_burger_menu").click(function () {
          $("#page-wrapper").toggleClass("active_burger_menu_page_wrapper");
          $("#page").toggleClass("active_burger_menu_page");
          $(".menu--main-page-menu").toggleClass("active_burger_menu");
        });
        // closing menu
        $(".my_burger_menu_closing_x").click(function() {
          // $(".menu--main-page-menu").toggleClass("active_burger_menu");
          $("#page-wrapper").toggleClass("active_burger_menu_page_wrapper");
          $("#page").toggleClass("active_burger_menu_page");
          $(".menu--main-page-menu").toggleClass("active_burger_menu");
        });
        //burger menu items open children on click
        $(".menu--main-page-menu .menu__item--level-1").click(function () {
          let $element = $(this);
          console.log('me');
          $(".menu--level-2", $element).toggleClass("display-menu-on-click-class");
          $element.toggleClass("display-menu-on-click-class-arrows");
        });

      });

    }
  };
})(jQuery, Drupal, once);




