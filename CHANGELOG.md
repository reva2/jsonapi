# Changelog

## Unreleased

### Changed
- Missing or empty `Accept` header is treated as `*/*`; missing `Content-Type` is not checked for requests without body.
  Malformed headers now result in `JsonApiException` (415 for `Content-Type`, 406 for `Accept`) instead of
  `InvalidArgumentException`.
- Unsupported `Accept` now results in HTTP 406 (was 415).
- A request with a non-empty body and no `Content-Type` header is rejected with HTTP 415, even if the endpoint has no
  `bodyType` configured (so the body is never parsed).
- A malformed `Content-Type` is now reported as `HeadersChecker::INVALID_CONTENT_TYPE_ERROR` with HTTP status 415
  (previously the request failed with HTTP 500).
- For requests without body an unsupported or malformed `Content-Type` is ignored (no 415); a supported one still
  selects the decoder, so an empty body on an endpoint with `bodyType` still fails with HTTP 400 as before.
- **BC break**: `EnvironmentInterface::setDecoder()` now accepts `?DecoderInterface`; custom implementations must
  update the signature.
- `HeadersChecker::checkHeaders()` accepts `?MediaTypeInterface $contentType`; `null` skips the content type check.
