drop table if exists cms_authitem;
drop table if exists cms_authitemchild;
drop table if exists cms_authassignment;
drop table if exists cms_rights;

create table cms_authitem
(
   name varchar(64) not null,
   type integer not null,
   description text,
   bizrule text,
   data text,
   primary key (name)
);

create table cms_authitemchild
(
   parent varchar(64) not null,
   child varchar(64) not null,
   primary key (parent,child),
   foreign key (parent) references cms_authitem (name) on delete cascade on update cascade,
   foreign key (child) references cms_authitem (name) on delete cascade on update cascade
);

create table cms_authassignment
(
   itemname varchar(64) not null,
   userid varchar(64) not null,
   bizrule text,
   data text,
   primary key (itemname,userid),
   foreign key (itemname) references cms_authitem (name) on delete cascade on update cascade
);

create table cms_rights
(
	itemname varchar(64) not null,
	type integer not null,
	weight integer not null,
	primary key (itemname),
	foreign key (itemname) references cms_authitem (name) on delete cascade on update cascade
);