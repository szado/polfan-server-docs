# Wiadomości

Wiadomość (`Message`) należy do [tematu](topics.md) i jest podstawową jednostką treści na czacie. Ta strona opisuje
publikowanie, pobieranie historii, wzmianki, [reakcje i ankiety](messages.md#reakcje) oraz
[usuwanie treści](messages.md#usuwanie-wiadomości-redakcja).

## Obiekty

#### `Message`

| Pole          | Typ                                                    | Opis                                                             |
|---------------|--------------------------------------------------------|------------------------------------------------------------------|
| `id`          | `UUID`                                                 | identyfikator (UUID v7 – [rośnie chronologicznie](protocol.md#identyfikatory)) |
| `location`    | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | pełna lokalizacja wiadomości                                     |
| `createdAt`   | `string`                                               | znacznik czasu w formacie ISO 8601                               |
| `type`        | `MessageType`                                          | [typ wiadomości](messages.md#typy-wiadomości)                    |
| `author`      | [`MessageAuthor`](messages.md#messageauthor)           | autor wraz z prezentacją                                         |
| `content`     | `string`&#124;`null`                                   | treść                                                            |
| `topicRef`    | `UUID`&#124;`null`                                     | temat odgałęziony od tej wiadomości                              |
| `attachments` | `UUID[]`&#124;`null`                                   | [ID plików](files.md#załączniki-w-wiadomościach)                 |
| `reactions`   | [`MessageReaction[]`](messages.md#messagereaction)     | zagregowane liczniki reakcji                                     |
| `poll`        | [`MessagePoll`](messages.md#messagepoll)&#124;`null`   | definicja ankiety, jeśli wiadomość jest ankietą                  |

#### `MessageAuthor`

| Pole         | Typ                     | Opis                                                                    |
|--------------|-------------------------|-------------------------------------------------------------------------|
| `user`       | [`User`](users.md#user) | konto autora                                                            |
| `customNick` | `string`&#124;`null`    | pseudonim użyty w tej wiadomości                                        |
| `color`      | `string`&#124;`null`    | kolor pseudonimu w HEX                                                  |

Serwer utrwala prezentację autora **w momencie wysłania**: późniejsza zmiana pseudonimu czy koloru nie zmienia
wyglądu starych wiadomości. Kolor wyznaczany jest kaskadowo – nadpisanie w wiadomości, potem
[profil w pokoju](rooms.md#profil-członka-pokoju), na końcu kolor roli o najwyższym priorytecie.

#### Typy wiadomości

| Typ                | Pochodzenie   | Opis                                                                  |
|--------------------|---------------|-----------------------------------------------------------------------|
| `Text`             | użytkownik    | zwykła wiadomość tekstowa                                             |
| `Poll`             | użytkownik    | wiadomość z [ankietą](messages.md#ankiety)                            |
| `Ephemeral`        | użytkownik    | wiadomość nietrwała – nie trafia do historii                          |
| `RoomJoin`         | system        | ktoś wszedł do pokoju                                                 |
| `RoomMemberAdd`    | system        | ktoś został dodany do pokoju; autorem jest dodany, `content` zawiera ID dodającego |
| `RoomLeave`        | system        | ktoś opuścił pokój                                                    |
| `SpaceJoin`        | system        | ktoś dołączył do przestrzeni                                          |
| `SpaceLeave`       | system        | ktoś opuścił przestrzeń                                               |
| `TopicChange`      | system        | zmiana tematu                                                         |
| `CustomNickChange` | system        | zmiana pseudonimu członka                                             |

Wiadomości systemowe pojawiają się tylko w pokojach z flagą
[`AllowSystemMessages`](rooms.md#flagi-pokoju). Bot moderacyjny powinien je ignorować przy analizie treści – nie
przechodzą przez parser [wzmianek](messages.md#wzmianki) i nie są pisane przez ludzi.

## Tworzenie wiadomości

`CreateMessage` publikuje wiadomość w temacie. Wszyscy członkowie pokoju, łącznie z nadawcą, otrzymują
`NewMessage`.

Wymaga uprawnienia `CreateMessages`, a dla ankiet dodatkowo `CreatePolls`.

#### `CreateMessage`

| Pole          | Typ                                                    | Opis                                                                  |
|---------------|--------------------------------------------------------|-----------------------------------------------------------------------|
| `location`    | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja na warstwie `Topic`                                       |
| `content`     | `string`                                               | treść, do 2500 znaków                                                 |
| `attachments` | `UUID[]`&#124;`null`                                   | do 5 [wcześniej przesłanych plików](files.md#załączniki-w-wiadomościach) |
| `customNick`  | `string`&#124;`null`                                   | pseudonim tylko dla tej wiadomości – **wyłącznie dla kont `bot`**     |
| `customColor` | `string`&#124;`null`                                   | kolor pseudonimu w HEX – **wyłącznie dla kont `bot`**                 |
| `poll`        | [`MessagePoll`](messages.md#messagepoll)&#124;`null`   | zamienia wiadomość w ankietę; `content` staje się pytaniem            |

Wiadomość z załącznikiem może mieć pustą treść; bez załącznika treść jest wymagana.

?> Pola `customNick` i `customColor` istnieją z myślą o **botach-mostkach**, które przekazują wiadomości z innych
systemów: pozwalają zaprezentować oryginalnego autora zamiast konta bota. Konto bez flagi `bot` otrzyma
`BotOnlyOperationException`.

<details><summary>Przykład <code>CreateMessage</code></summary>

```json
{
  "meta": { "type": "CreateMessage", "ref": "31" },
  "data": {
    "location": {
      "roomId": "7hK9pQ2vXnR4tY6uW1sZbA",
      "topicId": "9wZbA7hK9pQ2vXnR4tY6uW"
    },
    "content": "Zgłoszenie przyjęte, <@10493> zajmie się nim.",
    "attachments": null
  }
}
```

</details>

#### `NewMessage`

| Pole      | Typ                              | Opis              |
|-----------|----------------------------------|-------------------|
| `message` | [`Message`](messages.md#message) | nowa wiadomość    |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                             | Opis                                                          |
|---------------------------------|---------------------------------------------------------------|
| `AccessDeniedException`         | brak `CreateMessages` (lub `CreatePolls` dla ankiety)         |
| `TopicNotFoundException`        | temat nie istnieje                                            |
| `RoomNotFoundException`         | użytkownik nie jest członkiem pokoju                          |
| `BannedAccessException`         | aktywne [wyciszenie](moderation.md) w tej lokalizacji         |
| `AttachmentNotFoundException`   | co najmniej jeden załącznik nie istnieje                      |
| `BotOnlyOperationException`     | `customNick`/`customColor` użyte przez konto bez flagi `bot`  |
| `ProtocolException`             | przekroczone limity treści, załączników lub opcji ankiety     |

## Wzmianki

Wzmianki osadzane są bezpośrednio w treści:

| Składnia        | Znaczenie                                    |
|-----------------|----------------------------------------------|
| `<@userId>`     | wzmianka użytkownika (ID numeryczne)         |
| `<@&roleId>`    | wzmianka [roli](roles.md) (UUID)             |

Serwer parsuje wzmianki wyłącznie w wiadomościach typu `Text` i `Poll`, do 50 celów na wiadomość. Wzmianka
powoduje automatyczne [obserwowanie tematu](topics.md#obserwowanie-tematów) przez wskazanych użytkowników oraz
zwiększenie ich licznika `mentionCount`.

Ciąg, który nie jest poprawnym identyfikatorem, pozostaje zwykłym tekstem. Bot budujący treść powinien wstawiać
identyfikatory, nie pseudonimy – pseudonim zmienia się, identyfikator nie.

## Pobieranie historii

`GetMessages` zwraca wiadomości tematu. Kierunek pobierania wyznacza dokładnie jedno z pól `before`, `after`,
`around`; pominięcie wszystkich zwraca najnowsze wiadomości.

#### `GetMessages`

| Pole                 | Typ                                                    | Opis                                                        |
|----------------------|--------------------------------------------------------|-------------------------------------------------------------|
| `location`           | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja na warstwie `Topic`                             |
| `before`             | `UUID`&#124;`null`                                     | wiadomości starsze niż podana                               |
| `after`              | `UUID`&#124;`null`                                     | wiadomości nowsze niż podana                                |
| `around`             | `UUID`&#124;`null`                                     | wiadomości wokół podanej                                    |
| `limit`              | `int`&#124;`null`                                      | 1–50, domyślnie 50                                          |
| `includeMyReactions` | `boolean`&#124;`null`                                  | dołącz reakcje pytającego, oszczędzając osobne zapytanie    |

#### `Messages`

| Pole          | Typ                                                       | Opis                                                        |
|---------------|-----------------------------------------------------------|-------------------------------------------------------------|
| `location`    | [`ChatLocation`](protocol.md#lokalizacja-chatlocation)    | lokalizacja tematu                                          |
| `messages`    | [`Message[]`](messages.md#message)                        | wiadomości                                                  |
| `myReactions` | `object`&#124;`null`                                      | mapa `ID wiadomości → [UserReaction]`; tylko gdy zażądano   |

**Nadrabianie zaległości po rekonekcie:** zapamiętaj ID ostatniej przetworzonej wiadomości i po połączeniu wołaj
`GetMessages` z `after` w pętli, aż zwrócona lista będzie krótsza niż `limit`. Ponieważ identyfikatory rosną
chronologicznie, nie potrzebujesz osobnego kursora ani znaczników czasu.

W pokojach z historią [`Ephemeral`](rooms.md#roomhistory) odpowiedź będzie pusta – wiadomości nie są tam
utrwalane.

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                        | Opis                                    |
|----------------------------|-----------------------------------------|
| `TopicNotFoundException`   | temat nie istnieje                      |
| `UserNotFoundException`    | użytkownik nie jest członkiem pokoju    |
| `PermissionLayerException` | lokalizacja nie wskazuje tematu         |

## Usuwanie wiadomości (redakcja)

`RedactMessages` usuwa treść wiadomości. To podstawowe narzędzie bota moderacyjnego reagującego na spam lub
niedozwolone treści.

Wymaga uprawnienia `RedactMessages`.

Członkowie pokoju otrzymują `MessagesRedacted`.

#### `RedactMessages`

| Pole        | Typ    | Opis                     |
|-------------|--------|--------------------------|
| `messageId` | `UUID` | identyfikator wiadomości |

#### `MessagesRedacted`

| Pole       | Typ                                                    | Opis                              |
|------------|--------------------------------------------------------|-----------------------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja                       |
| `ids`      | `UUID[]`                                               | identyfikatory usuniętych wiadomości |

Zdarzenie zwraca **listę** identyfikatorów – jedna komenda może usunąć więcej niż jedną wiadomość (np. wiadomość
wraz z powiązanym wątkiem). Usuwaj z lokalnego cache wszystkie wskazane pozycje.

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                        | Opis                                |
|----------------------------|-------------------------------------|
| `AccessDeniedException`    | brak uprawnienia `RedactMessages`   |
| `MessageNotFoundException` | wiadomość nie istnieje              |

## Reakcje

Reakcja to głos oddany na wiadomość – emoji lub [emotikoną](emoticons.md). Ten sam mechanizm obsługuje
[ankiety](messages.md#ankiety): głos w ankiecie to reakcja o wartości równej opcji.

#### `MessageReaction`

Zagregowany licznik, dołączany do każdej wiadomości.

| Pole    | Typ                             | Opis                          |
|---------|---------------------------------|-------------------------------|
| `type`  | `"Emoji"`&#124;`"Emoticon"`     | rodzaj reakcji                |
| `value` | `string`                        | emoji lub ID emotikony (do 64 znaków) |
| `count` | `int`                           | liczba głosów                 |

#### `UserReaction`

`{ type, value }` – reakcja pojedynczego użytkownika, bez licznika.

### `React` i `Unreact`

Dodaje lub usuwa reakcję bieżącego użytkownika.

| Pole        | Typ                         | Opis                     |
|-------------|-----------------------------|--------------------------|
| `messageId` | `UUID`                      | identyfikator wiadomości |
| `type`      | `"Emoji"`&#124;`"Emoticon"` | rodzaj reakcji           |
| `value`     | `string`                    | emoji lub ID emotikony   |

Reagowanie na zwykłą wiadomość wymaga uprawnienia `React`. Głosowanie w ankiecie jest otwarte dla każdego, kto
ją widzi – bramkowane jest **utworzenie** ankiety (`CreatePolls`), nie udział w niej.

Obie komendy generują dwa zdarzenia:

#### `Reacted`

Trafia do wszystkich sesji głosującego, aby każde jego urządzenie znało własny stan bez pobierania historii.

| Pole        | Typ      | Opis                                                     |
|-------------|----------|----------------------------------------------------------|
| `messageId` | `UUID`   | identyfikator wiadomości                                 |
| `reaction`  | `object` | `{ type, value, isAdded }` – `isAdded` rozróżnia dodanie od usunięcia |

#### `ReactionUpdated`

Trafia do całego pokoju i jest **jedynym źródłem prawdy o liczniku**. Nie inkrementuj liczników samodzielnie na
podstawie `Reacted` – przy równoczesnych głosach rozjedziesz się ze stanem serwera.

| Pole        | Typ                                              | Opis                          |
|-------------|--------------------------------------------------|-------------------------------|
| `messageId` | `UUID`                                           | identyfikator wiadomości      |
| `reaction`  | [`MessageReaction`](messages.md#messagereaction) | aktualny stan licznika        |

### `GetReactionDetails`

Zwraca listę użytkowników, którzy oddali daną reakcję – w kolejności od najstarszego głosu.

| Pole        | Typ                         | Opis                        |
|-------------|-----------------------------|-----------------------------|
| `messageId` | `UUID`                      | identyfikator wiadomości    |
| `type`      | `"Emoji"`&#124;`"Emoticon"` | rodzaj reakcji              |
| `value`     | `string`                    | emoji lub ID emotikony      |
| `limit`     | `int`&#124;`null`           | liczba wyników              |
| `offset`    | `int`&#124;`null`           | przesunięcie (stronicowanie)|

#### `ReactionDetails`

| Pole        | Typ        | Opis                                                                  |
|-------------|------------|-----------------------------------------------------------------------|
| `messageId` | `UUID`     | identyfikator wiadomości                                              |
| `type`      | `string`   | rodzaj reakcji                                                        |
| `value`     | `string`   | wartość reakcji                                                       |
| `userIds`   | `string[]` | ID głosujących – pseudonimy weź z [listy członków](rooms.md#lista-członków-pokoju) |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                        | Opis                                                          |
|----------------------------|---------------------------------------------------------------|
| `MessageNotFoundException` | wiadomość nie istnieje                                        |
| `UserNotFoundException`    | użytkownik nie jest członkiem pokoju                          |
| `AccessDeniedException`    | brak uprawnienia `React`                                      |
| `BannedAccessException`    | aktywne wyciszenie w tej lokalizacji                          |
| `InvalidReactionException` | wartość spoza opcji ankiety                                   |

## Ankiety

Ankieta to wiadomość z dołączoną definicją opcji. Głosy zbierane są tym samym mechanizmem co
[reakcje](messages.md#reakcje), więc klient obsługujący reakcje obsługuje ankiety niemal bez dodatkowego kodu.

#### `MessagePoll`

| Pole             | Typ                                                    | Opis                                       |
|------------------|--------------------------------------------------------|--------------------------------------------|
| `multipleChoice` | `boolean`                                              | czy można wybrać więcej niż jedną opcję    |
| `options`        | [`MessagePollOption[]`](messages.md#messagepolloption) | 2–10 opcji                                 |

#### `MessagePollOption`

| Pole    | Typ                         | Opis                                       |
|---------|-----------------------------|--------------------------------------------|
| `type`  | `"Emoji"`&#124;`"Emoticon"` | rodzaj symbolu opcji                       |
| `value` | `string`                    | wartość reakcji odpowiadająca tej opcji    |
| `label` | `string`                    | etykieta opcji, do 160 znaków              |

Zasady głosowania:

* głosem jest [`React`](messages.md#react-i-unreact) z `type` i `value` **równym jednej z opcji** – inna wartość
  kończy się błędem `InvalidReactionException`;
* w ankiecie jednokrotnego wyboru (`multipleChoice: false`) nowy głos **przenosi** wcześniejszy, zamiast go
  dublować;
* wycofanie głosu to `Unreact` z tą samą wartością;
* wyniki odczytujesz z `reactions` wiadomości i ze zdarzeń `ReactionUpdated`, a listę głosujących z
  [`GetReactionDetails`](messages.md#getreactiondetails).

Utworzenie ankiety wymaga uprawnień `CreateMessages` i `CreatePolls`.
