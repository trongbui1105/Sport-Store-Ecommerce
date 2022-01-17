<?php
class ModelPlazaNewsletter extends Model
{
    public function checkMail($mail) {
        $valid = true;
        
        $sql = "SELECT * FROM " . DB_PREFIX . "customer";

        $results = $this->db->query($sql);

        foreach ($results->rows as $result) {
            if($result['email'] == $mail) {
                $valid = false;
                break;
            }
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "ptnewsletter_email";

        $results = $this->db->query($sql);

        foreach ($results->rows as $result) {
            if($result['mail'] == $mail) {
                $valid = false;
                break;
            }
        }
        
        return $valid;
    }
    
    public function subscribeMail($mail) {
        $sql = "INSERT INTO " . DB_PREFIX . "ptnewsletter_email SET subscribe = '1', mail = '" . $this->db->escape($mail) . "'";

        $this->db->query($sql);
    }
}

