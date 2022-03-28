# Pokoje

Pokój jest podzbiorem użytkowników i powiązanych ustawień w przestrzeni. Zawiera tematy w ramach których odbywa się wymiana wiadomości.

## Wejście do pokoju

Komenda `JoinRoom` umożliwia wejście do pokoju. 

W przypadku powodzenia klient dołączający otrzyma zdarzenie `RoomJoined`, natomiast użytkownicy będący już w pokoju`RoomMemberJoined`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

### `JoinRoom`

Żądanie wejścia do pokoju.

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

{payload-example JoinRoom}

### `RoomJoined`

Zdarzenie potwierdzające wejście do pokoju.

| Pole   | Typ    | Opis                                                |
|--------|--------|-----------------------------------------------------|
| `room` | `Room` | informacje o pokoju do którego nastąpiło dołączenie |

#### `Room`

| Pole        | Typ                                       | Opis                                  |
|-------------|-------------------------------------------|---------------------------------------|
| `id`        | `UUID`                                    | ID pokoju                             |
| `spaceId`   | `UUID`                                    | ID przestrzeni do której należy pokój |
| `basicData` | [`RoomBasicData`](rooms.md#roombasicdata) | podstawowe informacje o pokoju        |
| `topics`    | [`Topic[]`](topics.md#topic)              | lista tematów w pokoju                |
| `members`   | [`RoomMember[]`](rooms.md#roommember)     | lista użytkowników w pokoju           |

#### `RoomBasicData`

| Pole          | Typ      | Opis         |
|---------------|----------|--------------|
| `name`        | `string` | nazwa pokoju |
| `description` | `string` | opis pokoju  |

#### `RoomMember`

| Pole          | Typ      | Opis                                                                         |
|---------------|----------|------------------------------------------------------------------------------|
| `userId`      | `string` | ID użytkownika (nawiązujący do [członka przestrzeni](spaces.md#spacemember)) |

### `RoomMemberJoined`

Zdarzenie informujące o dołączeniu nowego członka do pokoju.

| Pole     | Typ                                 | Opis                       |
|----------|-------------------------------------|----------------------------|
| `member` | [`RoomMember`](rooms.md#roommember) | informacje o nowym członku |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                                    |
|------------------------------|---------------------------------------------------------|
| `RoomNotFoundException`      | pokój nie istnieje                                      |
| `UserExistsAlreadyException` | użytkownik istnieje już w pokoju                        |
| `UserNotFoundException`      | użytkownika nie ma w przestrzeni do której należy pokój |

## Wyjście z pokoju

Komenda `LeaveRoom` umożliwia wyjście z pokoju. 

W przypadku powodzenia użytkownik opuszczający otrzyma zdarzenie `RoomLeft`, natomiast pozostali użytkownicy  w pokoju`RoomMemberLeft`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

### `LeaveRoom`

Żądanie wyjścia z pokoju.

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

{payload-example LeaveRoom}

### `RoomLeft`

Zdarzenie potwierdzające wyjście z pokoju.

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

### `RoomMemberLeft`

Zdarzenie informujące o dołączeniu nowego członka do pokoju.

| Pole     | Typ      | Opis           |
|----------|----------|----------------|
| `userId` | `string` | ID użytkownika |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                        |
|-------------------------|-----------------------------|
| `RoomNotFoundException` | pokój nie istnieje          |
| `UserNotFoundException` | użytkownika nie ma w pokoju |

## Tworzenie pokoju

Komenda `CreateRoom` umożliwia utworzenie nowego pokoju. Aby utworzyć nowy pokój w przestrzeni, użytkownik musi być jej członkiem.

W przypadku powodzenia wszyscy użytkownicy obecni w przestrzeni otrzymają zdarzenie `NewRoom`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

### `CreateRoom`

Żądanie utworzenia nowego pokoju.

| Pole        | Typ                                       | Opis                                            |
|-------------|-------------------------------------------|-------------------------------------------------|
| `id`        | `UUID`                                    | wygenerowany przez klienta identyfikator pokoju |
| `spaceId`   | `UUID`                                    | ID przestrzeni w której ma być utworzony pokój  |
| `basicData` | [`RoomBasicData`](rooms.md#roombasicdata) | podstawowe informacje od przestrzeni            |

{payload-example CreateRoom}

### `NewRoom`

Zdarzenie informujące o utworzeniu nowego pokoju w przestrzeni.

| Pole      | Typ                                   | Opis                                            |
|-----------|---------------------------------------|-------------------------------------------------|
| `summary` | [`RoomSummary`](rooms.md#roomsummary) | wygenerowany przez klienta identyfikator pokoju |

#### `RoomSummary`

| Pole        | Typ                                       | Opis                   |
|-------------|-------------------------------------------|------------------------|
| `id`        | `UUID`                                    | ID pokoju              |
| `basicData` | [`RoomBasicData`](rooms.md#roombasicdata) | podstawowe dane pokoju |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                                                   |
|------------------------------|------------------------------------------------------------------------|
| `AccessDeniedException`      | brak uprawnień                                                         |
| `SpaceNotFoundException`     | przestrzeń o takim ID nie istnieje                                     |
| `UserNotFoundException`      | użytkownik nie jest członkiem przestrzeni w której chce utworzyć pokój |
| `RoomExistsAlreadyException` | pokój o takim ID już istnieje                                          |

## Usuwanie pokoju

Komenda `DeleteRoom` umożliwia usunięcie pokoju. 

W przypadku powodzenia, klient i wszyscy członkowie otrzymają zdarzenie `RoomDeleted`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

### `DeleteRoom`

Żądanie usunięcia pokoju.

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

{payload-example DeleteRoom}

### `RoomDeleted`

Zdarzenie informujące o usunięciu pokoju.

| Pole | Typ    | Opis                            |
|------|--------|---------------------------------|
| `id` | `UUID` | identyfikator usuniętego pokoju |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis               |
|-------------------------|--------------------|
| `RoomNotFoundException` | pokój nie istnieje |
