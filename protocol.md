# Protokół

Protokół definiuje komunikaty, za pomocą których rozmawiają [klienty i serwer](connection.md#połączenie) oraz opisuje ich format.

## Rodzaje wiadomości

![Schemat](img/protocol.png)

Wiadomości protokołu zostały umownie podzielone na 3 grupy opisujące ich sposób użycia.

### Komendy

Komendy wysyłane są przez klienta i podlegają przetworzeniu po stronie serwera. 

### Zdarzenia

Zdarzenie to wiadomość wysyłana do wielu klientów jednocześnie w celu poinformowania o zmianie stanu.

**Przykład:** *wysłanie komendy `CreateMessage` spowoduje emisję zdarzenia `NewMessage` do wszystkich obecnych w pokoju*.

### Odpowiedzi

Odpowiedź to wiadomość wysyłana przez serwer w reakcji na wydaną komendę, jedynie do klienta, który ją wydał.

Odpowiedzią może być np. informacja o błędzie.

**Przykład 1:** *wysłanie komendy `GetUserPermissions` spowoduje odesłanie wiadomości `Permissions` w odpowiedzi do źródłowego klienta.*

**Przykład 2:** *wysłanie komendy `CreateMessage`, przez użytkownika który nie posiada wymaganych uprawnień, spowoduje emisję wiadomości `Error` w odpowiedzi.*

## Format wiadomości

Wiadomości przesyłane są w formacie JSON i zbudowane są w następujący sposób:

```
{
    "_": {
        "type": <string>,
        "ref": <string|null>
    },
    
    // ...pola zależne od typu wiadomości...
}
```

Dozwolone typy wiadomości wraz z opisami ich pól znajdują się w dalszej części dokumentacji.

!> Ponieważ komunikacja z serwerem ma charakter asynchroniczny, w celu identyfikacji konkretnej wiadomości pochodzącej z serwera, klient może nadać wysyłanej komendzie identyfikator referencyjny w polu `ref`. Identyfikator zostanie przepisany przez serwer do pola `ref` w wiadomości zwrotnej. Z tego powodu wartość ta powinna być unikalna (np. UUID).

!> Maksymalna długość identyfikatora w polu `ref` to 36 znaków.