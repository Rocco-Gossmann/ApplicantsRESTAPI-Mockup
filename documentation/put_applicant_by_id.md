# `PUT /api/applicants/{id}`

<!--toc:start-->
- [`PUT /api/applicants/{id}](#put-apiapplicantsid)
  - [URL Parameters](#url-parameters)
  - [Special Behavior](#special-behavior)
  - [Required Headers](#required-headers)
  - [Request Body](#request-body)
  - [Response Codes](#response-codes)
  - [Success Response](#success-response)
    - [Fields per Object](#fields-per-object)
<!--toc:end-->

Allows for editing a specific Applicants dataset

## URL Parameters
| Parameter | Type | Description |
| - | - | - |
| `{id}` | `int` | `id` obtained by calling [GET /api/applicants](./get_applicants.md) |

## Special Behavior

if you want to change either `addr_city`, `addr_zip` or `country_id` you must
submit all 3 values

## Required Headers

| Header          | Description                                       | Example            |
|-----------------|---------------------------------------------------|--------------------|
| `Authorization` | A Bearer JWT - Token provided by the API-Provider | `Bearer abc3...fe` |
| `Content-Type`  | Should be set to `application/json`| |

## Request Body

The Request Body should be a JSON encoded Array of Objects.
Each Object is one Applicant.

These are the Properties, that each Applicant can consist of.

| Field         | Footnote | Type                                                            | Description                                                                                                     |
|---------------|----------|-----------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------|
| `gender`      |          | Can be: <br>-`male`<br>-`female`<br>-`diverse`<br>-`no_comment` |                                                                                                                 |
| `title`       |          | `text`                                                          | any kind of Title like "Prof.", "Dr.", etc. ...                                                                 |
| `firstname`   |          | `text` |                                                                                                                 |
| `lastname`    |          | `text` |                                                                                                                 |
| `addr_street` |          | `text`                                                          | Street and Housenr. of the applicants living Address                                                             |
| `addr_city`   | *        | `text`                                                          | City- / Village-Name of the applicants living Address                                                            |
| `addr_zip`    | *        | `text`                                                          | Zip-Code of the applicants living Address                                                                        |
| `country_id`  | *        | `text`                                                          | The ID of the Country in which the Address is located <br> Can be found [in the Country-List](./country_list.md) |
  `*` = these 3 must be always sent together

**example**
```json
{
    "firstname": "Maria",
    "lastname": "Mustermann",
}
```

## Response Codes

| Code | Content-Type | Description                                                                                                           |
|------|--------------|-----------------------------------------------------------------------------------------------------------------------|
| 200  | JSON         | The List of created applicants in the order you submitted them in (see [Success Response](#success-response))<br><br> |
| 400  | Text         | The Data you proved is invalid. The Response body contains more info about what went wrong.
| 403  | None         | The JWT Token you used is invalid                                                                                     |
| 404  | Text         | The requested Applicant does not exist on the Server                                          |
| 409  | Text         | The Name and City you entered does match that of a different Applicant. 
| 500  | Text or HTML | A Server-Side technical error occurred. Should that keep happening, please contact the support                         |


## Success Response

The JSON formatted Object. Containing all updated data for the Applicant.

### Fields per Object

| Field         | Type                                                            | Description                                                                                                     |
|---------------|-----------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------|
| `id`          | `number`                                                        | Primary Key, that can be used in PUT or DELETE operations                                                      |
| `gender`      | Can be: <br>-`male`<br>-`female`<br>-`diverse`<br>-`no_comment` |                                                                                                                 |
| `title`       | `text`                                                          | any kind of Title like "Prof.", "Dr.", etc. ...                                                                 |
| `firstname`   | `text`                                                          |                                                                                                                 |
| `lastname`    | `text`                                                          |                                                                                                                 |
| `addr_street` | `text`                                                          | Street and Housenr. of the applicants living Address                                                             |
| `addr_city`   | `text`                                                          | City- / Village-Name of the applicants living Address                                                            |
| `addr_zip`    | `text`                                                          | Zip-Code of the applicants living Address                                                                        |
| `country_id`  | `text`                                                          | The ID of the Country in which the Address is located <br> Can be found [in the Country-List](./country_list.md) |

**example:** 
```json
{
    "id": 84,
    "gender": "female",
    "title": "",
    "firstname": "Maria",
    "lastname": "Mustermann",
    "addr_street": "Musterstr. 123",
    "addr_city": "Musterburg",
    "addr_zip": "00000",
    "country_id": 63
}
```
