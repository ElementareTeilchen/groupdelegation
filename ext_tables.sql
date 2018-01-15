#
# Table structure for table 'be_users'
#
CREATE TABLE be_users (
	tx_groupdelegation_organisationunit int(11) DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'be_groups'
#
CREATE TABLE be_groups (
	tx_groupdelegation_delegateable int(11) DEFAULT '0' NOT NULL,
	tx_groupdelegation_issubadmingroup tinyint(3) DEFAULT '0' NOT NULL,
	tx_groupdelegation_organisationunit int(11) DEFAULT '0' NOT NULL,
	tx_groupdelegation_canactivate tinyint(3) DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_groupdelegation_subadmin_begroups_mm'
#

CREATE TABLE tx_groupdelegation_subadmin_begroups_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign  int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_groupdelegation_organisationunit'
#
CREATE TABLE tx_groupdelegation_organisationunit (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_groupdelegation_begroups_organisationunit_mm'
#

CREATE TABLE tx_groupdelegation_begroups_organisationunit_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign  int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_groupdelegation_beuser_organisationunit_mm'
#

CREATE TABLE tx_groupdelegation_beusers_organisationunit_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign  int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);
