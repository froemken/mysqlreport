#
# Table structure for table 'tx_mysqlreport_query_information'
#
CREATE TABLE tx_mysqlreport_query_information
(
	query_id               int(11)      unsigned DEFAULT '0',
	mode                   char(3)      DEFAULT ''           NOT NULL,
	ip                     varchar(45)  DEFAULT ''           NOT NULL,
	request                text,
	referer                text,
	unique_call_identifier varchar(24)  DEFAULT ''           NOT NULL,
	duration               double(11,8) DEFAULT '0.00000000' NOT NULL,
	query                  blob                              NOT NULL,
	query_type             varchar(20)  DEFAULT ''           NOT NULL,
	explain_query          text                              NOT NULL,
	using_index            tinyint(1)   DEFAULT '0'          NOT NULL,
	using_fulltable        tinyint(1)   DEFAULT '0'          NOT NULL,

	KEY                    uniqueCalls (unique_call_identifier,crdate,mode,duration)
);
