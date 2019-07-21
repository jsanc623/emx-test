# EMX Code Test

Thank you for the opportunity to work on and submit this code test.

## Path to Solution

Initially, I thought that the solution was based on rules - for instance
a `<` in one cell would dictate which characters would be in the surrounding cells.

However, upon testing, I quickly realized that this was not the case. So, I took to 
pen and paper. I translated each character to a numeric value, as follows:

    =   2
    >   1
    <  -1
    -   0

Where `0` would be considered an empty cell. I then translated a few puzzle problems 
as follows:

Initial translations:
![Initial Tests](http://emx.juanleonardosanchez.com/paper_2.jpg)

Further translations:
![Further Tests](http://emx.juanleonardosanchez.com/paper_1.jpg) 

This led me to see that the `=` were dividers, and each cell's lower mirror 
was the opposite value. Additionally, the character in the cell on each row that
was pre-filled dictated which characters were to be on that line (if `>`, then left 
would be `<` and to the right would be `>` in most cases, for instance).

The rest was simply a matter of coding those rules into the matrix manipulation function. 

## Structure

There is an `index.php` which acts as the gateway. It's a simple service, 
so I saw no need for an overkill application based on a framework like Laravel. 

Additionally, I wrote `helper.php`, which contains two classes - `Log`, and `Helper` 
(which extends `Log` for ease of use).

All of the data is stored in a `data.json` file, though in a broader application 
it would be stored in a database (sample schema below), though the treatment would be
the same. 

## Possible MySQL Schema for `data.json`

TABLE STRUCTURE FOR: users

    CREATE TABLE `users` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `uuid` varchar(64) NOT NULL,
      `username` varchar(100) NOT NULL,
      `password` varchar(255) NOT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
TABLE STRUCTURE FOR: answers

    CREATE TABLE `answers` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `question_id` int(11) unsigned NOT NULL,
      `answer` varchar(255) NULL,
      `created_at` datetime NOT NULL,
      UNIQUE KEY `id` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TABLE STRUCTURE FOR: questions

    CREATE TABLE `questions` (
      `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
      `key` varchar(32) NOT NULL
      `question` varchar(255) NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Where a query for selecting an answer based on a `q` value of 'Years', might go 
as follows:

    SELECT
        a.answer
    FROM
        users u
        INNER JOIN answers a on a.user_id = u.id
        LEFT JOIN questions q on q.id = a.question_id
    WHERE
        u.uuid = ?
        AND q.key = ?

Where the first placeholder would be the user ID for a specific user (
assuming all answers were publicly accessible if the UUID is passed in), and the
second placeholder would be the `q` portion of the query (e.g. 'Years')
