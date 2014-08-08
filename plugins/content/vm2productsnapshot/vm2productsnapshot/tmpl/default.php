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
/*
 Customization - the following items are available:-
 
 $list->product_id
 $list->category_id
 $list->product_name
 $list->product_link - url of Virtuemart product page
 $list->product_link_button - html for product link  button
 $list->product_thumb_image - html for product thumb image
 $list->product_s_desc - product short description
 $list->price
 $list->addtocart_link  - url to add product to cart
 $list->addtocart_button - html for add to cart button
 $list->stock
 $list->sku 
*/

?>
<div class="vmproductSnapshot">
<?php if(! empty($list->product_name)): ?>
<h3 class="productSnapshotTitle"><?php echo $list->product_name; ?></h3>
<?php endif; ?>
<?php echo $list->product_thumb_image; ?>
<?php echo $list->caption; ?>
<?php echo $list->product_s_desc; ?><br style="clear:both;"/><br />
<?php echo $list->product_link_button; ?><br /><br />
<span class="snapshotPrice"><?php echo $list->price; ?></span><br />
<?php echo $list->addtocart_button; ?>
<?php echo $list->sku; ?> 
<?php echo $list->product_desc; ?>
<br /><?php echo $list->stock; ?>
<br />

</div>





