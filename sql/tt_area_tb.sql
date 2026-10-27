CREATE TABLE tt_user_mst(
    user_id INT NOT NULL AUTO_INCREMENT,
    user_name VARCHAR(200) NOT NULL,
    user_email VARCHAR(200) NULL,
    user_password VARCHAR(200) NOT NULL,
    delete_ku CHAR(1) NOT NULL,
    insert_at DATETIME NOT NULL,
    update_at DATETIME NOT NULL,

    PRIMARY KEY(
        user_id
    )
);