<?php

namespace Drupal\slavnews_fieldwidget\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * A widget bar.
 *
 * @FieldWidget(
 *   id = "slavnews_fieldwidget",
 *   label = @Translation("Hex Color Option"),
 *   field_types = {
 *     "string",
 *   }
 * )
 */
class SlavnewsFieldWidget extends StringTextfieldWidget {

  /**
   * Default placeholder settings.
   */
  public static function defaultSettings() {
    return [
      'size' => 7,
      'placeholder' => '',
      'default_value' => '#',
    ] + parent::defaultSettings();
  }

  /**
   * Widget field in which you can specify your default value.
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['placeholder'] = [
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('placeholder'),
      '#title' => $this->t('Your default Placeholder Value'),
      '#description' => $this->t('Enter HEX value as shown #373737'),
      '#required' => TRUE,
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = $element + [
      '#suffix' => '<div class="color_box"></div>',
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->value ?? NULL,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#attributes' => [
        'class' => ['render_input'],
        'color' => ['field_color'],
        'background-color' => ['field_background_color'],
      ],
      '#element_validate' => [$this, 'validate'],
      '#attached' => [
        'library' => ['slavnews_fieldwidget/preview'],
      ],
    ];
    return $element;
  }

  /**
   * Validate Hex value entered in taxonomy term field.
//   */
//  public function validate($element, FormStateInterface $form_state) {
//    $input = strtolower($element['value']);
//
//    if (!empty($input)) {
//      if (strlen($input) == 7) {
//        if (!preg_match('/^#([a-f0-9]{6})$/iD', $input)) {
//          $form_state->setError($element, $this->t('Please enter a valid HEX value!'));
//        }
//      }
//      else {
//        $form_state->setError($element, $this->t('Please enter a valid HEX value, it shout include one # and six numbers!'));
//      }
//    }
//    else {
//      $form_state->setError($element, $this->t('Please enter a valid HEX value, dont leave field empty!'));
//    }
//  }

}
