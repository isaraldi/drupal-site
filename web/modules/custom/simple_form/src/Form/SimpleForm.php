<?php

namespace Drupal\simple_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Provides a Simple Form.
 */
class SimpleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simple_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $connection = Database::getConnection();
    $query = $connection->select('simple_form_data', 'sfd')
      ->fields('sfd', ['text_field', 'select_field'])
      ->orderBy('id', 'DESC')
      ->range(0, 1);
    $result = $query->execute()->fetchAssoc();

    $default_text = isset($result['text_field']) ? $result['text_field'] : '';
    $default_select = isset($result['select_field']) ? $result['select_field'] : '';

    $form['text_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Field'),
      '#default_value' => $default_text,
      '#required' => TRUE,
    ];

    $form['select_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Field'),
      '#options' => [
        'option_1' => $this->t('Option 1'),
        'option_2' => $this->t('Option 2'),
        'option_3' => $this->t('Option 3'),
      ],
      '#default_value' => $default_select,
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $text = $form_state->getValue('text_field');
    $select = $form_state->getValue('select_field');

    $connection = Database::getConnection();
    $connection->insert('simple_form_data')
      ->fields([
        'text_field' => $text,
        'select_field' => $select,
      ])
      ->execute();

    $this->messenger()->addMessage($this->t('The form has been submitted successfully.'));
  }
}
