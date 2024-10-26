<?php

namespace Members\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Members extends BaseController
{
    public function index()
    {
        return $this->display_view('Members\index');
    }
}
