<?php
class ControllerExtensionModulePtmenu extends Controller
{
    public function index($setting) {
        $this->load->language('plaza/module/ptmenu');

        $this->load->model('plaza/ultimatemenu');
        $this->load->model('tool/image');
        $this->load->model('localisation/language');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');

        $data = array();

        $data['warning'] = false;

        $module_id = rand(0, 10000);
        $data['module_id'] = $module_id;

        $data['items'] = array();

        $menu_id = $setting['menu'];

        $menu = $this->model_plaza_ultimatemenu->getMenuById($menu_id);

        $data['menu_type'] = $menu['menu_type'];

        if($menu) {
            if($menu['status']) {
                $top_items = $this->model_plaza_ultimatemenu->getTopItems($menu_id);

                $lang_code = $this->session->data['language'];

                $lang = $this->model_plaza_ultimatemenu->getLanguageByCode($lang_code);

                foreach ($top_items as $top_item) {
                    $sub_items_lv2 = array();

                    $sub_items2 = $this->model_plaza_ultimatemenu->getSubItems($top_item['menu_item_id'], '2');

                    foreach ($sub_items2 as $sub_item2) {
                        $sub_items_lv3 = array();

                        $sub_items3 = $this->model_plaza_ultimatemenu->getSubItems($sub_item2['sub_menu_item_id'], '3');

                        foreach ($sub_items3 as $sub_item3) {
                            $third_title = $this->model_plaza_ultimatemenu->getSubItemDescriptionById($sub_item3['sub_menu_item_id']);

                            if($sub_item3['status']) {
                                $third_status = true;
                            } else {
                                $third_status = false;
                            }

                            if(isset($third_title[$lang['language_id']])) {
                                $title = $third_title[$lang['language_id']];
                            } else {
                                $title = 'Third Level Item';
                            }

                            $sub_items_lv3[] = array(
                                'id'    => $sub_item3['sub_menu_item_id'],
                                'level' => $sub_item3['level'],
                                'status' => $third_status,
                                'link' => $this->getLink($sub_item3['link']),
                                'position' => $sub_item3['position'],
                                'title' => $title,
                            );
                        }

                        $second_title = $this->model_plaza_ultimatemenu->getSubItemDescriptionById($sub_item2['sub_menu_item_id']);

                        if($sub_item2['status']) {
                            $second_status = true;
                        } else {
                            $second_status = false;
                        }

                        if(isset($second_title[$lang['language_id']])) {
                            $title = $second_title[$lang['language_id']];
                        } else {
                            $title = 'Second Level Item';
                        }

                        $sub_items_lv2[] = array(
                            'id'    => $sub_item2['sub_menu_item_id'],
                            'level' => $sub_item2['level'],
                            'status' => $second_status,
                            'link' => $this->getLink($sub_item2['link']),
                            'position' => $sub_item2['position'],
                            'title' => $title,
                            'sub_items' => $sub_items_lv3
                        );
                    }

                    $top_item_title = $this->model_plaza_ultimatemenu->getTopItemDescriptionById($top_item['menu_item_id']);

                    if(isset($top_item_title[$lang['language_id']])) {
                        $top_level_title = $top_item_title[$lang['language_id']];
                    } else {
                        $top_level_title = 'Top Item';
                    }

                    if($top_item['status']) {
                        $top_item_status = true;
                    } else {
                        $top_item_status = false;
                    }

                    if($top_item['has_title']) {
                        $top_item_has_title = true;
                    } else {
                        $top_item_has_title = false;
                    }

                    if($top_item['has_link']) {
                        $top_item_has_link = true;
                    } else {
                        $top_item_has_link = false;
                    }

                    if($top_item['has_child']) {
                        $top_item_has_child = true;
                    } else {
                        $top_item_has_child = false;
                    }

                    if($top_item['icon']) {
                        $icon = $this->model_tool_image->resize($top_item['icon'], 15, 15);
                    } else {
                        $icon = false;
                    }

                    if($top_item['sub_menu_content']) {
                        $sub_content = json_decode($top_item['sub_menu_content'], true);
                    } else {
                        $sub_content = false;
                    }

                    if($top_item['sub_menu_content_columns']) {
                        $column = (int) $top_item['sub_menu_content_columns'];
                        if($column == 5) {
                            $cols = false;
                        } else {
                            $cols = 12 / $column;
                        }

                    } else {
                        $cols = 12;
                    }

                    if($top_item['category_id']) {
                        $top_category_info = $this->model_catalog_category->getCategory($top_item['category_id']);

                        if($top_category_info && $top_item_status) {
                            $top_item_status = true;
                        } else {
                            $top_item_status = false;
                        }
                    }

                    $sub_menu_content = array();

                    if($sub_content) {
                        foreach ($sub_content as $sub_type => $widgets) {
                            $category_widgets = array();

                            if($sub_type == "category") {
                                if($top_item_status) {
                                    if($widgets) {
                                        foreach ($widgets as $widget) {
                                            $category_id = $widget['category_id'];
                                            $category_info = $this->model_catalog_category->getCategory($category_id);

                                            if ($category_info) {
                                                $type = $widget['type'];
                                                $title = $category_info['name'];
                                                $link = $this->url->link('product/category', 'path=' . $top_item['category_id'] . '_' . $category_id, true);
                                                $w_cols = $widget['cols'];

                                                if($widget['show_image']) {
                                                    if ($category_info['image']) {
                                                        $image = $this->model_tool_image->resize($category_info['image'], 100, 100);
                                                    } else {
                                                        $image = false;
                                                    }
                                                } else {
                                                    $image = false;
                                                }

                                                $children = array();

                                                if($widget['show_child']) {
                                                    $results = $this->model_catalog_category->getCategories($category_id);

                                                    foreach ($results as $result) {
                                                        $children[] = array(
                                                            'title' => $result['name'],
                                                            'link' => $this->url->link('product/category', 'path=' . $top_item['category_id'] . '_' . $category_id . '_' . $result['category_id'], true)
                                                        );
                                                    }
                                                }

                                                $sub_menu_content['category'][] = array(
                                                    'id'        => $category_id,
                                                    'title'     => $title,
                                                    'link'      => $link,
                                                    'cols'      => $w_cols,
                                                    'image'     => $image,
                                                    'children'  => $children
                                                );
                                            }
                                        }
                                    }
                                }
                            }

                            $widget_widgets = array();

                            if($sub_type == "widget") {
                                if($top_item_status) {
                                    if($widgets) {
                                        foreach ($widgets as $widget) {
                                            if($widget['type'] == "category") {
                                                $category_id = $widget['category_id'];
                                                $category_info = $this->model_catalog_category->getCategory($category_id);

                                                if ($category_info) {
                                                    $title = $category_info['name'];
                                                    $link = $this->url->link('product/category', 'path=' . $category_id, true);
                                                    $w_cols = $widget['cols'];

                                                    if($widget['show_image']) {
                                                        if ($category_info['image']) {
                                                            $image = $this->model_tool_image->resize($category_info['image'], 100, 100);
                                                        } else {
                                                            $image = false;
                                                        }
                                                    } else {
                                                        $image = false;
                                                    }

                                                    $children = array();

                                                    if($widget['show_child']) {
                                                        $results = $this->model_catalog_category->getCategories($category_id);

                                                        foreach ($results as $result) {
                                                            $children[] = array(
                                                                'title' => $result['name'],
                                                                'link' => $this->url->link('product/category', 'path=' . $category_id . '_' . $result['category_id'], true)
                                                            );
                                                        }
                                                    }

                                                    $sub_menu_content['widget'][] = array(
                                                        'type'      => $widget['type'],
                                                        'title'     => $title,
                                                        'link'      => $link,
                                                        'cols'      => $w_cols,
                                                        'image'     => $image,
                                                        'children'  => $children
                                                    );
                                                }
                                            }

                                            if($widget['type'] == 'html') {
                                                if($widget['show_title']) {
                                                    if(isset($widget['name'][$lang['language_id']])) {
                                                        $title = $widget['name'][$lang['language_id']];
                                                    } else {
                                                        $title = 'Widget HTML';
                                                    }
                                                } else {
                                                    $title = false;
                                                }

                                                $w_cols = $widget['cols'];

                                                if(isset($widget['content'][$lang['language_id']])) {
                                                    $html_content = html_entity_decode($widget['content'][$lang['language_id']], ENT_QUOTES, 'UTF-8');
                                                } else {
                                                    $html_content = '';
                                                }

                                                $sub_menu_content['widget'][] = array(
                                                    'type'      => $widget['type'],
                                                    'title'     => $title,
                                                    'cols'      => $w_cols,
                                                    'content'   => $html_content
                                                );
                                            }

                                            if($widget['type'] == 'product') {
                                                $product_id = $widget['product_id'];
                                                $product_info = $this->model_catalog_product->getProduct($product_id);

                                                if($product_info) {
                                                    $w_cols = $widget['cols'];
                                                    $title = $product_info['name'];

                                                    if($widget['show_image']) {
                                                        if ($product_info['image']) {
                                                            $image = $this->model_tool_image->resize($product_info['image'], 100, 100);
                                                        } else {
                                                            $image = false;
                                                        }
                                                    }

                                                    $link = $this->url->link('product/product', '&product_id=' . $product_id, true);

                                                    $sub_menu_content['widget'][] = array(
                                                        'type'      => $widget['type'],
                                                        'title'     => $title,
                                                        'link'      => $link,
                                                        'cols'      => $w_cols,
                                                        'image'     => $image
                                                    );
                                                }
                                            }

                                            if($widget['type'] == 'link') {
                                                if(isset($widget['name'][$lang['language_id']])) {
                                                    $title = $widget['name'][$lang['language_id']];
                                                } else {
                                                    $title = "Widget Link";
                                                }

                                                $sub_menu_content['widget'][] = array(
                                                    'type'      => $widget['type'],
                                                    'title'     => $title,
                                                    'cols'      => $widget['cols'],
                                                    'link'      => $widget['link']
                                                );
                                            }
                                        }
                                    }
                                }
                            }

//                            $sub_menu_content['category'] = $category_widgets;
//                            $sub_menu_content['widget'] = $widget_widgets;
                        }


                    }



                    if(isset($top_item['category_id']) && $top_item['sub_menu_content_type'] == 'category') {
                        $top_link = $this->url->link('product/category', 'path=' . $top_item['category_id'], true);
                    } else {
                        $top_link = $this->getLink($top_item['link']);
                    }
					
                    $data['items'][] = array(
                        'id'    => $top_item['menu_item_id'],
                        'sub_items' => $sub_items_lv2,
                        'status' => $top_item_status,
                        'has_title' => $top_item_has_title,
                        'has_link' => $top_item_has_link,
                        'has_child' => $top_item_has_child,
                        'category_id' => $top_item['category_id'],
                        'link' => $top_link,
                        'icon' => $icon,
                        'item_align' => $top_item['item_align'],
                        'sub_menu_type' => $top_item['sub_menu_type'],
                        'sub_menu_content_type' => $top_item['sub_menu_content_type'],
                        'sub_menu_content_width' => $top_item['sub_menu_content_width'],
                        'sub_menu_content_columns' => $cols,
                        'sub_menu_content' => $sub_menu_content,
                        'title' => $top_level_title
                    );
                }
            } else {
                $data['warning'] = true;
            }
        } else {
            $data['warning'] = true;
        }

//        echo "<pre>"; var_dump($data['items']);die;

        $data['menu_setting'] = array(
            'name'                          => $setting['name'],
            'status'                        => $setting['status'],
            'effect'                        => $setting['effect'],  // js

            'menu_bg'                       => '#' . $setting['menu_bg'],
            'menu_text_color'               => '#' . $setting['menu_text_color'],
            'menu_pd_top'                   => $setting['menu_pd_top'],
            'menu_pd_right'                 => $setting['menu_pd_right'],
            'menu_pd_bottom'                => $setting['menu_pd_bottom'],
            'menu_pd_left'                  => $setting['menu_pd_left'],
            'item_bg'                       => '#' . $setting['item_bg'],
            'item_bg_hover'                 => '#' . $setting['item_bg_hover'],
            'item_font_color'               => '#' . $setting['item_font_color'],
            'item_font_size'                => $setting['item_font_size'],
            'item_font_transform'           => $setting['item_font_transform'],
            'item_font_weight'              => $setting['item_font_weight'],
            'item_font_color_hover'         => '#' . $setting['item_font_color_hover'],
            'item_font_weight_hover'        => $setting['item_font_weight_hover'],
            'item_pd_top'                   => $setting['item_pd_top'],
            'item_pd_right'                 => $setting['item_pd_right'],
            'item_pd_bottom'                => $setting['item_pd_bottom'],
            'item_pd_left'                  => $setting['item_pd_left'],
            'item_show'                     => (int) $setting['item_show'],
            'mega_menu_bg'                  => '#' . $setting['mega_menu_bg'],
            'mega_sub_item_font_color'               => '#' . $setting['mega_sub_item_font_color'],
            'mega_sub_item_font_size'               => $setting['mega_sub_item_font_size'],
            'mega_sub_item_font_transform'               => $setting['mega_sub_item_font_transform'],
            'mega_sub_item_font_weight'               => $setting['mega_sub_item_font_weight'],
            'mega_sub_item_hover_font_color'               => '#' . $setting['mega_sub_item_hover_font_color'],
            'mega_sub_item_hover_font_weight'               => $setting['mega_sub_item_hover_font_weight'],
            'mega_sub_sub_item_font_color'               => '#' . $setting['mega_sub_sub_item_font_color'],
            'mega_sub_sub_item_font_size'               => $setting['mega_sub_sub_item_font_size'],
            'mega_sub_sub_item_font_transform'               => $setting['mega_sub_sub_item_font_transform'],
            'mega_sub_sub_item_font_weight'               => $setting['mega_sub_sub_item_font_weight'],
            'mega_sub_sub_item_hover_font_color'               => '#' . $setting['mega_sub_sub_item_hover_font_color'],
            'mega_sub_sub_item_hover_font_weight'               => $setting['mega_sub_sub_item_hover_font_weight'],
            'mega_menu_pd_top'              => $setting['mega_menu_pd_top'],
            'mega_menu_pd_right'            => $setting['mega_menu_pd_right'],
            'mega_menu_pd_bottom'           => $setting['mega_menu_pd_bottom'],
            'mega_menu_pd_left'             => $setting['mega_menu_pd_left'],
            'second_item_bg'                => '#' . $setting['second_item_bg'],
            'second_item_bg_hover'          => '#' . $setting['second_item_bg_hover'],
            'second_item_font_color'        => '#' . $setting['second_item_font_color'],
            'second_item_font_size'         => $setting['second_item_font_size'],
            'second_item_font_transform'    => $setting['second_item_font_transform'],
            'second_item_font_weight'       => $setting['second_item_font_weight'],
            'second_item_font_color_hover'  => '#' . $setting['second_item_font_color_hover'],
            'second_item_font_weight_hover' => $setting['second_item_font_weight_hover'],
            'third_item_bg'                 => '#' . $setting['third_item_bg'],
            'third_item_bg_hover'           => '#' . $setting['third_item_bg_hover'],
            'third_item_font_color'         => '#' . $setting['third_item_font_color'],
            'third_item_font_size'          => $setting['third_item_font_size'],
            'third_item_font_transform'     => $setting['third_item_font_transform'],
            'third_item_font_weight'        => $setting['third_item_font_weight'],
            'third_item_font_color_hover'   => '#' . $setting['third_item_font_color_hover'],
            'third_item_font_weight_hover'  => $setting['third_item_font_weight_hover'],
        );

        $this->document->addScript('catalog/view/javascript/plaza/ultimatemenu/menu.js');

        return $this->load->view('plaza/module/ptmenu', $data);
    }

    public function getLink($url) {
        $parts = explode('?', $url);

        if(isset($parts[1])) {
            $params_path = $parts[1];

            if($params_path) {
                $params = explode('&amp;', $params_path);

                $route_param = $params[0];

                $route = explode('=', $route_param)[1];

                $params_attr = '';

                if(isset($params[1])) {
                    for($i = 1; $i <= (count($params) - 1); $i++) {
                        if($i == 1) {
                            $params_attr = $params[1];
                        } else {
                            $params_attr .= "&" . $params[$i];
                        }
                    }
                }

                if($params_attr == '') {
                    $link = $this->url->link($route);
                } else {
                    $link = $this->url->link($route, $params_attr);
                }
            }
            return $link;
        } else {
            return $url;
        }
    }
}