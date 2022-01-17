<?php
class ControllerPlazaHeader extends Controller
{
    public function index() {
        $this->load->language('common/header');

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }

        // Wishlist
        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
        } else {
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
        }

        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

        $data['home'] = $this->url->link('common/home');
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['compare'] = $this->url->link('product/compare');
        $data['logged'] = $this->customer->isLogged();
        $data['account'] = $this->url->link('account/account', '', true);
        $data['register'] = $this->url->link('account/register', '', true);
        $data['login'] = $this->url->link('account/login', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['transaction'] = $this->url->link('account/transaction', '', true);
        $data['download'] = $this->url->link('account/download', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);
        $data['shopping_cart'] = $this->url->link('checkout/cart');
        $data['checkout'] = $this->url->link('checkout/checkout', '', true);
        $data['contact'] = $this->url->link('information/contact');
        $data['telephone'] = $this->config->get('config_telephone');
		$data['email'] = $this->config->get('config_email');
        $data['language'] = $this->load->controller('common/language');
        $data['currency'] = $this->load->controller('common/currency');
        $data['search'] = $this->load->controller('common/search');
        $data['cart'] = $this->load->controller('common/cart');
        $data['menu'] = $this->load->controller('common/menu');

        $search_status = $this->config->get('module_ptsearch_status');
        if($search_status) {
            $data['search'] = $this->load->controller('extension/module/ptsearch');
            $data['search_status'] = true;
        } else {
            $data['search'] = $this->load->controller('common/search');
            $data['search_status'] = false;
        }

        $data['store_id'] = $this->config->get('config_store_id');
		// Plaza Module Postion
			$data['position1'] = $this->load->controller('common/position1');
			$data['position2'] = $this->load->controller('common/position2');
			$data['position3'] = $this->load->controller('common/position3');
			$data['position4'] = $this->load->controller('common/position4');
			$data['position5'] = $this->load->controller('common/position5');
			$data['position6'] = $this->load->controller('common/position6');
			$data['position7'] = $this->load->controller('common/position7');
			$data['position8'] = $this->load->controller('common/position8');
			$data['position9'] = $this->load->controller('common/position9');
			$data['position10'] = $this->load->controller('common/position10');
		// End Plaza Module Postion
        /* General */
        if(isset($this->config->get('module_ptcontrolpanel_header_layout')[$data['store_id']])) {
            $header_layout = (int) $this->config->get('module_ptcontrolpanel_header_layout')[$data['store_id']];
        } else {
            $header_layout = 1;
        }

        /* Catalog Mode */
        /* Header */
        if(isset($this->config->get('module_ptcontrolpanel_header_cart')[$data['store_id']])) {
            $data['header_cart'] = (int) $this->config->get('module_ptcontrolpanel_header_cart')[$data['store_id']];
        } else {
            $data['header_cart'] = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_header_currency')[$data['store_id']])) {
            $data['header_currency'] = (int) $this->config->get('module_ptcontrolpanel_header_currency')[$data['store_id']];
        } else {
            $data['header_currency'] = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_module_quickview')[$data['store_id']])) {
            $module_quick_view = (int) $this->config->get('module_ptcontrolpanel_module_quickview')[$data['store_id']];
        } else {
            $module_quick_view = 0;
        }

        if(isset($this->config->get('module_ptcontrolpanel_cate_quickview')[$data['store_id']])) {
            $category_quick_view = (int) $this->config->get('module_ptcontrolpanel_cate_quickview')[$data['store_id']];
        } else {
            $category_quick_view = 0;
        }

        if($module_quick_view || $category_quick_view) {
            $data['use_quick_view'] = true;
        } else {
            $data['use_quick_view'] = false;
        }

        return $this->load->view('plaza/page_section/header/header' . $header_layout, $data);
    }
}