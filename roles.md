# Role

Rola jest podzbiorem użytkowników w przestrzeni mogącym przypisywać im uprawnienia i atrybuty.

## Tworzenie roli

Komenda `CreateRole` umożliwia utworzenie nowej roli w przestrzeni.

W przypadku powodzenia wszyscy członkowie przestrzeni otrzymają zdarzenie `NewRole`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `CreateRole`

| Pole      | Typ                  | Opis                                    |
|-----------|----------------------|-----------------------------------------|
| `spaceId` | `UUID`               | ID przestrzeni                          |
| `name`    | `string`             | nazwa roli                              |
| `color`   | `string`&#124;`null` | kolor roli w formacie HEX (np. #ff0000) |

{payload-example CreateRole}

#### `NewRole`

| Pole      | Typ                     | Opis           |
|-----------|-------------------------|----------------|
| `spaceId` | `UUID`                  | ID przestrzeni |
| `role`    | [`Role`](roles.md#role) | nowy temat     |

#### `Role`

| Pole   | Typ                  | Opis       |
|--------|----------------------|------------|
| `id`   | `UUID`               | ID roli    |
| `name` | `string`             | nazwa roli |
| `name` | `string`&#124;`null` | kolor roli |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                         |
|------------------------------|----------------------------------------------|
| `SpaceNotFoundException`     | przestrzeń nie istnieje                      |
| `RoleExistsAlreadyException` | rola o podanym ID istnieje już w przestrzeni |

## Usuwanie roli

Komenda `DeleteRole` umożliwia usunięcie roli.

W przypadku powodzenia wszyscy członkowie przestrzeni otrzymają zdarzenie `RoleDeleted`, co również będzie znaczyło, że osoby, które posiadały rolę, utraciły ją.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `DeleteRole`

| Pole      | Typ    | Opis                      |
|-----------|--------|---------------------------|
| `roleId`  | `UUID` | identyfikator roli        |
| `spaceId` | `UUID` | identyfikator przestrzeni |

{payload-example DeleteRole}

#### `RoleDeleted`

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

Komenda `AssignRole` umożliwia przyznanie roli członkowi przestrzeni.

W przypadku powodzenia wszyscy członkowie w pokoju otrzymają zdarzenie `SpaceMemberUpdate`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `AssignRole`

| Pole      | Typ    | Opis           |
|-----------|--------|----------------|
| `roleId`  | `UUID` | ID roli        |
| `userId`  | `UUID` | ID użytkownika |
| `spaceId` | `UUID` | ID przestrzeni |

{payload-example AssignRole}

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                                                                |
|------------------------------|-------------------------------------------------------------------------------------|
| `UserNotFoundException`      | użytkownik nie istnieje w przestrzeni (nadający rolę lub ten któremu jest nadawana) |
| `SpaceNotFoundException`     | przestrzeń nie istnieje                                                             |
| `RoleNotFoundException`      | rola nie istnieje                                                                   |
| `RoleExistsAlreadyException` | użytkownik posiada już tę rolę                                                      |

## Odbieranie roli członkowi

Komenda `DeassignRole` umożliwia odebranie roli użytkownikowi.

W przypadku powodzenia wszyscy członkowie przestrzeni otrzymają zdarzenie `SpaceMemberUpdate`.

W przypadku błędu serwer wyśle zdarzenie `Error`.

#### `DeassignRole`

| Pole      | Typ    | Opis           |
|-----------|--------|----------------|
| `roleId`  | `UUID` | ID roli        |
| `userId`  | `UUID` | ID użytkownika |
| `spaceId` | `UUID` | ID przestrzeni |

{payload-example DeassignRole}

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                                                                                    |
|--------------------------|-----------------------------------------------------------------------------------------|
| `UserNotFoundException`  | użytkownik nie istnieje w przestrzeni (odbierający rolę lub ten któremu jest odbierana) |
| `SpaceNotFoundException` | przestrzeń nie istnieje                                                                 |
| `RoleNotFoundException`  | rola nie istnieje lub nie jest nadana                                                   |
