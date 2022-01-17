<?php
class ControllerExtensionModulePtfeaturedcate extends Controller
{
    public function index($setting) {
        $this->load->model('plaza/catalog');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('plaza/rotateimage');
        $this->load->language('plaza/module/ptfeaturedcate');

        $data = array();

        /* Module Settings */
        if(isset($setting['status']) && $setting['status']) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }

        if(isset($setting['type']) && $setting['type']) {
            $data['type'] = $setting['type'];;
        } else {
            $data['type'] = false;
        }

        /* Slider Settings */
        if(isset($setting['limit']) && $setting['limit']) {
            $limit = (int) $setting['limit'];
        } else {
            $limit = 10;
        }

        if(isset($setting['item']) && $setting['item']) {
            $item = (int) $setting['item'];
        } else {
            $item = 4;
        }

        if(isset($setting['speed']) && $setting['speed']) {
            $speed = (int) $setting['speed'];
        } else {
            $speed = 3000;
        }

        if(isset($setting['autoplay']) && $setting['autoplay']) {
            $autoplay = true;
        } else {
            $autoplay = false;
        }

        if(isset($setting['rows']) && $setting['rows']) {
            $rows = (int) $setting['rows'];
        } else {
            $rows = 1;
        }

        if(isset($setting['shownextback']) && $setting['shownextback']) {
            $nextback = true;
        } else {
            $nextback = false;
        }

        if(isset($setting['shownav']) && $setting['shownav']) {
            $pagination = true;
        } else {
            $pagination = false;
        }

        $data['slide_settings'] = array(
            'items' => $item,
            'autoplay' => $autoplay,
            'shownextback' => $nextback,
            'shownav' => $pagination,
            'speed' => $speed,
            'rows' => $rows
        );

        /* Category Settings */
        if(isset($setting['slider']) && $setting['slider']) {
            $data['use_slider'] = true;
        } else {
            $data['use_slider'] = false;
        }

        if(isset($setting['showcatedes']) && $setting['showcatedes']) {
            $data['show_cate_des'] = true;
        } else {
            $data['show_cate_des'] = false;
        }

        if(isset($setting['showsub']) && $setting['showsub']) {
            $data['show_child'] = true;
        } else {
            $data['show_child'] = false;
        }

        if(isset($setting['showsubnumber']) && $setting['showsubnumber']) {
            $data['child_number'] = (int) $setting['showsubnumber'];
        } else {
            $data['child_number'] = 4;
        }

        if(isset($setting['use_cate_second_image']) && $setting['use_cate_second_image']) {
            $data['use_second_img'] = true;
        } else {
            $data['use_second_img'] = false;
        }

        $data['categories'] = array();

        $_featured_categories = $this->model_plaza_catalog->getFeaturedCategories($limit);

        if ($_featured_categories) {
            foreach ($_featured_categories as $_category) {
                $sub_categories = array();

                $sub_data_categories = $this->model_catalog_category->getCategories($_category['category_id']);

                foreach($sub_data_categories as $sub_category) {
                    $filter_data = array('filter_category_id' => $sub_category['category_id'], 'filter_sub_category' => true);

                    $sub_categories[] = array(
                        'category_id' => $sub_category['category_id'],
                        'name' => $sub_category['name'],
                        'href' => $this->url->link('product/category', 'path=' . $_category['category_id'] . '_' . $sub_category['category_id'])
                    );
                }

                if ($_category['secondary_image']) {
                    $secondary_image = $this->model_tool_image->resize($_category['secondary_image'], $setting['width'], $setting['height']);
                } else {
                    $secondary_image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
                }
                
                if ($_category['description']) {
                    $description = utf8_substr(strip_tags(html_entity_decode($_category['description'], ENT_QUOTES, 'UTF-8')), 0, 80) . '..';
                } else {
                    $description = false;
                }
				$filter_data = array(
					'filter_category_id' => $_category['category_id']
				);
				$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
                $data['categories'][] = array(
                    'children'			=> $sub_categories,
                    'category_id'  		=> $_category['category_id'],
                    'secondary_image'   => $secondary_image,
                    'name'        		=> $_category['name'],
					'product_total'		=> $product_total,
                    'description' 		=> $description,
                    'href'        		=> $this->url->link('product/category', 'path=' . $_category['category_id']),
                );
            }
        }

        if ($data['categories']) {
            return $this->load->view('plaza/module/ptfeaturedcate', $data);
        }
    }
}