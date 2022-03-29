# Tematy

Temat jest elementem pokoju, w ramach którego wysyłane są wiadomości oraz zapisywana jest historia rozmowy.

## Tworzenie wiadomości

Komenda `CreateMessage` umożliwia utworzenie nowej wiadomości w temacie.

W przypadku powodzenia wszyscy członkowie w pokoju otrzymają zdarzenie `NewMessage`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

### `CreateMessage`

Żądanie utworzenia nowej wiadomości.

| Pole      | Typ                            | Opis                                                        |
|-----------|--------------------------------|-------------------------------------------------------------|
| `topicId` | `UUID`                         | identyfikator tematu w którym wiadomość ma być opublikowana |
| `message` | [`Message`](topics.md#message) | obiekt wiadomości                                           |

{payload-example CreateMessage}

### `NewMessage`

Zdarzenie informujące o nowej wiadomości w temacie.

| Pole      | Typ                            | Opis                                                         |
|-----------|--------------------------------|--------------------------------------------------------------|
| `topicId` | `UUID`                         | identyfikator tematu w którym wiadomość została opublikowana |
| `userId`  | `string`                       | identyfikator nadawcy                                        |
| `message` | [`Message`](topics.md#message) | obiekt wiadomości                                            |

#### `Message`

| Pole   | Typ      | Opis             |
|--------|----------|------------------|
| `text` | `string` | treść wiadomości |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                             | Opis                                                                     |
|---------------------------------|--------------------------------------------------------------------------|
| `TopicNotFoundException`        | temat o takim ID nie istnieje                                            |
| `UserNotFoundException`         | użytkownik nie jest członkiem pokoju w którym chce opublikować wiadomość |
| `MessageExistsAlreadyException` | wiadomość o takim ID już istnieje w temacie                              |

## Tworzenie tematu

Komenda `CreateTopic` umożliwia utworzenie nowego tematu. Aby utworzyć nowy temat w pokoju, użytkownik musi być jego członkiem.

W przypadku powodzenia wszyscy członkowie w pokoju otrzymają zdarzenie `NewTopic`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

### `CreateTopic`

Żądanie utworzenia nowego tematu.

| Pole        | Typ                                          | Opis                                            |
|-------------|----------------------------------------------|-------------------------------------------------|
| `roomId`    | `UUID`                                       | ID pokoju w którym ma być utworzony temat       |
| `basicData` | [`TopicBasicData`](topics.md#topicbasicdata) | podstawowe informacje o temacie                 |

{payload-example CreateTopic}

### `NewTopic`

Zdarzenie informujące o utworzeniu nowego tematu w pokoju.

| Pole     | Typ                        | Opis       |
|----------|----------------------------|------------|
| `roomId` | `UUID`                     | ID pokoju  |
| `topic`  | [`Topic`](topics.md#topic) | nowy temat |

#### `Topic`

| Pole        | Typ                                          | Opis                                  |
|-------------|----------------------------------------------|---------------------------------------|
| `id`        | `UUID`                                       | ID pokoju                             |
| `basicData` | [`TopicBasicData`](topics.md#topicbasicdata) | podstawowe informacje o pokoju        |

#### `TopicBasicData`

| Pole          | Typ      | Opis         |
|---------------|----------|--------------|
| `name`        | `string` | nazwa tematu |
| `description` | `string` | opis tematu  |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                           | Opis                                                              |
|-------------------------------|-------------------------------------------------------------------|
| `RoomNotFoundException`       | pokój o takim ID nie istnieje                                     |
| `UserNotFoundException`       | użytkownik nie jest członkiem pokoju w którym chce utworzyć temat |
| `TopicExistsAlreadyException` | temat o takim ID już istnieje                                     |

## Usuwanie tematu

Komenda `DeleteTopic` umożliwia usunięcie tematu wraz z historią wiadomości. 

W przypadku powodzenia wszyscy członkowie pokoju otrzymają zdarzenie `TopicDeleted`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

### `DeleteTopic`

Żądanie usunięcia tematu.

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator tematu |

{payload-example DeleteTopic}

### `TopicDeleted`

Zdarzenie informujące o usunięciu tematu.

| Pole | Typ    | Opis                            |
|------|--------|---------------------------------|
| `id` | `UUID` | identyfikator usuniętego tematu |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis               |
|--------------------------|--------------------|
| `TopicNotFoundException` | temat nie istnieje |
