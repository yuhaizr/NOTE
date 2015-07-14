<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
use Org\Util\FlashData;
class BaseController extends Controller{
	
	public function _initialize(){
		// 用户权限检查
		if (C ( 'USER_AUTH_ON' ) && !in_array(CONTROLLER_NAME,explode(',',C('NOT_AUTH_MODULE')))) {
	
				//检查认证识别号
				if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
					//跳转到认证网关
					if (C('IS_MAIN_COPY')){
						redirect ( C('MAIN_SITEURL') . C ( 'USER_AUTH_GATEWAY' ) );
					}else {
						redirect(PHP_FILE.C('USER_AUTH_GATEWAY'));
					}
					
				}else {
					if (! Rbac::AccessDecision()){
						// 没有权限 抛出错误
						if (C ( 'RBAC_ERROR_PAGE' )) {
							// 定义权限错误页面
							redirect ( C ( 'RBAC_ERROR_PAGE' ) );
						} else {
							if (C ( 'GUEST_AUTH_ON' )) {
								$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
							}
							// 提示错误信息
							$this->error ( L ( '_VALID_ACCESS_' ) );
						}
					}
				}
		}	
		$session = $_SESSION;
		//生成菜单
		$this->menu();
	}
	
	/**
	 * 当验证成功时的跳转
	 * @param unknown $message
	 * @param string $url
	 */
	protected function crmSuccess($message,$url = ''){
		$this->_flashRediect($message,'success',$url);
		
	}
	
	/**
	 *  当验证失败时的跳转
	 * @param unknown $message
	 * @param string $url
	 */
	protected function  crmError($message,$url =''){
		$this->_flashRediect($message,'error',$url);
		
	}
	
	/**
	 * 把系统消息和消息类型放到list中去
	 * HTTP_REFERER 指的是链接到当前地址的前一个页面的URL
	 * @param string $message
	 * @param string $type
	 * @param string $url
	 */
	private  function _flashRediect($message ='',$type ='success',$url=''){
		FlashData::flash('__SYS_MESSAGE__', $message);
		FlashData::flash('__SYS_MESSAGE_TYPE__', $type);
		if(empty($url)){
			//HTTP_REFERER 指的是链接到当前地址的前一个页面的URL
			$url = $_SERVER['HTTP_REFERER'];
		}	
		//页面的重定向，thinkphp的方法
		redirect($url);
	} 
	
	protected function menu(){
		if(isset($_SESSION[C('USER_AUTH_KEY')])){
			//菜单的数组
			$menu = array();
			
			if (isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]])){
				//如果缓存中存在menu就直接从缓存中获取
 				//$menu = $_SESSION['menu'.$_SESSION['USER_AUTH_KEY']];
			}else {
				//从配置文件中获取菜单列表
				$list = C('MENU');
				if (isset($_SESSION['_ACCESS_LIST'])){
					//当前用户允许访问的列表
					$accessList = $_SESSION['_ACCESS_LIST'];
				}else {
					$accessList = Rbac::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
				}
				
				foreach ($list as $key => $value){
					if (isset($value['childs'])){
						$childs = array();
						foreach ($value['childs'] as $k => $v){
							$link = $v['link'];
							if (preg_match_all("/^\/([\w]+[^\/])\/([\w]+[^\/])\/([\w]+[^\/|\?])/", $link,$matches)){
								$module = $matches[1][0];
								$action = $matches[2][0];
								$function = $matches[3][0];
								if(isset($accessList[strtoupper($module)][strtoupper($action)][strtoupper($function)])
									||isset($_SESSION['administrator'])){
									$v['access'] = 1;
									$childs[$k] = $v;
								}
							}
						}
						if (!empty($childs)){
							$value['access'] = 1;
							$value['childs'] = $childs;
							$menu[$key] = $value;
						}
					}else {
						$link = $value['link'];
						list(,$module,$action) = explode('/', $link);
						if (isset($accessList[strtoupper($module)][strtoupper($action)])
							||isset($_SESSION['administrator'])){
							$value['access'] =1;
							$menu[$key] = $value;
						}
					}
				}
				
			//	$_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]] = $menu;
				
			}
			
			$this->assign('menu',$menu);

		}
	}
}