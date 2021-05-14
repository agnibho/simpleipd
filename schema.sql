CREATE TABLE clinical(
pid int,
time int,
data text
);
CREATE TABLE drugs(
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
CREATE TABLE patients(
pid int unique,
name text,
age int,
sex text,
status text,
data text
);
CREATE TABLE reports(
pid int,
time int,
form text,
data text
);
