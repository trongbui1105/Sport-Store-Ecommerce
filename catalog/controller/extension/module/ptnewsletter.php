<?php
class ControllerExtensionModulePtnewsletter extends Controller
{
    public function index($setting) {
        $this->load->language('plaza/module/ptnewsletter');

        $data = array();

        if (isset($setting['popup']) && $setting['popup']) {
            $data['popup'] = true;
        } else {
            $data['popup'] = false;
        }

        $this->document->addScript('catalog/view/javascript/plaza/newsletter/mail.js');

        return $this->load->view('plaza/module/ptnewsletter', $data);
    }
}