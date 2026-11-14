# Changelog

## Unreleased

### Changed
- Missing or empty `Accept` header is treated as `*/*`; missing `Content-Type` is not checked for requests without body.
  Malformed headers now result in `JsonApiException` (415 for `Content-Type`, 406 for `Accept`) instead of
  `InvalidArgumentException`.
- Unsupported `Accept` now results in HTTP 406 (was 415).
- A request with a non-empty body and no `Content-Type` header is rejected with HTTP 415, even if the endpoint has no
  `bodyType` configured (so the body is never parsed).
- `HeadersChecker::INVALID_CONTENT_TYPE_ERROR` is now used with HTTP status 415 (previously the error meant 400).
  Clients that match on this error code will see a different HTTP status.
- `HeadersChecker::checkHeaders()` accepts `?MediaTypeInterface $contentType`; `null` skips the content type check.
