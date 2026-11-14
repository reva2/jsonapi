# Changelog

## 3.3.1 - 2026-11-14

### Fixed
- An empty request to an endpoint with `bodyType` for which no decoder was selected is rejected with HTTP 415 and
  `HeadersChecker::UNSUPPORTED_CONTENT_TYPE_ERROR` (3.3.0 let it through with a `null` body). For a valid but
  unsupported `Content-Type` this restores the 3.2.3 behaviour; for a missing or malformed `Content-Type` the response
  is 415 for the first time (3.2.3 failed with HTTP 500). An empty body with a supported `Content-Type` still fails
  with HTTP 400, endpoints without `bodyType` are not affected.
- This also affects endpoints whose `bodyType` is inherited from the class-level `#[Request]` attribute, including `GET`
  ones: such a request without a body and without a `Content-Type` header used to reach the controller with a `null`
  body in 3.3.0, now it is rejected with HTTP 415 (and with a supported `Content-Type` it fails with HTTP 400, as
  before). Set `bodyType` only on the methods that read a body, or mark the body as optional with `bodyOptional: true`
  (see below).

### Added
- `HeadersChecker::createUnsupportedContentTypeException()`.
- `bodyOptional` option of `#[Request]` (class or method level; `Environment::setBodyOptional()` /
  `isBodyOptional()`). When `bodyType` is set the body is required by default. With `bodyOptional: true` a request
  without body (empty content) passes with a `null` body for any `Content-Type`; a request with a body is checked as
  usual (415 `UNSUPPORTED_CONTENT_TYPE_ERROR` for an unsupported `Content-Type`, 415 `INVALID_CONTENT_TYPE_ERROR` for a
  missing or malformed one). The option is honoured only by the bundled `Environment` implementation.

### Changed
- The environment (`_jsonapi` request attribute) is designed for a single request: reusing one environment object for
  several `parseRequest()` calls is not supported, and a decoder set manually before the call is never reset.

## 3.3.0 - 2026-11-14

### Changed
- Missing or empty `Accept` header is treated as `*/*`; missing `Content-Type` is not checked for requests without body.
  Malformed headers now result in `JsonApiException` (415 for `Content-Type` of requests with body, 406 for `Accept`)
  instead of `InvalidArgumentException`.
- Unsupported `Accept` now results in HTTP 406 (was 415).
- A request with a non-empty body and no `Content-Type` header is rejected with HTTP 415, even if the endpoint has no
  `bodyType` configured (so the body is never parsed).
- A malformed `Content-Type` is now reported as `HeadersChecker::INVALID_CONTENT_TYPE_ERROR` with HTTP status 415
  (previously the request failed with HTTP 500).
- For requests without body an unsupported or malformed `Content-Type` is ignored (no 415); a supported one still
  selects the decoder, so an empty body on an endpoint with `bodyType` still fails with HTTP 400 as before.
- `EnvironmentInterface::setDecoder()` is called only when a decoder was selected; `Environment::getDecoder()` returns
  `null` by default.
- `HeadersChecker::checkHeaders()` accepts `?MediaTypeInterface $contentType`; `null` skips the content type check.
