# Moderacja

Serwer udostępnia trzy niezależne narzędzia moderacyjne: **wyrzucenie** (`Kick`) usuwa użytkownika z lokalizacji,
**ban** blokuje mu do niej powrót, a **wyciszenie** (`Mute`) odbiera prawo publikowania, pozostawiając możliwość
czytania. Do usuwania samych treści służy [redakcja wiadomości](messages.md#usuwanie-wiadomości-redakcja).

Wszystkie operacje działają w [lokalizacji](protocol.md#lokalizacja-chatlocation) – od pojedynczego pokoju po
cały serwer (warstwa `Global`). Bot moderacyjny dobiera zasięg do skali problemu: spam w jednym pokoju to ban
pokojowy, ewidentne nadużycie – ban w przestrzeni.

#### `BanObject`

| Pole          | Typ                                                    | Opis                                                |
|---------------|--------------------------------------------------------|-----------------------------------------------------|
| `id`          | `UUID`                                                 | identyfikator bana – potrzebny do `Unban`           |
| `bannedUser`  | [`User`](users.md#user)                                | ukarany użytkownik                                  |
| `banningUser` | [`User`](users.md#user)&#124;`null`                    | moderator; `null` dla akcji systemowych             |
| `location`    | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | zasięg obowiązywania                                |
| `reason`      | `string`                                               | powód (do 200 znaków)                               |
| `expiresAt`   | `string`&#124;`null`                                   | data wygaśnięcia (ISO 8601); `null` = bezterminowo  |
| `createdAt`   | `string`                                               | data nałożenia (ISO 8601)                           |
| `type`        | `"Ban"`&#124;`"Mute"`                                  | rodzaj kary                                         |
| `parentId`    | `UUID`&#124;`null`                                     | ban nadrzędny, z którego ten wynika                 |

## Banowanie i wyciszanie

`Ban` nakłada karę w podanej lokalizacji. Dla `type: "Ban"` użytkownik zostaje dodatkowo natychmiast usunięty
z lokalizacji; wyciszenie (`type: "Mute"`) pozostawia go na miejscu, ale blokuje
[tworzenie wiadomości](messages.md#tworzenie-wiadomości) i [reagowanie](messages.md#reakcje).

Ban można nałożyć również na osobę, której w danym miejscu nie ma – zadziała prewencyjnie przy próbie wejścia.

Wymaga uprawnienia `ManageBans` w lokalizacji.

Odpowiedź: [`Ok`](protocol.md#zdarzenie-ok).

#### `Ban`

| Pole        | Typ                                                    | Opis                                                             |
|-------------|--------------------------------------------------------|------------------------------------------------------------------|
| `userId`    | `string`                                               | ID karanego użytkownika                                          |
| `location`  | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | zasięg kary                                                      |
| `reason`    | `string`                                               | powód (do 200 znaków)                                            |
| `expiresAt` | `string`&#124;`null`                                   | data wygaśnięcia (ISO 8601); pominięcie = bezterminowo           |
| `notify`    | `boolean`                                              | czy poinformować ukaranego o powodzie; domyślnie `true`          |
| `type`      | `"Ban"`&#124;`"Mute"`                                  | rodzaj kary; domyślnie `Ban`                                     |

Pole `notify` steruje treścią zdarzenia [`RoomLeft`/`SpaceLeft`](connection.md#leavereason), które otrzyma
ukarany: przy `true` powodem jest `Ban` wraz z pełnym obiektem kary, przy `false` – zwykłe `Leave`. Nie zmienia
to skutków kary, wyłącznie to, co widzi ukarany.

!> Nie można ukarać użytkownika, który sam posiada `ManageBans` w tej lokalizacji – serwer zwróci
`BanPermissionConflictException`. Zabezpiecza to przed wojnami moderatorów i przed przejęciem przestrzeni
przez zhakowane konto.

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                              | Opis                                                        |
|----------------------------------|-------------------------------------------------------------|
| `AccessDeniedException`          | brak uprawnienia `ManageBans`                               |
| `BanPermissionConflictException` | karany ma równorzędne uprawnienia moderacyjne               |
| `UserNotFoundException`          | użytkownik nie istnieje                                     |
| `BanExistsAlreadyException`      | taka kara już obowiązuje                                    |
| `OwnerException`                 | próba ukarania [właściciela](permissions.md#właściciele)     |
| `ProtocolException`              | powód przekracza 200 znaków                                 |

## Zdejmowanie kary

`Unban` usuwa karę wskazaną identyfikatorem. Lokalizację i wymagane uprawnienia serwer wyznacza z samego bana –
wystarczy `ManageBans` tam, gdzie ban obowiązuje.

Odpowiedź: [`Ok`](protocol.md#zdarzenie-ok).

#### `Unban`

| Pole | Typ    | Opis                |
|------|--------|---------------------|
| `id` | `UUID` | identyfikator bana  |

### Możliwe kody błędów

| Kod                       | Opis                          |
|---------------------------|-------------------------------|
| `BanNotFoundException`    | ban nie istnieje              |
| `AccessDeniedException`   | brak uprawnienia `ManageBans` |

## Lista kar

`GetBans` zwraca kary obowiązujące w lokalizacji. Wymaga uprawnienia `ManageBans`.

#### `GetBans`

| Pole       | Typ                                                    | Opis                                          |
|------------|--------------------------------------------------------|-----------------------------------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja                                   |
| `type`     | `"Ban"`&#124;`"Mute"`&#124;`null`                      | filtr rodzaju; pominięcie zwraca wszystkie    |

#### `Bans`

| Pole       | Typ                                                    | Opis          |
|------------|--------------------------------------------------------|---------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja   |
| `bans`     | [`BanObject[]`](moderation.md#banobject)               | lista kar     |

## Wyrzucanie (`Kick`)

`Kick` usuwa użytkownika z lokalizacji **bez** blokowania powrotu. Sprawdza się jako łagodna interwencja:
przerwanie eskalującej dyskusji, wyproszenie bota, wymuszenie ponownego wczytania stanu.

Wymaga uprawnienia `Kick` w lokalizacji.

Odpowiedź: [`Ok`](protocol.md#zdarzenie-ok). Wyrzucony otrzymuje [`RoomLeft`/`SpaceLeft`](connection.md#leavereason)
z powodem `Kick` (lub `Leave`, gdy `notify` jest `false`).

#### `Kick`

| Pole       | Typ                                                    | Opis                                              |
|------------|--------------------------------------------------------|---------------------------------------------------|
| `userId`   | `string`                                               | ID użytkownika                                    |
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | zasięg                                            |
| `reason`   | `string`                                               | powód (do 200 znaków)                             |
| `notify`   | `boolean`                                              | czy przekazać powód wyrzuconemu                   |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                          |
|-------------------------|-------------------------------|
| `AccessDeniedException` | brak uprawnienia `Kick`       |
| `UserNotFoundException` | użytkownik nie istnieje       |

## Zgłaszanie nadużyć

`ReportAbuse` przekazuje zgłoszenie do zespołu moderacji serwisu. Zgłosić można profil użytkownika albo konkretną
wiadomość; zgłoszenie trafia do wewnętrznego pokoju obsługi wraz z kontekstem.

Komenda nie wymaga żadnych uprawnień – dostępna jest dla każdego zalogowanego użytkownika. Obowiązuje jednak
**24-godzinny okres karencji** dla tego samego zgłoszenia od tego samego użytkownika; jego naruszenie kończy się
błędem `AbuseReportedAlreadyException`.

Odpowiedź: [`Ok`](protocol.md#zdarzenie-ok).

#### `ReportAbuse`

| Pole        | Typ                             | Opis                                        |
|-------------|---------------------------------|---------------------------------------------|
| `category`  | `AbuseCategory`                 | kategoria nadużycia                         |
| `target`    | `"Profile"`&#124;`"Message"`    | co jest zgłaszane                           |
| `userId`    | `string`&#124;`null`            | wymagane gdy `target` = `Profile`           |
| `messageId` | `UUID`&#124;`null`              | wymagane gdy `target` = `Message`           |

Dozwolone wartości `AbuseCategory`: `SpamOrScam`, `HarassmentOrBullying`, `HateSpeechOrExtremism`,
`ThreatsOrViolence`, `SelfHarmOrSuicide`, `SexualContent`, `SexualExploitationOfMinors`, `ChildSafety`,
`NonConsensualIntimateContent`, `PrivacyViolation`, `Impersonation`, `IllegalGoodsOrServices`,
`RegulatedGoodsOrServices`, `AccountSecurity`, `IntellectualProperty`, `Misinformation`.

?> Komenda jest opcjonalnym modułem serwera. Jeśli operator jej nie skonfigurował, wysłanie `ReportAbuse` zwróci
`ProtocolException` (nieznany typ wiadomości).

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                             | Opis                                                  |
|---------------------------------|-------------------------------------------------------|
| `AbuseReportedAlreadyException` | identyczne zgłoszenie wysłano w ciągu ostatniej doby  |
| `UserNotFoundException`         | zgłaszany użytkownik nie istnieje                     |
| `MessageNotFoundException`      | zgłaszana wiadomość nie istnieje                      |
