<?php

namespace Drupal\monitor_status_agent\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MonitorStatusAgentAdminForm.
 */
class MonitorStatusAgentAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'monitor_status_agent_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['monitor_status_agent.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('monitor_status_agent.settings');

    $form['agent'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Agent public key'),
    ];

    $priv_key = $config->get('monitor_status_agent_private_key');

    $form['agent']['monitor_status_agent_private_key'] = [
      '#type' => 'value',
      '#value' => $priv_key,
    ];

    if (!empty($priv_key)) {
      $res = openssl_pkey_get_private($priv_key);
      $pub_key = openssl_pkey_get_details($res)["key"];

      $form['agent']['monitor_status_agent_public_key'] = [
        '#markup' => $pub_key,
        '#prefix' => '<pre>',
        '#suffix' => '<pre>',
      ];
    }

    $form['dashboard'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Dashboard side'),
    ];

    $form['dashboard']['monitor_status_agent_dashboard_key'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Public key'),
      '#default_value' => $config->get('monitor_status_agent_dashboard_key'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $priv_key = $form_state->getValue('monitor_status_agent_private_key');
    if (empty($priv_key)) {
      $options = [
        'digest_alg' => 'sha512',
        'private_key_bits' => 4096,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
      ];
      $res = openssl_pkey_new($options);
      openssl_pkey_export($res, $priv_key);

      $form_state->setValueForElement($form['agent']['monitor_status_agent_private_key'], $priv_key);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('monitor_status_agent.settings');
    foreach ($form_state->getValues() as $key => $value) {
      if (strpos($key, 'monitor_status_agent_') === 0) {
        $config->set($key, $value);
      }
    }
    $config->save();
  }

}
