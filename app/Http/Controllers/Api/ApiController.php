<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp;

class ApiController extends Controller
{
    //
    public function test(Request $request){
        $url="http://www.api.com/api_test.php";
        $client=new GuzzleHttp\Client();
        $result=$client->request('GET',$url);
        $info=$result->getBody();
        echo $info;
    }
}
