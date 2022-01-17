<?php
class ModelPlazaQuickview extends Model
{
    public function getProductBySeoUrl($seo_url) {
        $product_id = 0;

        $keywords = explode('/', $seo_url);

        foreach ($keywords as $keyword) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($keyword) . "'");

            if ($query->num_rows) {
                $url = explode('=', $query->row['query']);

                if ($url[0] == 'product_id') {
                    $product_id = $url[1];
                }
            }
        }

        return $product_id;
    }
}