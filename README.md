# AppManMockup

A Demo-Application to demonstrate REST based CRUD-Opperations for Applicant-Management

## Information
- [Project-Setup](./documentation/Projekt_Structur.md)

## Security
Calling any Endpoint in this API will require a signed JWT-Token that is passed in via the `Authorization` Request Header.
Failing to do so, will always return a `403 Forbidden` Status Response.

> [!Attention]
> This is a Demo Application. To keep it simple, any validly signed Token will work.

## API - Endpoints
<small>(Click to see Documentation)</small>

- [`GET /api/applicants`](./documentation/get_applicants.md)
- [`POST /api/applicants`](./documentation/post_applicants.md)
- [`GET /api/applicants/{id}`](./documentation/get_applicant_by_id.md)
- [`PUT /api/applicants/{id}`](./documentation/put_applicant_by_id.md)
- [`DELETE /api/applicants/{id}`](./documentation/delete_applicant_by_id.md)
