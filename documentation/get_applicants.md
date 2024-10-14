# `GET /api/applicants`

<!--toc:start-->
- [`GET /api/applicants`](#get-apiapplicants)
  - [Required Headers:](#required-headers)
  - [Response Codes](#response-codes)
  - [Success Response](#success-response)
    - [Fields per Object](#fields-per-object)
<!--toc:end-->

Provides a List of all currently registered applicants.


## Required Headers

| Header          | Description                                       | Example            |
|-----------------|---------------------------------------------------|--------------------|
| `Authorization` | A bearer JWT - Token provided by the API-Provider | `Bearer abc3...fe` |

## Response Codes

| Code | Content-Type | Description                                                                                   |
|------|--------------|-----------------------------------------------------------------------------------------------|
| 200  | JSON         | List of applicants (see [Success Response](#success-response))<br><br>                      |
| 403  | None         | The JWT Token you used is invalid                                                             |
| 500  | Text or HTML | A Server-Side technical error occurred. Should that keep happening, please contact the support |

## Success Response

A JSON formatted Array of Objects. Each Object is one Applicant.

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
[
    {
        "id": 84,
        "gender": "female",
        "title": "",
        "firstname": "Maria",
        "lastname": "Mustermann",
        "addr_street": "Musterstr. 123",
        "addr_city": "Musterburg",
        "addr_zip": "00000",
        "country_id": 63,
    },

    {
        ...
    }
]
```
