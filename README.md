# EXS-LanderTrackingAWEBundle

[![Build Status](https://travis-ci.org/ExSituMarketing/EXS-LanderTrackingAWEBundle.svg)](https://travis-ci.org/ExSituMarketing/EXS-LanderTrackingAWEBundle)

## What is this bundle doing ?

This bundle is not a standalone bundle and requires `EXS-LanderTrackingHouseBundle`.

It will add an extracter and a formatter to be added to `EXS-LanderTrackingHouseBundle` to manage AWE tracking parameter.

The extracter service searches for parameters :
- `prm[campaign_id]` which contains `{cmp}`
- `subAffId` which contains a string composed of `{exid}~{visit}`

The formatter service will add the parameters if  :
- `prm[campaign_id]` will contains `{cmp}`
- `subAffId` will contains a string composed of `{exid}~{visit}`

## Installation

Download the bundle using composer

```
$ composer require exs/lander-tracking-awe-bundle
```

Enable the bundle

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new EXS\LanderTrackingAWEBundle\EXSLanderTrackingAWEBundle(),
        // ...
    );
}
```

## Configuration

The `cmp` parameter has a default value configurable with this configuration key : 

```yml
# Default values.
exs_lander_tracking_awe:
    default_cmp: 1
```

## Usage

Example :
```twig
    <a href="{{ 'http://www.test.tld/' | appendTracking('awe') }}">Some link</a>
    <!-- Will generate : "http://www.test.tld/?prm[campaign_id]=123&subAffId=987654321~5" -->
    
    <a href="{{ 'http://www.test.tld/?foo=bar' | appendTracking('awe') }}">Some link</a>
    <!-- Will generate : "http://www.test.tld?foo=bar&prm[campaign_id]=123&subAffId=987654321~5" -->
```

See `EXS-LanderTrackingHouseBundle`'s documentation for more information.
