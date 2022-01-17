<?php
class ControllerExtensionModulePtinstagram extends Controller
{
    public function index($setting) {
        $this->load->language('plaza/module/ptinstagram');

        $data = array();

        $data['text_copyright'] = sprintf($this->language->get('text_copyright'), $this->config->get('config_name'));

        if (empty($setting['limit'])) {
            $setting['limit'] = 10;
        }

        $file = "https://api.instagram.com/v1/users/" . $setting['user_id'] . "/media/recent/?access_token=" . $setting['access_token'];

        $content = @file_get_contents($file);

        $data['error_connect']	= true;

        if($content === false){
            $data['error_connect']	= false;
        } else {
            $instagram_json_data = json_decode($content,true);
            $instagram_arrays = $instagram_json_data['data'];

            $data['username'] = $instagram_arrays[0]['user']['username'];

            $data['instagrams'] = array();

            foreach($instagram_arrays as $key => $instagram_array) {
                $instagrams[] = array(
                    'likes' => $instagram_array['likes']['count'],
                    'small_image' => $instagram_array['images']['thumbnail']['url'], // 150x150
                    'medium_image' => $instagram_array['images']['low_resolution']['url'], // 320x320
                    'image' => $instagram_array['images']['standard_resolution']['url'], // 640x640
                    'link'  => $instagram_array['link'],
                    'created_time' => date('m/d/Y', $instagram_array['created_time']),
                    'comment' => $instagram_array['comments']['count'],
                    'caption' => $instagram_array['caption']['text'],
                );
                if ($key == $setting['limit'] - 1) break;
            };

            $data['instagrams'] = $instagrams;

            $data['slider'] = array(
                'items' => $setting['item'],
                'auto' => $setting['auto'],
                'navigation' => $setting['navigation'],
                'pagination' => $setting['pagination'],
                'speed' => $setting['speed'],
                'rows' => $setting['rows'],
                'view_mode' => $setting['view_mode']
            );
        }

        // $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
        // $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');

        return $this->load->view('plaza/module/ptinstagram', $data);
    }
}