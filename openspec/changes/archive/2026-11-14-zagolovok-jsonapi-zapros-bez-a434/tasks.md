## 1. HeadersChecker

- [x] 1.1 В `src/Http/Headers/HeadersChecker.php` изменить сигнатуру на `checkHeaders(?MediaTypeInterface $contentType, iterable $acceptedContentTypes)`; при `null` пропускать `checkContentTypeHeader()`.
- [x] 1.2 В `checkAcceptHeader()` выбрасывать `JsonApiException` с HTTP-кодом 406 (`HTTP_CODE_NOT_ACCEPTABLE`) и статусом ошибки 406 вместо 415.
- [x] 1.3 Добавить публичные статические фабрики или константы ошибок для «некорректного Content-Type» (415, `INVALID_CONTENT_TYPE_ERROR`) и «некорректного Accept» (406, новая константа `INVALID_ACCEPT_ERROR`), чтобы `JsonApiService` создавал исключения в одном стиле.

## 2. JsonApiService

- [x] 2.1 В `parseRequestHeaders()` читать `Accept`; если он отсутствует или после `trim()` пуст — использовать `*/*`.
- [x] 2.2 Оборачивать вызовы `parseAcceptHeader()` в `try/catch` (`Neomerx\JsonApi\Exceptions\InvalidArgumentException`/`\InvalidArgumentException`) и превращать ошибку в `JsonApiException` 406. Генератор `parseAcceptHeader()` материализовать (`iterator_to_array`/массив) внутри `try`, чтобы ошибка разбора не вылетала позже в `matchEncoder()`.
- [x] 2.3 Если `Content-Type` отсутствует или пуст: при пустом теле (`'' === $request->getContent()`) передавать `null` в `checkHeaders()`; при непустом теле — выбрасывать `JsonApiException` 415.
- [x] 2.4 Непустой `Content-Type` разбирать в `try/catch`, ошибку парсера превращать в `JsonApiException` 415.
- [x] 2.5 В `initializeEnvironment()`/`parseBody()` учитывать, что декодер может быть не выбран: если `getDecoder()` вернул `null`, `parseBody()` возвращает `null` без вызова декодера.

## 3. Тесты

- [x] 3.1 Переписать `tests/Http/Headers/HeadersCheckerTest.php` под новую сигнатуру: успешная проверка, `null` Content-Type (декодер не проверяется), неподдерживаемый Content-Type → 415, неподдерживаемый Accept → 406.
- [x] 3.2 В `tests/Services/JsonApiServiceTest.php` добавить: GET без `Accept` и `Content-Type` → запрос разобран, энкодер выставлен, `encoderMediaType` = `application/vnd.json+api`.
- [x] 3.3 Добавить: GET с пустым `Accept` → ведёт себя как `*/*`.
- [x] 3.4 Добавить: POST с телом без `Content-Type` → `JsonApiException` с кодом 415.
- [x] 3.5 Добавить: `Accept: text/html` → `JsonApiException` с кодом 406; `Accept: garbage` (без `/`) → 406; `Content-Type: garbage` при наличии тела → 415.
- [x] 3.6 Убедиться, что существующие тесты (`shouldParseRequest`, `shouldThrowOnInvalidMediaTypes` и др.) проходят без изменений: `vendor/bin/phpunit tests/Services tests/Http/Headers`, затем полный `vendor/bin/phpunit`.

## 4. Проверка

- [x] 4.1 Прогнать `vendor/bin/phpcs --standard=PSR2 src` для изменённых файлов.
- [ ] 4.2 (Вне репозитория) Интеграционно проверить в api-server: запрос без заголовков возвращает не 500.
