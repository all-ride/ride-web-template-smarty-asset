# Ride: Template Assets (Smarty)

Smarty plugins for the Ride framework, used to render assets from the ORM.

## Code Sample

### asset

Function to render an asset

| Parameter | Type | Description |
| --- | --- | --- |
| src | string|AssetEntry | Asset to render |
| style | string | Machine name of the style (optional) |
| var | string | Variable to assign the result to |

```Smarty
{asset src=15 style="thumbnail"}
```

## Related Modules 

- [ride/app](https://github.com/all-ride/ride-app)
- [ride/app-image](https://github.com/all-ride/ride-app-template)
- [ride/app-template](https://github.com/all-ride/ride-app-template)
- [ride/app-template-smarty](https://github.com/all-ride/ride-app-template-smarty)
- [ride/lib-image](https://github.com/all-ride/ride-lib-i18n)
- [ride/lib-template](https://github.com/all-ride/ride-lib-template)
- [ride/lib-template-smarty](https://github.com/all-ride/ride-lib-template-smarty)
- [ride/wba-assets](https://github.com/all-ride/ride-wba-assets)
- [ride/web](https://github.com/all-ride/ride-web)
- [ride/web-image](https://github.com/all-ride/ride-web-image)
- [ride/web-template](https://github.com/all-ride/ride-web-template)
- [ride/web-template-smarty](https://github.com/all-ride/ride-web-template-smarty)

## Installation

You can use [Composer](http://getcomposer.org) to install this application.

```
composer require ride/web-template-smarty-asset
```
