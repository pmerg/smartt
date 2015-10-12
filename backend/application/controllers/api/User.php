<?php

/**
 * Created by PhpStorm.
 * User: rg
 * Date: 8/9/2015
 * Time: 10:51 πμ
 * @author   Tsadimas anargyros <tsadimas@gmail.com>
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller
{

    function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
    }


    /**
     * Register a user
     * @var string $email
     * @var string $device_id
     * @var string $password
     * @return object $user or error message
     */
    function register_post()
    {
        if ((!$this->post('email') || !$this->post('device_id') || !$this->post('password'))) {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }

        $email = $this->post('email');


        // Remove all illegal characters from email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            log_message('info', "$email is a valid email address");
        } else {
            log_message('info', "$email is not a valid email address");
            $message = array('error' => 'Not accepted email', 'success' => 'false');
            $this->response([
                'status' => FALSE,
                'message' => $message
            ], REST_Controller::HTTP_BAD_REQUEST);
        }


        if ($this->user_model->check_user_exists($email)) {
            $message = array('error' => 'User already registered');
            $this->response([
                'status' => FALSE,
                'message' => $message
            ], REST_Controller::HTTP_OK);
        }


        $user_id = $this->user_model->save_user($email, $this->post('device_id'), $this->post('password'));

        if (!$user_id) {
            $message = array('error' => 'DB error, user not inserted!', 'success' => 'false');
            $this->response([
                'status' => FALSE,
                'message' => $message
            ], REST_Controller::HTTP_CONFLICT);
        }

        log_message('info', 'db returned userid ' . $user_id);


        $message = array(
            'id' => $user_id,
            'device_id' => $this->post('device_id'),
            'email' => $this->post('email'),
            'message' => 'user registered!',
            );

        $this->response($message, 200);
        $this->response([
            'status' => TRUE,
            'message' => $message
        ], REST_Controller::HTTP_OK);
    }

    function login_post()
    {
        if ((!$this->post('email') || !$this->post('device_id') || !$this->post('password'))) {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }

        $email = $this->post('email');


        // Remove all illegal characters from email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            log_message('info', "$email is a valid email address");
        } else {
            log_message('info', "$email is not a valid email address");
            $message = array('error' => 'Not accepted email', 'success' => 'false');
            $this->response($message, 400);
        }


        if ($this->user_model->check_user_exists($email)) {

            $dev_id = $this->user_model->check_user($email, $this->post('password'));
            $user_id=$this->user_model->get_id_from_email($email);
            if (empty($dev_id)) {
                $message = array('message' => 'User not authorized', 'success' => 'false');
                $this->response($message, 403);
            }
            if ($dev_id == $this->post('device_id')) {

                $message = array('message' => 'User login ok', 'email'=> $email, 'success' => 'true', 'user_id'=>$user_id);
                $this->response($message, 200);
            } else {
                $this->user_model->change_device_id($email, $this->post('device_id'));
                $message = array('message' => 'Device id changed', 'email'=>$email,'success' => 'true', 'user_id'=>$user_id);
                $this->response($message, 200);
            }

        }


        $user_id = $this->user_model->save_user($email, $this->post('device_id'), $this->post('password'));

        if (!$user_id) {
            $message = array('error' => 'DB error, user not inserted!', 'success' => 'false');
            $this->response($message, 409);
        }

        log_message('info', 'db returned userid ' . $user_id);


        $message = array('id' => $user_id, 'device_id' => $this->post('device_id'), 'email' => $this->post('email'), 'message' => 'user registered!', 'success' => 'true');

        $this->response($message, 200);
    }

    public function location_post()
    {

        //log_message('lat', $this->post('lat'));
        if (!$this->post('email') || !$this->post('lat') || !$this->post('lon') || !$this->post('route') || !$this->post('dir'))  {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }

        $check=$this->getuserdistancefromline($this->post('lat'),$this->post('lon'),$this->post('route'),$this->post('dir'));
        //var_dump($check);
        if ($check['distance'] > 0.1) {
            $message = array('message' => 'User away from line', 'success' => 'false');
            $this->response($message, 200);
        }
        $user_id = $this->user_model->get_id_from_email($this->post('email'));
        if ($user_id) {
            $user_lat=$check['lat'];
            $user_lon=$check['lon'];
            if ($this->user_model->save_user_location($user_id, $user_lat, $user_lon,$this->post('route'),$this->post('dir'))) {
                $message = array('message' => 'User location saved', 'success' => 'true');
                $this->response($message, 200);

            } else {
                $message = array('error' => 'User data not saved', 'success' => 'false');
                $this->response($message, 400);
            }

        } else {
            $message = array('error' => 'User does not exist', 'success' => 'false');
            $this->response($message, 400);
        }
    }




    public function profile_get(){
        if (!$this->get('email')){
            $message = array('success' => 'false');
            $this->response($message, 400);
        }
        $user=$this->user_model->get_user_profile($this->get('email'));

        if ($user) {
            $message = array('user' => $user, 'success' => 'true');
            $this->response($message, 200);
        }
        else {
            $message = array('error'=>'no user found','success' => 'false');
            $this->response($message, 200);
        }

    }

    public function rating_post()
    {

        if (!$this->post('email') || !$this->post('route') || !$this->post('dir') || !$this->post('arrival_time') || !$this->post('comfort') || !$this->post('route_duration') || !$this->post('driver_rating'))  {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }

        $user_id = $this->user_model->get_id_from_email($this->post('email'));

        if ($user_id) {
            $comment=($this->post('comment')) ? $this->post('comment') : "";

            if ($this->user_model->save_user_rating($user_id, $this->post('route'), $this->post('dir'), $this->post('arrival_time'), $this->post('comfort'), $this->post('route_duration'), $this->post('driver_rating'),$comment)) {
                $message = array('message' => 'User rating saved', 'success' => 'true');
                $this->response($message, 200);

            } else {
                $message = array('error' => 'User data not saved', 'success' => 'false');
                $this->response($message, 400);
            }

        } else {
            $message = array('error' => 'User does not exist', 'success' => 'false');
            $this->response($message, 400);
        }
    }


    public function getuserdistancefromline($user_lat,$user_lon,$route,$dir) {
        //get origin, destination and waypoints in order to call google directions api
        $waypoints=$this->routes_model->get_waypoints_from_route($route,$dir);
        //var_dump($waypoints);
        if ($waypoints['waypoints']) {//if waypoints found

            //implode lat,lon with "|" in order to send to google directions api
            $way = implode('|', array_map(function($a) {
                return $a->point;
            }, $waypoints['waypoints']));


            //var_dump($way);
            //check of waypoints exists in database
            $waypoints_db=$this->routes_model->check_route_waypoints($route, $dir);


            //var_dump($waypoints_db);
            if ($waypoints_db) {

                $points=$waypoints_db;

            }
            else {
                $d=directions_call($waypoints['origin']['0']->point, $waypoints['destination']['0']->point, $way,$this->get('route'),$this->get('dir'));
                //var_dump($d);
                $points=$this->routes_model->check_route_waypoints($route, $dir);
            }
            $message = array('points' =>  $points, 'success' => 'true');
            $message1=array();
            foreach ($points as $point) {
                $message1[]=$this->distance($user_lat, $user_lon, $point->lat,$point->lon, "K");
            }

            $min=min( $message1);
            $key = array_search($min, $message1);
            $the_point=$points[$key];
            //$message = array('waypoints' =>  $waypoints['waypoints'], 'success' => 'true');
           // $this->response($min, 200);
            $data['distance']=$min;
            $data['lat']=$the_point->lat;
            $data['lon']=$the_point->lon;
           return $data;
        }
        else { //if no waypoints defined
            return FALSE;
        }



    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}