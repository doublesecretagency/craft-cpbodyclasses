Control Panel Body Classes plugin for Craft CMS
======================================

_Add special classes to the CP's `<body>` tag._

## Usage

This plugin will automatically add classes to the `<body>` tag in the control panel. You can use these classes, along with CSS or JavaScript, to target specific DOM elements and manipulate the page. This only affects the control panel... your front-end pages will be unaffected.

After you install it, select which classes you want to use:

![](README-images/example-settings.png)

## Why?

Because there are a million things that you may want to do in your control panel, and many of them are conditional. Perhaps you want your CSS (or JS) to only take effect for a certain user group, or only on a specific page. This plugin is designed to pair perfectly with [Control Panel CSS](https://github.com/lindseydiloreto/craft-cpcss) and/or [Control Panel JS](https://github.com/lindseydiloreto/craft-cpjs).

You can include CSS/JS in your own custom plugin as well!

***

## Disclaimer

It's important to note that showing/hiding fields via CSS/JS is **purely cosmetic**. Those fields may remain accessible to a savvy user, so don't rely on this plugin to guarantee access/denial of any DOM elements. **We accept no liability for any security issues arising from the use of this plugin.**