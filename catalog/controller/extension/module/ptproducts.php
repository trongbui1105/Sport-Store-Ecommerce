<?php
class ControllerExtensionModulePtproducts extends Controller
{
    public function index($setting) {
        $this->load->language('plaza/module/ptproducts');

        $this->load->model('catalog/product');
        $this->load->model('plaza/catalog');
        $this->load->model('tool/image');
        $this->load->model('plaza/rotateimage');
        $this->load->model('plaza/swatches');

        $data = array();

        $data['module_id'] = $setting['module_id'];

        /* Module Settings */
        $lang_code = $this->session->data['language'];
        
        if(isset($setting['module_title'])) {
            if(isset($setting['module_title'][$lang_code])) {
                $data['title'] = $setting['module_title'][$lang_code];
            } else {
                $data['title'] = false;
            }
        } else {
            $data['title'] = false;
        }

        if(isset($setting['module_type'])) {
            $data['module_type'] = $setting['module_type'];
        } else {
            $data['module_type'] = '';
        }

        if(isset($setting['show_module_description'])) {
            $show_module_des = (int) $setting['show_module_description'];
            if($show_module_des) {
                if(isset($setting['module_description'])) {
                    if(isset($setting['module_description'][$lang_code])) {
                        $data['module_des'] = html_entity_decode($setting['module_description'][$lang_code], ENT_QUOTES, 'UTF-8');
                    } else {
                        $data['module_des'] = false;
                    }
                } else {
                    $data['module_des'] = false;
                }
            } else {
                $data['module_des'] = false;
            }
        } else {
            $data['module_des'] = false;
        }

        // Layout Settings
        if(isset($setting['layout_type'])) {
            $data['layout_type'] = $setting['layout_type'];
        } else {
            $data['layout_type'] = '';
        }

        if(isset($setting['product_layout_type'])) {
            $product_layout_type = $setting['product_layout_type'];
        } else {
            $product_layout_type = 'grid';
        }
        $data['product_layout_type'] = $product_layout_type;

        if(isset($setting['layout_classname'])) {
            $data['layout_classname'] = $setting['layout_classname'];
        } else {
            $data['layout_classname'] = '';
        }

        // Slider Settings
        $slider_width = $setting['slider_width'];

        $slider_height = $setting['slider_height'];

        if (isset($setting['auto']) && $setting['auto']) {
            $auto = true;
        } else {
            $auto = false;
        }

        if (isset($setting['item'])) {
            if($setting['item']['desktop']) {
                $desktop_item = (int) $setting['item']['desktop'];
            } else {
                $desktop_item = 4;
            }

            if($setting['item']['laptop']) {
                $laptop_item = (int) $setting['item']['laptop'];
            } else {
                $laptop_item = 4;
            }

            if($setting['item']['tablet']) {
                $tablet_item = (int) $setting['item']['tablet'];
            } else {
                $tablet_item = 3;
            }
			
			if($setting['item']['s_tablet']) {
                $s_tablet_item = (int) $setting['item']['s_tablet'];
            } else {
                $s_tablet_item = 3;
            }

            if($setting['item']['mobile']) {
                $mobile_item = (int) $setting['item']['mobile'];
            } else {
                $mobile_item = 2;
            }
			
			if($setting['item']['s_mobile']) {
                $s_mobile_item = (int) $setting['item']['s_mobile'];
            } else {
                $s_mobile_item = 2;
            }

            $item = array(
                'desktop' => $desktop_item,
                'laptop'  => $laptop_item,
                'tablet'  => $tablet_item,
                's_tablet'  => $s_tablet_item,
                'mobile'  => $mobile_item,
                's_mobile'  => $s_mobile_item
            );
        } else {
            $item = array(
                'desktop' => 4,
                'laptop'  => 4,
                'tablet'  => 3,
                's_tablet'  => 3,
                'mobile'  => 2,
                's_mobile'  => 2
            );
        }

        if (isset($setting['row'])) {
            if($setting['row']['desktop']) {
                $desktop_row = (int) $setting['row']['desktop'];
            } else {
                $desktop_row = 1;
            }

            if($setting['row']['laptop']) {
                $laptop_row = (int) $setting['row']['laptop'];
            } else {
                $laptop_row = 1;
            }

            if($setting['row']['tablet']) {
                $tablet_row = (int) $setting['row']['tablet'];
            } else {
                $tablet_row = 1;
            }
			
			if($setting['row']['s_tablet']) {
                $s_tablet_row = (int) $setting['row']['s_tablet'];
            } else {
                $s_tablet_row = 1;
            }

            if($setting['row']['mobile']) {
                $mobile_row = (int) $setting['row']['mobile'];
            } else {
                $mobile_row = 1;
            }
			
			if($setting['row']['s_mobile']) {
                $s_mobile_row = (int) $setting['row']['s_mobile'];
            } else {
                $s_mobile_row = 1;
            }

            $row = array(
                'desktop' => $desktop_row,
                'laptop'  => $laptop_row,
                'tablet'  => $tablet_row,
                's_tablet'  => $s_tablet_row,
                'mobile'  => $mobile_row,
                's_mobile'  => $s_mobile_row
            );
        } else {
            $row = array(
                'desktop' => 1,
                'laptop'  => 1,
                'tablet'  => 1,
                's_tablet'  => 1,
                'mobile'  => 1,
                's_mobile'  => 1
            );
        }

        if (isset($setting['speed'])) {
            $speed = $setting['speed'];
        } else {
            $speed = 500;
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

        $data['slide'] = array(
            'width' => $slider_width,
            'height' => $slider_height,
            'auto'  => $auto,
            'item_desktop' => $item['desktop'],
            'item_laptop' => $item['laptop'],
            'item_tablet' => $item['tablet'],
            'item_s_tablet' => $item['s_tablet'],
            'item_mobile' => $item['mobile'],
            'item_s_mobile' => $item['s_mobile'],
            'row_desktop' => $row['desktop'],
            'row_laptop' => $row['laptop'],
            'row_tablet' => $row['tablet'],
            'row_s_tablet' => $row['s_tablet'],
            'row_mobile' => $row['mobile'],
            'row_s_mobile' => $row['s_mobile'],
            'speed' => $speed,
            'navigation' => $navigation,
            'pagination' => $pagination
        );

        /* Catalog Settings */
        if(isset($setting['show_price'])) {
            $show_price = (int) $setting['show_price'];
        } else {
            $show_price = 0;
        }

        if(isset($setting['show_cart'])) {
            $show_cart = (int) $setting['show_cart'];
        } else {
            $show_cart = 0;
        }

        if(isset($setting['show_wishlist'])) {
            $show_wishlist = (int) $setting['show_wishlist'];
        } else {
            $show_wishlist = 0;
        }

        if(isset($setting['show_compare'])) {
            $show_compare = (int) $setting['show_compare'];
        } else {
            $show_compare = 0;
        }

        if(isset($setting['show_countdown'])) {
            $show_countdown = (int) $setting['show_countdown'];
        } else {
            $show_countdown = 0;
        }

        if(isset($setting['show_hover_image'])) {
            $show_hover_image = (int) $setting['show_hover_image'];
        } else {
            $show_hover_image = 0;
        }

        if(isset($setting['show_swatches_image'])) {
            $show_swatches_image = (int) $setting['show_swatches_image'];
        } else {
            $show_swatches_image = 0;
        }

        if(isset($setting['show_quickview'])) {
            $show_quickview = (int) $setting['show_quickview'];
        } else {
            $show_quickview = 0;
        }

        if(isset($setting['show_product_description'])) {
            $show_product_description = (int) $setting['show_product_description'];
        } else {
            $show_product_description = 0;
        }

        if(isset($setting['show_label'])) {
            $show_label = (int) $setting['show_label'];
        } else {
            $show_label = 0;
        }

        $new_results = $this->model_catalog_product->getLatestProducts(10);

        $catalog_settings = array(
            'show_price' => $show_price,
            'show_cart' => $show_cart,
            'show_wishlist' => $show_wishlist,
            'show_compare' => $show_compare,
            'show_countdown' => $show_countdown,
            'show_quickview' => $show_quickview,
            'show_hover_image' => $show_hover_image,
            'show_swatches_image' => $show_swatches_image,
            'show_description' => $show_product_description,
            'show_label' => $show_label,
            'new_results' => $new_results,
            'slider_width' => $slider_width,
            'slider_height' => $slider_height
        );

        // Products Collection
        if(isset($setting['limit'])) {
            $limit = (int) $setting['limit'];
        } else {
            $limit = 10;
        }

        // Single Tab
        $data['single_products'] = array();

        $single_collection_type = $setting['single_product_collection'];

        if($single_collection_type == 'specified') {
            if(isset($setting['single_specified_products'])) {
                $single_specified_products = $setting['single_specified_products'];
            } else {
                $single_specified_products = false;
            }

            if($single_specified_products) {
                $products = array_slice($single_specified_products, 0, $limit);

                foreach ($products as $pid) {
                    $product = $this->getProductData($pid, $catalog_settings, $product_layout_type, $data['module_id']);

                    if($product) {
                        $data['single_products'][] = $product;
                    }
                }
            }
        }

        if($single_collection_type == 'category') {
            $single_category_id = (int) $setting['single_category'];
            $single_category_product_collection = $setting['single_category_product_type'];

            if($single_category_product_collection == 'all') {
                $filter_data = array(
                    'filter_category_id' => $single_category_id,
                    'start' => 0,
                    'limit' => $limit
                );

                $results = $this->model_catalog_product->getProducts($filter_data);
                if($results) {
                    foreach ($results as $result) {
                        $product = $this->getProductData($result['product_id'], $catalog_settings, $product_layout_type, $data['module_id']);

                        if($product) {
                            $data['single_products'][] = $product;
                        }
                    }
                }
            }

            if($single_category_product_collection == 'specified') {
                if(isset($setting['single_category_products'])) {
                    $single_category_products = $setting['single_category_products'];
                } else {
                    $single_category_products = false;
                }

                if($single_category_products) {
                    $products = array_slice($single_category_products, 0, $limit);

                    foreach ($products as $pid) {
                        $product = $this->getProductData($pid, $catalog_settings, $product_layout_type, $data['module_id']);

                        if($product) {
                            $data['single_products'][] = $product;
                        }
                    }
                }
            }

            if($single_category_product_collection == 'special') {
                if(isset($setting['single_category_product_special_type']) && $setting['single_category_product_special_type'] != '') {
                    $single_category_product_special_type = $setting['single_category_product_special_type'];
                } else {
                    $single_category_product_special_type = false;
                }

                if($single_category_product_special_type) {
                    $results = array();

                    if($single_category_product_special_type == 'mostviewed') {
                        $results = $this->model_plaza_catalog->getMostViewedByCategory($limit, $single_category_id);
                    }

                    if($single_category_product_special_type == 'bestseller') {
                        $results = $this->model_plaza_catalog->getBestSellerProductsByCategory($limit, $single_category_id);
                    }

                    if($single_category_product_special_type == 'special') {
                        $results = $this->model_plaza_catalog->getProductSpecialsByCategory($limit, $single_category_id);
                    }

                    if($single_category_product_special_type == 'latest') {
                        $results = $this->model_plaza_catalog->getLatestProductsByCategory($limit, $single_category_id);
                    }

                    if($single_category_product_special_type == 'random') {
                        $results = $this->model_plaza_catalog->getRandomByCategory($limit, $single_category_id);
                    }

                    if($results) {
                        foreach ($results as $result) {
                            $product = $this->getProductData($result['product_id'], $catalog_settings, $product_layout_type, $data['module_id']);
                            
                            if($product) {
                                $data['single_products'][] = $product;
                            }
                        }
                    }
                }
            }
        }

        if($single_collection_type == 'special') {
            if(isset($setting['single_product_special_type']) && $setting['single_product_special_type'] != '') {
                $single_product_special_type = $setting['single_product_special_type'];
            } else {
                $single_product_special_type = false;
            }

            if($single_product_special_type) {
                $results = array();

                if($single_product_special_type == 'mostviewed') {
                    $results = $this->model_plaza_catalog->getMostViewed($limit);
                }

                if($single_product_special_type == 'bestseller') {
                    $results = $this->model_catalog_product->getBestSellerProducts($limit);
                }

                if($single_product_special_type == 'special') {
                    $results = $this->model_catalog_product->getProductSpecials(array('start' => 0, 'limit' => $limit));
                }

                if($single_product_special_type == 'latest') {
                    $results = $this->model_catalog_product->getLatestProducts($limit);
                }

                if($single_product_special_type == 'random') {
                    $results = $this->model_plaza_catalog->getRandom($limit);
                }

                if($results) {
                    foreach ($results as $result) {
                        $product = $this->getProductData($result['product_id'], $catalog_settings, $product_layout_type, $data['module_id']);
                        
                        if($product) {
                            $data['single_products'][] = $product;
                        }
                    }
                }
            }
        }

        $data['single_first_product'] = $this->getFirstProducts($data['single_products']);
        $data['single_products_except_first'] = $this->getProductsExceptFirst($data['single_products']);
        
        if(!empty($setting['single_image_width'])) {
            $single_image_width = (int) $setting['single_image_width'];
        } else {
            $single_image_width = 100;
        }
        
        if(!empty($setting['single_image_height'])) {
            $single_image_height = (int) $setting['single_image_height'];
        } else {
            $single_image_height = 100;
        }

        if(!empty($setting['single_image']) && is_file(DIR_IMAGE . $setting['single_image'])) {
            $data['single_image'] = $this->model_tool_image->resize($setting['single_image'], $single_image_width, $single_image_height);
        } else {
            $data['single_image'] = $this->model_tool_image->resize('no_image.png', $single_image_width, $single_image_height);
        }

        if(!empty($setting['single_image_link']) && $setting['single_image_link'] !== "") {
            $data['single_image_link'] = $setting['single_image_link'];
        } else {
            $data['single_image_link'] = false;
        }

        // Multi Tabs
        $product_tabs = array();
        if(!empty($setting['tabs'])) {
            $tabs = $setting['tabs'];
            $tab_count = 0;
            foreach ($tabs as $tab) {
                $module_tab_id = $data['module_id'] . '-' . $tab_count;

                if(!empty($tab['title'])) {
                    if(!empty($tab['title'][$lang_code])) {
                        $title = $tab['title'][$lang_code];
                    } else {
                        $title = false;
                    }
                } else {
                    $title = false;
                }

                if(!empty($tab['title_image']) && is_file(DIR_IMAGE . $tab['title_image'])) {
                    $title_image = $this->model_tool_image->resize($tab['title_image'], 40, 45);
                } else {
                    $title_image = false;
                }

                $tab_products = array();

                $tab_collection_type = $tab['product_collection'];

                if($tab_collection_type == "specified") {
                    if(!empty($tabs['specified_products'])) {
                        $tab_specified_products = $tabs['specified_products'];
                    } else {
                        $tab_specified_products = false;
                    }

                    if($tab_specified_products) {
                        $products = array_slice($tab_specified_products, 0, $limit);

                        foreach ($products as $pid) {
                            $product = $this->getProductData($pid, $catalog_settings, $product_layout_type, $module_tab_id);

                            if($product) {
                                $tab_products[] = $product;
                            }
                        }
                    }
                }

                if($tab_collection_type == "category") {
                    $tab_category_id = $tab['category'];

                    if(!empty($tab['category_product_type'])) {
                        $tab_category_product_type = $tab['category_product_type'];
                    } else {
                        $tab_category_product_type = '';
                    }

                    if($tab_category_product_type == "all") {
                        $filter_data = array(
                            'filter_category_id' => $tab_category_id,
                            'start' => 0,
                            'limit' => $limit
                        );

                        $results = $this->model_catalog_product->getProducts($filter_data);
                        if($results) {
                            foreach ($results as $result) {
                                $product = $this->getProductData($result['product_id'], $catalog_settings, $product_layout_type, $module_tab_id);

                                if($product) {
                                    $tab_products[] = $product;
                                }
                            }
                        }
                    }

                    if($tab_category_product_type == "specified") {
                        if(!empty($tab['category_products'])) {
                            $tab_category_products = $tab['category_products'];
                        } else {
                            $tab_category_products = false;
                        }

                        if($tab_category_products) {
                            $products = array_slice($tab_category_products, 0, $limit);

                            foreach ($products as $pid) {
                                $product = $this->getProductData($pid, $catalog_settings, $product_layout_type, $module_tab_id);

                                if($product) {
                                    $tab_products[] = $product;
                                }
                            }
                        }
                    }

                    if($tab_category_product_type == "special") {
                        if(isset($tab['category_product_special_type']) && $tab['category_product_special_type'] != '') {
                            $tab_category_product_special_type = $tab['category_product_special_type'];
                        } else {
                            $tab_category_product_special_type = false;
                        }

                        if($tab_category_product_special_type) {
                            $results = array();

                            if($tab_category_product_special_type == 'mostviewed') {
                                $results = $this->model_plaza_catalog->getMostViewedByCategory($limit, $tab_category_id);
                            }

                            if($tab_category_product_special_type == 'bestseller') {
                                $results = $this->model_plaza_catalog->getBestSellerProductsByCategory($limit, $tab_category_id);
                            }

                            if($tab_category_product_special_type == 'special') {
                                $results = $this->model_plaza_catalog->getProductSpecialsByCategory($limit, $tab_category_id);
                            }

                            if($tab_category_product_special_type == 'latest') {
                                $results = $this->model_plaza_catalog->getLatestProductsByCategory($limit, $tab_category_id);
                            }

                            if($tab_category_product_special_type == 'random') {
                                $results = $this->model_plaza_catalog->getRandomByCategory($limit, $tab_category_id);
                            }

                            if($results) {
                                foreach ($results as $result) {
                                    $product = $this->getProductData($result['product_id'], $catalog_settings, $product_layout_type, $module_tab_id);

                                    if($product) {
                                        $tab_products[] = $product;
                                    }
                                }
                            }
                        }
                    }
                }

                if($tab_collection_type == "special") {
                    if(isset($tab['product_special_type']) && $tab['product_special_type'] != '') {
                        $tab_product_special_type = $tab['product_special_type'];
                    } else {
                        $tab_product_special_type = false;
                    }

                    if($tab_product_special_type) {
                        $results = array();

                        if($tab_product_special_type == 'mostviewed') {
                            $results = $this->model_plaza_catalog->getMostViewed($limit);
                        }

                        if($tab_product_special_type == 'bestseller') {
                            $results = $this->model_catalog_product->getBestSellerProducts($limit);
                        }

                        if($tab_product_special_type == 'special') {
                            $results = $this->model_catalog_product->getProductSpecials(array('start' => 0, 'limit' => $limit));
                        }

                        if($tab_product_special_type == 'latest') {
                            $results = $this->model_catalog_product->getLatestProducts($limit);
                        }

                        if($tab_product_special_type == 'random') {
                            $results = $this->model_plaza_catalog->getRandom($limit);
                        }

                        if($results) {
                            foreach ($results as $result) {
                                $product = $this->getProductData($result['product_id'], $catalog_settings, $product_layout_type, $module_tab_id);

                                if($product) {
                                    $tab_products[] = $product;
                                }
                            }
                        }
                    }
                }

                if(!empty($tab['image_width'])) {
                    $multi_image_width = (int) $tab['image_width'];
                } else {
                    $multi_image_width = 100;
                }

                if(!empty($tab['image_height'])) {
                    $multi_image_height = (int) $tab['image_height'];
                } else {
                    $multi_image_height = 100;
                }

                if(!empty($tab['image']) && is_file(DIR_IMAGE . $tab['image'])) {
                    $tab_image = $this->model_tool_image->resize($tab['image'], $multi_image_width, $multi_image_height);
                } else {
                    $tab_image = $this->model_tool_image->resize('no_image.png', $multi_image_width, $multi_image_height);
                }

                if(!empty($tab['image_link']) && $tab['image_link'] != '') {
                    $tab_image_link = $tab['image_link'];
                } else {
                    $tab_image_link = false;
                }

                $product_tabs[] = array(
                    'title' => $title,
                    'title_image' => $title_image,
                    'products' => $tab_products,
                    'first_product' => $this->getFirstProducts($tab_products),
                    'products_except_first' => $this->getProductsExceptFirst($tab_products),
                    'image' => array(
                        'src' => $tab_image,
                        'link' => $tab_image_link
                    )
                );

                $tab_count++;
            }
        }

        $data['tabs'] = $product_tabs;
        if($show_countdown) {
            $this->document->addScript('catalog/view/javascript/plaza/countdown/countdown.js');
        }
        
        if($show_swatches_image) {
            $this->document->addScript('catalog/view/javascript/plaza/swatches/swatches.js');
        }

        return $this->load->view('plaza/module/ptproducts', $data);
    }

    public function getFirstProducts($products) {
        if($products) {
            return $products[0];
        } else {
            return false;
        }
    }

    public function getProductsExceptFirst($products) {
        if($products) {
            $total = count($products);
            return $products = array_slice($products, 1, $total);
        } else {
            return false;
        }
    }

    public function getProductData($product_id, $params, $layout, $section_id) {
		$store_id = $this->config->get('config_store_id');
		
		/* Lazy Load */
        if(isset($this->config->get('module_ptcontrolpanel_lazy_load')[$store_id])) {
            $lazy_load = (int) $this->config->get('module_ptcontrolpanel_lazy_load')[$store_id];
        } else {
            $lazy_load = 0;
        }
		
        $new_products = $params['new_results'];
        $width = $params['slider_width'];
        $height = $params['slider_height'];

        $result = $this->model_plaza_catalog->getProduct($product_id);

        if($result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $width, $height);
            } else {
                $image = false;
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }

            if ((float)$result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                $rate_special = round(($result['price'] - $result['special']) / $result['price'] * 100);
            } else {
                $special = false;
                $rate_special = false;
            }

            if ($this->config->get('config_review_status')) {
                $rating = $result['rating'];
            } else {
                $rating = false;
            }

            if($result['description']) {
                $description = utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, 80) . '...';
            } else {
                $description = false;
            }

            if (isset($result['date_start']) && $result['date_start']) {
                $date_start = $result['date_start'];
            } else {
                $date_start = false;
            }

            if(isset($result['date_end']) &&  $result['date_end']) {
                $date_end = $result['date_end'];
            } else {
                $date_end = false;
            }
			
			$sold_number = $this->model_plaza_catalog->getSoldNumber($result['product_id']);

            $is_new = false;
            if ($new_products) {
                foreach($new_products as $new_r) {
                    if($result['product_id'] == $new_r['product_id']) {
                        $is_new = true;
                    }
                }
            }

            if($params['show_hover_image'] == 1) {
                $rotate_image = $this->model_plaza_rotateimage->getProductRotateImage($product_id);

                if($rotate_image) {
                    $rotate_image = $this->model_tool_image->resize($rotate_image, $width, $height);
                } else {
                    $rotate_image = false;
                }
            } else {
                $rotate_image = false;
            }

            $attribute_groups = false;

            $swatches_images = false;

            $options = false;

            if($params['show_swatches_image'] == 1) {
                $is_swatches = false;

                $store_id = $this->config->get('config_store_id');

                $images = $this->model_catalog_product->getProductImages($product_id);

                foreach ($images as $img) {
                    if ($img['product_option_value_id']) {
                        $image_option_id = $this->model_plaza_swatches->getOptionIdByProductOptionValueId($img['product_option_value_id']);

                        if($image_option_id == $this->config->get('module_ptcontrolpanel_swatches_option')[$store_id]) {
                            $is_swatches = true;

                            $swatches_images[] = array(
                                'product_option_value_id' => $img['product_option_value_id'],
                                'image' => $this->model_tool_image->resize($img['image'], $width, $height)
                            );
                        }
                    }
                }

                if($is_swatches) {
                    foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
                        if($option['option_id'] == $this->config->get('module_ptcontrolpanel_swatches_option')[$store_id]) {
                            $product_option_value_data = array();

                            foreach ($option['product_option_value'] as $option_value) {
                                if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                    $product_option_value_data[] = array(
                                        'product_option_value_id' => $option_value['product_option_value_id'],
                                        'option_value_id'         => $option_value['option_value_id'],
                                        'name'                    => $option_value['name'],
                                        'image'                   => $this->model_tool_image->resize($option_value['image'], 15, 15),
                                    );
                                }
                            }

                            $options[] = array(
                                'product_option_id'    => $option['product_option_id'],
                                'product_option_value' => $product_option_value_data,
                                'option_id'            => $option['option_id'],
                                'name'                 => $option['name'],
                                'type'                 => $option['type'],
                                'value'                => $option['value'],
                            );
                        }
                    }
                }
            }
			$gallery_images = false;

            $result_images = $this->model_catalog_product->getProductImages($result['product_id']);

            if($result_images) {
                foreach ($result_images as $img) {
                    $gallery_images[] = array(
                        'popup' => $this->model_tool_image->resize($img['image'], $width, $height),
                        'thumb' => $this->model_tool_image->resize($img['image'], 90, 90)
                    );
                }
            }
            $data = array(
                'product_id'        => $result['product_id'],
                'thumb'   	        => $image,
                'rotate_image'      => $rotate_image,
                'img_width'         => $width,
                'img_height'        => $height,
                'name'    	        => $result['name'],
                'price'   	        => $price,
                'special' 	        => $special,
                'rate_special'      => $rate_special,
                'is_new'            => $is_new,
                'date_start'  	    => $date_start,
                'date_end'    	    => $date_end,
                'rating'            => $rating,
                'description'       => $description,
                'options'           => $options,
				'quantity'      	=> (int) $result['quantity'],
				'sold_number'   	=> $sold_number,
				'total_qty'     	=> (int) $result['quantity'] + $sold_number,
				'gallery_images'    => $gallery_images,
                'swatches_images'   => $swatches_images,
                'attribute_groups'  => $attribute_groups,
                'show_price'        => $params['show_price'],
                'show_cart'         => $params['show_cart'],
                'show_wishlist'     => $params['show_wishlist'],
                'show_compare'      => $params['show_compare'],
                'show_countdown'    => $params['show_countdown'],
                'show_quickview'    => $params['show_quickview'],
                'show_description'  => $params['show_description'],
                'show_label'        => $params['show_label'],
				'lazy_load'			=> $lazy_load,
                'section_id'        => $section_id,
                'layout_type'       => $layout,
                'href'    	        => $this->url->link('product/product', 'product_id=' . $result['product_id'], true),
				'manufacturer' 		=> $result['manufacturer'],
				'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
            );

            $html_content = $this->load->view('plaza/module/ptproducts/content', $data);
			$large_html_content = $this->load->view('plaza/module/ptproducts/large_content', $data);
            $data['html'] = $html_content;
			$data['large_html'] = $large_html_content;
            return $data;
        } else {
            return false;
        }
    }
}