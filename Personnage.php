<?php

/**
 * This class allow to create and hydrate a character with data contained in database
 * 
 *
 * @author Benoît Puech
 */
class Personnage {
    private $_id,
            $_name,
            $_damages,
            $_experience,
            $_level,
            $_strengh;
    
    const CEST_MOI = 1;
    const PERSONNAGE_TUE = 2;
    const PERSONNAGE_FRAPPE = 3;
    
    public function __construct(array $donnees) {
        $this->hydrate($donnees);
    }
    
    /**
     * This function first checks if the character created does not hit himself and then, if is
     * not the case, allows damages to the character
     * 
     * @param Personnage $perso
     * @return damages the character is going to take
     */
    public function frapper(Personnage $perso) {
        if ($perso->id() == $this->_id) {
            return self::CEST_MOI;
        }
        return $perso->recevoirDegats($perso);
    }
    /**
     * Allocate data to the variables by calling related setter if it exists
     * 
     * @param array $donnees
     */
    public function hydrate(array $donnees)
    {
        foreach($donnees as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    
    /**
     * Checks if the name sent by user is correct
     * @return type string
     */
    
    public function nomValide() {
        return !empty($this->_name);
    }
    
    /**
     * Allocate damages to the character which was hit if the maximum damages is not reached (100). 
     * Otherwise it means the character is dead.
     * 
     * @param Personnage $perso
     * @return type
     */
    public function recevoirDegats(Personnage $perso) {
        $this->_damages += $this->_strengh;
        
        if ($this->_damages >= 100)
        {
            return self::PERSONNAGE_TUE;
        }
        
        return self::PERSONNAGE_FRAPPE;
    }
    
    // GETTERS //
    public function damages() { 
        return $this->_damages;
    }
    
    public function experience() {
        return $this->_experience;
    }
    
    public function id() {
        return $this->_id;
    }
    
    public function level() {
        return $this->_level;
        
    }
    
    public function name() {
        return $this->_name;
    }
    
    public function strengh() {
        return $this->_strengh;
    }


    // SETTER //
    public function setDamages($damages){
        $damages = (int)$damages;
        
        if ($damages >= 0 && $damages < 100)
        {
            $this->_damages = $damages;
        }
    }
    
    public function setExperience()
    {
        if ($this->_experience < 100)
        {
            $this->_experience += 10;
        } elseif ($this->_experience >= 100) {
            $this->_experience = 0;
            $this->setLevel(1);    
    }
    }
    
    public function setId($id) {
        $id = (int)$id;
        
        if ($id > 0)
        {
            $this->_id = $id;
        }
    }
    
    public function setLevel($level) {
        $level = (int) $level;
        
        if ($level >= 0 && $level <= 100) {
            $this->_level += $level;
            $this->setStrengh($level);
        }
    }
    
    public function setName($name) {
        if (is_string($name)){
            $this->_name = $name;
        }
    }
    
    public function setStrengh($level) {
        $level = (int) $level;
        
        switch (true) {
            case ($level >= 0 && $level < 10):
                $this->_strengh = 5;
                break;
            case ($level >= 10 && $level < 20):
                $this->_strengh = 10;
                break;
            case ($level >= 20 && $level < 30):
                $this->_strengh = 15;
                break;
            case ($level >= 30 && $level < 40):
                $this->_strengh = 20;
                break;
            case ($level >= 40 && $level < 50):
                $this->_strengh = 25;
                break;
            case ($level >= 50 && $level < 60):
                $this->_strengh = 30;
                break;
            case ($level >= 60 && $level < 70):
                $this->_strengh = 35;
                break;
            case ($level >= 70 && $level < 80):
                $this->_strengh = 40;
                break;
            case ($level >= 80 && $level <= 100):
                $this->_strengh = 45;
                break;
            default :
                echo 'Niveau inconnu';
                break;
        }
    }
}
