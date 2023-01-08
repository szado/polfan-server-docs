# Obsługa błędów

W odpowiedzi na komendę serwer może zwrócić wiadomość o typie `Error`:

| Pole      | Typ      | Opis                        |
|-----------|----------|-----------------------------|
| `code`    | `string` | kod błędu                   |
| `message` | `string` | opis czytelny dla człowieka |

## Przykładowa wiadomość `Error`

```json
{
  "meta": {
    "type": "Error",
    "ref": "e7fa8f5a-aed7-11ec-b909-0242ac120002"
  },
  "data": {
    "code": "ProtocolException",
    "message": "Invalid JSON"
  }
}
```

## Globalne kody błędów

Błędy wymienione w poniższej tabeli mogą wystąpić niezależnie od wykonywanej komendy.

| Kod HTTP (WebAPI) | Kod błędu                      | Opis                                                            |
|-------------------|--------------------------------|-----------------------------------------------------------------|
| `400`             | `ProtocolException`            | nieprawidłowy typ wiadomości, błąd walidacji, błąd formatu JSON |
| `401`             | `AuthenticationException`      | błąd uwierzytelniania (np. nieprawidłowy token)                 |
| `403`             | `AccessDeniedException`        | brak wymaganych uprawnień                                       |
| `500`             | `UnexpectedServerException`    | błąd serwera                                                    |

!> W przypadku WebAPI, wszystkie inne błędy generowane przez poszczególne komendy zwracane będą z kodem HTTP 422.