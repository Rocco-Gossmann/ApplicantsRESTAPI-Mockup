# AppManMockup

A Demo-Application to demonstrate REST based CRUD-Opperations for Applicant-Management
via [CakePHP 5.x](https://cakephp.org/)

This is just a Demonstration and by no means production-ready

#Project-Structure :

- [Projekt-Struktur und Ordner](./documentation/Projekt_Structur.md)  
(Lesen Sie dies, bevor Sie das Projekt einrichten und zum ersten mal starten)

## Authorization

All Endpoints are secured via JWT Bearer-Token, that Must be passed in the Request-Headers. `Authorization` Field.

The Token was signed with a secret, that must be set in the Servers Env-Vars. 
the name of the Env-Var is `JWT_TOKEN_SECRET`

>[!warning]  
> This is a Demo Application, so any validly signed Token will work.


## API - Endpunkte
<small>(Click to see Documentation)</small>
- [`GET /api/applicants`](./documentation/get_applicants.md)
- `POST /api/applicants` DOCUMENTATION WIP
- `GET /api/applicants/{id}` DOCUMENTATION WIP
- `PUT /api/applicants/{id}` DOCUMENTATION WIP
- `DELETE /api/applicants/{id}`  DOCUMENTATION WIP

