<?php
/**
 * Created by PhpStorm.
 * User: rg
 * Date: 8/9/2015
 * Time: 11:05 πμ
 */

class Users_model extends CI_Model
{

    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

    public function get_users()
    {
        $query = $this->db->get('users');
        return $query->result();
    }

    public function check_user_exists($email)
    {

        $query = $this->db->get_where('users', array('email' => $email));
        if ($query->num_rows() != 1) {
            return false;
        } else {
            return true;
        }
    }

    public function save_user($email, $dev, $pass)
    {
        $data = array(
            'email' => $email,
            'device_id' => $dev,
            'password' => md5($pass),
        );

        try {

            $this->db->insert('users', $data);
            $insert_id = $this->db->insert_id();
            if (!$insert_id) throw new Exception($this->db->_error_message(), $this->db->_error_number());
            $this->db->trans_complete();
            if ($insert_id > 0) {
                return $insert_id;
            }

        } catch (Exception $e) {
            log_message('error', sprintf('%s : %s : DB transaction failed. Error no: %s, Error msg:%s, Last query: %s', __CLASS__, __FUNCTION__, $e->getCode(), $e->getMessage(), print_r($this->db->last_query(), TRUE)));
            return false;
        }
    }
}