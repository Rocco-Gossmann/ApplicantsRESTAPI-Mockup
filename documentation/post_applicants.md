# `POST /api/applicants`

<!--toc:start-->
- [`POST /api/applicants`](#post-apiapplicants)
  - [Special Behavior:](#special-behavior)
  - [Required Headers:](#required-headers)
  - [Request Body:](#request-body)
  - [Response Codes:](#response-codes)
  - [Success Response:](#success-response)
    - [Fields per Object](#fields-per-object)
<!--toc:end-->

Allows for adding multiple new Applicants into the Database

## Special Behavior:

The `firstname`, `lastname`, `country_id` and `addr_zip` form a Unique Key.
That means if you try to add an Applicant, that already has these 4 values together in the System.
The Response will give you the Data that is already in the System instead of creating new Data.

It Still counts as a Success, since the Data was successfully created, just at an earlier point in time.


## Required Headers:

| Header          | Description                                       | Example            |
|-----------------|---------------------------------------------------|--------------------|
| `Authorization` | A Bearer JWT - Token provided by the API-Provider | `Bearer abc3...fe` |
| `Content-Type`  | Should be set to `application/json`               |                    |

## Request Body:

The Request Body should be a JSON encoded Array of Objects.
Each Object is one Applicant.

These are the Properties, that each Applicant can consist of. 

| Field         | required | Type                                                            | Description                                                                                                     |
|---------------|----------|-----------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------|
| `gender`      |          | Can be: <br>-`male`<br>-`female`<br>-`diverse`<br>-`no_comment` |                                                                                                                 |
| `title`       |          | `text`                                                          | any kind of Title like "Prof.", "Dr.", etc. ...                                                                 |
| `firstname`   | yes      | `text`                                                          |                                                                                                                 |
| `lastname`    | yes      | `text`                                                          |                                                                                                                 |
| `addr_street` | yes      | `text`                                                          | Street and Housenr. of the applicants living Address                                                             |
| `addr_city`   | yes      | `text`                                                          | City- / Village-Name of the applicants living Address                                                            |
| `addr_zip`    | yes      | `text`                                                          | Zip-Code of the applicants living Address                                                                        |
| `country_id`  | yes      | `text`                                                          | The ID of the Country in which the Address is located <br> Can be found [in the Country-List](./country_list.md) |


**example:**
```json
[
    {
        "firstname": "Maria",
        "lastname": "Mustermann",
        "addr_street": "Musterstr. 123",
        "addr_city": "Musterburg",
        "addr_zip": "00000",
        "country_id": 63,
    },
    { 
        ... 
    },
    ...
] 
```

## Response Codes:

| Code | Content-Type | Description                                                                                                           |
|------|--------------|-----------------------------------------------------------------------------------------------------------------------|
| 200  | JSON         | The List of created applicants in the order you submitted them in (see [Success Response](#success-response))<br><br> |
| 400  | Text         | The Data you proved is invalid. The Response body contains more info about what went wrong.
| 403  | None         | The JWT Token you used is invalid                                                                                     |
| 500  | Text or HTML | A Server-Side technical error occurred. Should that keep happening, please contact the support                         |


## Success Response:

A JSON formatted Array of Objects. Each Object is one Applicant.
The Order of Applicants is the same order, you put in the [Request Body](#request-body)

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

** example **
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
