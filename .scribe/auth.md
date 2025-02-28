# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_KEY}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

You can retrieve your token by making sure **PASSPORT_PERSONAL_ACCESS_CLIENT_ID** and **PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET** are included and a personal token is issued to create user tokens. Most paths will only need **HMAC** verification, this ensures all paths are secure.
