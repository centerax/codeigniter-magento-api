# CodeIgniter Magento API Spark

This spark facilitates the access to all [Magento](http://www.magentocommerce.com/) Core API resources and methods.
One library, one config file and that is it!

## Requirements

1. PHP 5.1+
2. CodeIgniter Reactor 2.0
3. Magento installation
4. [PHP-SOAP](http://www.php.net/soap)

## Usage

Open config file and enter:
- Magento WSDL URI, eg: http://demo.magentocommerce.com/api/soap?wsdl
- Magento API username and password

This spark automagically calls [API methods](http://www.magentocommerce.com/support/magento_core_api) from inside CodeIgniter code.
So you don't need to worry about instantiating SOAP or anything.

A few examples below.

Want to update a product?

    $this->load->spark('mage-api/0.0.1');
    $update = array('name'=>'New Name');
    var_dump( $this->mage_api->product_update( 'product_sku', $update ) );

How about getting a list (PHP array) of all customer groups?

    var_dump( $this->mage_api->customer_group_list() );

The "magic" is that _customer_group_list_ is translated to _customer_group.list_ API call and so on with all the methods on the API.

## Change Log

### 0.0.1

* First release