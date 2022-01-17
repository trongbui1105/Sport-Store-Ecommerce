<?php
class ControllerPlazaZoom extends Controller
{
    public function lightbox($product_id) {
        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        $data = array();

        $data['has_images'] = false;

        if ($product_info) {
            $data['images'] = array();

            $this->load->model('tool/image');

            if ($product_info['image']) {
                $data['images'][] = array(
                    'src' => $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'))
                );

                $data['has_images'] = true;
            }

            $results = $this->model_catalog_product->getProductImages($product_id);

            if($results) {
                foreach ($results as $result) {
                    $data['images'][] = array(
                        'src' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'))
                    );
                }

                $data['has_images'] = true;
            }
        }

        return $this->load->view('plaza/product/lightbox', $data);
    }
    
    public function openLightbox() {
        $product_id = (int)$this->request->get['product_id'];
        
        $json = array();

        $json['html'] = $this->lightbox($product_id);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}