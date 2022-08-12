<?php

namespace Drupal\slavnews_weather\Plugin\Form\WeatherForm;

// Use Drupal\Core\Form\FormBase;
// use Drupal\Core\Form\FormStateInterface;.
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
// phpcs:ignore
use Symfony\Component\HttpFoundation;

/**
 * My form.
 */
class WeatherForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'AdminWeather.settings';

  /**
   * Proclaim static config setting.
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
    // phpcs:ignore
    $config = $this->config(static::SETTINGS);
    $form['weatherapi_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Weather Api Key:'),
      '#default_value' => $config->get('weatherapi_token'),
      '#required' => TRUE,
    ];
    $form['admin_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter City And Country:'),
      '#markup' => $this->t('Allowed format "City, Country"'),
      '#default_value' => $config->get('admin_city'),
      '#required' => TRUE,
    ];
    $form['ipfind_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Token for IpFind: '),
      '#default_value' => $config->get('ipfind_token'),
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
   *
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
  private function checkCity($adminCity, $apiKey) {
    // (if city name contains only letters)
    if (!preg_match("/^[a-zA-Z ,]{1,32}$/", $adminCity)) {
      return FALSE;
    }
    // phpcs:ignore
    $client = \Drupal::httpClient();
    $method = 'GET';
    $response = $client->get(
      'http://api.weatherapi.com/v1/current.json?key=' . $apiKey . '&q=' . $adminCity . '&aqi=no',
      ['http_errors' => FALSE]
    );
    // Ip key validate.
    $data = json_decode($response->getBody($method), TRUE);
    // (if city name exists)
    if (isset($data['error'])) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validating ipFind token.
   */
  private function checkIp($ip_token) {
    // phpcs:ignore
    $client = \Drupal::httpClient();
    $method = 'GET';
    $response = $client->get('https://api.ipfind.com?ip=8.8.8.8&auth=' . $ip_token, ['http_errors' => FALSE]);
    // Ip key validate.
    $data = json_decode($response->getBody($method), TRUE);
    // (if variable exist)
    if (isset($data['error'])) {
      // (if error has error because of invalid key)
      return FALSE;
    }
    return TRUE;
  }
  /**
   * Validating form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Getting api key.
    $apiKey = $form_state->getValue('weatherapi_token');
    // Sending api key to validation function.
    if (!$this->checkApi($apiKey)) {
      $form_state->setErrorByName('weatherapi_token_error', $this->t('Please enter a valid Weather Api key!'));
    }
    // Sending city and api key to validate city.
    if (!$this->checkCity($form_state->getValue('admin_city'), $apiKey)) {
      $form_state->setErrorByName('admin_city_error', $this->t('Please enter a valid City And Or Country!'));
    }
    // Sending ip api key to validate if correct ip token used.
    if (!$this->checkIp($form_state->getValue('ipfind_token'))) {
      $form_state->setErrorByName('ipfind_token_error', $this->t('Please enter a valid Ip Api key!'));
    }
  }

  /**
   * At submit actions.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->config(static::SETTINGS)
      // weatherApi token saved.
      ->set(static::SETTINGS . '.weatherapi_token', $form_state->getValue('weatherapi_token'))
      // Admin city and country save.
      ->set(static::SETTINGS . '.admin_city', $form_state->getValue('admin_city'))
      // Ipfind token save.
      ->set(static::SETTINGS . '.ipfind_token', $form_state->getValue('ipfind_token'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
