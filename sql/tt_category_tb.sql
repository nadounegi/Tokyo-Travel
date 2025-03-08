CREATE table tt_category_mst(
    category_no int not null auto_increment,
    category_name varchar(50) null,
    delete_ku char(1) not null,
    insert_at datetime not null,
    update_at datetime not null,

    primary key(
        category_no
    )
);