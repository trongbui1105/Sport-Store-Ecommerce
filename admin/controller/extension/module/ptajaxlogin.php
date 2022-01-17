<?php
class ControllerExtensionModulePtajaxlogin extends Controller
{
    private $error = array();

    public function install() {
        $config = array(
            'module_ptajaxlogin_status' => 1,
            'module_ptajaxlogin_loader_img' => 'plaza/ajax-loader.gif'
        );
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_ptajaxlogin', $config);
    }

    public function index() {
        $this->load->language('extension/module/ptajaxlogin');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');
        $this->load->model('tool/image');
        $this->load->model('setting/module');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_ptajaxlogin', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token'], true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/ptajaxlogin', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/ptajaxlogin', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->request->post['module_ptajaxlogin_status'])) {
            $data['module_ptajaxlogin_status'] = $this->request->post['module_ptajaxlogin_status'];
        } else {
            $data['module_ptajaxlogin_status'] = $this->config->get('module_ptajaxlogin_status');
        }

        if (isset($this->request->post['module_ptajaxlogin_redirect_status'])) {
            $data['module_ptajaxlogin_redirect_status'] = $this->request->post['module_ptajaxlogin_redirect_status'];
        } else {
            $data['module_ptajaxlogin_redirect_status'] = $this->config->get('module_ptajaxlogin_redirect_status');
        }

        $this->document->addStyle('view/stylesheet/plaza/themeadmin.css');
        $this->document->addScript('view/javascript/plaza/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/plaza/switch-toggle/css/bootstrap-toggle.min.css');
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('plaza/module/ptajaxlogin', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/ptajaxlogin')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}