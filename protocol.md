# Protokół

Protokół definiuje komunikaty, którymi wymieniają się [klient i serwer](connection.md), oraz ich format.
Jest wspólny dla [WebSocket i WebAPI](connection.md) – zmienia się tylko sposób dostarczenia komunikatu.

## Rodzaje wiadomości

![Schemat](img/protocol.png)

**Komendy** wysyła klient. Każda komenda jest żądaniem wykonania operacji i podlega walidacji oraz sprawdzeniu
[uprawnień](permissions.md) po stronie serwera.

**Zdarzenia** wysyła serwer. Zdarzenie informuje o zmianie stanu lub zwraca żądane dane. Jedna komenda może
wygenerować kilka zdarzeń, skierowanych do różnych odbiorców:

| Komenda         | Zdarzenie do nadawcy | Zdarzenia do pozostałych                       |
|-----------------|----------------------|------------------------------------------------|
| `CreateMessage` | `NewMessage`         | `NewMessage` do wszystkich członków pokoju     |
| `JoinRoom`      | `RoomJoined`         | `RoomMembersJoined` do obecnych w pokoju       |
| `DeleteRoom`    | `RoomDeleted`        | `RoomDeleted` do wszystkich członków pokoju    |
| `DeleteRoom` (bez uprawnień) | `Error` | – |

Zdarzenia przychodzą także bez powiązania z Twoją komendą – to konsekwencja działań innych użytkowników.
Klient powinien traktować je jako jedyne źródło prawdy o stanie i aktualizować swój cache na ich podstawie.

## Format wiadomości

Wiadomości przesyłane są w formacie JSON i mają zawsze tę samą kopertę:

```
{
    "meta": {
        "type": <string>,   // nazwa komendy lub zdarzenia
        "ref": <string|null>
    },
    "data": {
        // pola zależne od typu wiadomości
    }
}
```

Nazwy typów są dokładnie takie, jak nagłówki sekcji w dalszej części dokumentacji (np. `CreateMessage`, `NewMessage`).

### Identyfikator referencyjny (`ref`)

Komunikacja jest asynchroniczna: po wysłaniu komendy możesz otrzymać wiele niepowiązanych zdarzeń, zanim przyjdzie
odpowiedź. Aby dopasować odpowiedź do komendy, nadaj komendzie własny identyfikator w polu `ref` – serwer przepisze
go do zdarzenia zwrotnego.

* Wartość musi być unikalna w ramach połączenia (np. licznik albo UUID).
* Maksymalna długość to 36 znaków.
* Jeśli nie podasz `ref`, serwer wygeneruje własny – zdarzenie zwrotne i tak będzie miało wypełnione to pole,
  ale nie będziesz w stanie go dopasować.

**Wzorzec dla klienta:** trzymaj mapę `ref → promise/callback`. Po odebraniu koperty z niepustym `ref`, który
znajduje się w mapie, rozwiąż oczekujące żądanie i usuń wpis. Koperty z nieznanym `ref` (lub bez niego)
traktuj jako zdarzenia rozgłoszeniowe.

?> Zdarzenie [`Error`](errors.md) również niesie `ref` komendy, która je wywołała – ten sam mechanizm obsługuje
sukces i porażkę.

## Identyfikatory

Wszystkie identyfikatory obiektów czatu (przestrzeni, pokojów, tematów, wiadomości, ról, plików) to **UUID v7**.
Serwer serializuje je do JSON w **formacie base58** (np. `3dEUaR7YQ8mQ1L2pF5nKwz`), ale w komendach akceptuje
zarówno base58, jak i klasyczny zapis (`0195e3f1-...`). Zwracaj je zawsze w takiej postaci, w jakiej je otrzymałeś.

Z UUID v7 wynikają dwie praktyczne własności:

* **są posortowane chronologicznie** – porównanie ID wiadomości mówi, która powstała wcześniej;
* **niosą znacznik czasu utworzenia** – możesz go odczytać bez odpytywania serwera.

Identyfikator użytkownika (`User.id`) to numeryczny ciąg znaków, nie UUID.

## Lokalizacja: `ChatLocation`

Większość komend operuje w kontekście miejsca na czacie. Zamiast osobnych pól przekazujesz jeden obiekt:

#### `ChatLocation`

| Pole      | Typ                | Opis                                        |
|-----------|--------------------|---------------------------------------------|
| `spaceId` | `UUID`&#124;`null` | identyfikator przestrzeni                   |
| `roomId`  | `UUID`&#124;`null` | identyfikator pokoju                        |
| `topicId` | `UUID`&#124;`null` | identyfikator tematu                        |

Wypełnione pola wyznaczają **warstwę docelową** lokalizacji – najgłębszą podaną: `Global` → `Space` → `Room` → `Topic`.
Każda komenda deklaruje, których warstw oczekuje; podanie innej kończy się błędem `PermissionLayerException`.

Zasady:

* `topicId` wymaga podania `roomId`.
* `spaceId` jest opcjonalne przy adresowaniu pokoju – serwer wyznaczy je sam. Jeśli je podasz, musi być poprawne,
  w przeciwnym razie otrzymasz `SpaceNotFoundException`.
* Pusty obiekt (`{}`) oznacza warstwę globalną (cały serwer).

```json
{ "spaceId": null, "roomId": "3dEUaR7YQ8mQ1L2pF5nKwz", "topicId": "7hK9pQ2vXnR4tY6uW1sZbA" }
```

## Konwencje nazewnicze

Znajomość konwencji pozwala przewidzieć nazwę zdarzenia bez zaglądania do dokumentacji:

| Wzorzec komendy | Zdarzenie zwrotne  | Przykład                                    |
|-----------------|--------------------|---------------------------------------------|
| `GetX`          | `X`                | `GetRoomMembers` → `RoomMembers`            |
| `CreateX`       | `NewX`             | `CreateRole` → `NewRole`                    |
| `UpdateX`       | `XUpdated`         | `UpdateRole` → `RoleUpdated`                |
| `DeleteX`       | `XDeleted`         | `DeleteRole` → `RoleDeleted`                |

Pozostałe zdarzenia nazywane są jako podmiot + czasownik w czasie przeszłym (`RoomJoined`, `SpaceLeft`,
`MessagesRedacted`).

### Semantyka pól w komendach `UpdateX`

Komendy edycyjne stosują aktualizację częściową:

* **pominięcie pola** – wartość pozostaje bez zmian;
* **`null`** – wartość zostaje wyczyszczona (dla pól, które na to pozwalają);
* **wartość** – zostaje ustawiona.

To rozróżnienie jest istotne: `{"id": "...", "description": null}` czyści opis, a `{"id": "..."}` go nie rusza.

## Zdarzenie `Ok`

Komendy, które nie zwracają danych (np. `Ban`, `Kick`, `SetUserData`), potwierdzają wykonanie pustym zdarzeniem `Ok`:

```json
{ "meta": { "type": "Ok", "ref": "42" }, "data": {} }
```

Brak `Ok` i brak `Error` przy danym `ref` oznacza, że odpowiedź dotrze jako właściwe zdarzenie stanu – tak działają
m.in. `React`, `Ack`, `FollowTopic` i `CreateRoom`.

## Komendy pomocnicze

Poza komendami domenowymi serwer udostępnia kilka narzędzi ogólnego przeznaczenia. Są one dostarczane przez
opcjonalne moduły – jeśli operator ich nie włączył, wysłanie komendy zwróci `ProtocolException`.

| Komenda        | Zdarzenie zwrotne | Zastosowanie                                                                 |
|----------------|-------------------|------------------------------------------------------------------------------|
| `Ping`         | `Pong`            | [utrzymanie połączenia](connection.md#utrzymanie-połączenia); brak pól       |
| `GetDebug`     | `Debug`           | metryki węzła serwera: obciążenie, pule połączeń, statystyki komend           |
| `ProxyRequest` | `ProxiedResponse` | pobranie zasobu HTTP przez serwer – obejście ograniczeń CORS w kliencie webowym |

### `ProxyRequest`

| Pole  | Typ      | Opis                     |
|-------|----------|--------------------------|
| `url` | `string` | adres do pobrania        |

Odpowiedź `ProxiedResponse` zawiera pole `response` z treścią. Ograniczenia proxy: wyłącznie metoda GET, limit
czasu 1 s, maksymalnie 5 000 bajtów odpowiedzi (nadmiar jest obcinany), wynik cache'owany przez 10 s. Niepowodzenie
zwraca `ProxiedRequestException`.

Narzędzie powstało dla klientów przeglądarkowych; bot serwerowy powinien wykonywać żądania HTTP samodzielnie.
