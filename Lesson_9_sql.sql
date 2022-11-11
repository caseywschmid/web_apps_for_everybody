


-- DATABASE TERMINOLOGY

-- Database     = Many Tables
-- Relation     = Table - contains tuples and attributes
-- Tuple        = Row   - a set of fields that generally represent an 'object'
-- Attrubute    = Cell  - One of possibly many elements of data corresponding 
--                        to an object 


-- KEY TERMINOLOGY

-- Primary Key - Usually an auto increment integer 
-- Logical Key - What the outside world uses for a lookup 
-- Foreign Key - generally an int key pointing to a row in another table 

-- NEVER use you logical key as your primary key 

CREATE TABLE Artist (
    artist_id INTEGER NOT NULL AUTO_INCREMENT KEY,
    name VARCHAR(255)
) ENGINE = InnoDB;

CREATE TABLE Album (
    album_id INTEGER NOT NULL AUTO_INCREMENT KEY,
    artist_id INTEGER,
    title VARCHAR(128),

    INDEX USING BTREE (title),

    CONSTRAINT FOREIGN KEY (artist_id) REFERENCES Artist (artist_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB; 

CREATE TABLE Genre (
    genre_id INTEGER NOT NULL AUTO_INCREMENT KEY,
    name VARCHAR(255)
) ENGINE = InnoDB;

CREATE TABLE Track (
    genre_id INTEGER NOT NULL AUTO_INCREMENT KEY,
    title VARCHAR(255),
    len INTEGER,
    rating INTEGER, 
    count INTEGER, 
    album_id INTEGER,
    genre_id INTEGER,

    INDEX USING BTREE (title),

    CONSTRAINT FOREIGN KEY (album_id) REFERENCES Album (album_id)
        ON DELETE CASCADE ON UPDATE CASCADE, 
    CONSTRAINT FOREIGN KEY (genre_id) REFERENCES Genre (genre_id)
        ON DELETE CASCADE ON UPDATE CASCADE, 
) ENGINE = InnoDB;


INSERT INTO Artist (name) VALUES ()