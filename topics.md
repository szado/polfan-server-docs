# Tematy

Temat (`Topic`) to wątek rozmowy wewnątrz [pokoju](rooms.md). Wiadomości i historia zawsze należą do konkretnego
tematu, a nie do pokoju.

Każdy pokój ma **temat domyślny** (`defaultTopic`) – główny wątek, w którym toczy się rozmowa. W pokojach typu
`Text` i `Pm` użytkownicy mogą dodatkowo zakładać nowe tematy, odgałęziając je od istniejącej wiadomości.
Pokoje `ClassicText` mają wyłącznie temat domyślny.

Dla bota temat jest jednostką, na której operują komendy związane z wiadomościami: publikowanie,
[historia](messages.md#pobieranie-historii), [potwierdzanie odczytu](topics.md#potwierdzanie-odczytu) oraz
[obserwowanie](topics.md#obserwowanie-tematów). Adresuje się go
[lokalizacją](protocol.md#lokalizacja-chatlocation) na warstwie `Topic`.

#### `Topic`

| Pole           | Typ                                        | Opis                                                             |
|----------------|--------------------------------------------|------------------------------------------------------------------|
| `id`           | `UUID`                                     | identyfikator tematu                                             |
| `name`         | `string`&#124;`null`                       | nazwa (1–1000 znaków); pusta dla tematu domyślnego               |
| `messageCount` | `int`                                      | liczba wiadomości w temacie                                      |
| `refMessage`   | [`Message`](messages.md#message)&#124;`null` | wiadomość, od której odgałęziono temat                        |
| `lastMessage`  | [`Message`](messages.md#message)&#124;`null` | ostatnia wiadomość – pozwala wyświetlić podgląd bez pobierania historii |

## Tworzenie tematu

`CreateTopic` zakłada nowy wątek w pokoju. W pokojach `Text` i `Pm` temat **zawsze** wyrasta z konkretnej
wiadomości: podanie `refMessageId` oraz `initialMessage` jest obowiązkowe. Dzięki temu wątek ma kontekst, a
wiadomość źródłowa zyskuje odnośnik (`topicRef`), po którym klienci wyświetlają wejście do wątku.

Wymaga uprawnień `CreateTopics` **oraz** `CreateMessages`.

Członkowie pokoju otrzymują `NewTopic`.

#### `CreateTopic`

| Pole             | Typ                                                    | Opis                                                        |
|------------------|--------------------------------------------------------|-------------------------------------------------------------|
| `location`       | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja na warstwie `Room`                              |
| `name`           | `string`                                               | nazwa tematu (1–1000 znaków)                                |
| `refMessageId`   | `UUID`&#124;`null`                                     | wiadomość, od której odgałęziany jest temat                 |
| `initialMessage` | `object`&#124;`null`                                   | pierwsza wiadomość: `{ content, attachments }`              |

<details><summary>Przykład <code>CreateTopic</code></summary>

```json
{
  "meta": { "type": "CreateTopic", "ref": "12" },
  "data": {
    "location": { "roomId": "7hK9pQ2vXnR4tY6uW1sZbA" },
    "name": "Zgłoszenie #4213",
    "refMessageId": "2vXnR4tY6uW1sZbA7hK9pQ",
    "initialMessage": { "content": "Przenoszę wątek tutaj." }
  }
}
```

</details>

#### `NewTopic`

| Pole     | Typ                        | Opis                 |
|----------|----------------------------|----------------------|
| `roomId` | `UUID`                     | identyfikator pokoju |
| `topic`  | [`Topic`](topics.md#topic) | nowy temat           |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                                | Opis                                                                        |
|------------------------------------|-----------------------------------------------------------------------------|
| `AccessDeniedException`            | brak `CreateTopics` lub `CreateMessages`                                    |
| `RoomNotFoundException`            | pokój nie istnieje                                                          |
| `UserNotFoundException`            | użytkownik nie jest członkiem pokoju                                        |
| `RoomTypeException`                | pokój nie wspiera tworzenia tematów (`ClassicText`)                         |
| `InvalidInitialMessageException`   | brak wymaganej wiadomości początkowej lub referencji                        |
| `InvalidMessageReferenceException` | wiadomość nie istnieje, ma już wątek, jest z innego pokoju lub go nie wspiera |
| `TopicExistsAlreadyException`      | temat o takim ID już istnieje                                               |

## Edycja tematu

`UpdateTopic` zmienia nazwę tematu. Członkowie pokoju otrzymują `TopicUpdated`.

Wymaga uprawnienia `ManageTopic`.

#### `UpdateTopic`

| Pole       | Typ                                                    | Opis                            |
|------------|--------------------------------------------------------|---------------------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja na warstwie `Topic` |
| `name`     | `string`                                               | nowa nazwa                      |

#### `TopicUpdated`

| Pole       | Typ                                                    | Opis                 |
|------------|--------------------------------------------------------|----------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja tematu   |
| `topic`    | [`Topic`](topics.md#topic)                             | temat po zmianach    |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                      | Opis                           |
|--------------------------|--------------------------------|
| `AccessDeniedException`  | brak uprawnienia `ManageTopic` |
| `TopicNotFoundException` | temat nie istnieje             |

## Pobieranie tematów

`GetTopics` zwraca wskazane tematy pokoju wraz z licznikami i ostatnimi wiadomościami. Użyj jej, gdy z listy
[obserwowanych tematów](topics.md#obserwowanie-tematów) masz identyfikatory, a potrzebujesz nazw i podglądu.

#### `GetTopics`

| Pole       | Typ      | Opis                      |
|------------|----------|---------------------------|
| `roomId`   | `UUID`   | identyfikator pokoju      |
| `topicIds` | `UUID[]` | identyfikatory tematów    |

#### `Topics`

| Pole       | Typ                                                    | Opis                  |
|------------|--------------------------------------------------------|-----------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja pokoju    |
| `topics`   | [`Topic[]`](topics.md#topic)                           | znalezione tematy     |

Tematy, których nie znaleziono, są pomijane – lista wynikowa może być krótsza od żądanej.

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                     | Opis                                 |
|-------------------------|--------------------------------------|
| `RoomNotFoundException` | pokój nie istnieje                   |
| `UserNotFoundException` | użytkownik nie jest członkiem pokoju |

## Usuwanie tematu

Komenda `DeleteTopic` (pole `location` na warstwie `Topic`, zdarzenie zwrotne `TopicDeleted`) jest zarezerwowana
w protokole, ale **nie jest jeszcze zaimplementowana** – serwer odpowiada błędem. Aby usunąć wątek wraz z historią,
usuń [pokój](rooms.md#usuwanie-pokoju).

## Obserwowanie tematów

Obserwowanie (`follow`) to mechanizm, dzięki któremu klient wie, gdzie czekają nieprzeczytane treści, bez
pobierania historii wszystkich tematów. Dla bota powiadomieniowego jest to podstawowe źródło informacji o tym,
co wymaga reakcji.

Serwer **automatycznie** dodaje obserwację tematu użytkownikowi, który w nim napisał, oraz każdemu, kto został
w wiadomości bezpośrednio [wspomniany](messages.md#wzmianki). Ręczne obserwowanie przydaje się, gdy bot chce
śledzić wątek, w którym się nie odzywa.

#### `FollowedTopic`

| Pole                | Typ                                                    | Opis                                                     |
|---------------------|--------------------------------------------------------|----------------------------------------------------------|
| `location`          | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja tematu                                       |
| `lastAckMessageId`  | `UUID`&#124;`null`                                     | ostatnia potwierdzona wiadomość                          |
| `isUnread`          | `boolean`                                              | czy temat zawiera nieprzeczytane wiadomości              |
| `mentionCount`      | `int`                                                  | liczba nieprzeczytanych wzmianek o użytkowniku           |
| `notificationLevel` | `"All"`&#124;`"Mentions"`&#124;`"None"`                | poziom powiadomień                                       |
| `missed`            | `int`&#124;`null`                                      | starszy licznik: `1` gdy nieprzeczytane, `0` w przeciwnym razie |

### `FollowTopic`

Rozpoczyna obserwację tematu.

| Pole       | Typ                                                    | Opis                            |
|------------|--------------------------------------------------------|---------------------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja na warstwie `Topic` |

Odpowiedź: `TopicFollowed` z polem `followedTopic` ([`FollowedTopic`](topics.md#followedtopic)). Zdarzenie
dociera do **wszystkich sesji** użytkownika, więc każde jego urządzenie od razu zna nowy stan.

### `UnfollowTopic`

Kończy obserwację. Pola jak wyżej. Odpowiedź: `TopicUnfollowed` z polem `location`.

### `UpdateFollowedTopic`

Zmienia poziom powiadomień obserwowanego tematu.

| Pole                | Typ                                                    | Opis                                 |
|---------------------|--------------------------------------------------------|--------------------------------------|
| `location`          | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja na warstwie `Topic`      |
| `notificationLevel` | `"All"`&#124;`"Mentions"`&#124;`"None"`                | nowy poziom powiadomień              |

Odpowiedź: `FollowedTopicUpdated` z polem `followedTopic`.

### `GetFollowedTopics`

Zwraca obserwowane tematy w podanym zakresie. Lokalizacja wyznacza zakres: warstwa `Global` (pusty obiekt) zwraca
wszystkie, `Space` – z jednej przestrzeni, `Room` – z jednego pokoju.

| Pole       | Typ                                                    | Opis                |
|------------|--------------------------------------------------------|---------------------|
| `location` | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | zakres wyszukiwania |

#### `FollowedTopics`

| Pole             | Typ                                                    | Opis                        |
|------------------|--------------------------------------------------------|-----------------------------|
| `location`       | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | zakres, którego dotyczy odpowiedź |
| `followedTopics` | [`FollowedTopic[]`](topics.md#followedtopic)           | obserwowane tematy          |

**Wzorzec startowy dla bota:** po odebraniu [`Session`](connection.md#zdarzenie-session) wyślij `GetFollowedTopics`
z pustą lokalizacją, aby jednym zapytaniem poznać wszystkie miejsca z zaległościami, a następnie nadrobić je
komendą [`GetMessages`](messages.md#pobieranie-historii).

## Potwierdzanie odczytu

`Ack` oznacza temat jako przeczytany do wskazanej wiadomości. To ta sama operacja, którą wykonuje klient czatu,
gdy użytkownik przewinie wątek – bot powinien jej używać, aby jego własne liczniki nieprzeczytanych nie rosły
w nieskończoność.

#### `Ack`

| Pole        | Typ                                                    | Opis                                                        |
|-------------|--------------------------------------------------------|-------------------------------------------------------------|
| `location`  | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | lokalizacja na warstwie `Topic`                             |
| `messageId` | `UUID`&#124;`null`                                     | potwierdź do tej wiadomości; `null` potwierdza cały temat    |

Odpowiedź: `FollowedTopicUpdated` rozesłane do wszystkich sesji użytkownika. Jeśli temat nie jest obserwowany,
nie ma czego aktualizować i serwer odpowiada [`Ok`](protocol.md#zdarzenie-ok).

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                             | Opis                                                                       |
|---------------------------------|----------------------------------------------------------------------------|
| `UserNotFoundException`         | użytkownik nie jest członkiem pokoju                                       |
| `MessageNotFoundException`      | wiadomość nie istnieje lub jej znacznik czasu jest z przyszłości           |
| `MessageExistsAlreadyException` | ta lub nowsza wiadomość została już potwierdzona                           |
| `PermissionLayerException`      | lokalizacja nie wskazuje tematu                                            |

?> `MessageExistsAlreadyException` przy `Ack` nie jest awarią – oznacza, że stan już jest aktualny. Traktuj go
jak sukces.
