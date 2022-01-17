<?php
class ControllerExtensionModulePtsearch extends Controller
{
    public function index() {
        $this->load->language('plaza/module/ptsearch');

        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        if (isset($this->request->get['search'])) {
            $data['search'] = $this->request->get['search'];
        } else {
            $data['search'] = '';
        }

        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
        } else {
            $category_id = 0;
        }

        $data['categories'] = array();

        $categories_1 = $this->model_catalog_category->getCategories(0);

        foreach ($categories_1 as $category_1) {
            $level_2_data = array();

            $categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

            foreach ($categories_2 as $category_2) {
                $level_3_data = array();

                $categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

                foreach ($categories_3 as $category_3) {
                    $level_3_data[] = array(
                        'category_id' => $category_3['category_id'],
                        'name'        => $category_3['name'],
                    );
                }

                $level_2_data[] = array(
                    'category_id' => $category_2['category_id'],
                    'name'        => $category_2['name'],
                    'children'    => $level_3_data
                );
            }

            $data['categories'][] = array(
                'category_id' => $category_1['category_id'],
                'name'        => $category_1['name'],
                'children'    => $level_2_data
            );
        }

        $data['category_id'] = $category_id;
        $data['search_action'] = $this->url->link('product/search', '', true);
        $data['search_ajax_action'] = $this->url->link('extension/module/ptsearch/ajaxSearch', '', true);

        $data['ajax_enabled'] = (int) $this->config->get('module_ptsearch_ajax');

        $store_id = $this->config->get('config_store_id');

        if (!empty($_SERVER['HTTPS'])) {
            // SSL connection
            $common_url = str_replace('http://', 'https://', $this->config->get('config_url'));
        } else {
            $common_url = $this->config->get('config_url');
        }

        if(isset($this->config->get('module_ptcontrolpanel_loader_img')[$store_id])) {
            $data['loader_img'] = $common_url . 'image/' . $this->config->get('module_ptcontrolpanel_loader_img')[$store_id];
        } else {
            $data['loader_img'] = $common_url . 'image/plaza/ajax-loader.gif';;
        }

        return $this->load->view('plaza/search/form', $data);
    }

    public function ajaxSearch() {
        $this->load->language('plaza/module/ptsearch');

        $json = array();

        if(($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $postData = $this->request->post;

            $this->setAjaxSearchResult($postData);

            if(!$json) {
                $json['success'] = true;
            }

            $productCollection = $this->getAjaxSearchResult();
            if(!$productCollection || count($productCollection) == 0) {
                $data['products'] = array();
            } else {
                $data['products'] = $productCollection;
            }

            $data['product_img_enabled'] = (int) $this->config->get('module_ptsearch_show_img');
            $data['product_price_enabled'] = (int) $this->config->get('module_ptsearch_show_price');

            $json['result_html'] = $this->load->view('plaza/search/ajaxresult', $data);

        } else {
            if(!$json) {
                $json['success'] = false;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function setAjaxSearchResult($data) {
        $text_search = $data['text_search'];
        $cate_search = $data['cate_search'];

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $url = '';

        if (isset($text_search)) {
            $search = $text_search;
            $url .= '&search=' . urlencode(html_entity_decode($text_search, ENT_QUOTES, 'UTF-8'));
        } else {
            $search = '';
        }

        if (isset($cate_search)) {
            $category_id = $cate_search;
            $url .= '&category_id=' . $cate_search;
        } else {
            $category_id = 0;
        }

        $data['products'] = array();

        $filter_data = array(
            'filter_name'         => $search,
            'filter_category_id'  => $category_id
        );

        $results = $this->model_catalog_product->getProducts($filter_data);

        foreach ($results as $result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
            }

            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }

            if ((float)$result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $special = false;
            }

            $data['products'][] = array(
                'product_id'  => $result['product_id'],
                'thumb'       => $image,
                'name'        => $result['name'],
                'price'       => $price,
                'special'     => $special,
                'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
            );
        }

        $this->__set('ajax_search_result', $data['products']);
    }

    public function getAjaxSearchResult() {
        return $this->__get('ajax_search_result');
    }
}