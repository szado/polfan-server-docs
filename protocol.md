# Protokół

Protokół definiuje komunikaty, za pomocą których rozmawiają klienty i serwer oraz opisuje, w jaki sposób i w jakim formacie są one wymieniane. Przesyłane wiadomości można podzielić na komendy, zdarzenia i odpowiedzi.

## Rodzaje wiadomości

![Schemat](img/protocol.png)

### Komendy

Komendy zawsze wysyłane są przez klienty i podlegają przetworzeniu po stronie serwera. 

### Zdarzenia

Jeśli komenda zmieniła stan serwera, wyemituje on zdarzenie informujące o zmianie do każdego klienta, którego ten stan dotyczył. 

**Przykład:** *wysłanie komendy `CreateMessage` tworzącej wiadomość spowoduje emisję zdarzenia `NewMessage` do wszystkich obecnych w pokoju*.

### Odpowiedzi

Jeśli komenda zakończyła działanie bez modyfikacji stanu, odpowiedź o jej wyniku otrzyma jedynie klient który ją wydał.

**Przykład 1:** *wysłanie komendy `GetUserPermissions` pobierającej listę uprawnień spowoduje odesłanie wiadomości `Permissions` w odpowiedzi do źródłowego klienta.*

**Przykład 2:** *wysłanie komendy `CreateMessage` tworzącej wiadomość, przez użytkownika który nie posiada wymaganych uprawnień, spowoduje odesłanie wiadomości `Error` w odpowiedzi do źródłowego klienta.*

## Format wiadomości

Wiadomości przesyłane są w formacie JSON i wyglądają w następujący sposób.

```json
    {
	    "_": {
		    "type": "JoinSpace",
		    "ref": 23
	    },
	    "id": "a3a38d09-613d-4b83-bd63-0737d1daad1b"
    }
```

Dozwolone typy wiadomości wraz z ich polami znajdują się w dalszej części dokumentacji.

Ponieważ komunikacja z serwerem ma charakter asynchroniczny, w celu łatwego namierzenia konkretnej odpowiedzi z serwera, klient może nadać komendzie wysyłanej do serwera identyfikator referencyjny. Identyfikator zostanie zwrócony w wiadomości/zdarzeniu odpowiadającym na komendę. Z tego powodu, identyfikatory powinny zawierać losową, unikalną wartość (np. UUID lub inkrementowany licznik wydanych komend).