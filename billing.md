# Pakiety i uprawnienia

Katalog pakietów, cenniki i subskrypcje żyją w osobnym mikroserwisie (billing). Serwer czatu nie
sprzedaje pakietów — udostępnia klientowi **uprawnienia pakietowe** (entitlements) danej przestrzeni
lub konta, żeby interfejs wiedział, co jest dostępne i co pokazać jako ograniczenie planu.

„Co masz” (uprawnienia) jest rozdzielone od „jak płacisz” (subskrypcja): protokół czatu odpowiada
tylko na pierwsze pytanie. Historia zmian, sposób płatności i szczegóły subskrypcji dostępne są
bezpośrednio w billingu, po [tikecie dostępu](billing.md#tiket-dostępu-do-billingu).

## Dwa rozłączne zakresy

Pakiet jest przypisany **albo** do przestrzeni, **albo** do użytkownika. Pakiet przestrzeni i pakiet
indywidualny nigdy nie opisują tej samej funkcji — nie ma między nimi łączenia, sumowania ani
priorytetu. Klucz uprawnienia jednoznacznie mówi, do którego zakresu należy:

| Zakres        | Prefiks klucza | Kto widzi                                    |
|---------------|-----------------|-----------------------------------------------|
| `space`       | `space.*`       | członkowie przestrzeni                        |
| `user`        | `user.*`        | wyłącznie właściciel konta (chyba że `Root`)  |

Znane klucze uprawnień przestrzeni:

| Klucz                     | Typ      | Znaczenie                                  |
|---------------------------|----------|---------------------------------------------|
| `space.members.limit`     | `limit`  | maksymalna liczba członków                  |
| `space.rooms.limit`       | `limit`  | maksymalna liczba pokoi                     |
| `space.storage.limit_mb`  | `limit`  | limit przestrzeni dyskowej (MB)             |
| `space.emoticons.limit`   | `limit`  | maksymalna liczba własnych emotikon         |
| `space.bots.limit`        | `limit`  | maksymalna liczba botów w przestrzeni       |
| `space.insights`          | `flag`   | dostęp do statystyk przestrzeni             |
| `space.discoverable`      | `flag`   | możliwość wpisu do katalogu publicznego     |
| `space.branding`          | `flag`   | własne logo/kolory przestrzeni              |
| `space.support`           | `setting`| poziom wsparcia                             |

Znane klucze uprawnień konta:

| Klucz                       | Typ      | Znaczenie                                 |
|-----------------------------|----------|--------------------------------------------|
| `user.bots.limit`           | `limit`  | maksymalna liczba botów tego użytkownika  |
| `user.upload.max_file_mb`   | `limit`  | maksymalny rozmiar pojedynczego pliku (MB)|
| `user.animated_avatar`      | `flag`   | animowany awatar                          |
| `user.badge`                | `flag`   | odznaka konta                             |

Limit `-1` oznacza brak ograniczenia. Uprawnienie pakietowe mówi wyłącznie **czy** podmiot ma daną
funkcję — o tym **kto** może z niej skorzystać decydują zwykłe [uprawnienia](permissions.md) roli.
Zestaw kluczy może się z czasem rozszerzać; klient nie powinien zakładać zamkniętej listy.

## Pobieranie uprawnień

`GetEntitlements` — bez pól zwraca uprawnienia własnego konta, z `spaceId` — uprawnienia przestrzeni:

```json
{ "type": "GetEntitlements", "data": {} }
{ "type": "GetEntitlements", "data": { "spaceId": "6xK2p9" } }
```

#### `GetEntitlements`

| Pole      | Typ                | Opis                                                              |
|-----------|--------------------|---------------------------------------------------------------------|
| `spaceId` | `string`&#124;`null` | ID przestrzeni; pominięcie razem z `userId` odpytuje własne konto |
| `userId`  | `string`&#124;`null` | ID konta; może być wyłącznie ID pytającego (chyba że `Root`)      |

#### `Entitlements`

| Pole            | Typ                                        | Opis                                             |
|-----------------|---------------------------------------------|---------------------------------------------------|
| `subject`       | [`EntitlementsSubject`](billing.md#entitlementssubject) | kogo dotyczy odpowiedź                |
| `planCode`      | `string`                                    | kod aktualnego pakietu                            |
| `entitlements`  | `object`                                    | mapa klucz uprawnienia → wartość (`boolean`&#124;`number`&#124;`string`) |

#### `EntitlementsSubject`

| Pole     | Typ                     | Opis                        |
|----------|--------------------------|------------------------------|
| `target` | `"space"`&#124;`"user"` | zakres uprawnienia            |
| `id`     | `string`                | ID przestrzeni albo konta    |

To samo zdarzenie `Entitlements` przychodzi jako **push**, gdy pakiet się zmienia — patrz
[Zmiana pakietu](billing.md#zmiana-pakietu).

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów), a ponadto:

| Kod błędu                | Kiedy występuje                                                                 |
|---------------------------|----------------------------------------------------------------------------------|
| `SpaceNotFoundException`  | podana przestrzeń nie istnieje lub pytający nie jest jej członkiem              |
| `AccessDeniedException`   | podano `userId` innego konta bez globalnego uprawnienia `Root`                  |

Użytkownik z globalnym uprawnieniem `Root` odczytuje uprawnienia dowolnej przestrzeni i dowolnego
konta, niezależnie od członkostwa.

## Tiket dostępu do billingu

Żeby dostać więcej niż aktualny stan (subskrypcja, historia zmian, cennik), klient pyta billing
bezpośrednio. Serwer czatu wystawia do tego krótkotrwały, podpisany tiket — to jedyny sposób
uwierzytelnienia się w billingu, billing nie zna sesji czatu.

`CreateAccessTicket` — bez `spaceId` tiket dotyczy własnego konta, z `spaceId` — przestrzeni
(wymaga uprawnienia `ManageSpace`, ma je właściciel):

```json
{ "type": "CreateAccessTicket", "data": { "audience": "billing" } }
{ "type": "CreateAccessTicket", "data": { "audience": "billing", "spaceId": "6xK2p9" } }
```

#### `CreateAccessTicket`

| Pole      | Typ                  | Opis                                                        |
|-----------|-----------------------|---------------------------------------------------------------|
| `audience`| `string`              | usługa docelowa; obecnie tylko `"billing"`                   |
| `spaceId` | `string`&#124;`null`  | przestrzeń, dla której wystawiany jest tiket (opcjonalne)     |

#### `AccessTicket`

| Pole          | Typ        | Opis                                                             |
|---------------|-------------|--------------------------------------------------------------------|
| `id`          | `string`   | identyfikator tiketu                                              |
| `token`       | `string`   | podpisany token do nagłówka `Authorization: Bearer <token>`       |
| `audience`    | `string`   | usługa, dla której tiket jest ważny                                |
| `scope`       | `string`   | zakres w formacie `space:<id>` albo `user:<id>`                   |
| `permissions` | `string[]` | lista uprawnień w usłudze docelowej                                |
| `expiresAt`   | `string`   | data wygaśnięcia (ISO 8601); tiket żyje krótko, ~2 minuty          |

Tiket nigdy nie jest szerszy niż uprawnienia wydającego go użytkownika w czacie. Klient wysyła go
w nagłówku `Authorization` do billingu — np. `GET /spaces/{id}/entitlements`, `.../subscription`,
`.../history` — billing weryfikuje podpis i to, że `scope` dokładnie odpowiada zasobowi w ścieżce.

### Możliwe kody błędów

[Błąd globalny](errors.md#globalne-kody-błędów), a ponadto:

| Kod błędu                    | Kiedy występuje                                                          |
|-------------------------------|-----------------------------------------------------------------------------|
| `ProtocolException`           | nieznana wartość `audience`                                               |
| `AccessDeniedException`       | `spaceId` podano bez uprawnienia `ManageSpace`                            |
| `TicketsUnavailableException` | serwer nie ma skonfigurowanego wystawiania tiketów                        |

## Zmiana pakietu

Gdy pakiet przestrzeni lub konta się zmienia (nadanie, odebranie, wygaśnięcie subskrypcji), serwer
rozsyła zdarzenie `Entitlements` — dokładnie w takim samym kształcie, jak odpowiedź na
`GetEntitlements` — do wszystkich zainteresowanych klientów: całej przestrzeni dla pakietu
przestrzeni, wszystkich sesji danego konta dla pakietu indywidualnego. Klient obsługuje jedną
strukturę zarówno przy odczycie na żądanie, jak i przy tym pushu.
