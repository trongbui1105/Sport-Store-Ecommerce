<?php
class ControllerPlazaBlogList extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('plaza/blog/list');
        $this->load->language('plaza/adminmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('plaza/blog');

        $this->getList();
    }

    public function add() {
        $this->load->language('plaza/blog/list');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('plaza/blog');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $post_list_id = $this->model_plaza_blog->addPostList($this->request->post);
            $this->model_plaza_blog->addPostToList($post_list_id, $this->request->post['post']);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('plaza/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('plaza/blog/list');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('plaza/blog');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_plaza_blog->editPostList($this->request->get['post_list_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('plaza/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('plaza/blog/list');
        $this->load->language('plaza/adminmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('plaza/blog');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $post_list_id) {
                $this->model_plaza_blog->deletePostList($post_list_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('plaza/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function getList() {
        $data = array();

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['module_id'])) {
            $url .= '&module_id=' . $this->request->get['module_id'];
        }

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
            'href' => $this->url->link('plaza/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('plaza/blog/list/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['copy'] = $this->url->link('plaza/blog/list/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('plaza/blog/list/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['post_list'] = array();

        $filter_data = array(
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        $post_list_total = $this->model_plaza_blog->getTotalPostList();

        $results = $this->model_plaza_blog->getAllPostList($filter_data);

        foreach ($results as $result) {
            $data['post_list'][] = array(
                'post_list_id'  => $result['post_list_id'],
                'status'        => $result['status'],
                'name'          => $result['name'],
                'status_text'   => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'          => $this->url->link('plaza/blog/list/edit', 'user_token=' . $this->session->data['user_token'] . '&post_list_id=' . $result['post_list_id'] . $url, true)
            );
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $pagination = new Pagination();
        $pagination->total = $post_list_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('plaza/blog/list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($post_list_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($post_list_total - $this->config->get('config_limit_admin'))) ? $post_list_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $post_list_total, ceil($post_list_total / $this->config->get('config_limit_admin')));

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
                    'active' => 1
                );
            }

            if ($this->user->hasPermission('access', 'plaza/blog/setting')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_blog_setting'),
                    'url'    => $this->url->link('plaza/blog/setting', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
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

        $this->response->setOutput($this->load->view('plaza/blog/list/list', $data));
    }

    public function getForm() {
        $data['text_form'] = !isset($this->request->get['post_list_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = array();
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = '';
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['module_id'])) {
            $url .= '&module_id=' . $this->request->get['module_id'];
        }

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
            'href' => $this->url->link('plaza/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['post_list_id'])) {
            $data['action'] = $this->url->link('plaza/blog/list/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('plaza/blog/list/edit', 'user_token=' . $this->session->data['user_token'] . '&post_list_id=' . $this->request->get['post_list_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('plaza/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['post_list_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $post_list_info = $this->model_plaza_blog->getPostList($this->request->get['post_list_id']);
            $post_list_info['post'] = $this->model_plaza_blog->getPostToList($this->request->get['post_list_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['post_list_description'])) {
            $data['post_list_description'] = $this->request->post['post_list_description'];
        } elseif (isset($this->request->get['post_list_id'])) {
            $data['post_list_description'] = $this->model_plaza_blog->getPostListDescriptions($this->request->get['post_list_id']);
        } else {
            $data['post_list_description'] = array();
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($post_info)) {
            $data['sort_order'] = $post_info['sort_order'];
        } else {
            $data['sort_order'] = 1;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($post_list_info)) {
            $data['status'] = $post_list_info['status'];
        } else {
            $data['status'] = true;
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        if (isset($this->request->post['post_list_store'])) {
            $data['post_list_store'] = $this->request->post['post_list_store'];
        } elseif (isset($this->request->get['post_list_id'])) {
            $data['post_list_store'] = $this->model_plaza_blog->getPostListStores($this->request->get['post_list_id']);
        } else {
            $data['post_list_store'] = array(0);
        }

        $data['posts'] = array();

        if (isset($this->request->post['post'])) {
            $posts = $this->request->post['post'];
        } elseif (!empty($post_list_info)) {
            $posts = $post_list_info['post'];
        } else {
            $posts = array();
        }

        foreach ($posts as $post) {
            $post_info = $this->model_plaza_blog->getPost($post['post_id']);

            if ($post_info) {
                $data['posts'][] = array(
                    'post_id' => $post_info['post_id'],
                    'name'       => $post_info['name']
                );
            }
        }

        $this->document->addStyle('view/stylesheet/plaza/themeadmin.css');
        $this->document->addScript('view/javascript/plaza/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/plaza/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('plaza/blog/list/form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'plaza/blog/list')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['post_list_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
                $this->error['name'][$language_id] = $this->language->get('error_name');
            }

            if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
                $this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
            }

            if (utf8_strlen($value['seo_url']) > 0) {
                $this->load->model('design/seo_url');

                $url_alias_info = $this->model_design_seo_url->getSeoUrlsByKeyword($value['seo_url']);

                foreach ($url_alias_info as $surl) {
                    if ($url_alias_info && isset($this->request->get['post_list_id']) && $surl['query'] != 'post_list_id=' . $this->request->get['post_list_id']) {
                        $this->error['keyword'] = sprintf($this->language->get('error_keyword'));

                        break;
                    }
                }

                if ($url_alias_info && !isset($this->request->get['post_list_id'])) {
                    $this->error['keyword'] = sprintf($this->language->get('error_keyword'));
                }
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'plaza/blog/list')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name']) ) {
            $this->load->model('plaza/blog');

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 5;
            }

            $filter_data = array(
                'filter_name'  => $filter_name,
                'start'        => 0,
                'limit'        => $limit
            );

            $results = $this->model_plaza_blog->getPostLists($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'post_list_id' => $result['post_list_id'],
                    'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}