# Protokół

Protokół definiuje komunikaty, za pomocą których rozmawiają [klienty i serwer](connection.md#połączenie) oraz opisuje ich format.

## Rodzaje wiadomości

![Schemat](img/protocol.png)

### Komendy

Komendy wysyłane są przez klienta i podlegają przetworzeniu po stronie serwera. 

### Zdarzenia

Zdarzenia to wiadomości wysyłane do jednego lub wielu klientów jednocześnie w celu poinformowania o zmianie stanu lub w wyniku działania komendy.

**Przykład:** *wysłanie komendy `CreateMessage` spowoduje emisję zdarzenia `NewMessage` do wszystkich obecnych w pokoju*.

**Przykład:** *wysłanie komendy `DeleteRoom` przez użytkownika nieposiadającego wymaganych uprawnień, spowoduje emisję zdarzenia `Error` do tego użytkownika.*

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

!> Ponieważ komunikacja z serwerem ma charakter asynchroniczny, w celu identyfikacji konkretnej wiadomości pochodzącej z serwera, klient może nadać wysyłanej komendzie identyfikator referencyjny w polu `ref`. Wartość ta zostanie przepisana przez serwer do pola `ref` w wiadomości zwrotnej. Z tego powodu wartość ta powinna być unikalna (np. UUID).

!> Brak wartości w polu `ref` spowoduje przypisanie jej przez serwer losowego identyfikatora, który zostanie przepisany również do zdarzenia zwróconego w odpowiedzi zgodnie z powyższą zasadą.

!> Maksymalna długość identyfikatora w polu `ref` to 36 znaków.