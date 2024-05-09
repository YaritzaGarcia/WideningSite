<?php

namespace Drupal\mail_safety\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\mail_safety\Controller\MailSafetyController;

/**
 * Class ClearForm.
 *
 * @package Drupal\mail_safety\Form
 */
class ClearForm extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mail_safety_clear_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to clear all mail?');
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
    return $this->t('Clear mail');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Delete all emails stored by this module.
    $mail_ids = array_keys(MailSafetyController::load());
    foreach ($mail_ids as $mail_id) {
      MailSafetyController::delete($mail_id);
    }

    // Return the user to the dashboard.
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
