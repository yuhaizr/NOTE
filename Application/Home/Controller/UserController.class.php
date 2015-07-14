<?php
namespace Home\Controller;
use Home\Controller\BaseController;
use Home\Model\UserModel;
class UserController extends BaseController{
	/**
	 * 显示用户列表
	 */
	public function userList(){
		$user = new UserModel();
		$userList = $user->select();
		
		$this->assign('userList',$userList);
		$this->display();
	}
	/**
	 * 添加用户
	 */
	public function userAdd(){
		$type = I("type");
		if(!$type){
			$this->display();
			exit();
		}
		
		$data['username'] = I("username");
		$data['password'] = I("password");
		$user = new UserModel();
		$u_id = $user->add($data);
		if($u_id){
			$this->success('添加成功',__APP__.'/Home/User/userList');
		}else {
			$this->error('添加失败',__APP__.'/Home/User/userAdd');
		}
	}
	
	/**
	 * 删除用户
	 */
	public function userDel(){
		
		$id = I("id");
		
		$user = new UserModel();
		$status = $user->where("id={$id}")->delete();
		if($status){
			$this->success('删除成功');
		}else{
			$this->error("删除失败");
		}
	}
	
	public function userEdit(){
		$type = I("type");
		$id = I("id");
		$username = I("username");
		if(!$id){
			$this->error('不能获取用户信息',__APP__."/Home/User/userList");
		}
		if(!$type){
			
			$user = new UserModel();
			$userInfo = $user->where("id={$id}")->select();
			if($userInfo){
				$this->assign('userInfo',$userInfo[0]);
				$this->display();
				exit();
			}else {
				$this->error('不能获取用户信息',__APP__."/Home/User/userList");
			}
			
		}
		
		$data['username'] = $username;
		$user = new UserModel();
		$status = $user->where("id={$id}")->data($data)->save();
		
		if($status){
			$this->success('修改成功',__APP__."/Home/User/userList");
		}else{
			$this->error('修改失败',__APP__."/Home/User/userList");
		}
		
	}
	
	
}