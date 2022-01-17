<?php
class ModelPlazaBlog extends Model
{
    public function addPost($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost SET author = '" . $this->db->escape($data['author']) . "', image = '" . $this->db->escape($data['image']) . "', sort_order = '" . (int) $data['sort_order'] . "', status = '" . (int) $data['status'] . "', date_added = NOW()");

        $post_id = $this->db->getLastId();

        foreach ($data['post_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_description SET post_id = '" . (int) $post_id . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', intro_text = '" . $this->db->escape($value['intro_text']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");

            foreach ($data['post_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int) $store_id . "', language_id = '" . (int) $language_id . "', query = 'post_id=" . (int) $post_id . "', keyword = '" . $this->db->escape($value['seo_url']) . "'");
            }
        }

        if (isset($data['post_store'])) {
            foreach ($data['post_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_to_store SET post_id = '" . (int) $post_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        if (isset($data['related'])) {
            foreach ($data['related'] as $related_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_related_post SET post_id = '" . (int) $post_id . "', related_post_id = '" . (int) $related_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_related_post SET related_post_id = '" . (int) $post_id . "', post_id = '" . (int) $related_id . "'");
            }
        }

        $this->cache->delete('post');

        return $post_id;
    }

    public function editPost($post_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "ptpost SET author = '" . $this->db->escape($data['author']) . "', image = '" . $this->db->escape($data['image']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE post_id = '" . (int) $post_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_description WHERE post_id = '" . (int) $post_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'post_id=" . (int) $post_id . "'");

        foreach ($data['post_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_description SET post_id = '" . (int) $post_id . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', intro_text = '" . $this->db->escape($value['intro_text']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");

            foreach ($data['post_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int) $store_id . "', language_id = '" . (int) $language_id . "', query = 'post_id=" . (int) $post_id . "', keyword = '" . $this->db->escape($value['seo_url']) . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_to_store WHERE post_id = '" . (int) $post_id . "'");

        if (isset($data['post_store'])) {
            foreach ($data['post_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_to_store SET post_id = '" . (int) $post_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_related_post WHERE post_id = '" . (int) $post_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_related_post WHERE related_post_id = '" . (int) $post_id . "'");

        if (isset($data['related'])) {
            foreach ($data['related'] as $related_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_related_post SET post_id = '" . (int) $post_id . "', related_post_id = '" . (int) $related_id . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_related_post SET related_post_id = '" . (int) $post_id . "', post_id = '" . (int) $related_id . "'");
            }
        }

        $this->cache->delete('post');
    }

    public function copyPost($post_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ptpost p LEFT JOIN " . DB_PREFIX . "ptpost_description pd ON (p.post_id = pd.post_id) WHERE p.post_id = '" . (int) $post_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

        if ($query->num_rows) {
            $data = $query->row;
            $data['status'] = '0';
            $data = array_merge($data, array('post_description' => $this->getPostDescriptions($post_id)));

            $this->addPost($data);
        }
    }

    public function deletePost($post_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost WHERE post_id = '" . (int) $post_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_description WHERE post_id = '" . (int) $post_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_to_list WHERE post_id = '" . (int) $post_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_to_store WHERE post_id = '" . (int) $post_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_related_post WHERE post_id = '" . (int) $post_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_related_post WHERE related_post_id = '" . (int) $post_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'post_id=" . (int) $post_id . "'");
        $this->cache->delete('post');
    }

    public function getPost($post_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ptpost p LEFT JOIN " . DB_PREFIX . "ptpost_description pd ON (p.post_id = pd.post_id) WHERE p.post_id = '" . (int) $post_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "'");

        $post_data = $query->row;

        return $post_data;
    }

    public function getPostStores($post_id) {
        $post_store_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ptpost_to_store WHERE post_id = '" . (int) $post_id . "'");

        foreach ($query->rows as $result) {
            $post_store_data[] = $result['store_id'];
        }

        return $post_store_data;
    }

    public function getRelatedPost($post_id) {
        $related_post_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ptpost_related_post WHERE post_id = '" . (int) $post_id . "'");

        foreach ($query->rows as $result) {
            $related_post_data[] = $result['related_post_id'];
        }

        return $related_post_data;
    }

    public function getPosts($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "ptpost p LEFT JOIN " . DB_PREFIX . "ptpost_description pd ON (p.post_id = pd.post_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.post_id";

        $sort_data = array(
            'pd.name',
            'p.status',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY pd.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getPostDescriptions($post_id) {
        $post_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ptpost_description WHERE post_id = '" . (int) $post_id . "'");

        foreach ($query->rows as $result) {
            $post_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'description'      => $result['description'],
                'meta_title'       => $result['meta_title'],
                'meta_description' => $result['meta_description'],
                'meta_keyword'     => $result['meta_keyword'],
                'intro_text'       => $result['intro_text']
            );
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "seo_url su LEFT JOIN " . DB_PREFIX . "ptpost_to_store dts ON (dts.store_id = su.store_id) WHERE dts.post_id = '" . (int) $post_id . "' AND su.query = 'post_id=" . (int) $post_id . "'";

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $post_description_data[$result['language_id']]['seo_url'] = $result['keyword'];
        }

        return $post_description_data;
    }

    public function getTotalPosts($data = array()) {
        $sql = "SELECT COUNT(DISTINCT p.post_id) AS total FROM " . DB_PREFIX . "ptpost p LEFT JOIN " . DB_PREFIX . "ptpost_description pd ON (p.post_id = pd.post_id)";

        $sql .= " WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function addPostList($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_list SET sort_order = '" . (int) $data['sort_order'] . "', status = '" . (int) $data['status'] . "'");

        $post_list_id = $this->db->getLastId();

        foreach ($data['post_list_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_list_description SET post_list_id = '" . (int) $post_list_id . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");

            foreach ($data['post_list_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int) $store_id . "', language_id = '" . (int) $language_id . "', query = 'post_list_id=" . (int) $post_list_id . "', keyword = '" . $this->db->escape($value['seo_url']) . "'");
            }
        }

        if (isset($data['post_list_store'])) {
            foreach ($data['post_list_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_list_to_store SET post_list_id = '" . (int) $post_list_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        $this->cache->delete('post_list');

        return $post_list_id;
    }

    public function addPostToList($post_list_id, $postIds) {
        foreach($postIds as $post_id) {
            $sql = "INSERT INTO " . DB_PREFIX . "ptpost_to_list SET post_list_id = '". (int) $post_list_id . "', post_id = '" . (int) $post_id . "'";

            $this->db->query($sql);
        }

        $this->cache->delete('post_to_list');

        return;
    }

    public function editPostList($post_list_id, $data = array()) {
        $sql = "UPDATE " . DB_PREFIX . "ptpost_list SET sort_order = '" . (int) $data['sort_order'] . "', status = '" . (int) $data['status'] . "' WHERE post_list_id = '" . (int) $post_list_id . "'";

        $this->db->query($sql);

        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_to_list WHERE post_list_id = '" . (int) $post_list_id . "'");

        foreach($data['post'] as $post_id) {
            $sql = "INSERT INTO " . DB_PREFIX . "ptpost_to_list SET post_list_id = '". (int) $post_list_id . "', post_id = '" . (int) $post_id . "'";

            $this->db->query($sql);
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_list_description WHERE post_list_id = '" . (int) $post_list_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'post_list_id=" . (int) $post_list_id . "'");

        foreach ($data['post_list_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_list_description SET post_list_id = '" . (int) $post_list_id . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");

            foreach ($data['post_list_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int) $store_id . "', language_id = '" . (int) $language_id . "', query = 'post_list_id=" . (int) $post_list_id . "', keyword = '" . $this->db->escape($value['seo_url']) . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_list_to_store WHERE post_list_id = '" . (int) $post_list_id . "'");

        if (isset($data['post_list_store'])) {
            foreach ($data['post_list_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "ptpost_list_to_store SET post_list_id = '" . (int) $post_list_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        $this->cache->delete('post_list');

        return;
    }

    public function deletePostList($post_list_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_list WHERE post_list_id = '" . (int) $post_list_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_list_description WHERE post_list_id = '" . (int) $post_list_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_list_to_store WHERE post_list_id = '" . (int) $post_list_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptpost_to_list WHERE post_list_id = '" . (int) $post_list_id . "'");
        $this->cache->delete('post_list');
    }

    public function getPostList($post_list_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ptpost_list pl LEFT JOIN " . DB_PREFIX . "ptpost_list_description pld ON (pl.post_list_id = pld.post_list_id) WHERE pl.post_list_id = '" . (int) $post_list_id . "'");

        return $query->row;
    }

    public function getPostListDescriptions($post_list_id) {
        $post_list_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ptpost_list_description WHERE post_list_id = '" . (int) $post_list_id . "'");

        foreach ($query->rows as $result) {
            $post_list_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'description'      => $result['description'],
                'meta_title'       => $result['meta_title'],
                'meta_description' => $result['meta_description'],
                'meta_keyword'     => $result['meta_keyword']
            );
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "seo_url su LEFT JOIN " . DB_PREFIX . "ptpost_list_to_store dts ON (dts.store_id = su.store_id) WHERE dts.post_list_id = '" . (int) $post_list_id . "' AND su.query = 'post_list_id=" . (int) $post_list_id . "'";

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $post_list_description_data[$result['language_id']]['seo_url'] = $result['keyword'];
        }

        return $post_list_description_data;
    }
    
    public function getPostListStores($post_list_id) {
        $post_list_store_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ptpost_list_to_store WHERE post_list_id = '" . (int) $post_list_id . "'");

        foreach ($query->rows as $result) {
            $post_list_store_data[] = $result['store_id'];
        }

        return $post_list_store_data;
    }

    public function getPostToList($post_list_id) {
        $query = $this->db->query("SELECT post_id FROM " . DB_PREFIX . "ptpost_to_list WHERE post_list_id = '" . (int) $post_list_id . "'");

        return $query->rows;
    }

    public function getAllPostList($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "ptpost_list p LEFT JOIN " . DB_PREFIX . "ptpost_list_description pd ON (p.post_list_id = pd.post_list_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        $sql .= " GROUP BY p.post_list_id";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getPostLists($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "ptpost_list p LEFT JOIN " . DB_PREFIX . "ptpost_list_description pd ON (p.post_list_id = pd.post_list_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }
        
        $sql .= " GROUP BY p.post_list_id";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalPostList() {
        $sql = "SELECT COUNT(DISTINCT p.post_list_id) AS total FROM " . DB_PREFIX . "ptpost_list p LEFT JOIN " . DB_PREFIX . "ptpost_list_description pd ON (p.post_list_id = pd.post_list_id)";

        $sql .= " WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
    
    public function addBlogSeoUrl($data, $stores) {
        if($data) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'ptblog'");

            foreach ($stores as $store) {
                foreach ($data as $language_id => $keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int) $store['store_id'] . "', language_id = '" . (int) $language_id . "', query = 'ptblog', keyword = '" . $this->db->escape($keyword) . "'");
                }
            }
        }
    }

    public function install() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptpost` (
			    `post_id` INT(11) NOT NULL AUTO_INCREMENT,
	            `status` TINYINT(1) NOT NULL DEFAULT '0',
	            `sort_order` INT(11) NOT NULL DEFAULT '0',
	            `image` varchar(255) DEFAULT NULL,
	            `author` varchar(100) DEFAULT NULL,
	            `date_added` DATETIME NOT NULL,
	            `date_modified` DATETIME NOT NULL,
	        PRIMARY KEY (`post_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptpost_description` (
			    `post_id` INT(11) NOT NULL,
                `language_id` INT(11) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `intro_text` TEXT NOT NULL,
                `meta_title` VARCHAR(255) NOT NULL,
                `meta_description` VARCHAR(255) NOT NULL,
                `meta_keyword` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`post_id`, `language_id`),
	        INDEX `name` (`name`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptpost_comment` (
			    `comment_id` INT(11) NOT NULL AUTO_INCREMENT,
                `post_id` INT(11) NOT NULL,
                `reply_comment_id` INT(11) DEFAULT NULL,
                `author` VARCHAR(255) NOT NULL,
                `comment` TEXT NOT NULL,
                `date_submitted` DATETIME NOT NULL,
                `approved` TINYINT(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`comment_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptpost_list` (
			    `post_list_id` INT(11) NOT NULL AUTO_INCREMENT,
                `status` TINYINT(1) NOT NULL DEFAULT '0',
                `sort_order` INT(11) NOT NULL DEFAULT '0',
            PRIMARY KEY (`post_list_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptpost_list_description` (
			    `post_list_id` INT(11) NOT NULL,
                `language_id` INT(11) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `meta_title` VARCHAR(255) NOT NULL,
                `meta_description` VARCHAR(255) NOT NULL,
                `meta_keyword` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`post_list_id`, `language_id`),
	        INDEX `name` (`name`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptpost_to_list` (
			    `post_list_id` INT(11) NOT NULL,
                `post_id` INT(11) NOT NULL
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptpost_to_store` (
			    `post_id` INT(11) NOT NULL,
                `store_id` INT(11) NOT NULL
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptpost_list_to_store` (
			    `post_list_id` INT(11) NOT NULL,
                `store_id` INT(11) NOT NULL
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptpost_related_post` (
			    `post_id` INT(11) NOT NULL,
                `related_post_id` INT(11) NOT NULL
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'plaza/blog');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'plaza/blog');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'plaza/blog/post');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'plaza/blog/post');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'plaza/blog/list');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'plaza/blog/list');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'plaza/blog/setting');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'plaza/blog/setting');
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptpost`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptpost_description`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptpost_list`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptpost_to_list`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptpost_to_store`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptpost_comment`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptpost_list_description`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptpost_list_to_store`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptpost_related_post`");

        $this->load->model('user/user_group');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/blog');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/blog');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/blog/post');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/blog/post');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/blog/list');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/blog/list');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/blog/setting');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/blog/setting');
    }
}