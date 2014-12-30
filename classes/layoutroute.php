<?php
/**
 * @copyright (c) 2014 aircheng
 * @file themeroute.php
 * @brief 视图布局选择路由类
 * @author nswe
 * @date 2014/7/15 18:50:48
 * @version 2.6
 */
class layoutroute extends IInterceptorBase
{
	/**
	 * @brief layout布局文件进行选择,从主题中的config.php中获取layout配置
	 */
	public static function onCreateView()
	{
		//从主题中的config.php获取layout配置
		$themeConfig = is_file(IWeb::$app->controller->getViewPath().'config.php') ? include(IWeb::$app->controller->getViewPath().'config.php') : null;
		if(isset($themeConfig['layout'][IWeb::$app->controller->getId()]))
		{
			IWeb::$app->controller->layout = $themeConfig['layout'][IWeb::$app->controller->getId()];
		}
	}
}