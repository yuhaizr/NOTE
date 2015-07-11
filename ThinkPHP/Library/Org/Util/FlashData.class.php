<?php
/**
 * 用于存储数据,当做缓存来处理
 * @author Administrator
 *
 */
class FlashData{
	private static $list = array();

	/**
	 * 把一对键值放入到list中去
	 * @param unknown $key
	 * @param unknown $value
	 */
	static public function flash($key,$value){
		session($key,$value);
		array_push(self::$list,$key);
	}
	/**
	 * 获取list中的所有的键值对
	 */
	static public function getList(){
		return self::$list;
	}
}