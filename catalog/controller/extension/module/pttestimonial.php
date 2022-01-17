<?php
class ControllerExtensionModulePttestimonial extends Controller
{
    public function index($setting) {
        $this->load->language('plaza/module/pttestimonial');

        $this->load->model('plaza/testimonial');
        $this->load->model('tool/image');

        $data = array();

        $testimonial_total = $this->model_plaza_testimonial->getTotalTestimonials();

        if(isset($setting['limit'])) {
            $limit = (int) $setting['limit'];
        } else {
            $limit = 10;
        }

        if($limit > (int) $testimonial_total) {
            $limit = (int) $testimonial_total;
        }

        if (isset($setting['rows'])) {
            $rows = $setting['rows'];
        } else {
            $rows = 1;
        }

        if (isset($setting['items'])) {
            $items = $setting['items'];
        } else {
            $items = 4;
        }

        if (isset($setting['speed'])) {
            $speed = $setting['speed'];
        } else {
            $speed = 3000;
        }

        if (isset($setting['auto']) && $setting['auto']) {
            $auto = true;
        } else {
            $auto = false;
        }

        if (isset($setting['navigation']) && $setting['navigation']) {
            $navigation = true;
        } else {
            $navigation = false;
        }

        if (isset($setting['pagination']) && $setting['pagination']) {
            $pagination = true;
        } else {
            $pagination = false;
        }

        $data['limit'] = $limit;
        $data['testimonials'] = array();

        $results = $this->model_plaza_testimonial->getTestimonials(0, $limit);
        if($results) {
            foreach($results as $result){
                if($result['image']) {
                    $timage = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
                } else {
                    $timage = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
                }
                $data['testimonials'][] = array(
                    'customer_name'    	=> $result['customer_name'],
                    'image'             => $timage,
                    'content'   		=> strip_tags(html_entity_decode($result['content'], ENT_QUOTES, 'UTF-8')),
                );
            }
        }

        $data['slide'] = array(
            'auto' => $auto,
            'rows' => $rows,
            'navigation' => $navigation,
            'pagination' => $pagination,
            'speed' => $speed,
            'items' => $items
        );

        return $this->load->view('plaza/module/pttestimonial', $data);
    }
}