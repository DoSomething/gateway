# Gateway

This is **Gateway**, an opinionated HTTP client built with [Guzzle](http://guzzle.readthedocs.io/en/stable/). It makes API requests easy with built-in [OAuth2 authorization](traits/AuthorizesWithOAuth2.md), support for [transaction ID headers](traits/ForwardsTransactionIds.md), and [web authentication](server/OpenIDConnect.md) via OpenID Connect.

### Getting Started

* [Usage](Usage.md) - installation & configuration

### Clients

* [RestApiClient](clients/RestApiClient.md) – the base API client & how to make your own
* [Northstar](clients/Northstar.md) - the DoSomething.org user & identity API
* [Blink](clients/Blink.md) – the DoSomething.org message bus

### Traits

* [OAuth 2](traits/AuthorizesWithOAuth2.md) - authorize requests with OAuth2
* [Basic Auth](traits/AuthorizesWithBasicAuth.md) - authorize requests with HTTP Basic Authentcation
* [API Key Header](traits/AuthorizesWithApiKey.md) - authorize requests with a static API key
* [Transaction IDs](traits/ForwardsTransactionIds.md) - automatically set & increment request IDs

### Servers

* [Resource Servers](server/ResourceServer.md) - resource server guard & middleware
* [OpenID Connect](server/OpenIDConnect.md) - single-sign-on for Laravel apps
