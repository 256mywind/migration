# Attention

Must be added namespace

![](app.png)
### or
![](custom.png)

## swoft migration

swoft database migration component

## install

```sh
composer require yangdong/swoft-migration
```
If it cannot be installed.
maybe add version number.
```sh
composer require yangdong/swoft-migration:dev-master
```
## explain

about config

.env 

```sh
DB_URI=127.0.0.1:3306/swoft?user=root&password=admin123456&charset=utf8mb4&table_prefix=sw_,127.0.0.1:3306/swoft?user=root&password=admin123456&charset=utf8mb4&table_prefix=sw_

```
Table prefix can be added.

```sh
&table_prefix=sw_
```

If no prefix is added, the default prefix ``` sw_ ``` will be used.

db.php can use ```evn()``` get .env file config data.
