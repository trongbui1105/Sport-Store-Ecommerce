<?php
class ModelPlazaSlider extends Model
{
    public function addSlider($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ptslider SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "',auto = '" . (int)$data['auto'] . "',delay = '" . (int)$data['delay'] . "',hover = '" . (int)$data['hover'] . "',nextback = '" . (int)$data['nextback'] . "',contrl = '" . (int)$data['contrl'] . "'");

        $ptslider_id = $this->db->getLastId();

        if (isset($data['ptslider_image'])) {
            foreach ($data['ptslider_image'] as $ptslider_image) {

                $slider_store = "";
                if(isset($data['slider_store'])) {
                    $slider_store = implode(',', $data['slider_store']);
                }

                $this->db->query("INSERT INTO " . DB_PREFIX . "ptslider_image SET ptslider_id = '" . (int) $ptslider_id . "', link = '" .  $this->db->escape($ptslider_image['link']) . "', type = '" .  $this->db->escape($ptslider_image['type']) . "', image = '" .  $this->db->escape($ptslider_image['image']) . "', slider_store = '" .$slider_store. "'");

                $ptslider_image_id = $this->db->getLastId();

                foreach ($ptslider_image['ptslider_image_description'] as $language_id => $ptslider_image_description) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "ptslider_image_description SET ptslider_image_id = '" . (int) $ptslider_image_id . "', language_id = '" . (int) $language_id . "', ptslider_id = '" . (int) $ptslider_id . "', title = '" .  $this->db->escape($ptslider_image_description['title']) . "',sub_title = '" .  $this->db->escape($ptslider_image_description['sub_title']) . "',description = '" .  $this->db->escape($ptslider_image_description['description']) . "'");
                }
            }
        }
    }

    public function copySlider($ptslider_id) {
        $slider = $this->getSlider($ptslider_id);
        $sliderImages = $this->getSliderImages($ptslider_id);

        $this->db->query("INSERT INTO " . DB_PREFIX . "ptslider SET name = '" . $this->db->escape($slider['name']) . "', status = '" . (int) $slider['status'] . "',auto = '" . (int) $slider['auto'] . "',delay = '" . (int) $slider['delay'] . "',hover = '" . (int) $slider['hover'] . "',nextback = '" . (int) $slider['nextback'] . "',contrl = '" . (int) $slider['contrl'] . "'");

        $ptslider_id = $this->db->getLastId();

        if (isset($sliderImages)) {
            foreach ($sliderImages as $ptslider_image) {

                $slider_store = "";
                if(isset($ptslider_image['slider_store'])) {
                    $slider_store = implode(',', $ptslider_image['slider_store']);
                }

                $this->db->query("INSERT INTO " . DB_PREFIX . "ptslider_image SET ptslider_id = '" . (int) $ptslider_id . "', link = '" .  $this->db->escape($ptslider_image['link']) . "', type = '" .  $this->db->escape($ptslider_image['type']) . "', image = '" .  $this->db->escape($ptslider_image['image']) . "', slider_store = '" .$slider_store. "'");

                $ptslider_image_id = $this->db->getLastId();

                foreach ($ptslider_image['ptslider_image_description'] as $language_id => $ptslider_image_description) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "ptslider_image_description SET ptslider_image_id = '" . (int) $ptslider_image_id . "', language_id = '" . (int) $language_id . "', ptslider_id = '" . (int) $ptslider_id . "', title = '" .  $this->db->escape($ptslider_image_description['title']) . "',sub_title = '" .  $this->db->escape($ptslider_image_description['sub_title']) . "',description = '" .  $this->db->escape($ptslider_image_description['description']) . "'");
                }
            }
        }
    }

    public function editSlider($ptslider_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "ptslider SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int) $data['status'] . "', auto = '" . (int) $data['auto'] . "', delay = '" . (int) $data['delay'] . "', hover = '" . (int) $data['hover'] . "', nextback = '" . (int) $data['nextback'] . "', contrl = '" . (int) $data['contrl'] . "' WHERE ptslider_id = '" . (int) $ptslider_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "ptslider_image WHERE ptslider_id = '" . (int) $ptslider_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptslider_image_description WHERE ptslider_id = '" . (int) $ptslider_id . "'");

        if (isset($data['ptslider_image'])) {

            foreach ($data['ptslider_image'] as $ptslider_image) {

                $slider_store = "";
                if(isset($data['slider_store'])) {
                    $slider_store = implode(',', $data['slider_store']);
                }
                $this->db->query("INSERT INTO " . DB_PREFIX . "ptslider_image SET ptslider_id = '" . (int) $ptslider_id . "', link = '" .  $this->db->escape($ptslider_image['link']) . "', type = '" .  $this->db->escape($ptslider_image['type']) . "', image = '" .  $this->db->escape($ptslider_image['image']) . "', slider_store = '" .  $slider_store . "'");

                $ptslider_image_id = $this->db->getLastId();

                foreach ($ptslider_image['ptslider_image_description'] as $language_id => $ptslider_image_description) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "ptslider_image_description SET ptslider_image_id = '" . (int) $ptslider_image_id . "', language_id = '" . (int) $language_id . "', ptslider_id = '" . (int) $ptslider_id . "', title = '" .  $this->db->escape($ptslider_image_description['title']) . "', sub_title = '" .  $this->db->escape($ptslider_image_description['sub_title']) . "', description = '" .  $this->db->escape($ptslider_image_description['description']) . "'");
                }
            }
        }
    }

    public function deleteSlider($ptslider_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptslider WHERE ptslider_id = '" . (int) $ptslider_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptslider_image WHERE ptslider_id = '" . (int) $ptslider_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "ptslider_image_description WHERE ptslider_id = '" . (int) $ptslider_id . "'");
    }

    public function getSlider($ptslider_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ptslider WHERE ptslider_id = '" . (int) $ptslider_id . "'");

        return $query->row;
    }

    public function getSliders($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "ptslider";

        $sort_data = array(
            'name',
            'status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
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

    public function getSliderImages($ptslider_id) {
        $ptslider_image_data = array();

        $ptslider_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ptslider_image WHERE ptslider_id = '" . (int) $ptslider_id . "'");

        foreach ($ptslider_image_query->rows as $ptslider_image) {
            $ptslider_image_description_data = array();

            $ptslider_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ptslider_image_description WHERE ptslider_image_id = '" . (int) $ptslider_image['ptslider_image_id'] . "' AND ptslider_id = '" . (int) $ptslider_id . "'");

            foreach ($ptslider_image_description_query->rows as $ptslider_image_description) {
                $ptslider_image_description_data[$ptslider_image_description['language_id']] = array('title' => $ptslider_image_description['title'],
                    'sub_title' => $ptslider_image_description['sub_title'],
                    'description' => $ptslider_image_description['description'],

                );
            }

            $ptslider_image_data[] = array(
                'ptslider_image_description'    => $ptslider_image_description_data,
                'link'                          => $ptslider_image['link'],
                'type'                          => $ptslider_image['type'],
                'image'                         => $ptslider_image['image'],
                'slider_store'                  => $ptslider_image['slider_store']
            );
        }

        return $ptslider_image_data;
    }

    public function getTotalSliders() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "ptslider");

        return $query->row['total'];
    }

    public function setupData() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptslider` (
                `ptslider_id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(64) NOT NULL,
                `status` tinyint(1) NOT NULL,
                `auto` tinyint(1) DEFAULT NULL,
                `delay` int(11) DEFAULT NULL,
                `hover` tinyint(1) DEFAULT NULL,
                `nextback` tinyint(1) DEFAULT NULL,
                `contrl` tinyint(1) DEFAULT NULL,
                `effect` varchar(64) NOT NULL,
                PRIMARY KEY (`ptslider_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptslider_image` (
                `ptslider_image_id` int(11) NOT NULL AUTO_INCREMENT,
                `ptslider_id` int(11) NOT NULL,
                `link` varchar(255) NOT NULL,
                `type` int(11) NOT NULL,
                `slider_store` varchar(110) DEFAULT '0',
                `image` varchar(255) NOT NULL,
                `secondary_image` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`ptslider_image_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ptslider_image_description` (
                `ptslider_image_id` int(11) NOT NULL,
                `ptslider_id` int(11) NOT NULL,
                `language_id` int(11) NOT NULL,
                `title` varchar(64) NOT NULL,
                `sub_title` varchar(64) DEFAULT NULL,
                `description` text,
                PRIMARY KEY (`ptslider_image_id`,`language_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'plaza/slider');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'plaza/slider');
    }
    
    public function deleteSliderData() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptslider_image_description`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptslider_image`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ptslider`;");

        $this->load->model('user/user_group');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/slider');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/slider');
    }
}