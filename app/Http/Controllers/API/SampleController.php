<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

class SampleController extends APIController
{
    public function hello()
    {
        return "Hej z kontrolera!";
    }
}
