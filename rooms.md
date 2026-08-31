# Pokoje

Pokój (`Room`) to podzbiór członków przestrzeni wraz z własnymi ustawieniami i uprawnieniami. Zawiera
[tematy](topics.md), w ramach których odbywa się wymiana [wiadomości](messages.md).

Obecność w pokoju jest warunkiem odbierania jego zdarzeń: bot dostaje `NewMessage` tylko z pokojów, do których
dołączył. Wejście do pokoju wymaga wcześniejszego [członkostwa w przestrzeni](spaces.md#wejście-do-przestrzeni) –
z wyjątkiem [rozmów prywatnych](rooms.md#rozmowy-prywatne-pm).

## Obiekty

#### `Room`

| Pole           | Typ                                          | Opis                                                              |
|----------------|----------------------------------------------|-------------------------------------------------------------------|
| `id`           | `UUID`                                       | identyfikator pokoju                                              |
| `spaceId`      | `UUID`&#124;`null`                           | przestrzeń, do której należy; `null` dla rozmów prywatnych        |
| `name`         | `string`                                     | nazwa (do 50 znaków)                                              |
| `description`  | `string`                                     | opis (do 200 znaków)                                              |
| `type`         | `"Text"`&#124;`"ClassicText"`&#124;`"Pm"`    | [typ pokoju](rooms.md#typy-pokojów)                               |
| `defaultTopic` | [`Topic`](topics.md#topic)&#124;`null`       | temat domyślny (główny wątek pokoju)                              |
| `recipients`   | [`User[]`](users.md#user)&#124;`null`        | uczestnicy rozmowy prywatnej; `null` dla pokojów przestrzeni      |
| `flags`        | `int`                                        | maska bitowa [flag](rooms.md#flagi-pokoju)                        |
| `stream`       | [`RoomStream`](rooms.md#roomstream)&#124;`null` | powiązany strumień audio                                       |
| `history`      | [`RoomHistory`](rooms.md#roomhistory)&#124;`null` | polityka przechowywania historii                             |

#### Typy pokojów

| Typ           | Charakterystyka                                                                                 |
|---------------|-------------------------------------------------------------------------------------------------|
| `Text`        | pokój tekstowy z wieloma [tematami](topics.md) – wątki tworzone są przez użytkowników            |
| `ClassicText` | pokój z jednym, domyślnym tematem – model klasycznego czatu bez wątków                           |
| `Pm`          | [rozmowa prywatna](rooms.md#rozmowy-prywatne-pm) poza przestrzenią                               |

#### Flagi pokoju

| Bit      | Wartość | Nazwa                 | Znaczenie                                                              |
|----------|---------|-----------------------|------------------------------------------------------------------------|
| `1 << 0` | `1`     | `AllowSystemMessages` | publikuj w pokoju [wiadomości systemowe](messages.md#typy-wiadomości)   |
| `1 << 1` | `2`     | `Private`             | pokój nie jest widoczny na liście pokojów przestrzeni                  |
| `1 << 2` | `4`     | `InvitationOnly`      | wejście wyłącznie na [zaproszenie](rooms.md#zaproszenia); wymaga `Private` |

#### `RoomHistory`

| Pole      | Typ                                              | Opis                                                    |
|-----------|--------------------------------------------------|---------------------------------------------------------|
| `mode`    | `"Full"`&#124;`"Ephemeral"`&#124;`"MaxAge"`      | tryb przechowywania                                     |
| `maxAge`  | `int`&#124;`null`                                | czas życia wiadomości w sekundach (tylko dla `MaxAge`)  |

W trybie `Ephemeral` wiadomości nie są zapisywane – docierają wyłącznie do klientów obecnych w pokoju, a
[`GetMessages`](messages.md#pobieranie-historii) zwróci pustą listę. Bot archiwizujący musi w takich pokojach
konsumować zdarzenia `NewMessage` na bieżąco.

#### `RoomStream`

| Pole   | Typ                                | Opis                        |
|--------|------------------------------------|-----------------------------|
| `type` | `"Direct"`&#124;`"Shoutcast"`      | rodzaj strumienia           |
| `url`  | `string`                           | adres strumienia            |

#### `RoomMember`

| Pole           | Typ                                            | Opis                                                                   |
|----------------|------------------------------------------------|------------------------------------------------------------------------|
| `user`         | [`User`](users.md#user)&#124;`null`            | dane użytkownika; `null` gdy dostępne przez `spaceMember`               |
| `spaceMember`  | [`SpaceMember`](spaces.md#spacemember)&#124;`null` | członkostwo w przestrzeni (role, profil przestrzenny)              |
| `roles`        | `UUID[]`&#124;`null`                           | [role](roles.md) przypisane wyłącznie w tym pokoju                      |
| `customNick`   | `string`&#124;`null`                           | pseudonim w tym pokoju                                                 |
| `customColor`  | `string`&#124;`null`                           | kolor pseudonimu w formacie HEX                                        |
| `customAvatar` | `UUID`&#124;`null`                             | [ID pliku](files.md#awatary-członków) awatara w tym pokoju             |
| `extras`       | `string`                                       | dowolne dane pomocnicze członka (wykorzystywane przez boty-mostki)     |

!> Dane użytkownika mogą być w `user` **albo** w `spaceMember.user` – serwer nie duplikuje ich, by ograniczyć
wielkość zdarzeń. Napisz jedną funkcję pomocniczą rozwiązującą użytkownika i używaj jej wszędzie.

#### `RoomSummary`

| Pole          | Typ                | Opis                                    |
|---------------|--------------------|-----------------------------------------|
| `id`          | `UUID`             | identyfikator pokoju                    |
| `spaceId`     | `UUID`&#124;`null` | identyfikator przestrzeni               |
| `name`        | `string`           | nazwa                                   |
| `description` | `string`           | opis                                    |
| `memberCount` | `int`              | liczba członków                         |
| `type`        | `RoomType`         | [typ pokoju](rooms.md#typy-pokojów)     |
| `extras`      | `object`&#124;`null` | `{ isPrivate: bool, isInvitationOnly: bool }` |

## Wejście do pokoju

`JoinRoom` służy do dwóch scenariuszy:

* **wejście własne** – podaj samo `id`; nadawca otrzyma `RoomJoined`, a obecni w pokoju `RoomMembersJoined`;
* **dodanie innych** – podaj `userIds` i/lub `roleIds`; nadawca otrzyma [`Ok`](protocol.md#zdarzenie-ok),
  a dodane osoby oraz obecni w pokoju – `RoomMembersJoined` z wypełnionym `addedByUserId`.

Drugi tryb wymaga uprawnienia `AddMembers` i jest podstawowym narzędziem bota onboardingowego: pozwala jedną
komendą wprowadzić do pokoju wszystkich posiadaczy danej roli.

#### `JoinRoom`

| Pole      | Typ        | Opis                                                                                     |
|-----------|------------|------------------------------------------------------------------------------------------|
| `id`      | `UUID`     | identyfikator pokoju                                                                     |
| `userIds` | `string[]` | ID użytkowników do dodania zamiast samego siebie; wymaga `AddMembers`                    |
| `roleIds` | `UUID[]`   | ID ról, których posiadacze mają zostać dodani; rola domyślna `@everyone` nie jest wspierana |

<details><summary>Przykład <code>JoinRoom</code></summary>

```json
{
  "type": "JoinRoom",
  "ref": "7",
  "data": { "id": "7hK9pQ2vXnR4tY6uW1sZbA" }
}
```

</details>

#### `RoomJoined`

| Pole   | Typ                      | Opis                                          |
|--------|--------------------------|-----------------------------------------------|
| `room` | [`Room`](rooms.md#room)  | pełny obiekt pokoju, w tym `defaultTopic`     |

#### `RoomMembersJoined`

| Pole            | Typ                                   | Opis                                                            |
|-----------------|---------------------------------------|-----------------------------------------------------------------|
| `roomId`        | `UUID`                                | identyfikator pokoju                                            |
| `members`       | [`RoomMember[]`](rooms.md#roommember) | nowi członkowie                                                 |
| `addedByUserId` | `string`&#124;`null`                  | kto ich dodał; `null` gdy weszli samodzielnie                   |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                                       |
|------------------------------|------------------------------------------------------------|
| `RoomNotFoundException`      | pokój nie istnieje lub jest niewidoczny dla użytkownika     |
| `UserExistsAlreadyException` | użytkownik jest już w pokoju                                |
| `UserNotFoundException`      | użytkownik nie należy do przestrzeni pokoju                 |
| `AccessDeniedException`      | brak `AddMembers` albo brak zaproszenia do pokoju `InvitationOnly` |
| `BannedAccessException`      | aktywny ban w pokoju lub przestrzeni                        |

## Wyjście z pokoju

`LeaveRoom` usuwa nadawcę z pokoju. Nadawca otrzymuje `RoomLeft`, pozostali – `RoomMemberLeft`.

#### `LeaveRoom`

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

#### `RoomLeft`

| Pole     | Typ                                        | Opis                     |
|----------|--------------------------------------------|--------------------------|
| `id`     | `UUID`                                     | identyfikator pokoju     |
| `reason` | [`LeaveReason`](connection.md#leavereason) | powód opuszczenia        |

`RoomLeft` otrzymasz również bez własnej komendy: przy wyrzuceniu (`Kick`), banie (`Ban`) oraz po opuszczeniu
przestrzeni (`SpaceLeave`). Zawsze sprawdzaj `reason.type`, zanim bot spróbuje wrócić do pokoju.

#### `RoomMemberLeft`

| Pole     | Typ      | Opis                 |
|----------|----------|----------------------|
| `roomId` | `UUID`   | identyfikator pokoju |
| `userId` | `string` | ID użytkownika       |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                        |
|-------------------------|-----------------------------|
| `RoomNotFoundException` | pokój nie istnieje          |
| `UserNotFoundException` | użytkownika nie ma w pokoju |

## Tworzenie pokoju

`CreateRoom` tworzy pokój w przestrzeni albo otwiera [rozmowę prywatną](rooms.md#rozmowy-prywatne-pm) – decyduje
pole `type`. Twórca zostaje właścicielem pokoju i automatycznie do niego wchodzi, otrzymując
[`RoomJoined`](rooms.md#roomjoined). Członkowie przestrzeni dostają `NewRoom` (poza pokojami prywatnymi).

Utworzenie pokoju w przestrzeni wymaga uprawnienia `CreateRooms`.

#### `CreateRoom`

| Pole           | Typ                    | Opis                                                                    |
|----------------|------------------------|-------------------------------------------------------------------------|
| `type`         | `RoomType`             | `Text`, `ClassicText` lub `Pm`                                          |
| `spaceId`      | `UUID`&#124;`null`     | wymagane dla `Text`/`ClassicText`, niedozwolone dla `Pm`                |
| `recipientIds` | `string[]`&#124;`null` | wymagane dla `Pm`, niedozwolone dla pozostałych typów                    |
| `name`         | `string`&#124;`null`   | nazwa pokoju (do 50 znaków)                                             |
| `description`  | `string`&#124;`null`   | opis (do 200 znaków)                                                    |
| `flags`        | `int`&#124;`null`      | [flagi](rooms.md#flagi-pokoju); niedozwolone dla `Pm`                   |

#### `NewRoom`

| Pole      | Typ                                   | Opis                          |
|-----------|---------------------------------------|-------------------------------|
| `spaceId` | `UUID`                                | przestrzeń, w której powstał  |
| `summary` | [`RoomSummary`](rooms.md#roomsummary) | skrócony opis nowego pokoju   |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                          | Opis                                                 |
|------------------------------|------------------------------------------------------|
| `AccessDeniedException`      | brak uprawnienia `CreateRooms`                       |
| `SpaceNotFoundException`     | przestrzeń nie istnieje                              |
| `UserNotFoundException`      | użytkownik nie jest członkiem przestrzeni            |
| `RoomExistsAlreadyException` | pokój o takim ID już istnieje                        |
| `ProtocolException`          | niedozwolona kombinacja pól dla wybranego typu       |

### Rozmowy prywatne (`Pm`)

Rozmowa prywatna to pokój bez przestrzeni, o stałym zestawie uczestników. Aby ją otworzyć, wyślij `CreateRoom`
z `type: "Pm"` i listą `recipientIds`.

Komenda jest **idempotentna**: jeśli rozmowa o dokładnie takim zestawie uczestników już istnieje, serwer nie
tworzy nowej, tylko wprowadza Cię do istniejącej. Bot nie musi więc pamiętać ID rozmów – wystarczy, że zna
identyfikatory rozmówców.

Odbiorcy dołączają do rozmowy dopiero w momencie odebrania pierwszej wiadomości i tylko wtedy, gdy pozwala na to
ich [polityka wiadomości prywatnych](users.md#dane-użytkownika) oraz brak
[relacji ignorowania](users.md#relacje). W przeciwnym razie wiadomość zostaje wysłana, ale rozmówca jej nie
zobaczy – nie jest to zgłaszane jako błąd, więc bot nie może traktować braku odpowiedzi jako awarii.

## Edycja pokoju

`UpdateRoom` zmienia konfigurację pokoju zgodnie z
[semantyką aktualizacji częściowej](protocol.md#semantyka-pól-w-komendach-updatex). Członkowie pokoju otrzymują
`RoomUpdated`, a członkowie przestrzeni `RoomSummaryUpdated`.

Wymaga uprawnienia `ManageRoom`.

#### `UpdateRoom`

| Pole          | Typ                                             | Opis                                             |
|---------------|-------------------------------------------------|--------------------------------------------------|
| `id`          | `UUID`                                          | identyfikator pokoju                             |
| `name`        | `string`                                        | nowa nazwa                                       |
| `description` | `string`                                        | nowy opis                                        |
| `flags`       | `int`                                           | nowa maska [flag](rooms.md#flagi-pokoju)         |
| `stream`      | [`RoomStream`](rooms.md#roomstream)&#124;`null` | strumień; `null` odłącza                         |
| `history`     | [`RoomHistory`](rooms.md#roomhistory)           | polityka historii                                |

#### `RoomUpdated`

| Pole   | Typ                     | Opis                |
|--------|-------------------------|---------------------|
| `room` | [`Room`](rooms.md#room) | pokój po zmianach   |

#### `RoomSummaryUpdated`

| Pole      | Typ                                   | Opis                                                                  |
|-----------|---------------------------------------|-----------------------------------------------------------------------|
| `summary` | [`RoomSummary`](rooms.md#roomsummary) | **częściowy** obiekt – scal go z zapamiętanym; pokoje prywatne go nie emitują |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                              |
|-------------------------|-----------------------------------|
| `AccessDeniedException` | brak uprawnienia `ManageRoom`     |
| `RoomNotFoundException` | pokój nie istnieje                |
| `RoomTypeException`     | typ pokoju nie wspiera tej zmiany |

## Usuwanie pokoju

`DeleteRoom` trwale usuwa pokój wraz z tematami i historią. Wszyscy członkowie otrzymują `RoomDeleted`.

Wymaga uprawnienia `ManageRoom`.

#### `DeleteRoom`

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

#### `RoomDeleted`

| Pole | Typ    | Opis                            |
|------|--------|---------------------------------|
| `id` | `UUID` | identyfikator usuniętego pokoju |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                          |
|-------------------------|-------------------------------|
| `AccessDeniedException` | brak uprawnienia `ManageRoom` |
| `RoomNotFoundException` | pokój nie istnieje            |

## Lista członków pokoju

`GetRoomMembers` zwraca komplet członków pokoju. Utrzymuj listę na podstawie zdarzeń `RoomMembersJoined`,
`RoomMemberLeft` i `RoomMemberUpdated`, a pełne pobranie wykonuj po każdym
[ponownym połączeniu](connection.md#ponowne-połączenie).

#### `GetRoomMembers`

| Pole | Typ    | Opis                 |
|------|--------|----------------------|
| `id` | `UUID` | identyfikator pokoju |

#### `RoomMembers`

| Pole      | Typ                                   | Opis                 |
|-----------|---------------------------------------|----------------------|
| `id`      | `UUID`                                | identyfikator pokoju |
| `members` | [`RoomMember[]`](rooms.md#roommember) | lista członków       |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                                 |
|-------------------------|--------------------------------------|
| `UserNotFoundException` | użytkownik nie znajduje się w pokoju |

## Podgląd pokoju

`GetRoomSummary` zwraca [`RoomSummary`](rooms.md#roomsummary) pojedynczego pokoju bez wchodzenia do niego.

#### `GetRoomSummary`

| Pole       | Typ                                             | Opis                                    |
|------------|-------------------------------------------------|-----------------------------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja na warstwie `Room`   |

#### `RoomSummary` (zdarzenie)

| Pole      | Typ                                   | Opis          |
|-----------|---------------------------------------|---------------|
| `summary` | [`RoomSummary`](rooms.md#roomsummary) | dane pokoju   |

### Możliwe kody błędów

| Kod                     | Opis                                           |
|-------------------------|------------------------------------------------|
| `RoomNotFoundException` | pokój nie istnieje lub jest niewidoczny        |

## Profil członka pokoju

`UpdateRoomMember` nadpisuje pseudonim, kolor, awatar lub dane pomocnicze użytkownika **w obrębie jednego pokoju**.
Nadpisania mają pierwszeństwo przed [profilem przestrzeni](spaces.md#profil-członka-przestrzeni).

Członkowie pokoju otrzymują `RoomMemberUpdated`.

Uprawnienia:

* własny pseudonim lub awatar – `ChangeOwnProfile` albo `ManageMemberProfiles`;
* własny kolor – `ChangeOwnColor` albo `ManageMemberProfiles`;
* pole `extras` może zmieniać wyłącznie sam członek.

#### `UpdateRoomMember`

| Pole           | Typ                  | Opis                                                       |
|----------------|----------------------|------------------------------------------------------------|
| `roomId`       | `UUID`               | identyfikator pokoju                                       |
| `userId`       | `string`             | ID użytkownika                                             |
| `customNick`   | `string`&#124;`null` | pseudonim (1–32 znaki); `null` usuwa nadpisanie            |
| `customColor`  | `string`&#124;`null` | kolor HEX (np. `#ff0000`); `null` usuwa nadpisanie         |
| `customAvatar` | `UUID`&#124;`null`   | [ID pliku](files.md#awatary-członków); `null` usuwa nadpisanie |
| `extras`       | `string`&#124;`null` | dane pomocnicze członka                                    |

#### `RoomMemberUpdated`

| Pole     | Typ                                 | Opis                  |
|----------|-------------------------------------|-----------------------|
| `roomId` | `UUID`                              | identyfikator pokoju  |
| `userId` | `string`                            | ID użytkownika        |
| `member` | [`RoomMember`](rooms.md#roommember) | członek po zmianach   |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                                                         |
|-------------------------|--------------------------------------------------------------|
| `AccessDeniedException` | brak wymaganego uprawnienia lub próba zmiany cudzych `extras` |
| `RoomNotFoundException` | pokój nie istnieje lub użytkownik nie jest jego członkiem     |
| `MemberAvatarException` | awatar nie spełnia wymagań                                    |

## Zaproszenia

Pokoje z flagą `InvitationOnly` wpuszczają wyłącznie osoby z ważnym zaproszeniem. Zaproszenie może dotyczyć
konkretnego użytkownika albo całej [roli](roles.md).

Zaproszeniami zarządzać może każdy członek pokoju oraz każdy, kto sam posiada do niego zaproszenie. Wszystkie trzy
komendy odpowiadają tym samym zdarzeniem `Invited`, zawierającym **pełną, aktualną listę** – klient nie musi
scalać zmian przyrostowo.

#### `Invite`

| Pole      | Typ        | Opis                                                                   |
|-----------|------------|------------------------------------------------------------------------|
| `roomId`  | `UUID`     | identyfikator pokoju                                                   |
| `userIds` | `string[]` | ID użytkowników do zaproszenia                                         |
| `roleIds` | `UUID[]`   | ID ról do zaproszenia; rola domyślna `@everyone` nie jest wspierana    |

#### `Uninvite`

Te same pola co `Invite` – cofa wskazane zaproszenia.

#### `GetInvited`

| Pole     | Typ    | Opis                 |
|----------|--------|----------------------|
| `roomId` | `UUID` | identyfikator pokoju |

#### `Invited`

| Pole       | Typ                                                    | Opis                                                              |
|------------|--------------------------------------------------------|-------------------------------------------------------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja pokoju                                                |
| `userIds`  | `string[]`                                             | ID zaproszonych użytkowników – połącz z listą członków przestrzeni |
| `roles`    | [`Role[]`](roles.md#role)                              | zaproszone role (pełne obiekty)                                   |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                                                |
|--------------------------|-----------------------------------------------------|
| `RoomNotFoundException`  | pokój nie istnieje                                  |
| `RoomTypeException`      | pokój nie obsługuje zaproszeń (brak `InvitationOnly`) |
| `AccessDeniedException`  | brak prawa do zarządzania zaproszeniami             |
| `DefaultRoleException`   | próba zaproszenia roli domyślnej                    |
