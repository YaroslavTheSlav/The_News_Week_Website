<?php

namespace Drupal\slavnews_weather\Plugin\Block\WeatherBlockFolder;

use Drupal\Core\Block\BlockBase;
// phpcs:ignore
use Symfony\Component\HttpFoundation;

/**
 * Provides a 'Weather' Block.
 *
 * @Block(
 *   id = "weather_block",
 *   admin_label = @Translation("Weather Block"),
 *   category = @Translation("My Weather"),
 * )
 */
class WeatherBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // (receive weather api)
    $method = 'GET';
    // (receive client ip)
    // phpcs:ignore
    $ip = \Drupal::request()->getClientIp();
    $ip_query = 'auto:' . $ip;
    // (receive weather api)
    // phpcs:ignore
    $client = \Drupal::httpClient();
    // (if it's our local ip, then we will hardcode location to Lutsk)
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
      // (reverting to default if ip is local variable)
      $ip_query = 'Lutsk';
    }
    // (receive weather api link on user ip location)
    $weather_url = 'http://api.weatherapi.com/v1/current.json?key=774df53d912143dd9b5124404221008&q=' . $ip_query . '%20Ukraine%20&aqi=no';
    // (pulling json data)
    $response = $client->get($weather_url);
    $data = json_decode($response->getBody($method), TRUE);
    // (outputting all data)
    $my_block_content = [
      '#theme' => 'hello_block',
      '#markup' => $data['current']['temp_c'] . ' ' . $data['location']['name'],
    ];
    return $my_block_content;
  }

}
