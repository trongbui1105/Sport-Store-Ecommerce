<?php
class ControllerExtensionModulePtstaticblock extends Controller
{
    public function index($setting) {
        if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
            if(isset($setting['show_title'])) {
                $data['show_title'] = (int) $setting['show_title'];
            } else {
                $data['show_title'] = 0;
            }
            $data['title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
            $data['block_content'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');

            return $this->load->view('plaza/module/ptstaticblock', $data);
        }
    }
}