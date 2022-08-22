<?php

namespace Drupal\slavnews_weather\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller that displays data form slavnews_weather db.
 */
class UserStatistics extends ControllerBase {

  /**
   * The Join function.
   */
  public function theJoin() {
    // @todo use dependency injection
    // phpcs:ignore
    $renderer = \Drupal::service('renderer');
    // phpcs:ignore
    $connection = \Drupal::database();
    $query = $connection->select('slavnews_weather', 'sw');
    $query->join('users_field_data', 'ufd', 'sw.userID = ufd.uid');
    $query
      ->fields('ufd', ['name'])
      ->fields('sw', ['userId'])
      ->fields('sw', ['city_country'])
      ->orderBy('ufd.created', 'DESC');
    $result = $query->execute()->fetchAll();
    $data = [];

    foreach ($result as $row) {
      if (empty($row->name)) {
        $row->name = 'Anonymous';
      }
      if ($row->name == 'admin') {
        $row->name = 'Admin';
      }
      $data[] = [
        'userID' => $row->userId,
        'name' => $row->name,
        'city_country' => $row->city_country,
      ];
    }
    $heading = ['userID', 'name', 'city_country'];

    $construct['table'] = [
      '#type' => 'table',
      '#header' => $heading,
      '#rows' => $data,
    ];
    return [
      $construct,
    ];
  }

}
