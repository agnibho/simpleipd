CREATE TABLE clinical(
pid int,
time int,
data text
);
CREATE TABLE death(
pid int,
time int,
data text
);
CREATE TABLE discharge(
pid int,
name text,
dose text,
route text,
frequency text,
duration text,
addl text
);
CREATE TABLE patients(
pid int unique,
name text,
age int,
sex text,
status text,
diagnosis text,
summary text,
admission int,
departure int,
ward text,
bed int,
data text,
history text
);
CREATE TABLE reports(
pid int,
time int,
form text,
data text
);
CREATE TABLE treatment(
pid int,
name text,
dose text,
route text,
frequency text,
start int,
duration text,
omit boolean,
addl text
);
