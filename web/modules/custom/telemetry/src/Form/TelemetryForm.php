<?php

declare(strict_types=1);

namespace Drupal\telemetry\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\telemetry\Module as Module;
use Drupal\Core\Database\Database;

/**
 * Provides a Telemetry form.
 */
final class TelemetryForm extends FormBase {

  private $connection;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'telemetry_telemetry';
  }

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->connection = Database::getConnection();
  }

  /**
   * getFields
   *
   * Should return form fields processed, merged with the provided ones and
   * extracted taking the list of parameters.
   *
   * @param  array $form Form fields to append.
   * @return array Form fields processed.
   */
  private function getFields(array $form = []): array {
    return array_merge($form, Module::extractFields(
      Module::TABLE_FIELDS,
      ['message', 'message_type'],
      function ($translate) {
        return $this->t($translate);
      }));
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $query = $this->connection->select('telemetry', 't')
      ->fields('t', ['message', 'message_type'])
      ->orderBy('id', 'DESC')
      ->range(0, 5);
    $restoring = $query->execute()->fetchAll();
    $elements  = sizeof($restoring);
    foreach ($restoring as $result) {
      $this->messenger()->{$result->message_type}($result->message);
    }

    Module::telemetry('Status', $this, 'Accessing method *'.__METHOD__.'*.');

    $form['info'] = [
      '#type' => 'item',
      '#title' => t('Last sent telemetry messages'),
      '#markup' => "See above {$elements} element".($elements > 1 ? 's' : '').' sent previously.',
    ];

    $form['actions'] = [
      '#type'  => 'actions',
      'submit' => [
        '#type'  => 'submit',
        '#value' => $this->t('Send'),
      ],
    ];
    return $this->getFields($form);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    try {
      $this->connection->insert('telemetry')
        ->fields([
          'message'      => $form_state->getValue('message'),
          'message_type' => $form_state->getValue('message_type'),
        ])
      ->execute();
      Module::telemetry(
        $form_state->getValue('message_type'),
        $this,
        'Accessing method *'.__METHOD__.'*. ',
        $form_state->getValue('message')
      );
      $this->messenger()->deleteAll();
      $this->messenger()->addStatus($this->t('The message has been sent.'));
    } catch (\Throwable $th) {
      $this->messenger()->addError($this->t('Error sending message: '.$th->getMessage()));
    }
    $form_state->setRedirect('<front>');
  }

}
