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

insert into tt_category_mst(
    category_name,
    delete_ku,
    insert_at,
    update_at
) values
(
    "アニメ",
    "0",
    now(),
    now()
),
(
    "飲食",
    "0",
    now(),
    now()
),
{
    "場所",
    "0",
    now(),
    now()
};