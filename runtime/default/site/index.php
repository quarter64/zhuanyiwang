<?php 
	$myCartObj  = new Cart();
	$myCartInfo = $myCartObj->getMyCart();
	$siteConfig = new Config("site_config");
	$callback   = IReq::get('callback') ? urlencode(IFilter::act(IReq::get('callback'),'url')) : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $siteConfig->name;?></title>
	<link type="image/x-icon" href="favicon.ico" rel="icon">
	<link rel="stylesheet" href="<?php echo IUrl::creatUrl("")."views/".$this->theme."/skin/".$this->skin."/css/index.css";?>" />
	<script type="text/javascript" charset="UTF-8" src="/zwx/runtime/default/systemjs/jquery/jquery-1.9.0.min.js"></script><script type="text/javascript" charset="UTF-8" src="/zwx/runtime/default/systemjs/jquery/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" charset="UTF-8" src="/zwx/runtime/default/systemjs/form/form.js"></script>
	<script type="text/javascript" charset="UTF-8" src="/zwx/runtime/default/systemjs/autovalidate/validate.js"></script><link rel="stylesheet" type="text/css" href="/zwx/runtime/default/systemjs/autovalidate/style.css" />
	<script type="text/javascript" charset="UTF-8" src="/zwx/runtime/default/systemjs/artdialog/artDialog.js"></script><script type="text/javascript" charset="UTF-8" src="/zwx/runtime/default/systemjs/artdialog/plugins/iframeTools.js"></script><link rel="stylesheet" type="text/css" href="/zwx/runtime/default/systemjs/artdialog/skins/default.css" />
	<script type="text/javascript" charset="UTF-8" src="/zwx/runtime/default/systemjs/artTemplate/artTemplate.js"></script><script type="text/javascript" charset="UTF-8" src="/zwx/runtime/default/systemjs/artTemplate/artTemplate-plugin.js"></script>
	<script type='text/javascript' src="<?php echo IUrl::creatUrl("")."views/".$this->theme."/javascript/common.js";?>"></script>
	<script type='text/javascript' src='<?php echo IUrl::creatUrl("")."views/".$this->theme."/javascript/site.js";?>'></script>
	<?php $sonline = new Sonline();$sonline->show($siteConfig->phone,$siteConfig->service_online);?>
</head>
<body class="index">
<div class="container">
	<div class="header">
		<h1 class="logo"><a title="<?php echo $siteConfig->name;?>" style="background:url(<?php echo IUrl::creatUrl("")."image/yxr.jpg";?>);" href="<?php echo IUrl::creatUrl("");?>"><?php echo $siteConfig->name;?></a></h1>
		<ul class="shortcut">
			<li class="first"><a href="<?php echo IUrl::creatUrl("/ucenter/index");?>">我的账户</a></li><li><a href="<?php echo IUrl::creatUrl("/ucenter/order");?>">我的订单</a></li><li><a href="<?php echo IUrl::creatUrl("/create/goods_list");?>">我的商品</a></li><li class='last'><a href="<?php echo IUrl::creatUrl("/site/help_list");?>">使用帮助</a></li>
		</ul>
		<p class="loginfo">
			<?php if($this->user){?>
			<?php echo $this->user['username'];?>您好，欢迎您来到<?php echo $siteConfig->name;?>购物！[<a href="<?php echo IUrl::creatUrl("/simple/logout");?>" class="reg">安全退出</a>]
			<?php }else{?>
			[<a href="<?php echo IUrl::creatUrl("/simple/login?callback=$callback");?>">登录</a><a class="reg" href="<?php echo IUrl::creatUrl("/simple/reg?callback=$callback");?>">免费注册</a>]
			<?php }?>
		</p>
	</div>
	<div class="navbar">
		<ul>
			<li><a href="<?php echo IUrl::creatUrl("/site/index");?>">首页</a></li>
			<?php $query = new IQuery("guide");$items = $query->find(); foreach($items as $key => $item){?>
			<li><a href="<?php echo IUrl::creatUrl("$item[link]");?>"><?php echo isset($item['name'])?$item['name']:"";?><span> </span></a></li>
			<?php }?>
		</ul>

		<div class="mycart">
			<dl>
				<dt><a href="<?php echo IUrl::creatUrl("/simple/cart");?>">锦囊<b name="mycart_count"><?php echo isset($myCartInfo['count'])?$myCartInfo['count']:"";?></b>件</a></dt>
				<dd><a href="<?php echo IUrl::creatUrl("/simple/cart");?>">去结算</a></dd>
			</dl>

			<!--购物车浮动div 开始-->
			<div class="shopping" id='div_mycart' style='display:none;'>
			</div>
			<!--购物车浮动div 结束-->

			<!--购物车模板 开始-->
			<script type='text/html' id='cartTemplete'>
			<dl class="cartlist">
				<%for(var item in goodsData){%>
				<%var data = goodsData[item]%>
				<dd id="site_cart_dd_<%=item%>">
					<div class="pic f_l"><img width="55" height="55" src="<?php echo IUrl::creatUrl("")."<%=data['img']%>";?>"></div>
					<h3 class="title f_l"><a href="<?php echo IUrl::creatUrl("/site/products/id/<%=data['goods_id']%>");?>"><%=data['name']%></a></h3>
					<div class="price f_r t_r">
						<b class="block">￥<%=data['sell_price']%> x <%=data['count']%></b>
						<input class="del" type="button" value="删除" onclick="removeCart('<?php echo IUrl::creatUrl("/simple/removeCart");?>','<%=data['id']%>','<%=data['type']%>');$('#site_cart_dd_<%=item%>').hide('slow');" />
					</div>
				</dd>
				<%}%>

				<dd class="static"><span>共<b name="mycart_count"><%=goodsCount%></b>件商品</span>金额总计：<b name="mycart_sum">￥<%=goodsSum%></b></dd>

				<%if(goodsData){%>
				<dd class="static">
					<?php if(ISafe::get('user_id')){?>
					<a class="f_l" href="javascript:void(0)" onclick="deposit_ajax('<?php echo IUrl::creatUrl("/simple/deposit_cart_set");?>');">存锦囊>></a>
					<?php }?>
					<label class="btn_orange"><input type="button" value="去锦囊里结算" onclick="window.location.href='<?php echo IUrl::creatUrl("/simple/cart");?>';" /></label>
				</dd>
				<%}%>
			</dl>
			</script>
			<!--购物车模板 结束-->
		</div>
	</div>

	<div class="searchbar">
		<div class="allsort">
			<a href="javascript:void(0);">全部商品分类</a>

			<!--总的商品分类-开始-->
			<ul class="sortlist" id='div_allsort' style='display:none'>
				<?php $query = new IQuery("category");$query->where = "parent_id = 0 and visibility = 1";$query->order = "sort asc";$items = $query->find(); foreach($items as $key => $first){?>
				<li>
					<h2><a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/$first[id]");?>"><?php echo isset($first['name'])?$first['name']:"";?></a></h2>

					<!--商品分类 浮动div 开始-->
					<div class="sublist" style='display:none'>
						<div class="items">
							<strong>选择分类</strong>
							<?php $query = new IQuery("category");$query->where = "parent_id = $first[id] and visibility = 1";$query->order = "sort asc";$items = $query->find(); foreach($items as $key => $second){?>
							<dl class="category selected">
								<dt>
									<a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/$second[id]");?>"><?php echo isset($second['name'])?$second['name']:"";?></a>
								</dt>

								<dd>
									<?php $query = new IQuery("category");$query->where = "parent_id = $second[id] and visibility = 1";$query->order = "sort asc";$items = $query->find(); foreach($items as $key => $third){?>
									<a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/$third[id]");?>"><?php echo isset($third['name'])?$third['name']:"";?></a>|
									<?php }?>
								</dd>
							</dl>
							<?php }?>
						</div>
					</div>
					<!--商品分类 浮动div 结束-->
				</li>
				<?php }?>
			</ul>
			<!--总的商品分类-结束-->
		</div>

		<div class="searchbox">
			<form method='get' action='<?php echo IUrl::creatUrl("/");?>'>
				<input type='hidden' name='controller' value='site' />
				<input type='hidden' name='action' value='search_list' />
				<input class="text" type="text" name='word' autocomplete="off" value="输入关键字..." />
				<input class="btn" type="submit" value="商品搜索" onclick="checkInput('word','输入关键字...');" />
			</form>

			<!--自动完成div 开始-->
			<ul class="auto_list" style='display:none'></ul>
			<!--自动完成div 开始-->

		</div>
		<div class="hotwords">热门搜索：
			<?php $query = new IQuery("keyword");$query->where = "hot = 1";$query->limit = "5";$query->order = "`order` asc";$items = $query->find(); foreach($items as $key => $item){?>
			<?php $tmpWord = urlencode($item['word']);?>
			<a href="<?php echo IUrl::creatUrl("/site/search_list/word/$tmpWord");?>"><?php echo isset($item['word'])?$item['word']:"";?></a>
			<?php }?>
		</div>
	</div>
	<?php echo Ad::show(1);?>

	<?php 
	$site_config=new Config('site_config');
	$seo_data=array();
	$seo_data['title']=$site_config->name;
	$seo_data['title'].=$site_config->index_seo_title;
	$seo_data['keywords']=$site_config->index_seo_keywords;
	$seo_data['description']=$site_config->index_seo_description;
	seo::set($seo_data);
?>

<link rel="stylesheet" type="text/css" href="<?php echo IUrl::creatUrl("")."views/".$this->theme."/javascript/jquery.bxSlider/jquery.bxslider.css";?>" />
<script type="text/javascript" src="<?php echo IUrl::creatUrl("")."views/".$this->theme."/javascript/jquery.bxSlider/jquery.bxSlider.min.js";?>"></script>

<div class="wrapper clearfix">
	<!--<div class="sidebar f_r">

		<!--cms新闻展示-->
		<!--<div class="box m_10">
			<div class="title"><h2>Shop资讯</h2><a class="more" href="<?php echo IUrl::creatUrl("/site/article");?>">更多...</a></div>
			<div class="cont">
				<ul class="list">
					<?php $query = new IQuery("article");$query->where = "visibility = 1 and top = 1";$query->order = "sort ASC,id DESC";$query->fields = "title,id,style,color";$query->limit = "5";$items = $query->find(); foreach($items as $key => $item){?>
					<?php $tmpId=$item['id'];?>
					<li><a href="<?php echo IUrl::creatUrl("/site/article_detail/id/$tmpId");?>"><?php echo Article::showTitle($item['title'],$item['color'],$item['style']);?></a></li>
					<?php }?>
				</ul>
			</div>
		</div>

		<?php echo Ad::show(7);?>
	</div>-->
<?php echo Ad::show(6);?>
	<!--幻灯片 开始-->
	<div class="main f_l">
		<?php if($this->index_slide){?>
		<ul class="bxslider">
			<?php foreach($this->index_slide as $key => $item){?>
			<li title="<?php echo isset($item['name'])?$item['name']:"";?>"><a href="<?php echo IUrl::creatUrl("$item[url]");?>" target="_blank"><img src="<?php echo IUrl::creatUrl("")."$item[img]";?>" width="955px" height="250px" title="<?php echo isset($item['name'])?$item['name']:"";?>" /></a></li>
			<?php }?>
		</ul>
		<?php }?>
	</div>
	<!--幻灯片 结束-->
</div>

<?php echo Ad::show(6);?>

<div class="wrapper clearfix">
	<div class="sidebar f_r">

		<!--团购-->
		<div class="group_on box m_10">
			<div class="title"><h2>团购商品</h2><a class="more" href="<?php echo IUrl::creatUrl("/site/groupon");?>">更多...</a></div>
			<div class="cont">
				<ul class="ranklist">
					<?php $query = new IQuery("regiment");$query->where = "is_close = 0 and NOW() between start_time and end_time";$query->limit = "5";$query->fields = "id,title,regiment_price,img";$query->order = "id desc";$items = $query->find(); foreach($items as $key => $item){?>
					<li class="current">
						<?php $tmpId=$item['id'];?>
						<a href="<?php echo IUrl::creatUrl("/site/groupon/id/$tmpId");?>"><img width="60px" height="60px" alt="<?php echo isset($item['title'])?$item['title']:"";?>" src="<?php echo IUrl::creatUrl("")."$item[img]";?>"></a>
						<a class="p_name" title="<?php echo isset($item['title'])?$item['title']:"";?>" href="<?php echo IUrl::creatUrl("/site/groupon/id/$tmpId");?>"><?php echo isset($item['title'])?$item['title']:"";?></a><p class="light_gray">团购价：<em>￥<?php echo isset($item['regiment_price'])?$item['regiment_price']:"";?></em></p>
					</li>
					<?php }?>
				</ul>
			</div>
		</div>
		<!--团购-->

		<!--限时抢购
		<div class="buying box m_10">
			<div class="title"><h2>限时抢购</h2></div>
			<div class="cont clearfix">
				<ul class="prolist">
					<?php $countNumsItem = array();?>
					<?php $query = new IQuery("promotion as p");$query->join = "left join goods as go on go.id = p.condition";$query->fields = "p.end_time,go.img as img,p.name as name,p.award_value as award_value,go.id as goods_id,p.id as p_id,end_time";$query->where = "p.type = 1 and p.is_close = 0 and go.is_del = 0 and NOW() between start_time and end_time AND go.id is not null";$query->limit = "2";$items = $query->find(); foreach($items as $key => $item){?>
					<?php $free_time = ITime::getDiffSec($item['end_time'])?>
					<?php $countNumsItem[] = $item['p_id'];?>
					<li>
						<p class="countdown">倒计时:<br /><b id='cd_hour_<?php echo isset($item['p_id'])?$item['p_id']:"";?>'><?php echo floor($free_time/3600);?></b>时<b id='cd_minute_<?php echo isset($item['p_id'])?$item['p_id']:"";?>'><?php echo floor(($free_time%3600)/60);?></b>分<b id='cd_second_<?php echo isset($item['p_id'])?$item['p_id']:"";?>'><?php echo $free_time%60;?></b>秒</p>
						<?php $tmpGoodsId=$item['goods_id'];$tmpPId=$item['p_id'];?>
						<a href="<?php echo IUrl::creatUrl("/site/products/id/$tmpGoodsId/promo/time/active_id/$tmpPId");?>"><img src="<?php echo IUrl::creatUrl("")."";?><?php echo Thumb::get($item['img'],175,175);?>" width="175" height="175" alt="<?php echo isset($item['name'])?$item['name']:"";?>" title="<?php echo isset($item['name'])?$item['name']:"";?>" /></a>
						<p class="pro_title"><a href="<?php echo IUrl::creatUrl("/site/products/id/$tmpGoodsId/promo/time/active_id/$tmpPId");?>"><?php echo isset($item['name'])?$item['name']:"";?></a></p>
						<p class="light_gray">抢购价：<b>￥<?php echo isset($item['award_value'])?$item['award_value']:"";?></b></p>
						<div></div>
					</li>
					<?php }?>
				</ul>
			</div>
		</div>
		<!--限时抢购-->

		<!--热卖商品
		<div class="hot box m_10">
			<div class="title"><h2>热卖商品</h2></div>
			<div class="cont clearfix">
				<ul class="prolist">
					<?php $query = new IQuery("commend_goods as co");$query->join = "left join goods as go on co.goods_id = go.id";$query->where = "co.commend_id = 3 and go.is_del = 0 AND go.id is not null";$query->fields = "go.img,go.sell_price,go.name,go.id";$query->limit = "8";$query->order = "sort asc,id desc";$items = $query->find(); foreach($items as $key => $item){?>
					<?php $tmpId=$item['id']?>
					<li>
						<a href="<?php echo IUrl::creatUrl("/site/products/id/$tmpId");?>"><img src="<?php echo IUrl::creatUrl("")."";?><?php echo Thumb::get($item['img'],85,85);?>" width="85" height="85" alt="<?php echo isset($item['name'])?$item['name']:"";?>" /></a>
						<p class="pro_title"><a href="<?php echo IUrl::creatUrl("/site/products/id/$tmpId");?>"><?php echo isset($item['name'])?$item['name']:"";?></a></p>
						<p class="brown"><b>￥<?php echo isset($item['sell_price'])?$item['sell_price']:"";?></b></p>
					</li>
					<?php }?>
				</ul>
			</div>
		</div>
		<!--热卖商品-->

		<!--公告通知
		<div class="box m_10">
			<div class="title"><h2>公告通知</h2><a class="more" href="<?php echo IUrl::creatUrl("/site/notice");?>">更多...</a></div>
			<div class="cont">
				<ul class="list">
					<?php $query = new IQuery("announcement");$query->limit = "5";$query->order = "id desc";$items = $query->find(); foreach($items as $key => $item){?>
					<?php $tmpId=$item['id'];?>
					<li><a href="<?php echo IUrl::creatUrl("/site/notice_detail/id/$tmpId");?>"><?php echo isset($item['title'])?$item['title']:"";?></a></li>
					<?php }?>
				</ul>
			</div>
		</div>
		<!--公告通知-->

		<!--关键词
		<div class="box m_10">
			<div class="title"><h2>关键词</h2><a class="more" href="<?php echo IUrl::creatUrl("/site/tags");?>">更多...</a></div>
			<div class="tag cont t_l">
				<?php $query = new IQuery("keyword");$query->where = "hot = 1";$query->limit = "8";$query->order = "`order` asc";$items = $query->find(); foreach($items as $key => $item){?>
				<?php $searchWord =urlencode($item['word']);?>
				<a href="<?php echo IUrl::creatUrl("/site/search_list/word/$searchWord");?>" class="orange"><?php echo isset($item['word'])?$item['word']:"";?></a>
				<?php }?>
			</div>
		</div>
		<!--关键词-->

		<!--电子订阅
		<div class="book box m_10">
			<div class="title"><h2>电子订阅</h2></div>
			<div class="cont">
				<p>我们会将最新的资讯发到您的Email</p>
				<input type="text" class="gray_m light_gray f_l" name='orderinfo' value="输入您的电子邮箱地址" />
				<label class="btn_orange"><input type="button" onclick="orderinfo();" value="订阅" /></label>
			</div>
		</div>
		<!--电子订阅-->
	</div>

	<div class="main f_l">
		<!--商品分类展示-->
		<div class="category box">
			<div class="title2">
				<h2><img src="<?php echo IUrl::creatUrl("")."views/".$this->theme."/skin/".$this->skin."/images/front/category.gif";?>" alt="商品分类" width="155" height="36" /></h2>
				<a class="more" href="<?php echo IUrl::creatUrl("/site/sitemap");?>">全部商品分类</a>
			</div>
		</div>

		<table id="index_category" class="sort_table m_10" width="100%">
			<col width="100px" />
			<col />
			<?php $query = new IQuery("category");$query->where = "parent_id = 0 and visibility = 1";$query->order = "sort asc";$firsts = $query->find(); foreach($firsts as $key => $first){?>
			<tr>
				<th><a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/$first[id]");?>"><?php echo isset($first['name'])?$first['name']:"";?></a></th>
				<td>
					<?php $query = new IQuery("category");$query->where = "parent_id = $first[id] and visibility = 1";$query->order = "sort asc";$seconds = $query->find(); foreach($seconds as $key => $second){?>
					<a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/$second[id]");?>"><?php echo isset($second['name'])?$second['name']:"";?></a> |
					<?php }?>
				</td>
			</tr>
			<?php }?>
		</table>
		<!--商品分类展示-->

		<!--最新商品-->
		<div class="box yellow m_10">
			<div class="title2">
				<h2><img src="<?php echo IUrl::creatUrl("")."views/".$this->theme."/skin/".$this->skin."/images/front/new_product.gif";?>" alt="最新商品" width="160" height="36" /></h2>
			</div>
			<div class="cont clearfix">
				<ul class="prolist">
					<?php $query = new IQuery("commend_goods as co");$query->join = "left join goods as go on co.goods_id = go.id";$query->where = "co.commend_id = 1 and go.is_del = 0 AND go.id is not null";$query->fields = "go.img,go.sell_price,go.name,go.market_price,go.id";$query->limit = "8";$query->order = "go.sort asc,id desc";$query->group = "id";$items = $query->find(); foreach($items as $key => $item){?>
					<?php $tmpId=$item['id'];?>
					<li style="overflow:hidden">
						<a href="<?php echo IUrl::creatUrl("/site/products/id/$tmpId");?>"><img src="<?php echo IUrl::creatUrl("")."";?><?php echo Thumb::get($item['img'],175,175);?>" width="175" height="175" alt="<?php echo isset($item['name'])?$item['name']:"";?>" /></a>
						<p class="pro_title"><a title="<?php echo isset($item['name'])?$item['name']:"";?>" href="<?php echo IUrl::creatUrl("/site/products/id/$tmpId");?>"><?php echo isset($item['name'])?$item['name']:"";?></a></p>
						<p class="brown">惊喜价：<b>￥<?php echo isset($item['sell_price'])?$item['sell_price']:"";?></b></p>
						<p class="light_gray">市场价：<s>￥<?php echo isset($item['market_price'])?$item['market_price']:"";?></s></p>
					</li>
					<?php }?>
				</ul>
			</div>
		</div>
		<!--最新商品-->

		<!--首页推荐商品-->
		<?php foreach($firsts as $key => $first){?>
		<div class="box m_10" name="showGoods">
			<div class="title title3">
				<h2><a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/$first[id]");?>"><strong><?php echo isset($first['name'])?$first['name']:"";?></strong></a></h2>
				<a class="more" href="<?php echo IUrl::creatUrl("/site/pro_list/cat/$first[id]");?>">更多商品...</a>
				<ul class="category">
					<?php $query = new IQuery("category");$query->where = "parent_id = $first[id] and visibility = 1";$query->order = "sort asc";$seconds = $query->find(); foreach($seconds as $key => $second){?>
					<li><a href="<?php echo IUrl::creatUrl("/site/pro_list/cat/$second[id]");?>"><?php echo isset($second['name'])?$second['name']:"";?></a><span></span></li>
					<?php }?>
				</ul>
			</div>

			<div class="cont clearfix">
				<ul class="prolist">
					<?php $query = new IQuery("category_extend as ca");$query->join = "left join goods as go on go.id = ca.goods_id";$query->where = "ca.category_id = $first[id] and go.is_del = 0";$query->limit = "8";$query->order = "go.sort asc,go.id desc";$query->fields = "go.img,go.id,go.name,go.sell_price,go.market_price";$items = $query->find(); foreach($items as $key => $item){?>
					<li style="overflow:hidden">
						<a href="<?php echo IUrl::creatUrl("/site/products/id/$item[id]");?>"><img src="<?php echo IUrl::creatUrl("")."";?><?php echo Thumb::get($item['img'],175,175);?>" width="175" height="175" alt="<?php echo isset($item['name'])?$item['name']:"";?>" title="<?php echo isset($item['name'])?$item['name']:"";?>" /></a>
						<p class="pro_title"><a title="<?php echo isset($item['name'])?$item['name']:"";?>" href="<?php echo IUrl::creatUrl("/site/products/id/$item[id]");?>"><?php echo isset($item['name'])?$item['name']:"";?></a></p>
						<p class="brown">惊喜价：<b>￥<?php echo isset($item['sell_price'])?$item['sell_price']:"";?></b></p>
						<p class="light_gray">市场价：<s>￥<?php echo isset($item['market_price'])?$item['market_price']:"";?></s></p>
					</li>
					<?php }?>
				</ul>
			</div>
		</div>
		<?php }?>

		<!--品牌列表
		<div class="brand box m_10">
			<div class="title2"><h2><img src="<?php echo IUrl::creatUrl("")."views/".$this->theme."/skin/".$this->skin."/images/front/brand.gif";?>" alt="品牌列表" width="155" height="36" /></h2><a class="more" href="<?php echo IUrl::creatUrl("/site/brand");?>">&lt;<span>全部品牌</span>&gt;</a></div>
			<div class="cont clearfix">
				<ul>
					<?php $query = new IQuery("brand");$query->fields = "id,name,logo";$query->order = "sort asc";$query->limit = "6";$items = $query->find(); foreach($items as $key => $item){?>
					<?php $tmpId=$item['id'];?>
					<li><a href="<?php echo IUrl::creatUrl("/site/brand_zone/id/$tmpId");?>"><img src="<?php echo IUrl::creatUrl("")."$item[logo]";?>"  width="110" height="50"/><?php echo isset($item['name'])?$item['name']:"";?></a></li>
					<?php }?>
				</ul>
			</div>
		</div>
		<!--品牌列表-->

		<!--最新评论
		<div class="comment box m_10">
			<div class="title2"><h2><img src="<?php echo IUrl::creatUrl("")."views/".$this->theme."/skin/".$this->skin."/images/front/comment.gif";?>" alt="最新评论" width="155" height="36" /></h2></div>
			<div class="cont clearfix">
				<?php $query = new IQuery("comment as co");$query->join = "left join goods as go on co.goods_id = go.id";$query->order = "co.id desc";$query->limit = "6";$query->where = "co.status = 1 AND go.is_del = 0 AND go.id is not null";$query->fields = "go.img as img,go.name as name,co.point,co.contents,co.goods_id";$items = $query->find(); foreach($items as $key => $item){?>
				<dl class="no_bg">
					<?php $tmpGoodsId=$item['goods_id'];?>
					<dt><a href="<?php echo IUrl::creatUrl("/site/products/id/$tmpGoodsId");?>"><img src="<?php echo IUrl::creatUrl("")."";?><?php echo Thumb::get($item['img'],66,66);?>" width="66" height="66" /></a></dt>
					<dd><a href="<?php echo IUrl::creatUrl("/site/products/id/$tmpGoodsId");?>"><?php echo isset($item['name'])?$item['name']:"";?></a></dd>
					<dd><span class="grade"><i style="width:<?php echo $item['point']*14;?>px"></i></span></dd>
					<dd class="com_c"><?php echo isset($item['contents'])?$item['contents']:"";?></dd>
				</dl>
				<?php }?>
			</div>
		</div>
		<!--最新评论-->
	</div>
</div>

<script type='text/javascript'>
//dom载入完毕执行
jQuery(function()
{
	//幻灯片开启
	$('.bxslider').bxSlider({'mode':'fade','captions':true,'pager':false,'auto':true});

	//index 分类展示
	$('#index_category tr').hover(
		function(){
			$(this).addClass('current');
		},
		function(){
			$(this).removeClass('current');
		}
	);

	//首页商品变色
	var colorArray = ['green','yellow','purple'];
	$('div[name="showGoods"]').each(function(i)
	{
		$(this).addClass(colorArray[i%colorArray.length]);
	});
});

</script>

	<div class="help m_10">
		<div class="cont clearfix">
			<?php $query = new IQuery("help_category");$query->where = "position_foot = 1";$query->order = "sort ASC,id desc";$query->limit = "5";$items = $query->find(); foreach($items as $key => $helpCat){?>
			<dl>
     			<dt><a href="<?php echo IUrl::creatUrl("/site/help_list/id/$helpCat[id]");?>"><?php echo isset($helpCat['name'])?$helpCat['name']:"";?></a></dt>
     			<?php $query = new IQuery("help");$query->where = "cat_id = $helpCat[id]";$query->order = "sort ASC,id desc";$items = $query->find(); foreach($items as $key => $item){?>
					<dd><a href="<?php echo IUrl::creatUrl("/site/help/id/$item[id]");?>"><?php echo isset($item['name'])?$item['name']:"";?></a></dd>
				<?php }?>
      		</dl>
      		<?php }?>
		</div>
	</div>
	<?php echo IFilter::stripSlash($siteConfig->site_footer_code);?>
</div>

<!--选择货品添加购物车模板 开始-->
<script type='text/html' id='selectProductTemplate'>
<table width="100%">
	<col />
	<col width="80px" />
	<col width="60px" />
	<%for(var item in productData){%>
	<%item = productData[item]%>
	<tr>
		<td align="left">
			<%for(var spectName in item['specData']){%>
			<%var spectValue = item['specData'][spectName]%>
				<%=spectName%>：<%==spectValue%> &nbsp&nbsp
			<%}%>
		</td>
		<td align="center"><span class="bold red2">￥<%=item['sell_price']%></span></td>
		<td align="right"><label class="btn_gray_s"><input type="button" onclick="joinCart_ajax('<%=item['id']%>','product');" value="购买"></label></td>
	</tr>
	<%}%>
	<tr>
		<td colspan='3' align="left"><a href="<?php echo IUrl::creatUrl("/site/products/id/<%=item['goods_id']%>");?>">查看更多</a></td>
	</tr>
</table>
</script>
<!--选择货品添加购物车模板 结束-->

<script type='text/javascript'>
$(function()
{
	<?php $word = IReq::get('word') ? IFilter::act(IReq::get('word'),'text') : '输入关键字...'?>
	$('input:text[name="word"]').val("<?php echo isset($word)?$word:"";?>");

	$('input:text[name="word"]').bind({
		keyup:function(){autoComplete('<?php echo IUrl::creatUrl("/site/autoComplete");?>','<?php echo IUrl::creatUrl("/site/search_list/word/@word@");?>','<?php echo isset($siteConfig->auto_finish)?$siteConfig->auto_finish:"";?>');}
	});

	var mycartLateCall = new lateCall(200,function(){showCart('<?php echo IUrl::creatUrl("/simple/showCart");?>')});

	//购物车div层
	$('.mycart').hover(
		function(){
			mycartLateCall.start();
		},
		function(){
			mycartLateCall.stop();
			$('#div_mycart').hide('slow');
		}
	);
});

//[ajax]加入购物车
function joinCart_ajax(id,type)
{
	$.getJSON("<?php echo IUrl::creatUrl("/simple/joinCart");?>",{"goods_id":id,"type":type,"random":Math.random()},function(content){
		if(content.isError == false)
		{
			var count = parseInt($('[name="mycart_count"]').html()) + 1;
			$('[name="mycart_count"]').html(count);
			$('.msgbox').hide();
			alert(content.message);
		}
		else
		{
			alert(content.message);
		}
	});
}

//列表页加入购物车统一接口
function joinCart_list(id)
{
	$.getJSON('<?php echo IUrl::creatUrl("/simple/getProducts");?>',{"id":id},function(content){
		if(!content)
		{
			joinCart_ajax(id,'goods');
		}
		else
		{
			var selectProductTemplate = template.render('selectProductTemplate',{'productData':content});
			$('#product_box_'+id).html(selectProductTemplate);
			$('#product_box_'+id).parent().show();
		}
	});
}
</script>
</body>
</html>
