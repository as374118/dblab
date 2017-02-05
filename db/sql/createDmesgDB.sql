--creating drivers table
CREATE TABLE drivers
(
	driverId NUMBER(20) NOT NULL,
	name VARCHAR2(300) NOT NULL
);

ALTER TABLE drivers ADD (
  CONSTRAINT drivers_pk PRIMARY KEY (driverId));

CREATE SEQUENCE drivers_seq;

CREATE OR REPLACE TRIGGER drivers_bir 
BEFORE INSERT ON drivers 
FOR EACH ROW
WHEN (new.driverId IS NULL)
BEGIN
  SELECT drivers_seq.NEXTVAL
  INTO   :new.driverId
  FROM   dual;
END;
/

--creating comments table
CREATE TABLE comments
(
	commentId NUMBER(20) NOT NULL,
	commentContent VARCHAR2(50)
);

ALTER TABLE comments ADD (
  CONSTRAINT comments_pk PRIMARY KEY (commentId));

CREATE SEQUENCE comments_seq;

CREATE OR REPLACE TRIGGER comments_bir 
BEFORE INSERT ON comments 
FOR EACH ROW
WHEN (new.commentId IS NULL)
BEGIN
  SELECT comments_seq.NEXTVAL
  INTO   :new.commentId
  FROM   dual;
END;
/

--creating logs table
CREATE TABLE logs
(
	logId NUMBER(20) NOT NULL,
	driverId NUMBER(20) REFERENCES drivers,
	message VARCHAR2(300),
	commentId NUMBER(20) REFERENCES comments,
	logTime VARCHAR(100)
);

ALTER TABLE logs ADD (
  CONSTRAINT logs_pk PRIMARY KEY (logId));

CREATE SEQUENCE logs_seq;

CREATE OR REPLACE TRIGGER logs_bir 
BEFORE INSERT ON logs 
FOR EACH ROW
WHEN (new.logId IS NULL)
BEGIN
  SELECT logs_seq.NEXTVAL
  INTO   :new.logId
  FROM   dual;
END;
/
