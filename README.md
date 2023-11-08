This package currently supports bulk updates ONLY IN MYSQL and is compatible with Laravel version 8 and above

<br>

- [x] supports MySql
- [ ] supports MariaDB
- [ ] supports PortgreSQL

# Laravel fast bulk update

[![Latest Stable Version](http://poser.pugx.org/quangpv/fast-bulk-update/v)](https://packagist.org/packages/quangpv/fast-bulk-update)
[![Total Downloads](http://poser.pugx.org/quangpv/fast-bulk-update/downloads)](https://packagist.org/packages/quangpv/fast-bulk-update)
[![Latest Unstable Version](http://poser.pugx.org/quangpv/fast-bulk-update/v/unstable)](https://packagist.org/packages/quangpv/fast-bulk-update) 
[![License](http://poser.pugx.org/quangpv/fast-bulk-update/license)](https://packagist.org/packages/quangpv/fast-bulk-update) 
[![PHP Version Require](http://poser.pugx.org/quangpv/fast-bulk-update/require/php)](https://packagist.org/packages/quangpv/fast-bulk-update)


# Install
`composer require quangpv/fast-bulk-update`

# Example Usage

```php
use App\Models\User;

$userInstance = App::make(User::class);

$value = [
     [
         'id' => 1,
         'code' => 'UC01',
         'nickname' => 'quangpv'
     ] ,
     [
         'id' => 2,
         'code' => 'UC02',
         'nickname' => 'haiza'
     ] ,
];

// We will find the records by their IDs and update them.
$indexes = ['id']; // primary key
// $indexes = ['code', 'id']; // composite primary key

$affectedRows = BatchUpdate::execute($userInstance, $value, $indexes);
```

> [!WARNING]
> Ensure that the indexes match the primary key in your table, because the MySQL database driver always uses the "primary" and "unique" indexes of the table to detect existing records.

# Other option
<br>
If you wanna update on fields that are not primary or unique indexes, you can refer to my approach here 
<br>
https://github.com/quanggpv/laravel-upsert-improved.
<br>

<br>

# My idea

<br>

The basic update queries will use the 'INSERT...ON DUPLICATE KEY UPDATE...' statement in MYSQL.

I took inspiration from Laravel's 'upsert' function, but removed the 'insert' functionality. I believe the built-in function of MySQL has an excellent data structure for updating

<br>

> [!IMPORTANT]
>  If this package has been helpful to you, please give me a star. Thank you! :D

# References
<br>
https://dev.mysql.com/doc/refman/8.0/en/insert-on-duplicate.html
<br>
<br>
https://laravel.com/docs/10.x/eloquent#upserts
<br>







