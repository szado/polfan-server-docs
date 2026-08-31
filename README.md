# Polfan Server API

API Polfana daje pełny dostęp do czatu w czasie rzeczywistym: wysyłanie i odbieranie wiadomości, zarządzanie
przestrzeniami, pokojami i tematami, moderację oraz obserwowanie wszystkiego, co dzieje się na serwerze.

Zbudowaliśmy je z myślą o integratorach: botach moderacyjnych, automatyzacjach, mostkach do innych systemów,
narzędziach analitycznych i alternatywnych klientach czatu. Jeden protokół JSON obsługuje oba transporty
(HTTP i WebSocket), więc kod komend piszesz raz.

## Od czego zacząć

1. **[Połączenie](connection.md)** – zdobądź token dostępowy i połącz się przez WebSocket lub WebAPI.
2. **[Protokół](protocol.md)** – poznaj kopertę wiadomości, identyfikatory i obiekt lokalizacji `ChatLocation`.
3. **[Obsługa błędów](errors.md)** – dowiedz się, jak serwer raportuje problemy i które kody warto obsłużyć.

Dalej dokumentacja podzielona jest wg zagadnień: [przestrzenie](spaces.md), [pokoje](rooms.md),
[tematy](topics.md), [wiadomości](messages.md), [role](roles.md), [uprawnienia](permissions.md),
[użytkownicy](users.md), [moderacja](moderation.md), [emotikony](emoticons.md) i [pliki](files.md).

## Model danych w skrócie

```
Przestrzeń (Space)          izolowany "serwer": członkowie, role, pokoje
  └── Pokój (Room)          podzbiór członków przestrzeni; jednostka uprawnień i obecności
        └── Temat (Topic)   wątek rozmowy; tu żyją wiadomości i historia
              └── Wiadomość (Message)
```

Pokoje prywatne (`Pm`) istnieją poza przestrzeniami – patrz [rozmowy prywatne](rooms.md#rozmowy-prywatne-pm).

## Biblioteka kliencka

Dla JavaScriptu i TypeScriptu udostępniamy [oficjalną bibliotekę](https://github.com/szado/polfan-js-client-library),
która obsługuje oba transporty, dopasowuje odpowiedzi do komend i utrzymuje aktualny stan czatu.
Jeśli piszesz w innym języku, ta dokumentacja zawiera wszystko, czego potrzebujesz do własnej implementacji.

!> Jeśli nie masz pewności, czy planowany sposób wykorzystania API jest zgodny z regulaminem – skontaktuj się z nami
przed wdrożeniem.

---

Wersja online: https://polfan.pl/v3/docs
