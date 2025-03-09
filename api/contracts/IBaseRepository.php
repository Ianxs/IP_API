<?php

interface IBaseRepository {
    public function GetAllList() : array;
    public function GetById(int $id);
    public function Add($entity);
    public function Update($entity) : void;
    public function Delete(int $id) : void;
}