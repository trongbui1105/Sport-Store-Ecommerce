<?php 
class ModelPlazaRotateimage extends Model
{
	public function getProductRotateImage($product_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = " . (int) $product_id . " AND is_rotate = 1";

		$query = $this->db->query($sql);
		if($query->num_rows) {
			return $query->row['image'];
		} else {
			return false;
		}
	}
}