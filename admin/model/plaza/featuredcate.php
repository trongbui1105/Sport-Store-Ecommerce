<?php
class ModelPlazaFeaturedcate extends Model
{
    public function createFeaturedCate() {
        $this->load->language('plaza/featuredcate');

        $result = false;

        $result = $this->checkNaddCol('secondary_image', 'category', 'varchar(255)', 'NULL');
        $result = $this->checkNaddCol('alternative_image', 'category', 'varchar(255)', 'NULL');
        $result = $this->checkNaddCol('is_featured', 'category', 'tinyint(1)', '0');

        if($result) {
            $info_text = $this->language->get('text_info');
            if($info_text != "") {
                $this->session->data['information'] = $info_text;
            }
        }

        return;
    }

    public function checkNaddCol($column, $table, $type, $default_value) {
        $flag = false;

        $check_sql = "SHOW COLUMNS FROM `" . DB_PREFIX . $table . "` LIKE '". $column ."'";

        $query = $this->db->query($check_sql);

        if($query->rows) {
            $flag = true;
        } else {
            $sql = "ALTER TABLE `" . DB_PREFIX . $table . "` ADD `" . $column . "` ". $type ." DEFAULT " . $default_value;
            $this->db->query($sql);
        }

        return $flag;
    }

    public function getCategory($category_id) {
        $query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getCategories($data = array()) {
        $sql = "SELECT c1.secondary_image, c1.alternative_image, c1.is_featured, cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " GROUP BY cp.category_id";

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
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

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getFeaturedCategories($data = array()) {
        $sql = "SELECT c1.secondary_image, c1.alternative_image, c1.is_featured, cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE c1.is_featured = '1' AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " GROUP BY cp.category_id";

        $sort_data = array(
            'name',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
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

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function editFeaturedCate($category_id, $data) {
        $sql1 = "UPDATE " . DB_PREFIX . "category SET secondary_image = '" . $this->db->escape($data['secondary_image']) . "' WHERE category_id = '" . (int)$category_id . "'";

        $this->db->query($sql1);

        $sql2 = "UPDATE " . DB_PREFIX . "category SET alternative_image = '" . $this->db->escape($data['alternative_image']) . "' WHERE category_id = '" . (int)$category_id . "'";

        $this->db->query($sql2);

        $sql3 = "UPDATE " . DB_PREFIX . "category SET is_featured = '" . (int) $data['is_featured'] . "' WHERE category_id = '" . (int)$category_id . "'";

        $this->db->query($sql3);
    }
}