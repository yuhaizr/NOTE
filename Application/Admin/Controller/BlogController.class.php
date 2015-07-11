<?php
namespace Admin\Controller;
use Think\Controller;
use Home\Controller\BaseController;
class  BlogController extends BaseController{
	public  function index(){
		$this->display();
	}
	
	public function  blogList(){
		$Blog = D("Blog");
		$blogList = $Blog->field('title,content,readCount,tags')->select();
		
	
		foreach ($blogList as $key => $val){
			$val_new = array();
			
			foreach ($val as $k => $v){
				$val_new[] = $v;	
			}
		
			$blogList[$key] = $val_new;	
			
		}
		
		
		$blogList_json = json_encode($blogList);
		
// 		$blogList_json = str_replace("{", "[", $blogList_json);
// 		$blogList_json = str_replace("}", "]", $blogList_json);
		
		$this->assign('blogList',$blogList_json);
		
		$this->display();
	}
	
	public function getBlogList(){
		$Blog = D("Blog");
		$blogList = $Blog->select();
		
		$blogList_json = json_encode($blogList,true);
		echo json_encode(array(
		    "draw" => 2,
		    "recordsTotal" => 1,
		    "recordsFiltered" => 1,
		    "data" => $blogList
		),JSON_UNESCAPED_UNICODE);
		exit();
	}
	
	public function  searchBlog(){

		//获取Datatables发送的参数 必要
		$draw = $_GET['draw'];//这个值作者会直接返回给前台
		
		//排序
		$order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
		$order_dir = $_GET['order']['0']['dir'];//ase desc 升序或者降序
		
		//拼接排序sql
		$orderSql = "";
		if(isset($order_column)){
			$i = intval($order_column);
			switch($i){
				case 0;$orderSql = " order by first_name ".$order_dir;break;
				case 1;$orderSql = " order by last_name ".$order_dir;break;
				case 2;$orderSql = " order by position ".$order_dir;break;
				case 3;$orderSql = " order by office ".$order_dir;break;
				case 4;$orderSql = " order by start_date ".$order_dir;break;
				case 5;$orderSql = " order by salary ".$order_dir;break;
				default;$orderSql = '';
			}
		}
		//搜索
		$search = $_GET['search']['value'];//获取前台传过来的过滤条件
		
		//分页
		$start = $_GET['start'];//从多少开始
		$length = $_GET['length'];//数据长度
		$limitSql = '';
		$limitFlag = isset($_GET['start']) && $length != -1 ;
		if ($limitFlag ) {
			$limitSql = " LIMIT ".intval($start).", ".intval($length);
		}
		
		//定义查询数据总记录数sql
		$sumSql = "SELECT count(id) as sum FROM DATATABLES_DEMO";
		//条件过滤后记录数 必要
		$recordsFiltered = 0;
		//表的总记录数 必要
		$recordsTotal = 0;
		$recordsTotalResult = $db->query($sumSql);
		while ($row = $recordsTotalResult->fetchArray(SQLITE3_ASSOC)) {
			$recordsTotal =  $row['sum'];
		}
		//定义过滤条件查询过滤后的记录数sql
		$sumSqlWhere =" where first_name||last_name||position||office||start_date||salary LIKE '%".$search."%'";
		if(strlen($search)>0){
			$recordsFilteredResult = $db->query($sumSql.$sumSqlWhere);
			while ($row = $recordsFilteredResult->fetchArray(SQLITE3_ASSOC)) {
				$recordsFiltered =  $row['sum'];
			}
		}else{
			$recordsFiltered = $recordsTotal;
		}
		
		//query data
		$totalResultSql = "SELECT first_name,last_name,position,office,start_date,salary FROM DATATABLES_DEMO";
		$infos = array();
		if(strlen($search)>0){
			//如果有搜索条件，按条件过滤找出记录
			$dataResult = $db->query($totalResultSql.$sumSqlWhere.$orderSql.$limitSql);
			while ($row = $dataResult->fetchArray(SQLITE3_ASSOC)) {
				$obj = array($row['first_name'], $row['last_name'], $row['position'], $row['office'], $row['start_date'], $row['salary']);
				array_push($infos,$obj);
			}
		}else{
			//直接查询所有记录
			$dataResult = $db->query($totalResultSql.$orderSql.$limitSql);
			while ($row = $dataResult->fetchArray(SQLITE3_ASSOC)) {
				$obj = array($row['first_name'], $row['last_name'], $row['position'],$row['office'], $row['start_date'], $row['salary']);
				array_push($infos,$obj);
			}
		}
		
		/*
		 * Output 包含的是必要的
		*/
		echo json_encode(array(
				"draw" => intval($draw),
				"recordsTotal" => intval($recordsTotal),
				"recordsFiltered" => intval($recordsFiltered),
				"data" => $infos
		),JSON_UNESCAPED_UNICODE);
	}
	
}