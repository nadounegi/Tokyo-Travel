CREATE table tt_spots_fileupload_tb(
    id int auto_increment,
    spots_no int not null,
    folder_name varchar(255) not null,
    file_name varchar(255) not null,
    delete_ku char(1) not null,
    insert_at datetime not null,
    update_at datetime not null,

primary key(id)
);
