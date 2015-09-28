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

    public function get_stops_by_route($route, $dir)
    {

        $sql = "select bus_stops.s_id, bus_stops.name_el, bus_stops.name_en, bus_stops.lat, bus_stops.lon, line_stops.direction_flag
                from bus_stops, line_stops, bus_lines
                where bus_stops.s_id=line_stops.stop_id  and line_stops.direction_flag = " . $dir . " and line_stops.line_id=bus_lines._id and bus_lines._id='" . $route . "'";


        $query_response = $this->db->query($sql);
        $result = $query_response->result();

        if ($query_response->num_rows() < 1) {
            return NULL;
        } else {
            return $result;
        }

        log_message('info', $this->db->last_query());
        return $query->result();

    }

    public function get_nearest_stops($lat, $lon, $dist)
    {

        $sql = "SELECT  s_id,  name_el, name_en, street_el, street_en, lat, lon, (
                    6371 * ACOS(COS(RADIANS(" . $lat . ")) * COS(RADIANS(lat)) * COS(RADIANS(lon) - RADIANS(" . $lon . ")) + SIN(RADIANS(" . $lat . ")) * SIN(RADIANS(lat)))
                ) AS distance
            FROM bus_stops
            HAVING distance < " . $dist . "
            ORDER BY distance";
        $query_response = $this->db->query($sql);
        log_message('info', $this->db->last_query());
        $result = $query_response->result();

        if ($query_response->num_rows() < 1) {
            return NULL;
        } else {
            return $result;
        }
        return $query->result();
    }

    public function get_nearest_lines($lat, $lon, $dist)
    {

        $sql = "SELECT   line_id, direction_flag, line_name_el, line_name_en, (
                    6371 * ACOS(COS(RADIANS(" . $lat . ")) * COS(RADIANS(lat)) * COS(RADIANS(lon) - RADIANS(" . $lon . ")) + SIN(RADIANS(" . $lat . ")) * SIN(RADIANS(lat)))
                ) AS distance
            FROM bus_stops, line_stops, bus_lines
            where bus_stops.s_id = line_stops.stop_id and line_stops.line_id = bus_lines._id
            HAVING distance < " . $dist . "
            ORDER BY distance";
        $query_response = $this->db->query($sql);
        log_message('info', $this->db->last_query());
        //$result = $query_response->result();
        $result = array();
        if ($query_response->num_rows() > 0) {
            foreach ($query_response->result() as $row) {
                $this->db->join('user_locations', 'bus_lines._id =  user_locations.routeid');
                $dir=$row->direction_flag;
                $this->db->where('direction',$dir);
                $query1 = $this->db->get_where('bus_lines', array('_id' => $row->line_id));

                $obj = new stdClass();

                $obj->line_id = $row->line_id;
                $obj->direction_flag = $row->direction_flag;
                $obj->line_name_el = $row->line_name_el;
                $obj->line_name_en = $row->line_name_en;
                $obj->distance = $row->distance;
                $obj->tracked = $query1->num_rows();

                array_push($result, $obj);
            }

        }
        return $result;


        /* if ($query_response->num_rows() < 1) {
             return NULL;
         } else {
             return $result;
         }
         return $query->result();*/
    }

    public function findbusfromstop($stop)
    {

        $result = array();
        $this->db->select('line_stops.line_id');
        $this->db->select('line_stops.direction_flag');
        $this->db->select('bus_lines.line_name_el');
        $this->db->select('bus_lines.line_name_en');
        $this->db->select('bus_lines.is_circular');
        $this->db->join('bus_lines', 'line_stops.line_id = bus_lines._id');
        //$this->db->join('user_locations','line_stops.line_id =  user_locations.routeid');
        $query = $this->db->get_where('line_stops', array('stop_id' => $stop));
        //log_message('info', $this->db->last_query());


        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {

                $this->db->join('user_locations', 'bus_lines._id =  user_locations.routeid');
                $dir=$row->direction_flag;
                $this->db->where('direction',$dir);
                $query1 = $this->db->get_where('bus_lines', array('_id' => $row->line_id));

                $obj = new stdClass();

                $obj->line_id = $row->line_id;
                $obj->direction_flag = $row->direction_flag;
                $obj->line_name_el = $row->line_name_el;
                $obj->line_name_en = $row->line_name_en;
                $obj->is_circular = $row->is_circular;
                $obj->tracked = $query1->num_rows();

                array_push($result, $obj);


            }
        }

        //log_message('info', print_r($result));
        //return $query->result();
        return $result;
    }

    public function gettimes($line, $dir, $day)
    {
        $sql = "SELECT time(minute) as m
                FROM bus_times
                WHERE line_id = " . $line . "
                        AND day = " . $day . "
                        AND direction = " . $dir . "
                ORDER BY m";


        $query_response = $this->db->query($sql);
        log_message('info', $this->db->last_query());
        $result = $query_response->result();

        if ($query_response->num_rows() < 1) {
            return NULL;
        } else {
            return $result;
        }
        return $query->result();

    }

}
