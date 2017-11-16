# Gateway [![Packagist](https://img.shields.io/packagist/v/dosomething/gateway.svg?style=flat)](https://packagist.org/packages/dosomething/gateway) [![wercker status](https://app.wercker.com/status/42faaea97c7e73c85e24ddf56df9f1e2/s/master "wercker status")](https://app.wercker.com/project/byKey/42faaea97c7e73c85e24ddf56df9f1e2)

This is **Gateway**, an opinionated HTTP client built with [Guzzle](http://guzzle.readthedocs.io/en/stable/). It makes API requests easy with built-in [OAuth2 authorization](docs/traits/AuthorizesWithOAuth2.md), support for [transaction ID headers](docs/traits/ForwardsTransactionIds.md), and [web authentication](docs/server/OpenIDConnect.md) via OpenID Connect.

### Getting Started

Install the latest release with [Composer](https://getcomposer.org):

```sh
composer require dosomething/gateway
```

Then, follow the instructions for [Laravel](docs/Usage.md#laravel) or [vanilla PHP](docs/Usage.md#vanilla-php).

### Included Clients

Gateway includes pre-built clients for a few DoSomething.org services:

* [Northstar](docs/clients/Northstar.md) - the DoSomething.org user & identity API
* [Blink](docs/clients/Blink.md) – the DoSomething.org message bus

### License

MIT &copy; DoSomething.org
