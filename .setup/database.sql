CREATE DATABASE IF NOT EXISTS application_mockup;
GRANT ALL ON application_mockup.* TO 'appmock_user'@'%' IDENTIFIED BY 'lksj@84$6lj dl-k45j-dskjf';
USE application_mockup;

-- Coutries are an LLM generated list, Since this is a Dummy App, it should not matter anyways. ;-)
CREATE TABLE IF NOT EXISTS countries (
    co_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    co_label VARCHAR(256),

    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    modified DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    
    CONSTRAINT countries_pk PRIMARY KEY(co_id),
    CONSTRAINT countries_unique_label UNIQUE (co_label)
);

INSERT IGNORE INTO countries (co_id, co_label) VALUES 
(1, "Afghanistan"), (2, "Albania"), (3, "Algeria"), (4, "Andorra"), (5, "Angola"), (6, "Antigua and Barbuda"), (7, "Argentina"),
(8, "Armenia"), (9, "Australia"), (10, "Austria"), (11, "Azerbaijan"), (12, "Bahamas"), (13, "Bahrain"), (14, "Bangladesh"),
(15, "Barbados"), (16, "Belarus"), (17, "Belgium"), (18, "Belize"), (19, "Benin"), (20, "Bhutan"), (21, "Bolivia (Plurinational State of)"),
(22, "Bosnia and Herzegovina"), (23, "Botswana"), (24, "Brazil"), (25, "Brunei Darussalam"), (26, "Bulgaria"), (27, "Burkina Faso"), (28, "Burundi"),
(29, "Cabo Verde"), (30, "Cambodia"), (31, "Cameroon"), (32, "Canada"), (33, "Central African Republic"), (34, "Chad"), (35, "Chile"),
(36, "China"), (37, "Colombia"), (38, "Comoros"), (39, "Democratic Republic of the Congo"), (40, "Republic of the Congo"), (41, "Costa Rica"), (42, "Croatia"),
(43, "Cuba"), (44, "Cyprus"), (45, "Czechia"), (46, "Denmark"), (47, "Djibouti"), (48, "Dominica"), (49, "Dominican Republic"),
(50, "Ecuador"), (51, "Egypt"), (52, "El Salvador"), (53, "Equatorial Guinea"), (54, "Eritrea"), (55, "Estonia"), (56, "Ethiopia"),
(57, "Fiji"), (58, "Finland"), (59, "France"), (60, "Gabon"), (61, "Gambia"), (62, "Georgia"), (63, "Germany"),
(64, "Ghana"), (65, "Greece"), (66, "Grenada"), (67, "Guatemala"), (68, "Guinea"), (69, "Guinea-Bissau"), (70, "Guyana"),
(71, "Haiti"), (72, "Honduras"), (73, "Hungary"), (74, "Iceland"), (75, "India"), (76, "Indonesia"), (77, "Iran (Islamic Republic of)"),
(78, "Iraq"), (79, "Ireland"), (80, "Israel"), (81, "Italy"), (82, "Ivory Coast"), (83, "Jamaica"), (84, "Japan"),
(85, "Jordan"), (86, "Kazakhstan"), (87, "Kenya"), (88, "Kiribati"), (89, "Korea (Democratic People's Republic of)"), (90, "Korea (Republic of)"), (91, "Kuwait"),
(92, "Kyrgyzstan"), (93, "Lao People's Democratic Republic"), (94, "Latvia"), (95, "Lebanon"), (96, "Lesotho"), (97, "Liberia"), (98, "Libya"),
(99, "Liechtenstein"), (100, "Lithuania"), (101, "Luxembourg"), (102, "Madagascar"), (103, "Malawi"), (104, "Malaysia"), (105, "Maldives"),
(106, "Mali"), (107, "Malta"), (108, "Marshall Islands"), (109, "Mauritania"), (110, "Mauritius"), (111, "Mexico"), (112, "Federated States of Micronesia"),
(113, "Moldova (Republic of)"), (114, "Monaco"), (115, "Mongolia"), (116, "Montenegro"), (117, "Morocco"), (118, "Mozambique"), (119, "Myanmar"),
(120, "Namibia"), (121, "Nauru"), (122, "Nepal"), (123, "Netherlands"), (124, "New Zealand"), (125, "Nicaragua"), (126, "Niger"),
(127, "Nigeria"), (128, "North Macedonia"), (129, "Norway"), (130, "Oman"), (131, "Pakistan"), (132, "Palau"), (133, "Panama"),
(134, "Papua New Guinea"), (135, "Paraguay"), (136, "Peru"), (137, "Philippines"), (138, "Poland"), (139, "Portugal"), (140, "Qatar"),
(141, "Romania"), (142, "Russia (Federation of)"), (143, "Rwanda"), (144, "Saint Kitts and Nevis"), (145, "Saint Lucia"), (146, "Saint Vincent and the Grenadines"), (147, "Samoa"),
(148, "San Marino"), (149, "Sao Tome and Principe"), (150, "Saudi Arabia"), (151, "Senegal"), (152, "Serbia"), (153, "Seychelles"), (154, "Sierra Leone"),
(155, "Singapore"), (156, "Slovakia"), (157, "Slovenia"), (158, "Solomon Islands"), (159, "Somalia"), (160, "South Africa"), (161, "South Sudan"),
(162, "Spain"), (163, "Sri Lanka"), (164, "Sudan"), (165, "Suriname"), (166, "Swaziland"), (167, "Sweden"), (168, "Switzerland"),
(169, "Syria (Arab Republic of)"), (170, "Taiwan (Province of China)"), (171, "Tajikistan"), (172, "Thailand"), (173, "Timor-Leste"), (174, "Togo"), (175, "Tonga"),
(176, "Trinidad and Tobago"), (177, "Tunisia"), (178, "Turkey"), (179, "Turkmenistan"), (180, "Tuvalu"), (181, "Uganda"), (182, "Ukraine"),
(183, "United Arab Emirates"), (184, "United Kingdom of Great Britain and Northern Ireland"), (185, "United Republic of Tanzania"), (186, "United States of America"), (187, "Uruguay"), (188, "Uzbekistan"), (189, "Vanuatu"),
(190, "Vatican City"), (191, "Venezuela (Bolivarian Republic of)"), (192, "Vietnam"), (193, "Yemen (Republic of)"), (194, "Zambia"), (195, "Zimbabwe") 
;

CREATE TABLE IF NOT EXISTS cities (
    ci_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    co_id INT UNSIGNED NOT NULL COMMENT 'Land => `countries`.`co_id`',
    ci_zip VARCHAR(10) NOT NULL COMMENT 'PLZ',
    ci_name VARCHAR(256) NOT NULL COMMENT 'Ort',

    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    modified DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),

    CONSTRAINT cities_pk PRIMARY KEY(ci_id),
    CONSTRAINT cities_countries_fk FOREIGN KEY(co_id) REFERENCES countries(co_id) ON DELETE RESTRICT,
    CONSTRAINT cities_unique_zip UNIQUE(ci_zip)
);

CREATE TABLE IF NOT EXISTS applicants(
    a_id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT " Primary Key ",
    a_gender ENUM("male", "female", "diverse", "no_comment") DEFAULT 'no_comment' COMMENT "Geschlecht",
    a_title VARCHAR(64) COMMENT "akademische Titel z.B. 'Dr.', etc. ... ",
    a_firstname VARCHAR(512) NOT NULL COMMENT "Vorname",
    a_lastname VARCHAR(512) NOT NULL COMMENT "Nachname",

    a_city_street VARCHAR(512) NOT NULL,
    ci_id INT UNSIGNED NOT NULL COMMENT "Ort der Adresse aus `cities`.`ci_id`",

    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    modified DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),

    CONSTRAINT applicants_pk PRIMARY KEY(a_id),
    CONSTRAINT applicants_cities_fk FOREIGN KEY(ci_id) REFERENCES cities(ci_id) ON DELETE RESTRICT,

    UNIQUE KEY applicants_unique (`a_firstname`,`a_lastname`,`ci_id`) USING HASH
);
