# Role

Rola to nazwany zbiór użytkowników w [przestrzeni](spaces.md), któremu można przypisać
[uprawnienia](permissions.md), kolor i priorytet. Role są podstawowym narzędziem porządkowania społeczności:
zamiast nadawać uprawnienia pojedynczym osobom, nadajesz je roli i zarządzasz jej składem.

Dla bota automatyzacyjnego role są też mechanizmem adresowania grup – jedną komendą można
[dodać do pokoju](rooms.md#wejście-do-pokoju) lub [zaprosić](rooms.md#zaproszenia) wszystkich posiadaczy roli.

## Model

* Role definiowane są **na poziomie przestrzeni** i widoczne w polu `roles` obiektu [`Space`](spaces.md#space).
* Rolę można przypisać użytkownikowi **w przestrzeni** albo **w konkretnym pokoju** – decyduje
  [lokalizacja](protocol.md#lokalizacja-chatlocation) przekazana w komendzie.
* Przypisanie w przestrzeni obowiązuje we wszystkich jej pokojach i ma pierwszeństwo przed przypisaniem
  pokojowym. Usunięcie roli na poziomie przestrzeni nie odbiera jej w pokojach, w których została nadana osobno.
* Przypisanie pokojowe jest niemożliwe w pokojach prywatnych (poza przestrzenią).
* **Priorytet** rozstrzyga, którą rolę pokazać przy pseudonimie (kolor) – wyższa wartość wygrywa. Przy
  [obliczaniu uprawnień](permissions.md#obliczanie-uprawnień) priorytet nie ma znaczenia.

#### `Role`

| Pole       | Typ                  | Opis                                            |
|------------|----------------------|-------------------------------------------------|
| `id`       | `UUID`               | identyfikator roli                              |
| `priority` | `int`                | priorytet; `0` zarezerwowane dla roli domyślnej |
| `name`     | `string`             | nazwa (1–50 znaków)                             |
| `color`    | `string`&#124;`null` | kolor w formacie HEX (np. `#ff0000`)            |

## Rola domyślna (`@everyone`)

Wraz z przestrzenią powstaje rola domyślna, którą otrzymuje **każdy** jej członek. Rządzą nią szczególne zasady:

* jej **ID jest równe ID przestrzeni** – rozpoznasz ją bez dodatkowego zapytania;
* nie można jej usunąć, odebrać użytkownikowi ani zmienić jej priorytetu (zawsze `0`);
* na warstwie przestrzeni definiuje wartości **wszystkich** uprawnień, przez co stanowi punkt odniesienia dla
  całej przestrzeni – patrz [uprawnienia](permissions.md#rola-domyślna-a-uprawnienia);
* nie jest wspierana tam, gdzie komenda operuje na wybranych rolach (`JoinRoom`, `Invite`) – rozwinięcie całej
  przestrzeni byłoby zbyt kosztowne.

Operacje naruszające te zasady kończą się błędem `DefaultRoleException`.

## Tworzenie roli

`CreateRole` tworzy nową rolę w przestrzeni. Nowa rola nie ma jeszcze
[nadpisań uprawnień](permissions.md#nadpisania-uprawnień) ani członków.

Wymaga uprawnienia `ManageRoles`.

Wszyscy członkowie przestrzeni otrzymują `NewRole`.

#### `CreateRole`

| Pole      | Typ                  | Opis                                     |
|-----------|----------------------|------------------------------------------|
| `spaceId` | `UUID`               | identyfikator przestrzeni                |
| `name`    | `string`             | nazwa roli (1–50 znaków)                 |
| `color`   | `string`&#124;`null` | kolor w formacie HEX                     |

#### `NewRole`

| Pole      | Typ                     | Opis                      |
|-----------|-------------------------|---------------------------|
| `spaceId` | `UUID`                  | identyfikator przestrzeni |
| `role`    | [`Role`](roles.md#role) | nowa rola                 |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                            |
|------------------------------|---------------------------------|
| `AccessDeniedException`      | brak uprawnienia `ManageRoles`  |
| `SpaceNotFoundException`     | przestrzeń nie istnieje         |
| `RoleExistsAlreadyException` | rola o takim ID już istnieje    |

## Edycja roli

`UpdateRole` zmienia nazwę, kolor lub priorytet roli, zgodnie z
[semantyką aktualizacji częściowej](protocol.md#semantyka-pól-w-komendach-updatex).

Wymaga uprawnienia `ManageRoles`.

Wszyscy członkowie przestrzeni otrzymują `RoleUpdated`.

#### `UpdateRole`

| Pole       | Typ                  | Opis                                            |
|------------|----------------------|-------------------------------------------------|
| `spaceId`  | `UUID`               | identyfikator przestrzeni                       |
| `id`       | `UUID`               | identyfikator roli                              |
| `priority` | `int`&#124;`null`    | nowy priorytet (od `1` w górę)                  |
| `name`     | `string`&#124;`null` | nowa nazwa                                      |
| `color`    | `string`&#124;`null` | nowy kolor; `null` usuwa kolor                  |

#### `RoleUpdated`

| Pole      | Typ                     | Opis                      |
|-----------|-------------------------|---------------------------|
| `spaceId` | `UUID`                  | identyfikator przestrzeni |
| `role`    | [`Role`](roles.md#role) | rola po zmianach          |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                               | Opis                                                    |
|-----------------------------------|---------------------------------------------------------|
| `AccessDeniedException`           | brak uprawnienia `ManageRoles`                          |
| `RoleNotFoundException`           | rola nie istnieje                                       |
| `DefaultRoleException`            | próba zmiany priorytetu roli domyślnej                  |
| `RolePriorityOutOfRangeException` | priorytet poza dozwolonym zakresem                      |

## Usuwanie roli

`DeleteRole` usuwa rolę wraz z jej nadpisaniami uprawnień. Wszyscy, którzy ją posiadali, tracą ją w tym samym
momencie – oddzielne zdarzenia `SpaceMemberUpdated` **nie są** emitowane, więc klient musi sam usunąć ID roli
z zapamiętanych członków.

Wymaga uprawnienia `ManageRoles`.

Wszyscy członkowie przestrzeni otrzymują `RoleDeleted`.

#### `DeleteRole`

| Pole      | Typ    | Opis                      |
|-----------|--------|---------------------------|
| `roleId`  | `UUID` | identyfikator roli        |
| `spaceId` | `UUID` | identyfikator przestrzeni |

#### `RoleDeleted`

| Pole      | Typ    | Opis                         |
|-----------|--------|------------------------------|
| `id`      | `UUID` | identyfikator usuniętej roli |
| `spaceId` | `UUID` | identyfikator przestrzeni    |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                                |
|--------------------------|-------------------------------------|
| `AccessDeniedException`  | brak uprawnienia `ManageRoles`      |
| `SpaceNotFoundException` | przestrzeń nie istnieje             |
| `RoleNotFoundException`  | rola nie istnieje                   |
| `DefaultRoleException`   | próba usunięcia roli domyślnej      |

## Przyznawanie i odbieranie roli

`AssignRole` i `DeassignRole` zmieniają skład roli. Warstwa lokalizacji decyduje o zasięgu przypisania:

| `location`             | Zasięg                                                       | Zdarzenie zwrotne     |
|------------------------|--------------------------------------------------------------|-----------------------|
| warstwa `Space`        | rola obowiązuje w całej przestrzeni                          | `SpaceMemberUpdated`  |
| warstwa `Room`         | rola obowiązuje tylko w tym pokoju                           | `RoomMemberUpdated`   |

Obie komendy wymagają uprawnienia `ManageMemberRoles` w danej lokalizacji.

#### `AssignRole` / `DeassignRole`

| Pole       | Typ                                                    | Opis                                       |
|------------|--------------------------------------------------------|--------------------------------------------|
| `roleId`   | `UUID`                                                 | identyfikator roli                         |
| `userId`   | `string`                                               | ID użytkownika                             |
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | warstwa `Space` lub `Room`                 |

Zdarzenia zwrotne opisane są przy [członkach przestrzeni](spaces.md#spacememberupdated) i
[członkach pokoju](rooms.md#roommemberupdated).

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                                                     |
|------------------------------|--------------------------------------------------------------------------|
| `AccessDeniedException`      | brak uprawnienia `ManageMemberRoles`                                     |
| `PermissionLayerException`   | lokalizacja nie wskazuje przestrzeni ani pokoju                          |
| `SpaceNotFoundException`     | przestrzeń nie istnieje lub pokój do niej nie należy                     |
| `RoleNotFoundException`      | rola nie istnieje (`AssignRole`) lub nie jest przypisana (`DeassignRole`) |
| `UserNotFoundException`      | użytkownik nie jest członkiem wskazanej lokalizacji                      |
| `RoleExistsAlreadyException` | użytkownik posiada już tę rolę                                           |
| `DefaultRoleException`       | próba odebrania roli domyślnej                                           |
