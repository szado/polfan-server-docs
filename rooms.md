# Pokoje

Pokój jest podzbiorem użytkowników i powiązanych ustawień w przestrzeni. Zawiera tematy w ramach których odbywa się wymiana wiadomości.

## Wejście do pokoju

Komenda `JoinRoom` umożliwia wejście do pokoju. 

W przypadku powodzenia klient dołączający otrzyma zdarzenie `RoomJoined`, natomiast użytkownicy będący już w pokoju`RoomMemberJoined`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `JoinRoom`

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

{payload-example JoinRoom}

#### `RoomJoined`

| Pole   | Typ    | Opis                                                |
|--------|--------|-----------------------------------------------------|
| `room` | `Room` | informacje o pokoju do którego nastąpiło dołączenie |

#### `Room`

| Pole          | Typ                          | Opis                                  |
|---------------|------------------------------|---------------------------------------|
| `id`          | `UUID`                       | ID pokoju                             |
| `spaceId`     | `UUID`                       | ID przestrzeni do której należy pokój |
| `name`        | `string`                     | nazwa pokoju                          |
| `description` | `string`                     | opis pokoju                           |
| `topics`      | [`Topic[]`](topics.md#topic) | lista tematów w pokoju                |

#### `RoomMember`

| Pole   | Typ                          | Opis                                                                       |
|--------|------------------------------|----------------------------------------------------------------------------|
| `user` | [`User`](connection.md#user) | dane użytkownika (zobacz: [członkowie przestrzeni](spaces.md#spacemember)) |

#### `RoomMemberJoined`

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

#### `LeaveRoom`

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

{payload-example LeaveRoom}

#### `RoomLeft`

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

#### `RoomMemberLeft`

| Pole     | Typ      | Opis           |
|----------|----------|----------------|
| `userId` | `string` | ID użytkownika |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                        |
|-------------------------|-----------------------------|
| `RoomNotFoundException` | pokój nie istnieje          |
| `UserNotFoundException` | użytkownika nie ma w pokoju |

## Lista członków pokoju

Komenda `GetRoomMembers` umożliwia pobranie listy członków należących do pokoju.

W odpowiedzi serwer wyśle zdarzenie `RoomMembers`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `GetRoomMembers`

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

{payload-example GetRoomMembers}

#### `RoomMembers`

| Pole      | Typ                                   | Opis           |
|-----------|---------------------------------------|----------------|
| `members` | [`RoomMember[]`](rooms.md#roommember) | lista członków |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                                 |
|--------------------------|--------------------------------------|
| `UserNotFoundException`  | użytkownik nie znajduje się w pokoju |

## Tworzenie pokoju

Komenda `CreateRoom` umożliwia utworzenie nowego pokoju. Aby utworzyć nowy pokój w przestrzeni, użytkownik musi być jej członkiem.

W przypadku powodzenia wszyscy użytkownicy obecni w przestrzeni otrzymają zdarzenie `NewRoom`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `CreateRoom`

| Pole          | Typ      | Opis                                           |
|---------------|----------|------------------------------------------------|
| `spaceId`     | `UUID`   | ID przestrzeni w której ma być utworzony pokój |
| `name`        | `string` | nazwa pokoju                                   |
| `description` | `string` | opis pokoju                                    |

{payload-example CreateRoom}

#### `NewRoom`

| Pole      | Typ                                   | Opis                                           |
|-----------|---------------------------------------|------------------------------------------------|
| `spaceId` | `UUID`                                | id przestrzeni w której utworzony został pokój |
| `summary` | [`RoomSummary`](rooms.md#roomsummary) | uproszczony obiekt pokoju                      |

#### `RoomSummary`

| Pole          | Typ      | Opis         |
|---------------|----------|--------------|
| `id`          | `UUID`   | ID pokoju    |
| `name`        | `string` | nazwa pokoju |
| `description` | `string` | opis pokoju  |

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

W przypadku powodzenia klient i wszyscy członkowie otrzymają zdarzenie `RoomDeleted`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `DeleteRoom`

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

{payload-example DeleteRoom}

#### `RoomDeleted`

| Pole | Typ    | Opis                            |
|------|--------|---------------------------------|
| `id` | `UUID` | identyfikator usuniętego pokoju |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis               |
|-------------------------|--------------------|
| `RoomNotFoundException` | pokój nie istnieje |
