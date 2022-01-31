# Obsługa błędów

W odpowiedzi na wydanie dowolnej komendy, serwer może zwrócić wiadomość o typie `Error`:

| Pole      | Typ      | Opis                        |
|-----------|----------|-----------------------------|
| `code`    | `string` | kod błędu                   |
| `message` | `string` | opis czytelny dla człowieka |

## Globalne kody błędów

Błędy wymienione w poniższej tabeli mogą wystąpić niezależnie od wykonywanej komendy.

| Kod HTTP (WebAPI) | Kod błędu                      | Opis                                                            |
|-------------------|--------------------------------|-----------------------------------------------------------------|
| `400`             | `ProtocolException`            | nieprawidłowy typ wiadomości, błąd walidacji, błąd formatu JSON |
| `401`             | `AuthenticationException`      | błąd uwierzytelniania (np. nieprawidłowy token)                 |
| `403`             | `AccessDeniedException`        | brak wymaganych uprawnień                                       |
| `422`             | zależny od wykonywanej komendy | opisy poszczególnych komend zawierają szczegóły wystąpienia     |
| `500`             | `UnexpectedServerException`    | błąd serwera                                                    |
