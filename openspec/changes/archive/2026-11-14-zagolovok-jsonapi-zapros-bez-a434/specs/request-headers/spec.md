## ADDED Requirements

### Requirement: Отсутствующий Accept трактуется как */*
Если заголовок `Accept` отсутствует или пуст, `JsonApiService` SHALL считать его равным `*/*` (RFC 7231) и выбирать зарегистрированный энкодер, не выбрасывая исключений.

#### Scenario: GET без Accept и Content-Type
- **WHEN** приходит GET-запрос без заголовков `Accept` и `Content-Type` и без тела, а в матчере зарегистрирован энкодер `application/vnd.json+api`
- **THEN** `parseRequest()` успешно возвращает запрос, а в окружении выставлены энкодер и `encoderMediaType` `application/vnd.json+api`

#### Scenario: Пустой Accept
- **WHEN** приходит запрос с заголовком `Accept: ` (пустое значение)
- **THEN** запрос обрабатывается так же, как с `Accept: */*`

### Requirement: Content-Type проверяется только при наличии тела
Если заголовок `Content-Type` отсутствует, `JsonApiService` MUST NOT разбирать и проверять его при пустом теле запроса. При непустом теле без `Content-Type` сервис MUST выбросить `JsonApiException` с HTTP-кодом 415. Тело запроса MUST NOT декодироваться, если декодер не выбран.

#### Scenario: GET без тела и без Content-Type
- **WHEN** приходит GET-запрос без `Content-Type` и без тела
- **THEN** проверка Content-Type пропускается и запрос разбирается без ошибки, тело равно `null`

#### Scenario: POST с телом без Content-Type
- **WHEN** приходит POST-запрос с непустым телом и без заголовка `Content-Type`
- **THEN** выбрасывается `JsonApiException` с HTTP-кодом 415

### Requirement: Некорректные заголовки дают ошибки JSON:API
Синтаксически некорректные или неподдерживаемые значения заголовков MUST приводить к `JsonApiException`, а не к другим исключениям: для `Content-Type` — с HTTP-кодом 415, для `Accept` — с HTTP-кодом 406.

#### Scenario: Неподдерживаемый Accept
- **WHEN** приходит запрос с `Accept: text/html`, а зарегистрирован только энкодер `application/vnd.json+api`
- **THEN** выбрасывается `JsonApiException` с HTTP-кодом 406

#### Scenario: Синтаксически некорректный Accept
- **WHEN** приходит запрос с `Accept: garbage`
- **THEN** выбрасывается `JsonApiException` с HTTP-кодом 406

#### Scenario: Синтаксически некорректный Content-Type
- **WHEN** приходит запрос с непустым телом и `Content-Type: garbage`
- **THEN** выбрасывается `JsonApiException` с HTTP-кодом 415

#### Scenario: Неподдерживаемый Content-Type
- **WHEN** приходит запрос с телом и `Content-Type: text/plain`, а зарегистрирован только декодер `application/vnd.json+api`
- **THEN** выбрасывается `JsonApiException` с HTTP-кодом 415

#### Scenario: Корректные заголовки
- **WHEN** приходит запрос с `Content-Type: application/vnd.json+api` и `Accept: application/vnd.json+api`
- **THEN** запрос разбирается как раньше, выбраны зарегистрированные декодер и энкодер
