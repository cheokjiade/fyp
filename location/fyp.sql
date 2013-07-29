CREATE TABLE fyp.userdata
(
  userdata_id bigint NOT NULL AUTO_INCREMENT,
  userdata_email varchar(255) NOT NULL UNIQUE,
  userdata_password varchar(255) NOT NULL,
  userdata_hash varchar(255) UNIQUE,
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

CREATE TABLE fyp.action
(
  session_hash varchar(255) NOT NULL,
  location_time datetime NOT NULL,
  action_code INT,
  action_details TEXT,
  primary key (session_hash, location_time),
  foreign key (session_hash) references session(session_hash),
  foreign key (location_time) references location(location_time)
);

CREATE TABLE fyp.session
(
  session_hash varchar(255) NOT NULL UNIQUE,
  userdata_id bigint NOT NULL,
  device_id varchar(255) NOT NULL,
  session_timestamp DATETIME NOT NULL,
  foreign key (device_id) references device(device_id),
  foreign key (userdata_id) references userdata(userdata_id)

);

CREATE TABLE fyp.form
(
  userdata_id bigint NOT NULL,
  device_id varchar(255) NOT NULL,
  session_timestamp DATETIME NOT NULL,
  foreign key (userdata_id) references userdata(userdata_id)

);

