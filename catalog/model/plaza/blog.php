<?php
class ModelPlazaBlog extends Model
{
    public function getPost($post_id) {
        $query = $this->db->query("SELECT DISTINCT p.post_id, p.image, p.author, p.sort_order, p.status, p.date_added, p.date_modified, pd.language_id, pd.name, pd.description, pd.intro_text, pd.meta_title, pd.meta_description, pd.meta_keyword FROM " . DB_PREFIX . "ptpost p LEFT JOIN " . DB_PREFIX . "ptpost_description pd ON (p.post_id = pd.post_id) LEFT JOIN " . DB_PREFIX . "ptpost_to_store pts ON (p.post_id = pts.post_id) WHERE p.post_id = '" . (int) $post_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND pts.store_id = '". (int) $this->config->get('config_store_id') ."' AND p.status = '1' ");

        if ($query->num_rows) {
            return array(
                'post_id'          => $query->row['post_id'],
                'name'             => $query->row['name'],
                'author'           => $query->row['author'],
                'image'            => $query->row['image'],
                'description'      => $query->row['description'],
                'meta_title'       => $query->row['meta_title'],
                'meta_description' => $query->row['meta_description'],
                'meta_keyword'     => $query->row['meta_keyword'],
                'intro_text'       => $query->row['intro_text'],
                'sort_order'       => $query->row['sort_order'],
                'status'           => $query->row['status'],
                'date_added'       => $query->row['date_added'],
                'date_modified'    => $query->row['date_modified']
            );
        } else {
            return false;
        }
    }

    public function getPostsByList($data = array(), $post_list_id) {
        $sql = "SELECT p.post_id " . " FROM " . DB_PREFIX . "ptpost p";

        $sql .= " LEFT JOIN " . DB_PREFIX . "ptpost_to_list pl ON (p.post_id = pl.post_id) ";

        $sql .= " LEFT JOIN " . DB_PREFIX . "ptpost_to_store pts ON (p.post_id = pts.post_id) ";

        $sql .= " LEFT JOIN " . DB_PREFIX . "ptpost_description pd ON (p.post_id = pd.post_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND pl.post_list_id = '" . (int) $post_list_id . "' AND pts.store_id = '" . (int) $this->config->get('config_store_id') . "' AND p.status = '1' ";


        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                if (!empty($data['filter_description'])) {
                    $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_description']) . "%'";
                }
            }

            if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                $sql .= " OR ";
            }
            $sql .= ")";
        }

        $sql .= " GROUP BY p.post_id";

        $sort_data = array(
            'pd.name',
            'p.sort_order',
            'p.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(pd.name) DESC";
        } else {
            $sql .= " ASC, LCASE(pd.name) ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $post_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $post_data[$result['post_id']] = $this->getPost($result['post_id']);
        }

        return $post_data;
    }

    public function getRelatedPosts($post_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "ptpost_related_post WHERE post_id = '" . $post_id . "'";

        $query = $this->db->query($sql);

        $related_posts = false;

        foreach ($query->rows as $result) {
            $related_posts[] = $result['related_post_id'];
        }

        return $related_posts;
    }

    public function getPosts($data = array()) {
        $sql = "SELECT p.post_id " . " FROM " . DB_PREFIX . "ptpost p";

        $sql .= " LEFT JOIN " . DB_PREFIX . "ptpost_to_store pts ON (p.post_id = pts.post_id) ";

        $sql .= " LEFT JOIN " . DB_PREFIX . "ptpost_description pd ON (p.post_id = pd.post_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND pts.store_id = '". (int) $this->config->get('config_store_id') ."' AND p.status = '1' ";


        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                if (!empty($data['filter_description'])) {
                    $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_description']) . "%'";
                }
            }

            if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                $sql .= " OR ";
            }
            $sql .= ")";
        }

        $sql .= " GROUP BY p.post_id";

        $sort_data = array(
            'pd.name',
            'p.sort_order',
            'p.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(pd.name) DESC";
        } else {
            $sql .= " ASC, LCASE(pd.name) ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $post_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $post_data[$result['post_id']] = $this->getPost($result['post_id']);
        }

        return $post_data;
    }

    public function getTotalPosts($data = array()) {
        $sql = "SELECT COUNT(DISTINCT p.post_id) AS total FROM " . DB_PREFIX . "ptpost p";

        $sql .= " LEFT JOIN " . DB_PREFIX . "ptpost_to_store pts ON (p.post_id = pts.post_id) ";

        $sql .= " LEFT JOIN " . DB_PREFIX . "ptpost_description pd ON (p.post_id = pd.post_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND pts.store_id = '". (int) $this->config->get('config_store_id') ."' AND p.status = '1' ";

        if (!empty($data['filter_name']) ) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                if (!empty($data['filter_description'])) {
                    $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_description']) . "%'";
                }
            }

            $sql .= ")";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
    
    public function getPostList($post_list_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ptpost_list pl LEFT JOIN " . DB_PREFIX . "ptpost_list_description pld ON (pl.post_list_id = pld.post_list_id) LEFT JOIN " . DB_PREFIX . "ptpost_list_to_store pls ON (pl.post_list_id = pls.post_list_id) WHERE pl.post_list_id = '" . (int) $post_list_id . "' AND pld.language_id = '" . (int) $this->config->get('config_language_id') . "' AND pls.store_id = '". (int) $this->config->get('config_store_id') ."' AND pl.status = '1' ");

        if ($query->num_rows) {
            return array(
                'post_list_id'     => $query->row['post_list_id'],
                'name'             => $query->row['name'],
                'description'      => $query->row['description'],
                'meta_title'       => $query->row['meta_title'],
                'meta_description' => $query->row['meta_description'],
                'meta_keyword'     => $query->row['meta_keyword'],
                'sort_order'       => $query->row['sort_order'],
                'status'           => $query->row['status']
            );
        } else {
            return false;
        }
    }
}