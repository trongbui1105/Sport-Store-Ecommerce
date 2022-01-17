<?php
class ControllerPlazaQuickview extends Controller
{
    public function index() {
        $json = array();

        if (isset($this->request->get['product_id'])) {
            $product_id = (int) $this->request->get['product_id'];
        } else {
            $product_id = 0;
        }

        $data = $this->loadProduct($product_id);

        if(!$json) {
            if($data) {
                $json['html'] = $this->load->view('plaza/quickview/product', $data);
                $json['success'] = true;
            } else {
                $json['success'] = false;
                $json['html'] = "There is no product";
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function seoview() {
        $json = array();

        $this->load->model('plaza/quickview');

        if(!$json) {
            if (isset($this->request->get['ourl'])) {
                $seo_url = $this->request->get['ourl'];

                $product_id = $this->model_plaza_quickview->getProductBySeoUrl($seo_url);

                $data = $this->loadProduct($product_id);

                if($data) {
                    $json['html'] = $this->load->view('plaza/quickview/product', $data);
                    $json['success'] = true;
                } else {
                    $json['success'] = false;
                    $json['html'] = "There is no product";
                }
            } else {
                $json['success'] = false;
                $json['html'] = "There is no product";
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function loadProduct($product_id) {
        $this->load->language('product/product');
        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if($product_info) {
            $data['product_name'] = $product_info['name'];

            /* Plaza Product Configuration */
            $this->load->model('tool/image');
            $store_id = $this->config->get('config_store_id');

            /* Catalog Mode */
            if(isset($this->config->get('module_ptcontrolpanel_product_price')[$store_id])) {
                $data['show_product_price'] = (int) $this->config->get('module_ptcontrolpanel_product_price')[$store_id];
            } else {
                $data['show_product_price'] = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_product_cart')[$store_id])) {
                $data['show_product_button_cart'] = (int) $this->config->get('module_ptcontrolpanel_product_cart')[$store_id];
            } else {
                $data['show_product_button_cart'] = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_product_wishlist')[$store_id])) {
                $data['show_product_button_wishlist'] = (int) $this->config->get('module_ptcontrolpanel_product_wishlist')[$store_id];
            } else {
                $data['show_product_button_wishlist'] = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_product_compare')[$store_id])) {
                $data['show_product_button_compare'] = (int) $this->config->get('module_ptcontrolpanel_product_compare')[$store_id];
            } else {
                $data['show_product_button_compare'] = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_product_options')[$store_id])) {
                $data['show_product_options'] = (int) $this->config->get('module_ptcontrolpanel_product_options')[$store_id];
            } else {
                $data['show_product_options'] = 0;
            }

            /* Product Details */
            if(isset($this->config->get('module_ptcontrolpanel_related')[$store_id])) {
                $data['show_product_related'] = (int) $this->config->get('module_ptcontrolpanel_related')[$store_id];
            } else {
                $data['show_product_related'] = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_social')[$store_id])) {
                $data['show_product_social'] = (int) $this->config->get('module_ptcontrolpanel_social')[$store_id];
            } else {
                $data['show_product_social'] = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_tax')[$store_id])) {
                $data['show_product_tax'] = (int) $this->config->get('module_ptcontrolpanel_tax')[$store_id];
            } else {
                $data['show_product_tax'] = 0;
            }

            if(isset($this->config->get('module_ptcontrolpanel_tags')[$store_id])) {
                $data['show_product_tags'] = (int) $this->config->get('module_ptcontrolpanel_tags')[$store_id];
            } else {
                $data['show_product_tags'] = 0;
            }

            $use_zoom = (int) $this->config->get('module_ptcontrolpanel_use_zoom')[$store_id];

            if($use_zoom) {
                $data['use_zoom'] = true;

                if ($product_info['image']) {
                    $data['small_image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
                } else {
                    $data['small_image'] = '';
                }

                $data['popup_dimension'] = array(
                    'width' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'),
                    'height' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')
                );

                $data['thumb_dimension'] = array(
                    'width' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'),
                    'height' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')
                );

                $data['small_dimension'] = array(
                    'width' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'),
                    'height' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')
                );

                if(isset($this->config->get('module_ptcontrolpanel_zoom_type')[$store_id])) {
                    $zoom_type = $this->config->get('module_ptcontrolpanel_zoom_type')[$store_id];
                } else {
                    $zoom_type = 'outer';
                }

                if(isset($this->config->get('module_ptcontrolpanel_zoom_space')[$store_id])) {
                    $zoom_space = $this->config->get('module_ptcontrolpanel_zoom_space')[$store_id];
                } else {
                    $zoom_space = '0';
                }

                if(isset($this->config->get('module_ptcontrolpanel_zoom_title')[$store_id])) {
                    $zoom_title = (int) $this->config->get('module_ptcontrolpanel_zoom_title')[$store_id];
                } else {
                    $zoom_title = 0;
                }

                $data['product_zoom_settings'] = array(
                    'type' => $zoom_type,
                    'space' => $zoom_space,
                    'title' => $zoom_title
                );
            } else {
                $data['use_zoom'] = false;
            }

            $use_swatches = (int) $this->config->get('module_ptcontrolpanel_use_swatches')[$store_id];

            if($use_swatches) {
                $data['use_swatches'] = true;
                $data['icon_swatches_width'] = $this->config->get('module_ptcontrolpanel_swatches_width')[$store_id];
                $data['icon_swatches_height'] = $this->config->get('module_ptcontrolpanel_swatches_height')[$store_id];
                $data['swatches_option'] = $this->config->get('module_ptcontrolpanel_swatches_option')[$store_id];
            } else {
                $data['use_swatches'] = false;
            }

            $data['heading_title'] = $product_info['name'];

            $data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
            $data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

            $this->load->model('catalog/review');

            $data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

            $data['product_id'] = (int) $product_id;
            $data['manufacturer'] = $product_info['manufacturer'];
            $data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
            $data['model'] = $product_info['model'];
            $data['reward'] = $product_info['reward'];
            $data['points'] = $product_info['points'];
            $data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

            if ($product_info['quantity'] <= 0) {
                $data['stock'] = $product_info['stock_status'];
            } elseif ($this->config->get('config_stock_display')) {
                $data['stock'] = $product_info['quantity'];
            } else {
                $data['stock'] = $this->language->get('text_instock');
            }

            $this->load->model('tool/image');

            if ($product_info['image']) {
                $data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
            } else {
                $data['popup'] = '';
            }

            if ($product_info['image']) {
                $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
            } else {
                $data['thumb'] = '';
            }

            $data['images'] = array();

            $results = $this->model_catalog_product->getProductImages($product_id);

            foreach ($results as $result) {
                $data['images'][] = array(
                    'product_option_value_id' => $result['product_option_value_id'],
                    'product_image_option' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')),
                    'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
                    'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
                );
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $data['price'] = false;
            }

            if ((float)$product_info['special']) {
                $data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $data['special'] = false;
            }

            if ($this->config->get('config_tax')) {
                $data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
            } else {
                $data['tax'] = false;
            }

            $discounts = $this->model_catalog_product->getProductDiscounts($product_id);

            $data['discounts'] = array();

            foreach ($discounts as $discount) {
                $data['discounts'][] = array(
                    'quantity' => $discount['quantity'],
                    'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
                );
            }

            $data['options'] = array();

            foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
                $product_option_value_data = array();

                foreach ($option['product_option_value'] as $option_value) {
                    if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                        if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                            $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                        } else {
                            $price = false;
                        }

                        $product_option_value_data[] = array(
                            'product_option_value_id' => $option_value['product_option_value_id'],
                            'option_value_id'         => $option_value['option_value_id'],
                            'name'                    => $option_value['name'],
                            'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
                            'price'                   => $price,
                            'price_prefix'            => $option_value['price_prefix']
                        );
                    }
                }

                $data['options'][] = array(
                    'product_option_id'    => $option['product_option_id'],
                    'product_option_value' => $product_option_value_data,
                    'option_id'            => $option['option_id'],
                    'name'                 => $option['name'],
                    'type'                 => $option['type'],
                    'value'                => $option['value'],
                    'required'             => $option['required']
                );
            }

            if ($product_info['minimum']) {
                $data['minimum'] = $product_info['minimum'];
            } else {
                $data['minimum'] = 1;
            }

            $data['review_status'] = $this->config->get('config_review_status');

            if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
                $data['review_guest'] = true;
            } else {
                $data['review_guest'] = false;
            }

            if ($this->customer->isLogged()) {
                $data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
            } else {
                $data['customer_name'] = '';
            }

            $data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
            $data['rating'] = (int)$product_info['rating'];

            // Captcha
            if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
                $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
            } else {
                $data['captcha'] = '';
            }

            $data['share'] = $this->url->link('product/product', 'product_id=' . (int) $product_id);
			
			$data['product_link'] = $this->url->link('product/product', 'product_id=' . (int) $product_id, true);

            $data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product_id);

            $data['tags'] = array();

            if ($product_info['tag']) {
                $tags = explode(',', $product_info['tag']);

                foreach ($tags as $tag) {
                    $data['tags'][] = array(
                        'tag'  => trim($tag),
                        'href' => $this->url->link('product/search', 'tag=' . trim($tag))
                    );
                }
            }

            $data['recurrings'] = $this->model_catalog_product->getProfiles($product_id);

            $this->model_catalog_product->updateViewed($product_id);
        } else {
            $data = false;
        }

        return $data;
    }

    public function container() {
        $this->load->language('plaza/quickview');
        
        if (!empty($_SERVER['HTTPS'])) {
            // SSL connection
            $common_url = str_replace('http://', 'https://', $this->config->get('config_url'));
        } else {
            $common_url = $this->config->get('config_url');
        }

        $store_id = $this->config->get('config_store_id');

        /* Loader Image */
        if(isset($this->config->get('module_ptcontrolpanel_loader_img')[$store_id])) {
            $loader_img = $this->config->get('module_ptcontrolpanel_loader_img')[$store_id];
        } else {
            $loader_img = false;
        }

        if($loader_img) {
            $data['loader_img'] = $common_url . 'image/' . $loader_img;
        } else {
            $data['loader_img'] = $common_url . 'image/plaza/ajax-loader.gif';
        }

        if(isset($this->config->get('module_ptcontrolpanel_module_quickview')[$store_id])) {
            $module_quick_view = (int) $this->config->get('module_ptcontrolpanel_module_quickview')[$store_id];
        } else {
            $module_quick_view = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_cate_quickview')[$store_id])) {
            $category_quick_view = (int) $this->config->get('module_ptcontrolpanel_cate_quickview')[$store_id];
        } else {
            $category_quick_view = 0;
        }

        if($module_quick_view || $category_quick_view) {
            $data['use_quick_view'] = true;
        } else {
            $data['use_quick_view'] = false;
        }

        return $this->load->view('plaza/quickview/qvcontainer', $data);
    }

    public function appendcontainer() {
        $this->response->setOutput($this->container());
    }
}