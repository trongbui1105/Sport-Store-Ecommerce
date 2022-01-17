<?php

            require_once(DIR_SYSTEM . 'Mobile_Detect.php');
            
class ControllerCommonColumnLeft extends Controller {
	public function index() {
		$this->load->model('design/layout');

		
            if (isset($this->request->get['route']) && $this->request->get['route'] != "common/home") {
            
			$route = (string)$this->request->get['route'];
		} else {
			
            $store_id = $this->config->get('config_store_id');

            if(isset($this->config->get('module_ptcontrolpanel_responsive_type')[$store_id])) {
                $responsive_type = $this->config->get('module_ptcontrolpanel_responsive_type')[$store_id];
            } else {
                $responsive_type = "";
            }

            if($responsive_type == "specified") {
                $detect = new Mobile_Detect;

                if ( $detect->isTablet() ) {
                    $route = 'common/home';
                } else {
                    if( $detect->isMobile()){
                        $route = 'plaza/responsive/mobile';
                    } else {
                        $route = 'common/home';
                    }
                }
            } else {
                $route = 'common/home';
            }
            
		}

		$layout_id = 0;


       	if ($route == 'product/category') {
			$store_id = $this->config->get('config_store_id');

			if(isset($this->config->get('module_ptcontrolpanel_use_filter')[$store_id])) {
				$use_filter = (int) $this->config->get('module_ptcontrolpanel_use_filter')[$store_id];
			} else {
				$use_filter = 0;
			}

			if(isset($this->config->get('module_ptcontrolpanel_filter_position')[$store_id])) {
				$filter_position = $this->config->get('module_ptcontrolpanel_filter_position')[$store_id];
			} else {
				$filter_position = false;
			}

			if($use_filter && $filter_position == 'left') {
				$data['use_filter'] = true;
			} else {
				$data['use_filter'] = false;
			}

			if($data['use_filter']) {
				$data['filter_section'] = $this->load->controller('plaza/filter');
			}
		} else {
			$data['use_filter'] = false;
		}
            
		if ($route == 'product/category' && isset($this->request->get['path'])) {
			$this->load->model('catalog/category');

			$path = explode('_', (string)$this->request->get['path']);

			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}

		if ($route == 'product/product' && isset($this->request->get['product_id'])) {
			$this->load->model('catalog/product');

			$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
		}

		if ($route == 'information/information' && isset($this->request->get['information_id'])) {
			$this->load->model('catalog/information');

			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
		}

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}

		$this->load->model('setting/module');

		$data['modules'] = array();

		$modules = $this->model_design_layout->getLayoutModules($layout_id, 'column_left');

		foreach ($modules as $module) {
			$part = explode('.', $module['code']);

			if (isset($part[0]) && $this->config->get('module_' . $part[0] . '_status')) {
				$module_data = $this->load->controller('extension/module/' . $part[0]);

				if ($module_data) {
					$data['modules'][] = $module_data;
				}
			}

			if (isset($part[1])) {
				$setting_info = $this->model_setting_module->getModule($part[1]);

				if ($setting_info && $setting_info['status']) {
					$output = $this->load->controller('extension/module/' . $part[0], $setting_info);

					if ($output) {
						$data['modules'][] = $output;
					}
				}
			}
		}

		return $this->load->view('common/column_left', $data);
	}
}
