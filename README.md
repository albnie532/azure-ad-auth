# Tytułem wstępu

W tym przykładzie posłużyłem się autoryzacją z użyciem certyfikatów. Można to też zrobić tajnym kluczem co jest dużo łatwiejsze, ale i też mniej bezpieczne.

# Generowanie certyfikatu

Aby dokonać autoryzacji opisaną tu metodą najpierw musimy wygenerować certyfikat wraz z kluczem prywatnym.
Aby to zrobić możemy użyć OpenSSL i wygenerować go komendą:

    openssl req -x509 -newkey rsa:2048 -keyout key.pem -out cert.pem -sha256 -days 365

Pamiętamy, aby zapisać gdzieś `passphrase` (będzie później potrzebne).

# Konfiguracja Azure

## Rejestracja aplikacji

1. Przechodzimy do usługi `Azure Active Directory`.
2. Z menu `Manage` wybieramy `App registrations`.
3. Rejestrujemy nową aplikację naciskając `New registration`.

## Dodanie certyfikatu

Dodajemy cerytifikat potrzebny przy autoryzacji.

1. Przechodzimy do ekranu zarządzania aplikacją.
2. Z menu `Manage` wybieramy `Certificates & secrets`.
3. Wybieramy zakładkę `Certificates`.
4. Naciskamy `Upload certificate` i wybieramy plik z certyfikatem (`cert.pem`).

## Uprawnienia API

Możemy nadać dostępy do poszczególnych zasobów poprzez MS Graph API (np. profile użytkowników).

1. Przechodzimy do ekranu zarejestrowaną aplikacją.
2. Z menu `Manage` wybieramy `API permissions`.
3. Naciskamy `Add a permission`.
4. Wybieramy `Microsoft Graph`.
5. Wybieramy `Application permissions`.
6. Zaznaczamy interesujące nas uprawnienia. Część uprawnień może wymagać dodatkowej zgody administratora (kolumna `Admin consent required`).
7. Naciskamy `Add permissions`.
8. Jeśli któreś z wybranych uprawnień wymaga zgody administratora możemy ją nadać naciskając `Grant admin consent for...`.

## Assertion

Źródło: https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-certificate-credentials

Zawarta została tu prosta aplikacja napisana w `Node.js`, która koduje opisane w załączonym artykule `JWT assertion`.

### Użycie aplikacji

Przechodzimy do folderu `signer`, który zawiera rzeczoną aplikację.

Zanim użyjemy jej musimy zapisać plik `.env.example` jako `.env` i go uzupełnić:

- `TENANT` oraz `CLIENT_ID` pobieramy z Azure Portal przechodząc do zakładki `Overview` w ekranie zarządzania aplikacją.
- `THUMBPRINT` pobieramy przechodząc do ekranu zarządzania aplikacją, pozycji `Certificates & secrets` w menu `Manage` i zakładki `Certificates` (wartość z kolumny `Thumbprint`).
- `EXPIRES_DAYS` to czas wygaśnięcia wyrażony w dniach.
- `PASSPHRASE` to fraza zabezpieczająca wybrana przy generowaniu certyfikatu.

Teraz musimy umieścić wygenerowany wcześniej klucz w folderze `cert` pod nazwą `key.pem`.

Mając wszystkie potrzebne rzeczy najpierw instalujemy potrzebne zależności:

    npm install

Następnie uruchamiamy aplikację:

    npm run start

W wyniku, na konsoli pojawi się `token`, który gdzieś zapisujemy.

## Autoryzacja z użyciem PHP

Źródło: https://docs.microsoft.com/en-us/azure/active-directory/develop/v2-oauth2-client-creds-grant-flow#second-case-access-token-request-with-a-certificate

Ostatni etap to już użycie wygenerowanego token'a do autorzacji. W wyniku dostajemy `access token`, którego używamy do robienia zapytań przez MS Graph do dozwolnych zasobów.

Skrypt `index.php` zawiera kompletny przykład. Po pierwsze należy ustawić potrzebne parametry:

- `$tenant` oraz `$clientId` to samo co przy okazji `Assertion`.
- `$clientAssertion` - tu wklejamy `token`, który wcześniej dostaliśmy.

Kolejne kroki:

1. Skrypt uderza do endpoint'a od autoryzacji, w rezultacie dostajemy token. W tym przykładzie używam paczki `guzzlehttp/guzzle`, która jest instalowana wraz z `microsoft/microsoft-graph` i robię zwykłe zapytanie `HTTP GET`.
2. Używając wyżej wspomnianej paczki `microsoft/microsoft-graph` pobieram profil jakiegoś użytkownika i wypisuje jego nazwę.

Pamiętamy też aby zainstalować potrzebne paczki przez `Composer`:

    composer install
    
Uwaga! Aby uruchomić ten przykład trzeba dodać nowego użytkownika do Azure AD, skopiować jego ID i wkleić je w miejsce `<user_id>`.
