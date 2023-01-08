# Tematy

Temat jest elementem pokoju, w ramach którego wysyłane są wiadomości oraz zapisywana jest historia rozmowy.

## Tworzenie wiadomości

Komenda `CreateMessage` umożliwia utworzenie nowej wiadomości w temacie.

W przypadku powodzenia wszyscy członkowie w pokoju (łącznie z nadawcą) otrzymają zdarzenie `NewMessage`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `CreateMessage`

| Pole      | Typ      | Opis                                                        |
|-----------|----------|-------------------------------------------------------------|
| `topicId` | `UUID`   | identyfikator tematu w którym wiadomość ma być opublikowana |
| `content` | `string` | treść wiadomości                                            |

{payload-example CreateMessage}

#### `NewMessage`

| Pole      | Typ                            | Opis                                                         |
|-----------|--------------------------------|--------------------------------------------------------------|
| `message` | [`Message`](topics.md#message) | obiekt wiadomości                                            |

#### `Message`

| Pole      | Typ                          | Opis                                      |
|-----------|------------------------------|-------------------------------------------|
| `id`      | `UUID`                       | id wiadomości                             |
| `author`  | [`User`](connection.md#user) | dane użytkownika - autora wiadomości      |
| `topicId` | `UUID`                       | id tematu w którym opublikowano wiadomość |
| `content` | `string`                     | treść wiadomości                          |

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

#### `CreateTopic`

| Pole          | Typ      | Opis                                      |
|---------------|----------|-------------------------------------------|
| `roomId`      | `UUID`   | ID pokoju w którym ma być utworzony temat |
| `name`        | `string` | nazwa tematu                              |
| `description` | `string` | opis tematu                               |

{payload-example CreateTopic}

#### `NewTopic`

| Pole     | Typ                        | Opis       |
|----------|----------------------------|------------|
| `roomId` | `UUID`                     | ID pokoju  |
| `topic`  | [`Topic`](topics.md#topic) | nowy temat |

#### `Topic`

| Pole          | Typ      | Opis         |
|---------------|----------|--------------|
| `id`          | `UUID`   | ID tematu    |
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

#### `DeleteTopic`

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator tematu |

{payload-example DeleteTopic}

#### `TopicDeleted`

| Pole | Typ    | Opis                            |
|------|--------|---------------------------------|
| `id` | `UUID` | identyfikator usuniętego tematu |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis               |
|--------------------------|--------------------|
| `TopicNotFoundException` | temat nie istnieje |
