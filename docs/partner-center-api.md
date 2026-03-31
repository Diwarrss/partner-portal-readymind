# ReadyMind Partner Center API (v1)

Base URL: `/api/v1`

## Auth

### `POST /auth/token`

Creates a customer API token using external identity.

Request body:

```json
{
  "external_id": "uuid",
  "password": "customer-password"
}
```

Response:

```json
{
  "token": "plain_text_token",
  "token_type": "Bearer",
  "customer": {
    "id": 1,
    "name": "Customer Name",
    "email": "customer@example.com",
    "external_id": "uuid"
  }
}
```

Use header: `Authorization: Bearer {token}` for all endpoints below.

## Subscriptions

### `GET /subscriptions`

Returns customer subscriptions from local cache after a sync from Partner Center.

### `POST /subscriptions/{subscription}/increment`

Request body:

```json
{
  "delta": 5
}
```

### `POST /subscriptions/{subscription}/decrement`

Request body:

```json
{
  "delta": 2
}
```

If Microsoft grace period has expired, returns `422`.

## Contact

### `POST /contact`

This is a contact form (not a product order workflow).

Request body:

```json
{
  "product_name": "Microsoft 365 E3",
  "quantity": 50,
  "message": "Need availability and timeline."
}
```

Triggers notification email to configured recipients.
