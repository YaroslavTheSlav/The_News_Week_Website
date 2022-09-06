<?php

namespace Drupal\slavnews_fieldwidget\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManager;

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
class SlavnewsFieldWidget extends WidgetBase {

  /**
   *
   */
//  public function fetchTaxonomyName() {
//    $terms = \Drupal::entityTypeManager()
//      ->getStorage('taxonomy_term')
//      ->loadTree($vid, 0, NULL, TRUE);
//    // $product_name_id = $product_name_reference->getValue()[0]['target_id'];
//    //   $product_name = \Drupal\taxonomy\Entity\Term::load($product_name_id)->label();
//
//    $product_name = \Drupal\taxonomy\Entity\Term::load($product_name_id);
//    // Vid or machine name.
//    $vid = $product_name->getVocabularyId();
//
////    $singleTerm = $terms->getValues()[]['values']['field_background_color']['x-default']['0'];
////    $singleTermTwo = $terms->getValues()[]['values']['field_color']['x-default']['0'];
//  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // @todo Implement formElement() method.
  }

}
