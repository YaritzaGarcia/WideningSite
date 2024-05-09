<?php

namespace Drupal\mail_safety\Controller;

/**
 * Class MailSafetyController.
 *
 * @package Drupal\mail_safety\Controller
 */
class MailSafetyController {

  /**
   * Let's the user view the e-mail caught by Mail Safety.
   *
   * @return array
   *   A render array of the e-mail content.
   */
  public function view($mail_safety) {
    $system = $this->getMailSystem($mail_safety['mail']);
    $mail_safety['mail'] = $system->format($mail_safety['mail']);

    return [
      '#theme' => 'mail_safety_mail',
      '#mail' => $mail_safety['mail'],
    ];
  }

  /**
   * Let's the user view the details of an e-mail caught by Mail Safety.
   *
   * @return array
   *   A render array of the e-mail details.
   */
  public function details($mail_safety) {
    return [
      '#theme' => 'mail_safety_details',
      '#mail' => $mail_safety['mail'],
      '#details' => print_r($mail_safety['mail'], TRUE),
    ];
  }

  /**
   * Load one or more mails.
   *
   * @param optional $mail_id
   *   If mail_id is not given it will load all the mails.
   *
   * @return array|bool
   *   Returns an array of one ore more mails.
   */
  public static function load($mail_id = NULL) {
    $mails = [];

    $connection = \Drupal::database();
    $query = $connection->select('mail_safety_dashboard', 'msd');
    $query->fields('msd', ['mail_id', 'sent', 'mail']);

    // Add a condition for the mail id is given.
    if (!is_null($mail_id)) {
      $query->condition('mail_id', $mail_id);
    }

    $query->orderBy('sent', 'DESC');

    $result = $query->execute();

    while ($row = $result->fetchAssoc()) {
      $mails[$row['mail_id']] = [
        'mail' => unserialize($row['mail']),
        'sent' => $row['sent'],
        'mail_id' => $row['mail_id'],
      ];
    };

    // Let other modules respond before a mail is loaded.
    // E.g. attachments that were saved with the mail.
    foreach ($mails as $key => &$mail) {
      \Drupal::moduleHandler()->invokeAllWith('mail_safety_load', function (callable $hook, string $module) use (&$mail) {
        $mail['mail'] = call_user_func($hook, $mail['mail']);
      });
    }

    if (!is_null($mail_id) && !empty($mails[$mail_id])) {
      return $mails[$mail_id];
    }
    elseif (!empty($mails)) {
      return $mails;
    }

    return $mails;
  }

  /**
   * Delete a mail from the database.
   *
   * @param int $mail_id
   *   The mail id.
   */
  public static function delete($mail_id) {
    $connection = \Drupal::database();
    $connection->delete('mail_safety_dashboard')
      ->condition('mail_id', $mail_id)
      ->execute();
  }

  /**
   * Saves the mail to the dashboard.
   *
   * @param array $message
   *   The drupal message array.
   */
  public static function insert(array $message) {
    // Let other modules alter the message array before a mail is inserted.
    // E.g. save attachments that are sent with the mail.
    \Drupal::moduleHandler()->alter('mail_safety_pre_insert', $message);

    $mail = [
      'sent' => time(),
      'mail' => serialize($message),
    ];

    $connection = \Drupal::database();
    $connection->insert('mail_safety_dashboard')
      ->fields($mail)
      ->execute();
  }

  /**
   * Get the mail system of the given mail.
   *
   * @param array $mail
   *   The mail array.
   *
   * @return object
   *   The mail system object.
   */
  public static function getMailSystem(array $mail) {
    $mail_manager = \Drupal::service('plugin.manager.mail');
    return $mail_manager->getInstance(['module' => $mail['module'], 'key' => $mail['key']]);
  }

}
