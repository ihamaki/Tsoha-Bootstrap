<?php

class Person extends BaseModel{

    public $id, $account_id, $name, $birthday, $description;

    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_name', 'validate_birthday', 'validate_description');
    }

    public static function all(){
        $query = DB::connection()->prepare('SELECT * FROM Person');
        $query->execute();
        $rows = $query->fetchAll();
    
        $accounts = array();
        
        foreach($rows as $row){
            $people[] = new Person(array(
                'id' => $row['id'],
                'account_id' => $row['account_id'],
                'name' => $row['name'],
                'birthday' => $row['birthday'],
                'description' => $row['description']
            ));
        }

        return $people;
    }

    public static function find($id){
        $query = DB::connection()->prepare('SELECT * FROM Person WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if($row){
            $person = new Person(array(
                'id' => $row['id'],
                'account_id' => $row['account_id'],
                'name' => $row['name'],
                'birthday' => $row['birthday'],
                'description' => $row['description']
            ));

            return $person;
        }

        return null;
    }

    public function save(){
        $query = DB::connection()->prepare('INSERT INTO Person (name, birthday, description) 
                                            VALUES (:name, :birthday, :description) 
                                            RETURNING id');
        $query->execute(array(
            'name' => $this->name, 
            'birthday' => $this->birthday, 
            'description' => $this->description));
        $row = $query->fetch();
        $this->id = $row['id'];
    }

    public function update($id){
        $query = DB::connection()->prepare('UPDATE Person 
                                            SET name = :name, birthday = :birthday, description = :description 
                                            WHERE id = :id');
        $query->execute(array(
            'id' => $id,
            'name' => $this->name, 
            'birthday' => $this->birthday, 
            'description' => $this->description));   
    }

    public function validate_name(){
        $errors = array();
        if($this->name == '' || $this->name == null){
            $errors[] = 'Henkilön nimi ei saa olla tyhjä';
        }
        if(strlen($this->name) > 50){
            $errors[] = 'Henkilön nimi ei saa olla yli 50 merkkiä';
        }
        return $errors;
    }

    public function validate_birthday(){
        $errors = array();
        if($this->birthday == ''){
            $errors[] = 'Henkilön syntymäpäivä ei saa olla tyhjä';
        }
        return $errors;
    }

    public function validate_description(){
        $errors = array();
        if(strlen($this->description) > 500){
            $errors[] = 'Kuvaus ei saa olla yli 500 merkkiä';
        }
        return $errors;
    }

}