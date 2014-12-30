<?php
/**
 * @copyright (c) 2014 aircheng
 * @file themeroute.php
 * @brief 主题皮肤选择路由类
 * @author nswe
 * @date 2014/7/15 18:50:48
 * @version 2.6
 *
 * config.php 中的theme和skin多种写法，只用theme举例说明
 * 1, 'theme' => 'default' #所有客户端平台都用default主题
 * 2, 'theme' => array('pc' => 'default','mobile' => 'mobile') #pc端用default主题；mobile端用mobile主题
 */
class themeroute extends IInterceptorBase
{
	//后台管理的控制器
	private static $syscontroller = array(
		'block','brand','comment','goods','market','member','message','order','system','systemadmin','tools'
	);

	//前台页面的控制器
	private static $controller = array(
		'site','simple','ucenter'
	);

	/**
	 * @brief theme和skin进行选择
	 */
	public static function onCreateController()
	{
		//判断是否为后台管理控制器
		if(in_array(IWeb::$app->controller->getId(),self::$syscontroller))
		{
			IWeb::$app->controller->theme = 'sysdefault';
			IWeb::$app->controller->skin  = 'default';
		}
		else
		{
			/**
			 * 对于theme和skin的判断流程
			 * 1,直接从URL中获取是否已经设定了方案__theme,__skin
			 * 2,获取cookie中的方案名称
			 * 3,读取config配置中的默认方案
			 */
			$urlTheme = IReq::get('__theme');
			$urlSkin  = IReq::get('__skin');

			if($urlTheme && $urlSkin && preg_match('|^\w+$|',$urlTheme) && preg_match('|^\w+$|',$urlSkin))
			{
				ISafe::set('__theme',IWeb::$app->controller->theme = $urlTheme);
				ISafe::set('__skin',IWeb::$app->controller->skin  = $urlSkin);
			}
			elseif(ISafe::get('__theme') && ISafe::get('__skin'))
			{
				IWeb::$app->controller->theme = ISafe::get('__theme');
				IWeb::$app->controller->skin  = ISafe::get('__skin');
			}
			else
			{
				if(isset(IWeb::$app->config['theme']))
				{
					//根据不同的客户端进行智能选择
					if(is_array(IWeb::$app->config['theme']))
					{
						$client = IClient::getDevice();
						IWeb::$app->controller->theme = isset(IWeb::$app->config['theme'][$client]) ? IWeb::$app->config['theme'][$client] : current(IWeb::$app->config['theme']);
					}
					else
					{
						IWeb::$app->controller->theme = IWeb::$app->config['theme'];
					}
				}

				if(isset(IWeb::$app->config['skin']))
				{
					//根据不同的客户端进行智能选择
					if(is_array(IWeb::$app->config['skin']))
					{
						$client = IClient::getDevice();
						IWeb::$app->controller->skin = isset(IWeb::$app->config['skin'][$client]) ? IWeb::$app->config['skin'][$client] : current(IWeb::$app->config['skin']);
					}
					else
					{
						IWeb::$app->controller->skin = IWeb::$app->config['skin'];
					}
				}
			}
		}

		//修正runtime配置
		IWeb::$app->runtimePath = IWeb::$app->getRuntimePath().IWeb::$app->controller->theme.'/';
		IWeb::$app->webRunPath  = IWeb::$app->getWebRunPath().IWeb::$app->controller->theme.'/';
	}
}