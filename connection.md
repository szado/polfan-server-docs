# Połączenie

![Schemat](img/connection-arch.png)

Do połączenia z serwerem wykorzystać możesz klasyczne WebAPI lub komunikację za pomocą WebSocket. Obie metody są ze sobą
w pełni kompatybilne dzięki wykorzystaniu [wspólnego protokołu opartego o komunikaty JSON](protocol.md).

## WebAPI

WebAPI to usługa sieciowa pozwalająca na komunikację z serwerem w modelu żądanie-odpowiedź za pomocą HTTP. 
Z WebAPI korzystać można niezależnie lub w połączeniu z komunikacją websocketową, zapewniającą odbiór zdarzeń w czasie 
rzeczywistym.

### Uwierzytelnianie

Każdy request podpisz nagłówkiem `Authorization: Bearer token_dostępowy`, zastępując `token_dostępowy` 
[tokenem otrzymanym z usługi uwierzytelniającej](connection.md#token-dostępowy).

### Komunikacja

Wszystkie żądania należy wysyłać metodą POST na adres:

`https://s2.polfan.pl/api`

W ciele żądania prześlij [komendę](protocol.md). W przypadku pomyślnego przetworzenia żądania otrzymasz odpowiednie zdarzenie ze statusem HTTP 200 lub [zdarzenie `Error` z innym kodem błędu HTTP](errors.md#globalne-kody-błędów).

## WebSocket

Aby połączyć się z serwerem, możesz wykorzystać protokół [WebSocket](https://developer.mozilla.org/en-US/docs/Web/API/WebSocket). 
Za pomocą takiego połączenia możesz wysyłać komendy i otrzymywać wszystkie zdarzenia w czasie rzeczywistym.

Użycie WebSocket jest dobrym pomysłem gdy chcesz stworzyć aplikację, która jest w stanie utrzymywać (w razie potrzeby odnawiać) 
długotrwałe połączenie websocketowe, chcesz uniknąć konieczności wykonywania wielu zapytań HTTP i zależy Ci na komunikacji
z minimalnymi opóźnieniami.

### Uwierzytelnianie i komunikacja

[Token uzyskany z usługi uwierzytelniającej](connection.md#token-dostępowy) prześlij w parametrze `token` przy nawiązywaniu połączenia z serwerem: 

`wss://s2.polfan.pl/ws?token=token_dostępowy`

Po prawidłowym nawiązywaniu połączenia z serwerem otrzymasz zdarzenie `Session` które będzie zawierało pełną informację 
o stanie sesji na moment nawiązania połączenia. Wszelkie zdarzenia występujące od tej chwili będą modyfikować ten stan.

#### `Session`

| Pole            | Typ                                  | Opis                                                         |
|-----------------|--------------------------------------|--------------------------------------------------------------|
| `serverVersion` | `string`                             | wersja serwera z którym nastąpiło połączenie                 |
| `state`         | [`UserState`](connection.md#session) | obiekt zawierający stan sesji: otwarte przestrzenie i pokoje |
| `user`          | [`User`](connection.md#session)      | obiekt zawierający informacje o aktualnym użytkowniku        |

#### `UserState`

| Pole            | Typ                                            | Opis                                               |
|-----------------|------------------------------------------------|----------------------------------------------------|
| `spaces`        | [`Space[]`](spaces.md#space)                   | lista przestrzeni w których obecny jest użytkownik |
| `rooms`         | [`Room[]`](rooms.md#room)                      | lista pokojów w których obecny jest użytkownik     |

#### `User`

| Pole     | Typ        | Opis                                           |
|----------|------------|------------------------------------------------|
| `id`     | `string`   | identyfikator użytkownika                      |
| `nick`   | `string`   | nazwa użytkownika                              |
| `avatar` | `string`   | URL awatara użytkownika                        |
| `flags`  | `string[]` | globalne flagi użytkownika (np. `bot`, `temp`) |

#### Przykład zdarzenia `Session`

```json
{
  "meta": {
    "type": "Session",
    "ref": null
  },
  "data": {
    "serverVersion": "PolfanServer/0.0.1",
    "state": {
      "spaces": [
        {
          "id": "252e63a0-10cf-4856-bdcb-db2b2aadedd2",
          "name": "Hogwart",
          "roles": [
            {
              "id": "252e63a0-10cf-4856-bdcb-db2b2aadedd2",
              "name": "Gryffindor",
              "color": "#ff0000"
            }
          ]
        }
      ],
      "rooms": [
        {
          "id": "252e63a0-10cf-4856-bdcb-db2b2aadedd2",
          "spaceId": "252e63a0-10cf-4856-bdcb-db2b2aadedd2",
          "name": "Zamek",
          "description": "",
          "topics": {
            "id": "252e63a0-10cf-4856-bdcb-db2b2aadedd2",
            "name": "Wielka sala",
            "description": ""
          }
        }
      ]
    },
    "user": {
      "id": "252e63a0-10cf-4856-bdcb-db2b2aadedd2",
      "nick": "Polfan",
      "avatar": "https://i.imgur.com/XqQZQ.png",
      "flags": [
        "bot"
      ]
    }
  }
}
```

## Token dostępowy

Do nawiązania połączenia z serwerem potrzebny jest token dostępowy. Uzyskasz go, wysyłając dane logowania do usługi uwierzytelniającej metodą HTTP POST na adres:

`https://polfan.pl/webservice/auth/token` 

    {
	    "login": "login_do_konta",
	    "password": "hasło_do_konta",
	    "client_name": "nazwa_programu"
    }

W przypadku poprawnego uwierzytelnienia w odpowiedzi ze statusem 200 otrzymasz token:

    {
	    "token": "token_dostępowy",
	    "expiration": "data_wygaśnięcia"
    }

W przypadku podania nieprawidłowych danych otrzymasz odpowiedź ze statusem 401 i szczegółami błędu:

    {
	    "errors": ["Invalid login or password"]
    }

!> Pojedynczy token wykorzystuj, dopóki jego w nie wygaśnie lub nie zostanie usunięty. Limit aktywnych tokenów na użytkownika jest ograniczony.
