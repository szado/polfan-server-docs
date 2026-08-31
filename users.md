# Użytkownicy

Ta strona opisuje obiekt użytkownika, mechanizm obecności oraz komendy służące do przechowywania ustawień:
[danych użytkownika](users.md#dane-użytkownika), [danych sesji](users.md#dane-sesji),
[danych klienta](users.md#dane-klienta) i [relacji](users.md#relacje).

#### `User`

| Pole     | Typ            | Opis                                                                   |
|----------|----------------|------------------------------------------------------------------------|
| `id`     | `string`       | identyfikator użytkownika (ciąg cyfr, nie UUID)                        |
| `nick`   | `string`       | globalna nazwa użytkownika                                             |
| `avatar` | `string`       | URL awatara                                                            |
| `tags`   | `string[]`     | globalne znaczniki konta, m.in. `bot` i `temp`                         |
| `status` | `int`          | [obecność](users.md#obecność)                                          |
| `online` | `boolean`      | **przestarzałe** – używaj `status`; pozostawione dla starszych klientów |

Znacznik `bot` odblokowuje operacje przeznaczone dla integracji (np.
[podmianę autora wiadomości](messages.md#tworzenie-wiadomości)) i pozwala klientom oznaczyć konto w interfejsie.
Znacznik `temp` oznacza konto tymczasowe (gość).

Zmiana globalnych danych konta rozsyłana jest zdarzeniem `UserUpdated` z polem `user`.

## Obecność

Pole `status` agreguje stan **wszystkich** sesji użytkownika:

| Wartość | Nazwa         | Znaczenie                                                                              |
|---------|---------------|----------------------------------------------------------------------------------------|
| `0`     | `Offline`     | brak połączenia i brak możliwości dostarczenia powiadomienia                            |
| `1`     | `Online`      | co najmniej jedna sesja ma aktywne połączenie                                           |
| `2`     | `OnlineAsync` | brak aktywnego połączenia, ale urządzenie z powiadomieniami push było niedawno aktywne  |

`OnlineAsync` mówi ważną rzecz: użytkownik nie odbierze wiadomości natychmiast, ale ją dostanie. Bot decydujący,
czy eskalować sprawę do innego moderatora, powinien traktować ten stan inaczej niż `Offline`.

Użytkownik może wyłączyć widoczność tego stanu ustawieniem
[`showAsyncPresence`](users.md#dane-użytkownika) – wtedy dla innych wygląda po prostu na `Offline`.

## Informacje o użytkowniku

`GetUserInfo` zwraca profil rozszerzony: kiedy konto było widziane po raz pierwszy i ostatni oraz w jakich
przestrzeniach i pokojach występuje. Zakres odpowiedzi zależy od uprawnień pytającego – to narzędzie moderacyjne,
nie publiczny profil.

* Użytkownik z globalnym `ManageBans` lub `Kick` otrzymuje pełny obraz, wraz z listą znanych aliasów konta.
* Pozostali widzą wyłącznie przestrzenie, w których sami mają `ManageBans`, `Kick` lub `ManageRoom`.

#### `GetUserInfo`

| Pole | Typ      | Opis           |
|------|----------|----------------|
| `id` | `string` | ID użytkownika |

#### `UserInfo`

| Pole   | Typ                                          | Opis            |
|--------|----------------------------------------------|-----------------|
| `info` | [`UserInformation`](users.md#userinformation) | dane profilu   |

#### `UserInformation`

| Pole           | Typ                                          | Opis                                                     |
|----------------|----------------------------------------------|----------------------------------------------------------|
| `user`         | [`User`](users.md#user)                      | konto                                                    |
| `firstSeenAt`  | `string`                                     | data pierwszego kontaktu (ISO 8601)                      |
| `lastSeenAt`   | `string`                                     | data ostatniej aktywności (ISO 8601)                     |
| `spaces`       | [`SpaceSummary[]`](spaces.md#spacesummary)   | przestrzenie widoczne dla pytającego                     |
| `rooms`        | [`RoomSummary[]`](rooms.md#roomsummary)      | pokoje widoczne dla pytającego                           |
| `knownAliases` | [`User[]`](users.md#user)&#124;`null`        | inne konta tej samej osoby; tylko dla globalnych moderatorów |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub `UserNotFoundException`.

## Dane użytkownika

Ustawienia konta obowiązujące we wszystkich sesjach. Odczyt: `GetUserData` (bez pól) → zdarzenie `UserData`.
Zapis: `SetUserData` → [`Ok`](protocol.md#zdarzenie-ok).

#### `UserData`

| Pole                   | Typ                                       | Opis                                                                 |
|------------------------|-------------------------------------------|----------------------------------------------------------------------|
| `privateMessagePolicy` | `"None"`&#124;`"Mutual"`&#124;`"All"`     | kto może rozpocząć [rozmowę prywatną](rooms.md#rozmowy-prywatne-pm)  |
| `showAsyncPresence`    | `boolean`                                 | czy inni widzą stan [`OnlineAsync`](users.md#obecność)               |

`privateMessagePolicy` przyjmuje wartości: `None` – nikt, `Mutual` – tylko osoby ze wspólnej przestrzeni,
`All` – każdy.

`SetUserData` przyjmuje te same pola; pominięte pozostają bez zmian.

## Dane sesji

Ustawienia pojedynczego połączenia – przede wszystkim rejestracja powiadomień push i stan skupienia klienta.
Odczyt: `GetSessionData` (bez pól) → zdarzenie `SessionData`. Zapis: `SetSessionData` →
[`Ok`](protocol.md#zdarzenie-ok).

#### `SetSessionData`

| Pole            | Typ                  | Opis                                                                       |
|-----------------|----------------------|----------------------------------------------------------------------------|
| `clientFocused` | `boolean`&#124;`null` | czy okno klienta jest aktywne; wpływa na decyzję o wysłaniu powiadomienia  |
| `push`          | `object`&#124;`null`  | rejestracja push: `{ token, active }`                                     |
| `platform`      | `string`&#124;`null`  | `web`, `ios`, `android` lub `desktop`                                     |

#### `SessionData`

| Pole       | Typ                  | Opis                                    |
|------------|----------------------|-----------------------------------------|
| `platform` | `string`&#124;`null` | platforma zgłoszona przy połączeniu     |
| `push`     | `object`&#124;`null` | aktualna rejestracja push               |

?> Platformę wygodniej podać raz, w [parametrze `p`](connection.md#parametry-połączenia) przy nawiązywaniu
połączenia. Pole `push.platform` jest przestarzałe – jeśli podasz oba, wygrywa pole `platform` najwyższego poziomu.

Boty serwerowe zwykle nie korzystają z push; pole `clientFocused` warto pozostawić niezmienione.

## Dane klienta

Dowolny obiekt JSON przechowywany po stronie serwera i przypisany do konta. Służy do synchronizacji ustawień
aplikacji między urządzeniami; serwer nie interpretuje jego zawartości.

* `SetClientData` – ciałem komendy jest **bezpośrednio** obiekt JSON do zapisania (nie jest opakowany w dodatkowe
  pole). Odpowiedź: [`Ok`](protocol.md#zdarzenie-ok).
* `GetClientData` (bez pól) – odpowiedź: zdarzenie `ClientData` z zapisanym obiektem.

Limit: **10 240 bajtów** po zakodowaniu do JSON. Przekroczenie lub przesłanie czegoś innego niż obiekt kończy się
błędem `ClientDataException`.

## Relacje

Relacja to trwałe powiązanie między dwoma kontami. Obecnie wspierany jest jeden typ: `Ignore`.

Ignorowanie ma konkretny skutek po stronie serwera: ignorowany użytkownik **nie może otworzyć** z Tobą nowej
[rozmowy prywatnej](rooms.md#rozmowy-prywatne-pm). Filtrowanie treści w pokojach wspólnych pozostaje po stronie
klienta.

#### `UserRelationship`

| Pole      | Typ                     | Opis                       |
|-----------|-------------------------|----------------------------|
| `refUser` | [`User`](users.md#user) | użytkownik, którego dotyczy relacja |
| `type`    | `"Ignore"`              | typ relacji                |

### `CreateRelationship`

| Pole        | Typ        | Opis                          |
|-------------|------------|-------------------------------|
| `refUserId` | `string`   | ID użytkownika                |
| `type`      | `"Ignore"` | typ relacji                   |

Odpowiedź: `NewRelationship` z polem `relationship`.

### `DeleteRelationship`

Te same pola. Odpowiedź: `RelationshipDeleted` z polem `relationship`.

### `GetRelationships`

Bez pól. Odpowiedź: `Relationships` z polem `relationships`
([`UserRelationship[]`](users.md#userrelationship)).

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                                  | Opis                             |
|--------------------------------------|----------------------------------|
| `UserNotFoundException`              | użytkownik nie istnieje          |
| `RelationshipExistsAlreadyException` | relacja tego typu już istnieje   |
| `RelationshipNotFoundException`      | relacja nie istnieje             |
