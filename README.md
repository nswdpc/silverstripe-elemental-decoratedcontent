# Decorated content block for Silverstripe

This module extends the content element to provide some decorations for the  default `ElementContent` content block.

It provides:

* an image + alignment option
* a video (YouTube / Vimeo) field
* a subtitle
* a link
* a call to action text field
* a date (different to created/last edited)
* a tag field (via silverstripe/taxonomy)

(all optional)

Implementation of these features in a template can and should be done at the project level.

## Link migration

This version includes [staged migration of links](https://github.com/nswdpc/silverstripe-linkfield/blob/feat-link-migrator-staged/docs/en/migration.md) to the core Silverstripe Link model.

1. *Important*: Update all calls to `$TargetLink`/`getTargetLink()`/`TargetLink()` in your project  to `$DerivedTargetLink`/`getDerivedTargetLink()`/`DerivedTargetLink()`
2. Once the above change is available on your site, in the "Link migration" admin section any existing links will show with a  "Migrate" button
3. Hit the "Migrate" button
4. Records will migrate to the core Silverstripe Link model and the element interface will display the core Silverstripe link field once the element is republished.
5. Template usage on site frontend will pick up the new, core, link.

The next major release of this module will remove dependency of `nswdpc/silverstripe-linkfield` and legacy linkfield implementation.

## Requirements

See [composer.json](./composer.json) for details.

## Installation

```sh
composer require nswdpc/silverstripe-element-decoratedcontent
```
Add the element to the list of `allowed_elements`.

## Usage

In a page, select the 'Decorated content' block and complete the fields, then publish.

## License

BSD-3-Clause

See [License](./LICENSE.md)

## Configuration

None!

## Maintainers

+ PD Web Team

## Bugtracker

Please add issues to the Github issue tracker

## Security

If you have found a security issue with this module, please email digital[@]dpc.nsw.gov.au in the first instance, detailing your findings.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
