<?php
class ControllerExtensionModulePttweet extends Controller
{
    public function index() {
        $this->load->language('plaza/module/pttweet');
        $data['heading_title'] = $this->language->get('heading_title');

        $data['pttweet_user'] = $this->config->get('module_pttweet_id');
        $data['pttweet_limit'] = $this->config->get('module_pttweet_limit');
        $data['pttweet_consumer_key'] = $this->config->get('module_pttweet_consumer_key');
        $data['pttweet_consumer_secret'] = $this->config->get('module_pttweet_consumer_secret');
        $data['pttweet_access_token'] = $this->config->get('module_pttweet_access_token');
        $data['pttweet_access_token_secret'] = $this->config->get('module_pttweet_access_token_secret');

        $show_time = (int) $this->config->get('module_pttweet_show_time');

        if($show_time) {
            $data['show_time'] = true;
        } else {
            $data['show_time'] = false;
        }

        if (!empty($_SERVER['HTTPS'])) {
            // SSL connection
            $base_url = str_replace('http://', 'https://', $this->config->get('config_url'));
        } else {
            $base_url = $this->config->get('config_url');
        }

        $data['base_url'] = $base_url;

        return $this->load->view('plaza/module/pttweet', $data);
    }
}