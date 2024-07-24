create table partite (
	IdPartita int AUTO_INCREMENT primary key,
	Data datetime,
	Occasione varchar(128) default '',
	Note varchar(1024) default ''
);

create table giocatori (
	IdGiocatore int AUTO_INCREMENT primary key,
	Nome varchar(32),
	Cognome varchar(32) default '',
	Alias varchar(32) default null,
	Password char(130) default null,
	Editor bit not null default 0,
	Admin bit not null default 0
);

create table mani (
	Partita int not null,
	Numero int not null,
	Chiamante int(1) not null,
	Socio int(1) not null,
	Vittoria bit default null,
	Cappotto bit default null,
	primary key (Partita, Numero),
	foreign key (Partita) references partite (IdPartita) on update cascade on delete cascade
);

create table partecipazioni (
	Giocatore int not null,
	Partita int not null,
	Inizio int not null default 1,
	Colonna int not null,
	primary key (Giocatore, Partita, Inizio),
	foreign key (Giocatore) references giocatori (IdGiocatore) on update cascade,
	foreign key (Partita) references partite (IdPartita) on update cascade on delete cascade
);

