<?php
class ControllerExtensionModulePtslider extends Controller
{
    public function index($setting) {
        $this->load->language('plaza/module/ptslider');

        $this->load->model('plaza/slider');
        $this->load->model('tool/image');

        $data = array();

        $data['ptsliders'] = array();

        $data['animate'] = 'animate-in';

        $results = array();

        if(isset($setting['slider'])) {
            $results = $this->model_plaza_slider->getSliderDescription($setting['slider']);
        }

        if($results) {
            $store_id  = $this->config->get('config_store_id');

            foreach ($results as $result) {
                $slider_store = array();
                if(isset($result['slider_store'])) {
                    $slider_store = explode(',',$result['slider_store']);
                }

                if(in_array($store_id, $slider_store)) {
                    $data['ptsliders'][] = array(
                        'title'         => $result['title'],
                        'sub_title'     => $result['sub_title'],
                        'description'   => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'link'          => $result['link'],
                        'type'          => $result['type'],
                        'image'         => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
                    );
                }
            }

            $data['slider'] = $this->model_plaza_slider->getSlider($result['ptslider_id']);
            $this->document->addScript('catalog/view/javascript/plaza/slider/jquery.nivo.slider.js');

        }

        return $this->load->view('plaza/module/ptslider', $data);
    }
}