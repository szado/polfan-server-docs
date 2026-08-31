# Pliki

Grafiki i załączniki nie są przesyłane przez protokół czatu. Obsługuje je osobny mikroserwis plików: najpierw
wysyłasz tam plik zwykłym żądaniem HTTP, otrzymujesz jego **ID**, a następnie przekazujesz to ID w komendzie
czatu. Dzięki temu transfer binarny nie blokuje połączenia z czatem, a ten sam plik można wykorzystać wielokrotnie.

## Przesyłanie pliku

```
POST https://files.devana.pl/files
Authorization: Bearer token_dostępowy
Content-Disposition: attachment; filename="nazwa.png"
```

Do uwierzytelnienia użyj tego samego [tokenu dostępowego](connection.md#token-dostępowy) co w czacie. Adres
mikroserwisu może różnić się między instalacjami – w [bibliotece klienckiej](https://github.com/szado/polfan-js-client-library)
odpowiada mu klasa `FilesClient`.

Ciałem żądania są surowe bajty pliku. W odpowiedzi otrzymasz metadane:

| Pole     | Typ               | Opis                                    |
|----------|-------------------|-----------------------------------------|
| `id`     | `UUID`            | identyfikator pliku – używaj go w komendach |
| `url`    | `string`          | publiczny adres pliku                   |
| `name`   | `string`          | nazwa pliku                             |
| `mime`   | `string`          | typ MIME                                |
| `size`   | `int`             | rozmiar w bajtach                       |
| `width`  | `int`&#124;`null` | szerokość obrazu                        |
| `height` | `int`&#124;`null` | wysokość obrazu                         |

Metadane pojedynczego pliku pobierzesz przez `GET /files/{id}`, a wielu naraz przez `GET /files?id[]=...&id[]=...`.

!> Plik, którego nie użyto w żadnej komendzie, jest po pewnym czasie usuwany. Prześlij go, a następnie **od razu**
wykorzystaj – nie buduj magazynu plików „na zapas”.

## Załączniki w wiadomościach

W komendzie [`CreateMessage`](messages.md#tworzenie-wiadomości) przekaż ID plików w polu `attachments` –
maksymalnie **5** na wiadomość.

Serwer dystrybuuje wiadomość natychmiast, a oznaczenie plików jako używanych wykonuje w tle. Nie oznacza to
jednak, że dowolne ID zostanie przyjęte: nieistniejący plik kończy się błędem `AttachmentNotFoundException`.

Wiadomość z załącznikiem może mieć pustą treść.

## Awatary członków

Awatar nadpisany [w przestrzeni](spaces.md#profil-członka-przestrzeni) (`UpdateSpaceMember`) lub
[w pokoju](rooms.md#profil-członka-pokoju) (`UpdateRoomMember`) wskazywany jest polem `customAvatar` zawierającym
ID przesłanego pliku.

Wymagania: obraz **od 100 × 100 px do 360 × 360 px**. Niespełnienie kończy się błędem `MemberAvatarException`.

Przekazanie `null` usuwa nadpisanie i przywraca awatar globalny konta. Poprzedni plik jest wtedy zwalniany
automatycznie – nie musisz go usuwać samodzielnie.

Pliki awatarów znikają razem z przestrzenią lub pokojem, którego dotyczą, a także w momencie opuszczenia
przestrzeni przez użytkownika i przy usunięciu konta.

## Ikona i baner przestrzeni

Grafiki przestrzeni ustawiasz komendą [`UpdateSpace`](spaces.md#edycja-przestrzeni), przekazując ID plików
w polach `icon` i `banner`.

| Element | Wymagania                                    | Błąd przy niezgodności |
|---------|----------------------------------------------|------------------------|
| Ikona   | kwadrat **360 × 360 px**                     | `SpaceIconException`   |
| Baner   | dokładnie **660 × 360 px**                   | `SpaceBannerException` |

Podobnie jak przy awatarach, przekazanie `null` usuwa grafikę, a stary plik zwalniany jest automatycznie.

## Emotikony

Grafika [emotikony](emoticons.md#dodawanie-emotikony) nie może przekraczać **120 × 100 px**. Plik wskazujesz
polem `fileId` komendy `CreateEmoticon`.
