<?php
/**
 * Created by PhpStorm.
 * User: rg
 * Date: 10/9/2015
 * Time: 11:00 πμ
 */

class Routes_model extends CI_Model
{

    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }


    public function get_routes()
    {
        $query = $this->db->get('bus_lines');
        log_message('info', $this->db->last_query());
        return $query->result();
    }
}
