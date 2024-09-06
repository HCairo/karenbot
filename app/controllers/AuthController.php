<?php
namespace Controllers;

use Models\AuthModel;
use Views\AuthView;

class AuthController {
    protected $model;
    protected $view;

    public function __construct() {
        $this->model = new AuthModel();
        $this->view = new AuthView();
    }
}