<?php
/**
* use log adapter file PhalconPHP
*/
use Phalcon\Logger\Adapter\File as FileAdapter;

/**
 * Shopping Cart Class
 *
 * @package     PhalconPHP
 * @subpackage  Libraries
 * @category    Shopping Cart
 * @author      Iparra
 * @link        http://uno-de-piera.com/
 */
class ShoppingCart extends Phalcon\Mvc\User\Component
{
    /**
    * @desc - content cart
    */
    protected $_cart = array();

    /**
    * @desc - identificator for shopping cart
    */
    protected $_identificator;

    /**
    * @desc - log phalcon
    */
    protected $_logger;

    /**
    * @desc - constructor class, set params by default
    * @access public
    * @author Iparra
    * @param $cartIdentificator - cart name
    */
    public function __construct($cartIdentificator = "shop")
    {     
        //set identifier cart
        $this->_identificator = $cartIdentificator;

        //set cart session
        if($this->session->has($this->_identificator) !== FALSE)
        {
            $this->_cart = $this->session->get($this->_identificator);
        }
    }

    /**
    * @desc - create a new instance for log class
    * @access private
    * @author Iparra
    */
    private function _instanceLog()
    {
        $this->_createLogDir();
        $this->_logger = new FileAdapter("../app/logs/shoppingCart.log");
    }

    /**
    * @desc - set session cart and save content cart into session
    * @access private
    * @author Iparra
    */
    private function _save()
    {
        $this->session->set($this->_identificator, $this->_cart);
    }

    /**
    * @desc - add product to cart
    * @access public
    * @author Iparra
    * @param $product - array with product data to add
    */
    public function add($product = array(), $isUpdate = FALSE)
    {
        //check if is array and not empty, otherwise write log message and return false
        if(!is_array($product) || empty($product))
        {
            $this->_logNotArray(__LINE__);
            return false;
        }
 
        //check if isset keys id qty and price, otherwise return false
        if(!$product["id"] || !$product["qty"] || !$product["price"])
        {
            $this->_instanceLog();
            $this->_logger->error(
                "Error, the product need next keys: id, qty and price, line ".__LINE__." class ".__CLASS__."!"
            );   
            return false;
        }
 
        //check if is numeric values id qty and price, otherwise return false
        if(!is_numeric($product["id"]) || !is_numeric($product["qty"]) || !is_numeric($product["price"]))
        {
            $this->_instanceLog();
            $this->_logger->error(
                "Error, the product should have numerics id, qty y price, line ".__LINE__." class ".__CLASS__."!"
            );   
            return false;  
        }

        //we must create a rowId identifier for each product
        if(isset($product['options']))
        {
            $rowId = md5($product['id'].serialize($product['options'])); 
        }
        else
        {
            $rowId = md5($product["id"]);
        }
    
        //create the rowId id for the product
        $product["rowId"] = $rowId;
        
        //if not empty cart we loop
        if(!empty($this->_cart))
        {
            foreach ($this->_cart as $row) 
            {
                //check if this product was already in the 
                //cart for add or update a product   
                if($row["rowId"] === $rowId && $isUpdate === FALSE)
                {
                    //if isset only add refresh qty items
                    $product["qty"] = $row["qty"] + $product["qty"];
                }
            }
        }
 
        //qty and price may only be positive numbers
        $product["qty"] = trim(preg_replace('/([^0-9\.])/i', '', $product["qty"]));
        $product["price"] = trim($this->_formatNumber($product["price"]));
 
        //add to cart  total price of the sum of this article
        $product["total"] = $this->_formatNumber($product["qty"] * $product["price"]);
 
        //must first remove the product if it was in the cart
        $this->removeProduct($rowId);
 
        ///add the product to cart
        $this->_cart[$rowId] = $product;
 
        //update the total price and the number of items in shopping cart
        $this->_updatePriceQty();

        return true;
    }

    /**
    * @desc - add multiple products to cart
    * @access public
    * @author Iparra
    * @param $products - array with product data to add
    */
    public function addMulti($products = array())
    {
        //check if is array and not empty, otherwise return false
        if(!is_array($products) || empty($products))
        {
            $this->_logNotArray(__LINE__);
            return false;    
        }

        foreach($products as $product)
        {
            $this->add($product);
        }
        return true;
    }

    /**
    * @desc - update info product
    * @access public
    * @author Iparra
    * @param $product - array with product data to update
    */
    public function update($product = array())
    {
        //check if is array and not empty, otherwise return false
        if(!is_array($product) || empty($product))
        {
            $this->_logNotArray(__LINE__);  
            return false;    
        }
        //update product with method add and second param to true
        if($this->add($product, TRUE) === true)
        {
            return true;
        }
    }

    /**
    * @desc - update multiple products
    * @access public
    * @author Iparra
    * @param $products - array with products data to update
    */
    public function updateMulti($products = array())
    {
        //check if is array and not empty, otherwise return false
        if(!is_array($products) || empty($products))
        {
            $this->_logNotArray(__LINE__);  
        }
        //update each product with method add and second param to true
        foreach($products as $product)
        {
            $this->add($product, TRUE);
        }
        return true;
    }

    /**
    * @desc - write to log error message if data product is incorrect
    * @access private
    * @author Iparra
    */
    private function _logNotArray($line)
    {
        $this->_instanceLog();
        $this->_logger->error(
            "Error, the product add to cart is empty o not is array, line ".$line." class ".__CLASS__."!"
        ); 
    }

    /**
    * @desc - remove a product by rowid and update content cart
    * @access private
    * @author Iparra
    * @return bool
    */
    private function _updatePriceQty()
    {
        //set price and product to 0
        $price = 0;
        $products = 0;
 
        //loop cart and update price and qty products
        foreach ($this->_cart as $row) 
        {
            $price += $this->_formatNumber(($row['price'] * $row['qty']));
            $products += $row['qty'];
        }
 
        //asign values to total_items and cart_total
        $this->_cart["total_items"] = $products;
        $this->_cart["cart_total"] = $price;
 
        //update content cart
        $this->_save();
    }

    /**
    * @desc - Check if cart item has options
    * @access public
    * @param $rowid - rowid contains references item would check
    * @author Iparra         
    * @return bool
    */
    public function hasOptions($rowId = '')
    {
        if(!isset($this->_cart[$rowId]['options']) || count($this->_cart[$rowId]['options']) === 0)
        {
            return FALSE;
        }

        return TRUE;
    }

    /**
    * @desc - get options for product by rowId
    * @access public
    * @author Iparra 
    * @param $rowId - rowid contains references item would return        
    * @return array
    */
    public function getOptions($rowId = '')
    {
        if(!isset($this->_cart[$rowId]['options']) || count($this->_cart[$rowId]['options']) === 0)
        {
            return array();
        }

        return $this->_cart[$rowId]['options'];
    }

    /**
    * @desc - return total price in cart
    * @access  public
    * @author Iparra
    * @return  float
    */
    public function getTotal()
    {
        return $this->_cart['cart_total'] ? $this->_formatNumber($this->_cart['cart_total']) : 0;
    }

    // --------------------------------------------------------------------

    /**
    * @desc - return total items in cart
    * @access  public
    * @author Iparra
    * @return  integer
    */
    public function getTotalItems()
    {
        return $this->_cart['total_items'] ? $this->_cart['total_items'] : 0;
    }

    /**
    * @desc - format array cart and return content
    * @access public
    * @author Iparra
    * @return array
    */
    public function getContent()
    {
        //asign content cart to $cart var and unset total_items and cart_total
        $cart = $this->_cart;
        unset($cart["total_items"]);
        unset($cart["cart_total"]);
        return $cart;
    }

    /**
     * Format Number
     *
     * Returns the supplied number with commas and a decimal point.
     *
     * @access  public
     * @return  integer
     */
    private function _formatNumber($number = '')
    {
        if ($number == '')
        {
            return '';
        }

        //Remove anything that isn't a number or decimal point
        $number = trim(preg_replace('/([^0-9\.])/i', '', $number));

        return number_format($number, 2, '.', ',');
    }

    /**
    * @desc - remove a product by rowid and update content cart
    * @access public
    * @author Iparra
    * @param $rowId - product rowid
    * @return bool
    */
    public function removeProduct($rowId = '')
    {
        if($this->_removeProduct($rowId) === TRUE)
        {
            return TRUE;
        }
        return FALSE;
    }

    /**
    * @desc - remove product by rowid
    * @access private 
    * @author Iparra 
    * @param $rowId - product rowid
    * @return bool
    */
    private function _removeProduct($rowId = '')
    {
        if(isset($this->_cart[$rowId]))
        {
            unset($this->_cart[$rowId]);
            $this->_updatePriceQty();
            $this->_save();    
            return TRUE;   
        }
        //the product not exists
        return FALSE;
    }

    /**
    * @desc - create dir logs if not exists
    * @access private
    * @author Iparra
    */
    private function _createLogDir()
    {
        if(is_dir("../app")) 
        {
            if(!is_dir("../app/logs")) 
            {
                mkdir("../app/logs");
            }
        }
    }

    /**
    * @desc - remove and update content cart
    * @access public
    * @author Iparra
    * @return bool
    */
    public function destroy()
    {
        $this->_destroy();  
        return true;
    }

    /**
    * @desc - Destroy cart
    * @access private  
    * @author Iparra
    */
    private function _destroy()
    {
        $this->_cart = null;
        $this->_save();        
    }
}
//End class shoppingCart
