<?php
class ControllerExtensionModulePtcontrolpanel extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('plaza/adminmenu');
        $this->load->language('extension/module/ptcontrolpanel');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');
        $this->load->model('plaza/sass');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_ptcontrolpanel', $this->request->post);
            $this->model_plaza_sass->compileData($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/ptcontrolpanel', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->config->get('config_name') . $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        $this->load->model('catalog/option');

        $data['options'] = array();

        $results = $this->model_catalog_option->getOptions();

        foreach ($results as $result) {
            $data['options'][] = array(
                'option_id'  => $result['option_id'],
                'type'       => $result['type'],
                'name'       => $result['name']
            );
        }

        if(isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = false;
        }

        if(isset($this->session->data['error_load_file'])) {
            $data['error_load_file'] = $this->session->data['error_load_file'];

            unset($this->session->data['error_load_file']);
        } else {
            $data['error_load_file'] = false;
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
            'href' => $this->url->link('extension/module/ptcontrolpanel', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['database'] = array(
            DIR_APPLICATION . '../plazadata/plaza_db1.sql' => 'Layout 1',
            DIR_APPLICATION . '../plazadata/plaza_db2.sql' => 'Layout 2',
            DIR_APPLICATION . '../plazadata/plaza_db3.sql' => 'Layout 3',
            DIR_APPLICATION . '../plazadata/plaza_db4.sql' => 'Layout 4'
        );

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyAov68H0SNcVzNpBfx40cOrObR8ZvV_cps';

        $fonts_file = file_get_contents($url, false, stream_context_create($arrContextOptions));

        $google_fonts = json_decode($fonts_file, true);

        $fonts = $google_fonts['items'];

        foreach ($fonts as $key => $font) {
            $font_family_val = str_replace(' ', '+', $font['family']);
            $variants = implode(',', $font['variants']);
            $subsets = implode(',', $font['subsets']);
            $data['fonts'][] = array(
                'id'    => $key,
                'family' => $font['family'],
                'family_val' => $font_family_val,
                'variants' => $variants,
                'subsets' => $subsets,
                'category' => $font['category']
            );
        }

        $data['user_token'] = $this->session->data['user_token'];

        $data['action'] = $this->url->link('extension/module/ptcontrolpanel', 'user_token=' . $this->session->data['user_token'], true);
        $data['action_import'] = $this->url->link('extension/module/ptcontrolpanel/import', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token'], true);

        /* General */
        if (isset($this->request->post['module_ptcontrolpanel_sticky_header'])) {
            $data['module_ptcontrolpanel_sticky_header'] = $this->request->post['module_ptcontrolpanel_sticky_header'];
        } else {
            $data['module_ptcontrolpanel_sticky_header'] = $this->config->get('module_ptcontrolpanel_sticky_header');
        }

        if (isset($this->request->post['module_ptcontrolpanel_scroll_top'])) {
            $data['module_ptcontrolpanel_scroll_top'] = $this->request->post['module_ptcontrolpanel_scroll_top'];
        } else {
            $data['module_ptcontrolpanel_scroll_top'] = $this->config->get('module_ptcontrolpanel_scroll_top');
        }

        if (isset($this->request->post['module_ptcontrolpanel_lazy_load'])) {
            $data['module_ptcontrolpanel_lazy_load'] = $this->request->post['module_ptcontrolpanel_lazy_load'];
        } else {
            $data['module_ptcontrolpanel_lazy_load'] = $this->config->get('module_ptcontrolpanel_lazy_load');
        }

        if (isset($this->request->post['module_ptcontrolpanel_header_layout'])) {
            $data['module_ptcontrolpanel_header_layout'] = $this->request->post['module_ptcontrolpanel_header_layout'];
        } else {
            $data['module_ptcontrolpanel_header_layout'] = $this->config->get('module_ptcontrolpanel_header_layout');
        }

        if (isset($this->request->post['module_ptcontrolpanel_responsive_type'])) {
            $data['module_ptcontrolpanel_responsive_type'] = $this->request->post['module_ptcontrolpanel_responsive_type'];
        } else {
            $data['module_ptcontrolpanel_responsive_type'] = $this->config->get('module_ptcontrolpanel_responsive_type');
        }

        $this->load->model('tool/image');

        foreach ($data['stores'] as $store) {
            if (isset($this->request->post['module_ptcontrolpanel_loader_img'][$store['store_id']]) && is_file(DIR_IMAGE . $this->request->post['module_ptcontrolpanel_loader_img'][$store['store_id']])) {
                $data['thumb'][$store['store_id']] = $this->model_tool_image->resize($this->request->post['module_ptcontrolpanel_loader_img'][$store['store_id']], 50, 50);
                $data['module_ptcontrolpanel_loader_img'] = $this->request->post['module_ptcontrolpanel_loader_img'];
            } elseif (is_file(DIR_IMAGE . $this->config->get('module_ptcontrolpanel_loader_img')[$store['store_id']])) {
                $data['thumb'][$store['store_id']] = $this->model_tool_image->resize($this->config->get('module_ptcontrolpanel_loader_img')[$store['store_id']], 50, 50);
                $data['module_ptcontrolpanel_loader_img'] = $this->config->get('module_ptcontrolpanel_loader_img');
            } else {
                $data['thumb'][$store['store_id']] = $this->model_tool_image->resize('no_image.png', 50, 50);
            }
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 50, 50);

        /* Font & CSS */
        /* Body */
        if (isset($this->request->post['module_ptcontrolpanel_body_font_family_id'])) {
            $data['module_ptcontrolpanel_body_font_family_id'] = $this->request->post['module_ptcontrolpanel_body_font_family_id'];
        } else {
            $data['module_ptcontrolpanel_body_font_family_id'] = $this->config->get('module_ptcontrolpanel_body_font_family_id');
        }

        if (isset($this->request->post['module_ptcontrolpanel_body_font_disable'])) {
            $data['module_ptcontrolpanel_body_font_disable'] = $this->request->post['module_ptcontrolpanel_body_font_disable'];
        } else {
            $data['module_ptcontrolpanel_body_font_disable'] = $this->config->get('module_ptcontrolpanel_body_font_disable');
        }

        if (isset($this->request->post['module_ptcontrolpanel_body_font_family_name'])) {
            $data['module_ptcontrolpanel_body_font_family_name'] = $this->request->post['module_ptcontrolpanel_body_font_family_name'];
        } else {
            $data['module_ptcontrolpanel_body_font_family_name'] = $this->config->get('module_ptcontrolpanel_body_font_family_name');
        }

        if (isset($this->request->post['module_ptcontrolpanel_body_font_family_cate'])) {
            $data['module_ptcontrolpanel_body_font_family_cate'] = $this->request->post['module_ptcontrolpanel_body_font_family_cate'];
        } else {
            $data['module_ptcontrolpanel_body_font_family_cate'] = $this->config->get('module_ptcontrolpanel_body_font_family_cate');
        }

        if (isset($this->request->post['module_ptcontrolpanel_body_font_family_link'])) {
            $data['module_ptcontrolpanel_body_font_family_link'] = $this->request->post['module_ptcontrolpanel_body_font_family_link'];
        } else {
            $data['module_ptcontrolpanel_body_font_family_link'] = $this->config->get('module_ptcontrolpanel_body_font_family_link');
        }

        if (isset($this->request->post['module_ptcontrolpanel_body_font_size'])) {
            $data['module_ptcontrolpanel_body_font_size'] = $this->request->post['module_ptcontrolpanel_body_font_size'];
        } else {
            $data['module_ptcontrolpanel_body_font_size'] = $this->config->get('module_ptcontrolpanel_body_font_size');
        }

        if (isset($this->request->post['module_ptcontrolpanel_body_font_weight'])) {
            $data['module_ptcontrolpanel_body_font_weight'] = $this->request->post['module_ptcontrolpanel_body_font_weight'];
        } else {
            $data['module_ptcontrolpanel_body_font_weight'] = $this->config->get('module_ptcontrolpanel_body_font_weight');
        }

        if (isset($this->request->post['module_ptcontrolpanel_body_color'])) {
            $data['module_ptcontrolpanel_body_color'] = $this->request->post['module_ptcontrolpanel_body_color'];
        } else {
            $data['module_ptcontrolpanel_body_color'] = $this->config->get('module_ptcontrolpanel_body_color');
        }

        /* Heading */
        if (isset($this->request->post['module_ptcontrolpanel_heading_font_family_id'])) {
            $data['module_ptcontrolpanel_heading_font_family_id'] = $this->request->post['module_ptcontrolpanel_heading_font_family_id'];
        } else {
            $data['module_ptcontrolpanel_heading_font_family_id'] = $this->config->get('module_ptcontrolpanel_heading_font_family_id');
        }

        if (isset($this->request->post['module_ptcontrolpanel_heading_font_disable'])) {
            $data['module_ptcontrolpanel_heading_font_disable'] = $this->request->post['module_ptcontrolpanel_heading_font_disable'];
        } else {
            $data['module_ptcontrolpanel_heading_font_disable'] = $this->config->get('module_ptcontrolpanel_heading_font_disable');
        }

        if (isset($this->request->post['module_ptcontrolpanel_heading_font_family_name'])) {
            $data['module_ptcontrolpanel_heading_font_family_name'] = $this->request->post['module_ptcontrolpanel_heading_font_family_name'];
        } else {
            $data['module_ptcontrolpanel_heading_font_family_name'] = $this->config->get('module_ptcontrolpanel_heading_font_family_name');
        }

        if (isset($this->request->post['module_ptcontrolpanel_heading_font_family_cate'])) {
            $data['module_ptcontrolpanel_heading_font_family_cate'] = $this->request->post['module_ptcontrolpanel_heading_font_family_cate'];
        } else {
            $data['module_ptcontrolpanel_heading_font_family_cate'] = $this->config->get('module_ptcontrolpanel_heading_font_family_cate');
        }

        if (isset($this->request->post['module_ptcontrolpanel_heading_font_family_link'])) {
            $data['module_ptcontrolpanel_heading_font_family_link'] = $this->request->post['module_ptcontrolpanel_heading_font_family_link'];
        } else {
            $data['module_ptcontrolpanel_heading_font_family_link'] = $this->config->get('module_ptcontrolpanel_heading_font_family_link');
        }

        if (isset($this->request->post['module_ptcontrolpanel_heading_font_weight'])) {
            $data['module_ptcontrolpanel_heading_font_weight'] = $this->request->post['module_ptcontrolpanel_heading_font_weight'];
        } else {
            $data['module_ptcontrolpanel_heading_font_weight'] = $this->config->get('module_ptcontrolpanel_heading_font_weight');
        }

        if (isset($this->request->post['module_ptcontrolpanel_heading_color'])) {
            $data['module_ptcontrolpanel_heading_color'] = $this->request->post['module_ptcontrolpanel_heading_color'];
        } else {
            $data['module_ptcontrolpanel_heading_color'] = $this->config->get('module_ptcontrolpanel_heading_color');
        }

        /* Link */
        if (isset($this->request->post['module_ptcontrolpanel_link_color'])) {
            $data['module_ptcontrolpanel_link_color'] = $this->request->post['module_ptcontrolpanel_link_color'];
        } else {
            $data['module_ptcontrolpanel_link_color'] = $this->config->get('module_ptcontrolpanel_link_color');
        }

        if (isset($this->request->post['module_ptcontrolpanel_link_hover_color'])) {
            $data['module_ptcontrolpanel_link_hover_color'] = $this->request->post['module_ptcontrolpanel_link_hover_color'];
        } else {
            $data['module_ptcontrolpanel_link_hover_color'] = $this->config->get('module_ptcontrolpanel_link_hover_color');
        }

        /* Button */
        if (isset($this->request->post['module_ptcontrolpanel_button_color'])) {
            $data['module_ptcontrolpanel_button_color'] = $this->request->post['module_ptcontrolpanel_button_color'];
        } else {
            $data['module_ptcontrolpanel_button_color'] = $this->config->get('module_ptcontrolpanel_button_color');
        }

        if (isset($this->request->post['module_ptcontrolpanel_button_hover_color'])) {
            $data['module_ptcontrolpanel_button_hover_color'] = $this->request->post['module_ptcontrolpanel_button_hover_color'];
        } else {
            $data['module_ptcontrolpanel_button_hover_color'] = $this->config->get('module_ptcontrolpanel_button_hover_color');
        }

        if (isset($this->request->post['module_ptcontrolpanel_button_bg_color'])) {
            $data['module_ptcontrolpanel_button_bg_color'] = $this->request->post['module_ptcontrolpanel_button_bg_color'];
        } else {
            $data['module_ptcontrolpanel_button_bg_color'] = $this->config->get('module_ptcontrolpanel_button_bg_color');
        }

        if (isset($this->request->post['module_ptcontrolpanel_button_bg_hover_color'])) {
            $data['module_ptcontrolpanel_button_bg_hover_color'] = $this->request->post['module_ptcontrolpanel_button_bg_hover_color'];
        } else {
            $data['module_ptcontrolpanel_button_bg_hover_color'] = $this->config->get('module_ptcontrolpanel_button_bg_hover_color');
        }

        /* Catalog */
        /* Header */
        if (isset($this->request->post['module_ptcontrolpanel_header_cart'])) {
            $data['module_ptcontrolpanel_header_cart'] = $this->request->post['module_ptcontrolpanel_header_cart'];
        } else {
            $data['module_ptcontrolpanel_header_cart'] = $this->config->get('module_ptcontrolpanel_header_cart');
        }

        if (isset($this->request->post['module_ptcontrolpanel_header_currency'])) {
            $data['module_ptcontrolpanel_header_currency'] = $this->request->post['module_ptcontrolpanel_header_currency'];
        } else {
            $data['module_ptcontrolpanel_header_currency'] = $this->config->get('module_ptcontrolpanel_header_currency');
        }

        /* Product catalog */
        if (isset($this->request->post['module_ptcontrolpanel_product_price'])) {
            $data['module_ptcontrolpanel_product_price'] = $this->request->post['module_ptcontrolpanel_product_price'];
        } else {
            $data['module_ptcontrolpanel_product_price'] = $this->config->get('module_ptcontrolpanel_product_price');
        }

        if (isset($this->request->post['module_ptcontrolpanel_product_cart'])) {
            $data['module_ptcontrolpanel_product_cart'] = $this->request->post['module_ptcontrolpanel_product_cart'];
        } else {
            $data['module_ptcontrolpanel_product_cart'] = $this->config->get('module_ptcontrolpanel_product_cart');
        }

        if (isset($this->request->post['module_ptcontrolpanel_product_wishlist'])) {
            $data['module_ptcontrolpanel_product_wishlist'] = $this->request->post['module_ptcontrolpanel_product_wishlist'];
        } else {
            $data['module_ptcontrolpanel_product_wishlist'] = $this->config->get('module_ptcontrolpanel_product_wishlist');
        }

        if (isset($this->request->post['module_ptcontrolpanel_product_compare'])) {
            $data['module_ptcontrolpanel_product_compare'] = $this->request->post['module_ptcontrolpanel_product_compare'];
        } else {
            $data['module_ptcontrolpanel_product_compare'] = $this->config->get('module_ptcontrolpanel_product_compare');
        }

        if (isset($this->request->post['module_ptcontrolpanel_product_options'])) {
            $data['module_ptcontrolpanel_product_options'] = $this->request->post['module_ptcontrolpanel_product_options'];
        } else {
            $data['module_ptcontrolpanel_product_options'] = $this->config->get('module_ptcontrolpanel_product_options');
        }

        /* Category Catalog */
        if (isset($this->request->post['module_ptcontrolpanel_category_price'])) {
            $data['module_ptcontrolpanel_category_price'] = $this->request->post['module_ptcontrolpanel_category_price'];
        } else {
            $data['module_ptcontrolpanel_category_price'] = $this->config->get('module_ptcontrolpanel_category_price');
        }

        if (isset($this->request->post['module_ptcontrolpanel_category_cart'])) {
            $data['module_ptcontrolpanel_category_cart'] = $this->request->post['module_ptcontrolpanel_category_cart'];
        } else {
            $data['module_ptcontrolpanel_category_cart'] = $this->config->get('module_ptcontrolpanel_category_cart');
        }

        if (isset($this->request->post['module_ptcontrolpanel_category_wishlist'])) {
            $data['module_ptcontrolpanel_category_wishlist'] = $this->request->post['module_ptcontrolpanel_category_wishlist'];
        } else {
            $data['module_ptcontrolpanel_category_wishlist'] = $this->config->get('module_ptcontrolpanel_category_wishlist');
        }

        if (isset($this->request->post['module_ptcontrolpanel_category_compare'])) {
            $data['module_ptcontrolpanel_category_compare'] = $this->request->post['module_ptcontrolpanel_category_compare'];
        } else {
            $data['module_ptcontrolpanel_category_compare'] = $this->config->get('module_ptcontrolpanel_category_compare');
        }

        if (isset($this->request->post['module_ptcontrolpanel_category_prodes'])) {
            $data['module_ptcontrolpanel_category_prodes'] = $this->request->post['module_ptcontrolpanel_category_prodes'];
        } else {
            $data['module_ptcontrolpanel_category_prodes'] = $this->config->get('module_ptcontrolpanel_category_prodes');
        }

        if (isset($this->request->post['module_ptcontrolpanel_category_label'])) {
            $data['module_ptcontrolpanel_category_label'] = $this->request->post['module_ptcontrolpanel_category_label'];
        } else {
            $data['module_ptcontrolpanel_category_label'] = $this->config->get('module_ptcontrolpanel_category_label');
        }

        /* Product */
        if (isset($this->request->post['module_ptcontrolpanel_related'])) {
            $data['module_ptcontrolpanel_related'] = $this->request->post['module_ptcontrolpanel_related'];
        } else {
            $data['module_ptcontrolpanel_related'] = $this->config->get('module_ptcontrolpanel_related');
        }

        if (isset($this->request->post['module_ptcontrolpanel_social'])) {
            $data['module_ptcontrolpanel_social'] = $this->request->post['module_ptcontrolpanel_social'];
        } else {
            $data['module_ptcontrolpanel_social'] = $this->config->get('module_ptcontrolpanel_social');
        }

        if (isset($this->request->post['module_ptcontrolpanel_tax'])) {
            $data['module_ptcontrolpanel_tax'] = $this->request->post['module_ptcontrolpanel_tax'];
        } else {
            $data['module_ptcontrolpanel_tax'] = $this->config->get('module_ptcontrolpanel_tax');
        }

        if (isset($this->request->post['module_ptcontrolpanel_tags'])) {
            $data['module_ptcontrolpanel_tags'] = $this->request->post['module_ptcontrolpanel_tags'];
        } else {
            $data['module_ptcontrolpanel_tags'] = $this->config->get('module_ptcontrolpanel_tags');
        }

        if (isset($this->request->post['module_ptcontrolpanel_use_zoom'])) {
            $data['module_ptcontrolpanel_use_zoom'] = $this->request->post['module_ptcontrolpanel_use_zoom'];
        } else {
            $data['module_ptcontrolpanel_use_zoom'] = $this->config->get('module_ptcontrolpanel_use_zoom');
        }

        if (isset($this->request->post['module_ptcontrolpanel_zoom_type'])) {
            $data['module_ptcontrolpanel_zoom_type'] = $this->request->post['module_ptcontrolpanel_zoom_type'];
        } else {
            $data['module_ptcontrolpanel_zoom_type'] = $this->config->get('module_ptcontrolpanel_zoom_type');
        }

        if (isset($this->request->post['module_ptcontrolpanel_zoom_space'])) {
            $data['module_ptcontrolpanel_zoom_space'] = $this->request->post['module_ptcontrolpanel_zoom_space'];
        } else {
            $data['module_ptcontrolpanel_zoom_space'] = $this->config->get('module_ptcontrolpanel_zoom_space');
        }

        if (isset($this->request->post['module_ptcontrolpanel_zoom_title'])) {
            $data['module_ptcontrolpanel_zoom_title'] = $this->request->post['module_ptcontrolpanel_zoom_title'];
        } else {
            $data['module_ptcontrolpanel_zoom_title'] = $this->config->get('module_ptcontrolpanel_zoom_title');
        }

        if (isset($this->request->post['module_ptcontrolpanel_use_swatches'])) {
            $data['module_ptcontrolpanel_use_swatches'] = $this->request->post['module_ptcontrolpanel_use_swatches'];
        } else {
            $data['module_ptcontrolpanel_use_swatches'] = $this->config->get('module_ptcontrolpanel_use_swatches');
        }

        if (isset($this->request->post['module_ptcontrolpanel_swatches_width'])) {
            $data['module_ptcontrolpanel_swatches_width'] = $this->request->post['module_ptcontrolpanel_swatches_width'];
        } else {
            $data['module_ptcontrolpanel_swatches_width'] = $this->config->get('module_ptcontrolpanel_swatches_width');
        }

        if (isset($this->request->post['module_ptcontrolpanel_swatches_height'])) {
            $data['module_ptcontrolpanel_swatches_height'] = $this->request->post['module_ptcontrolpanel_swatches_height'];
        } else {
            $data['module_ptcontrolpanel_swatches_height'] = $this->config->get('module_ptcontrolpanel_swatches_height');
        }

        if (isset($this->request->post['module_ptcontrolpanel_swatches_option'])) {
            $data['module_ptcontrolpanel_swatches_option'] = $this->request->post['module_ptcontrolpanel_swatches_option'];
        } else {
            $data['module_ptcontrolpanel_swatches_option'] = $this->config->get('module_ptcontrolpanel_swatches_option');
        }

        /* Category */
        if (isset($this->request->post['module_ptcontrolpanel_category_image'])) {
            $data['module_ptcontrolpanel_category_image'] = $this->request->post['module_ptcontrolpanel_category_image'];
        } else {
            $data['module_ptcontrolpanel_category_image'] = $this->config->get('module_ptcontrolpanel_category_image');
        }

        if (isset($this->request->post['module_ptcontrolpanel_category_description'])) {
            $data['module_ptcontrolpanel_category_description'] = $this->request->post['module_ptcontrolpanel_category_description'];
        } else {
            $data['module_ptcontrolpanel_category_description'] = $this->config->get('module_ptcontrolpanel_category_description');
        }

        if (isset($this->request->post['module_ptcontrolpanel_sub_category'])) {
            $data['module_ptcontrolpanel_sub_category'] = $this->request->post['module_ptcontrolpanel_sub_category'];
        } else {
            $data['module_ptcontrolpanel_sub_category'] = $this->config->get('module_ptcontrolpanel_sub_category');
        }

        if (isset($this->request->post['module_ptcontrolpanel_use_filter'])) {
            $data['module_ptcontrolpanel_use_filter'] = $this->request->post['module_ptcontrolpanel_use_filter'];
        } else {
            $data['module_ptcontrolpanel_use_filter'] = $this->config->get('module_ptcontrolpanel_use_filter');
        }

        if (isset($this->request->post['module_ptcontrolpanel_filter_position'])) {
            $data['module_ptcontrolpanel_filter_position'] = $this->request->post['module_ptcontrolpanel_filter_position'];
        } else {
            $data['module_ptcontrolpanel_filter_position'] = $this->config->get('module_ptcontrolpanel_filter_position');
        }

        if (isset($this->request->post['module_ptcontrolpanel_cate_quickview'])) {
            $data['module_ptcontrolpanel_cate_quickview'] = $this->request->post['module_ptcontrolpanel_cate_quickview'];
        } else {
            $data['module_ptcontrolpanel_cate_quickview'] = $this->config->get('module_ptcontrolpanel_cate_quickview');
        }

        if (isset($this->request->post['module_ptcontrolpanel_img_effect'])) {
            $data['module_ptcontrolpanel_img_effect'] = $this->request->post['module_ptcontrolpanel_img_effect'];
        } else {
            $data['module_ptcontrolpanel_img_effect'] = $this->config->get('module_ptcontrolpanel_img_effect');
        }

        if (isset($this->request->post['module_ptcontrolpanel_cate_swatches_width'])) {
            $data['module_ptcontrolpanel_cate_swatches_width'] = $this->request->post['module_ptcontrolpanel_cate_swatches_width'];
        } else {
            $data['module_ptcontrolpanel_cate_swatches_width'] = $this->config->get('module_ptcontrolpanel_cate_swatches_width');
        }

        if (isset($this->request->post['module_ptcontrolpanel_cate_swatches_height'])) {
            $data['module_ptcontrolpanel_cate_swatches_height'] = $this->request->post['module_ptcontrolpanel_cate_swatches_height'];
        } else {
            $data['module_ptcontrolpanel_cate_swatches_height'] = $this->config->get('module_ptcontrolpanel_cate_swatches_height');
        }

        if (isset($this->request->post['module_ptcontrolpanel_advance_view'])) {
            $data['module_ptcontrolpanel_advance_view'] = $this->request->post['module_ptcontrolpanel_advance_view'];
        } else {
            $data['module_ptcontrolpanel_advance_view'] = $this->config->get('module_ptcontrolpanel_advance_view');
        }

        if (isset($this->request->post['module_ptcontrolpanel_default_view'])) {
            $data['module_ptcontrolpanel_default_view'] = $this->request->post['module_ptcontrolpanel_default_view'];
        } else {
            $data['module_ptcontrolpanel_default_view'] = $this->config->get('module_ptcontrolpanel_default_view');
        }

        if (isset($this->request->post['module_ptcontrolpanel_product_row'])) {
            $data['module_ptcontrolpanel_product_row'] = $this->request->post['module_ptcontrolpanel_product_row'];
        } else {
            $data['module_ptcontrolpanel_product_row'] = $this->config->get('module_ptcontrolpanel_product_row');
        }

        if (isset($this->request->post['module_ptcontrolpanel_custom_css'])) {
            $data['module_ptcontrolpanel_custom_css'] = $this->request->post['module_ptcontrolpanel_custom_css'];
        } else {
            $data['module_ptcontrolpanel_custom_css'] = $this->config->get('module_ptcontrolpanel_custom_css');
        }

        if (isset($this->request->post['module_ptcontrolpanel_custom_js'])) {
            $data['module_ptcontrolpanel_custom_js'] = $this->request->post['module_ptcontrolpanel_custom_js'];
        } else {
            $data['module_ptcontrolpanel_custom_js'] = $this->config->get('module_ptcontrolpanel_custom_js');
        }

        $data['plaza_menus'] = array();

        if($this->user->hasPermission('access', 'extension/module/ptcontrolpanel')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-magic"></i> ' . $this->language->get('text_control_panel'),
                'url'    => $this->url->link('extension/module/ptcontrolpanel', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 1
            );
        }

        if($this->user->hasPermission('access', 'plaza/module')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-puzzle-piece"></i> ' . $this->language->get('text_theme_module'),
                'url'    => $this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'plaza/featuredcate')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-tag"></i> ' . $this->language->get('text_special_category'),
                'url'    => $this->url->link('plaza/featuredcate', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'plaza/ultimatemenu')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-bars"></i> ' . $this->language->get('text_ultimate_menu'),
                'url'    => $this->url->link('plaza/ultimatemenu/menuList', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if ($this->user->hasPermission('access', 'plaza/blog')) {
            $blog_menu = array();

            if ($this->user->hasPermission('access', 'plaza/blog/post')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_posts'),
                    'url'    => $this->url->link('plaza/blog/post', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if ($this->user->hasPermission('access', 'plaza/blog/list')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_posts_list'),
                    'url'    => $this->url->link('plaza/blog/list', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if ($this->user->hasPermission('access', 'plaza/blog/setting')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_blog_setting'),
                    'url'    => $this->url->link('plaza/blog/setting', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if($blog_menu) {
                $data['plaza_menus'][] = array(
                    'title'  => '<i class="a fa fa-ticket"></i> ' . $this->language->get('text_blog'),
                    'child'  => $blog_menu,
                    'active' => 0
                );
            }
        }

        if($this->user->hasPermission('access', 'plaza/slider')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-film"></i> ' . $this->language->get('text_slider'),
                'url'    => $this->url->link('plaza/slider', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'plaza/testimonial')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-comment"></i> ' . $this->language->get('text_testimonial'),
                'url'    => $this->url->link('plaza/testimonial', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'plaza/newsletter')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-envelope"></i> ' . $this->language->get('text_newsletter'),
                'url'    => $this->url->link('plaza/newsletter', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        $this->document->addStyle('view/stylesheet/plaza/themeadmin.css');
        $this->document->addScript('view/javascript/plaza/jscolor.min.js');
        $this->document->addScript('view/javascript/plaza/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/plaza/switch-toggle/css/bootstrap-toggle.min.css');
        $this->document->addScript('view/javascript/plaza/selection/js/bootstrap-select.min.js');
        $this->document->addStyle('view/javascript/plaza/selection/css/bootstrap-select.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('plaza/module/ptcontrolpanel', $data));
    }

    public function import() {
        $this->load->language('extension/module/ptcontrolpanel');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['file'])) {
            $file = $this->request->post['file'];
        } else {
            $file = '';
        }

        if (!file_exists($file)) {
            unset($this->session->data['success']);

            $this->session->data['error_load_file'] = sprintf($this->language->get('error_load_file'), $file);

            $this->response->redirect($this->url->link('extension/module/ptcontrolpanel', 'user_token=' . $this->session->data['user_token'], true));
        } else {
            unset($this->session->data['error_load_file']);

            $lines = file($file);

            if($lines) {
                $sql = '';

                foreach($lines as $line) {
                    if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
                        $sql .= $line;

                        if (preg_match('/;\s*$/', $line)) {
                            $sql = str_replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" . DB_PREFIX, $sql);
                            $sql = str_replace("CREATE TABLE `oc_", "CREATE TABLE `" . DB_PREFIX, $sql);
                            $sql = str_replace("CREATE TABLE IF NOT EXISTS `oc_", "CREATE TABLE `" . DB_PREFIX, $sql);
                            $sql = str_replace("INSERT INTO `oc_", "INSERT INTO `" . DB_PREFIX, $sql);
                            $sql = str_replace("UPDATE `oc_", "UPDATE `" . DB_PREFIX, $sql);
                            $sql = str_replace("WHERE `oc_", "WHERE `" . DB_PREFIX, $sql);
                            $sql = str_replace("TRUNCATE TABLE `oc_", "TRUNCATE TABLE `" . DB_PREFIX, $sql);
                            $sql = str_replace("ALTER TABLE `oc_", "ALTER TABLE `" . DB_PREFIX, $sql);

                            $this->db->query($sql);

                            $sql = '';
                        }
                    }
                }
            }

            $this->session->data['success'] = $this->language->get('text_import_success');

            $this->response->redirect($this->url->link('extension/module/ptcontrolpanel', 'user_token=' . $this->session->data['user_token'], true));
        }
    }

    public function install() {
        $this->load->model('plaza/controlpanel');
        $this->model_plaza_controlpanel->setupData();

        $this->load->model('setting/setting');

        $data = array(
            'module_ptcontrolpanel_status' => 1
        );

        $this->model_setting_setting->editSetting('module_ptcontrolpanel', $data, 0);

        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'plaza/module');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'plaza/module');
    }

    public function uninstall() {
        $this->load->model('user/user_group');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/module/ptcontrolpanel');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/module/ptcontrolpanel');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/blog');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/blog');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/blog/post');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/blog/post');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/blog/list');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/blog/list');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/blog/setting');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/blog/setting');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/slider');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/slider');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/testimonial');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/testimonial');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/ultimatemenu');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/ultimatemenu');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/featuredcate');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/featuredcate');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/newsletter');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/newsletter');
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/ptcontrolpanel')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
