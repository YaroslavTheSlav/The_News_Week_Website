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
        .focus(function () {
          // Selects closes div to field, in this instance it's my preview field.
          let previewElement = $(this).closest('div').parent().find(".color_box");
          // Field on keyup give preview background color.
          $(this).on('change paste keyup select DOMSubtreeModified', (function (){
            previewElement.css({
              backgroundColor: this.value,
            })
          }))
        })
    }
  }
})(jQuery);
