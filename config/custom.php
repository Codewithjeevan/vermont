<?php

return [

	/**
	 * 
	 * This is a number which tells how many decimals should be there for amount
	 * 
	 */
	'amt_fix' => 2,

	/**
	 * 
	 * This is a number which tells how many decimals should be there for quantity
	 * 
	 */
	'qty_fix' => 3,


	/**
	 * The number will pad bill number with given digit
	 * 
	 */
	
	'bill_pad' => 0,

	/**	
	 * 	This is currency Symbol
	 */
	'curr' => "AED",


	/**
	 *  This is bill Number Prefix Word
	 */
	
	'bill_prefix_word' => "",

	/**
	 * This is Email option
	 */
	
	'email' => "retailsalesac@gmail.com",

	/**
	 * This date Format option
	 */
	
	'date_format' => "d/m/Y",

	/**
	 * This is Text Currency
	 */
    
    'text_currency' => "AED ",

    /**
     * This is Text Currency After Decimal
     */
    
    'text_currency_after_decimal' => " Fils Only",

    /**
     * This is barcode Type
     */
    
    'barcode_type' => "BCGcode128",

    /**
     * This is Borcode rate prefix option
     */
    'barcode_rate_prefix' => 99,

    /**
     * This is boarcode Weight Prefix
     */
    'barcode_weight_prefix' =>55,

    /**
     *  This is barcode decimal price limit
     */
    
    'barcode_decimal_price_limit' =>2,


    /**
     *  This is for default vat type 0 => no vat, 1 => inclusive Vat and 2 => exclusive Vat
     */
    
    'vat_type' => 0,
    
	]
	?>