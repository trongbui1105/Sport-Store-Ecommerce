<?php
class ControllerPlazaBlog extends Controller
{
    public function index() {
        $this->load->language('plaza/blog');

        $this->load->model('plaza/blog');
        $this->load->model('tool/image');

        $this->document->setTitle($this->config->get('module_ptblog_meta_title'));
        $this->document->setDescription($this->config->get('module_ptblog_meta_description'));
        $this->document->setKeywords($this->config->get('module_ptblog_meta_keyword'));
        $this->document->addLink($this->url->link('plaza/blog'), '');

        $url = '';

        if (isset($this->request->get['layout'])) {
            $url .= '&layout=' . $this->request->get['layout'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $data['list_content_link'] = $this->url->link('plaza/blog', 'content=list' . $url, true);
        $data['grid_content_link'] = $this->url->link('plaza/blog', 'content=grid' . $url, true);

        $data['heading_title'] = $this->config->get('module_ptblog_meta_title');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        if(!empty($this->config->get('module_ptblog_blog_layout'))) {
            $data['layout'] = $this->config->get('module_ptblog_blog_layout');
        } else {
            $data['layout'] = "right";
        }

        if (isset($this->request->get['layout'])) {
            $data['layout'] = $this->request->get['layout'];
        }

        if(!empty($this->config->get('module_ptblog_blog_post_content'))) {
            $data['post_content'] = $this->config->get('module_ptblog_blog_post_content');
        } else {
            $data['post_content'] = "grid";
        }

        if (isset($this->request->get['content'])) {
            $data['post_content'] = $this->request->get['content'];
        }

        $url = '';

        if (isset($this->request->get['layout'])) {
            $url .= '&layout=' . $this->request->get['layout'];
        }

        if (isset($this->request->get['content'])) {
            $url .= '&content=' . $this->request->get['content'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('module_ptblog_blog_post_limit');
        }

        $filter_data = array(
            'start'              => ($page - 1) * $limit,
            'limit'              => $limit
        );

        $post_total = $this->model_plaza_blog->getTotalPosts($filter_data);

        $results = $this->model_plaza_blog->getPosts($filter_data);

        $width = (int) $this->config->get('module_ptblog_blog_width');
        $height = (int) $this->config->get('module_ptblog_blog_height');

        $data['posts'] = array();

        foreach ($results as $result) {
            $image = $this->model_tool_image->resize($result['image'], $width, $height);

            $data['posts'][] = array(
                'post_id'     => $result['post_id'],
                'name'        => $result['name'],
                'author'	  => $result['author'],
                'image'		  => $image,
                'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'intro_text'  => html_entity_decode($result['intro_text'], ENT_QUOTES, 'UTF-8'),
                'href'        => $this->url->link('plaza/blog/post', '&post_id=' . $result['post_id'] . $url)
            );
        }

        $url = '';

        if (isset($this->request->get['layout'])) {
            $url .= '&layout=' . $this->request->get['layout'];
        }

        if (isset($this->request->get['content'])) {
            $url .= '&content=' . $this->request->get['content'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['limits'] = array();

        $limits = array_unique(array($this->config->get('module_ptblog_blog_post_limit'), 50, 75, 100));

        sort($limits);

        foreach($limits as $value) {
            $data['limits'][] = array(
                'text'  => $value,
                'value' => $value,
                'href'  => $this->url->link('plaza/blog', $url . '&limit=' . $value, true)
            );
        }

        $url = '';

        if (isset($this->request->get['layout'])) {
            $url .= '&layout=' . $this->request->get['layout'];
        }

        if (isset($this->request->get['content'])) {
            $url .= '&content=' . $this->request->get['content'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $pagination = new Pagination();
        $pagination->total = $post_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('plaza/blog', $url . '&page={page}');

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($post_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($post_total - $limit)) ? $post_total : ((($page - 1) * $limit) + $limit), $post_total, ceil($post_total / $limit));

        $data['limit'] = $limit;

        $data['category_list_widget'] = $this->categories_list();

        $latest_blog_show = false;

        if(!empty($this->config->get('module_ptblog_blog_latest_post'))) {
            $latest_blog_show = (int) $this->config->get('module_ptblog_blog_latest_post');
        }

        if(!empty($this->config->get('module_ptblog_blog_latest_post_limit'))) {
            $latest_blog_limit = (int) $this->config->get('module_ptblog_blog_latest_post_limit');
        } else {
            $latest_blog_limit = 5;
        }

        $data['latest_blog_widget'] = $this->latest_blog($latest_blog_show, $latest_blog_limit);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('plaza/blog/list', $data));
    }

    public function category() {
        $this->load->language('plaza/blog');

        $this->load->model('plaza/blog');
        $this->load->model('tool/image');

        if (isset($this->request->get['post_list_id'])) {
            $post_list_id = (int)$this->request->get['post_list_id'];
        } else {
            $post_list_id = 0;
        }

        $category_info = $this->model_plaza_blog->getPostList($post_list_id);

        if($category_info) {
            $url = '';

            if (isset($this->request->get['layout'])) {
                $url .= '&layout=' . $this->request->get['layout'];
            }

            if (isset($this->request->get['content'])) {
                $url .= '&content=' . $this->request->get['content'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_blog'),
                'href' => $this->url->link('plaza/blog', $url, true)
            );

            $this->document->setTitle($category_info['meta_title']);
            $this->document->setDescription($category_info['meta_description']);
            $this->document->setKeywords($category_info['meta_keyword']);
            $this->document->addLink($this->url->link('plaza/blog/category', 'post_list_id=' . $this->request->get['post_list_id'], true), true);

            $data['category_title'] = $category_info['name'];
            $data['category_description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
            
            if(!empty($this->config->get('module_ptblog_category_layout'))) {
                $data['layout'] = $this->config->get('module_ptblog_category_layout');
            } else {
                $data['layout'] = "right";
            }

            if (isset($this->request->get['layout'])) {
                $data['layout'] = $this->request->get['layout'];
            }

            if(!empty($this->config->get('module_ptblog_category_post_content'))) {
                $data['post_content'] = $this->config->get('module_ptblog_category_post_content');
            } else {
                $data['post_content'] = "grid";
            }

            if (isset($this->request->get['content'])) {
                $data['post_content'] = $this->request->get['content'];
            }
            
            $url = '';

            if (isset($this->request->get['layout'])) {
                $url .= '&layout=' . $this->request->get['layout'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['list_content_link'] = $this->url->link('plaza/blog/category', 'post_list_id=' . $this->request->get['post_list_id'] . '&content=list' . $url, true);
            $data['grid_content_link'] = $this->url->link('plaza/blog/category', 'post_list_id=' . $this->request->get['post_list_id'] . '&content=grid' . $url, true);

            $url = '';

            if (isset($this->request->get['layout'])) {
                $url .= '&layout=' . $this->request->get['layout'];
            }

            if (isset($this->request->get['content'])) {
                $url .= '&content=' . $this->request->get['content'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $data['limits'] = array();

            $limits = array_unique(array($this->config->get('module_ptblog_category_post_limit'), 50, 75, 100));

            sort($limits);

            foreach($limits as $value) {
                $data['limits'][] = array(
                    'text'  => $value,
                    'value' => $value,
                    'href'  => $this->url->link('plaza/blog/category', 'post_list_id=' . $this->request->get['post_list_id'] . $url . '&limit=' . $value, true)
                );
            }

            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } else {
                $page = 1;
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = $this->config->get('module_ptblog_category_post_limit');
            }

            $filter_data = array(
                'start'              => ($page - 1) * $limit,
                'limit'              => $limit
            );

            $results = $this->model_plaza_blog->getPostsByList($filter_data, $post_list_id);

            $post_total = count($results);

            $width = (int) $this->config->get('module_ptblog_category_width');
            $height = (int) $this->config->get('module_ptblog_category_height');

            $data['posts'] = array();

            foreach ($results as $result) {
                $image = $this->model_tool_image->resize($result['image'], $width, $height);

                $data['posts'][] = array(
                    'post_id'     => $result['post_id'],
                    'name'        => $result['name'],
                    'author'	  => $result['author'],
                    'image'		  => $image,
                    'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                    'intro_text'  => html_entity_decode($result['intro_text'], ENT_QUOTES, 'UTF-8'),
                    'href'        => $this->url->link('plaza/blog/post', '&post_id=' . $result['post_id'] . $url)
                );
            }

            $url = '';

            if (isset($this->request->get['layout'])) {
                $url .= '&layout=' . $this->request->get['layout'];
            }

            if (isset($this->request->get['content'])) {
                $url .= '&content=' . $this->request->get['content'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $pagination = new Pagination();
            $pagination->total = $post_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->url = $this->url->link('plaza/blog/category', 'post_list_id=' . $this->request->get['post_list_id'] . $url . '&page={page}');

            $data['pagination'] = $pagination->render();
            $data['results'] = sprintf($this->language->get('text_pagination'), ($post_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($post_total - $limit)) ? $post_total : ((($page - 1) * $limit) + $limit), $post_total, ceil($post_total / $limit));

            $data['limit'] = $limit;

            $latest_blog_show = false;

            if(!empty($this->config->get('module_ptblog_category_latest_post'))) {
                $latest_blog_show = (int) $this->config->get('module_ptblog_category_latest_post');
            }

            if(!empty($this->config->get('module_ptblog_category_latest_post_limit'))) {
                $latest_blog_limit = (int) $this->config->get('module_ptblog_category_latest_post_limit');
            } else {
                $latest_blog_limit = 5;
            }

            $data['latest_blog_widget'] = $this->latest_blog($latest_blog_show, $latest_blog_limit);

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('plaza/blog/category', $data));
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('plaza/blog/category', '&post_id=' . $post_list_id)
            );

            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $data['text_error'] = $this->language->get('text_error');

            $data['button_continue'] = $this->language->get('button_continue');

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

    public function post() {
        $this->load->language('plaza/blog');

        $this->load->model('plaza/blog');
        $this->load->model('tool/image');

        if (isset($this->request->get['post_id'])) {
            $post_id = (int)$this->request->get['post_id'];
        } else {
            $post_id = 0;
        }

        $post_info = $this->model_plaza_blog->getPost($post_id);

        if ($post_info) {
            $url = '';

            if (isset($this->request->get['layout'])) {
                $url .= '&layout=' . $this->request->get['layout'];
            }

            if (isset($this->request->get['content'])) {
                $url .= '&content=' . $this->request->get['content'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_blog'),
                'href' => $this->url->link('plaza/blog', $url, true)
            );

            $this->document->setTitle($post_info['meta_title']);
            $this->document->setDescription($post_info['meta_description']);
            $this->document->setKeywords($post_info['meta_keyword']);
            $this->document->addLink($this->url->link('plaza/blog/post', 'post_id=' . $this->request->get['post_id'], true), true);

            $data['heading_title'] = $post_info['name'];
            $data['author'] = $post_info['author'];
            $data['date'] = date($this->language->get('date_format_short'), strtotime($post_info['date_added']));
            $data['post_id'] = (int) $this->request->get['post_id'];
            $data['description'] = html_entity_decode($post_info['description'], ENT_QUOTES, 'UTF-8');

            if($this->config->get('module_ptblog_post_width')) {
                $image_size_width = (int) $this->config->get('module_ptblog_post_width');
            } else {
                $image_size_width = 200;
            }

            if($this->config->get('module_ptblog_post_height')) {
                $image_size_height = (int) $this->config->get('module_ptblog_post_height');
            } else {
                $image_size_height = 200;
            }
			
			if($this->config->get('module_ptblog_related_post_width')) {
                $image_related_size_width = (int) $this->config->get('module_ptblog_related_post_width');
            } else {
                $image_related_size_width = 200;
            }

            if($this->config->get('module_ptblog_related_post_height')) {
                $image_related_size_height = (int) $this->config->get('module_ptblog_related_post_height');
            } else {
                $image_related_size_height = 200;
            }

            $data['image'] = $this->model_tool_image->resize($post_info['image'], $image_size_width, $image_size_height);

            $show_related = false;

            $data['related_posts'] = array();
            
            if(!empty($this->config->get('module_ptblog_post_related'))) {
                $show_related = (int) $this->config->get('module_ptblog_post_related');
            }

            if($show_related) {
                $related_posts = $this->model_plaza_blog->getRelatedPosts($post_id);

                if($related_posts) {
                    if(!empty($this->config->get('module_ptblog_post_related_limit'))) {
                        $related_limit = (int) $this->config->get('module_ptblog_post_related_limit');
                    } else {
                        $related_limit = 5;
                    }

                    $related_posts = array_slice($related_posts, 0, $related_limit);

                    foreach ($related_posts as $pid) {
                        $related_post_info = $this->model_plaza_blog->getPost($pid);

                        $image = $this->model_tool_image->resize($related_post_info['image'], $image_related_size_width, $image_related_size_height);
                        $image_full = $this->model_tool_image->resize($related_post_info['image'], $image_related_size_width, $image_related_size_height);

                        $data['related_posts'][] = array(
                            'post_id'     => $related_post_info['post_id'],
                            'name'        => substr($related_post_info['name'],0,40).'...',
                            'name_full'        => $related_post_info['name'],
                            'author'	  => $related_post_info['author'],
                            'image'		  => $image,
                            'image_full'		  => $image_full,
                            'date_added'  => date($this->language->get('date_format_short'), strtotime($related_post_info['date_added'])),
                            'intro_text'  => html_entity_decode($related_post_info['intro_text'], ENT_QUOTES, 'UTF-8'),
                            'href'        => $this->url->link('plaza/blog/post', '&post_id=' . $related_post_info['post_id'] . $url)
                        );
                    }
                }
            }

            if(!empty($this->config->get('module_ptblog_post_layout'))) {
                $data['layout'] = $this->config->get('module_ptblog_post_layout');
            } else {
                $data['layout'] = "full";
            }

            if($data['layout'] == "full") {
                $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
                $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
            }

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('plaza/blog/post', $data));
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('plaza/blog/post', '&post_id=' . $post_id)
            );

            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $data['text_error'] = $this->language->get('text_error');

            $data['button_continue'] = $this->language->get('button_continue');

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

    public function categories_list() {
        $data = array();

        $cate_show = false;

        if(!empty($this->config->get('module_ptblog_cates_show'))) {
            $cate_show = (int) $this->config->get('module_ptblog_cates_show');
        }

        $data['categories'] = array();

        if($cate_show) {
            if(!empty($this->config->get('module_ptblog_cates_list'))) {
                $cate_list_ids = $this->config->get('module_ptblog_cates_list');

                if($cate_list_ids) {
                    $url = '';

                    if (isset($this->request->get['layout'])) {
                        $url .= '&layout=' . $this->request->get['layout'];
                    }

                    if (isset($this->request->get['content'])) {
                        $url .= '&content=' . $this->request->get['content'];
                    }

                    if (isset($this->request->get['page'])) {
                        $url .= '&page=' . $this->request->get['page'];
                    }

                    if (isset($this->request->get['limit'])) {
                        $url .= '&limit=' . $this->request->get['limit'];
                    }

                    foreach ($cate_list_ids as $cate_id) {
                        $cate_info = $this->model_plaza_blog->getPostList($cate_id);

                        if($cate_info) {
                            $data['categories'][] = array(
                                'name'  => $cate_info['name'],
                                'href'  => $this->url->link('plaza/blog/category', '&post_list_id=' . $cate_id . $url, true)
                            );
                        }
                    }
                }
            }
        }

        return $this->load->view('plaza/blog/widget/cate_list', $data);
    }

    public function latest_blog($latest_blog_show, $limit) {
        $data = array();

        $data['latest_blog'] = array();

        if($latest_blog_show) {
            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            if (isset($this->request->get['layout'])) {
                $url .= '&layout=' . $this->request->get['layout'];
            }

            if (isset($this->request->get['content'])) {
                $url .= '&content=' . $this->request->get['content'];
            }

            $filter_data = array(
                'sort'  => 'p.date_added',
                'order' => 'DESC',
                'start' => 0,
                'limit' => $limit
            );
            $results = $this->model_plaza_blog->getPosts($filter_data);
            foreach ($results as $result) {
                $image = $this->model_tool_image->resize($result['image'], 75, 63);

                $data['latest_blog'][] = array(
                    'post_id'     => $result['post_id'],
                    'name'        => substr($result['name'],0,40).'...',
                    'author'	  => $result['author'],
                    'image'		  => $image,
                    'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                    'intro_text'  => html_entity_decode($result['intro_text'], ENT_QUOTES, 'UTF-8'),
                    'href'        => $this->url->link('plaza/blog/post', 'post_id=' . $result['post_id'] . $url)
                );
            }
        }

        return $this->load->view('plaza/blog/widget/latest_blog', $data);
    }
}