CREATE TABLE fyp.userdata
(
  userdata_id bigint NOT NULL AUTO_INCREMENT,
  userdata_email varchar(255) NOT NULL UNIQUE,
  userdata_password varchar(255) NOT NULL,
  userdata_hash varchar(255) UNIQUE, -- The hash will be auto generated as required, not currently in use
  userdata_phonenumber varchar(255),
  primary key (userdata_id)
);

CREATE TABLE fyp.device
(
  userdata_id bigint NOT NULL ,
  device_id varchar(255) NOT NULL,
  device_salt varchar(255) NOT NULL,
  device_details text,
  primary key (userdata_id, device_id),
  foreign key (userdata_id) references userdata(userdata_id)
);

CREATE TABLE fyp.session
(
  session_hash varchar(255) NOT NULL UNIQUE,
  userdata_id bigint NOT NULL,
  device_id varchar(255) NOT NULL,
  session_timestamp DATETIME NOT NULL,
  foreign key (userdata_id, device_id) references device(userdata_id, device_id)
);

CREATE TABLE fyp.location
(
  session_hash varchar(255) NOT NULL,
  location_lat double ,
  location_lng double ,
  location_height double ,
  location_accuracy float,
  location_time datetime NOT NULL,
  primary key (session_hash, location_time),
  foreign key (session_hash) references session(session_hash)
);

CREATE TABLE fyp.humanactivity
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  humanactivity_probableactivity int NOT NULL,
  humanactivity_probableactivityconfidence int NOT NULL,
  foreign key (session_hash) references session(session_hash),
  primary key (session_hash, location_time)
);

--CREATE TABLE fyp.phoneevents
--(
--  session_hash varchar(255) NOT NULL,
--  location_time datetime NOT NULL,
--  phoneevents_event INT NOT NULL, -- 1,STARTUP/SHUTDOWN;2,ON/OFF;3,LOCK/UNLOCK;4,SMS;5,CALL
--  phoneevents_action INT NOT NULL, -- 1,STARTUP/ON/LOCK/INCOMING
--  foreign key (session_hash, location_time) references location(session_hash, location_time)
--);

CREATE TABLE fyp.call
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  call_number varchar(25) NOT NULL,
  call_duration bigint NOT NULL,
  foreign key (session_hash, location_time) references phoneevents(session_hash, location_time)
);

CREATE TABLE fyp.sms
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  sms_number INT NOT NULL,
  sms_isadv INT NOT NULL,
  sms_length INT NOT NULL,
  sms_incomming INT NOT NULL,
  foreign key (session_hash, location_time) references location(session_hash, location_time)
);

CREATE TABLE fyp.sim
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  sim_number varchar(255) NOT NULL,
  sim_operator varchar(255) NOT NULL,
  foreign key (session_hash, location_time) references phoneevents(session_hash, location_time)
);

CREATE TABLE fyp.network
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  network_operator varchar(255) NOT NULL,
  foreign key (session_hash, location_time) references phoneevents(session_hash, location_time)
);

CREATE TABLE fyp.wifissid
(
  wifissid_id bigint NOT NULL AUTO_INCREMENT,
  wifissid_name varchar(255) NOT NULL UNIQUE,
  primary key (wifissid_id)
);

CREATE TABLE fyp.activities
(
  activities_id bigint NOT NULL AUTO_INCREMENT,
  activities_name varchar(255) NOT NULL UNIQUE,
  primary key (activities_id)
);

CREATE TABLE fyp.detectedwifi
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  wifissid_id bigint NOT NULL,
  foreign key (session_hash, location_time) references phoneevents(session_hash, location_time),
  foreign key (wifissid_id) references  wifissid(wifissid_id)
);

CREATE TABLE fyp.foregroundtask
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  activities_id bigint NOT NULL,
  foreign key (session_hash, location_time) references phoneevents(session_hash, location_time),
  foreign key (activities_id) references  activities(activities_id)
);

