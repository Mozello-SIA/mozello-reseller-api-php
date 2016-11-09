Mozello Reseller PHP API
========================

PHP API for Mozello resellers. Mozello is online website creator: http://mozello.com

Implements Mozello Reseller API: http://www.mozello.com/developers/reseller-api/

Sample usage:

```php
<?php

require 'mozelloapi.php';

$resellerEmail = '';
$resellerPassword = '';

$mozello = new MozelloApi($resellerEmail, $resellerPassword);
$mozello->authorize();

//Demo: update reseller's support e-mail address
$res = $mozello->setSettings(['supportEmail' => 'support@example.org']);

$mozello->logout();
?>
```
