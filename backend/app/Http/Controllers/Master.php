<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Valet;
use DB;

class Master extends ApiController
{
    use Valet;

    public function __construct()
    {
        parent::__construct();
    }

    
}