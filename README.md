# EXS-LanderTrackingAWEBundle

[![Build Status](https://travis-ci.org/ExSituMarketing/EXS-LanderTrackingAWEBundle.svg)](https://travis-ci.org/ExSituMarketing/EXS-LanderTrackingAWEBundle)

## What is this bundle doing ?

This bundle is not a standalone bundle and requires `EXS-LanderTrackingHouseBundle`.

It will add an extracter and a formatter to be added to `EXS-LanderTrackingHouseBundle` to manage AWE tracking parameter.

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

## Usage

See `EXS-LanderTrackingHouseBundle`'s documentation for more information.
