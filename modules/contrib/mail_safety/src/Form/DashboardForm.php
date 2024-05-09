<?php

namespace Drupal\mail_safety\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DashboardForm.
 *
 * @package Drupal\mail_safety\Form
 */
class DashboardForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * DashboardForm constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(Connection $database, DateFormatter $date_formatter, ModuleHandlerInterface $module_handler) {
    $this->database = $database;
    $this->dateFormatter = $date_formatter;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('date.formatter'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    // Unique ID of the form.
    return 'mail_safety_dashboard_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $table_structure = [];
    // Create the headers.
    $table_structure['header'] = [
      ['data' => $this->t('Subject')],
      ['data' => $this->t('Date sent'), 'field' => 'sent', 'sort' => 'desc'],
      ['data' => $this->t('To')],
      ['data' => $this->t('CC')],
      ['data' => $this->t('Bcc')],
      ['data' => $this->t('Module')],
      ['data' => $this->t('Key')],
      ['data' => $this->t('Details')],
      ['data' => $this->t('Send to original')],
      ['data' => $this->t('Send to default mail')],
      ['data' => $this->t('Delete')],
    ];

    // Create the query.
    /** @var \Drupal\Core\Database\Query\SelectInterface $query */
    $query = $this->database->select('mail_safety_dashboard', 'msd')
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit(50)
      ->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->orderByHeader($table_structure['header'])
      ->fields('msd', [
        'mail_id',
        'sent',
        'mail',
      ]);

    $results = $query->execute();

    // Fill the rows for the table.
    $table_structure['rows'] = [];

    foreach ($results as $row) {
      $mail = unserialize($row->mail);

      // Build the links for the row.
      $view_url = Url::fromRoute('mail_safety.view', ['mail_safety' => $row->mail_id]);
      $details_url = Url::fromRoute('mail_safety.details', ['mail_safety' => $row->mail_id]);
      $send_original_url = Url::fromRoute('mail_safety.send_original', ['mail_safety' => $row->mail_id]);
      $send_default_url = Url::fromRoute('mail_safety.send_default', ['mail_safety' => $row->mail_id]);
      $delete_url = Url::fromRoute('mail_safety.delete', ['mail_safety' => $row->mail_id]);

      $cc = $mail['headers']['Cc'] ?? $this->t('none');
      $bcc = $mail['headers']['Bcc'] ?? $this->t('none');

      $table_structure['rows'][$row->mail_id] = [
        'data' => [
          Link::fromTextAndUrl($mail['subject'], $view_url),
          $this->dateFormatter->format((int) $row->sent, 'short'),
          $mail['to'],
          $cc,
          $bcc,
          $mail['module'],
          $mail['key'],
          Link::fromTextAndUrl($this->t('Details'), $details_url),
          Link::fromTextAndUrl($this->t('Send to original'), $send_original_url),
          Link::fromTextAndUrl($this->t('Send to default mail'), $send_default_url),
          Link::fromTextAndUrl($this->t('Delete'), $delete_url),
        ],
      ];
    }

    // Let other modules change the table structure to add or remove
    // information to be shown. E.g. attachments that need to be downloaded.
    $this->moduleHandler->alter('mail_safety_table_structure', $table_structure);

    $form['mails']['table'] = [
      '#theme' => 'table',
      '#header' => $table_structure['header'],
      '#rows' => $table_structure['rows'],
      '#sticky' => TRUE,
      '#empty' => $this->t('No mails found'),
    ];

    $form['mails']['pager'] = [
      '#type' => 'pager',
      '#tags' => [],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate submitted form data.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Handle submitted form data.
  }

}
