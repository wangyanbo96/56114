<?php
namespace app\index\controller;
use think\Model;
use think\Db;
use think\Controller;
use think\View;

class Map extends Index
{

    public function index()
    {
        return $this->fetch();
    }


    
    
}
