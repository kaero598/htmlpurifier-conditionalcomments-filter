# IE conditional comments filter for <a href="https://github.com/ezyang/htmlpurifier">HTMLPurifier</a>

HTMLPurifier always removes IE conditional comments from HTML and there is no way to alter that behavior without touching the sources.

Conditional comments filter disguises IE conditional comments as plain tags and reverts them back after HTMLPurifier has purified HTML. This also means that HTML inside conditional comments also gets purified.

## Example

```php
<?php

$config = HTMLPurifier_Config::createDefault();

$config->set('Filter.Custom', [
    new HTMLCleaner\Filter\ConditionalComments(),
]);

$purifier = new HTMLPurifier($config);

$purified_html = $purifier->purify($html);
```
