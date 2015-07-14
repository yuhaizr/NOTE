<?php
namespace Home\Model;
use Think\Model;
class RoleModel extends Model{
	
	public function getUserListByRoleId($role_id){
		
		$SQL  = " SELECT id,username ";
		$SQL .= " FROM think_user   ";
		$SQL .= " JOIN think_role_user ON think_user.id = think_role_user.user_id ";
		$SQL .= " WHERE think_role_user.role_id = {$role_id} ";
		
		$userList = $this->query($SQL);
		
		return $userList;
		
	}

	
}