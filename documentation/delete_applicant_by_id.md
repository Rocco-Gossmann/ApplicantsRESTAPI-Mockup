# `DELETE /api/applicants/{id}`

<!--toc:start-->
- [`DELETE /api/applicants/{id}`](#delete-apiapplicantsid)
  - [URL Params](#url-params)
  - [Special Behavior:](#special-behavior)
  - [Required Headers:](#required-headers)
  - [Response Codes](#response-codes)
<!--toc:end-->

Removes a specific Applicant from the Database.

## URL Parameters
| Parameter | Type | Description |
| - | - | - |
| `{id}` | `int` | `id` obtained by calling [GET /api/applicants](./get_applicants.md) |


## Special Behavior:

If you request the deletion of an already deleted Applicant, you'll receive a 
Success Message regardless. 

Reason being, that the goal of the Applicant no longer being in the system was fullfilled.


## Required Headers:

| Header          | Description                                       | Example            |
|-----------------|---------------------------------------------------|--------------------|
| `Authorization` | A Bearer JWT - Token provided by the API-Provider | `Bearer abc3...fe` |
| `Content-Type`  | Should be set to `application/json`               |                    |


## Response Codes

| Code | Content-Type | Description                                                                                   |
| ---- | ------------ | ------------------------------------------------------|
| 200  | Text         | Deletion was successful. Response Text will be `deleted` or `already deleted`                        |
| 400  | Text         | if you pass `0` as your applicant `{id}` |
| 403  | None         | The JWT Token you used is invalid                     |
| 500  | Text or HTML | A Server-Side technical error occurred.<br> Should that keep happening, please contact the support |

