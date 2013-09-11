# HTTP Client Hints

The Client Hints (CH) header communicates device capabilities to the server, allowing the server to return the best possible experience back to the device.

This project aims to polyfill <abbr title="Client Hints">CH</abbr> to the best abilities of the device and server.

[HTTP Client Hints Draft](//github.com/igrigorik/http-client-hints)

## How it works:

One line of JavaScript can make a variety of hints available to the server. For instance, if the following script is included at the top of the document, all future requests (even those made from that same document) can leverage these hints. The draft calls for 3 hints; device pixel ratio, device width, and device height.

```javascript
document.cookie = 'CH=dh=' + screen.height +
	',dpr=' + (window.devicePixelRatio || 1) +
	',dw=' + screen.width +
	';expires=' + new Date(+new Date+31536000000).toGMTString() +
	';path=/';
```

Since these 3 <abbr title="Client Hints">CH</abbr> are unlikely to change in a given session, the above snippet could be wrapped in a server-side conditional to load just once.

```php
<?php if (!isset($_SERVER['HTTP_CH'])) : ?>
	<script>document.cookie = ...</script>
<?php endIf; ?>
```

The native implementation of <abbr title="Client Hints">CH</abbr> works by sending hints through a request header, which the server could cache. This polyfill emulates the projected end-result &mdash; a server with a cache of hints.

## What is included:

### create.htaccess

An Apache configuration snippet for converting a <abbr title="Client Hints">CH</abbr> cookie into a server environment variable.

### create.js

A JavaScript snippet for creating a <abbr title="Client Hints">CH</abbr> cookie.

### create.php

A PHP snippet for conditionally creating a <abbr title="Client Hints">CH</abbr> cookie when the server environment variable is not yet found.

## What the future holds:

An [issue on GitHub](//github.com/igrigorik/http-client-hints/issues/3) suggests including more dynamic data in <abbr title="Client Hints">CH</abbr>, such as device orientation, viewport size, and zoom level as well.

Admittedly, these values [*<q>change frequently and are difficult to cache</q>*](//github.com/igrigorik/http-client-hints/issues/3#issuecomment-14573532), whereas the currently drafted hints are unlikely to change in the life of the device.

Since <abbr title="Client Hints">CH</abbr> adoption is in its early stages, [Ilya Grigorik](//twitter.com/igrigorik), author of the [HTTP Client Hints Draft](//github.com/igrigorik/http-client-hints), argues that the [*<q>CH header must be kept as small and as static as possible to prove its viability</q>*](//github.com/igrigorik/http-client-hints/issues/3#issuecomment-14427978).

## Native implementation status:

* [Latest Client Hints draft on IETF tracker](//tools.ietf.org/html/draft-grigorik-http-client-hints)
* [Blink intent to implement thread](//groups.google.com/a/chromium.org/d/msg/blink-dev/c38s7y6dH-Q/bNFczRZj5MsJ) ([patch under review](//codereview.chromium.org/23654014))

---

All of these contributions to <abbr title="Client Hints">CH</abbr> are dedicated to the [public domain with no copyright](//creativecommons.org/publicdomain/zero/1.0/).