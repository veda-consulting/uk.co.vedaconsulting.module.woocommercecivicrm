<?php

require_once 'woocommercecivicrm.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function woocommercecivicrm_civicrm_config(&$config) {
  _woocommercecivicrm_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function woocommercecivicrm_civicrm_xmlMenu(&$files) {
  _woocommercecivicrm_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function woocommercecivicrm_civicrm_install() {
  _woocommercecivicrm_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function woocommercecivicrm_civicrm_uninstall() {
  _woocommercecivicrm_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function woocommercecivicrm_civicrm_enable() {
  _woocommercecivicrm_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function woocommercecivicrm_civicrm_disable() {
  _woocommercecivicrm_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function woocommercecivicrm_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _woocommercecivicrm_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function woocommercecivicrm_civicrm_managed(&$entities) {
  _woocommercecivicrm_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function woocommercecivicrm_civicrm_caseTypes(&$caseTypes) {
  _woocommercecivicrm_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function woocommercecivicrm_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _woocommercecivicrm_civix_civicrm_alterSettingsFolders($metaDataFolders);
}


/**
 * Implements hook_civicrm_tabs().
 */
function woocommercecivicrm_civicrm_tabs(&$tabs, $cid) {
  $uid = CRM_Core_BAO_UFMatch::getUFId($cid);
  if (empty($uid)) {
    return;
  }

  $orders = _woocommercecivicrm_customer_orders($uid);

  //$history = woocommerce_civicrm_contact_transaction($uid, FALSE);
  //$count = count($history['orders']['#rows']);

  $url = CRM_Utils_System::url( 'civicrm/woocommerce/view/purchases', "reset=1&uid=$uid&snippet=1&force=1");
  $tabs[] = array( 'id'    => 'woocommerce-orders',
                   'url'   => $url,
                   'title' => 'Orders',
                   'count' => count($orders),
                   'weight' => 99 );

}

function _woocommercecivicrm_customer_orders($uid) {
  $customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
    //'numberposts' => $order_count,
    'meta_key'    => '_customer_user',
    'meta_value'  => $uid,
    'post_type'   => 'shop_order',
    'post_status' => array_keys( wc_get_order_statuses() )
  ) ) );


  $site_url = get_site_url();

  $orders = array();
  foreach ( $customer_orders as $customer_order ) {
    $order = new WC_Order();
    $order->populate( $customer_order );

    $status = get_term_by( 'slug', $order->status, 'shop_order_status' );

    //$items = $order->get_items();
    //print_r ($items);

    $item_count = $order->get_item_count();
    $total = $order->get_total();

    $orders[$customer_order->ID]['order_number'] = $order->get_order_number();
    $orders[$customer_order->ID]['order_date'] = date( 'Y-m-d', strtotime( $order->order_date ));
    $orders[$customer_order->ID]['order_billing_name'] = $order->get_formatted_billing_full_name();
    $orders[$customer_order->ID]['order_shipping_name'] = $order->get_formatted_shipping_full_name();
    $orders[$customer_order->ID]['item_count'] = $item_count;
    $orders[$customer_order->ID]['order_total'] = $total;
    $orders[$customer_order->ID]['order_link'] = $site_url."/wp-admin/post.php?action=edit&post=".$order->get_order_number();
    //$orders['order_total'] = $order->get_order_number();
  }

  return $orders; 
}