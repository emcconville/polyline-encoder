# Polyline Encoder
[![Build Status][buildStatusImage]][buildStatusLink]
[![Latest Stable Version][stableStatusImage]][stableStatusLink]
[![License][licenseStatusImage]][licenseStatusLink]

Formerly [emcconville/google-map-polyline-encoding-tool][oldLibRef], this library
provides encoding/decoding methods for Google Map's [Encoded Polyline][polyAlgoRef],
and Microsoft's [Point Compression Algorithm][pointAlgoRef]. The intent of the
[Polyline Encoder][newlibRef] library is to shift the core algorithms to PHP Traits
over traditional class implementations. 

## Installation

Add `emcconville/polyline-encoder` to composer's required list.

```json
{
  "require" : {
    "emcconville/polyline-encoder" : "1.*"
  }
}
```

Follow basic composer installation & guide.

```bash
curl -sS https://getcomposer.org/installer | php
./composer.phar install
```
## Usage

Both `BingTrait` & `GoogleTrait` over the same two methods.

*string* &lt;object&gt;**::encodePoints**( *array* $points )
```php
// Convert list of points into encoded string.
$points = [
  [41.89084,-87.62386],
  [41.89086,-87.62279],
  [41.89028,-87.62277],
  [41.89028,-87.62385],
  [41.89084,-87.62386]
];

$googleObject->encodePoints($points); //=> "wxt~Fd`yuOCuErBC?vEoB@"
$bingObject->encodePoints($points);   //=> "yg7qol5jxJjqX3iH01W5sG"
```
*array*  &lt;object&gt;**::decodeString**( *string* $string )

```php
// Restore list from encode string
$points = $googleObject->decodeString("wxt~Fd`yuOCuErBC?vEoB@");
$points[3]; //=> array(41.89028,-87.62385)
$points = $bingObject->decodeString("yg7qol5jxJjqX3iH01W5sG");
$points[4]; //=> array(41.89084,-87.62386)
```

### Goolge Map

```php

// Apply Google Trait
class MyGooglePolyline
{
  use emcconville\Polyline\GoogleTrait;
}

```

### Bing Map

```php

// Apply Bing Trait
class MyGooglePolyline
{
  use emcconville\Polyline\BingTrait;
}

```

### OSRM Map

```php
// Apply Google Trait with precision overwrite
class MyGooglePolyline
{
  use emcconville\Polyline\GoogleTrait;
  
  /**
   * Implement precision method in sub-class
   * @return int
   */
  public function polylinePrecision()
  {
      return 6;
  }
}

```

[buildStatusImage]:   https://secure.travis-ci.org/emcconville/polyline-encoder.png
[buildStatusLink]:    http://travis-ci.org/emcconville/polyline-encoder
[stableStatusImage]:  https://poser.pugx.org/emcconville/polyline-encoder/v/stable.png
[stableStatusLink]:   https://packagist.org/packages/emcconville/polyline-encoder
[licenseStatusImage]: https://poser.pugx.org/emcconville/polyline-encoder/license.png
[licenseStatusLink]:  https://packagist.org/packages/emcconville/polyline-encoder
[oldLibRef]:          https://github.com/emcconville/google-map-polyline-encoding-tool
[newLibRef]:          https://github.com/emcconville/polyline-encoder
[pointAlgoRef]:       http://msdn.microsoft.com/en-us/library/jj158958.aspx
[polyAlgoRef]:        https://developers.google.com/maps/documentation/utilities/polylinealgorithm

