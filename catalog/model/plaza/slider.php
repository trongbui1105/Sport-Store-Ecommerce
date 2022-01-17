<?php
class ModelPlazaSlider extends Model
{
    public function getSlider($slider_id) {
        $query = "SELECT * FROM " . DB_PREFIX . "ptslider WHERE ptslider_id = ". (int) $slider_id;
        $result = $this->db->query($query);

        return $result->rows;
    }

    public function getSliderDescription($slider_id) {
        $select ="SELECT * FROM " . DB_PREFIX . "ptslider_image di LEFT JOIN " . DB_PREFIX . "ptslider_image_description did ON (di.ptslider_image_id  = did.ptslider_image_id) WHERE di.ptslider_id = '" . (int) $slider_id . "' AND did.language_id = '" . (int) $this->config->get('config_language_id') . "'";
        $query = $this->db->query($select);

        return $query->rows;
    }
}