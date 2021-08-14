DROP TABLE branch1 cascade constraints;
DROP TABLE branch2 cascade constraints;
DROP TABLE client cascade constraints;
DROP TABLE lifter cascade constraints;
DROP TABLE runner cascade constraints;
DROP TABLE goal cascade constraints;
DROP TABLE program cascade constraints;
DROP TABLE consistsof cascade constraints;
DROP TABLE workout cascade constraints;
DROP TABLE contains cascade constraints;
DROP TABLE performs cascade constraints;
DROP TABLE exercise cascade constraints;
DROP TABLE requires cascade constraints;
DROP TABLE equipment1 cascade constraints;
DROP TABLE equipment2 cascade constraints;
DROP TABLE coach cascade constraints;
DROP TABLE coaches cascade constraints;
DROP TABLE powerliftingCoach cascade constraints;
DROP TABLE physiotherapist cascade constraints;

CREATE TABLE branch2 (postalCode char(10) PRIMARY KEY, province char(30));
CREATE TABLE branch1 (branchID int PRIMARY KEY, city char(20) NOT NULL, postalCode char(10) NOT NULL, FOREIGN KEY(postalCode) REFERENCES branch2);
CREATE TABLE program (name char(50) PRIMARY KEY);
CREATE TABLE client (id int PRIMARY KEY, branchID int NOT NULL, pname char(50), name char(20) NOT NULL, age int, FOREIGN KEY(branchID) REFERENCES branch1 ON DELETE CASCADE, FOREIGN KEY (pname) REFERENCES Program);
CREATE TABLE lifter (cid int PRIMARY KEY, timeSpentOnWeights int, timeSpentOnTreadmill int, FOREIGN KEY (cid) REFERENCES client ON DELETE CASCADE);
CREATE TABLE runner (cid int PRIMARY KEY, timeSpentOnWeights int, timeSpentOnTreadmill int, FOREIGN KEY (cid) REFERENCES client ON DELETE CASCADE);
CREATE TABLE goal (cid int, weight int, timeline char(50) NOT NULL, PRIMARY KEY(cid, weight), FOREIGN KEY(cid) REFERENCES client ON DELETE CASCADE);
CREATE TABLE workout (name char(50) PRIMARY KEY);
CREATE TABLE consistsof (pname char(50), wname char(50), PRIMARY KEY (pname, wname), FOREIGN KEY (pname) REFERENCES program, FOREIGN KEY (wname) REFERENCES workout);
CREATE TABLE exercise (name char(20) PRIMARY KEY);
CREATE TABLE contains (wname char(50), exname char(20), PRIMARY KEY (wname, exname), FOREIGN KEY (wname) REFERENCES workout, FOREIGN KEY (exname) REFERENCES exercise);
CREATE TABLE performs (weight int, reps int, sets int, cid int, exname char(20), PRIMARY KEY (cid, exname), FOREIGN KEY (cid) REFERENCES client);
CREATE TABLE equipment1 (name char(50) PRIMARY KEY, price int, sizes int);
CREATE TABLE requires (eqname char(50), exname char(20), PRIMARY KEY (eqname, exname), FOREIGN KEY (eqname) REFERENCES equipment1, FOREIGN KEY (exname) REFERENCES exercise);
CREATE TABLE equipment2 (id int PRIMARY KEY, branchID int DEFAULT 1, name char(50) NOT NULL, FOREIGN KEY (branchID) REFERENCES branch1, FOREIGN KEY (name) REFERENCES equipment1 ON DELETE CASCADE); 
CREATE TABLE coach (id int PRIMARY KEY, branchID int NOT NULL, name char(30) NOT NULL, age int, FOREIGN KEY (branchID) REFERENCES branch1 ON DELETE CASCADE);
CREATE TABLE coaches (coachID int, clientID int, PRIMARY KEY (coachID, clientID), FOREIGN KEY (coachID) REFERENCES coach ON DELETE CASCADE, FOREIGN KEY (clientID) REFERENCES client ON DELETE CASCADE);
CREATE TABLE powerliftingCoach (id int PRIMARY KEY, liftingTotal int, FOREIGN KEY (id) REFERENCES coach ON DELETE CASCADE);
CREATE TABLE physiotherapist (id int PRIMARY KEY, degree char(50), FOREIGN KEY (id) REFERENCES coach ON DELETE CASCADE);

insert into branch2 values ('V3J 2Y6', 'British Columbia');
insert into branch2 values ('V1N 6L2', 'British Columbia');
insert into branch2 values ('B2J 7K3', 'Ontario');
insert into branch2 values ('J8L 0H2', 'Alberta');
insert into branch2 values ('V3B 1R3', 'British Columbia');

insert into branch1 values (1, 'Vancouver', 'V3J 2Y6');
insert into branch1 values (2, 'Vancouver', 'V3J 2Y6');
insert into branch1 values (3, 'Vancouver', 'V1N 6L2');
insert into branch1 values (4, 'Toronto', 'B2J 7K3');
insert into branch1 values (5, 'Edmonton', 'J8L 0H2');
insert into branch1 values (6, 'Coquitlam', 'V3B 1R3');

insert into program values('PPL');
insert into program values('Stronglifts');
insert into program values('Nsuns');
insert into program values('Upper Lower Split');
insert into program values('5 Day Split');

insert into client values(100, 1, 'PPL', 'John Lee', 19);
insert into client values(101, 1, 'Stronglifts', 'Robert Nun', 30);
insert into client values(102, 2, 'Stronglifts', 'Kim Rico', 22);
insert into client values(103, 3, 'Nsuns', 'Julie Wong', 43);
insert into client values(104, 4, 'Upper Lower Split', 'Andy Wire', 28);
insert into client values(105, 5, 'Upper Lower Split', 'Maria Paul', 33);
insert into client values(106, 6, '5 Day Split', 'Jamie Cal', 33);

insert into lifter values(100, 50, 10);
insert into lifter values(101, 60, 0);
insert into lifter values(102, 30, 30);
insert into lifter values(103, 40, 50);
insert into lifter values(104, 30, 30);

insert into runner values(102, 30, 30);
insert into runner values(103, 40, 50);
insert into runner values(104, 30, 30);
insert into runner values(105, 0, 40);
insert into runner values(106, 15, 50);

insert into goal values(100, 110, '6 months');
insert into goal values(102, 150, '1 year');
insert into goal values(103, 140, '2 months');
insert into goal values(103, 130, '6 months');
insert into goal values(104, 135, '3 months');

insert into workout values('Squat day');
insert into workout values('Chest day');
insert into workout values('Upper body day');
insert into workout values('Pull day');
insert into workout values('Push day');

insert into consistsof values('Nsuns', 'Squat day');
insert into consistsof values('Stronglifts', 'Chest day');
insert into consistsof values('5 Day Split', 'Upper body day');
insert into consistsof values('Upper Lower Split', 'Pull day');
insert into consistsof values('PPL', 'Push day');

insert into exercise values('Bicep curl');
insert into exercise values('Leg curl');
insert into exercise values('Leg press');
insert into exercise values('Tricep pushdown');
insert into exercise values('Shoulder press');

insert into contains values('Upper body day', 'Bicep curl');
insert into contains values('Squat day', 'Leg curl');
insert into contains values('Squat day', 'Leg press');
insert into contains values('Chest day', 'Tricep pushdown');
insert into contains values('Push day', 'Shoulder press');

insert into performs values(30, 10, 3, 100, 'Bicep curl');
insert into performs values(110, 5, 2, 101, 'Leg curl');
insert into performs values(180, 5, 2, 102, 'Leg press');
insert into performs values(50, 5, 3, 103, 'Tricep pushdown');
insert into performs values(95, 10, 3, 104, 'Shoulder press');

insert into equipment1 values('Leg Press', 6200, 8);
insert into equipment1 values('Dumbbell', 100, 0);
insert into equipment1 values('Barbell', 300, 0);
insert into equipment1 values('Shoulder Press Machine', 3499, 7);
insert into equipment1 values('Leg Extension Machine', 3699, 7);

insert into equipment2 values(102, 1, 'Leg Press');
insert into equipment2 values(121, 1, 'Leg Press');
insert into equipment2 values(107, 2, 'Barbell');
insert into equipment2 values(106, 2, 'Dumbbell');
insert into equipment2 values(133, 2, 'Leg Press');
insert into equipment2 values(108, 3, 'Dumbbell');
insert into equipment2 values(119, 3, 'Barbell');
insert into equipment2 values(122, 3, 'Leg Press');
insert into equipment2 values(178, 3, 'Shoulder Press Machine');
insert into equipment2 values(179, 3, 'Leg Extension Machine');


insert into requires values('Dumbbell', 'Bicep curl');
insert into requires values('Leg Extension Machine', 'Leg curl');
insert into requires values('Leg Press', 'Leg press');
insert into requires values('Barbell', 'Tricep pushdown');
insert into requires values('Shoulder Press Machine', 'Shoulder press');

insert into coach values(21, 1, 'Robbert Stone', 28);
insert into coach values(22, 2, 'Sarah McDonald', 39);
insert into coach values(33, 3, 'Vincent Chang', 27);
insert into coach values(41, 4, 'Derrick Manning', 45);
insert into coach values(42, 4, 'Michelle Farrell', 30);
insert into coach values(34, 4, 'Georgia Evin', 70);

insert into coaches values(21, 100);
insert into coaches values(21, 101);
insert into coaches values(22, 102);
insert into coaches values(33, 103);
insert into coaches values(41, 104);
insert into coaches values(42, 105);

insert into powerliftingcoach values(21, 1230);
insert into powerliftingcoach values(22, 880);
insert into powerliftingcoach values(41, 1560);
insert into powerliftingcoach values(33, NULL);
insert into powerliftingcoach values(34, NULL);

insert into physiotherapist values(21, 'Bachelor of Kinesiology at UBC');
insert into physiotherapist values(33, NULL);
insert into physiotherapist values(22, 'Bachelor of Physical Therapy at McGill');
insert into physiotherapist values(41, 'Bachelor of Physical Therapy at McGill');
insert into physiotherapist values(42, NULL);
