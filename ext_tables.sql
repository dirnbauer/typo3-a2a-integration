#
# A2A task log — one row per delegated task (backend console, public JSON-RPC,
# frontend Concierge): the activity / audit record.
#
CREATE TABLE tx_a2aintegration_task_log (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    request_date int(11) unsigned DEFAULT '0' NOT NULL,
    source varchar(16) DEFAULT '' NOT NULL,
    be_user int(11) unsigned DEFAULT '0' NOT NULL,
    task_id varchar(64) DEFAULT '' NOT NULL,
    context_id varchar(64) DEFAULT '' NOT NULL,
    skill varchar(64) DEFAULT '' NOT NULL,
    final_state varchar(32) DEFAULT '' NOT NULL,
    event_count int(11) unsigned DEFAULT '0' NOT NULL,
    artifact_count int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY request_date (request_date)
);

#
# A2A Concierge requests — what a visitor asked and the artifact returned.
#
CREATE TABLE tx_a2aintegration_request (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    page_uid int(11) unsigned DEFAULT '0' NOT NULL,
    source_url varchar(2048) DEFAULT '' NOT NULL,
    skill varchar(64) DEFAULT '' NOT NULL,
    prompt text,
    answer text,
    data text,

    PRIMARY KEY (uid)
);
