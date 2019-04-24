<?php
namespace app\index\controller;

class Sel extends Index{
	public function index(){
		return $this->fetch();
	}
}

// update user set host = '%' where user = 'root';
// FLUSH PRIVILEGES;