<?php
namespace Home\Controller;

use Home\Controller\BaseController;
use Home\Model\RoleModel;
class RoleController extends BaseController{
	/**
	 * 获取角色列表
	 */
	public function roleList(){
		
		$role = new RoleModel();
		$roleList = $role->select();
		
		$this->assign('roleList',$roleList);
		
		$this->display();
	}
	/**
	 * 添加角色
	 */
	public function roleAdd(){
		
		$type = I("type");
		if(!$type){
			$this->display();
			exit();
		}
		
		$data['name'] = I("name");
		$data['status'] = I("status")=='on'?1:0;
		$data['remark'] = I("remark");
		
		$role = new RoleModel();
		$r_id = $role->add($data);
		if($r_id){
			$this->crmSuccess('添加成功',__APP__."/Home/Role/roleList");
		}else{
			$this->crmError('添加失败',__APP__."/Home/Role/roleAdd");
		}
	}
	/**
	 * 删除角色
	 */
	public function roleDel(){
		$id = I("id");
		
		$role = new RoleModel();
		
		$status = $role->where("id={$id}")->delete();
	
		if($status){
			$this->success('删除成功',__APP__."/Home/Role/roleList");
		}else {
			$this->error('删除失败',__APP__."/Home/Role/roleList");
		}
	}
	/**
	 * 角色编辑
	 */
	public function  roleEdit(){
		$id = I("id");
		$type = I("type");
		$role = new RoleModel();
		if (!$id){
			$this->error('无法获取角色信息',__APP__."/Home/Role/roleList");
		}
		
		if(!$type){
			$roleInfo = $role->where("id={$id}")->select();
			if($roleInfo){
				if($roleInfo[0]['status'] == 1){
					$roleInfo[0]['status'] = "checked='checked'";
				}
				$this->assign('roleInfo',$roleInfo[0]);
				$this->display();
			}else{
				$this->error('无法获取角色信息',__APP__."/Home/Role/roleList");
			}
		
		}
		if("roleEdit" == $type){
			$data['id'] = $id;
			$data['name'] = I('name');
			$data['status'] = I('status')=="on"?1:0;
			$data['remark'] = I('remark');
			
			$status = $role->save($data);
			if($status){
				$this->success('编辑成功',__APP__."/Home/Role/roleList");
			}else{
				$this->error('编辑失败',__APP__."/Home/Role/roleList");
			}			
		}

		
	}
	/**
	 * 通过角色id获取用户列表
	 */
	public function  roleUserList(){
		$id = I("id");
		$type = I("type");
		$role = new RoleModel();
		
		if(!$id){
			$this->error('无法获取角色信息',__APP__."/Home/Role/roleList");
		}
		$user = D("User");
		$role = D("Role");
		$allUser = $user->select();
		$allRole = $role->select();
		
		foreach ($allRole as $key =>$val){
			if($val['id'] == $id){
				$allRole[$key]['selected'] = "selected='selected'";
			}
		}

		$userList = $role->getUserListByRoleId($id);
		$roleUserIds = array();
		foreach ($userList as $key => $val){
			$roleUserIds[] = $val['id'];
		}
		
		
		foreach ($allUser as $key =>$val){
			if(in_array($val['id'], $roleUserIds)){
				$allUser[$key]['checked'] = "checked='checked'";
			}
		}
		
		if("roleUserList" == $type){
			$allUser = json_encode($allUser,true);
			echo $allUser;
			exit();
		}
		
		$this->assign('allUser',$allUser);
		$this->assign('allRole',$allRole);

		$this->display();

	}
	/**
	 * 改变角色小的用户
	 */
	public function changeUserList(){
		
		$role_id = I("role_id");
		$userIds = I("userIds");
	}
	
	
}