<?php

namespace Drupal\zoocha_simple_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Html;

/**
 * Class ZoochaSimpleForm.
 *
 * Provides a simple form with a text field and a select list.
 */
class ZoochaSimpleForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a ZoochaSimpleForm object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   */
  public function __construct(Connection $database, LoggerInterface $logger) {
    $this->database = $database;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   *
   * Creates an instance of the form using dependency injection.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('logger.factory')->get('zoocha_simple_form')
    );
  }

  /**
   * {@inheritdoc}
   *
   * Returns a unique string identifying the form.
   */
  public function getFormId() {
    return 'zoocha_simple_form';
  }

  /**
   * {@inheritdoc}
   *
   * Builds the form elements.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Add a text field to the form.
    $form['text_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Field'), // Title of the text field.
      '#required' => TRUE, // Marks the field as required.
      '#maxlength' => 255, // Limit the maximum length for security.
    ];

    // Add a select list to the form.
    $form['select_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Field'), // Title of the select field.
      '#options' => [
        'option_1' => $this->t('Option 1'),
        'option_2' => $this->t('Option 2'),
      ],
      '#required' => TRUE, // Marks the field as required.
    ];

    // Add a submit button to the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'), // Text on the submit button.
    ];

    return $form; // Returns the form array.
  }

  /**
   * {@inheritdoc}
   *
   * Handles form submission and stores the submitted data in the database.
   *
   * @param array &$form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve and sanitize values from the form submission.
    $text = Html::escape($form_state->getValue('text_field'));
    $select = Html::escape($form_state->getValue('select_field'));

    // Attempt to insert the submitted data into the custom database table.
    try {
      // Use Drupal's Database API to perform a safe database insert.
      $this->database->insert('zoocha_simple_form_data')
        ->fields([
          'text_field' => $text,
          'select_field' => $select,
        ])
        ->execute();

      // Display a success message to the user.
      $this->messenger()->addMessage($this->t('Form submitted successfully.'));
    }
    catch (\Exception $e) {
      // Log the exception with a descriptive message.
      $this->logger->error('Database error: @message', ['@message' => $e->getMessage()]);
      // Display a generic error message to the user.
      $this->messenger()->addError($this->t('An error occurred while submitting the form. Please try again later.'));
    }
  }

}
