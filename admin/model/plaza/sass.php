<?php
class ModelPlazaSass extends Model
{
    public function compileData($data) {
        include_once(DIR_STORAGE . 'vendor/scss.inc.php');

        $this->load->model('setting/store');
        $this->load->model('setting/setting');

        $stores[] = array(
            'store_id' => 0,
            'name'     => $this->config->get('config_name') . $this->language->get('text_default')
        );

        foreach ($this->model_setting_store->getStores() as $store) {
            $stores[] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        foreach ($stores as $store) {
            // Detech Directory By Store
            $theme = $this->model_setting_setting->getSettingValue('config_theme', $store['store_id']);
            $theme_directory_code = "theme_" . $theme . '_directory';
            $directory = $this->model_setting_setting->getSettingValue($theme_directory_code, $store['store_id']);

            $file = DIR_CATALOG . 'view/theme/' . $directory . '/stylesheet/plaza/theme.css';
            $file_font = DIR_CATALOG . 'view/theme/' . $directory . '/stylesheet/plaza/theme_font.css';

            // Body
            if(!isset($data['module_ptcontrolpanel_body_font_disable'][$store['store_id']])) {
                $body_font_family = $data['module_ptcontrolpanel_body_font_family_name'][$store['store_id']];
                $body_font_cate = $data['module_ptcontrolpanel_body_font_family_cate'][$store['store_id']];
                $body_font_link = $data['module_ptcontrolpanel_body_font_family_link'][$store['store_id']];
            } else {
                $body_font_family = false;
                $body_font_cate = false;
                $body_font_link = false;
            }

            $body_font_size = $data['module_ptcontrolpanel_body_font_size'][$store['store_id']];
			if(!$body_font_size) $body_font_size = "14px";
            $body_font_weight = $data['module_ptcontrolpanel_body_font_weight'][$store['store_id']];
            $body_font_color = $data['module_ptcontrolpanel_body_color'][$store['store_id']];

            // Heading
            if(!isset($data['module_ptcontrolpanel_heading_font_disable'][$store['store_id']])) {
                $heading_font_family = $data['module_ptcontrolpanel_heading_font_family_name'][$store['store_id']];
                $heading_font_cate = $data['module_ptcontrolpanel_heading_font_family_cate'][$store['store_id']];
                $heading_font_link = $data['module_ptcontrolpanel_heading_font_family_link'][$store['store_id']];
            } else {
                $heading_font_family = false;
                $heading_font_cate = false;
                $heading_font_link = false;
            }

            $heading_font_weight = $data['module_ptcontrolpanel_heading_font_weight'][$store['store_id']];
            $heading_font_color = $data['module_ptcontrolpanel_heading_color'][$store['store_id']];

            // Link
            $link_color = $data['module_ptcontrolpanel_link_color'][$store['store_id']];
            $link_hover_color = $data['module_ptcontrolpanel_link_hover_color'][$store['store_id']];

            // Button
            $button_color = $data['module_ptcontrolpanel_button_color'][$store['store_id']];
            $button_hover_color = $data['module_ptcontrolpanel_button_hover_color'][$store['store_id']];
            $button_bg_color = $data['module_ptcontrolpanel_button_bg_color'][$store['store_id']];
            $button_bg_hover_color = $data['module_ptcontrolpanel_button_bg_hover_color'][$store['store_id']];

            // Custom
            $custom_css = $data['module_ptcontrolpanel_custom_css'][$store['store_id']];

            $css_line = "";
            $body_font_css_line = "";
            $heading_font_css_line = "";

            if($body_font_link && $body_font_cate && $body_font_family) {
                $css_line .= "@import url(". $body_font_link .");";
                $css_line .= "body { font-family: '". $body_font_family ."', ". $body_font_cate ."; }";
                $body_font_css_line = "--main-font: '". $body_font_family ."';";
            }

            if($heading_font_link && $heading_font_cate && $heading_font_family) {
                $css_line .= "@import url(". $heading_font_link .");";
                $css_line .= "h1, h2, h3, h4, h5, h6 { font-family: '". $heading_font_family ."', ". $heading_font_cate ."; }";
                $heading_font_css_line = "--heading-font: '". $heading_font_family ."';";
            }

            $css_line .= "body { font-size: ". $body_font_size ."; font-weight: ". $body_font_weight ."; color: #". $body_font_color ."; }";
            $css_line .= "h1, h2, h3, h4, h5, h6 { font-weight: ". $heading_font_weight ."; color: #". $heading_font_color ."; }";
            $css_line .= "a { color: #". $link_color ."; } a:hover { color: #". $link_hover_color ."; }";
            $css_line .= "button,.btn,.btn-primary { color: #". $button_color ."; background-color: #". $button_bg_color ."; border-color: #". $button_bg_color .";background-image: none;} button:hover,.btn:hover,.btn-primary:hover,.btn-primary.disabled,.btn-primary.disabled.active,.btn-primary.disabled.focus,.btn-primary.disabled:active,.btn-primary.disabled:focus,.btn-primary.disabled:hover,.btn-primary[disabled],.btn-primary[disabled].active,.btn-primary[disabled].focus,.btn-primary[disabled]:active,.btn-primary[disabled]:focus,.btn-primary[disabled]:hover,fieldset[disabled] .btn-primary,fieldset[disabled] .btn-primary.active,fieldset[disabled] .btn-primary.focus,fieldset[disabled] .btn-primary:active,fieldset[disabled] .btn-primary:focus,fieldset[disabled] .btn-primary:hover,.btn-primary.active.focus, .btn-primary.active:focus, .btn-primary.active:hover, .btn-primary:active.focus, .btn-primary:active:focus, .btn-primary:active:hover, .open>.dropdown-toggle.btn-primary.focus, .open>.dropdown-toggle.btn-primary:focus, .open>.dropdown-toggle.btn-primary:hover { color: #". $button_hover_color ."; background-color: #". $button_bg_hover_color ."; border-color: #". $button_bg_hover_color ."; }";
            $css_line .= $custom_css;

            $font_css_line = ":root { ". $body_font_css_line . $heading_font_css_line ." --main-color: #". $body_font_color .";--heading-color: #". $heading_font_color .";--heading-font-weight:". $heading_font_weight .";--link-hover-color: #". $link_hover_color .";--link-color: #". $link_color .";--button-color: #". $button_color .";--button-hover-color: #". $button_hover_color .";--button-bg-color: #". $button_bg_color .";--button-bg-hover-color: #". $button_bg_hover_color .";   }";

            $scss = new Scssc();
            $scss->setImportPaths(DIR_CATALOG . 'view/theme/' . $directory . '/stylesheet/sass/');
            $scss->setFormatter('scss_formatter_compressed');

            $output = $scss->compile($css_line);

            $handle = fopen($file, 'w');

            flock($handle, LOCK_EX);

            fwrite($handle, $output);

            fflush($handle);

            flock($handle, LOCK_UN);

            fclose($handle);

			$output = $scss->compile($font_css_line);

            $handle = fopen($file_font, 'w');

            flock($handle, LOCK_EX);

            fwrite($handle, $output);

            fflush($handle);

            flock($handle, LOCK_UN);

            fclose($handle);
        }
    }
}
