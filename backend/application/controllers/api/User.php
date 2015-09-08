<?php

/**
 * Created by PhpStorm.
 * User: rg
 * Date: 8/9/2015
 * Time: 10:51 πμ
 * @author   Tsadimas anargyros <tsadimas@gmail.com>
 */
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


        if ($this->users_model->check_user_exists($email)) {
            $message = array('error' => 'User already registered');
            $this->response([
                'status' => FALSE,
                'message' => $message
            ], REST_Controller::HTTP_OK);
        }


        $user_id = $this->users_model->save_user($email, $this->post('device_id'), $this->post('password'));

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


}