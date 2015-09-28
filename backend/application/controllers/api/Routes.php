<?php
/**
 * Created by PhpStorm.
 * User: rg
 * Date: 21/9/2015
 * Time: 10:49 πμ
 */

require APPPATH . '/libraries/REST_Controller.php';

class Routes extends REST_Controller
{
    function __construct()
    {
        // Construct our parent class
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();

        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; //500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; //100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key
    }


    function routes_get()
    {
        $routes = $this->routes_model->get_routes();
        if ($routes) {
            $this->response($routes, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Routes could not be found'), 404);
        }

    }

    function routestops_get()
    {
        if (!$this->get('route') || !$this->get('dir')) {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }

        $stops = $this->routes_model->get_stops_by_route($this->get('route'), $this->get('dir'));

        if ($stops) {
            $message = array('stops' => $stops, 'success' => 'true');
            $this->response($message, 200);
        }
    }

    function nearstops_get()
    {
        if (!$this->get('lat') || !$this->get('lon')) {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }

        if (!$this->get('dist')) {
            $dist = '0.2';
        } else {
            $dist = $this->get('dist');
        }

        $stops = $this->routes_model->get_nearest_stops($this->get('lat'), $this->get('lon'), $dist);
        if ($stops) {
            $message = array('stops' => $stops, 'success' => 'true');
            $this->response($message, 200);
        }
    }

    function nearlines_get()
    {
        //@todo get tracked
        if (!$this->get('lat') || !$this->get('lon')) {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }

        if (!$this->get('dist')) {
            $dist = '0.2';
        } else {
            $dist = $this->get('dist');
        }

        $stops = $this->routes_model->get_nearest_lines($this->get('lat'), $this->get('lon'), $dist);
        if ($stops) {
            $message = array('lines' => $stops, 'success' => 'true');
            $this->response($message, 200);
        }
    }

    function linesfromstop_get()
    {
        if (!$this->get('stop')) {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }

        $lines = $this->routes_model->findbusfromstop($this->get('stop'));
        if ($lines) {
            $message = array('lines' => $lines, 'success' => 'true');
            $this->response($message, 200);
        }
    }

    function timesforline_get(){
        if (!$this->get('line') || !$this->get('day') || !$this->get('dir')  ) {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }

        $times=$this->routes_model->gettimes($this->get('line'),$this->get('dir'),$this->get('day'));
        if ($times) {
            $message = array('times' =>  $times, 'success' => 'true');
            $this->response($message, 200);
        }
    }

    public function buslocation_get(){
        if (!$this->get('route') || !$this->get('dir')  ) {
            $message = array('success' => 'false');
            $this->response($message, 400);
        }
        $bus = $this->routes_model->get_bus_location($this->get('route'),$this->get('dir'));
        if ($bus) {
            $this->response($bus, 200); // 200 being the HTTP response code
        } else {
            $this->response(array('error' => 'Bus could not be found'), 404);
        }

    }

}