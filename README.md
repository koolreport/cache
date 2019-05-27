# Introduction

This package is all about the speed and responsiveness of your reports.
Let imagine your report need to pull large data from various sources to deliver the computed results. It takes time to load and process data. If many people
go to your report at the same time, server may be overloaded.

`Cache` package will solve above problem. It will store the computed results temporarily in a period of time. If the report need to reload, results will be loaded from the cache, which results in lowering the load on your database and also the computation of your report. Utimately, it will increase the speed and responsiveness of your report.

# Installation

1. Download the package and unzip
2. Copy `cache` folder into `koolreport\packages`

# Documentation

`Cache` package provides three type of caching options: `FileCache`, `ApcCache` and `MemCache`.

## FileCache

This will store computed results using file system. There is no further installation needed if you use `FileCache`.

To enable this cache, you do:

```
<?php

class MyReport extends \koolreport\KoolReport
{
    use \koolreport\cache\FileCache;

    function cacheSettings()
    {
        return array(
            "ttl"=>60,
        );
    }
    ...
}
```

The `"ttl"` means Time To Live which is the time cache will store the result.

## ApcCache

This will use the well-known cache system `Apc`. You need to install the Apc php module if you want to use this cache method. [Click here to know how to install Apc](http://php.net/manual/en/book.apc.php)

To use `ApcCache`, you do:

```
<?php

class MyReport extends \koolreport\KoolReport
{
    use \koolreport\cache\ApcCache;

    function cacheSettings()
    {
        return array(
            "ttl"=>60,
        );
    }
    ...
}
```

The `"ttl"` means Time To Live which is the time cache will store the result.

## MemCache

This is another well-known caching system in PHP. You will need to install the MemCached PHP Module to use the cache method.[Click here to know how to install MemCached](http://php.net/manual/en/book.memcached.php)

To use the `MemCache` you do:

```
<?php

class MyReport extends \koolreport\KoolReport
{
    use \koolreport\cache\MemCache;

    function cacheSettings()
    {
        return array(
            "ttl"=>60,
            "servers"=>array(
                "localhost"=>34212,
                "1.233.222.24"=>1223
            )
        );
    }
    ...
}
```

The `"ttl"` means Time To Live which is the time cache will store the result. And the `"servers"` contains the list of memcahe servers you want to use.


## Support

Please use our forum if you need support, by this way other people can benefit as well. If the support request need privacy, you may send email to us at __support@koolreport.com__.