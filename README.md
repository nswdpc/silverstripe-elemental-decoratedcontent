# Decorated content block for Silverstripe

This module extends the content element to provide some decorations for the  default `ElementContent` content block.

It provides:

* an image + alignment option
* a video (YouTube / Vimeo) field
* a subtitle
* a link (via gorriecoe/linkfield)
* a call to action text field
* a date (different to created/last edited)
* a tag field (via silverstripe/taxonomy)

(all optional)

Implementation of these features in a template can and should be done at the project level.

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
