# Obsługa błędów

Gdy komenda nie może zostać wykonana, serwer odpowiada zdarzeniem `Error` z `ref` tej komendy. Zdarzenie zawiera
maszynowy kod oraz komunikat pomocny przy diagnostyce.

#### `Error`

| Pole      | Typ      | Opis                                                        |
|-----------|----------|-------------------------------------------------------------|
| `code`    | `string` | kod błędu – stabilny identyfikator, na nim buduj logikę      |
| `message` | `string` | opis dla człowieka; treść może się zmieniać między wersjami  |

```json
{
  "meta": { "type": "Error", "ref": "42" },
  "data": {
    "code": "AccessDeniedException",
    "message": "Access denied"
  }
}
```

!> Nigdy nie parsuj pola `message`. Rozgałęziaj kod wyłącznie po `code`.

## Globalne kody błędów

Poniższe błędy mogą wystąpić przy dowolnej komendzie.

| Kod HTTP (WebAPI) | Kod błędu                   | Kiedy występuje                                                   |
|-------------------|-----------------------------|-------------------------------------------------------------------|
| `400`             | `ProtocolException`         | niepoprawny JSON, nieznany typ wiadomości, błąd mapowania pól      |
| `401`             | `AuthenticationException`   | brak, nieprawidłowy lub wygasły token                              |
| `403`             | `AccessDeniedException`     | brak wymaganych [uprawnień](permissions.md)                        |
| `500`             | `UnexpectedServerException` | nieoczekiwany błąd serwera – operację można ponowić                |

W WebAPI wszystkie pozostałe błędy zwracane są ze statusem **422**.

## Katalog kodów błędów

Sekcje poszczególnych komend wymieniają kody, których należy się spodziewać w danym kontekście. Pełna lista:

### Dostęp i uwierzytelnianie

| Kod                             | Znaczenie                                                             |
|---------------------------------|-----------------------------------------------------------------------|
| `AuthenticationException`       | uwierzytelnianie nie powiodło się                                     |
| `AccessDeniedException`         | brak uprawnienia wymaganego przez komendę                             |
| `BannedAccessException`         | użytkownik ma aktywny ban lub wyciszenie w tej lokalizacji            |
| `BotOnlyOperationException`     | operacja dostępna wyłącznie dla kont z flagą `bot`                    |
| `RootPermissionException`       | próba bezpośredniej modyfikacji uprawnienia `Root`                    |
| `OwnerException`                | niedozwolona operacja na właścicielu zasobu                           |

### Nie znaleziono / już istnieje

| Kod                                | Znaczenie                                                          |
|------------------------------------|--------------------------------------------------------------------|
| `SpaceNotFoundException`           | przestrzeń nie istnieje lub podany `spaceId` nie pasuje do pokoju   |
| `RoomNotFoundException`            | pokój nie istnieje                                                  |
| `TopicNotFoundException`           | temat nie istnieje                                                  |
| `MessageNotFoundException`         | wiadomość nie istnieje                                              |
| `RoleNotFoundException`            | rola nie istnieje lub nie jest przypisana                           |
| `UserNotFoundException`            | użytkownik nie istnieje **lub nie jest członkiem** danego kontekstu  |
| `BanNotFoundException`             | ban nie istnieje                                                    |
| `EmoticonNotFoundException`        | emotikona nie istnieje                                              |
| `AttachmentNotFoundException`      | co najmniej jeden załącznik nie istnieje                            |
| `PermissionNotFoundException`      | nieznane uprawnienie                                                |
| `RelationshipNotFoundException`    | relacja między użytkownikami nie istnieje                           |
| `SpaceExistsAlreadyException`      | przestrzeń o takim ID już istnieje                                  |
| `RoomExistsAlreadyException`       | pokój o takim ID już istnieje                                       |
| `TopicExistsAlreadyException`      | temat o takim ID już istnieje                                       |
| `MessageExistsAlreadyException`    | wiadomość już istnieje w tym kontekście (także: już potwierdzona)    |
| `RoleExistsAlreadyException`       | rola już istnieje / jest już przypisana                             |
| `UserExistsAlreadyException`       | użytkownik jest już członkiem                                       |
| `BanExistsAlreadyException`        | ban dla tego użytkownika już istnieje                               |
| `RelationshipExistsAlreadyException` | relacja tego typu już istnieje                                    |
| `AbuseReportedAlreadyException`    | zgłoszenie zostało już niedawno wysłane                             |

### Walidacja i reguły domenowe

| Kod                                   | Znaczenie                                                                |
|---------------------------------------|--------------------------------------------------------------------------|
| `ProtocolException`                   | błąd formatu koperty lub mapowania danych komendy                        |
| `PermissionLayerException`            | [lokalizacja](protocol.md#lokalizacja-chatlocation) wskazuje warstwę niedozwoloną dla tej komendy lub tego uprawnienia |
| `PermissionOverwritesTargetException` | niepoprawny cel nadpisań (brak `userId` lub `roleId`)                     |
| `PermissionBitException`              | wartość uprawnienia nie jest pojedynczym bitem                           |
| `DefaultRoleException`                | niedozwolona operacja na roli domyślnej (`@everyone`)                    |
| `RolePriorityOutOfRangeException`     | priorytet roli poza dozwolonym zakresem                                  |
| `RoomTypeException`                   | typ pokoju nie wspiera tej operacji                                      |
| `LastTopicException`                  | próba usunięcia ostatniego tematu w pokoju                               |
| `InvalidInitialMessageException`      | ten typ pokoju wymaga wiadomości początkowej przy tworzeniu tematu       |
| `InvalidMessageReferenceException`    | ten typ pokoju wymaga referencji do wiadomości przy tworzeniu tematu     |
| `InvalidReactionException`            | reakcja niedozwolona (np. wartość spoza opcji ankiety)                   |
| `BanPermissionConflictException`      | nie można zbanować użytkownika o równych uprawnieniach                   |
| `ClientDataException`                 | dane klienta nie są obiektem JSON lub przekraczają limit rozmiaru        |
| `MemberAvatarException`               | awatar członka nie spełnia wymagań                                       |
| `EmoticonNameException`               | niepoprawna nazwa emotikony                                              |
| `EmoticonFileException`               | plik emotikony nie spełnia wymagań                                       |
| `SpaceIconException`                  | niepoprawna ikona przestrzeni                                            |
| `SpaceBannerException`                | niepoprawny baner przestrzeni                                            |
| `SpaceDescriptionException`           | niepoprawny opis przestrzeni                                             |
| `SpaceFlagsException`                 | niepoprawna kombinacja flag przestrzeni                                  |
| `SpaceDiscoverableException`          | niepoprawny status widoczności przestrzeni                               |
| `ProxiedRequestException`             | nie udało się wykonać [żądania przez proxy](protocol.md#proxyrequest)    |

?> `UserNotFoundException` częściej niż braku konta dotyczy **braku członkostwa** – np. próby czytania historii
tematu w pokoju, do którego bot nie dołączył. Zanim zaczniesz szukać błędu w ID, sprawdź, czy bot jest członkiem.

## Zalecane postępowanie

| Klasa błędu                                             | Reakcja                                                     |
|---------------------------------------------------------|-------------------------------------------------------------|
| `UnexpectedServerException`                             | ponów z opóźnieniem wykładniczym                            |
| `AuthenticationException`                               | odśwież token, potem połącz ponownie                        |
| `AccessDeniedException`, `BannedAccessException`        | nie ponawiaj; zaloguj i zgłoś operatorowi                   |
| `ProtocolException`                                     | błąd w kodzie klienta – nie ponawiaj                        |
| `*NotFoundException`                                    | usuń zasób z lokalnego cache                                |
| `*ExistsAlreadyException`                               | operacja jest idempotentna w skutkach – potraktuj jak sukces |
