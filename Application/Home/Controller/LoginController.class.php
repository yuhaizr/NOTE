<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class LoginController extends Controller{


	public function  checkLogin(){
		$username = I("post.username");
		$password = I('post.password');
		
		if(empty($username)){
			$this->error('用户名不能为空',U('index'));
		}
		
		if(empty($password)){
			$this->error('密码不能为空',U('index'));
		}
		
		//生成认真添加
		$map = array();
		//支持绑定账号登入
		$map['account'] = $username;
		
		//根据账号获取该用户的登入信息
		$authInfo = Rbac::authenticate($map);
		
// 		dump($authInfo);
		
		if(false == $authInfo){
			$this->error("账号不存在");
		}else {
			if($authInfo['password'] != $password){
				$this->error("密码错误");
			}
			$this->dologin($authInfo);
		}
	
	}
	
	//账号密码验证成功之后把用户信息存入当session中去
	private  function dologin($authInfo){
		$_SESSION[C('USER_AUTH_KEY')] = $authInfo['id'];
		$_SESSION['loginUserName'] = $authInfo['nickname'];
		
		Rbac::saveAccessList();
		
		$this->success('登入成功',__APP__.'/Home/Index/index');
		
	}
	
	public function login(){
		$this->display();
	}
	
	
}