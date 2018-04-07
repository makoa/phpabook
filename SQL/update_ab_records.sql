#
# If you are updating from a previous version of phpAbook prior to version 0.7.1b
# you will need to run this file through mysql
#
# Here is an example:
# %> mysql -u username -p database < update_ab_records.sql
# Password: password
#
# Where username = your mysql username && database = the database you have access to &&
# password = the password for username
#


ALTER TABLE `ab_records` ADD `CreationDate` DATETIME NOT NULL AFTER `id`, ADD `ModifyDate` DATETIME NOT NULL AFTER `CreationDate`, ADD `ModifiedBy` VARCHAR(15) NOT NULL AFTER `ModifyDate`; 