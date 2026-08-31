# Przestrzenie

Przestrzeń (`Space`) to wyizolowany zbiór użytkowników, pokojów, ról i ustawień – odpowiednik „serwera” znanego
z innych komunikatorów. Członkostwo w przestrzeni jest warunkiem wejścia do jej pokojów, a
[uprawnienia](permissions.md) i [role](roles.md) definiowane są właśnie na tym poziomie.

Dla integracji przestrzeń jest naturalną jednostką wdrożenia: bot dołącza do przestrzeni raz, a następnie
obsługuje wszystkie należące do niej pokoje.

## Obiekty

#### `Space`

| Pole           | Typ                            | Opis                                                                 |
|----------------|--------------------------------|----------------------------------------------------------------------|
| `id`           | `UUID`                         | identyfikator przestrzeni                                            |
| `name`         | `string`                       | nazwa (1–50 znaków)                                                  |
| `description`  | `string`                       | opis (do 200 znaków)                                                 |
| `roles`        | [`Role[]`](roles.md#role)      | wszystkie role zdefiniowane w przestrzeni                            |
| `systemRoom`   | `UUID`&#124;`null`             | pokój, do którego trafiają [wiadomości systemowe](messages.md#typy-wiadomości) |
| `defaultRooms` | `UUID[]`                       | pokoje, do których nowy członek dołącza automatycznie                |
| `icon`         | `UUID`&#124;`null`             | [ID pliku](files.md) ikony                                           |
| `banner`       | `UUID`&#124;`null`             | [ID pliku](files.md) baneru                                          |
| `discoverable` | `SpaceDiscoverable`            | status widoczności w katalogu publicznym                             |
| `flags`        | `int`                          | maska bitowa [flag](spaces.md#flagi-przestrzeni)                     |

`SpaceDiscoverable` przyjmuje wartości: `NotRequested`, `Requested`, `Accepted`, `Declined`. Tylko przestrzenie
ze statusem `Accepted` pojawiają się w katalogu.

#### Flagi przestrzeni

| Bit     | Wartość | Nazwa     | Znaczenie                                             |
|---------|---------|-----------|-------------------------------------------------------|
| `1 << 0`| `1`     | `Private` | przestrzeń niewidoczna publicznie                     |
| `1 << 1`| `2`     | `Insight` | rozszerzone statystyki i wgląd dla właścicieli        |

#### `SpaceMember`

| Pole           | Typ                                | Opis                                                       |
|----------------|------------------------------------|------------------------------------------------------------|
| `user`         | [`User`](users.md#user)&#124;`null` | dane użytkownika; `null` gdy zwracany jest tylko szkielet  |
| `roles`        | `UUID[]`                           | ID [ról](roles.md) przypisanych na poziomie przestrzeni    |
| `customNick`   | `string`&#124;`null`               | pseudonim nadpisany w tej przestrzeni                      |
| `customAvatar` | `UUID`&#124;`null`                 | [ID pliku](files.md) awatara w tej przestrzeni             |

#### `SpaceSummary`

Skrócony opis przestrzeni, zwracany tam, gdzie użytkownik nie jest jej członkiem.

| Pole          | Typ                | Opis                       |
|---------------|--------------------|----------------------------|
| `id`          | `UUID`             | identyfikator              |
| `name`        | `string`           | nazwa                      |
| `description` | `string`           | opis                       |
| `icon`        | `UUID`&#124;`null` | ID pliku ikony             |
| `banner`      | `UUID`&#124;`null` | ID pliku baneru            |
| `memberCount` | `int`              | liczba członków            |

## Katalog przestrzeni publicznych

Zanim bot dołączy do przestrzeni, musi poznać jej identyfikator. Komenda `GetDiscoverableSpaces` (bez pól) zwraca
listę przestrzeni zgłoszonych i zaakceptowanych do katalogu publicznego.

W odpowiedzi serwer wysyła `DiscoverableSpaces`.

#### `DiscoverableSpaces`

| Pole        | Typ                                         | Opis                       |
|-------------|---------------------------------------------|----------------------------|
| `summaries` | [`SpaceSummary[]`](spaces.md#spacesummary)  | lista przestrzeni w katalogu |

## Podgląd przestrzeni

Komenda `GetSpaceSummary` zwraca [`SpaceSummary`](spaces.md#spacesummary) pojedynczej przestrzeni – bez konieczności
bycia jej członkiem. Przydatna, gdy chcesz pokazać zaproszenie albo zweryfikować identyfikator przed dołączeniem.

#### `GetSpaceSummary`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

W odpowiedzi serwer wysyła `SpaceSummary`.

#### `SpaceSummary` (zdarzenie)

| Pole      | Typ                                        | Opis        |
|-----------|--------------------------------------------|-------------|
| `summary` | [`SpaceSummary`](spaces.md#spacesummary)   | dane przestrzeni |

### Możliwe kody błędów

| Kod                      | Opis                                                    |
|--------------------------|---------------------------------------------------------|
| `SpaceNotFoundException` | przestrzeń nie istnieje lub jest prywatna               |

## Wejście do przestrzeni

`JoinSpace` dołącza bieżącego użytkownika do przestrzeni. Po dołączeniu użytkownik otrzymuje rolę domyślną
`@everyone` i zostaje automatycznie wprowadzony do pokojów wskazanych w `defaultRooms`.

Nadawca otrzymuje `SpaceJoined`, a pozostali członkowie – `SpaceMemberJoined`.

#### `JoinSpace`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

<details><summary>Przykład <code>JoinSpace</code></summary>

```json
{
  "meta": { "type": "JoinSpace", "ref": "1" },
  "data": { "id": "3dEUaR7YQ8mQ1L2pF5nKwz" }
}
```

</details>

#### `SpaceJoined`

| Pole    | Typ                        | Opis                                       |
|---------|----------------------------|--------------------------------------------|
| `space` | [`Space`](spaces.md#space) | pełny obiekt przestrzeni wraz z rolami     |

#### `SpaceMemberJoined`

| Pole      | Typ                                    | Opis                       |
|-----------|----------------------------------------|----------------------------|
| `spaceId` | `UUID`                                 | identyfikator przestrzeni  |
| `member`  | [`SpaceMember`](spaces.md#spacemember) | dane nowego członka        |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                           |
|------------------------------|------------------------------------------------|
| `SpaceNotFoundException`     | przestrzeń nie istnieje                        |
| `UserExistsAlreadyException` | użytkownik jest już członkiem                  |
| `BannedAccessException`      | użytkownik ma aktywny ban w tej przestrzeni    |

## Wyjście z przestrzeni

`LeaveSpace` usuwa użytkownika z przestrzeni **oraz ze wszystkich jej pokojów** – dla każdego z nich otrzymasz
osobne zdarzenie [`RoomLeft`](rooms.md#roomleft) z powodem `SpaceLeave`.

Nadawca otrzymuje `SpaceLeft`, pozostali członkowie – `SpaceMemberLeft`.

#### `LeaveSpace`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

#### `SpaceLeft`

| Pole     | Typ                                            | Opis                     |
|----------|------------------------------------------------|--------------------------|
| `id`     | `UUID`                                         | identyfikator przestrzeni |
| `reason` | [`LeaveReason`](connection.md#leavereason)     | powód opuszczenia         |

To samo zdarzenie otrzymasz przy wyrzuceniu lub zbanowaniu – rozróżnisz je po `reason.type`.

#### `SpaceMemberLeft`

| Pole      | Typ      | Opis                      |
|-----------|----------|---------------------------|
| `spaceId` | `UUID`   | identyfikator przestrzeni |
| `userId`  | `string` | ID użytkownika            |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                                  |
|--------------------------|---------------------------------------|
| `SpaceNotFoundException` | przestrzeń nie istnieje               |
| `UserNotFoundException`  | użytkownik nie jest członkiem         |

## Tworzenie przestrzeni

`CreateSpace` zakłada nową przestrzeń. Twórca zostaje jej **właścicielem** – otrzymuje uprawnienie
[`Root`](permissions.md#uprawnienie-root) i pełną kontrolę nad konfiguracją. Wraz z przestrzenią powstaje rola
domyślna `@everyone`.

W przypadku powodzenia nadawca zostaje dołączony do nowej przestrzeni i otrzymuje
[`SpaceJoined`](spaces.md#spacejoined).

Wymaga uprawnienia `CreateSpaces` na warstwie globalnej.

#### `CreateSpace`

| Pole    | Typ               | Opis                                             |
|---------|-------------------|--------------------------------------------------|
| `name`  | `string`          | nazwa przestrzeni (1–50 znaków)                  |
| `flags` | `int`&#124;`null` | [flagi](spaces.md#flagi-przestrzeni), domyślnie 0 |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                           | Opis                                 |
|-------------------------------|--------------------------------------|
| `AccessDeniedException`       | brak uprawnienia `CreateSpaces`      |
| `SpaceExistsAlreadyException` | przestrzeń o takim ID już istnieje   |
| `SpaceFlagsException`         | niepoprawna kombinacja flag          |

## Edycja przestrzeni

`UpdateSpace` zmienia konfigurację przestrzeni. Obowiązuje
[semantyka aktualizacji częściowej](protocol.md#semantyka-pól-w-komendach-updatex): pominięte pole pozostaje bez
zmian, jawny `null` czyści wartość.

Wszyscy członkowie otrzymują `SpaceUpdated`.

Wymaga uprawnienia `ManageSpace`.

#### `UpdateSpace`

| Pole           | Typ                          | Opis                                                             |
|----------------|------------------------------|------------------------------------------------------------------|
| `id`           | `UUID`                       | identyfikator przestrzeni                                        |
| `name`         | `string`                     | nowa nazwa                                                       |
| `description`  | `string`                     | nowy opis (do 200 znaków)                                        |
| `systemRoom`   | `UUID`&#124;`null`           | pokój na wiadomości systemowe; `null` wyłącza je                 |
| `defaultRooms` | `UUID[]`                     | pokoje, do których dołączają nowi członkowie                     |
| `icon`         | `UUID`&#124;`null`           | [ID przesłanego pliku](files.md#ikona-i-baner-przestrzeni)       |
| `banner`       | `UUID`&#124;`null`           | ID przesłanego pliku baneru                                      |
| `discoverable` | `SpaceDiscoverable`          | zgłoszenie do katalogu (`Requested`) lub wycofanie               |
| `flags`        | `int`                        | nowa maska [flag](spaces.md#flagi-przestrzeni)                   |

#### `SpaceUpdated`

| Pole    | Typ                        | Opis                        |
|---------|----------------------------|-----------------------------|
| `space` | [`Space`](spaces.md#space) | przestrzeń po zmianach      |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                    |
|------------------------------|-----------------------------------------|
| `AccessDeniedException`      | brak uprawnienia `ManageSpace`          |
| `SpaceNotFoundException`     | przestrzeń nie istnieje                 |
| `RoomNotFoundException`      | wskazany pokój nie należy do przestrzeni |
| `SpaceIconException`         | ikona nie spełnia wymagań               |
| `SpaceBannerException`       | baner nie spełnia wymagań               |
| `SpaceDescriptionException`  | niepoprawny opis                        |
| `SpaceDiscoverableException` | niepoprawny status widoczności          |
| `SpaceFlagsException`        | niepoprawna kombinacja flag             |

## Usuwanie przestrzeni

`DeleteSpace` trwale usuwa przestrzeń wraz z pokojami, tematami i historią. Wszyscy członkowie otrzymują
`SpaceDeleted`.

Wymaga uprawnienia `ManageSpace`.

#### `DeleteSpace`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

#### `SpaceDeleted`

| Pole | Typ    | Opis                                |
|------|--------|-------------------------------------|
| `id` | `UUID` | identyfikator usuniętej przestrzeni |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                            |
|--------------------------|---------------------------------|
| `AccessDeniedException`  | brak uprawnienia `ManageSpace`  |
| `SpaceNotFoundException` | przestrzeń nie istnieje         |

## Lista członków przestrzeni

`GetSpaceMembers` zwraca komplet członków przestrzeni wraz z ich rolami. To podstawowe źródło danych dla bota,
który mapuje użytkowników na role – wywołaj ją raz po połączeniu, a następnie utrzymuj listę na podstawie zdarzeń
`SpaceMemberJoined`, `SpaceMemberLeft` i `SpaceMemberUpdated`.

#### `GetSpaceMembers`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

#### `SpaceMembers`

| Pole      | Typ                                      | Opis                      |
|-----------|------------------------------------------|---------------------------|
| `id`      | `UUID`                                   | identyfikator przestrzeni |
| `members` | [`SpaceMember[]`](spaces.md#spacemember) | lista członków            |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                                 |
|-------------------------|--------------------------------------|
| `UserNotFoundException` | użytkownik nie należy do przestrzeni |

## Lista pokojów w przestrzeni

`GetSpaceRooms` zwraca [skrócone opisy](rooms.md#roomsummary) pokojów przestrzeni. Lista zawiera pokoje widoczne
dla wywołującego – pokoje prywatne i „tylko na zaproszenie” pojawią się, jeśli użytkownik jest do nich zaproszony,
jest ich członkiem albo posiada uprawnienie `ManageRoom`.

#### `GetSpaceRooms`

| Pole | Typ    | Opis                      |
|------|--------|---------------------------|
| `id` | `UUID` | identyfikator przestrzeni |

#### `SpaceRooms`

| Pole        | Typ                                     | Opis                      |
|-------------|-----------------------------------------|---------------------------|
| `id`        | `UUID`                                  | identyfikator przestrzeni |
| `summaries` | [`RoomSummary[]`](rooms.md#roomsummary) | lista pokojów             |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                                 |
|-------------------------|--------------------------------------|
| `UserNotFoundException` | użytkownik nie należy do przestrzeni |

## Profil członka przestrzeni

`UpdateSpaceMember` zmienia pseudonim lub awatar użytkownika **w obrębie jednej przestrzeni**. Pozwala botowi
prowadzić np. system nadawania przydomków albo przywracać nazwę użytkownikowi, który złamał regulamin nazewnictwa.

Wszyscy członkowie przestrzeni otrzymują `SpaceMemberUpdated`.

Uprawnienia:

* zmiana **własnego** profilu wymaga `ChangeOwnProfile`;
* zmiana profilu **innego** użytkownika wymaga `ManageMemberProfiles`.

#### `UpdateSpaceMember`

| Pole           | Typ                  | Opis                                                  |
|----------------|----------------------|-------------------------------------------------------|
| `spaceId`      | `UUID`               | identyfikator przestrzeni                             |
| `userId`       | `string`             | ID użytkownika                                        |
| `customNick`   | `string`&#124;`null` | pseudonim (1–32 znaki); `null` przywraca globalny nick |
| `customAvatar` | `UUID`&#124;`null`   | [ID pliku](files.md#awatary-członków) awatara; `null` przywraca globalny |

#### `SpaceMemberUpdated`

| Pole      | Typ                                    | Opis                       |
|-----------|----------------------------------------|----------------------------|
| `spaceId` | `UUID`                                 | identyfikator przestrzeni  |
| `userId`  | `string`                               | ID użytkownika             |
| `member`  | [`SpaceMember`](spaces.md#spacemember) | członek po zmianach        |

To samo zdarzenie emitowane jest przy [nadaniu i odebraniu roli](roles.md).

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                        | Opis                                                    |
|----------------------------|---------------------------------------------------------|
| `AccessDeniedException`    | brak `ChangeOwnProfile` / `ManageMemberProfiles`        |
| `SpaceNotFoundException`   | przestrzeń nie istnieje                                 |
| `UserNotFoundException`    | użytkownik nie jest członkiem przestrzeni               |
| `MemberAvatarException`    | awatar nie spełnia wymagań                              |
