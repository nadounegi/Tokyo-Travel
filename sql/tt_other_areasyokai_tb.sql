create table tt_other_areasyokai_tb(
    id int auto_increment,
    area_no int not null,
    category_no int not null,
    name varchar(20) not null,
    description text(1000) null,
    image_url varchar(100) null,
    delete_ku char(1) not null,
    insert_at datetime not null,
    update_at datetime not null,

    primary key(id)
);