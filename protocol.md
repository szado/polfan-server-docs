# Protokół

Protokół definiuje komunikaty, za pomocą których rozmawiają [klienty i serwer](connection.md#połączenie) oraz opisuje ich format.

## Rodzaje wiadomości

![Schemat](img/protocol.png)

Komendy wysyłane są przez klienta i podlegają przetworzeniu po stronie serwera. 

Zdarzenia to wiadomości wysyłane przez serwer do jednego lub wielu klientów by poinformować o zmianie stanu:

**przykład:** *wysłanie komendy `CreateMessage` spowoduje emisję zdarzenia `NewMessage` do wszystkich obecnych w pokoju*

lub innych wydarzeniach, np. błędach:

**przykład:** *wysłanie komendy `DeleteRoom` przez użytkownika nieposiadającego wymaganych uprawnień, spowoduje emisję zdarzenia `Error` do tego użytkownika*.

Są również komendy generujące kilka zdarzeń, a każde z nich może być skierowane do różnych klientów:

**przykład:** *wysłanie komendy `JoinRoom` wyemituje zdarzenie `JoinedRoom` do jej nadawcy, zawierające informacje o pokoju do którego dołączył,
a także zdarzenie `RoomMemberJoined` do wszystkich już obecnych w tym pokoju, zawierające informacje o nowoprzybyłym.*

## Format wiadomości

Wiadomości przesyłane są w formacie JSON i zbudowane są w następujący sposób:

``` 
{
    "meta": {
        "type": <string>,
        "ref": <string|null>
    },
    "data": {
        // pola zależne od typu wiadomości
    }
}
```

Dozwolone typy wiadomości wraz z opisami ich pól znajdują się w dalszej części dokumentacji.

### Identyfikator referencyjny wiadomości (envelope id)

Ponieważ komunikacja z serwerem ma charakter asynchroniczny, w celu identyfikacji konkretnej wiadomości pochodzącej z serwera, klient może nadać wysyłanej komendzie identyfikator referencyjny w polu `ref`. Wartość ta zostanie przepisana przez serwer do pola `ref` w wiadomości zwrotnej. Z tego powodu wartość ta powinna być unikalna (np. UUID). Maksymalna długość identyfikatora w polu `ref` to 36 znaków.

!> Serwer automatycznie wygeneruje identyfikator komendy jeśli nie zostanie on zdefiniowany przez klienta. Oznacza to, że po wysłaniu komendy z pustym polem `ref`, pole `ref` zdarzenia zwrotnego będzie wypełnione.