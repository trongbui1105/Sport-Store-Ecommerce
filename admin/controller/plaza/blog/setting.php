<?php
class ControllerPlazaBlogSetting extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('plaza/blog/setting');
        $this->load->language('plaza/adminmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');
        $this->load->model('setting/store');
        $this->load->model('plaza/blog');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_ptblog', $this->request->post);

            $blog_seo_url = $this->request->post['module_ptblog_seo_url'];

            $stores = array();

            $stores[] = array(
                'store_id' => 0,
                'name'     => $this->language->get('text_default')
            );

            $sts = $this->model_setting_store->getStores();

            foreach ($sts as $store) {
                $stores[] = array(
                    'store_id' => $store['store_id'],
                    'name'     => $store['name']
                );
            }

            $this->model_plaza_blog->addBlogSeoUrl($blog_seo_url, $stores);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('plaza/blog/setting', 'user_token=' . $this->session->data['user_token'], true));
        }

        if(isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = '';
        }

        if (isset($this->error['meta_description'])) {
            $data['error_meta_description'] = $this->error['meta_description'];
        } else {
            $data['error_meta_description'] = '';
        }

        if (isset($this->error['meta_keyword'])) {
            $data['error_meta_keyword'] = $this->error['meta_keyword'];
        } else {
            $data['error_meta_keyword'] = '';
        }

        if (isset($this->error['error_image_blog'])) {
            $data['error_image_blog'] = $this->error['error_image_blog'];
        } else {
            $data['error_image_blog'] = '';
        }

        if (isset($this->error['error_image_category'])) {
            $data['error_image_category'] = $this->error['error_image_category'];
        } else {
            $data['error_image_category'] = '';
        }

        if (isset($this->error['error_image_post'])) {
            $data['error_image_post'] = $this->error['error_image_post'];
        } else {
            $data['error_image_post'] = '';
        }
		
		if (isset($this->error['error_image_related_post'])) {
            $data['error_image_related_post'] = $this->error['error_image_related_post'];
        } else {
            $data['error_image_related_post'] = '';
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('plaza/blog/setting', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('plaza/blog/setting', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

        // Blog Page
        if (isset($this->request->post['module_ptblog_blog_width'])) {
            $data['module_ptblog_blog_width'] = $this->request->post['module_ptblog_blog_width'];
        } else {
            $data['module_ptblog_blog_width'] = $this->config->get('module_ptblog_blog_width');
        }

        if (isset($this->request->post['module_ptblog_blog_height'])) {
            $data['module_ptblog_blog_height'] = $this->request->post['module_ptblog_blog_height'];
        } else {
            $data['module_ptblog_blog_height'] = $this->config->get('module_ptblog_blog_height');
        }

        if (isset($this->request->post['module_ptblog_blog_post_limit'])) {
            $data['module_ptblog_blog_post_limit'] = $this->request->post['module_ptblog_blog_post_limit'];
        } else {
            $data['module_ptblog_blog_post_limit'] = $this->config->get('module_ptblog_blog_post_limit');
        }

        if (isset($this->request->post['module_ptblog_cates_show'])) {
            $data['module_ptblog_cates_show'] = $this->request->post['module_ptblog_cates_show'];
        } else {
            $data['module_ptblog_cates_show'] = $this->config->get('module_ptblog_cates_show');
        }

        $data['blog_categories'] = array();

        if (isset($this->request->post['module_ptblog_cates_list'])) {
            $blog_categories = $this->request->post['module_ptblog_cates_list'];
        } elseif(!empty($this->config->get('module_ptblog_cates_list'))) {
            $blog_categories = $this->config->get('module_ptblog_cates_list');
        } else {
            $blog_categories = array();
        }

        foreach ($blog_categories as $cate_id) {
            $cate_info = $this->model_plaza_blog->getPostList($cate_id);

            if ($cate_info) {
                $data['blog_categories'][] = array(
                    'post_list_id' => $cate_info['post_list_id'],
                    'name'       => $cate_info['name']
                );
            }
        }

        if (isset($this->request->post['module_ptblog_blog_latest_post'])) {
            $data['module_ptblog_blog_latest_post'] = $this->request->post['module_ptblog_blog_latest_post'];
        } else {
            $data['module_ptblog_blog_latest_post'] = $this->config->get('module_ptblog_blog_latest_post');
        }

        if (isset($this->request->post['module_ptblog_blog_latest_post_limit'])) {
            $data['module_ptblog_blog_latest_post_limit'] = $this->request->post['module_ptblog_blog_latest_post_limit'];
        } else {
            $data['module_ptblog_blog_latest_post_limit'] = $this->config->get('module_ptblog_blog_latest_post_limit');
        }

        if (isset($this->request->post['module_ptblog_blog_layout'])) {
            $data['module_ptblog_blog_layout'] = $this->request->post['module_ptblog_blog_layout'];
        } else {
            $data['module_ptblog_blog_layout'] = $this->config->get('module_ptblog_blog_layout');
        }

        if (isset($this->request->post['module_ptblog_blog_post_content'])) {
            $data['module_ptblog_blog_post_content'] = $this->request->post['module_ptblog_blog_post_content'];
        } else {
            $data['module_ptblog_blog_post_content'] = $this->config->get('module_ptblog_blog_post_content');
        }

        if (isset($this->request->post['module_ptblog_meta_title'])) {
            $data['module_ptblog_meta_title'] = $this->request->post['module_ptblog_meta_title'];
        } else {
            $data['module_ptblog_meta_title'] = $this->config->get('module_ptblog_meta_title');
        }

        if (isset($this->request->post['module_ptblog_meta_description'])) {
            $data['module_ptblog_meta_description'] = $this->request->post['module_ptblog_meta_description'];
        } else {
            $data['module_ptblog_meta_description'] = $this->config->get('module_ptblog_meta_description');
        }

        if (isset($this->request->post['module_ptblog_meta_keyword'])) {
            $data['module_ptblog_meta_keyword'] = $this->request->post['module_ptblog_meta_keyword'];
        } else {
            $data['module_ptblog_meta_keyword'] = $this->config->get('module_ptblog_meta_keyword');
        }

        if (isset($this->request->post['module_ptblog_seo_url'])) {
            $data['module_ptblog_seo_url'] = $this->request->post['module_ptblog_seo_url'];
        } else {
            $data['module_ptblog_seo_url'] = $this->config->get('module_ptblog_seo_url');
        }

        // Category Page
        if (isset($this->request->post['module_ptblog_category_width'])) {
            $data['module_ptblog_category_width'] = $this->request->post['module_ptblog_category_width'];
        } else {
            $data['module_ptblog_category_width'] = $this->config->get('module_ptblog_category_width');
        }

        if (isset($this->request->post['module_ptblog_category_height'])) {
            $data['module_ptblog_category_height'] = $this->request->post['module_ptblog_category_height'];
        } else {
            $data['module_ptblog_category_height'] = $this->config->get('module_ptblog_category_height');
        }

        if (isset($this->request->post['module_ptblog_category_post_limit'])) {
            $data['module_ptblog_category_post_limit'] = $this->request->post['module_ptblog_category_post_limit'];
        } else {
            $data['module_ptblog_category_post_limit'] = $this->config->get('module_ptblog_category_post_limit');
        }

        if (isset($this->request->post['module_ptblog_category_latest_post'])) {
            $data['module_ptblog_category_latest_post'] = $this->request->post['module_ptblog_category_latest_post'];
        } else {
            $data['module_ptblog_category_latest_post'] = $this->config->get('module_ptblog_category_latest_post');
        }

        if (isset($this->request->post['module_ptblog_category_latest_post_limit'])) {
            $data['module_ptblog_category_latest_post_limit'] = $this->request->post['module_ptblog_category_latest_post_limit'];
        } else {
            $data['module_ptblog_category_latest_post_limit'] = $this->config->get('module_ptblog_category_latest_post_limit');
        }

        if (isset($this->request->post['module_ptblog_category_layout'])) {
            $data['module_ptblog_category_layout'] = $this->request->post['module_ptblog_category_layout'];
        } else {
            $data['module_ptblog_category_layout'] = $this->config->get('module_ptblog_category_layout');
        }

        if (isset($this->request->post['module_ptblog_category_post_content'])) {
            $data['module_ptblog_category_post_content'] = $this->request->post['module_ptblog_category_post_content'];
        } else {
            $data['module_ptblog_category_post_content'] = $this->config->get('module_ptblog_category_post_content');
        }

        // Post Detail Page
        if (isset($this->request->post['module_ptblog_post_width'])) {
            $data['module_ptblog_post_width'] = $this->request->post['module_ptblog_post_width'];
        } else {
            $data['module_ptblog_post_width'] = $this->config->get('module_ptblog_post_width');
        }

        if (isset($this->request->post['module_ptblog_post_height'])) {
            $data['module_ptblog_post_height'] = $this->request->post['module_ptblog_post_height'];
        } else {
            $data['module_ptblog_post_height'] = $this->config->get('module_ptblog_post_height');
        }
		
		if (isset($this->request->post['module_ptblog_related_post_width'])) {
            $data['module_ptblog_related_post_width'] = $this->request->post['module_ptblog_related_post_width'];
        } else {
            $data['module_ptblog_related_post_width'] = $this->config->get('module_ptblog_related_post_width');
        }

        if (isset($this->request->post['module_ptblog_related_post_height'])) {
            $data['module_ptblog_related_post_height'] = $this->request->post['module_ptblog_related_post_height'];
        } else {
            $data['module_ptblog_related_post_height'] = $this->config->get('module_ptblog_related_post_height');
        }

        if (isset($this->request->post['module_ptblog_post_related'])) {
            $data['module_ptblog_post_related'] = $this->request->post['module_ptblog_post_related'];
        } else {
            $data['module_ptblog_post_related'] = $this->config->get('module_ptblog_post_related');
        }

        if (isset($this->request->post['module_ptblog_post_related_limit'])) {
            $data['module_ptblog_post_related_limit'] = $this->request->post['module_ptblog_post_related_limit'];
        } else {
            $data['module_ptblog_post_related_limit'] = $this->config->get('module_ptblog_post_related_limit');
        }

        if (isset($this->request->post['module_ptblog_post_layout'])) {
            $data['module_ptblog_post_layout'] = $this->request->post['module_ptblog_post_layout'];
        } else {
            $data['module_ptblog_post_layout'] = $this->config->get('module_ptblog_post_layout');
        }

        $data['plaza_menus'] = array();

        if($this->user->hasPermission('access', 'extension/module/ptcontrolpanel')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-magic"></i> ' . $this->language->get('text_control_panel'),
                'url'    => $this->url->link('extension/module/ptcontrolpanel', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'plaza/module')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-puzzle-piece"></i> ' . $this->language->get('text_theme_module'),
                'url'    => $this->url->link('plaza/module', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'plaza/featuredcate')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-tag"></i> ' . $this->language->get('text_special_category'),
                'url'    => $this->url->link('plaza/featuredcate', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'plaza/ultimatemenu')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-bars"></i> ' . $this->language->get('text_ultimate_menu'),
                'url'    => $this->url->link('plaza/ultimatemenu/menuList', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if ($this->user->hasPermission('access', 'plaza/blog')) {
            $blog_menu = array();

            if ($this->user->hasPermission('access', 'plaza/blog/post')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_posts'),
                    'url'    => $this->url->link('plaza/blog/post', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if ($this->user->hasPermission('access', 'plaza/blog/list')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_posts_list'),
                    'url'    => $this->url->link('plaza/blog/list', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if ($this->user->hasPermission('access', 'plaza/blog/setting')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_blog_setting'),
                    'url'    => $this->url->link('plaza/blog/setting', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 1
                );
            }

            if($blog_menu) {
                $data['plaza_menus'][] = array(
                    'title'  => '<i class="a fa fa-ticket"></i> ' . $this->language->get('text_blog'),
                    'child'  => $blog_menu,
                    'active' => 1
                );
            }
        }

        if($this->user->hasPermission('access', 'plaza/slider')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-film"></i> ' . $this->language->get('text_slider'),
                'url'    => $this->url->link('plaza/slider', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'plaza/testimonial')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-comment"></i> ' . $this->language->get('text_testimonial'),
                'url'    => $this->url->link('plaza/testimonial', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'plaza/newsletter')) {
            $data['plaza_menus'][] = array(
                'title'  => '<i class="a fa fa-envelope"></i> ' . $this->language->get('text_newsletter'),
                'url'    => $this->url->link('plaza/newsletter', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }
        
        $this->document->addStyle('view/stylesheet/plaza/themeadmin.css');
        $this->document->addScript('view/javascript/plaza/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/plaza/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('plaza/blog/setting', $data));
    }

    public function validate() {
        if (!$this->user->hasPermission('modify', 'plaza/blog/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['module_ptblog_meta_title']) {
            $this->error['meta_title'] = $this->language->get('error_meta_title');
        }

        if (!$this->request->post['module_ptblog_meta_description']) {
            $this->error['meta_description'] = $this->language->get('error_meta_description');
        }

        if (!$this->request->post['module_ptblog_meta_keyword']) {
            $this->error['meta_keyword'] = $this->language->get('error_meta_keyword');
        }

        if (!$this->request->post['module_ptblog_blog_width'] || !$this->request->post['module_ptblog_blog_height']) {
            $this->error['error_image_blog'] = $this->language->get('error_image_blog');
        }

        if (!$this->request->post['module_ptblog_category_width'] || !$this->request->post['module_ptblog_category_height']) {
            $this->error['error_image_category'] = $this->language->get('error_image_category');
        }

        if (!$this->request->post['module_ptblog_post_width'] || !$this->request->post['module_ptblog_post_height']) {
            $this->error['error_image_post'] = $this->language->get('error_image_post');
        }
		
		if (!$this->request->post['module_ptblog_related_post_width'] || !$this->request->post['module_ptblog_related_post_height']) {
            $this->error['error_image_related_post'] = $this->language->get('error_image_related_post');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }
}