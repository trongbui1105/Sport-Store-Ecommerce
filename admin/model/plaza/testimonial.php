<?php
class ModelPlazaTestimonial extends Model
{
    public function addTestimonial($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "pttestimonial SET status = '" . (int) $this->request->post['status'] . "', sort_order = '" . (int) $this->request->post['sort_order'] . "'");

        $testimonial_id = $this->db->getLastId();

        $this->db->query("INSERT INTO " . DB_PREFIX . "pttestimonial_description SET pttestimonial_id = '" . (int) $testimonial_id . "', customer_name = '" . $this->db->escape($data['testimonial_description']['customer_name']) . "', image = '" . $this->db->escape($data['image']) . "', content = '" . $this->db->escape($data['testimonial_description']['content']) . "'");

        $this->cache->delete('testimonial');
    }

    public function editTestimonial($testimonial_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "pttestimonial SET status = '" . (int) $this->request->post['status'] . "', sort_order = '" . (int) $data['sort_order'] . "' WHERE pttestimonial_id = '" . (int) $testimonial_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "pttestimonial_description WHERE pttestimonial_id = '" . (int) $testimonial_id . "'");

        $this->db->query("INSERT INTO " . DB_PREFIX . "pttestimonial_description SET pttestimonial_id = '" . (int) $testimonial_id . "', customer_name = '" . $this->db->escape($data['testimonial_description']['customer_name']) . "', image = '" . $this->db->escape($data['image']) . "', content = '" .  $this->db->escape($data['testimonial_description']['content']) . "'");
    }

    public function deleteTestimonial($testimonial_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "pttestimonial WHERE pttestimonial_id = '" . (int) $testimonial_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "pttestimonial_description WHERE pttestimonial_id = '" . (int) $testimonial_id . "'");

        $this->cache->delete('testimonial');
    }

    public function getTestimonial($testimonial_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "pttestimonial t LEFT JOIN " . DB_PREFIX . "pttestimonial_description td ON (t.pttestimonial_id = td.pttestimonial_id) WHERE t.pttestimonial_id = '" . (int) $testimonial_id . "' ");
        return $query->row;
    }

    public function getTestimonials($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "pttestimonial_description td LEFT JOIN " . DB_PREFIX . "pttestimonial t ON (t.pttestimonial_id = td.pttestimonial_id)";

            $sort_data = array(
                'td.customer_name',
                't.sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY td.customer_name";
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
        } else {
            $testimonial_data = $this->cache->get('testimonial.' . $this->config->get('config_language_id'));

            if (!$testimonial_data) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pttestimonial t LEFT JOIN " . DB_PREFIX . "pttestimonial_description td ON (t.pttestimonial_id = td.pttestimonial_id) ORDER BY td.customer_name ASC");

                $testimonial_data = $query->rows;

                $this->cache->set('testimonial.' . $this->config->get('config_language_id'), $testimonial_data);
            }

            return $testimonial_data;
        }
    }

    public function getTestimonialDescriptions($testimonial_id) {
        $testimonial_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pttestimonial_description WHERE pttestimonial_id = '" . (int) $testimonial_id . "'");

        foreach ($query->rows as $result) {
            $testimonial_description_data = array(
                'customer_name'       => $result['customer_name'],
                'content' => $result['content']
            );
        }

        return $testimonial_description_data;
    }

    public function getTotalTestimonials() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pttestimonial");

        return $query->row['total'];
    }

    public function install() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pttestimonial` (
                `pttestimonial_id` int(11) NOT NULL AUTO_INCREMENT,
                `status` int(1) NOT NULL DEFAULT '0',
                `sort_order` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY (`pttestimonial_id`)
        ) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pttestimonial_description`(
                `pttestimonial_id` int(11) unsigned NOT NULL,
                `language_id` int(11) NOT NULL,
                `image` varchar(255) NOT NULL,
                `customer_name` varchar(255) NOT NULL,
                `content` text,
                PRIMARY KEY (`pttestimonial_id`,`language_id`)
            ) DEFAULT COLLATE=utf8_general_ci;");

        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'plaza/testimonial');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'plaza/testimonial');
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pttestimonial`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pttestimonial_description`");

        $this->load->model('user/user_group');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'plaza/testimonial');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'plaza/testimonial');
    }
}