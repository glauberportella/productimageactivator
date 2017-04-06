# Prestashop Product Image Activator

## Requirements

- Prestashop 1.6 installed and running

## What this module will offer

This module will add a checkbox field on Product > Images that will allow you to select which image to activate (show in frontoffice).

## After installadtion

The module just override product administrator images template and add a new field on `Image` class (`pia_active`).

After installed you will need to edit your theme product views to do the check of `pia_active` field to see if you will allow that product image to be shown (pia_active = 1) or not (pia_active = 0).

## Example of theme product view template

Example based on `default` Prestashop 1.6 theme

```php
```