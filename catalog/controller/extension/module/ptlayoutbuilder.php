<?php
class ControllerExtensionModulePtlayoutbuilder extends Controller
{
    public function index($setting) {
        $this->load->model('setting/module');

        if(!empty($setting['widget'])) {
            $widgets = $setting['widget'];
        } else {
            $widgets = array();
        }

        $data['widgets'] = array();

        foreach($widgets as $main_row) {
            $main_row_info = array();

            foreach($main_row['main_cols'] as $main_col) {
                $main_col_info = array();

                if(isset($main_col['sub_rows']) && $main_col['sub_rows']) {
                    foreach($main_col['sub_rows'] as $sub_row) {
                        $sub_row_info = array();

                        foreach ($sub_row['sub_cols'] as $sub_col) {
                            $sub_col_info = array();
                            if(isset($sub_col['info'])) {
                                foreach ($sub_col['info'] as $modules) {
                                    $module_in_col = array();
                                    foreach ($modules as $module) {
                                        $part = explode('.', $module['code']);

                                        if (isset($part[0]) && $this->config->get('module_' . $part[0] . '_status')) {
                                            $module_data = $this->load->controller('extension/module/' . $part[0]);

                                            if ($module_data) {
                                                $module_in_col[] = $module_data;
                                            }
                                        }

                                        if (isset($part[1])) {
                                            $setting_info = $this->model_setting_module->getModule($part[1]);

                                            if ($setting_info && $setting_info['status']) {
                                                $module_data = $this->load->controller('extension/module/' . $part[0], $setting_info);

                                                if ($module_data) {
                                                    $module_in_col[] = $module_data;
                                                }
                                            }
                                        }
                                        $sub_col_info['info'] = $module_in_col;
                                    }

                                }
                            } else {
                                $sub_col_info['info'] = array();
                            }

                            $sub_col_info['format'] = $sub_col['format'];
                            $sub_row_info[] = $sub_col_info;
                        }

                        $main_col_info['sub_rows'][] = $sub_row_info;
                        $main_col_info['format'] = $main_col['format'];
                    }
                }
                $main_row_info['main_cols'][] = $main_col_info;
                $main_row_info['class'] = $main_row['class'];

            }
            $data['widgets'][] = $main_row_info;
        }

        return $this->load->view('plaza/module/ptlayoutbuilder', $data);
    }
}