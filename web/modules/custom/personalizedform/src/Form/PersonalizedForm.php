<?php

namespace Drupal\personalizedform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class PersonalizedForm extends FormBase {

  public function getFormId() {
    return 'personalizedform';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['text_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['select_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Options'),
      '#options' => [
        'option1' => $this->t('Option 1'),
        'option2' => $this->t('Option 2'),
        'option3' => $this->t('Option 3'),
      ],
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $connection = Database::getConnection();
    $connection->insert('personalizedform_data')
      ->fields([
        'text_field' => $form_state->getValue('text_field'),
        'select_field' => $form_state->getValue('select_field'),
      ])
      ->execute();

    \Drupal::messenger()->addMessage($this->t('Form data has been saved.'));
  }
}
