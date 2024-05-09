<?php

namespace Drupal\mail_safety\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\mail_safety\Controller\MailSafetyController;

/**
 * Class SendDefaultForm.
 *
 * @package Drupal\mail_safety\Form
 */
class SendDefaultForm extends ConfirmFormBase {

  /**
   * Store the mail safety array.
   *
   * @var array
   */
  protected $mailSafety = [];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mail_safety_send_default_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    $config = \Drupal::config('mail_safety.settings');

    return $this->t('Are you sure you want to send "@subject" to @to', [
      '@subject' => $this->mailSafety['mail']['subject'],
      '@to' => $config->get('default_mail_address'),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('mail_safety.dashboard');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Send default mail');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $mail_safety = NULL) {
    $config = \Drupal::config('mail_safety.settings');
    $this->mailSafety = $mail_safety;

    $form['to'] = [
      '#type' => 'textfield',
      '#title' => t('Send to another address'),
      '#description' => t('Only use this field if you want the e-mail to go to an address other than the default address.'),
      '#default_value' => $config->get('default_mail_address'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Resend the mail and bypass mail_alter by using the drupal_mail_system.
    $mail_array = $this->mailSafety['mail'];
    $mail_array['send'] = TRUE;

    // Let other modules respond before a mail is sent.
    // E.g. add attachments that were saved in the mail.
    \Drupal::moduleHandler()->invokeAllWith('mail_safety_pre_send', function (callable $hook, string $module) use (&$mail_array) {
      $mail_array = call_user_func($hook, $mail_array);
    });

    $mail_array['to'] = $form_state->getValue('to');

    // Get the mail manager and the mail system because we already
    // got the e-mail during the mail function and want to skip drupal
    // parsing the mail again.
    $system = MailSafetyController::getMailSystem($mail_array);
    $mail_array = $system->format($mail_array);
    $mail_array['result'] = $system->mail($mail_array);

    if ($mail_array['result']) {
      $this->messenger()->addStatus(t('Succesfully sent the message to @to', array('@to' => $mail_array['to'])));
    }
    else {
      $this->messenger()->addError(t('Failed to send the message to @to', array('@to' => $mail_array['to'])));
    }
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
