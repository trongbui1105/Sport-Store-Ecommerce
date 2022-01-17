<?php
class ControllerExtensionModulePtproducts extends Controller {
    private $error = array();

    public function index() {

        $this->load->language('extension/module/ptproducts');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/module');
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $post_data = $this->request->post;

            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('ptproducts', $post_data);

                $module_id = $this->db->getLastId();
                $post_data['module_id'] = $module_id;

                $this->model_setting_module->editModule($module_id, $post_data);
            } else {
                $post_data['module_id'] = $this->request->get['module_id'];
                $this->model_setting_module->editModule($this->request->get['module_id'], $post_data);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token']));
        }

        $this->load->model('localisation/language');

        $data['languages'] = array();

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language){
            if ($language['status']) {
                $data['languages'][] = array(
                    'name'  => $language['name'],
                    'language_id' => $language['language_id'],
                    'code' => $language['code']
                );
            }
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/ptproducts', 'user_token=' . $this->session->data['user_token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/ptproducts', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/ptproducts', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/ptproducts', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['cancel'] = $this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = 1;
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['module_title'])) {
            $data['module_title'] = $this->request->post['module_title'];
        } elseif (!empty($module_info)) {
            $data['module_title'] = $module_info['module_title'];
        } else {
            $data['module_title'] = array();
        }

        if (isset($this->request->post['show_module_description'])) {
            $data['show_module_description'] = $this->request->post['show_module_description'];
        } elseif (!empty($module_info)) {
            $data['show_module_description'] = $module_info['show_module_description'];
        } else {
            $data['show_module_description'] = 0;
        }

        if (isset($this->request->post['module_description'])) {
            $data['module_description'] = $this->request->post['module_description'];
        } elseif (!empty($module_info)) {
            $data['module_description'] = $module_info['module_description'];
        } else {
            $data['module_description'] = array();
        }

        if (isset($this->request->post['module_type'])) {
            $data['module_type'] = $this->request->post['module_type'];
        } elseif (!empty($module_info)) {
            $data['module_type'] = $module_info['module_type'];
        } else {
            $data['module_type'] = 'single_tab';
        }

        if (isset($this->request->post['layout_type'])) {
            $data['layout_type'] = $this->request->post['layout_type'];
        } elseif (!empty($module_info)) {
            $data['layout_type'] = $module_info['layout_type'];
        } else {
            $data['layout_type'] = 'slider';
        }

        if (isset($this->request->post['product_layout_type'])) {
            $data['product_layout_type'] = $this->request->post['product_layout_type'];
        } elseif (!empty($module_info)) {
            $data['product_layout_type'] = $module_info['product_layout_type'];
        } else {
            $data['product_layout_type'] = 'grid';
        }

        if (isset($this->request->post['layout_classname'])) {
            $data['layout_classname'] = $this->request->post['layout_classname'];
        } elseif (!empty($module_info)) {
            $data['layout_classname'] = $module_info['layout_classname'];
        } else {
            $data['layout_classname'] = '';
        }

        if (isset($this->request->post['slider_width'])) {
            $data['slider_width'] = $this->request->post['slider_width'];
        } elseif (!empty($module_info)) {
            $data['slider_width'] = $module_info['slider_width'];
        } else {
            $data['slider_width'] = 200;
        }

        if (isset($this->error['slider_width_error'])) {
            $data['error_slider_width'] = $this->error['slider_width_error'];
        } else {
            $data['error_slider_width'] = '';
        }

        if (isset($this->request->post['slider_height'])) {
            $data['slider_height'] = $this->request->post['slider_height'];
        } elseif (!empty($module_info)) {
            $data['slider_height'] = $module_info['slider_height'];
        } else {
            $data['slider_height'] = 200;
        }

        if (isset($this->error['slider_height_error'])) {
            $data['error_slider_height'] = $this->error['slider_height_error'];
        } else {
            $data['error_slider_height'] = '';
        }

        if (isset($this->request->post['auto'])) {
            $data['auto'] = $this->request->post['auto'];
        } elseif (!empty($module_info)) {
            $data['auto'] = $module_info['auto'];
        } else {
            $data['auto'] = 1;
        }

        if (isset($this->request->post['item'])) {
            $data['item'] = $this->request->post['item'];
        } elseif (!empty($module_info)) {
            $data['item'] = $module_info['item'];
        } else {
            $data['item'] = array();
        }

        if (isset($this->request->post['row'])) {
            $data['row'] = $this->request->post['row'];
        } elseif (!empty($module_info)) {
            $data['row'] = $module_info['row'];
        } else {
            $data['row'] = array();
        }

        if (isset($this->request->post['limit'])) {
            $data['limit'] = $this->request->post['limit'];
        } elseif (!empty($module_info)) {
            $data['limit'] = $module_info['limit'];
        } else {
            $data['limit'] = 10;
        }

        if (isset($this->request->post['speed'])) {
            $data['speed'] = $this->request->post['speed'];
        } elseif (!empty($module_info)) {
            $data['speed'] = $module_info['speed'];
        } else {
            $data['speed'] = 500;
        }

        if (isset($this->request->post['navigation'])) {
            $data['navigation'] = $this->request->post['navigation'];
        } elseif (!empty($module_info)) {
            $data['navigation'] = $module_info['navigation'];
        } else {
            $data['navigation'] = 1;
        }

        if (isset($this->request->post['pagination'])) {
            $data['pagination'] = $this->request->post['pagination'];
        } elseif (!empty($module_info)) {
            $data['pagination'] = $module_info['pagination'];
        } else {
            $data['pagination'] = 0;
        }

        if (isset($this->request->post['show_price'])) {
            $data['show_price'] = $this->request->post['show_price'];
        } elseif (!empty($module_info)) {
            $data['show_price'] = $module_info['show_price'];
        } else {
            $data['show_price'] = 1;
        }

        if (isset($this->request->post['show_cart'])) {
            $data['show_cart'] = $this->request->post['show_cart'];
        } elseif (!empty($module_info)) {
            $data['show_cart'] = $module_info['show_cart'];
        } else {
            $data['show_cart'] = 1;
        }

        if (isset($this->request->post['show_wishlist'])) {
            $data['show_wishlist'] = $this->request->post['show_wishlist'];
        } elseif (!empty($module_info)) {
            $data['show_wishlist'] = $module_info['show_wishlist'];
        } else {
            $data['show_wishlist'] = 1;
        }

        if (isset($this->request->post['show_compare'])) {
            $data['show_compare'] = $this->request->post['show_compare'];
        } elseif (!empty($module_info)) {
            $data['show_compare'] = $module_info['show_compare'];
        } else {
            $data['show_compare'] = 1;
        }

        if (isset($this->request->post['show_countdown'])) {
            $data['show_countdown'] = $this->request->post['show_countdown'];
        } elseif (!empty($module_info)) {
            $data['show_countdown'] = $module_info['show_countdown'];
        } else {
            $data['show_countdown'] = 0;
        }

        if (isset($this->request->post['show_hover_image'])) {
            $data['show_hover_image'] = $this->request->post['show_hover_image'];
        } elseif (!empty($module_info)) {
            $data['show_hover_image'] = $module_info['show_hover_image'];
        } else {
            $data['show_hover_image'] = 0;
        }

        if (isset($this->request->post['show_swatches_image'])) {
            $data['show_swatches_image'] = $this->request->post['show_swatches_image'];
        } elseif (!empty($module_info)) {
            $data['show_swatches_image'] = $module_info['show_swatches_image'];
        } else {
            $data['show_swatches_image'] = 0;
        }

        if (isset($this->request->post['show_quickview'])) {
            $data['show_quickview'] = $this->request->post['show_quickview'];
        } elseif (!empty($module_info)) {
            $data['show_quickview'] = $module_info['show_quickview'];
        } else {
            $data['show_quickview'] = 1;
        }

        if (isset($this->request->post['show_product_description'])) {
            $data['show_product_description'] = $this->request->post['show_product_description'];
        } elseif (!empty($module_info)) {
            $data['show_product_description'] = $module_info['show_product_description'];
        } else {
            $data['show_product_description'] = 0;
        }

        if (isset($this->request->post['show_label'])) {
            $data['show_label'] = $this->request->post['show_label'];
        } elseif (!empty($module_info)) {
            $data['show_label'] = $module_info['show_label'];
        } else {
            $data['show_label'] = 1;
        }

        // Single Products
        if (isset($this->request->post['single_product_collection'])) {
            $data['single_product_collection'] = $this->request->post['single_product_collection'];
        } elseif (!empty($module_info)) {
            $data['single_product_collection'] = $module_info['single_product_collection'];
        } else {
            $data['single_product_collection'] = 'specified';
        }

        $data['single_specified_products'] = array();

        if (!empty($this->request->post['single_specified_products'])) {
            $single_specified_products = $this->request->post['single_specified_products'];
        } elseif (!empty($module_info['single_specified_products'])) {
            $single_specified_products = $module_info['single_specified_products'];
        } else {
            $single_specified_products = array();
        }

        foreach ($single_specified_products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                $data['single_specified_products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'name'       => $product_info['name']
                );
            }
        }

        if (isset($this->request->post['single_category'])) {
            $data['single_category'] = $this->request->post['single_category'];
        } elseif (!empty($module_info)) {
            $data['single_category'] = $module_info['single_category'];
        } else {
            $data['single_category'] = 20;
        }

        $data['categories'] = array();

        $all_cate_count = $this->model_catalog_category->getTotalCategories();

        $filter_data = array(
            'start' => 0,
            'limit' => (int) $all_cate_count
        );

        $categories = $this->model_catalog_category->getCategories($filter_data);

        foreach ($categories as $category) {
            $category_info = $this->model_catalog_category->getCategory($category['category_id']);

            if ($category_info) {
                $data['categories'][] = array(
                    'category_id' => $category_info['category_id'],
                    'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
                );
            }
        }

        if (isset($this->request->post['single_category_product_type'])) {
            $data['single_category_product_type'] = $this->request->post['single_category_product_type'];
        } elseif (!empty($module_info)) {
            $data['single_category_product_type'] = $module_info['single_category_product_type'];
        } else {
            $data['single_category_product_type'] = 'all';
        }

        $data['single_category_products'] = array();

        if (!empty($this->request->post['single_category_products'])) {
            $single_category_products = $this->request->post['single_category_products'];
        } elseif (!empty($module_info['single_category_products'])) {
            $single_category_products = $module_info['single_category_products'];
        } else {
            $single_category_products = array();
        }

        foreach ($single_category_products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                $data['single_category_products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'name'       => $product_info['name']
                );
            }
        }

        if (isset($this->request->post['single_category_product_special_type'])) {
            $data['single_category_product_special_type'] = $this->request->post['single_category_product_special_type'];
        } elseif (!empty($module_info)) {
            $data['single_category_product_special_type'] = $module_info['single_category_product_special_type'];
        } else {
            $data['single_category_product_special_type'] = '';
        }

        if (isset($this->request->post['single_product_special_type'])) {
            $data['single_product_special_type'] = $this->request->post['single_product_special_type'];
        } elseif (!empty($module_info)) {
            $data['single_product_special_type'] = $module_info['single_product_special_type'];
        } else {
            $data['single_product_special_type'] = '';
        }

        if (isset($this->request->post['single_image_width'])) {
            $data['single_image_width'] = $this->request->post['single_image_width'];
        } elseif (!empty($module_info)) {
            $data['single_image_width'] = $module_info['single_image_width'];
        } else {
            $data['single_image_width'] = 100;
        }

        if (isset($this->request->post['single_image_height'])) {
            $data['single_image_height'] = $this->request->post['single_image_height'];
        } elseif (!empty($module_info)) {
            $data['single_image_height'] = $module_info['single_image_height'];
        } else {
            $data['single_image_height'] = 100;
        }

        if (isset($this->request->post['single_image']) && is_file(DIR_IMAGE . $this->request->post['single_image'])) {
            $data['single_image_thumb'] = $this->model_tool_image->resize($this->request->post['single_image'], 100, 100);
            $data['single_image'] = $this->request->post['single_image'];
        } elseif (!empty($module_info) && is_file(DIR_IMAGE . $module_info['single_image'])) {
            $data['single_image_thumb'] = $this->model_tool_image->resize($module_info['single_image'], 100, 100);
            $data['single_image'] = $module_info['single_image'];
        } else {
            $data['single_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['single_image_placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->request->post['single_image_link'])) {
            $data['single_image_link'] = $this->request->post['single_image_link'];
        } elseif (!empty($module_info)) {
            $data['single_image_link'] = $module_info['single_image_link'];
        } else {
            $data['single_image_link'] = '';
        }

        if (isset($this->request->post['tabs'])) {
            $tabs = $this->request->post['tabs'];
        } elseif (!empty($module_info) && !empty($module_info['tabs'])) {
            $tabs = $module_info['tabs'];
        } else {
            $tabs = array();
        }

        $data['tabs'] = array();

        foreach($tabs as $tab) {
            // Specified Products
            $spedified_products = array();
            if(isset($tab['specified_products'])) {
                foreach ($tab['specified_products'] as $pid) {
                    $product_info = $this->model_catalog_product->getProduct($pid);

                    if ($product_info) {
                        $spedified_products[] = array(
                            'product_id' => $product_info['product_id'],
                            'name'       => $product_info['name']
                        );
                    }
                }
            }
            $tab['specified_products_list'] = $spedified_products;

            // Category Products
            $category_products = array();
            if(isset($tab['category_products'])) {
                foreach ($tab['category_products'] as $pid) {
                    $product_info = $this->model_catalog_product->getProduct($pid);

                    if ($product_info) {
                        $category_products[] = array(
                            'product_id' => $product_info['product_id'],
                            'name'       => $product_info['name']
                        );
                    }
                }
            }
            $tab['category_products_list'] = $category_products;

            // Image
            if(isset($tab['image']) && is_file(DIR_IMAGE . $tab['image'])) {
                $tab['image_thumb'] = $this->model_tool_image->resize($tab['image'], 100, 100);
            } else {
                $tab['image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }

            // Image
            if(isset($tab['title_image']) && is_file(DIR_IMAGE . $tab['title_image'])) {
                $tab['title_image_thumb'] = $this->model_tool_image->resize($tab['title_image'], 100, 100);
            } else {
                $tab['title_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }

            $data['tabs'][] = $tab;
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $this->document->addStyle('view/stylesheet/plaza/themeadmin.css');
        $this->document->addScript('view/javascript/plaza/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addScript('view/javascript/plaza/selection/js/bootstrap-select.min.js');
        $this->document->addStyle('view/javascript/plaza/switch-toggle/css/bootstrap-toggle.min.css');
        $this->document->addStyle('view/javascript/plaza/selection/css/bootstrap-select.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('plaza/module/ptproducts', $data));
    }

    public function autoGetProductsByCategory() {
        $this->load->model('plaza/catalog');
        $this->load->model('tool/image');

        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
        } else {
            $category_id = '';
        }

        $filter_data = array(
            'filter_category_id' => $category_id,
            'filter_name'  => $filter_name,
            'start'        => 0,
            'limit'        => 5
        );

        $results = $this->model_plaza_catalog->getProductsByCategory($filter_data);

        foreach ($results as $result) {
            $image = $this->model_tool_image->resize($result['image'],40,40);
            $json[] = array(
                'product_id' => $result['product_id'],
                'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                'image'      => $image,
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/ptproducts')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!$this->request->post['slider_width']) {
            $this->error['slider_width_error'] = $this->language->get('error_width');
        }

        if (!$this->request->post['slider_height']) {
            $this->error['slider_height_error'] = $this->language->get('error_height');
        }

        return !$this->error;
    }
}