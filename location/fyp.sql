CREATE TABLE fyp.userdata
(
  userdata_id bigint NOT NULL AUTO_INCREMENT,
  userdata_email varchar(255) NOT NULL UNIQUE,
  userdata_password varchar(255) NOT NULL,
  userdata_hash varchar(255) UNIQUE, -- The hash will be auto generated as required, not currently in use
  userdata_phonenumber varchar(255),
  primary key (userdata_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.device
(
  userdata_id bigint NOT NULL ,
  device_id varchar(255) NOT NULL,
  device_salt varchar(255) NOT NULL,
  device_details text,
  primary key (userdata_id, device_id),
  foreign key (userdata_id) references userdata(userdata_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.session
(
  session_hash varchar(255) NOT NULL UNIQUE,
  userdata_id bigint NOT NULL,
  device_id varchar(255) NOT NULL,
  session_timestamp DATETIME NOT NULL,
  foreign key (userdata_id, device_id) references device(userdata_id, device_id)
)ENGINE = MyISAM;

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
)ENGINE = MyISAM;

CREATE TABLE fyp.humanactivity
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  humanactivity_probableactivity int NOT NULL,
  humanactivity_probableactivityconfidence int NOT NULL,
  foreign key (session_hash) references session(session_hash),
  primary key (session_hash, location_time)
)ENGINE = MyISAM;

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
)ENGINE = MyISAM;

CREATE TABLE fyp.sms
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  sms_number varchar(255) NOT NULL,
  sms_isadv INT NOT NULL,
  sms_length INT NOT NULL,
  sms_incomming INT NOT NULL,
  foreign key (session_hash, location_time) references location(session_hash, location_time)
)ENGINE = MyISAM;

CREATE TABLE fyp.sim
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  sim_number varchar(255) NOT NULL,
  sim_operator varchar(255) NOT NULL,
  foreign key (session_hash, location_time) references phoneevents(session_hash, location_time)
)ENGINE = MyISAM;

CREATE TABLE fyp.network
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  network_operator varchar(255) NOT NULL,
  foreign key (session_hash, location_time) references phoneevents(session_hash, location_time)
)ENGINE = MyISAM;

CREATE TABLE fyp.wifissid
(
  wifissid_id bigint NOT NULL AUTO_INCREMENT,
  wifissid_name varchar(255) NOT NULL UNIQUE,
  primary key (wifissid_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.activities
(
  activities_id bigint NOT NULL AUTO_INCREMENT,
  activities_name varchar(255) NOT NULL UNIQUE,
  primary key (activities_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.detectedwifi
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  wifissid_id bigint NOT NULL,
  foreign key (session_hash, location_time) references phoneevents(session_hash, location_time),
  foreign key (wifissid_id) references  wifissid(wifissid_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.foregroundtask
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  activities_id bigint NOT NULL,
  foreign key (session_hash, location_time) references phoneevents(session_hash, location_time),
  foreign key (activities_id) references  activities(activities_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.locationpoint
(
  locationpoint_id BIGINT NOT NULL AUTO_INCREMENT,
  locationpoint_description VARCHAR(255),
  locationpoint_type VARCHAR(255),
  locationpoint_center_lat double NOT NULL,
  locationpoint_center_lng double NOT NULL,
  locationpoint_accuracy float NOT NULL DEFAULT 50,
  primary key(locationpoint_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.stoppoint
(
  stoppoint_id BIGINT NOT NULL AUTO_INCREMENT,
  session_hash varchar(255) NOT NULL,
  locationpoint_id BIGINT NOT NULL,
  stoppoint_start_time datetime NOT NULL,
  stoppoint_end_time datetime NOT NULL,
  stoppoint_center_lat double NOT NULL,
  stoppoint_center_lng double NOT NULL,
  stoppoint_accuracy float NOT NULL DEFAULT 50,
  foreign key (session_hash) references `session`(session_hash),
  foreign key (locationpoint_id) references locationpoint(locationpoint_id),
  primary key (stoppoint_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.phoneaction
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  phoneaction_source INT NOT NULL,
  phoneaction_action INT NOT NULL,
  foreign key (session_hash, location_time) references location(session_hash, location_time)
)ENGINE = MyISAM;

CREATE TABLE fyp.publictransportstops
(
  publictransportstops_id varchar(50) NOT NULL UNIQUE,
  publictransportstops_lat double NOT NULL,
  publictransportstops_lng double NOT NULL,
  publictransportstops_description varchar(255),
  publictransportstops_radius double NOT NULL DEFAULT 10,
  primary key (publictransportstops_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.publictransportservices
(
  publictransportservices_id varchar(50) NOT NULL UNIQUE,
  publictransportservices_description varchar(255),
  publictransportservices_operator varchar(255),
  publictransportservices_type varchar(255),
  publictransportservices_num_routes INT,
  primary key (publictransportservices_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.publictransportstopservices
(
  publictransportstops_id varchar(50) NOT NULL,
  publictransportservices_id varchar(50) NOT NULL,
  foreign key(publictransportstops_id) references publictransportstops(publictransportstops_id),
  foreign key(publictransportservices_id) references publictransportservices(publictransportservices_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.route
(
  route_id BIGINT NOT NULL AUTO_INCREMENT,
  session_hash varchar(255) NOT NULL,
  locationpoint_id_start BIGINT NOT NULL,
  locationpoint_id_end BIGINT NOT NULL,
  foreign key (session_hash) references session(session_hash),
  foreign key(locationpoint_id_start) references locationpoint(locationpoint_id),
  foreign key(locationpoint_id_start) references locationpoint(locationpoint_id),
  primary key (route_id)
)ENGINE = MyISAM;

CREATE TABLE fyp.routepoint
(
  route_id BIGINT NOT NULL,
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  foreign key (session_hash, location_time) references location(session_hash, location_time),
  foreign key (route_id) references route(route_id)
)ENGINE = MyISAM;


DELIMITER //

DROP FUNCTION IF EXISTS DISTANCE; //

CREATE FUNCTION DISTANCE( lat1 DOUBLE, lon1 DOUBLE, lat2 DOUBLE, lon2 DOUBLE )
    RETURNS DOUBLE NO SQL DETERMINISTIC
    COMMENT 'counts distance (km) between 2 points on Earth surface'
BEGIN
    DECLARE dtor DOUBLE DEFAULT 57.295800;

    RETURN (6371 * acos(sin( lat1/dtor) * sin(lat2/dtor) +
        cos(lat1/dtor) * cos(lat2/dtor) *
        cos(lon2/dtor - lon1/dtor)));
END; //

DELIMITER ;

/*
  select aswkt(point(location_lng, location_lat))  from location;
DELIMITER $$
CREATE FUNCTION `distance`
(a POINT, b POINT)
RETURNS double DETERMINISTIC
BEGIN
RETURN round(glength(linestringfromwkb(linestring(asbinary(a),asbinary(b)))));
END $$
DELIMITER ;

SELECT location_lat, location_lng, glength(linestringfromwkb(linestring(point('103.8529281','1.3708097'),point(location_lng, location_lat))))  as sdistance
from location having sdistance < 5

SELECT * from location where location_lat = 0 or location_lng = 0
*/