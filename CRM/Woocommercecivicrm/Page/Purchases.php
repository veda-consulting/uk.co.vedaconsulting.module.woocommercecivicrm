<?php

require_once 'CRM/Core/Page.php';

class CRM_Woocommercecivicrm_Page_Purchases extends CRM_Core_Page {
  function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('Purchases'));

    // Example: Assign a variable for use in a template
    $uid = CRM_Utils_Request::retrieve('uid', 'Positive', $this);

    $orders = _woocommercecivicrm_customer_orders($uid);

    $this->assign('orders', $orders);

    //$country = new WC_Countries();
    //$countries = $country->get_countries();
    //print_r ($countries);exit;

    parent::run();
  }
}
