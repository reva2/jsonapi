# Обработка отсутствующих заголовков Content-Type и Accept

## Why
Если клиент не передаёт `Content-Type` и/или `Accept`, `JsonApiService::parseRequestHeaders()` отдаёт пустые строки в `HeaderParametersParser` из `neomerx/json-api`. Парсер бросает `InvalidArgumentException`, который не является `JsonApiException`, поэтому приложение отвечает HTTP 500 вместо корректного ответа в формате JSON:API.

## What Changes
- Отсутствующий или пустой `Accept` считается равным `*/*` (RFC 7231): выбирается первый зарегистрированный энкодер, запрос не падает.
- Отсутствующий `Content-Type` не разбирается и не проверяется, если у запроса нет тела (например, GET). Если тело есть, а `Content-Type` нет, выбрасывается `JsonApiException` с кодом 415.
- Непустой, но синтаксически некорректный `Content-Type` (ошибка парсера neomerx) превращается в `JsonApiException` с кодом 415, а некорректный `Accept` — в `JsonApiException` с кодом 406. Исключение парсера больше не уходит наружу.
- Неподдерживаемый `Accept` (ни один энкодер не подходит, например `text/html`) даёт код 406 вместо прежнего 415. Неподдерживаемый `Content-Type` по-прежнему даёт 415.
- `HeadersChecker::checkHeaders()` принимает `?MediaTypeInterface $contentType`: при `null` проверка декодера пропускается.
- Если декодер не выбран (нет тела и нет `Content-Type`), `JsonApiService::parseBody()` возвращает `null` и не вызывает декодер.
- Устаревший `tests/Http/Headers/HeadersCheckerTest.php` (вызывает `checkHeaders()` с одним аргументом и мокает интерфейсы, которых нет в neomerx 4.x) переписывается под текущую сигнатуру.

## Impact
- Спецификации: новая capability `request-headers`.
- Код:
  - `src/Services/JsonApiService.php` — `parseRequestHeaders()`, `initializeEnvironment()`, `parseBody()`.
  - `src/Http/Headers/HeadersChecker.php` — nullable `Content-Type`, код 406 для `Accept`, новые константы ошибок для некорректных заголовков.
- Тесты:
  - `tests/Services/JsonApiServiceTest.php` — новые сценарии без заголовков и с некорректными заголовками.
  - `tests/Http/Headers/HeadersCheckerTest.php` — переписать под `checkHeaders(?MediaTypeInterface, iterable)`.
- Поведение для клиентов: ответ на неподдерживаемый `Accept` меняется с 415 на 406 (это исправление по спецификации JSON:API). Связанный пакет `reva2/jsonapi-bundle` не меняется: его `ExceptionListener` уже обрабатывает `JsonApiException`.

## Invariants
- Запрос с корректными `Content-Type: application/vnd.json+api` и `Accept: application/vnd.json+api` разбирается так же, как раньше: тот же декодер, энкодер и `encoderMediaType` в окружении.
- Ни одно значение заголовков `Content-Type`/`Accept` (отсутствующее, пустое, некорректное, неподдерживаемое) не приводит к исключению, отличному от `JsonApiException`.
- GET без `Accept` и `Content-Type` успешно разбирается, а в окружении выставлены энкодер и `encoderMediaType` (ответ можно закодировать).
- Тело запроса никогда не декодируется без проверенного `Content-Type`: непустое тело без `Content-Type` или с неподдерживаемым `Content-Type` даёт 415.
- Неподдерживаемый или некорректный `Accept` всегда даёт 406, неподдерживаемый или некорректный `Content-Type` — 415.
- `Accept` с `q=0` для всех типов по-прежнему считается неприемлемым и даёт 406, а не подменяется на `*/*`.
- Ошибка конфигурации матчера (`Invalid media type ... specified`) по-прежнему выбрасывается как `InvalidArgumentException`: это ошибка разработчика, а не клиента.

## Checks
- `vendor/bin/phpunit tests/Services tests/Http/Headers`
