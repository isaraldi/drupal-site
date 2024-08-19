<?php

declare(strict_types=1);

namespace Drupal\telemetry\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\telemetry\Module as Module;

/**
 * Provides a Telemetry form.
 */
final class TelemetryForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'telemetry_telemetry';
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
    Module::telemetry('Status', $this, 'Accessing method *'.__METHOD__.'*.');
    return $this->getFields([
      'actions' => [
        '#type'  => 'actions',
        'submit' => [
          '#type'  => 'submit',
          '#value' => $this->t('Send'),
        ],
      ]
    ]);
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
    Module::telemetry(
      $form_state->getValue('message_type'),
      $this,
      'Accessing method *'.__METHOD__.'*. ',
      $form_state->getValue('message')
    );
    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
  }

}
