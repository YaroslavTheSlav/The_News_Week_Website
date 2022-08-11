<?php

namespace Drupal\slavnews_weather\Plugin\Form\WeatherForm;

// Use Drupal\Core\Form\FormBase;
// use Drupal\Core\Form\FormStateInterface;.
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
// phpcs:ignore
use Symfony\Component\HttpFoundation;

/**
 *
 */
class WeatherForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'AdminWeather.settings';

  /**
   * {@inheritdoc}.
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_weather_edit_form';
  }

  /**
   * Building Form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['admin_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Weather Api Key:'),
      '#required' => TRUE,
    ];
    $form['admin_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter City And Country:'),
      '#markup' => $this->t('Allowed format "City, Country"'),
      '#default_value' => $this->t('Lutsk, Ukraine'),
      '#required' => TRUE,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('SUBMIT'),
      '#button_type' => 'primary',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Sub Validating Api Key.
   */

  /**
   * @todo use dependency injection instead of phpcs:ignore
   */
  private function checkApi($apiKey) {
    // phpcs:ignore
    $client = \Drupal::httpClient();
    $method = 'GET';
    $response = $client->get('http://api.weatherapi.com/v1/current.json?key=' . $apiKey . '&q=Lutsk&q=%20Ukraine%20&aqi=no', ['http_errors' => FALSE]);
    // Ip key validate.
    $data = json_decode($response->getBody($method), TRUE);
    // (if variable exist)
    if (isset($data['error'])) {
      // (if error has error because of invalid key)
      if ($data['error']['message'] == 'API key is invalid.') {
        return FALSE;
      }
      return TRUE;
    }
    return TRUE;
  }

  /**
   * Sub Validate City and country.
   */
  private function checkCity($adminCity) {
    if (!preg_match("/^[a-zA-Z ,]{1,32}$/", $adminCity)) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validating form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // $apiKey = $form_state->getValue('admin_api_key');
    // sending api key to validation function
    if (!$this->checkApi($form_state->getValue('admin_api_key'))) {
      $form_state->setErrorByName('admin_api_key_error', $this->t('Please enter a valid Api key!'));
    }
    if (!$this->checkCity($form_state->getValue('admin_city'))) {
      $form_state->setErrorByName('admin_city_error', $this->t('Please enter a valid City And Or Country!'));
    }
  }

  /**
   * At submit actions.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->config(static::SETTINGS)
      // Set the submitted configuration setting.
      ->set('admin_api_key', $form_state->getValue('admin_api_key'))
      // You can set multiple configurations at once by making
      // multiple calls to set().
      ->set('admin_city', $form_state->getValue('admin_city'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
