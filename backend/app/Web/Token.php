<?php

namespace App\Web;
use Illuminate\Support\Facades\DB;
class Token extends AdminModel
{
    protected $table ="token";
    protected $fillable = [];
    public $timestamps = false;
    public $incrementing = false;
}
