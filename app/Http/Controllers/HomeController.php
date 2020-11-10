<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * HomeController constructor.
     */
    public
    function __construct()
    {
        //
    }

    public function index()
    {
        return view('welcome');       
    }    
}
