# Laravel 2C2P Redirect API , Payment Gateway Api & 123 Api

[![StyleCI](https://styleci.io/repos/98979571/shield?branch=master)](https://styleci.io/repos/98979571)
[![Latest Stable Version](https://poser.pugx.org/php-junior/laravel-2c2p/v/stable)](https://packagist.org/packages/php-junior/laravel-2c2p)
[![Total Downloads](https://poser.pugx.org/php-junior/laravel-2c2p/downloads)](https://packagist.org/packages/php-junior/laravel-2c2p)


Laravel 2C2P package

## Laravel version 5.x.x

## Installation

Install using composer:
```php
composer require php-junior/laravel-2c2p
```

Once installed, in your project's config/app.php file replace the following entry from the providers array:

```php
PhpJunior\Laravel2C2P\Laravel2C2PServiceProvider::class,
```

And 
```php 
php artisan vendor:publish --provider="PhpJunior\Laravel2C2P\Laravel2C2PServiceProvider" --force
```

This is the contents of the published config file:

```php
return [
    'merchant_id' => 'JT01',
    'secret_key' => '7jYcp4FxFdf0',

    'private_key_pass' => '2c2p',
    'private_key_path' => storage_path('cert/private.pem'),
    'public_key_path' => storage_path('cert/public.crt'),

    'redirect_access_url' => 'https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment',

    'access_url' => 'https://demo2.2c2p.com/2C2PFrontEnd/SecurePayment/PaymentAuth.aspx',
    'secure_pay_script' => 'https://demo2.2c2p.com/2C2PFrontEnd/SecurePayment/api/my2c2p.1.6.9.min.js',

    'currency_code' => 702, // Ref: http://en.wikipedia.org/wiki/ISO_4217
    'country_code' => 'MMR',

    '123_merchant_id' => 'merchant@smarthotel.com',
    '123_api_secret_key' => 'M5WCTP59J544IRRUBTJE0Q7Z2PAJX3CT',
    '123_public_key_path' => storage_path('cert/123.pem'), // 123' Certificate file
    '123_currency_code' => 'MMK',
    '123_country_code' => 'MMR',
    '123_agent_code' => 'ABC',
    '123_channel_code' => 'OVERTHECOUNTER',
    '123_merchant_url' => 'merchant url',
    '123_api_call_url' => 'api call url',
    '123_access_url' => 'https://demo3.2c2p.com/123MM/Payment/Pay/Slip'

    //QuickPay 
    'direct_api' => 'http://demo2.2c2p.com/2C2PFrontEnd/QuickPay/DirectAPI',
    'delivery_api' => 'http://demo2.2c2p.com/2C2PFrontEnd/QuickPay/DeliveryAPI'
];
```

#### Payment Request [ Using the Payment Gateway API and SecurePay ]

Construct Payment Form

Add the `data-encrypt` fields into the form to capture card information securely.

```html
<form id="2c2p-payment-form" action="" method="POST">
    <input type="text" data-encrypt="cardnumber" maxlength="16" placeholder="Credit Card Number"><br/>
    <input type="text" data-encrypt="month" maxlength="2" placeholder="MM"><br/>
    <input type="text" data-encrypt="year" maxlength="4" placeholder="YYYY"><br/>
    <input type="password" data-encrypt="cvv" maxlength="4" autocomplete="off" placeholder="CVV2/CVC2" ><br/>
    <input type="submit" value="Submit">
</form>

<script type="text/javascript" src="{{ config('laravel-2c2p.secure_pay_script') }}"></script>
<script type="text/javascript">
    My2c2p.onSubmitForm("2c2p-payment-form", function(errCode,errDesc){
        if(errCode!=0){ 
            alert(errDesc);
        }
    });
</script>

```

Submit the request your back end code will receives the encrypted credit card details from the checkout page

##### Preparation 

```php
$payload = \Payment2C2P::paymentRequest([
         'desc' => '1 room for 2 nights',
         'uniqueTransactionCode' => "Invoice".time(),
         'amt' => '1000000',
         'currencyCode' => '702',
         'cardholderName' => 'Card holder Name',
         'cardholderEmail' => 'email@emailcom',
         'panCountry' => 'SG',
         'encCardData' => $request->input('encryptedCardInfo'), // Retrieve encrypted credit card data 
         'userDefined1' => 'userDefined1',
         'userDefined2' => 'userDefined2'
     ]);
```

Submit the Payment Request:

```html
<!-- POST method to submit the form -->
<form action='{{ config('laravel-2c2p.access_url') }}' method='POST' name='paymentRequestForm'>
    Processing payment request, Do not close the browser, press back or refresh the page.
    <input type="hidden" name="paymentRequest" value="{{ $payload }}">
</form>
<script language="JavaScript">
    document.paymentRequestForm.submit();
</script>
```

#### Processing the response

```php
   $response = \Payment2C2P::getData($request->get('paymentResponse'))
   
   dd($response)
```

#### Payment Request [ Using 123 API ]

```php
$onwTwoThreeReq = \Payment2C2P::OneTwoThreeRequest([
       'MessageID' => '222222',
       'InvoiceNo' => 'QW232142',
       'Amount'    => 24444,
       'Discount'    => 10,
       'ShippingFee'    => 10,
       'ServiceFee'    => 10,
       'ProductDesc' => '1 room for 2 nights',
       'PayerName' => 'Name',
       'PayerEmail' => 'email@email.com',
       'ShippingAddress' => 'Yangon',
       'PayInSlipInfo' => 'Hello World',
       'PaymentItems' => [
           [
               'id' => 1212,
               'name' => 'Bla Bla',
               'price' => 12222,
               'quantity' => 1
           ],
           [
               'id' => 12,
               'name' => 'Bla Bla#2',
               'price' => 12222,
               'quantity' => 1
           ]
       ]
   ]);
```

Submit the Payment Request:

```html
<!-- POST method to submit the form -->
<form action='{{ config('laravel-2c2p.123_access_url') }}' method='POST' name='paymentRequestForm'>
    Processing payment request, Do not close the browser, press back or refresh the page.
    <input type="hidden" name="OneTwoThreeReq" value="{{ $onwTwoThreeReq }}">
</form>
<script language="JavaScript">
    document.paymentRequestForm.submit();
</script>
```

#### Processing the response

```php
   $response = \Payment2C2P::getData($request->get('OneTwoThreeRes'))
   
   dd($response)
```

#### Payment Request [ Using Redirect API ]

```php
<form action="{{ config('laravel-2c2p.redirect_access_url') }}"  method="POST">
    {!! \Payment2C2P::redirectRequest([
            'payment_description' => '2 room 2 night',
            'order_id' => 'QWERZX1234',
            'invoice_no' => 'ZXCQW123',
            'currency' => '840',
            'amount' => '1000',
            'customer_email' => 'email@email.com',
            'result_url_1' => 'http://127.0.0.1:8888/payment/complete'
    ]) !!}
    <button type="submit">Submit</button>
</form>
```

## QuickPay for Card Payments

QuickPay for Card Payments offers the following options
- QuickPay Direct API - Generate a new QuickPay URL, Merchant to deliver URL to customer (aka white label)
- QuickPay Delivery API - Generate and Send a QuickPay URL to an eMail or mobile phone

#### QuickPay Request [ Using Direct API ]
```php
    $requestMsg = Payment2C2P::quickPayRequest([
        'orderIdPrefix' => 'QP-zzzz2202',
        'description' => 'asasas',
        'currency' => 'USD',
        'amount' => '10',
        'allowMultiplePayment' => 'N',
        'expiry' => '16092017',
        'resultUrl1' => 'http://61.91.121.190/2c2pfrontend/uat/demomerchant/v3uifrontendurl.aspx', //	Front end return URL
        'resultUrl2' => 'http://61.91.121.190/2c2pfrontend/uat/demomerchant/v3uibackendurl.aspx',  //	Back end return URL
    ], 'generate' );

     $curl = curl_init();
     curl_setopt_array($curl, array(
       CURLOPT_URL => config('laravel-2c2p.direct_api'),
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_POSTFIELDS => $requestMsg,
     ));

     $response = curl_exec($curl);
     $err = curl_error($curl);
    
     curl_close($curl);
    
     if ($err) {
       dd("cURL Error #:" . $err);
     } else {
       dd(base64_decode($response)) ;
     }
```

#### QuickPay Request [ Using Delivery API ]
```php
    $requestMsg = Payment2C2P::quickPayRequest([
        'orderIdPrefix' => 'QP-zzzz2202',
        'description' => 'asasas',
        'currency' => 'USD',
        'amount' => '10',
        'allowMultiplePayment' => 'N',
        'expiry' => '16092017',
        'resultUrl1' => 'http://61.91.121.190/2c2pfrontend/uat/demomerchant/v3uifrontendurl.aspx', //	Front end return URL
        'resultUrl2' => 'http://61.91.121.190/2c2pfrontend/uat/demomerchant/v3uibackendurl.aspx',  //	Back end return URL
        'toEmails' => 'email@email.com',
        'emailSubject' => 'Email Subject',
        'emailMessage' => 'Message'
    ], 'generate-send' );

     $curl = curl_init();
     curl_setopt_array($curl, array(
       CURLOPT_URL => config('laravel-2c2p.delivery_api'),
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_POSTFIELDS => $requestMsg,
     ));

     $response = curl_exec($curl);
     $err = curl_error($curl);
    
     curl_close($curl);
    
     if ($err) {
       dd("cURL Error #:" . $err);
     } else {
       dd(base64_decode($response)) ;
     }
```

Send an existing Quickpay URL to an email or mobile phone.

```php
    $requestMsg = Payment2C2P::quickPayRequest([
        'qpID' => '121212',
        'toEmails' => 'email@email.com',
        'emailSubject' => 'Email Subject',
        'emailMessage' => 'Message'
    ], 'send-url' );

     $curl = curl_init();
     curl_setopt_array($curl, array(
       CURLOPT_URL => config('laravel-2c2p.delivery_api'),
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_POSTFIELDS => $requestMsg,
     ));

     $response = curl_exec($curl);
     $err = curl_error($curl);
    
     curl_close($curl);
    
     if ($err) {
       dd("cURL Error #:" . $err);
     } else {
       dd(base64_decode($response)) ;
     }
```

QuickPay Query - allows to check the status of an existing QuickPay transaction

```php
    $requestMsg = Payment2C2P::quickPayRequest([
        'qpID' => '12122',
    ], 'check' );

     $curl = curl_init();
     curl_setopt_array($curl, array(
       CURLOPT_URL => config('laravel-2c2p.direct_api'),
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_POSTFIELDS => $requestMsg,
     ));

     $response = curl_exec($curl);
     $err = curl_error($curl);
    
     curl_close($curl);
    
     if ($err) {
       dd("cURL Error #:" . $err);
     } else {
       dd(base64_decode($response)) ;
     }
```

QuickPay Update - allows a modification of an existing QuickPay transaction

```php
    $requestMsg = Payment2C2P::quickPayRequest([
        'qpID' => '1212',
        'description' => 'asasas',
        'currency' => 'USD',
        'amount' => '10',
    ], 'update' );

     $curl = curl_init();
     curl_setopt_array($curl, array(
       CURLOPT_URL => config('laravel-2c2p.direct_api'),
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_POSTFIELDS => $requestMsg,
     ));

     $response = curl_exec($curl);
     $err = curl_error($curl);
    
     curl_close($curl);
    
     if ($err) {
       dd("cURL Error #:" . $err);
     } else {
       dd(base64_decode($response)) ;
     }
```

QuickPay Delete - allows deletion of an existing QuickPay transaction

```php
    $requestMsg = Payment2C2P::quickPayRequest([
        'qpID' => '121212',
    ], 'delete' );

     $curl = curl_init();
     curl_setopt_array($curl, array(
       CURLOPT_URL => config('laravel-2c2p.direct_api'),
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_POSTFIELDS => $requestMsg,
     ));

     $response = curl_exec($curl);
     $err = curl_error($curl);
    
     curl_close($curl);
    
     if ($err) {
       dd("cURL Error #:" . $err);
     } else {
       dd(base64_decode($response)) ;
     }
```

## Api Variables

- Redirect Api variable from [here](https://developer.2c2p.com/docs/redirect-variables).
- Payment Gateway Api variable from [here](https://developer.2c2p.com/docs/api-variables).
- 123 Api from [here](https://developer.2c2p.com/docs/123-payments-123)
- QuickPay variable from [here](https://developer.2c2p.com/docs/quickpay-variables-1)

## Full Documentation

Read Full Documentation [here](https://developer.2c2p.com/docs)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.