# Przestrzenie

Przestrzeń to wyizolowany zbiór użytkowników, pokoi i powiązanych ustawień.

## Wejście do przestrzeni

Komenda `JoinSpace` umożliwia wejście do przestrzeni. 

W przypadku powodzenia, klient dołączający otrzyma zdarzenie `SpaceJoined`, natomiast użytkownicy będący już w przestrzeni `SpaceMemberJoined`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `JoinSpace`

Żądanie wejścia do przestrzeni.

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

### `SpaceJoined`

Zdarzenie potwierdzające wejście do przestrzeni.

| Pole            | Typ              | Opis                                        |
|-----------------|------------------|---------------------------------------------|
| `id`            | `UUID`           | identyfikator przestrzeni                   |
| `basicData`     | `SpaceBasicData` | podstawowe dane przestrzeni                 |
| `roles`         | `Role[]`         | tablica ról                                 |
| `members`       | `SpaceMember[]`  | tablica użytkowników obecnych w przestrzeni |
| `roomSummaries` | `RoomSummary[]`  | uproszczona lista pokojów w przestrzeni     |

### `SpaceMemberJoined`

Zdarzenie informujące o dołączeniu nowego członka do przestrzeni.

| Pole     | Typ           | Opis                       |
|----------|---------------|----------------------------|
| `member` | `SpaceMember` | informacje o nowym członku |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                  |
|------------------------------|---------------------------------------|
| `SpaceNotFoundException`     | przestrzeń nie istnieje               |
| `UserExistsAlreadyException` | użytkownik istnieje już w przestrzeni |

## Wyjście z przestrzeni

Komenda `LeaveSpace` umożliwia opuszczenie przestrzeni. 

W przypadku powodzenia, klient dołączający otrzyma zdarzenie `SpaceLeft`, natomiast pozostali użytkownicy w przestrzeni `SpaceMemberLeft`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `LeaveSpace`

Żądanie opuszczenia przestrzeni.

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

### `SpaceLeft`

Zdarzenie potwierdzające opuszczenie przestrzeni.

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

### `SpaceMemberLeft`

Zdarzenie informujące o opuszczeniu przestrzeni przez członka.

| Pole     | Typ      | Opis           |
|----------|----------|----------------|
| `userId` | `string` | ID użytkownika |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                                  |
|--------------------------|---------------------------------------|
| `SpaceNotFoundException` | przestrzeń nie istnieje               |
| `UserNotFoundException`  | użytkownik nie istnieje w przestrzeni |

## Tworzenie przestrzeni

Komenda `CreateSpace` umożliwia utworzenie nowej przestrzeni. 

W przypadku powodzenia użytkownik zostanie dołączony do nowej przestrzeni i otrzyma zdarzenie [`SpaceJoined`](#spacejoined).

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `CreateSpace`

Żądanie utworzenia nowej przestrzeni.

| Pole        | Typ              | Opis                                                 |
|-------------|------------------|------------------------------------------------------|
| `id`        | `UUID`           | wygenerowany przez klienta identyfikator przestrzeni |
| `basicData` | `SpaceBasicData` | podstawowe informacje od przestrzeni                 |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                           | Opis                               |
|-------------------------------|------------------------------------|
| `AccessDeniedException`       | brak uprawnień                     |
| `SpaceExistsAlreadyException` | przestrzeń o takim ID już istnieje |

## Usuwanie przestrzeni

Komenda `DeleteSpace` umożliwia usunięcie przestrzeni. 

W przypadku powodzenia klient i wszyscy członkowie otrzymają zdarzenie `SpaceDeleted`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `DeleteSpace`

Żądanie usunięcia przestrzeni.

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

### `SpaceDeleted`

Zdarzenie informujące o usunięciu przestrzeni.

| Pole | Typ    | Opis                                |
|------|--------|-------------------------------------|
| `id` | `UUID` | identyfikator usuniętej przestrzeni |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                    |
|--------------------------|-------------------------|
| `AccessDeniedException`  | brak uprawnień          |
| `SpaceNotFoundException` | przestrzeń nie istnieje |
