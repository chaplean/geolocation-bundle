Getting Started With ChapleanGeolocationBundle
==============================================

# Prerequisites

This version of the bundle requires Symfony 2.8+.

# Installation

## 1. Composer

```
composer require chaplean/geolocation-bundle
```

## 2. AppKernel.php

Add
```
            new Chaplean\Bundle\MailerBundle\ChapleanGeolocationBundle(),
```

## 3. config.yml

##### A. Import

```
    - { resource: @ChapleanGeolocationBundle/Resources/config/config.yml }
```

##### B. Parameters

Add parameter `chaplean_geolocation.api_key` in `app/config/parameters.yml`

###### Note

Default api key for chaplean: `AIzaSyA4iQOHlCF5nqaEDKk-9IsYAMapdOvIATc`
