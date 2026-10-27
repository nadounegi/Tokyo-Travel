create table tt_spot_tb(
    id int auto_increment,
    area_no int not null,
    category_no int not null,
    name varchar(20) not null,
    syoka text(100) null,
    decription text(1000) null,
    delete_ku char(1) not null,
    insert_at datetime not null,
    update_at datetime not null,

    primary key(id)
);
