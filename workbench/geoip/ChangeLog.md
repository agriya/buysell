# Change Log #

## 1.15 (2015-05-01)

* Calls to `die()` where replaced with calls to `trigger_error()`. This
  allows these (rare) errors to be gracefully handled by an error handler.
  Pull request by Dirk Weise. GitHub #28.
* Removed broken distributed queries code.
* Previously if a time zone was not found, an undefined variable error would
  be outputted. `get_time_zone` now returns `null` if there is no matching
  time zone. (Fixed by justgoodman. GitHub #30.)
* `$GEOIP_REGION_NAME` is now prefixed with `global` to make it autoloaded by
  Composer. Pull request by Laurent Goussard. GitHub #15.
* The script from updating the timezone data was improved to work with the
  new CSV format. Pull request by Shadman Kolahzary. GitHub #19.

## 1.14 (2013-11-05)

* Fix lookup issues with some domain databases ( Boris Zentner )
* Reorganize and clean up code ( Gregory Oschwald )
* Fix for module when mbstring extension is missing ( Gregory Oschwald )
* Update time zones ( Boris Zentner )

## 1.13 (2013-05-27)

* Composer support ( Maksim Kotlyar )
* Remove duplicate key - A placeholder for unused countries.
  ( Boris Zentner )

## 1.12 (2013-02-20)

* Update FIPS Codes ( Boris Zentner )
* Add South Sudan ( Boris Zentner )
* Remove trailing space ( Boris Zentner )

## 1.11 (2012-07-08)

* Update Time Zones ( Boris Zentner )
* Update FIPS codes ( Boris Zentner )

## 1.10 (2012-03-26)

* Update time zones and country codes ( Boris Zentner )
* Add example for netspeedcell databases. ( Boris Zentner )

## 1.9 (2011-08-23)

* Add new datatypes
  GEOIP_COUNTRY_EDITION_V6, GEOIP_CITY_EDITION_REV1_V6
  GEOIP_CITY_EDITION_REV0_V6, GEOIP_NETSPEED_EDITION_REV1,
  GEOIP_NETSPEED_EDITION_REV1_V6, GEOIP_ASNUM_EDITION_V6,
  GEOIP_ORG_EDITION_V6, GEOIP_DOMAIN_EDITION_V6,
  GEOIP_ISP_EDITION_V6 ( Boris Zentner )
* Add new functions
  geoip_country_id_by_name_v6
  geoip_country_code_by_name_v6
  geoip_country_name_by_name_v6
  geoip_country_id_by_addr_v6
  geoip_country_code_by_addr_v6
  geoip_country_name_by_addr_v6
  geoip_name_by_addr_v6
  GeoIP_record_by_addr_v6 ( Boris Zentner )
* Add new examples sample-v6.php, sample_city-v6.php and
  sample_asn-v6.php ( Boris Zentner )
* Replace ereg with substr ( Boris Zentner )
* replace split by explode ( Boris Zentner )
* Add all missing timezones ( Boris Zentner )
* Fix some 3letter codes ( Boris Zentner )
* Fix some continent codes ( Boris Zentner )
* Update FIPS codes 20100810 ( Boris Zentner )
* Add new database types GEOIP_LOCATIONA_EDITION, GEOIP_DOMAIN_EDITION
  and GEOIP_ACCURACYRADIUS_EDITION ( Boris Zentner )
* Workaround php's broken usage of mb_substr instead of substr with
  mbstring.func_overload and mbstring.internal_encoding ( Boris Zentner )
* Change Turkey's continent code from AS to EU ( Boris Zentner )
* Update FIPS codes 20090723 ( Boris Zentner )

## 1.8 (2009-04-02)

* Add continent_code to the city record. See: sample_city.php  ( Boris Zentner )
* Update FIPS codes 20090401 ( Boris Zentner )
* Fixed spelling of Kazakhstan, was Kazakstan
* Fix TN FIPS codes and add two new TH79 and TH80 ( Boris Zentner )
* Fix geoip_country_code_by_addr when used with a city database for unknown or private records ( cpw )
* Update timezone.php
* Sync geoipregionvars.php with fips codes from Jan, 14th 2009 ( Boris Zentner )
* use metro_code in sample_city.php ( Boris Zentner )
* replace the depreciated dma_code field with metro_code ( Boris Zentner )
* remove wrong but unreferenced Singapur SG fips regions codes ( Boris Zentner )
* update regions ( geoipregionvars.php ) ( Boris Zentner )
* Die when the database file is not found or readable ( Boris Zentner )

## 1.7 (2008-1-8)

* Added BL/Saint Barthelemy, MF/Saint Martin (ISO-3166-1 additions)
* fixed bug with newlines in Country Name
* replaced $s_array[size] with $s_array['size'] (Daniel Horchner)
* Fix bug where PHP API didn't work with new edition of GeoIP ISP
  1.6 2007-1-10
* Added AX/Aland Islands, GG/Guernsey, IM/Isle of Man, JE/Jersey (ISO-3166-1 changes)
* Replaced CS/Serbia and Montenegro with RS/Serbia, removed ZR/Zaire, added ME/Montenegro
* geoip_country_(code|name)_by_addr now work against Geo(IP|Lite) City (Frank Mather)
* Added code to lookup zoneinfo timezone given country and region (Frank Mather)
* TP/East Timor changed to TL/Timor-Leste, reflecting changes in ISO-3166

## 1.5 (2005-11-01)

* Added Shared Memory support for GeoIP City (Frank Mather)
* Replaced Yugoslavia with Serbia and Montenegro
* Removed global declaration for $GEOIP_COUNTRY_CODE_TO_NUMBER, $GEOIP_COUNTRY_CODES,
  and $GEOIP_COUNTRY_CODES3

## 1.4 (2005-01-13)

* Andrew Hill, Awarez Ltd. (http://www.awarez.net):
  * Formatted file according to PEAR library standards.
  * Moved $GEOIP_COUNTRY_CODE_TO_NUMBER, $GEOIP_COUNTRY_CODES,
    $GEOIP_COUNTRY_CODES3 and $GEOIP_COUNTRY_NAMES into the
    GeoIP class, so that library will still work even when
    not included in the $GLOBAL context.
* Updated geoip_country_code_by_addr to work with PHP5 (Eric of Host Ultra)
* Replaced bit operators (| and &) with logical operators (|| and &&)
* Defined GEOIP_ISP_EDITION

## 1.3 (2004-08-04)

* Changed license from GPL to LGPL so code can be included in PEAR
* added global definitions to prevent undefined variables error when including
  from function (C�dric Dufour)
* Updated country names
* Added support for GeoIP City, version 1 with DMA and Area codes

## 1.2 (2003-10-28)

* Added support for Shared Memory (Jason Priebe)
* Added support for Distributed queries
* Added support for GeoIP Region, version 1
* Added Anonymous Proxy and Satellite Provider code/labels
* Changed Taiwan, Province of China to Taiwan

## 1.1 (2003-01-15)

* Added support for GeoIP Region and GeoIP City

## 1.0 (2002-11-21)

* Initial checkin to CVS
