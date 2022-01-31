# Pokoje

Pokój jest podzbiorem użytkowników i powiązanych ustawień w przestrzeni. Zawiera tematy w ramach których odbywa się wymiana wiadomości.

## Wejście do pokoju

Komenda `JoinRoom` umożliwia wejście do pokoju. 

W przypadku powodzenia, klient dołączający otrzyma zdarzenie `RoomJoined`, natomiast użytkownicy będący już w pokoju`RoomMemberJoined`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `JoinRoom`

Żądanie wejścia do pokoju.

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

### `RoomJoined`

Zdarzenie potwierdzające wejście do pokoju.

| Pole   | Typ    | Opis                                                |
|--------|--------|-----------------------------------------------------|
| `room` | `Room` | informacje o pokoju do którego nastąpiło dołączenie |

### `RoomMemberJoined`

Zdarzenie informujące o dołączeniu nowego członka do pokoju.

| Pole     | Typ          | Opis                       |
|----------|--------------|----------------------------|
| `member` | `RoomMember` | informacje o nowym członku |

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

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `LeaveRoom`

Żądanie wyjścia z pokoju.

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

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

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `CreateRoom`

Żądanie utworzenia nowego pokoju.

| Pole        | Typ             | Opis                                            |
|-------------|-----------------|-------------------------------------------------|
| `id`        | `UUID`          | wygenerowany przez klienta identyfikator pokoju |
| `spaceId`   | `UUID`          | ID przestrzeni w której ma być utworzony pokój  |
| `basicData` | `RoomBasicData` | podstawowe informacje od przestrzeni            |

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

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `DeleteRoom`

Żądanie usunięcia pokoju.

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

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
