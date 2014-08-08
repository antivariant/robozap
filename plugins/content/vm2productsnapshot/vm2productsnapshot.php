<?php
/**
*
* @copyright	Inspiration Web Design
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://www.spiralscripts.co.uk
* Technical Support: http://www.spiralscripts.co.uk/Front-Page/support.html
*/

// No direct access.
defined('_JEXEC') or die;




class plgContentVm2ProductSnapshot extends JPlugin
{
	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$lang = JFactory::getLanguage();
        $lang->load('plg_vm2productsnapshot', JPATH_ADMINISTRATOR);
		
	}
	
	public function init()
	{

		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		// Load the language file of com_virtuemart.
		JFactory::getLanguage()->load('com_virtuemart');
		if (!class_exists( 'calculationHelper' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'calculationh.php');
		if (!class_exists( 'CurrencyDisplay' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'currencydisplay.php');
		if (!class_exists( 'VirtueMartModelVendor' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'models'.DS.'vendor.php');
		if (!class_exists( 'VmImage' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'image.php');
		if (!class_exists( 'shopFunctionsF' )) require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctionsf.php');
		if (!class_exists( 'calculationHelper' )) require(JPATH_COMPONENT_SITE.DS.'helpers'.DS.'cart.php');
		if (!class_exists( 'VirtueMartModelProduct' )){
		   JLoader::import( 'product', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
		}		
	}
	
	
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Simple performance check to determine whether bot should process further.
	/*	if (JString::strpos($article->text, '{product') === false) {
			return true;
		}*/
		
		$renderPlugin = false;
		
		$pluginParams =& $this->params;//the plugin parameters - note that the $params passed to this method are article parameters
		
	    $params_init = array('id' => '',
							 'sku' => '',							 
		                     'showAddToCart' =>1, 
							 'showPrice' => 1, 
							 'showCaption' => 0, 
							 'showSDescription' => 1, 
							 'showDescription' => 0, 
							 'showImage' => 1, 
							 'showName' => 1,
							 'showStock' => 1,							 
							 'showSKU' => 1,							 
							 'showProductLink' => 1,
							 'captionLength' => 0
							 );		
		
		$permittedKeys = array_keys($params_init);
		
		/*$permittedKeys = array('id',
		                     'showAddToCart', 
							 'showPrice', 
							 'showCaption',
							 'showSDescription',
							 'showDescription', 
							 'showImage', 
							 'showName',
							 'showProductLink',
							 'captionLength');*/
		
		
		$param_defaults = array();
	    foreach( $params_init as $key => $value ) {
		   $param_defaults[$key] = $pluginParams->get( $key, $value ) ;
		   //set the default parameters to the values defined in plugin manager
	    }
		
		$user_params = array();
		
		$regex	= '/{\s?product\s+(.*?)}/i';
		
		if(preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER))
		{	
		    $this->init(); //load virtuemart classes
			$i=0;
		
			foreach ($matches as $match) {
				
				if(isset($match[1])){
					$user_params[$i] = $this->get_params($match[1], $param_defaults, $permittedKeys );
				}
				else
				{
					$user_params[$i] = $param_defaults;
				}
				
				if(! empty($user_params[$i]['id']) || ! empty($user_params[$i]['sku']))
				{
					$productSnapshot = $this->getProductSnapshot($pluginParams, $user_params[$i]);
					//$output = $this->getOutput($productSnapshot, $params, $user_params, 'default.php');
					$output = $this->getOutput($productSnapshot, $pluginParams, $user_params, 'default.php');
					
				    $findtext = $match[0];
		            $article->text = JString::str_ireplace($findtext, $output, $article->text);
					$renderPlugin = true;
				}
				
				
				$i++;
				
			}
			
		}
		
		$loadStylesheet = (bool)$pluginParams->get('loadStylesheet',1);
		
		$baseurl  = '';
		
		if($renderPlugin && $loadStylesheet)
		{
		  $doc = JFactory::getDocument();
		//only load stylesheet if plugin is being rendered
		  if(file_exists(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'vm2productsnapshot'.DS.'vm2productsnapshot'.DS.'css'.DS.'vm2productsnapshot.css'))
		  {
			 //$doc->addStyleSheet($baseurl. 'plugins/content/vm2productsnapshot/vm2productsnapshot/css/vm2productsnapshot.css');
			 JHTML::_('stylesheet','plugins/content/vm2productsnapshot/vm2productsnapshot/css/vm2productsnapshot.css');
		  }
		}
		
		if($renderPlugin)
		{
		  JHTML::_('behavior.modal'); 	
		}


		return true;
	}
	
	
	 /**
	 *  compare and return parameters.
	 * @author Fiona Coulter
	 * @param string $match
	 * @param array $param_defaults
	 * @return array
	 */
	function get_params( &$match, $param_defaults, $permittedKeys ) {
		$match = str_ireplace($permittedKeys,$permittedKeys, $match); //make case insensitive
	    $params_init = $param_defaults;
		$params = explode( ";", $match ) ;
		foreach( $params as $param ) {
			$param = explode( "=", $param ) ;
			if( (isset( $params_init[$param[0]] ) && in_array($param[0],$permittedKeys)) ) {
				$params_init[$param[0]] = str_replace('&','',$param[1]) ;
			}
		}
		return $params_init ;
	}
	
	
	function getProductSnapshot(&$params, $userparams)
	{

		$product_id = (int)$userparams['id'];
		if($product_id == 0){ 
		   $sku = $userparams['sku'];
		   if(empty($sku)){return null;}
		   $db = JFactory::getDBO();
		   $sku = "'".$db->getEscaped($sku)."'";
		   $query = 'SELECT virtuemart_product_id FROM #__virtuemart_products WHERE product_sku='.$sku;
			$db->setQuery($query);
			if(!$product_id = $db->loadResult())
			{
			  return null;  
			}
		}

		
	    $mime_array = array('image/jpeg','image/png','image/gif');

		
        $showPrice = (bool)$userparams['showPrice']; // Display the Product Price?
		$showAddToCart = (bool)$userparams['showAddToCart'];
		$showName = (bool)$userparams['showName'];
		$showCaption = (bool)$userparams['showCaption'];
		$showSDescription = (bool)$userparams['showSDescription'];		
		$showDescription = (bool)$userparams['showDescription'];
		$showImage = (bool)$userparams['showImage'];
		$showProductLink = (bool)$userparams['showProductLink'];
		$showSKU = (bool)$userparams['showSKU'];			
		$showStock = (bool)$userparams['showStock'];		
		
		
		$productModel = VmModel::getModel('Product');
		$product = $productModel->getProduct($product_id);
		if($showImage)
		{
		   $productModel->addImages($product);
		}


		$list->product_id = $product_id;
		$list->category_id = $product->virtuemart_category_id;
		if($showName)
		{
		  $list->product_name = $product->product_name;
		}
		else
		{
		  $list->product_name = '';	
		}
		
		$list->product_link = $product->link;
		$list->product_link_button = '';
		if($showProductLink)
		{
			$list->product_link_button = '<a class="snapshotProductLink" href="'.$list->product_link.'">'.$params->get('linktext','product details').'</a>';
		}
		$list->addtocart = $params->get('addtocart','Add to Cart');
		
		
		$list->product_thumb_image = '';
		
		if(count($product->images) > 0)
		{
			$list->product_thumb_image = $product->images[0]->displayMediaThumb('class="snapshotImage" title="'.$product->product_name.'" ',true,'class="modal"');
			
		}
		
		$textcount = (int)$userparams['captionLength'];
		if($textcount == 0 || ! $showCaption)
		{
			$list->caption = '';
		}
		else
		{
		    $itemtext = strip_tags($product->product_s_desc);
			
			if ($textcount >= strlen($itemtext))
			{
			  $list->caption = $itemtext;
			}
			else
			{
			   $pos = strpos( $itemtext, ' ', $textcount );
			   if ( $pos === false)
			   {
				  $list->caption = $itemtext;			   
			   }
			   else
			   {
				  $list->caption = substr( $itemtext, 0, $pos );
			   }
			}
			
			
		}
		$list->product_s_desc = '';
		if($showSDescription)
		{
			$list->product_s_desc = $product->product_s_desc;
		}
		
		$list->product_desc = '';
		if($showDescription)
		{
			$list->product_desc = $product->product_desc;
		}
		
			if($showStock)
			{
				$list->stock = JTEXT::_('VM2PRODUCTSNAPSHOT_IN_STOCK').$product->product_in_stock;	
			}
			else
			{
				$list->stock = "";	
			}
			
			if($showSKU)
			{
				$list->sku = $product->product_sku;
			}
			else
			{
				$list->sku = "";
			}
		
		
		
		$list->price = '';
		$list->addtocart_link = '';
		$list->addtocart_button = '';
		$stockhandle = VmConfig::get ('stockhandle', 'none');
		$instock = true;
		if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($product->product_in_stock - $product->product_ordered) < 1) {
			$instock = false;
		}
		


		
		if (!VmConfig::get('use_as_catalog',0) && $showPrice) {
	        $currency = CurrencyDisplay::getInstance( );
           if (!empty($product->prices['salesPriceWithDiscount']) ) $list->price .= $currency->createPriceDiv('salesPriceWithDiscount','',$product->prices,true);			
           else if (!empty($product->prices['salesPrice'] ) ) {$list->price .= $currency->createPriceDiv('salesPrice','',$product->prices,true);}
		}
		if (!VmConfig::get('use_as_catalog',0) && $showAddToCart && $instock 
				&& !empty( $product->prices['salesPrice']) // Product must have a price to add it to cart
				) 
		{
			$url = "?option=com_virtuemart&view=cart&task=add&quantity[]=1&virtuemart_product_id[]=" .  $product_id.'&virtuemart_category_id[]='.$product->virtuemart_category_id;
			$addtocart_link = JRoute::_("index.php" . $url);
			$list->addtocart_link = $addtocart_link;
			$list->addtocart_button = '<div class="addtocart-button"><a class="addtocart-button" href="'.$list->addtocart_link.'">'.$list->addtocart.'</a></div>';
		}
		return $list;
				
			
		
	}
	
	function getOutput( &$list, &$params, &$user_params, $layout='default.php'){
						// Fetch the template
            if(!isset($list)){return '';}						
			ob_start();
			$tmplPath = $this->getTemplatePath('vm2productsnapshot',$layout);
			$tmplPath = $tmplPath->file;
			include($tmplPath);
			$output = ob_get_contents();
			ob_end_clean();
			
			return $output;
		
	}
	
	
	function getTemplatePath($pluginName,$file){
		$mainframe= &JFactory::getApplication();
		$p = new JObject;
		if(file_exists(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.str_replace('/',DS,$file))){
			$p->file = JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.$file;
			$p->http = JURI::base()."templates/".$mainframe->getTemplate()."/html/{$pluginName}/{$file}";
		} else if(file_exists(JPATH_SITE.DS.'plugins'.DS.'content'.DS.$pluginName.DS.'tmpl'.DS.$file)){
			$p->file = JPATH_SITE.DS.'plugins'.DS.'content'.DS.$pluginName.DS.'tmpl'.DS.$file;
			$p->http = JURI::base()."plugins/content/{$pluginName}/tmpl/{$file}";
		}
		else
		{
			$p->file = JPATH_SITE.DS.'plugins'.DS.'content'.DS.$pluginName.DS.$pluginName.DS.'tmpl'.DS.$file;
			$p->http = JURI::base()."plugins/content/{$pluginName}/{$pluginName}/tmpl/{$file}";
		}
		return $p;
	}
	
	
	
	
}