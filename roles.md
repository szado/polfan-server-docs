# Role

Rola jest podzbiorem użytkowników w przestrzeni mogącym przypisywać im uprawnienia i atrybuty.

## Tworzenie roli

Komenda `CreateRole` umożliwia utworzenie nowej roli w przestrzeni.

W przypadku powodzenia, wszyscy członkowie przestrzeni otrzymają zdarzenie `NewRole`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `CreateRole`

Żądanie utworzenia nowej wiadomości.

| Pole        | Typ             | Opis                                                |
|-------------|-----------------|-----------------------------------------------------|
| `id`        | `UUID`          | wygenerowany przez klienta identyfikator nowej roli |
| `spaceId`   | `UUID`          | ID przestrzeni                                      |
| `basicData` | `RoleBasicData` | podstawowe informacje o roli                        |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                         |
|------------------------------|----------------------------------------------|
| `SpaceNotFoundException`     | przestrzeń nie istnieje                      |
| `RoleExistsAlreadyException` | rola o podanym ID istnieje już w przestrzeni |

## Usuwanie roli

Komenda `DeleteRole` umożliwia usunięcie roli.

W przypadku powodzenia wszyscy członkowie przestrzeni otrzymają zdarzenie `RoleDeleted`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `DeleteRole`

Żądanie usunięcia roli.

| Pole      | Typ    | Opis                      |
|-----------|--------|---------------------------|
| `id`      | `UUID` | identyfikator roli        |
| `spaceId` | `UUID` | identyfikator przestrzeni |

### `RoleDeleted`

Zdarzenie informujące o usunięciu roli. Wszyscy użytkownicy, którzy ją posiadali, utracili ją.

| Pole      | Typ    | Opis                         |
|-----------|--------|------------------------------|
| `id`      | `UUID` | identyfikator usuniętej roli |
| `spaceId` | `UUID` | identyfikator przestrzeni    |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                    |
|--------------------------|-------------------------|
| `SpaceNotFoundException` | przestrzeń nie istnieje |
| `RoleNotFoundException`  | rola nie istnieje       |

## Przyznawanie roli członkowi

Komenda `AddMemberRole` umożliwia przyznanie roli członkowi przestrzeni.

W przypadku powodzenia wszyscy członkowie w pokoju otrzymają zdarzenie `SpaceMemberUpdate`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `AddMemberRole`

Żądanie przyznania roli użytkownikowi.

| Pole      | Typ    | Opis           |
|-----------|--------|----------------|
| `roleId`  | `UUID` | ID roli        |
| `userId`  | `UUID` | ID użytkownika |
| `spaceId` | `UUID` | ID przestrzeni |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                                                                |
|------------------------------|-------------------------------------------------------------------------------------|
| `UserNotFoundException`      | użytkownik nie istnieje w przestrzeni (nadający rolę lub ten któremu jest nadawana) |
| `SpaceNotFoundException`     | przestrzeń nie istnieje                                                             |
| `RoleNotFoundException`      | rola nie istnieje                                                                   |
| `RoleExistsAlreadyException` | użytkownik posiada już tę rolę                                                      |

## Odbieranie roli członkowi

Komenda `DeleteMemberRole` umożliwia odebranie roli użytkownikowi.

W przypadku powodzenia, wszyscy członkowie przestrzeni otrzymają zdarzenie `SpaceMemberUpdate`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `DeleteMemberRole`

Żądanie odebrania roli użytkownikowi.

| Pole      | Typ    | Opis           |
|-----------|--------|----------------|
| `roleId`  | `UUID` | ID roli        |
| `userId`  | `UUID` | ID użytkownika |
| `spaceId` | `UUID` | ID przestrzeni |

### `RoleDeleted`

Zdarzenie informujące o usunięciu roli. Wszyscy użytkownicy którzy ją posiadali, utracili ją.

| Pole      | Typ    | Opis                         |
|-----------|--------|------------------------------|
| `id`      | `UUID` | identyfikator usuniętej roli |
| `spaceId` | `UUID` | identyfikator przestrzeni    |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                                                                                    |
|--------------------------|-----------------------------------------------------------------------------------------|
| `UserNotFoundException`  | użytkownik nie istnieje w przestrzeni (odbierający rolę lub ten któremu jest odbierana) |
| `SpaceNotFoundException` | przestrzeń nie istnieje                                                                 |
| `RoleNotFoundException`  | rola nie istnieje lub nie jest nadana                                                   |
