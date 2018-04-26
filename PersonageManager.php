<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class manages communication with database
 *
 * @author Benoît Puech
 */
class PersonageManager {
    private $_db;
    
    public function __construct(PDO $db) {
        $this->setDb($db);
    }
    
    public function add(Personnage $perso)
    {
        $q = $this->_db->prepare('INSERT INTO personnages(name) VALUES(:name)');
        $q->bindValue(':name', $perso->name());
        $q->execute();
        
        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'damages' => 0,
            'experience' => 0,
            'level' => 0,
        ]);
    }
    
    public function count() {
        /* 
         * Compte le nombre total de personnages stockés dans la BDD et retourne
         * ce nombre
         */
        
        // RETURN le nombre de persos dans la bdd via requête SELECT COUNT
        return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }
    
    public function delete(Personnage $perso) {
        /* 
         * Supprime un personnage de la BDD en fonction de son ID 
         */
        
        // Requête DELETE via selection WHERE id=
        $q = $this->_db->query('DELETE FROM personnages WHERE id = '.$perso->id());
    }
    
    public function exists($info) {
       if (is_int($info))
       {
           return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn();
       }
       $q = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE name = :name');
       $q->execute([':name' => $info]);
       
       return (bool) $q->fetchColumn();
    }
    
    public function get($infos) {
        if (is_int($infos)) {
            $q = $this->_db->query('SELECT id, name, damages, experience, level FROM personnages WHERE id = '.$infos);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);
            return new Personnage($donnees);
        } else {
            $q = $this->_db->prepare('SELECT id, name, damages, experience, level FROM personnages WHERE name = :name');
            $q->execute([':name' => $infos]);
            return new Personnage($q->fetch(PDO::FETCH_ASSOC));
        }
    }
    
     public function getList($name)
    {
        $persos = [];

        $q = $this->_db->prepare('SELECT id, name, damages, experience, level FROM personnages WHERE name <> :nom ORDER BY name');
        $q->execute([':nom' => $name]);

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $persos[] = new Personnage($donnees);
        }

        return $persos;
    }
    
    public function update(Personnage $perso) {
        /*
         * Mets les dégats du personnage à jour dans la BDD en fonction ID
         */
        
        // UPDATE SET degats = WHERE id =
        // Bindvalue degats et id
        // execute
        $q = $this->_db->prepare('UPDATE personnages SET damages = :damages, experience = :experience, level = :level WHERE id = :id');
        $q->bindValue(':damages', $perso->damages());
        $q->bindValue(':experience', $perso->experience());
        $q->bindValue(':level', $perso->level());
        $q->bindValue(':id', $perso->id());
        
        $q->execute();
    }

    // SETTER //
    public function setDb($db) {
        $this->_db = $db;
    }
}
