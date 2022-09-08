/* eslint-disable */
/**
* @file
* Attaches behaviors for Drupal's color field.
*/
(function ($) {
  Drupal.behaviors.color = {
    attach: function (context) {
      // Display the current value as background if the field has one.
      $(".render_input", context)
        .once('color')
        .each(function () {
          var $boxclass = $(".color_box");
          ($boxclass).css('background-color', $(this).val());
        })
        .focus(function () {

        })
    }
  }
})(jQuery);
// (function ($, Drupal) {
//
//   /**
//    * Enables box widget on color elements.
//    *
//    * @type {Drupal~behavior}
//    *
//    * @prop {Drupal~behaviorAttach} attach
//    *   Attaches a box widget to a color input element.
//    */
//   Drupal.behaviors.color_field = {
//     attach: function (context, settings) {
//
//       // once("color_box", "body", context).forEach(function () {
//       //   // Apply the myCustomBehaviour effect to the elements only once.
//       //   //opening menu
//       //   $(".color_box").setAttribute(function () {
//       //     $("color_box").setAttribute("width", "40px");
//       //   });
//       // });
//       // once("color_box", "body", context).forEach(function () {
//         // Apply the myCustomBehaviour effect to the elements only once.
//         //opening menu
//         // $(".color_box").setAttribute(function () {
//       console.log('111111111');
//
//
//       once("my_burger_menu", "body", context).forEach(function () {
//         // Apply the myCustomBehaviour effect to the elements only once.
//         //opening menu
//         console.log('qqqqqqqqqq');
//
//         $("body").click(function () {
//           $(".color_box").toggleClass("hex_box");
//           console.log('ddddddddddddddddddd');
//         });
//       });
//
//         // });
//       // });
//     }
//
// })(jQuery, Drupal, once);
// (function ($) {
//   Drupal.behaviors.color = {
//     attach: function (context) {
//       // Display the current value as background if the field has one.
//       $(".render_input", context)
//         .once('color')
//         .each(function () {
//           console.log('dddddddd');
//           var result_elem = $('<div class="hex_box"></div>').insertBefore(this);
//
//           $(result_elem).css('background-color', $(this).val());
//         })
//         .focus(function () {
//           let result_elem = $(this).closest('div').parent().find(".hex_box");
//
//           $(this).on('change paste keyup select DOMSubtreeModified', function () {
//             result_elem.css({
//               backgroundColor: this.value,
//             });
//           });
//         })
//     }
//   }
// })(jQuery);

// (function ($, Drupal, once) {
//   Drupal.behaviors.my_burger_menu = {
//
//     attach: function (context, settings) {
//
//       once("my_burger_menu", "body", context).forEach(function () {
//         // Apply the myCustomBehaviour effect to the elements only once.
//         //opening menu
//         $(".my_burger_menu").click(function () {
//           $("#page-wrapper").toggleClass("active_burger_menu_page_wrapper");
//           $("#page").toggleClass("active_burger_menu_page");
//           $(".menu--main-page-menu").toggleClass("active_burger_menu");
//         });
//         // closing menu
//         $(".my_burger_menu_closing_x").click(function() {
//           // $(".menu--main-page-menu").toggleClass("active_burger_menu");
//           $("#page-wrapper").toggleClass("active_burger_menu_page_wrapper");
//           $("#page").toggleClass("active_burger_menu_page");
//           $(".menu--main-page-menu").toggleClass("active_burger_menu");
//         });
//         //burger menu items open children on click
//         $(".menu--main-page-menu .menu__item--level-1").click(function () {
//           let $element = $(this);
//           console.log('me');
//           $(".menu--level-2", $element).toggleClass("display-menu-on-click-class");
//           $element.toggleClass("display-menu-on-click-class-arrows");
//         });
//
//       });
//
//     }
//   };
// })(jQuery, Drupal, once);
//


