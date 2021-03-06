CREATE TABLE advice(
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
drug text,
dose text,
route text,
frequency text,
duration text,
addl text
);
CREATE TABLE nursing(
pid int,
time int,
data text
);
CREATE TABLE patients(
pid int unique,
name text,
age int,
sex text,
status text,
vp text,
diagnosis text,
summary text,
admission int,
departure int,
ward text,
bed int,
data text,
history text
);
CREATE TABLE physician(
pid int,
time int,
data text
);
CREATE TABLE reports(
pid int,
time int,
sample text,
form text,
data text
);
CREATE TABLE requisition(
pid int,
test text,
sample text,
time int,
room text,
form text,
status text,
addl text
);
CREATE TABLE treatment(
pid int,
drug text,
dose text,
route text,
frequency text,
start int,
end int,
duration text,
administer text,
omit boolean,
addl text
);
CREATE TABLE users(
user text unique,
usergroup text,
department text,
hash text,
change boolean,
last int
);
