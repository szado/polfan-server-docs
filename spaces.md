# Przestrzenie

Przestrzeń to wyizolowany zbiór użytkowników, pokoi i powiązanych ustawień.

## Wejście do przestrzeni

Komenda `JoinSpace` umożliwia wejście do przestrzeni. 

W przypadku powodzenia klient dołączający otrzyma zdarzenie `SpaceJoined`, natomiast użytkownicy będący już w przestrzeni `SpaceMemberJoined`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `JoinSpace`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

{payload-example JoinSpace}

#### `SpaceJoined`

| Pole    | Typ                        | Opis                                      |
|---------|----------------------------|-------------------------------------------|
| `space` | [`Space`](spaces.md#space) | przestrzeń do której nastąpiło dołączenie |

#### `Space`

| Pole    | Typ                       | Opis                       |
|---------|---------------------------|----------------------------|
| `id`    | `UUID`                    | identyfikator przestrzeni  |
| `name`  | `string`                  | nazwa przestrzeni          |
| `roles` | [`Role[]`](roles.md#role) | tablica zdefiniowanych ról |

#### `SpaceMember`

| Pole            | Typ                          | Opis                        |
|-----------------|------------------------------|-----------------------------|
| `user`          | [`User`](connection.md#user) | obiekt z danymi użytkownika |
| `roles`         | `string[]`                   | tablica ID ról              |

#### `SpaceMemberJoined`

| Pole     | Typ                                    | Opis                       |
|----------|----------------------------------------|----------------------------|
| `member` | [`SpaceMember`](spaces.md#spacemember) | informacje o nowym członku |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                  |
|------------------------------|---------------------------------------|
| `SpaceNotFoundException`     | przestrzeń nie istnieje               |
| `UserExistsAlreadyException` | użytkownik istnieje już w przestrzeni |

## Wyjście z przestrzeni

Komenda `LeaveSpace` umożliwia opuszczenie przestrzeni. 

W przypadku powodzenia klient dołączający otrzyma zdarzenie `SpaceLeft`, natomiast pozostali użytkownicy w przestrzeni `SpaceMemberLeft`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `LeaveSpace`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

{payload-example LeaveSpace}

#### `SpaceLeft`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

#### `SpaceMemberLeft`

| Pole     | Typ      | Opis           |
|----------|----------|----------------|
| `userId` | `string` | ID użytkownika |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                                  |
|--------------------------|---------------------------------------|
| `SpaceNotFoundException` | przestrzeń nie istnieje               |
| `UserNotFoundException`  | użytkownik nie istnieje w przestrzeni |

## Lista członków przestrzeni

Komenda `GetSpaceMembers` umożliwia pobranie listy członków należących do przestrzeni.

W odpowiedzi serwer wyśle zdarzenie `SpaceMembers`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `GetSpaceMembers`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

{payload-example GetSpaceMembers}

#### `SpaceMembers`

| Pole      | Typ                                      | Opis           |
|-----------|------------------------------------------|----------------|
| `members` | [`SpaceMember[]`](spaces.md#spacemember) | lista członków |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                                 |
|--------------------------|--------------------------------------|
| `UserNotFoundException`  | użytkownik nie należy do przestrzeni |

## Lista pokojów w przestrzeni

Komenda `GetSpaceRooms` umożliwia pobranie listy pokojów należących do przestrzeni.

W odpowiedzi serwer wyśle zdarzenie `SpaceRooms`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `GetSpaceRooms`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

{payload-example GetSpaceRooms}

#### `SpaceRooms`

| Pole        | Typ                                     | Opis                                      |
|-------------|-----------------------------------------|-------------------------------------------|
| `summaries` | [`RoomSummary[]`](rooms.md#roomsummary) | lista uproszczonych informacji o pokojach |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                                 |
|-------------------------|--------------------------------------|
| `UserNotFoundException` | użytkownik nie należy do przestrzeni |

## Tworzenie przestrzeni

Komenda `CreateSpace` umożliwia utworzenie nowej przestrzeni. 

W przypadku powodzenia użytkownik zostanie dołączony do nowej przestrzeni i otrzyma zdarzenie [`SpaceJoined`](#spacejoined).

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `CreateSpace`

| Pole   | Typ      | Opis              |
|--------|----------|-------------------|
| `name` | `string` | nazwa przestrzeni |

{payload-example CreateSpace}

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                           | Opis                               |
|-------------------------------|------------------------------------|
| `AccessDeniedException`       | brak uprawnień                     |
| `SpaceExistsAlreadyException` | przestrzeń o takim ID już istnieje |

## Usuwanie przestrzeni

Komenda `DeleteSpace` umożliwia usunięcie przestrzeni. 

W przypadku powodzenia klient i wszyscy członkowie otrzymają zdarzenie `SpaceDeleted`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `DeleteSpace`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

{payload-example DeleteSpace}

#### `SpaceDeleted`

| Pole | Typ    | Opis                                |
|------|--------|-------------------------------------|
| `id` | `UUID` | identyfikator usuniętej przestrzeni |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                    |
|--------------------------|-------------------------|
| `AccessDeniedException`  | brak uprawnień          |
| `SpaceNotFoundException` | przestrzeń nie istnieje |
