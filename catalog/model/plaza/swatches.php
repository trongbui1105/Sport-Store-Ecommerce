<?php
class ModelPlazaSwatches extends Model
{
    public function getOptionIdByProductOptionValueId($product_option_value_id) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "product_option_value` WHERE product_option_value_id = '" . (int) $product_option_value_id . "'";
        $query = $this->db->query($sql);
        if($query->row) {
            return $query->row['option_id'];
        } else {
            return false;
        }
    }
}