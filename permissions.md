# Uprawnienia

Uprawnienie identyfikowane jest za pomocą unikalnej nazwy. Jego wartość określa, czy użytkownik może wykonać operację lub zestaw operacji.

## Obliczanie wartości uprawnień

Obliczanie to proces podczas którego ustalana jest ostateczna wartość uprawnienia dla użytkownika w określonym kontekście (wynik obliczeń). 

Uprawnienie może przyjmować jedną z dwóch wartości:
- `zezwolenie`,
- `zabronienie`.

###  Warstwy

Wartość uprawnienia może być zdefiniowana dla użytkownika na siedmiu warstwach:

1. globalnej (serwerowej) warstwie użytkownika,
2. warstwie ról w przestrzeni,
3. warstwie użytkownika w przestrzeni,
4. warstwie ról w pokoju,
5. warstwie użytkownika w pokoju,
6. warstwie ról w temacie,
7. warstwie użytkownika w temacie.

Na warstwach od 2. do 7. wartości uprawnień mogą, lecz nie muszą być zdefiniowane.

Na warstwie 1. wszystkie uprawnienia mają przypisane wartości domyślne, tak, by proces obliczania zawsze zakończył się zwróceniem jednoznacznej wartości.

Na potrzeby procesu obliczania, z każdej warstwy pobierana jest pojedyncza wartość (jeśli istnieje). Następnie wartości te zostają poddane procesowi nadpisania.

#### Flaga `skip`

Każda wartość warstwy może posiadać przypisaną flagę logiczną `skip`. Jej wartość uwzględniana jest w procesie nadpisania.

### Kumulacja uprawnień ról

Ponieważ użytkownik może posiadać wiele ról w ramach przestrzeni, a każda rola może posiadać własną przypisaną wartość uprawnienia, zachodzi potrzeba kumulacji tych wartości wg zasady:

*Role przyznają `zezwolenie` w ramach warstwy, jeśli którejkolwiek roli użytkownika przypisano `zezwolenie`.*

### Nadpisanie uprawnień

Nadpisanie uprawnień to proces mający na celu ustalenie wyniku obliczeń na podstawie wartości pochodzących z warstw. Wynikiem jest wartość zdefiniowana na ostatniej warstwie lub warstwie posiadającej przypisaną flagę `skip` (kolejne wartości są pomijane), zachowując kolejność warstw, wymienioną powyżej. 

## Ustawienie uprawnień dla roli

Komenda `SetRolePermissions` umożliwia definicję wartości uprawnień dla roli w [warstwie 2., 4. i 6](permissions.md#warstwy).

W przypadku powodzenia klient otrzyma odpowiedź `Ok`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `SetRolePermissions`

Żądanie ustawienia listy uprawnień dla roli.

| Pole          | Typ                                    | Opis                                                                |
|---------------|----------------------------------------|---------------------------------------------------------------------|
| `roleId`      | `UUID`                                 | ID roli                                                             |
| `layer`       | `"Space"`&#124;`"Room"`&#124;`"Topic"` | warstwa dla której chcemy zdefiniować uprawnienia                   |
| `layerId`     | `integer`                              | ID warstwy (przestrzeni, pokoju lub tematu)                         |
| `permissions` | `Permission[]`                         | tablica uprawnień (te wartości **nadpiszą istniejące uprawnienia**) |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                    |
|--------------------------|-------------------------|
| `SpaceNotFoundException` | przestrzeń nie istnieje |
| `RoomNotFoundException`  | pokój nie istnieje      |
| `TopicNotFoundException` | temat nie istnieje      |
| `RoleNotFoundException`  | rola nie istnieje       |

## Pobieranie uprawnień roli

Komenda `GetRolePermissions` umożliwia pobranie wartości uprawnień dla roli z [warstwy 2., 4. i 6](permissions.md#warstwy).

W przypadku powodzenia, klient otrzyma odpowiedź `Permissions`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `GetRolePermissions`

Żądanie pobrania listy uprawnień dla roli.

| Pole      | Typ                                    | Opis                                                                     |
|-----------|----------------------------------------|--------------------------------------------------------------------------|
| `roleId`  | `string`                               | ID roli                                                                  |
| `layer`   | `"Space"`&#124;`"Room"`&#124;`"Topic"` | warstwa dla której chcemy pobrać uprawnienia                             |
| `layerId` | `integer`                              | ID warstwy (przestrzeni, pokoju lub tematu)                              |
| `names`   | `string[]`&#124;`null`                 | tablica nazw uprawnień do pobrania, domyślnie zwrócone zostaną wszystkie |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                    |
|--------------------------|-------------------------|
| `SpaceNotFoundException` | przestrzeń nie istnieje |
| `RoomNotFoundException`  | pokój nie istnieje      |
| `TopicNotFoundException` | temat nie istnieje      |
| `RoleNotFoundException`  | rola nie istnieje       |

## Ustawienie uprawnień dla użytkownika

Komenda `SetMemberPermissions` umożliwia definicję wartości uprawnień dla użytkownika w [warstwie 1., 3., 5. i 7](permissions.md#warstwy).

W przypadku powodzenia, klient otrzyma odpowiedź `Ok`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `SetMemberPermissions`

Żądanie ustawienia listy uprawnień dla użytkownika.

| Pole          | Typ                                                    | Opis                                                                                   |
|---------------|--------------------------------------------------------|----------------------------------------------------------------------------------------|
| `userId`      | `string`                                               | ID użytkownika                                                                         |
| `layer`       | `"Global"`&#124;`"Space"`&#124;`"Room"`&#124;`"Topic"` | warstwa dla której chcemy zdefiniować uprawnienia                                      |
| `layerId`     | `integer`&#124;`null`                                  | ID warstwy (przestrzeni, pokoju lub tematu). Przypisz `null` jeśli warstwa to `Global` |
| `permissions` | `Permission[]`                                         | tablica uprawnień (te wartości **nadpiszą istniejące uprawnienia**)                    |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                    |
|--------------------------|-------------------------|
| `UserNotFoundException`  | przestrzeń nie istnieje |
| `RoomNotFoundException`  | pokój nie istnieje      |
| `TopicNotFoundException` | temat nie istnieje      |
| `UserNotFoundException`  | użytkownik nie istnieje |

## Pobieranie uprawnień użytkownika

Komenda `GetMemberPermissions` umożliwia pobranie wartości uprawnień dla roli z [warstwy 1., 3., 5. i 7](permissions.md#warstwy).

W przypadku powodzenia klient otrzyma odpowiedź `Permissions`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `GetMemberPermissions`

Żądanie pobrania listy uprawnień użytkownika.

| Pole      | Typ                                                    | Opis                                                                                   |
|-----------|--------------------------------------------------------|----------------------------------------------------------------------------------------|
| `userId`  | `string`                                               | ID użytkownika                                                                         |
| `layer`   | `"Global"`&#124;`"Space"`&#124;`"Room"`&#124;`"Topic"` | warstwa dla której chcemy pobrać uprawnienia                                           |
| `layerId` | `integer`                                              | ID warstwy (przestrzeni, pokoju lub tematu). Przypisz `null` jeśli warstwa to `Global` |
| `names`   | `string[]`&#124;` null`                                | tablica nazw uprawnień do pobrania, domyślnie zwrócone zostaną wszystkie               |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                    |
|--------------------------|-------------------------|
| `SpaceNotFoundException` | przestrzeń nie istnieje |
| `RoomNotFoundException`  | pokój nie istnieje      |
| `TopicNotFoundException` | temat nie istnieje      |
| `UserNotFoundException`  | rola nie istnieje       |

## Pobieranie obliczonych uprawnień

Komenda `GetComputedPermissions` umożliwia pobranie obliczonych uprawnień dla aktualnego użytkownika.

W przypadku powodzenia, klient otrzyma odpowiedź `Permissions`.

W przypadku błędu serwer wyśle odpowiedź `Error`.

### `GetComputedPermissions`

Żądanie pobrania obliczonej listy uprawnień aktualnego użytkownika.

| Pole      | Typ                    | Opis                                                                     |
|-----------|------------------------|--------------------------------------------------------------------------|
| `spaceId` | `UUID`&#124;`null`     | ID przestrzeni                                                           |
| `roomId`  | `UUID`&#124;`null`     | ID pokoju                                                                |
| `topicId` | `UUID`&#124;`null`     | ID tematu                                                                |
| `names`   | `string[]`&#124;`null` | tablica nazw uprawnień do pobrania, domyślnie zwrócone zostaną wszystkie |

### Możliwe kody błędów w `Error`

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

|Kod|Opis|
|-----|-----|