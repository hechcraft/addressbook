<?php

class dbconnect  extends SQLite3
{
    public function __construct()
    {
        $this->open('C:\laragon\www\adressbook\database\database.sqlite');
    }

    public function getAllUsers()
    {
        return $this->query('SELECT *  FROM users');
    }

    public function getUsers(int $userId)
    {
        $findUser = $this->prepare("SELECT * FROM users where id = :userId");
        $findUser->bindValue(':userId', $userId);

        return $findUser->execute()->fetchArray();
    }

    public function saveUser(string $name, string $address, string $phone)
    {
        $saveQuery = $this->prepare("INSERT INTO users (username, address, phone) 
        VALUES (:name, :address, :phone)");
        $saveQuery->bindValue(':name', $name);
        $saveQuery->bindValue(':address', $address);
        $saveQuery->bindValue(':phone', $phone);

        return $saveQuery->execute();
    }

    public function updateUser(int $userId, string $name, string $address, string $phone)
    {
        $saveQuery = $this->prepare("UPDATE users set username=:name, address=:address, phone=:phone 
                                        WHERE id = $userId");
        $saveQuery->bindValue(':name', $name);
        $saveQuery->bindValue(':address', $address);
        $saveQuery->bindValue(':phone', $phone);

        return $saveQuery->execute();
    }

    public function userDelete(int $userId)
    {
        $deleteUser = $this->prepare("DELETE FROM users where id = :userId");
        $deleteUser->bindValue('userId', $userId);

        return $deleteUser->execute();
    }

    public function search(string $searchColumn, string $searchRequest)
    {
        $queryString = "SELECT * FROM users WHERE %s LIKE '%s'";

        return $this->query(sprintf($queryString, $searchColumn, $searchRequest));
    }
}