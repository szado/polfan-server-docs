# Połączenie

## Token dostępowy

Aby nawiązać połączenie z serwerem, potrzebujesz tokena dostępowego. Uzyskasz go, wysyłając dane logowania do usługi uwierzytelniającej.

    POST https://polfan.pl/webservice/auth/token
    {
	    "login": "login_do_konta",
	    "password": "hasło_do_konta",
	    "client_name": "nazwa_programu"
    }

W przypadku poprawnego uwierzytelnienia w odpowiedzi otrzymasz token:

    HTTP 200
    {
	    "token": "token_dostępowy",
	    "expiration": "data_wygaśnięcia"
    }

W przypadku podania nieprawidłowych danych otrzymasz komunikat o błędzie:

    HTTP 401
    {
	    "errors": ['Invalid login or password']
    }

## WebAPI

WebAPI to usługa sieciowa pozwalająca na komunikację z serwerem w modelu żądanie-odpowiedź za pomocą protokołu HTTP. Za jej pomocą wyślesz do serwera komendy i otrzymasz informacje o ewentualnych błędach, jednak nie otrzymasz zdarzeń związanych z ich wykonaniem. Jeśli chcesz otrzymywać zdarzenia w modelu żądanie-odpowiedź, wykorzystaj webhooki.

Użyj WebAPI gdy:

 - chcesz w prosty sposób wysyłać wiadomości i wykonywać inne komendy,
 - nie potrzebujesz otrzymywać zdarzeń.

### Uwierzytelnianie

Token prześlij w nagłówku `Authorization: Bearer token_dostępowy`. 

### Komunikacja

Żądanie kieruj na adres: 

`https://s2.polfan.pl/api`

W jego ciele prześlij [poprawną komendę](protocol.md). W odpowiedzi możesz otrzymać:

| Kod HTTP | Zawartość                                                                                                                                                                                         |
|----------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `200`    | odpowiedź na komendę. W przypadku komend generujących zdarzenia, sporadycznie może to być zdarzenie (z powodu asynchronicznej natury serwera **nie ma na to żadnej gwarancji**)                   |
| `201`    | wiadomość bez zawartości. Oznacza, że komenda została poprawnie wykonana, a zdarzenie z informacją o modyfikacji stanu zostanie rozesłane do klientów (możesz je odebrać np. za pomocą webhooków) |

lub [wiadomość `Error` z odpowiednim kodem HTTP](errors.md#globalne-kody-błędów).

## Webhooki

W przygotowaniu.

Użyj webhooków gdy:

 - używasz WebAPI i chcesz otrzymywać wybrane zdarzenia,
 - posiadasz lub chcesz zbudować aplikację działającą w modelu request-response,
 - Twoja aplikacja posiada publiczny endpoint, do którego będą mogły być kierowane zdarzenia.

## WebSocket

Aby połączyć się z usługą możesz wykorzystać protokół [WebSocket](https://developer.mozilla.org/en-US/docs/Web/API/WebSocket). Za pomocą takiego połączenia możesz wysyłać komendy i otrzymywać wszystkie zdarzenia i odpowiedzi w czasie rzeczywistym.

Użyj połączenia websocket gdy:

- posiadasz/chcesz stworzyć aplikację, która jest w stanie utrzymywać (w razie potrzeby odnawiać) długotrwałe połączenie websocketowe,
- chcesz odbierać zdarzenia, ale nie chcesz wystawiać publicznego endpointu dla webhooków,
- chcesz uniknąć konieczności wykonywania/odbierania wielu zapytań HTTP.

### Uwierzytelnianie i komunikacja

Token prześlij w parametrze `token` przy nawiązywaniu połączenia z serwerem: 

`wss://s2.polfan.pl/ws?token=token_dostępowy`

Po prawidłowym uwierzytelnieniu serwer wykona inicjalizację połączenia (tzw. handshake) i wyśle wiadomość powitalną `Welcome` zawierającą stan sesji użytkownika.
