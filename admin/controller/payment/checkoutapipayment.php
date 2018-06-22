<?php
class ControllerPaymentcheckoutapipayment extends Controller
{
    private $error = array();

    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "checkout_customer_cards` (
            `entity_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
            `customer_id` INT(11) NOT NULL COMMENT 'Customer ID from OPC',
            `card_id` VARCHAR(100) NOT NULL COMMENT 'Card ID from Checkout API',
            `card_number` VARCHAR(4) NOT NULL COMMENT 'Short Customer Credit Card Number',
            `card_type` VARCHAR(20) NOT NULL COMMENT 'Credit Card Type',
            `card_enabled` BIT NOT NULL DEFAULT 1 COMMENT 'Credit Card Enabled',
            `cko_customer_id` VARCHAR(100) NOT NULL COMMENT 'Customer ID from Checkout API',
          PRIMARY KEY (`entity_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;");
    }

    public function index()
    {
        $this->install();
        $this->language->load('payment/checkoutapipayment');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('checkoutapipayment', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title']                 = $this->language->get('heading_title');
        $this->data['text_checkoutapipayment_join']  = $this->language->get('text_checkoutapipayment_join');
        $this->data['text_checkoutapipayment']       = $this->language->get('text_checkoutapipayment');
        $this->data['text_payment']                  = $this->language->get('text_payment');
        $this->data['text_success']                  = $this->language->get('text_success');
        $this->data['text_page_title']               = $this->language->get('text_page_title');
        $this->data['text_status_on']                = $this->language->get('text_status_on');
        $this->data['text_status_off']               = $this->language->get('text_status_off');
        $this->data['text_mode_test']                = $this->language->get('text_mode_test');
        $this->data['text_mode_sandbox']             = $this->language->get('text_mode_sandbox');
        $this->data['text_mode_live']                = $this->language->get('text_mode_live');
        $this->data['text_auth_only']                = $this->language->get('text_auth_only');
        $this->data['text_auth_capture']             = $this->language->get('text_auth_capture');
        $this->data['text_pci_yes']                  = $this->language->get('text_pci_yes');
        $this->data['text_pci_no']                   = $this->language->get('text_pci_no');
        $this->data['text_lp_yes']                   = $this->language->get('text_lp_yes');
        $this->data['text_lp_no']                    = $this->language->get('text_lp_no');
        $this->data['text_gateway_timeout']          = $this->language->get('text_gateway_timeout');
        $this->data['text_symbol']                   = $this->language->get('text_symbol');
        $this->data['text_code']                     = $this->language->get('text_code');
        $this->data['text_checkout_js']              = $this->language->get('text_checkout_js');
        $this->data['text_frames_js']                = $this->language->get('text_frames_js');
        $this->data['text_theme_standard']           = $this->language->get('text_theme_standard');
        $this->data['text_theme_simple']             = $this->language->get('text_theme_simple');
        $this->data['text_is_3d_no']                 = $this->language->get('text_is_3d_no');
        $this->data['text_is_3d_yes']                = $this->language->get('text_is_3d_yes');
        $this->data['text_save_card_no']             = $this->language->get('text_save_card_no');
        $this->data['text_save_card_yes']            = $this->language->get('text_save_card_yes');

        $this->data['entry_test_mode']               = $this->language->get('entry_test_mode');
        $this->data['entry_secret_key']              = $this->language->get('entry_secret_key');
        $this->data['entry_public_key']              = $this->language->get('entry_public_key');
        $this->data['entry_localpayment_enable']     = $this->language->get('entry_localpayment_enable');
        $this->data['entry_payment_url']             = $this->language->get('entry_payment_url');
        $this->data['entry_pci_enable']              = $this->language->get('entry_pci_enable');
        $this->data['entry_payment_action']          = $this->language->get('entry_payment_action');
        $this->data['entry_autocapture_delay']       = $this->language->get('entry_autocapture_delay');
        $this->data['entry_card_type']               = $this->language->get('entry_card_type');
        $this->data['entry_gateway_timeout']         = $this->language->get('entry_gateway_timeout');
        $this->data['entry_successful_order_status'] = $this->language->get('entry_successful_order_status');
        $this->data['entry_failed_order_status']     = $this->language->get('entry_failed_order_status');
        $this->data['entry_sort_order']              = $this->language->get('entry_sort_order');
        $this->data['entry_status']                  = $this->language->get('entry_status');
        $this->data['entry_sort_order']              = $this->language->get('entry_sort_order');
        $this->data['entry_gateway_timeout']         = $this->language->get('entry_gateway_timeout');
        $this->data['entry_logo_url']                = $this->language->get('entry_logo_url');
        $this->data['entry_theme_color']             = $this->language->get('entry_theme_color');
        $this->data['entry_button_color']            = $this->language->get('entry_button_color');
        $this->data['entry_icon_color']              = $this->language->get('entry_icon_color');
        $this->data['entry_currency_format']         = $this->language->get('entry_currency_format');
        $this->data['entry_integration_type']        = $this->language->get('entry_integration_type');
        $this->data['entry_frames_theme']            = $this->language->get('entry_frames_theme');
        $this->data['entry_is_3d']                   = $this->language->get('entry_is_3d');
        $this->data['entry_save_card']               = $this->language->get('entry_save_card');

        $this->data['button_save']                   = $this->language->get('button_save');
        $this->data['button_cancel']                 = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['secret_key'])) {
            $this->data['error_secret_key'] = $this->error['secret_key'];
        } else {
            $this->data['error_secret_key'] = '';
        }

        if (isset($this->error['public_key'])) {
            $this->data['error_public_key'] = $this->error['public_key'];
        } else {
            $this->data['error_public_key'] = '';
        }


        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/checkoutapipayment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('payment/checkoutapipayment', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['test_mode'])) {
            $this->data['test_mode'] = $this->request->post['test_mode'];
        } else {
            $this->data['test_mode'] = $this->config->get('test_mode');
        }

        if (isset($this->request->post['secret_key'])) {
            $this->data['secret_key'] = $this->request->post['secret_key'];
        } else {
            $this->data['secret_key'] = $this->config->get('secret_key');
        }

        if (isset($this->request->post['public_key'])) {
            $this->data['public_key'] = $this->request->post['public_key'];
        } else {
            $this->data['public_key'] = $this->config->get('public_key');
        }

        if (isset($this->request->post['localpayment_enable'])) {
            $this->data['localpayment_enable'] = $this->request->post['localpayment_enable'];
        } else {
            $this->data['localpayment_enable'] = $this->config->get('localpayment_enable');
        }

        if (isset($this->request->post['integration_type'])) {
            $this->data['integration_type'] = $this->request->post['integration_type'];
        } else {
            $this->data['integration_type'] = $this->config->get('integration_type');
        }

        if (isset($this->request->post['payment_action'])) {
            $this->data['payment_action'] = $this->request->post['payment_action'];
        } else {
            $this->data['payment_action'] = $this->config->get('payment_action');
        }

        if (isset($this->request->post['autocapture_delay'])) {
            $this->data['autocapture_delay'] = $this->request->post['autocapture_delay'];
        } else {
            $this->data['autocapture_delay'] = $this->config->get('autocapture_delay');
        }

        if (isset($this->request->post['gateway_timeout'])) {
            $this->data['gateway_timeout'] = $this->request->post['gateway_timeout'];
        } else {
            $this->data['gateway_timeout'] = $this->config->get('gateway_timeout');
        }

        if (isset($this->request->post['logo_url'])) {
            $this->data['logo_url'] = $this->request->post['logo_url'];
        } else {
            $this->data['logo_url'] = $this->config->get('logo_url');
        }

        if (isset($this->request->post['theme_color'])) {
            $this->data['theme_color'] = $this->request->post['theme_color'];
        } else {
            $this->data['theme_color'] = $this->config->get('theme_color');
        }

        if (isset($this->request->post['button_color'])) {
            $this->data['button_color'] = $this->request->post['button_color'];
        } else {
            $this->data['button_color'] = $this->config->get('button_color');
        }

        if (isset($this->request->post['icon_color'])) {
            $this->data['icon_color'] = $this->request->post['icon_color'];
        } else {
            $this->data['icon_color'] = $this->config->get('icon_color');
        }

        if (isset($this->request->post['currency_format'])) {
            $this->data['currency_format'] = $this->request->post['currency_format'];
        } else {
            $this->data['currency_format'] = $this->config->get('currency_format');
        }

        if (isset($this->request->post['frames_theme'])) {
            $this->data['frames_theme'] = $this->request->post['frames_theme'];
        } else {
            $this->data['frames_theme'] = $this->config->get('frames_theme');
        }

        if (isset($this->request->post['is_3d'])) {
            $this->data['is_3d'] = $this->request->post['is_3d'];
        } else {
            $this->data['is_3d'] = $this->config->get('is_3d');
        }

        if (isset($this->request->post['save_card'])) {
            $this->data['save_card'] = $this->request->post['save_card'];
        } else {
            $this->data['save_card'] = $this->config->get('save_card');
        }

        if (isset($this->request->post['checkout_successful_order'])) {
            $this->data['checkout_successful_order'] = $this->request->post['checkout_successful_order'];
        } else {
            $this->data['checkout_successful_order'] = $this->config->get('checkout_successful_order');
        }

        if (isset($this->request->post['checkout_failed_order'])) {
            $this->data['checkout_failed_order'] = $this->request->post['checkout_failed_order'];
        } else {
            $this->data['checkout_failed_order'] = $this->config->get('checkout_failed_order');
        }

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['checkoutapipayment_status'])) {
            $this->data['checkoutapipayment_status'] = $this->request->post['checkoutapipayment_status'];
        } else {
            $this->data['checkoutapipayment_status'] = $this->config->get('checkoutapipayment_status');
        }

        if (isset($this->request->post['checkoutapipayment_sort_order'])) {
            $this->data['checkoutapipayment_sort_order'] = $this->request->post['checkoutapipayment_sort_order'];
        } else {
            $this->data['checkoutapipayment_sort_order'] = $this->config->get('checkoutapipayment_sort_order');
        }

        $this->data['callback'] = HTTP_CATALOG . 'index.php?route=payment/checkoutapipayment/callback';

        $this->template = 'payment/checkoutapi/checkoutapipayment.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/checkoutapipayment')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['secret_key']) {
            $this->error['secret_key'] = $this->language->get('error_secret_key');
        }

        if (!$this->request->post['public_key']) {
            $this->error['public_key'] = $this->language->get('error_public_key');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}