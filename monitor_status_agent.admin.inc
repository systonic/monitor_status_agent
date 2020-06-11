<?php

/**
 * @file
 * .
 */

/**
 * .
 */
function monitor_status_agent_settings() {
  $form = array();

  $form['agent'] = array(
    '#type' => 'fieldset',
    '#title' => t('Agent public key'),
  );

  $priv_key = variable_get('monitor_status_agent_private_key', '');

  $form['agent']['monitor_status_agent_private_key'] = array(
    '#type' => 'value',
    '#value' => $priv_key,
  );

  if (!empty($priv_key)) {
    $res = openssl_pkey_get_private($priv_key);
    $pub_key = openssl_pkey_get_details($res)["key"];

    $form['agent']['monitor_status_agent_public_key'] = array(
      '#markup' => $pub_key,
      '#prefix' => '<pre>',
      '#suffix' => '<pre>',
    );
  }

  $form['dashboard'] = array(
    '#type' => 'fieldset',
    '#title' => t('Dashboard side'),
  );

  $form['dashboard']['monitor_status_agent_dashboard_key'] = array(
    '#type' => 'textarea',
    '#title' => t('Public key'),
    '#default_value' => variable_get('monitor_status_agent_dashboard_key', ''),
  );

  $form['#validate'][] = 'monitor_status_agent_settings_validate';
  return system_settings_form($form);
}

/**
 * .
 */
function monitor_status_agent_settings_validate($form, &$form_state) {
  $priv_key = $form_state['values']['monitor_status_agent_private_key'];
  if (empty($priv_key)) {
    $config = array(
      'digest_alg' => 'sha512',
      'private_key_bits' => 4096,
      'private_key_type' => OPENSSL_KEYTYPE_RSA,
    );
    $res = openssl_pkey_new($config);
    openssl_pkey_export($res, $priv_key);

    form_set_value($form['agent']['monitor_status_agent_private_key'], $priv_key, $form_state);
  }
}
