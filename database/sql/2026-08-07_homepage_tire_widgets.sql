-- Odabrati produkcijsku bazu prije pokretanja ove skripte.
-- Usklađuje nazive/URL-ove ljetnog i zimskog widgeta te u zimski
-- widget postavlja pet dogovorenih Hankook proizvoda.
-- Nakon izvršavanja očistiti Laravel cache: php artisan cache:clear

START TRANSACTION;

UPDATE `widget_groups`
SET `title` = 'Ljetne gume', `updated_at` = NOW()
WHERE `id` = 2 AND `slug` = 'ljetne-gume';

UPDATE `widgets`
SET
    `title` = 'Ljetne auto gume',
    `url` = '/webshop/gume/ljetna-guma-za-osobna-vozila',
    `data` = CAST(FROM_BASE64('YTo5OntzOjU6InRpdGxlIjtzOjE2OiJMamV0bmUgYXV0byBndW1lIjtzOjg6InN1YnRpdGxlIjtOO3M6MzoidXJsIjtzOjQyOiIvd2Vic2hvcC9ndW1lL2xqZXRuYS1ndW1hLXphLW9zb2JuYS12b3ppbGEiO3M6MzoiY3NzIjtOO3M6Njoic3RhdHVzIjtzOjI6Im9uIjtzOjU6Imdyb3VwIjtzOjc6InByb2R1Y3QiO3M6ODoiZ3JvdXBfaWQiO3M6MToiMiI7czoxNDoiZ3JvdXBfdGVtcGxhdGUiO3M6MTY6InByb2R1Y3RfY2Fyb3VzZWwiO3M6NDoibGlzdCI7YToxMDp7aTo0MjE7czozOiI0MjEiO2k6NzI2O3M6MzoiNzI2IjtpOjE4ODQ7czo0OiIxODg0IjtpOjIwNDY7czo0OiIyMDQ2IjtpOjIxMTg7czo0OiIyMTE4IjtpOjIxODc7czo0OiIyMTg3IjtpOjI1Njg7czo0OiIyNTY4IjtpOjgwMjc7czo0OiI4MDI3IjtpOjEwNzM2O3M6NToiMTA3MzYiO2k6MTA3NTE7czo1OiIxMDc1MSI7fX0=') AS CHAR CHARACTER SET utf8mb4),
    `updated_at` = NOW()
WHERE `id` = 2 AND `group_id` = 2;

UPDATE `widget_groups`
SET `title` = 'Zimske auto gume', `updated_at` = NOW()
WHERE `id` = 5 AND `slug` = 'zimske-auto-gume';

UPDATE `widgets`
SET
    `title` = 'Zimske auto gume',
    `url` = '/webshop/gume/zimska-guma-za-osobna-vozila',
    `data` = CAST(FROM_BASE64('YTo5OntzOjU6InRpdGxlIjtzOjE2OiJaaW1za2UgYXV0byBndW1lIjtzOjg6InN1YnRpdGxlIjtOO3M6MzoidXJsIjtzOjQyOiIvd2Vic2hvcC9ndW1lL3ppbXNrYS1ndW1hLXphLW9zb2JuYS12b3ppbGEiO3M6MzoiY3NzIjtOO3M6Njoic3RhdHVzIjtzOjI6Im9uIjtzOjU6Imdyb3VwIjtzOjc6InByb2R1Y3QiO3M6ODoiZ3JvdXBfaWQiO3M6MToiNSI7czoxNDoiZ3JvdXBfdGVtcGxhdGUiO3M6MTY6InByb2R1Y3RfY2Fyb3VzZWwiO3M6NDoibGlzdCI7YTo1OntpOjI0NzM7czo0OiIyNDczIjtpOjEwNTg0O3M6NToiMTA1ODQiO2k6NzYxMDtzOjQ6Ijc2MTAiO2k6NzU5MjtzOjQ6Ijc1OTIiO2k6MjIwMDtzOjQ6IjIyMDAiO319') AS CHAR CHARACTER SET utf8mb4),
    `updated_at` = NOW()
WHERE `id` = 5 AND `group_id` = 5;

COMMIT;

-- Kontrola nakon izvršavanja: moraju se vratiti dva retka s ispravnim nazivima.
SELECT `id`, `group_id`, `title`, `url`
FROM `widgets`
WHERE `id` IN (2, 5)
ORDER BY `id`;
