<?php
class ControllerExtensionModulePtajaxlogin extends Controller
{
    public function index() {
        $this->load->language('plaza/module/ptajaxlogin');

        $enable_status = $this->config->get('module_ptajaxlogin_status');
        if($enable_status == '1') {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }

        $enable_redirect = $this->config->get('module_ptajaxlogin_redirect_status');
        if($enable_redirect == '1') {
            $data['redirect'] = true;
        } else {
            $data['redirect'] = false;
        }

        $store_id = $this->config->get('config_store_id');

        if (!empty($_SERVER['HTTPS'])) {
            // SSL connection
            $common_url = str_replace('http://', 'https://', $this->config->get('config_url'));
        } else {
            $common_url = $this->config->get('config_url');
        }

        if(isset($this->config->get('module_ptcontrolpanel_loader_img')[$store_id])) {
            $data['loader_img'] = $common_url . 'image/' . $this->config->get('module_ptcontrolpanel_loader_img')[$store_id];
        } else {
            $data['loader_img'] = $common_url . 'image/plaza/ajax-loader.gif';;
        }
        $this->document->addScript('catalog/view/javascript/plaza/ajaxlogin/ajaxlogin.js');

        $data['ajax_login_content'] = $this->load->controller('plaza/login');
        $data['ajax_register_content'] = $this->load->controller('plaza/register');
        $data['ajax_success_content'] = $this->load->controller('plaza/register/success');
        $data['ajax_logoutsuccess_content'] = $this->load->controller('plaza/login/logoutSuccess');

        return $this->load->view('plaza/module/ptajaxlogin', $data);
    }
}