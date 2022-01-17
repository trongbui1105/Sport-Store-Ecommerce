<?php
class ControllerExtensionModulePtblog extends Controller
{
    public function index($setting) {
        $this->load->language('plaza/blog');

        $this->load->model('plaza/blog');
        $this->load->model('tool/image');

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
            $speed = 500;
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

        $data['posts'] = array();

        $filter_data = array(
            'start'              => 0,
            'limit'              => 10
        );

        $results = $this->model_plaza_blog->getPostsByList($filter_data, $setting['list']);

        foreach ($results as $result) {
            $data['posts'][] = array(
                'post_id'     => $result['post_id'],
                'name'        => $result['name'],
                'author'	  => $result['author'],
                'image'		  => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
                'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'intro_text'  => html_entity_decode($result['intro_text'], ENT_QUOTES, 'UTF-8'),
                'href'        => $this->url->link('plaza/blog/post', 'post_id=' . $result['post_id'])
            );
        }

        $data['slide'] = array(
            'auto' => $auto,
            'rows' => $rows,
            'navigation' => $navigation,
            'pagination' => $pagination,
            'speed' => $speed,
            'items' => $items
        );

        return $this->load->view('plaza/module/ptblog', $data);
    }
}