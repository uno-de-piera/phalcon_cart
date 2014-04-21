<h1>Complet cart system for Phalcon</h1>
<p>Phalcon cart provides complet cart system, allows you to create multiple instances to have all the areas we want independently.</p>
<p>
If ocurred any problem with proccess cart, the class store logs into app/logs/shoppingCart.log for more info.</p>
<p>
Cart works with Phalcon sessions, if you would, you can use adapter sessions for manage cart with database, more info.
</p>

* [Incubator Link](https://github.com/phalcon/incubator/blob/master/README.md#installing-via-github)
* [Phalcon Sesion Adapter](https://github.com/phalcon/incubator/tree/master/Library/Phalcon/Session/Adapter#phalconsessionadapter)
* [Show demo](http://phalcon-tuts.com/phalconShop)

<h1>Installation with Composer</h1>
<p>Create a new file composer.json, open and add the next code</p>
```json
{
    "require": {
        "unodepiera/phalcon_cart": "dev-master"
    },
    "minimum-stability": "dev"
}
```
<p>Update your packages with composer update or install with composer install.</p>
<p>Now open app/config/loader.php and replace the code.</p>
```php

	$loader = new \Phalcon\Loader();

	/**
	 * We're a registering a set of directories taken from the configuration file
	 */
	$loader->registerDirs(
		array(
			$config->application->controllersDir,
			$config->application->modelsDir,
			$config->application->libraryDir
		)
	);

	//register the new class ShoppingCart
	$loader->registerClasses(
	    array(
	        "ShoppingCart"         => "../vendor/unodepiera/phalcon_cart/ShoppingCart.php",
	    )
	);

	$loader->register();
```

<h1>Installation without Composer</h1>
<p>
Download file ShoppingCart.php and create a new directory library in app dir.
Save file into library dir and open app/config/loader.php, now update this file.
</p>
```php
	$loader = new \Phalcon\Loader();

	/**
	 * We're a registering a set of directories taken from the configuration file
	 */
	$loader->registerDirs(
		array(
			$config->application->controllersDir,
			$config->application->modelsDir,
			$config->application->libraryDir//register dir library dir
		)
	);

	$loader->register();
```
<h1>Example Usage Phalcon Cart</h1>
<h2>First create a new instance</h2>
```php
	$this->cart = new ShoppingCart("myShop");
```
<h2>Insert simple product</h2>
```php
	$product = array(
		"id"			=>		3,
		"name"			=>		"Pasta de dientes",
		"price"			=>		1.80,
		"qty"			=>		2,
		"description"	=>		"Pasta de dientes......"
	);

	if($this->cart->add($product) != false)
	{
		echo "<pre>";
		var_dump($this->cart->getContent());
	}
```
<h2>Insert multiple products</h2>
```php
	$products = array(
		array(
			"id"			=>		1,
			"name"			=>		"Almendras",
			"price"			=>		2.5,
			"qty"			=>		8,
			"description"	=>		"Almendras saladas"
		),
		array(
			"id"			=>		2,
			"name"			=>		"Galletas pou",
			"price"			=>		2.7,
			"qty"			=>		5,
			"description"	=>		"Galletas del amigo pou"
		),
		array(
			"id"			=>		3,
			"name"			=>		"Pasta de dientes",
			"price"			=>		1.80,
			"qty"			=>		8,
			"description"	=>		"Pasta de dientes......"
		)
	);

	if($this->cart->addMulti($products) != false)
	{
		echo "<pre>";
		var_dump($this->cart->getContent());
	}
```
<h2>Update one product</h2>
```php
	$product = array(
		"id"			=>		3,
		"name"			=>		"Pasta de dientes",
		"price"			=>		1.80,
		"qty"			=>		12,
		"description"	=>		"Pasta de dientes......"
	);

	if($this->cart->update($product) != false)
	{
		echo "<pre>";
		var_dump($this->cart->getContent());
	}
```
<h2>Update multiple products</h2>
```php
	$products = array(
		array(
			"id"			=>		1,
			"name"			=>		"Almendras",
			"price"			=>		2.5,
			"qty"			=>		1,
			"description"	=>		"Almendras saladas"
		),
		array(
			"id"			=>		2,
			"name"			=>		"Galletas pou",
			"price"			=>		2.7,
			"qty"			=>		1,
			"description"	=>		"Galletas del amigo pou"
		),
		array(
			"id"			=>		3,
			"name"			=>		"Pasta de dientes",
			"price"			=>		1.80,
			"qty"			=>		1,
			"description"	=>		"Pasta de dientes......"
		)
	);

	if($this->cart->updateMulti($products) != false)
	{
		echo "<pre>";
		var_dump($this->cart->getContent());
	}
```
<h2>Check and print options</h2>
<p>Check if product has options and print, need his rowId</p>
```php
	if($this->cart->hasOptions("0e043c0cd48de80fa4f6ed23a15d6d10") != false)
	{
		echo "<pre>";
		var_dump($this->cart->getOptions("0e043c0cd48de80fa4f6ed23a15d6d10"));
	}
```
<h2>Get total price cart</h2>
```php
	echo $this->cart->getTotal();
```
<h2>Get total items cart</h2>
```php
	echo $this->cart->getTotalItems();
```
<h2>Remove a product</h2>
<p>You just need to pass a rowid that there</p>
```php
	if($this->cart->removeProduct("0e043c0cd48de80fa4f6ed23a15d6d10") != false)
	{
		echo "<pre>";
		var_dump($this->cart->getContent());
	}
```
<h2>Remove a cart</h2>
<p>You just need that there</p>
```php
	if($this->cart->destroy() != false)
	{
		echo "<pre>";
		var_dump($this->cart->getContent());
	}
```
<h2>Get cart content</h2>
```php	
	var_dump($this->cart->getContent());
```

## Visit me

* [Visit me](http://uno-de-piera.com)
* [Phalcon Cart on Packagist](https://packagist.org/packages/unodepiera/phalcon_cart)
* [License](http://www.opensource.org/licenses/mit-license.php)
