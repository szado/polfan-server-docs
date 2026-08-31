# Emotikony

Emotikona to obrazek dostępny pod krótką nazwą – można go wstawić w treści wiadomości albo użyć jako
[reakcji](messages.md#reakcje) o `type: "Emoticon"` i `value` równym ID emotikony.

Emotikony istnieją w dwóch zasięgach:

* **globalne** – dostępne na całym serwerze; tworzy je administracja,
* **przestrzeni** – dostępne dla członków jednej [przestrzeni](spaces.md); tworzą je jej moderatorzy.

#### `Emoticon`

| Pole      | Typ                     | Opis                                                        |
|-----------|-------------------------|-------------------------------------------------------------|
| `id`      | `UUID`                  | identyfikator – ta wartość trafia do `value` reakcji        |
| `spaceId` | `UUID`&#124;`null`      | przestrzeń; `null` dla emotikony globalnej                  |
| `name`    | `string`                | nazwa: 2–20 znaków, wyłącznie małe litery i cyfry           |
| `fileId`  | `UUID`                  | [ID pliku](files.md) z grafiką                              |
| `user`    | [`User`](users.md#user) | autor                                                       |
| `tag`     | `string`&#124;`null`    | znacznik pomocniczy nadany przez serwis plików              |

## Pobieranie emotikon

`GetEmoticons` zwraca emotikony w wybranym zasięgu: podaj `spaceId`, aby otrzymać zestaw przestrzeni, lub pomiń
pole, aby otrzymać zestaw globalny.

Klient powinien pobrać oba zestawy – globalny raz po połączeniu, a zestaw przestrzeni przy dołączaniu do niej –
i aktualizować je zdarzeniami `NewEmoticon` oraz `EmoticonDeleted`.

#### `GetEmoticons`

| Pole      | Typ                | Opis                                          |
|-----------|--------------------|-----------------------------------------------|
| `spaceId` | `UUID`&#124;`null` | przestrzeń; pominięcie zwraca zestaw globalny |

#### `Emoticons`

| Pole        | Typ                                                    | Opis                     |
|-------------|--------------------------------------------------------|--------------------------|
| `emoticons` | [`Emoticon[]`](emoticons.md#emoticon)                  | lista emotikon           |
| `location`  | [`ChatLocation`](protocol.md#lokalizacja-chatlocation) | zasięg, którego dotyczy  |

## Dodawanie emotikony

`CreateEmoticon` rejestruje wcześniej [przesłany plik](files.md) jako emotikonę. Członkowie zasięgu otrzymują
`NewEmoticon`.

Wymaga uprawnienia `CreateEmoticons` **lub** `ManageEmoticons` w danym zasięgu.

Wymagania grafiki: obraz o wymiarach **maksymalnie 120 × 100 px**.

#### `CreateEmoticon`

| Pole      | Typ                | Opis                                               |
|-----------|--------------------|----------------------------------------------------|
| `name`    | `string`           | 2–20 znaków, małe litery i cyfry                   |
| `fileId`  | `UUID`             | ID przesłanego pliku                               |
| `spaceId` | `UUID`&#124;`null` | przestrzeń; pominięcie tworzy emotikonę globalną   |

#### `NewEmoticon`

| Pole       | Typ                                   | Opis            |
|------------|---------------------------------------|-----------------|
| `emoticon` | [`Emoticon`](emoticons.md#emoticon)   | nowa emotikona  |

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                       | Opis                                                    |
|---------------------------|---------------------------------------------------------|
| `AccessDeniedException`   | brak `CreateEmoticons` ani `ManageEmoticons`            |
| `EmoticonNameException`   | nazwa nie spełnia wymagań lub jest już zajęta           |
| `EmoticonFileException`   | plik nie jest obrazem albo przekracza dozwolone wymiary |
| `SpaceNotFoundException`  | przestrzeń nie istnieje                                 |
| `UserNotFoundException`   | użytkownik nie jest członkiem przestrzeni               |

## Usuwanie emotikony

`DeleteEmoticon` usuwa emotikonę. Członkowie zasięgu otrzymują `EmoticonDeleted`.

Wymaga uprawnienia `ManageEmoticons`.

#### `DeleteEmoticon`

| Pole | Typ    | Opis                    |
|------|--------|-------------------------|
| `id` | `UUID` | identyfikator emotikony |

#### `EmoticonDeleted`

| Pole         | Typ                | Opis                                        |
|--------------|--------------------|---------------------------------------------|
| `emoticonId` | `UUID`             | identyfikator usuniętej emotikony           |
| `spaceId`    | `UUID`&#124;`null` | przestrzeń; `null` dla emotikony globalnej  |

Usunięcie emotikony nie zmienia wiadomości, w których jej użyto – reakcje o tej wartości pozostają, ale klient
nie znajdzie już grafiki. Warto przewidzieć zastępczy sposób wyświetlania.

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów) lub jeden z poniższych.

| Kod                         | Opis                              |
|-----------------------------|-----------------------------------|
| `AccessDeniedException`     | brak uprawnienia `ManageEmoticons` |
| `EmoticonNotFoundException` | emotikona nie istnieje            |
