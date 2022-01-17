<?php
class ControllerExtensionModulePtsearch extends Controller
{
    private $error = array();

    public function install() {
        $config = array(
            'module_ptsearch_status' => 1,
            'module_ptsearch_ajax' => 1,
            'module_ptsearch_show_img' => 1,
            'module_ptsearch_show_price' => 1
        );
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_ptsearch', $config);
    }

    public function index() {
        $this->load->language('extension/module/ptsearch');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');
        $this->load->model('tool/image');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $post_data = $this->request->post;

            $this->model_setting_setting->editSetting('module_ptsearch', $post_data);

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
            'href' => $this->url->link('extension/module/ptsearch', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/ptsearch', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->request->post['module_ptsearch_status'])) {
            $data['module_ptsearch_status'] = $this->request->post['module_ptsearch_status'];
        } else {
            $data['module_ptsearch_status'] = $this->config->get('module_ptsearch_status');
        }

        if (isset($this->request->post['module_ptsearch_ajax'])) {
            $data['module_ptsearch_ajax'] = $this->request->post['module_ptsearch_ajax'];
        } else {
            $data['module_ptsearch_ajax'] = $this->config->get('module_ptsearch_ajax');
        }

        if (isset($this->request->post['module_ptsearch_show_img'])) {
            $data['module_ptsearch_show_img'] = $this->request->post['module_ptsearch_show_img'];
        } else {
            $data['module_ptsearch_show_img'] = $this->config->get('module_ptsearch_show_img');
        }

        if (isset($this->request->post['module_ptsearch_show_price'])) {
            $data['module_ptsearch_show_price'] = $this->request->post['module_ptsearch_show_price'];
        } else {
            $data['module_ptsearch_show_price'] = $this->config->get('module_ptsearch_show_price');
        }

        $this->document->addStyle('view/stylesheet/plaza/themeadmin.css');
        $this->document->addScript('view/javascript/plaza/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/plaza/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('plaza/module/ptsearch', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/ptsearch')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}