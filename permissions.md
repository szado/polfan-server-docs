# Uprawnienia

Uprawnienie określa, czy użytkownik może wykonać daną operację w danym miejscu czatu. Uprawnienia reprezentowane
są jako **maska bitowa** – pojedyncza liczba całkowita, w której każdy bit odpowiada jednemu uprawnieniu.

Dzięki temu obliczone uprawnienia mieszczą się w jednej liczbie, a sprawdzenie dostępu po stronie klienta to
zwykła operacja bitowa:

```js
const canRedact = (computedPermissions & Permissions.RedactMessages) !== 0;
```

## Lista uprawnień

Kolumna **maks. warstwa** określa najgłębszą [warstwę](permissions.md#warstwy), na której uprawnienie ma sens
i można je zdefiniować. Próba zdefiniowania go głębiej kończy się błędem `PermissionLayerException`.

| Uprawnienie            | Bit      | Wartość   | Maks. warstwa | Pozwala na                                                     |
|------------------------|----------|-----------|---------------|----------------------------------------------------------------|
| `Root`                 | `1 << 0` | 1         | `Room`        | wszystko w danym zakresie – patrz [`Root`](permissions.md#uprawnienie-root) |
| `CreateSpaces`         | `1 << 1` | 2         | `Global`      | [tworzenie przestrzeni](spaces.md#tworzenie-przestrzeni)       |
| `ManageSpace`          | `1 << 2` | 4         | `Space`       | [edycję](spaces.md#edycja-przestrzeni) i usuwanie przestrzeni  |
| `ManageRoles`          | `1 << 3` | 8         | `Space`       | tworzenie, edycję i usuwanie [ról](roles.md)                   |
| `CreateTopics`         | `1 << 5` | 32        | `Room`        | [tworzenie tematów](topics.md#tworzenie-tematu)                |
| `ManageTopic`          | `1 << 6` | 64        | `Topic`       | [edycję tematu](topics.md#edycja-tematu)                       |
| `ManageMemberProfiles` | `1 << 7` | 128       | `Space`       | zmianę pseudonimów i awatarów innych członków                  |
| `ManageMemberRoles`    | `1 << 8` | 256       | `Room`        | [nadawanie i odbieranie ról](roles.md#przyznawanie-i-odbieranie-roli) |
| `CreateMessages`       | `1 << 9` | 512       | `Topic`       | [publikowanie wiadomości](messages.md#tworzenie-wiadomości)    |
| `ManagePermissions`    | `1 << 10`| 1024      | `Topic`       | odczyt i zmianę [nadpisań uprawnień](permissions.md#nadpisania-uprawnień) |
| `CreateRooms`          | `1 << 11`| 2048      | `Space`       | [tworzenie pokojów](rooms.md#tworzenie-pokoju)                 |
| `ManageRoom`           | `1 << 12`| 4096      | `Room`        | [edycję](rooms.md#edycja-pokoju) i usuwanie pokoju             |
| `CreateEmoticons`      | `1 << 13`| 8192      | `Space`       | [dodawanie emotikon](emoticons.md#dodawanie-emotikony)         |
| `ManageEmoticons`      | `1 << 14`| 16384     | `Space`       | zarządzanie cudzymi emotikonami                                |
| `ManageBans`           | `1 << 15`| 32768     | `Room`        | [bany i wyciszenia](moderation.md#banowanie-i-wyciszanie)      |
| `Kick`                 | `1 << 16`| 65536     | `Room`        | [wyrzucanie](moderation.md#wyrzucanie-kick)                    |
| `ChangeOwnProfile`     | `1 << 17`| 131072    | `Space`       | zmianę własnego pseudonimu i awatara                           |
| `ChangeOwnColor`       | `1 << 18`| 262144    | `Room`        | zmianę własnego koloru                                         |
| `RedactMessages`       | `1 << 19`| 524288    | `Topic`       | [usuwanie wiadomości](messages.md#usuwanie-wiadomości-redakcja) |
| `AddMembers`           | `1 << 20`| 1048576   | `Space`       | [dodawanie innych do pokojów](rooms.md#wejście-do-pokoju)      |
| `React`                | `1 << 21`| 2097152   | `Topic`       | [reagowanie na wiadomości](messages.md#reakcje)                |
| `CreatePolls`          | `1 << 22`| 4194304   | `Topic`       | [tworzenie ankiet](messages.md#ankiety)                        |

Bit `1 << 4` jest zarezerwowany i nieużywany.

## Warstwy

Uprawnienia obliczane są w hierarchii czterech warstw:

| Poziom | Warstwa  | Zakres                    |
|--------|----------|---------------------------|
| 0      | `Global` | cały serwer               |
| 1      | `Space`  | [przestrzeń](spaces.md)   |
| 2      | `Room`   | [pokój](rooms.md)         |
| 3      | `Topic`  | [temat](topics.md)        |

Warstwę wskazuje się [lokalizacją](protocol.md#lokalizacja-chatlocation): warstwą docelową jest najgłębszy
wypełniony identyfikator.

## Nadpisania uprawnień

Nadpisanie (`PermissionOverwrites`) to para masek bitowych przypisana **celowi** (roli lub użytkownikowi)
w konkretnej lokalizacji.

#### `PermissionOverwritesValue`

| Pole    | Typ               | Opis                                        |
|---------|-------------------|---------------------------------------------|
| `allow` | `int`&#124;`null` | maska uprawnień jawnie przyznanych          |
| `deny`  | `int`&#124;`null` | maska uprawnień jawnie odebranych           |

#### `PermissionOverwritesTarget`

| Pole     | Typ                     | Opis                                             |
|----------|-------------------------|--------------------------------------------------|
| `type`   | `"User"`&#124;`"Role"`  | rodzaj celu                                      |
| `userId` | `string`&#124;`null`    | wymagane gdy `type` = `User`                     |
| `roleId` | `UUID`&#124;`null`      | wymagane gdy `type` = `Role`                     |

Bit nieobecny ani w `allow`, ani w `deny` jest **niezdefiniowany** – wartość zostanie odziedziczona z wyższej
warstwy. To rozróżnienie (przyznane / odebrane / niezdefiniowane) jest sednem całego modelu.

## Obliczanie uprawnień

Serwer przechodzi warstwy od najwyższej do najniższej (`Global` → `Space` → `Room` → `Topic`). W obrębie każdej
warstwy wykonuje dwa kroki:

1. **Kumulacja ról** – nadpisania wszystkich ról użytkownika na tej warstwie łączone są sumą bitową. Uprawnienie
   przyznane przez którąkolwiek rolę wygrywa z odebraniem przez inną. Priorytety ról **nie mają tu znaczenia** –
   służą wyłącznie do prezentacji.
2. **Nadpisanie indywidualne** – nadpisania przypisane bezpośrednio użytkownikowi stosowane są po rolach i mają
   nad nimi pierwszeństwo.

Każdy krok stosowany jest do dotychczasowego wyniku według reguły:

```
wynik = (wynik & ~deny) | allow
```

Wynik z warstwy wyższej wchodzi jako stan początkowy warstwy niższej, dzięki czemu ustawienia z pokoju doprecyzowują
te z przestrzeni, a te z tematu – te z pokoju.

### Rola domyślna a uprawnienia

Rola [`@everyone`](roles.md#rola-domyślna-everyone) na warstwie `Space` musi definiować **wszystkie** uprawnienia
możliwe do zdefiniowania na tej warstwie – każdy bit musi trafić do `allow` albo do `deny`. Próba zapisania
niepełnego zestawu kończy się błędem `DefaultRoleException` z nazwą brakującego uprawnienia.

Dzięki temu przestrzeń ma jednoznaczny punkt odniesienia: wartości z warstwy globalnej nigdy nie „przeciekają” do
przestrzeni w sposób nieoczywisty dla jej właściciela. Praktyczna konsekwencja: **po dodaniu nowego uprawnienia do
serwera** możesz napotkać rolę domyślną bez jego wartości – każda edycja nadpisań wymusi wtedy jej uzupełnienie.

### Uprawnienie `Root`

`Root` to obejście całego procesu: jeśli pojawi się w masce `allow` dowolnych przetwarzanych nadpisań, obliczanie
kończy się natychmiast przyznaniem **wszystkich** uprawnień w danym zakresie i niżej.

* globalne `Root` ma użytkownik wskazany w konfiguracji serwera;
* `Root` w przestrzeni lub pokoju mają jej [właściciele](permissions.md#właściciele).

`Root` **nie może być nadany ani odebrany** komendą `SetPermissionOverwrites` – próba jego zmiany kończy się
błędem `RootPermissionException`. Do zarządzania nim służą komendy właścicielskie.

## Pobieranie obliczonych uprawnień

`GetComputedPermissions` zwraca wynikową maskę bieżącego użytkownika w podanej lokalizacji. Bot powinien wywołać
ją raz na lokalizację, w której działa, i sprawdzać uprawnienia lokalnie, zamiast czekać na
`AccessDeniedException`.

#### `GetComputedPermissions`

| Pole       | Typ                                                    | Opis                                              |
|------------|--------------------------------------------------------|---------------------------------------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja; pusty obiekt = warstwa globalna      |

W odpowiedzi serwer wysyła zdarzenie `ComputedPermissions`.

#### `ComputedPermissions`

| Pole          | Typ                                                    | Opis                              |
|---------------|--------------------------------------------------------|-----------------------------------|
| `permissions` | `int`                                                  | wynikowa maska bitowa             |
| `location`    | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja, której dotyczy wynik |

<details><summary>Przykład <code>GetComputedPermissions</code></summary>

```json
{
  "meta": { "type": "GetComputedPermissions", "ref": "5" },
  "data": {
    "location": { "roomId": "7hK9pQ2vXnR4tY6uW1sZbA" }
  }
}
```

</details>

!> Uprawnienia zmieniają się w czasie – po zdarzeniach `PermissionOverwritesUpdated`, `RoleDeleted`,
`SpaceMemberUpdated` i `RoomMemberUpdated` dotyczących bota warto odświeżyć obliczoną maskę.

## Pobieranie nadpisań

`GetPermissionOverwrites` zwraca nadpisania konkretnego celu w konkretnej lokalizacji – surowe maski `allow`
i `deny`, bez dziedziczenia.

Odczyt cudzych nadpisań wymaga uprawnienia `ManagePermissions`; własne nadpisania użytkownik może odczytać zawsze.

#### `GetPermissionOverwrites`

| Pole       | Typ                                                            | Opis                    |
|------------|----------------------------------------------------------------|-------------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation)         | lokalizacja             |
| `target`   | [`PermissionOverwritesTarget`](permissions.md#permissionoverwritestarget) | cel nadpisań |

#### `PermissionOverwrites`

| Pole         | Typ                                                                     | Opis            |
|--------------|-------------------------------------------------------------------------|-----------------|
| `location`   | [`ChatLocation`](protocol.md#lokalizacja-chatlocation)                  | lokalizacja     |
| `target`     | [`PermissionOverwritesTarget`](permissions.md#permissionoverwritestarget) | cel           |
| `overwrites` | [`PermissionOverwritesValue`](permissions.md#permissionoverwritesvalue) | maski           |

## Ustawianie nadpisań

`SetPermissionOverwrites` zapisuje nadpisania celu w lokalizacji. Przesłane maski **zastępują** dotychczasowe
w całości – nie są z nimi łączone. Przed zapisem pobierz stan komendą `GetPermissionOverwrites` i zmodyfikuj go
bitowo, żeby nie skasować cudzych ustawień.

Wymaga uprawnienia `ManagePermissions` w danej lokalizacji.

#### `SetPermissionOverwrites`

| Pole         | Typ                                                                     | Opis          |
|--------------|-------------------------------------------------------------------------|---------------|
| `location`   | [`ChatLocation`](protocol.md#lokalizacja-chatlocation)                  | lokalizacja   |
| `target`     | [`PermissionOverwritesTarget`](permissions.md#permissionoverwritestarget) | cel         |
| `overwrites` | [`PermissionOverwritesValue`](permissions.md#permissionoverwritesvalue) | nowe maski    |

Odpowiedzią jest `PermissionOverwritesUpdated` o strukturze identycznej z
[`PermissionOverwrites`](permissions.md#permissionoverwrites). Zdarzenie trafia też do pozostałych
zainteresowanych klientów.

<details><summary>Przykład <code>SetPermissionOverwrites</code></summary>

```json
{
  "meta": { "type": "SetPermissionOverwrites", "ref": "9" },
  "data": {
    "location": { "roomId": "7hK9pQ2vXnR4tY6uW1sZbA" },
    "target": { "type": "Role", "roleId": "5nKwz3dEUaR7YQ8mQ1L2pF" },
    "overwrites": { "allow": 524288, "deny": 512 }
  }
}
```

</details>

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                                   | Opis                                                                 |
|---------------------------------------|----------------------------------------------------------------------|
| `AccessDeniedException`               | brak uprawnienia `ManagePermissions`                                 |
| `RootPermissionException`             | próba zmiany bitu `Root`                                             |
| `PermissionLayerException`            | uprawnienie nie może być zdefiniowane na tej warstwie                |
| `PermissionBitException`              | maska zawiera bit spoza rejestru uprawnień                           |
| `PermissionNotFoundException`         | nieznane uprawnienie                                                 |
| `PermissionOverwritesTargetException` | cel bez wymaganego `userId` lub `roleId`                             |
| `DefaultRoleException`                | rola domyślna nie definiuje kompletu uprawnień warstwy `Space`       |
| `SpaceNotFoundException`, `RoomNotFoundException`, `TopicNotFoundException` | lokalizacja nie istnieje                |

## Lista celów z nadpisaniami

`GetPermissionOverwriteTargets` zwraca wszystkie role i wszystkich użytkowników, którzy mają jakiekolwiek
nadpisania w danej lokalizacji. To punkt startowy dla panelu uprawnień: zamiast odpytywać o każdego członka
osobno, dostajesz listę tych, dla których w ogóle coś zdefiniowano.

Wymaga uprawnienia `ManagePermissions`. Lokalizacja musi wskazywać warstwę `Space`, `Room` lub `Topic`.

#### `GetPermissionOverwriteTargets`

| Pole       | Typ                                                    | Opis        |
|------------|--------------------------------------------------------|-------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja |

#### `PermissionOverwriteTargets`

| Pole           | Typ                                                     | Opis                                              |
|----------------|---------------------------------------------------------|---------------------------------------------------|
| `location`     | [`ChatLocation`](protocol.md#lokalizacja-chatlocation)  | lokalizacja                                       |
| `spaceMembers` | [`SpaceMember[]`](spaces.md#spacemember)&#124;`null`    | członkowie przestrzeni z nadpisaniami             |
| `roomMembers`  | [`RoomMember[]`](rooms.md#roommember)&#124;`null`       | członkowie pokoju z nadpisaniami                  |
| `roles`        | [`Role[]`](roles.md#role)&#124;`null`                   | role z nadpisaniami                               |

Wypełnione są tylko pola mające sens dla warstwy z zapytania – pozostałe zawierają `null`.

## Właściciele

Właściciel (`owner`) to użytkownik z uprawnieniem [`Root`](permissions.md#uprawnienie-root) w danej przestrzeni
lub pokoju. Właścicielem staje się automatycznie twórca zasobu; kolejnych może dodać wyłącznie inny właściciel.

Wszystkie trzy komendy wymagają uprawnienia `Root` w lokalizacji i zwracają aktualną listę `Owners`.
Lokalizacja musi wskazywać warstwę `Space` lub `Room`.

#### `GetOwners` / `CreateOwner` / `DeleteOwner`

| Pole       | Typ                                                    | Opis                                        |
|------------|--------------------------------------------------------|---------------------------------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | warstwa `Space` lub `Room`                  |
| `userId`   | `string`                                               | ID użytkownika (poza `GetOwners`)           |

#### `Owners`

| Pole           | Typ                                                  | Opis                                        |
|----------------|------------------------------------------------------|---------------------------------------------|
| `location`     | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja                               |
| `spaceMembers` | [`SpaceMember[]`](spaces.md#spacemember)&#124;`null` | właściciele przestrzeni                     |
| `roomMembers`  | [`RoomMember[]`](rooms.md#roommember)&#124;`null`    | właściciele pokoju                          |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                        | Opis                                             |
|----------------------------|--------------------------------------------------|
| `AccessDeniedException`    | brak uprawnienia `Root` w lokalizacji            |
| `OwnerException`           | niedozwolona operacja na tym właścicielu         |
| `UserNotFoundException`    | użytkownik nie jest członkiem lokalizacji        |
| `PermissionLayerException` | lokalizacja nie wskazuje przestrzeni ani pokoju  |
