<?php
/**
 * @brief 用户商品列表
 * @class CREATE
 * @note  前台
 */
class Create extends IController
{
	public $layout = 'goodscenter';

	/***登录才能上***/
	public function init()
	{
		CheckRights::checkUserRights();

		if(ISafe::get('user_id') == '')
		{
			$this->redirect('/simple/login');
		}
	}

	public function goods_img_upload()
	{
		//获得配置文件中的数据
		$config = new Config("site_config");

	 	//调用文件上传类
		$photoObj = new PhotoUpload();
		$photo    = current($photoObj->run());

		//判断上传是否成功，如果float=1则成功
		if($photo['flag'] == 1)
		{
			$result = array(
				'flag'=> 1,
				'img' => $photo['img']
			);
		}
		else
		{
			$result = array('flag'=> $photo['flag']);
		}
		echo JSON::encode($result);
	}




	function goods_update()
	{
		$id       = IFilter::act(IReq::get('id'),'int');
		$callback = IFilter::act(IReq::get('callback'),'url');
		$callback = strpos($callback,'create/goods_list') === false ? '' : $callback;
		$postData = array();
		$nowDataTime = date('Y-m-d H:i:s');

		//检查表单提交状态
		if(!$_POST)
		{
			die('请确认表单提交正确');
		}

		//初始化商品数据
		unset($_POST['id']);
		unset($_POST['callback']);
		foreach($_POST as $key => $val)
		{
			$postData[$key] = $val;

			//数据过滤分组
			if(strpos($key,'attr_id_') !== false)
			{
				$goodsAttrData[ltrim($key,'attr_id_')] = IFilter::act($val);
			}
			else if($key[0] != '_')
			{
				$goodsUpdateData[$key] = IFilter::act($val,'text');
			}
		}

		//商品上架
		$goodsUpdateData['is_del']=0;
		if(isset($goodsUpdateData['is_del']))
		{
			$goodsUpdateData['is_del'] == 2 ? ($goodsUpdateData['down_time'] = $nowDataTime) : ($goodsUpdateData['up_time'] = $nowDataTime);
		}

		//是否存在货品
		$goodsUpdateData['spec_array'] = '';

		if(isset($postData['_spec_array']))
		{
			//生成goods中的spec_array字段数据
			$goods_spec_array = array();
			foreach($postData['_spec_array'] as $key => $val)
			{
				foreach($val as $v)
				{
					$tempSpec = JSON::decode($v);
					if(!isset($goods_spec_array[$tempSpec['id']]))
					{
						$goods_spec_array[$tempSpec['id']] = array('id' => $tempSpec['id'],'name' => $tempSpec['name'],'type' => $tempSpec['type'],'value' => array());
					}
					$goods_spec_array[$tempSpec['id']]['value'][] = $tempSpec['value'];
				}
			}
			foreach($goods_spec_array as $key => $val)
			{
				$val['value'] = array_unique($val['value']);
				$goods_spec_array[$key]['value'] = join(',',$val['value']);
			}
			$goodsUpdateData['spec_array'] = JSON::encode($goods_spec_array);
		}

		//取出邮箱，为避免改用户名麻烦
		$userObj = new IModel('user');
    	$where = 'id = '.$this->user['user_id'];
    	$email = $userObj->getObj($where);	

    	//存入数据库
    	$goodsUpdateData['email']=$email['email'];

		$goodsUpdateData['goods_no']     = preg_replace("/(?:\-\d*)$/","",current($postData['_goods_no']));
		$goodsUpdateData['store_nums']   = array_sum($postData['_store_nums']);
		$goodsUpdateData['market_price'] = isset($postData['_market_price']) ? current($postData['_market_price']) : 0;
		$goodsUpdateData['sell_price']   = isset($postData['_sell_price'])   ? current($postData['_sell_price'])   : 0;
		$goodsUpdateData['cost_price']   = isset($postData['_cost_price'])   ? current($postData['_cost_price'])   : 0;
		$goodsUpdateData['weight']       = isset($postData['_weight'])       ? current($postData['_weight'])       : 0;

		//服务器端判断
		if(!isset($postData['name']))
		{
			die("请填写产品名称!");
		}
		if(!isset($postData['contact']))
		{
			die("请填写联系方式!");
		}
		if(!isset($postData['_goods_category']))
		{
			die("请选择分类!");
		}
		if(!isset($postData['_goods_no']))
		{
			die("请填写货号!");
		}
		if(!isset($postData['_store_nums']))
		{
			die("请填写产品数量!");
		}
		if(!isset($postData['_sell_price']))
		{
			die("请填写产品价格!");
		}
		if(($goodsUpdateData['img'])=='')
		{
			die("图片上传失败!");
		}
		if(!isset($postData['surname']))
		{
			die("请填写您的姓!");
		}
		if(!isset($postData['appellation']))
		{
			die("请填写您的称呼!");
		}

		/*所在地处理*/
		$circlet="";
		if($postData['circlet']=="")
		{
			$circlet = $postData['area'];
		}
		else
		{
			$circlet = $postData['circlet'];
		}


		$goodsUpdateData['province'] = $postData['province'];;
		$goodsUpdateData['city']     = $postData['city'];
		$goodsUpdateData['area']     = $postData['area'];
		$goodsUpdateData['circlet']  = $circlet;

		/*联系方式处理*/
		$goodsUpdateData['contact']  = $postData['contact'];

		/*称呼处理*/
		$goodsUpdateData['surname'] = $postData['surname'];
		$goodsUpdateData['appellation'] = $postData['appellation'];


		//处理商品
		$goodsDB = new IModel('goods');
		if($id)
		{
			$goodsDB->setData($goodsUpdateData);
			$goodsDB->update('id = '.$id);
		}
		else
		{
			$goodsUpdateData['create_time'] = $nowDataTime;
			$goodsDB->setData($goodsUpdateData);
			$id = $goodsDB->add();
		}

		
		//处理商品属性和规格
		$goodsAttrDB = new IModel('goods_attribute');
		$goodsAttrDB->del('goods_id = '.$id);
		if(isset($goodsAttrData) && $goodsAttrData)
		{
			foreach($goodsAttrData as $key => $val)
			{
				$attrData = array(
					'goods_id' => $id,
					'model_id' => $goodsUpdateData['model_id'],
					'attribute_id' => $key,
					'attribute_value' => is_array($val) ? join(',',$val) : $val
				);
				$goodsAttrDB->setData($attrData);
				$goodsAttrDB->add();
			}
		}


		if(isset($goods_spec_array) && $goods_spec_array)
		{
			foreach($goods_spec_array as $key => $val)
			{
				$temp = explode(',',$val['value']);
				foreach($temp as $v)
				{
					$attrData = array(
						'goods_id' => $id,
						'model_id' => $goodsUpdateData['model_id'],
						'spec_id' => $val['id'],
						'spec_value' => $v
					);
					$goodsAttrDB->setData($attrData);
					$goodsAttrDB->add();
				}
			}
		}

		//是否存在货品
		$productsDB = new IModel('products');
		$productsDB->del('goods_id = '.$id);
		if(isset($postData['_spec_array']))
		{
			$productIdArray = array();

			//创建货品信息
			foreach($postData['_goods_no'] as $key => $rs)
			{
				$productsData = array(
					'goods_id' => $id,
					'products_no' => $postData['_goods_no'][$key],
					'store_nums' => $postData['_store_nums'][$key],
					'market_price' => $postData['_market_price'][$key],
					'sell_price' => $postData['_sell_price'][$key],
					'cost_price' => $postData['_cost_price'][$key],
					'weight' => $postData['_weight'][$key],
					'spec_array' => "[".join(',',$postData['_spec_array'][$key])."]"
				);
				$productsDB->setData($productsData);
				$productIdArray[$key] = $productsDB->add();
			}
		}


		//处理商品分类
		$categoryDB = new IModel('category_extend');
		$categoryDB->del('goods_id = '.$id);
		if(isset($postData['_goods_category']) && $postData['_goods_category'])
		{
			foreach($postData['_goods_category'] as $item)
			{
				$categoryDB->setData(array('goods_id' => $id,'category_id' => $item));
				$categoryDB->add();
			}
		}


		//处理商品促销

		$commendDB = new IModel('commend_goods');
		$commendDB->del('goods_id = '.$id);
		if(isset($postData['_goods_commend']) && $postData['_goods_commend'])
		{
			foreach($postData['_goods_commend'] as $item)
			{
				$commendDB->setData(array('goods_id' => $id,'commend_id' => $item));
				$commendDB->add();
			}
		}

		//处理商品关键词
		//keywords::add($goodsUpdateData['search_words']);

		//处理商品图片
		$photoRelationDB = new IModel('goods_photo_relation');
		$photoRelationDB->del('goods_id = '.$id);
		if(isset($postData['_imgList']) && $postData['_imgList'])
		{
			$postData['_imgList'] = str_replace(',','","',trim($postData['_imgList'],','));
			$photoDB = new IModel('goods_photo');
			$photoData = $photoDB->query('img in ("'.$postData['_imgList'].'")','id');
			if($photoData)
			{
				foreach($photoData as $item)
				{
					$photoRelationDB->setData(array('goods_id' => $id,'photo_id' => $item['id']));
					$photoRelationDB->add();
				}
			}
		}

		//处理会员组的价格
		$groupPriceDB = new IModel('group_price');
		$groupPriceDB->del('goods_id = '.$id);
		if(isset($productIdArray) && $productIdArray)
		{
			foreach($productIdArray as $index => $value)
			{
				if(isset($postData['_groupPrice'][$index]) && $postData['_groupPrice'][$index])
				{
					$temp = JSON::decode($postData['_groupPrice'][$index]);
					foreach($temp as $k => $v)
					{
						$groupPriceDB->setData(array(
							'goods_id' => $id,
							'product_id' => $value,
							'group_id' => $k,
							'price' => $v
						));
						$groupPriceDB->add();
					}
				}
			}
		}
		else
		{
			if(isset($postData['_groupPrice'][0]) && $postData['_groupPrice'][0])
			{
				$temp = JSON::decode($postData['_groupPrice'][0]);
				foreach($temp as $k => $v)
				{
					$groupPriceDB->setData(array(
						'goods_id' => $id,
						'group_id' => $k,
						'price' => $v
					));
					$groupPriceDB->add();
				}
			}
		}
		$callback ? $this->redirect($callback) : $this->redirect("goods_list");
	}


	function goods_edit()
	{
		$goods_id = IFilter::act(IReq::get('id'),'int');

		//初始化数据
		$goods_class = new goods_class();
		$tb_category = new IModel('category');
		$this->category = $goods_class->sortdata($tb_category->query(false,'*','sort','asc'),0,'--');
		$data = array();

		if($goods_id)
		{
			//获取商品
			$obj_goods = new IModel('goods');
			$goods_info = $obj_goods->getObj('id='.$goods_id);

			//读取到记录
			if($goods_info)
			{
				$data = $goods_class->edit($goods_info);

				//获取货品
				$productObj = new IModel('products');
				$product_info = $productObj->query('goods_id = '.$goods_id);
				if($product_info)
				{
					//获取货品会员价格
					$groupPriceDB = new IModel('group_price');
					foreach($product_info as $k => $rs)
					{
						$temp = array();
						$productPrice = $groupPriceDB->query('product_id = '.$rs['id']);
						foreach($productPrice as $key => $val)
						{
							$temp[$val['group_id']] = $val['price'];
						}
						$product_info[$k]['groupPrice'] = $temp ? JSON::encode($temp) : '';
					}
					$data['product'] = $product_info;
				}
			}
			else
			{
				$this->goods_list();
				Util::showMessage("没有找到相关商品！");
				exit;
			}
		}
		$this->setRenderData($data);
//die(print_r($data['form']['appellation']));
		$this->redirect('goods_edit');
	}


	function goods_list()
	{
    	$userObj       = new IModel('user');
    	$where         = 'id = '.$this->user['user_id'];
    	$this->userRow = $userObj->getObj($where);

		//搜索条件
		$search = IFilter::act(IReq::get('search'));
		$page   = IReq::get('page') ? IFilter::act(IReq::get('page'),'int') : 1;

		$join  = "left join brand as b on go.brand_id = b.id";

		//只要当前用户名的
		$where = " 1 "."and email='".$this->userRow['email']."'";

		//条件筛选处理
		if(isset($search['name']) && isset($search['keywords']) && $search['keywords'])
		{
			switch($search['name'])
			{
				case "goodsName":
				{
					$where .= " and go.name like '%".$search['keywords']."%' ";
				}
				break;

				case "goodsNo":
				{
					$where .= " and go.goods_no like '%".$search['keywords']."%' ";
				}
				break;
			}
		}

		if(isset($search['category_id']) && $search['category_id'])
		{
			$join  .= " left join category_extend as ce on ce.goods_id = go.id ";
			$where .= " and ce.category_id = ".$search['category_id'];
		}

		if(isset($search['is_del']) && $search['is_del'] !== '')
		{
			$where .= " and go.is_del = ".$search['is_del'];
		}
		else
		{
			$where .= " and go.is_del != 1";
		}

		if(isset($search['store_nums']) && $search['store_nums'])
		{
			switch($search['store_nums'])
			{
				case 1:
				{
					$where .= " and go.store_nums < 1 ";
				}
				break;

				case 10:
				{
					$where .= " and go.store_nums >= 1 and go.store_nums < 10 ";
				}
				break;

				case 100:
				{
					$where .= " and go.store_nums <= 100 and go.store_nums >= 10 ";
				}
				break;

				case 101:
				{
					$where .= " and go.store_nums >= 100 ";
				}
				break;
			}
		}

		if(isset($search['commend_id']) && $search['commend_id'])
		{
			$join  .= " left join commend_goods as cg on go.id = cg.goods_id ";
			$where .= " and cg.commend_id = ".$search['commend_id'];
		}

		//拼接sql
		$goodsHandle = new IQuery('goods as go');
		$goodsHandle->order    = "go.sort asc,go.id desc";
		$goodsHandle->distinct = "go.id";
		$goodsHandle->fields   = "go.*,b.name as brand_name";
		$goodsHandle->page     = $page;
		$goodsHandle->where    = $where;
		$goodsHandle->join     = $join;

		$this->search      = $search;
		$this->goodsHandle = $goodsHandle;

		$this->redirect("goods_list");
	}

	function goods_del()
	{
		//post数据
	    $id = IFilter::act(IReq::get('id'));
	    //生成goods对象
	    $tb_goods = new IModel('goods');
	    $tb_goods->setData(array('is_del'=>1));
	    if(!empty($id))
		{
			$tb_goods->update(Order_Class::getWhere($id));
		}
		else
		{
			Util::showMessage('请选择要删除的数据');
		}
		$this->redirect("goods_list");
	}




}